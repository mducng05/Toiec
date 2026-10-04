<?php

namespace App\Services;

use App\Models\Exam;
use App\Models\ExamPart;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Support\Facades\DB;

class DocumentParserService
{
    public function __construct(
        private QuestionService $questionService
    ) {}

    /**
     * Parse raw string into question_number => correct_label map.
     * Supports formats:
     * - "1A 2B 3C 4D"
     * - "1. A  2. B  3. C"
     * - "1-A, 2-B, 3-C"
     * - "1: A\n2: B\n3: C"
     * - "Question 1: A\nQuestion 2: B"
     * - JSON: '{"1": "A", "2": "B"}'
     */
    public function parseAnswerKeys(string $rawText): array
    {
        $rawText = trim($rawText);
        $keys = [];

        // Check if JSON
        if (str_starts_with($rawText, '{') && str_ends_with($rawText, '}')) {
            $json = json_decode($rawText, true);
            if (is_array($json)) {
                foreach ($json as $qNum => $ans) {
                    $qInt = (int) $qNum;
                    $ansClean = strtoupper(trim((string) $ans));
                    if ($qInt >= 1 && $qInt <= 200 && in_array($ansClean, ['A', 'B', 'C', 'D'])) {
                        $keys[$qInt] = $ansClean;
                    }
                }
                if (!empty($keys)) {
                    ksort($keys);
                    return $keys;
                }
            }
        }

        // Regex pattern to capture (Question Number) followed by optional punctuation then (A, B, C, D)
        // Matches: "1A", "1. A", "1.A", "1-A", "1: A", "Q1: A", "Question 1: A"
        $pattern = '/(?:Q(?:uestion)?\s*)?(\d{1,3})[\s\.\:\-\)\–\—]*([A-Da-d])(?![a-zA-Z0-9])/u';

        if (preg_match_all($pattern, $rawText, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $qNum = (int) $match[1];
                $ans = strtoupper($match[2]);

                if ($qNum >= 1 && $qNum <= 200 && in_array($ans, ['A', 'B', 'C', 'D'])) {
                    $keys[$qNum] = $ans;
                }
            }
        }

        ksort($keys);
        return $keys;
    }

    /**
     * Apply parsed answer keys to exam questions.
     */
    public function applyAnswerKeys(Exam $exam, array $keys, bool $createMissingSlots = false): array
    {
        return DB::transaction(function () use ($exam, $keys, $createMissingSlots) {
            $exam->load('parts.questions.options');
            $updated = 0;
            $created = 0;

            // Map standard parts for auto-slot creation
            $partRanges = [
                1 => ['min' => 1, 'max' => 6],
                2 => ['min' => 7, 'max' => 31],
                3 => ['min' => 32, 'max' => 70],
                4 => ['min' => 71, 'max' => 100],
                5 => ['min' => 101, 'max' => 130],
                6 => ['min' => 131, 'max' => 146],
                7 => ['min' => 147, 'max' => 200],
            ];

            foreach ($keys as $qNum => $correctLabel) {
                // Find question in exam
                $question = Question::whereHas('examPart', fn($q) => $q->where('exam_id', $exam->id))
                    ->where('question_number', $qNum)
                    ->with('options')
                    ->first();

                if ($question) {
                    // Update options
                    $hasTargetOption = false;
                    foreach ($question->options as $opt) {
                        $isCorrect = (strtoupper($opt->label) === $correctLabel);
                        $opt->update(['is_correct' => $isCorrect]);
                        if ($isCorrect) {
                            $hasTargetOption = true;
                        }
                    }

                    // If option doesn't exist yet (e.g. question had fewer options), add it
                    if (!$hasTargetOption) {
                        QuestionOption::create([
                            'question_id' => $question->id,
                            'label'       => $correctLabel,
                            'content'     => '',
                            'is_correct'  => true,
                            'order'       => ord($correctLabel) - 64,
                        ]);
                    }

                    $updated++;
                } elseif ($createMissingSlots) {
                    // Find which part this question number belongs to
                    $targetPartNumber = 1;
                    foreach ($partRanges as $pNum => $range) {
                        if ($qNum >= $range['min'] && $qNum <= $range['max']) {
                            $targetPartNumber = $pNum;
                            break;
                        }
                    }

                    $targetPart = $exam->parts->firstWhere('part_number', $targetPartNumber);
                    if (!$targetPart) {
                        // Create part if missing
                        $targetPart = ExamPart::create([
                            'exam_id'     => $exam->id,
                            'part_number' => $targetPartNumber,
                            'title'       => ExamPart::PART_TITLES[$targetPartNumber] ?? "Part {$targetPartNumber}",
                            'section'     => ExamPart::PART_SECTIONS[$targetPartNumber] ?? 'reading',
                            'order'       => $targetPartNumber,
                        ]);
                    }

                    // Create new question slot
                    $newQ = Question::create([
                        'exam_part_id'    => $targetPart->id,
                        'question_number' => $qNum,
                        'content'         => "Câu hỏi {$qNum}",
                        'order'           => $qNum,
                    ]);

                    $optCount = ($targetPartNumber === 2) ? 3 : 4;
                    for ($i = 0; $i < $optCount; $i++) {
                        $lbl = chr(65 + $i);
                        QuestionOption::create([
                            'question_id' => $newQ->id,
                            'label'       => $lbl,
                            'content'     => '',
                            'is_correct'  => ($lbl === $correctLabel),
                            'order'       => $i + 1,
                        ]);
                    }

                    $created++;
                }
            }

            return [
                'updated' => $updated,
                'created' => $created,
                'total'   => count($keys),
            ];
        });
    }

    /**
     * Parse multi-line questions text block into structured question objects.
     */
    public function parseQuestionBlocks(string $rawText): array
    {
        $rawText = str_replace(["\r\n", "\r"], "\n", $rawText);
        $questions = [];

        // Split text by question indicators like "101.", "Question 101:", "101)"
        $parts = preg_split('/(?=(?:^|\n)\s*(?:Q(?:uestion)?\s*)?\b\d{1,3}\b[\.\:\)])/u', $rawText, -1, PREG_SPLIT_NO_EMPTY);

        foreach ($parts as $block) {
            $block = trim($block);
            if (empty($block)) continue;

            // Extract question number
            if (!preg_match('/^(?:Q(?:uestion)?\s*)?(\b\d{1,3}\b)[\.\:\)]\s*(.*)$/us', $block, $qMatch)) {
                continue;
            }

            $qNum = (int) $qMatch[1];
            $body = trim($qMatch[2]);

            // Extract explanation if present (e.g. "Explanation: ...", "Giải thích: ...")
            $explanation = null;
            if (preg_match('/(?:Explanation|Giải thích|Lời giải|Note)[\s\:\-]+(.*)$/is', $body, $expMatch)) {
                $explanation = trim($expMatch[1]);
                $body = trim(substr($body, 0, strpos($body, $expMatch[0])));
            }

            // Extract correct answer key if present (e.g. "Key: A", "Answer: B", "Đáp án: C")
            $correctAnswer = null;
            if (preg_match('/(?:Key|Answer|Đáp án)[\s\:\-]+([A-Da-d])\b/iu', $body, $keyMatch)) {
                $correctAnswer = strtoupper($keyMatch[1]);
                $body = trim(str_replace($keyMatch[0], '', $body));
            }

            // Extract options: (A) ..., (B) ..., or A. ..., B. ...
            $options = [];
            $optPattern = '/(?:\*|\b)\(?([A-Da-d])\)?[.\s]+([^\n\(\)]+)/u';

            // Split body into question prompt and options
            // Usually options start from the first (A) or A.
            if (preg_match('/(?:\n|\s)\(?A\)?[.\s]/i', $body, $optStart, PREG_OFFSET_CAPTURE)) {
                $splitPos = $optStart[0][1];
                $prompt = trim(substr($body, 0, $splitPos));
                $optionsText = substr($body, $splitPos);

                // Check for asterisk indicating correct option (e.g. *(B) priority)
                if (preg_match_all('/(\*?)\(?([A-Da-d])\)?[.\s]+([^\n\*]+)/u', $optionsText, $optMatches, PREG_SET_ORDER)) {
                    foreach ($optMatches as $m) {
                        $isStar = ($m[1] === '*');
                        $lbl = strtoupper($m[2]);
                        $optContent = trim($m[3]);

                        $options[$lbl] = $optContent;
                        if ($isStar) {
                            $correctAnswer = $lbl;
                        }
                    }
                }
            } else {
                $prompt = $body;
            }

            if (!empty($options)) {
                $questions[] = [
                    'number'         => $qNum,
                    'prompt'         => $prompt,
                    'options'        => $options,
                    'correct_option' => $correctAnswer ?? 'A',
                    'explanation'    => $explanation,
                ];
            }
        }

        return $questions;
    }

    /**
     * Import parsed questions into a specific ExamPart.
     */
    public function importParsedQuestions(ExamPart $part, array $parsedQuestions, ?int $passageId = null): int
    {
        return DB::transaction(function () use ($part, $parsedQuestions, $passageId) {
            $imported = 0;

            foreach ($parsedQuestions as $item) {
                $qNum = (int) $item['number'];
                $prompt = $item['prompt'] ?? "Câu hỏi {$qNum}";
                $correct = strtoupper($item['correct_option'] ?? 'A');
                $explanation = $item['explanation'] ?? null;

                // Delete existing question if exists
                Question::where('exam_part_id', $part->id)
                    ->where('question_number', $qNum)
                    ->delete();

                $question = Question::create([
                    'exam_part_id'    => $part->id,
                    'passage_id'      => $passageId,
                    'question_number' => $qNum,
                    'content'         => $prompt,
                    'explanation'     => $explanation,
                    'order'           => $qNum,
                ]);

                $optIndex = 1;
                foreach ($item['options'] as $lbl => $content) {
                    $lblUpper = strtoupper($lbl);
                    QuestionOption::create([
                        'question_id' => $question->id,
                        'label'       => $lblUpper,
                        'content'     => trim($content),
                        'is_correct'  => ($lblUpper === $correct),
                        'order'       => $optIndex++,
                    ]);
                }

                $imported++;
            }

            $this->questionService->syncTotalQuestions = false; // synced in batch below
            app(ExamService::class)->syncTotalQuestions($part->exam);

            return $imported;
        });
    }
}
