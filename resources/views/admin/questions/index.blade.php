@extends('layouts.admin')

@section('title', 'Quản lý câu hỏi — ' . $exam->title)
@section('page-title', 'Ngân hàng câu hỏi')

@section('topbar-actions')
    <a href="{{ route('admin.exams.show', $exam) }}" class="btn btn-ghost btn-sm">
        ← Chi tiết đề thi
    </a>
    <a href="{{ route('admin.exams.parser.index', $exam) }}" class="btn btn-secondary btn-sm">
        <svg style="width:14px;height:14px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
        </svg>
        Nạp đề tự động
    </a>
    @if($activePart)
        <a href="{{ route('admin.parts.questions.create', $activePart) }}" class="btn btn-primary btn-sm">
            <svg style="width:14px;height:14px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tạo câu hỏi mới
        </a>
    @endif
@endsection

@section('content')

{{-- Header & Exam Breadcrumb --}}
<div style="margin-bottom:1.5rem;">
    <a href="{{ route('admin.exams.show', $exam) }}" style="display:inline-flex;align-items:center;gap:6px;font-size:0.8125rem;color:var(--text-faint);text-decoration:none;margin-bottom:0.75rem;transition:color 0.15s;" onmouseover="this.style.color='var(--gold)'" onmouseout="this.style.color='var(--text-faint)'">
        <svg style="width:14px;height:14px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Quay lại đề thi: {{ $exam->title }}
    </a>
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
        <div>
            <h2 style="font-size:1.5rem;font-weight:700;color:var(--text-bright);margin:0 0 0.25rem;">
                {{ $exam->title }}
            </h2>
            <div style="display:flex;align-items:center;gap:10px;font-size:0.8125rem;color:var(--text-faint);">
                <span>Tổng: <strong style="color:var(--gold);">{{ $exam->total_questions }}</strong> câu hỏi</span>
                <span>·</span>
                <span>{{ $exam->parts->count() }} phần thi</span>
                <span>·</span>
                <span class="badge {{ $exam->is_full_test ? 'badge-gold' : 'badge-patina' }}" style="font-size:0.6875rem;">
                    {{ $exam->is_full_test ? 'Full Test' : 'Mini Test' }}
                </span>
            </div>
        </div>
    </div>
</div>

{{-- ── Part Tabs Navigation ──────────────────────────────── --}}
<div style="display:flex;gap:4px;border-bottom:1px solid var(--border);margin-bottom:1.75rem;overflow-x:auto;padding-bottom:1px;">
    @foreach($exam->parts as $part)
        @php
            $isActive = $activePart && $activePart->id === $part->id;
            $qCount = $part->questions()->count();
        @endphp
        <a href="{{ route('admin.exams.questions.index', ['exam' => $exam, 'part_id' => $part->id]) }}"
           style="display:inline-flex;align-items:center;gap:8px;padding:0.75rem 1.125rem;font-size:0.875rem;font-weight:{{ $isActive ? '600' : '500' }};text-decoration:none;border-bottom:2px solid {{ $isActive ? 'var(--gold)' : 'transparent' }};color:{{ $isActive ? 'var(--gold)' : 'var(--text-muted)' }};background:{{ $isActive ? 'oklch(84% 0.19 80.46 / 0.05)' : 'transparent' }};border-radius:var(--r-md) var(--r-md) 0 0;transition:all 0.15s;white-space:nowrap;">
            <span>Part {{ $part->part_number }}</span>
            <span style="font-size:0.75rem;padding:0.15rem 0.45rem;border-radius:var(--r-full);background:{{ $isActive ? 'oklch(84% 0.19 80.46 / 0.15)' : 'var(--bg-deep)' }};color:{{ $isActive ? 'var(--gold)' : 'var(--text-faint)' }};border:1px solid {{ $isActive ? 'var(--border-gold)' : 'var(--border)' }};">
                {{ $qCount }}
            </span>
        </a>
    @endforeach
</div>

@if(!$activePart)
    <div class="card" style="text-align:center;padding:3rem 1.5rem;">
        <p style="color:var(--text-muted);font-size:0.875rem;">Đề thi chưa có Part nào.</p>
        <a href="{{ route('admin.exams.show', $exam) }}" class="btn btn-secondary btn-sm" style="margin-top:1rem;">
            Thêm Part vào đề thi
        </a>
    </div>
@else

    {{-- ── Active Part Toolbar ──────────────────────────────── --}}
    <div class="card" style="margin-bottom:1.5rem;padding:1.125rem 1.5rem;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
        <div>
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:0.25rem;">
                <span class="badge {{ $activePart->section === 'listening' ? 'badge-patina' : 'badge-gold' }}" style="font-size:0.6875rem;">
                    {{ $activePart->section === 'listening' ? 'Listening' : 'Reading' }}
                </span>
                <h3 style="font-size:1.125rem;font-weight:700;color:var(--text-bright);margin:0;">
                    Part {{ $activePart->part_number }}: {{ $activePart->title }}
                </h3>
            </div>
            <p style="font-size:0.8125rem;color:var(--text-faint);margin:0;">
                @if($activePart->part_number == 1)
                    6 câu hỏi tranh hình ảnh (Photographs) · 4 đáp án A, B, C, D
                @elseif($activePart->part_number == 2)
                    25 câu hỏi đáp (Question-Response) · 3 đáp án A, B, C
                @elseif(in_array($activePart->part_number, [3, 4]))
                    Đoạn hội thoại / Bài nói · Mỗi đoạn 3 câu hỏi kèm Audio & Transcript
                @elseif($activePart->part_number == 5)
                    30 câu điền vào câu chưa hoàn chỉnh (Incomplete Sentences)
                @elseif($activePart->part_number == 6)
                    16 câu hoàn thành đoạn văn (Text Completion) · 4 đoạn văn
                @else
                    54 câu đọc hiểu (Reading Comprehension) · Đoạn đơn & đa đoạn
                @endif
            </p>
        </div>

        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
            @if(in_array($activePart->part_number, [3, 4, 6, 7]))
                <a href="{{ route('admin.parts.passages.create', $activePart) }}" class="btn btn-secondary btn-sm">
                    <svg style="width:14px;height:14px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Thêm bài đọc / Đoạn văn
                </a>
            @endif
            <a href="{{ route('admin.parts.questions.create', $activePart) }}" class="btn btn-primary btn-sm">
                <svg style="width:14px;height:14px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Thêm câu hỏi
            </a>
            <form method="POST" action="{{ route('admin.parts.questions.generate-slots', $activePart) }}" onsubmit="return confirm('Khởi tạo tự động các ô câu hỏi mẫu cho Part {{ $activePart->part_number }}?')">
                @csrf
                <button type="submit" class="btn btn-ghost btn-sm" title="Khởi tạo số lượng câu hỏi mặc định theo chuẩn TOEIC">
                    <svg style="width:14px;height:14px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    Tạo khung câu hỏi nhanh
                </button>
            </form>
        </div>
    </div>

    {{-- ── 1. Passages Section (If Part has Passages) ────────── --}}
    @if(in_array($activePart->part_number, [3, 4, 6, 7]) && $passages->isNotEmpty())
        <div style="margin-bottom:2rem;display:flex;flex-direction:column;gap:1.5rem;">
            @foreach($passages as $pIndex => $passage)
            <div class="card" style="padding:0;overflow:hidden;border:1px solid var(--border);">
                {{-- Passage Top Bar --}}
                <div style="padding:1rem 1.5rem;background:var(--bg-deep);border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <span class="badge badge-gold" style="font-weight:700;">
                            Bài đọc #{{ $pIndex + 1 }}
                        </span>
                        @if($passage->title)
                            <span style="font-weight:600;color:var(--text-bright);font-size:0.9375rem;">
                                {{ $passage->title }}
                            </span>
                        @endif
                        <span style="font-size:0.75rem;color:var(--text-faint);">
                            ({{ $passage->questions->count() }} câu hỏi)
                        </span>
                    </div>

                    <div style="display:flex;align-items:center;gap:8px;">
                        <a href="{{ route('admin.parts.questions.create', ['part' => $activePart, 'passage_id' => $passage->id]) }}" class="btn btn-secondary btn-sm" style="font-size:0.75rem;">
                            + Thêm câu hỏi
                        </a>
                        <a href="{{ route('admin.passages.edit', $passage) }}" class="btn btn-ghost btn-sm btn-icon" title="Sửa bài đọc">
                            <svg style="width:13px;height:13px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </a>
                        <form method="POST" action="{{ route('admin.passages.destroy', $passage) }}" onsubmit="return confirm('Xóa bài đọc này?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm btn-icon" title="Xóa">
                                <svg style="width:13px;height:13px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Passage Content / Image / Audio --}}
                <div style="padding:1.25rem 1.5rem;background:oklch(10% 0.008 95);border-bottom:1px solid var(--border);">
                    @if($passage->audioFile)
                        <div style="margin-bottom:1rem;max-width:400px;">
                            <audio controls style="width:100%;height:32px;">
                                <source src="{{ route('admin.files.serve', base64_encode($passage->audioFile->file_path)) }}">
                            </audio>
                        </div>
                    @endif

                    @if($passage->image_path)
                        <div style="margin-bottom:1rem;">
                            <img src="{{ route('admin.files.serve', base64_encode($passage->image_path)) }}" alt="Passage image" style="max-height:300px;border-radius:var(--r-md);border:1px solid var(--border);">
                        </div>
                    @endif

                    @if($passage->content)
                        <div style="font-size:0.875rem;color:var(--text-warm);line-height:1.65;white-space:pre-wrap;background:var(--bg-deep);padding:1rem 1.25rem;border-radius:var(--r-md);border:1px solid var(--border);max-height:260px;overflow-y:auto;">
                            {{ $passage->content }}
                        </div>
                    @endif
                </div>

                {{-- Questions inside Passage --}}
                <div style="padding:1.25rem 1.5rem;display:flex;flex-direction:column;gap:1rem;">
                    @forelse($passage->questions as $question)
                        @include('admin.questions._question_card', ['question' => $question, 'part' => $activePart])
                    @empty
                        <div style="text-align:center;padding:1.5rem;color:var(--text-faint);font-size:0.8125rem;">
                            Chưa có câu hỏi nào cho bài đọc này.
                        </div>
                    @endforelse
                </div>
            </div>
            @endforeach
        </div>
    @endif

    {{-- ── 2. Standalone Questions Section ───────────────────── --}}
    @if($questions->isEmpty() && (empty($passages) || $passages->isEmpty()))
        <div class="card" style="text-align:center;padding:4rem 2rem;">
            <div style="width:52px;height:52px;background:var(--bg-graphite);border-radius:var(--r-xl);display:flex;align-items:center;justify-content:center;margin:0 auto 1.25rem;">
                <svg style="width:24px;height:24px;color:var(--text-faint)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h3 style="color:var(--text-warm);font-size:1.125rem;font-weight:600;margin:0 0 0.5rem;">
                Chưa có câu hỏi nào trong Part {{ $activePart->part_number }}
            </h3>
            <p style="color:var(--text-faint);font-size:0.875rem;margin:0 0 1.5rem;">
                Bạn có thể tạo thủ công từng câu hỏi hoặc dùng tính năng Tạo khung câu hỏi nhanh.
            </p>
            <div style="display:flex;align-items:center;justify-content:center;gap:12px;">
                <a href="{{ route('admin.parts.questions.create', $activePart) }}" class="btn btn-primary">
                    + Thêm câu hỏi đầu tiên
                </a>
            </div>
        </div>
    @else
        <div style="display:flex;flex-direction:column;gap:1rem;">
            @if(in_array($activePart->part_number, [3, 4, 6, 7]) && $questions->isNotEmpty())
                <div style="font-size:0.8125rem;font-weight:600;color:var(--text-faint);letter-spacing:0.05em;text-transform:uppercase;margin-top:1rem;">
                    Câu hỏi độc lập (Không thuộc bài đọc nào):
                </div>
            @endif

            @foreach($questions as $question)
                @include('admin.questions._question_card', ['question' => $question, 'part' => $activePart])
            @endforeach
        </div>
    @endif

@endif

@push('scripts')
<script>
// AJAX Quick Answer Switcher
document.querySelectorAll('.btn-quick-answer').forEach(btn => {
    btn.addEventListener('click', async function(e) {
        e.preventDefault();
        const qId = this.dataset.questionId;
        const label = this.dataset.label;
        const container = this.closest('.options-container');

        try {
            const res = await fetch(`/admin/questions/${qId}/quick-answer`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ correct_label: label })
            });

            const data = await res.json();
            if (data.success) {
                // Update UI buttons in this question card
                container.querySelectorAll('.btn-quick-answer').forEach(b => {
                    const isNowCorrect = (b.dataset.label === label);
                    if (isNowCorrect) {
                        b.style.background = 'oklch(70% 0.12 188 / 0.2)';
                        b.style.borderColor = 'var(--patina)';
                        b.style.color = 'var(--patina)';
                        b.querySelector('.check-indicator').style.display = 'inline';
                    } else {
                        b.style.background = 'var(--bg-deep)';
                        b.style.borderColor = 'var(--border)';
                        b.style.color = 'var(--text-muted)';
                        b.querySelector('.check-indicator').style.display = 'none';
                    }
                });
            }
        } catch (err) {
            console.error('Lỗi cập nhật đáp án:', err);
        }
    });
});
</script>
@endpush

@endsection
