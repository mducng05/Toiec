<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $attempt->exam->title }} — Phòng thi trực tuyến</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            background: var(--bg-ground);
            color: var(--text-warm);
            font-family: var(--font-body);
            margin: 0;
            overflow-x: hidden;
        }

        /* Top Sticky Examination Header */
        .exam-header {
            position: sticky;
            top: 0;
            z-index: 100;
            height: 60px;
            background: oklch(7% 0.006 95 / 0.95);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
        }

        .exam-title-box {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
        }

        .exam-title {
            font-size: 0.9375rem;
            font-weight: 700;
            color: var(--text-bright);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 380px;
        }

        /* Timer display */
        .timer-box {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 0.4rem 0.85rem;
            border-radius: var(--r-full);
            background: var(--bg-deep);
            border: 1px solid var(--border-gold);
            font-family: monospace;
            font-size: 1.125rem;
            font-weight: 700;
            color: var(--gold);
            letter-spacing: 0.05em;
        }
        .timer-warning {
            border-color: var(--error) !important;
            color: var(--error) !important;
            animation: pulse 1.5s infinite;
        }

        /* Main Examination Workspace */
        .exam-workspace {
            display: grid;
            grid-template-columns: 1fr 340px;
            min-height: calc(100vh - 60px);
        }

        @media (max-width: 1024px) {
            .exam-workspace {
                grid-template-columns: 1fr;
            }
            .palette-sidebar {
                display: none; /* toggleable on mobile */
            }
        }

        .exam-content-pane {
            padding: 2rem 2.5rem;
            max-width: 900px;
            margin: 0 auto;
            width: 100%;
            box-sizing: border-box;
        }

        /* Right Question Navigator Palette */
        .palette-sidebar {
            position: sticky;
            top: 60px;
            height: calc(100vh - 60px);
            background: var(--bg-deep);
            border-left: 1px solid var(--border);
            overflow-y: auto;
            padding: 1.5rem;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        /* Palette Number Circles */
        .palette-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 6px;
        }

        .palette-btn {
            height: 38px;
            border-radius: var(--r-sm);
            background: var(--bg-raised);
            border: 1px solid var(--border);
            color: var(--text-muted);
            font-size: 0.8125rem;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            transition: all 0.1s;
        }

        .palette-btn:hover {
            border-color: var(--gold);
            color: var(--text-bright);
        }

        .palette-btn.answered {
            background: oklch(70% 0.12 188 / 0.18);
            border-color: var(--patina);
            color: var(--patina);
        }

        .palette-btn.flagged::after {
            content: '';
            position: absolute;
            top: 3px;
            right: 3px;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--gold);
            box-shadow: 0 0 6px var(--gold);
        }

        /* Question Item Card */
        .question-item {
            scroll-margin-top: 80px;
            margin-bottom: 2rem;
            padding: 1.5rem;
            background: var(--bg-deep);
            border: 1px solid var(--border);
            border-radius: var(--r-xl);
            transition: border-color 0.15s;
        }
        .question-item:focus-within {
            border-color: var(--border-gold);
        }

        .option-label-card {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0.75rem 1rem;
            border-radius: var(--r-md);
            background: var(--bg-raised);
            border: 1px solid var(--border);
            cursor: pointer;
            transition: all 0.12s;
            margin-bottom: 8px;
        }
        .option-label-card:hover {
            border-color: var(--gold);
            background: oklch(84% 0.19 80.46 / 0.04);
        }
        .option-label-card.selected {
            background: oklch(70% 0.12 188 / 0.12);
            border-color: var(--patina);
        }

        /* Modal */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: oklch(0% 0 0 / 0.75);
            backdrop-filter: blur(4px);
            z-index: 200;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
    </style>
</head>
<body x-data="examApp()">

    {{-- ── Sticky Top Bar ──────────────────────────────────────── --}}
    <header class="exam-header">
        <div class="exam-title-box">
            <span class="badge badge-gold" style="font-weight:700;">TOEIC</span>
            <div class="exam-title">{{ $attempt->exam->title }}</div>
        </div>

        {{-- Countdown Timer --}}
        <div style="display:flex;align-items:center;gap:1.5rem;">
            <div id="save-status" style="font-size:0.75rem;color:var(--patina);display:flex;align-items:center;gap:4px;">
                <span>● Đã đồng bộ</span>
            </div>

            <div class="timer-box" :class="{ 'timer-warning': isWarning }">
                <svg style="width:16px;height:16px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" stroke-width="2"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2"/>
                </svg>
                <span x-text="formattedTime">--:--</span>
            </div>

            <button type="button" @click="showSubmitModal = true" class="btn btn-primary btn-sm">
                Nộp bài thi
            </button>
        </div>
    </header>

    {{-- ── Main Examination Workspace ──────────────────────────── --}}
    <div class="exam-workspace">

        {{-- Questions Column --}}
        <main class="exam-content-pane">
            @php
                $allQuestionsCount = 0;
            @endphp

            @foreach($attempt->exam->parts as $part)
                {{-- Part Separator Header --}}
                <div style="margin:2.5rem 0 1.5rem;padding-bottom:0.75rem;border-bottom:2px solid var(--border);display:flex;align-items:center;justify-content:space-between;">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <span class="badge {{ $part->section === 'listening' ? 'badge-patina' : 'badge-gold' }}" style="font-weight:700;">
                            Part {{ $part->part_number }}
                        </span>
                        <h2 style="font-size:1.25rem;font-weight:700;color:var(--text-bright);margin:0;">
                            {{ $part->title }}
                        </h2>
                    </div>
                    <span style="font-size:0.8125rem;color:var(--text-faint);">
                        {{ $part->questions->count() + $part->passages->reduce(fn($c, $p) => $c + $p->questions->count(), 0) }} câu hỏi
                    </span>
                </div>

                {{-- Part Audio Player if listening part --}}
                @if($part->isListening() && $part->audioFiles->isNotEmpty())
                    <div style="margin-bottom:1.5rem;padding:1rem 1.25rem;background:var(--bg-deep);border:1px solid var(--border-gold);border-radius:var(--r-lg);">
                        <div style="font-size:0.75rem;font-weight:600;color:var(--gold);margin-bottom:0.5rem;display:flex;align-items:center;gap:6px;">
                            <svg style="width:14px;height:14px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/>
                            </svg>
                            Audio phần thi nghe (Part {{ $part->part_number }}):
                        </div>
                        @foreach($part->audioFiles as $audio)
                            <div style="margin-bottom:6px;">
                                <audio controls preload="auto" style="width:100%;height:36px;">
                                    <source src="{{ route('admin.files.serve', base64_encode($audio->file_path)) }}">
                                </audio>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Passages & their questions (Part 3, 4, 6, 7) --}}
                @foreach($part->passages as $passage)
                    <div style="margin-bottom:2rem;background:oklch(10% 0.008 95);border:1px solid var(--border);border-radius:var(--r-xl);overflow:hidden;">
                        <div style="padding:1.25rem 1.5rem;border-bottom:1px solid var(--border);">
                            @if($passage->title)
                                <div style="font-weight:700;font-size:0.9375rem;color:var(--gold);margin-bottom:0.75rem;">
                                    {{ $passage->title }}
                                </div>
                            @endif

                            @if($passage->audioFile)
                                <div style="margin-bottom:0.75rem;">
                                    <audio controls style="width:100%;height:32px;">
                                        <source src="{{ route('admin.files.serve', base64_encode($passage->audioFile->file_path)) }}">
                                    </audio>
                                </div>
                            @endif

                            @if($passage->image_path)
                                <div style="margin-bottom:1rem;">
                                    <img src="{{ route('admin.files.serve', base64_encode($passage->image_path)) }}" alt="Passage illustration" style="max-height:360px;max-width:100%;border-radius:var(--r-md);border:1px solid var(--border);">
                                </div>
                            @endif

                            @if($passage->content)
                                <div style="font-size:0.9375rem;color:var(--text-bright);line-height:1.75;white-space:pre-wrap;background:var(--bg-deep);padding:1.25rem;border-radius:var(--r-md);border:1px solid var(--border);">
                                    {{ $passage->content }}
                                </div>
                            @endif
                        </div>

                        <div style="padding:1.5rem;">
                            @foreach($passage->questions as $question)
                                @php $allQuestionsCount++; @endphp
                                @include('user.exams._taking_question', ['question' => $question, 'userAnswers' => $userAnswers])
                            @endforeach
                        </div>
                    </div>
                @endforeach

                {{-- Standalone Questions (Part 1, 2, 5) --}}
                @foreach($part->questions as $question)
                    @php $allQuestionsCount++; @endphp
                    @include('user.exams._taking_question', ['question' => $question, 'userAnswers' => $userAnswers])
                @endforeach

            @endforeach
        </main>

        {{-- ── Right Palette Sidebar ─────────────────────────────── --}}
        <aside class="palette-sidebar">
            <div>
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.5rem;">
                    <h3 style="font-size:0.9375rem;font-weight:700;color:var(--text-bright);margin:0;">
                        Bảng câu hỏi
                    </h3>
                    <span style="font-size:0.75rem;color:var(--text-faint);">
                        <span x-text="answeredCount">0</span> / {{ $allQuestionsCount }} đã làm
                    </span>
                </div>
                <div style="height:4px;background:var(--bg-raised);border-radius:var(--r-full);overflow:hidden;">
                    <div style="height:100%;background:var(--patina);transition:width 0.3s;"
                         :style="`width: ${({{ $allQuestionsCount }} > 0 ? (answeredCount / {{ $allQuestionsCount }}) * 100 : 0)}%`"></div>
                </div>
            </div>

            {{-- Legend --}}
            <div style="display:flex;gap:12px;font-size:0.6875rem;color:var(--text-faint);">
                <div style="display:flex;align-items:center;gap:4px;">
                    <div style="width:10px;height:10px;background:oklch(70% 0.12 188 / 0.2);border:1px solid var(--patina);border-radius:2px;"></div>
                    <span>Đã làm</span>
                </div>
                <div style="display:flex;align-items:center;gap:4px;">
                    <div style="width:10px;height:10px;background:var(--bg-raised);border:1px solid var(--border);border-radius:2px;"></div>
                    <span>Chưa làm</span>
                </div>
                <div style="display:flex;align-items:center;gap:4px;">
                    <div style="width:6px;height:6px;background:var(--gold);border-radius:50%;"></div>
                    <span>Cờ phân vân</span>
                </div>
            </div>

            {{-- Palette Grid --}}
            <div class="palette-grid">
                @foreach($attempt->exam->parts as $part)
                    @php
                        $partQuestions = $part->questions->merge($part->passages->flatMap->questions)->sortBy('question_number');
                    @endphp
                    @foreach($partQuestions as $q)
                        @php
                            $ans = $userAnswers->get($q->id);
                            $hasAnswered = $ans && $ans->question_option_id;
                            $isFlagged = $ans && $ans->is_flagged;
                        @endphp
                        <button type="button"
                                class="palette-btn"
                                id="palette-btn-{{ $q->id }}"
                                :class="{ 'answered': answers[{{ $q->id }}], 'flagged': flags[{{ $q->id }}] }"
                                @click="scrollToQuestion({{ $q->id }})">
                            {{ $q->question_number }}
                        </button>
                    @endforeach
                @endforeach
            </div>

            {{-- Final Submit Button in Sidebar --}}
            <div style="margin-top:auto;padding-top:1rem;border-top:1px solid var(--border);">
                <button type="button" @click="showSubmitModal = true" class="btn btn-primary" style="width:100%;justify-content:center;">
                    Nộp bài thi
                </button>
            </div>
        </aside>

    </div>

    {{-- ── Submit Confirmation Modal ──────────────────────────── --}}
    <div x-show="showSubmitModal" class="modal-overlay" style="display:none;" x-cloak>
        <div class="card" style="max-width:440px;width:100%;padding:2rem;" @click.away="showSubmitModal = false">
            <h3 style="font-size:1.25rem;font-weight:700;color:var(--text-bright);margin:0 0 0.75rem;">
                Xác nhận nộp bài thi?
            </h3>
            <p style="font-size:0.875rem;color:var(--text-muted);line-height:1.5;margin:0 0 1.5rem;">
                Bạn đã hoàn thành <strong style="color:var(--patina);" x-text="answeredCount">0</strong> / {{ $allQuestionsCount }} câu hỏi.
                <span x-show="answeredCount < {{ $allQuestionsCount }}">
                    Vẫn còn <strong style="color:var(--error);" x-text="{{ $allQuestionsCount }} - answeredCount"></strong> câu chưa trả lời.
                </span>
                Bạn có chắc chắn muốn nộp bài và chấm điểm ngay bây giờ?
            </p>

            <div style="display:flex;align-items:center;justify-content:flex-end;gap:10px;">
                <button type="button" class="btn btn-ghost" @click="showSubmitModal = false">
                    Tiếp tục làm bài
                </button>
                <form method="POST" action="{{ route('exams.submit', $attempt) }}">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                        Xác nhận nộp bài
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Scripts for Countdown & AJAX Auto-save --}}
    <script>
        function examApp() {
            return {
                showSubmitModal: false,
                remainingSeconds: {{ $remainingSeconds }},
                timer: null,
                answers: {
                    @foreach($userAnswers as $qId => $ans)
                        @if($ans->question_option_id)
                            {{ $qId }}: {{ $ans->question_option_id }},
                        @endif
                    @endforeach
                },
                flags: {
                    @foreach($userAnswers as $qId => $ans)
                        @if($ans->is_flagged)
                            {{ $qId }}: true,
                        @endif
                    @endforeach
                },

                get answeredCount() {
                    return Object.keys(this.answers).length;
                },

                get isWarning() {
                    return this.remainingSeconds < 300; // < 5 mins
                },

                get formattedTime() {
                    const hrs = Math.floor(this.remainingSeconds / 3600);
                    const mins = Math.floor((this.remainingSeconds % 3600) / 60);
                    const secs = this.remainingSeconds % 60;
                    if (hrs > 0) {
                        return `${hrs}:${mins < 10 ? '0' : ''}${mins}:${secs < 10 ? '0' : ''}${secs}`;
                    }
                    return `${mins}:${secs < 10 ? '0' : ''}${secs}`;
                },

                init() {
                    this.timer = setInterval(() => {
                        if (this.remainingSeconds > 0) {
                            this.remainingSeconds--;
                        } else {
                            clearInterval(this.timer);
                            alert('Thời gian làm bài đã kết thúc! Hệ thống sẽ tự động nộp bài.');
                            document.forms[0].submit();
                        }
                    }, 1000);
                },

                async selectOption(questionId, optionId) {
                    this.answers[questionId] = optionId;
                    this.flashSaveStatus('Đang lưu...');

                    try {
                        const res = await fetch("{{ route('exams.save-answer', $attempt) }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                question_id: questionId,
                                question_option_id: optionId
                            })
                        });
                        const data = await res.json();
                        if (data.success) {
                            this.flashSaveStatus('Đã đồng bộ ✓');
                        }
                    } catch (e) {
                        console.error('Lỗi tự động lưu:', e);
                        this.flashSaveStatus('Lỗi kết nối ⚠');
                    }
                },

                async toggleFlag(questionId) {
                    const current = !this.flags[questionId];
                    this.flags[questionId] = current;

                    try {
                        await fetch("{{ route('exams.save-answer', $attempt) }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                question_id: questionId,
                                is_flagged: current
                            })
                        });
                    } catch (e) {
                        console.error(e);
                    }
                },

                scrollToQuestion(qId) {
                    const el = document.getElementById('q-' + qId);
                    if (el) {
                        el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                },

                flashSaveStatus(text) {
                    const el = document.getElementById('save-status');
                    if (el) el.innerHTML = `<span>● ${text}</span>`;
                }
            };
        }
    </script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
