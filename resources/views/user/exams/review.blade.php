@extends('layouts.app')

@section('title', 'Xem lại bài thi: ' . $attempt->exam->title)

@section('content')
<div style="max-width:1200px;margin:2rem auto;padding:0 1.5rem;" x-data="{ filter: 'all' }">

    {{-- Breadcrumb & Header --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem;">
        <div>
            <a href="{{ route('exams.results', $attempt) }}" style="display:inline-flex;align-items:center;gap:6px;font-size:0.8125rem;color:var(--text-faint);text-decoration:none;margin-bottom:0.5rem;transition:color 0.15s;" onmouseover="this.style.color='var(--gold)'" onmouseout="this.style.color='var(--text-faint)'">
                <svg style="width:14px;height:14px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Quay lại bảng điểm kết quả
            </a>
            <h1 style="font-size:1.5rem;font-weight:700;color:var(--text-bright);margin:0;">
                Xem lại bài làm: {{ $attempt->exam->title }}
            </h1>
        </div>

        {{-- Filter Buttons --}}
        <div style="display:flex;gap:6px;background:var(--bg-deep);padding:4px;border-radius:var(--r-md);border:1px solid var(--border);">
            <button type="button" @click="filter = 'all'" class="btn btn-sm"
                    :class="filter === 'all' ? 'btn-primary' : 'btn-ghost'">
                Tất cả ({{ $attempt->result->total_questions ?? 0 }})
            </button>
            <button type="button" @click="filter = 'wrong'" class="btn btn-sm"
                    :class="filter === 'wrong' ? 'btn-danger' : 'btn-ghost'">
                Câu sai ({{ $attempt->result->wrong_answers ?? 0 }})
            </button>
            <button type="button" @click="filter = 'correct'" class="btn btn-sm"
                    :class="filter === 'correct' ? 'btn-secondary' : 'btn-ghost'">
                Câu đúng ({{ $attempt->result->correct_answers ?? 0 }})
            </button>
            <button type="button" @click="filter = 'unanswered'" class="btn btn-sm"
                    :class="filter === 'unanswered' ? 'btn-secondary' : 'btn-ghost'">
                Chưa làm ({{ $attempt->result->unanswered ?? 0 }})
            </button>
        </div>
    </div>

    {{-- 2 Columns Grid: Questions list + Sticky Palette --}}
    <div style="display:grid;grid-template-columns:1fr 300px;gap:2rem;align-items:start;">

        {{-- Questions Column --}}
        <div>
            @foreach($attempt->exam->parts as $part)
                @php
                    $partQuestions = $part->questions->merge($part->passages->flatMap->questions)->sortBy('question_number');
                @endphp

                <div style="margin-bottom:2.5rem;">
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:1.25rem;padding-bottom:0.5rem;border-bottom:1px solid var(--border);">
                        <span class="badge {{ $part->section === 'listening' ? 'badge-patina' : 'badge-gold' }}" style="font-weight:700;">
                            Part {{ $part->part_number }}
                        </span>
                        <h2 style="font-size:1.125rem;font-weight:700;color:var(--text-bright);margin:0;">
                            {{ $part->title }}
                        </h2>
                    </div>

                    @foreach($partQuestions as $q)
                        @php
                            $userAns = $userAnswers->get($q->id);
                            $selectedOptId = $userAns?->question_option_id;
                            $correctOpt = $q->options->firstWhere('is_correct', true);
                            $isCorrect = $selectedOptId && $correctOpt && ($selectedOptId === $correctOpt->id);
                            $isUnanswered = !$selectedOptId;
                            $isWrong = $selectedOptId && !$isCorrect;
                            $status = $isCorrect ? 'correct' : ($isUnanswered ? 'unanswered' : 'wrong');
                        @endphp

                        <div class="card"
                             id="review-q-{{ $q->id }}"
                             x-show="filter === 'all' || filter === '{{ $status }}'"
                             style="margin-bottom:1.5rem;padding:1.5rem;background:var(--bg-deep);border:1px solid {{ $isCorrect ? 'oklch(70% 0.12 188 / 0.4)' : ($isWrong ? 'oklch(60% 0.20 25 / 0.4)' : 'var(--border)') }};">
                            
                            {{-- Header --}}
                            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
                                <div style="display:flex;align-items:center;gap:10px;">
                                    <div style="width:34px;height:34px;background:{{ $isCorrect ? 'oklch(70% 0.12 188 / 0.15)' : ($isWrong ? 'oklch(60% 0.20 25 / 0.15)' : 'var(--bg-raised)') }};border:1px solid {{ $isCorrect ? 'var(--patina)' : ($isWrong ? 'var(--error)' : 'var(--border)') }};border-radius:var(--r-md);display:flex;align-items:center;justify-content:center;color:{{ $isCorrect ? 'var(--patina)' : ($isWrong ? 'var(--error)' : 'var(--text-faint)') }};font-weight:700;font-size:0.875rem;">
                                        #{{ $q->question_number }}
                                    </div>
                                    <span style="font-weight:600;font-size:0.9375rem;color:var(--text-bright);">
                                        {{ $q->content ?: "(Câu hỏi âm thanh / hình ảnh)" }}
                                    </span>
                                </div>
                                <div>
                                    @if($isCorrect)
                                        <span class="badge badge-patina">Chính xác ✓</span>
                                    @elseif($isWrong)
                                        <span class="badge badge-error">Sai ✗</span>
                                    @else
                                        <span class="badge badge-muted">Chưa làm</span>
                                    @endif
                                </div>
                            </div>

                            {{-- Image if exists --}}
                            @if($q->image_path)
                                <div style="margin:0 0 1.25rem;">
                                    <img src="{{ route('admin.files.serve', base64_encode($q->image_path)) }}" alt="Question illustration" style="max-height:260px;max-width:100%;border-radius:var(--r-md);border:1px solid var(--border);">
                                </div>
                            @endif

                            {{-- Options List --}}
                            <div style="display:flex;flex-direction:column;gap:6px;margin-bottom:1.25rem;">
                                @foreach($q->options as $opt)
                                    @php
                                        $userSelectedThis = ($selectedOptId === $opt->id);
                                        $isThisCorrect = $opt->is_correct;
                                    @endphp
                                    <div style="display:flex;align-items:center;justify-content:space-between;padding:0.75rem 1rem;border-radius:var(--r-md);border:1px solid {{ $isThisCorrect ? 'var(--patina)' : ($userSelectedThis ? 'var(--error)' : 'var(--border)') }};background:{{ $isThisCorrect ? 'oklch(70% 0.12 188 / 0.12)' : ($userSelectedThis ? 'oklch(60% 0.20 25 / 0.12)' : 'var(--bg-raised)') }};">
                                        <div style="display:flex;align-items:center;gap:10px;font-size:0.875rem;">
                                            <span style="font-weight:700;color:{{ $isThisCorrect ? 'var(--patina)' : ($userSelectedThis ? 'var(--error)' : 'var(--gold)') }};min-width:20px;">
                                                ({{ $opt->label }})
                                            </span>
                                            <span style="color:var(--text-bright);">
                                                {{ $opt->content }}
                                            </span>
                                        </div>
                                        <div style="font-size:0.75rem;font-weight:600;">
                                            @if($isThisCorrect && $userSelectedThis)
                                                <span style="color:var(--patina);">Đáp án bạn chọn (Đúng ✓)</span>
                                            @elseif($isThisCorrect)
                                                <span style="color:var(--patina);">Đáp án đúng ✓</span>
                                            @elseif($userSelectedThis)
                                                <span style="color:var(--error);">Bạn đã chọn ✗</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Explanation --}}
                            @if($q->explanation)
                                <div style="padding:1rem 1.25rem;background:oklch(84% 0.19 80.46 / 0.05);border-left:3px solid var(--gold);border-radius:0 var(--r-md) var(--r-md) 0;font-size:0.875rem;color:var(--text-warm);line-height:1.6;">
                                    <div style="font-weight:700;color:var(--gold);margin-bottom:0.25rem;font-size:0.8125rem;text-transform:uppercase;letter-spacing:0.04em;">
                                        Giải thích chi tiết & Từ vựng:
                                    </div>
                                    {{ $q->explanation }}
                                </div>
                            @endif

                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>

        {{-- Right Palette Navigation --}}
        <aside style="position:sticky;top:80px;background:var(--bg-deep);border:1px solid var(--border);border-radius:var(--r-xl);padding:1.25rem;">
            <h3 style="font-size:0.9375rem;font-weight:700;color:var(--text-bright);margin:0 0 1rem;">
                Danh sách câu hỏi
            </h3>

            <div style="display:grid;grid-template-columns:repeat(5, 1fr);gap:6px;max-height:calc(100vh - 200px);overflow-y:auto;padding-right:4px;">
                @foreach($attempt->exam->parts as $part)
                    @php
                        $partQuestions = $part->questions->merge($part->passages->flatMap->questions)->sortBy('question_number');
                    @endphp
                    @foreach($partQuestions as $q)
                        @php
                            $userAns = $userAnswers->get($q->id);
                            $selectedOptId = $userAns?->question_option_id;
                            $correctOpt = $q->options->firstWhere('is_correct', true);
                            $isCorrect = $selectedOptId && $correctOpt && ($selectedOptId === $correctOpt->id);
                            $isUnanswered = !$selectedOptId;
                        @endphp
                        <button type="button"
                                onclick="document.getElementById('review-q-{{ $q->id }}')?.scrollIntoView({ behavior: 'smooth', block: 'center' })"
                                style="height:34px;border-radius:var(--r-sm);border:1px solid;font-size:0.75rem;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all 0.1s;{{ $isCorrect ? 'background:oklch(70% 0.12 188 / 0.15);border-color:var(--patina);color:var(--patina);' : ($isUnanswered ? 'background:var(--bg-raised);border-color:var(--border);color:var(--text-faint);' : 'background:oklch(60% 0.20 25 / 0.15);border-color:var(--error);color:var(--error);') }}">
                            {{ $q->question_number }}
                        </button>
                    @endforeach
                @endforeach
            </div>
        </aside>

    </div>

</div>
@endsection
