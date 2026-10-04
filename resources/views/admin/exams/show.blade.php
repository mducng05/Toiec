@extends('layouts.admin')

@section('title', $exam->title . ' — Chi tiết đề thi')
@section('page-title', 'Chi tiết đề thi')

@section('topbar-actions')
    <a href="{{ route('admin.exams.questions.index', $exam) }}" class="btn btn-primary btn-sm">
        <svg style="width:14px;height:14px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        Ngân hàng câu hỏi
    </a>
    <a href="{{ route('admin.exams.uploads', $exam) }}" class="btn btn-secondary btn-sm">
        <svg style="width:14px;height:14px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
        </svg>
        File PDF & Audio
    </a>
    <a href="{{ route('admin.exams.edit', $exam) }}" class="btn btn-ghost btn-sm">
        <svg style="width:14px;height:14px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
        </svg>
        Chỉnh sửa
    </a>
    <form method="POST" action="{{ route('admin.exams.toggle-publish', $exam) }}" style="display:inline;">
        @csrf
        <button type="submit" class="btn {{ $exam->status === 'published' ? 'btn-ghost' : 'btn-primary' }} btn-sm">
            @if($exam->status === 'published')
                <svg style="width:14px;height:14px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                </svg>
                Thu hồi nháp
            @else
                <svg style="width:14px;height:14px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Xuất bản
            @endif
        </button>
    </form>
@endsection

@section('content')

{{-- Back Link & Header Title --}}
<div style="margin-bottom: 1.5rem;">
    <a href="{{ route('admin.exams.index') }}" style="display:inline-flex;align-items:center;gap:6px;font-size:0.8125rem;color:var(--text-faint);text-decoration:none;margin-bottom:0.75rem;transition:color 0.15s;" onmouseover="this.style.color='var(--gold)'" onmouseout="this.style.color='var(--text-faint)'">
        <svg style="width:14px;height:14px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Quay lại danh sách đề thi
    </a>
    <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
        <div>
            <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:0.35rem;">
                <h2 style="font-size:1.5rem;font-weight:700;color:var(--text-bright);margin:0;">
                    {{ $exam->title }}
                </h2>
                @if($exam->status === 'published')
                    <span class="badge badge-patina">Đang mở xuất bản</span>
                @else
                    <span class="badge badge-muted">Bản nháp (Riêng tư)</span>
                @endif
                @if($exam->is_full_test)
                    <span class="badge badge-gold">Full Test (Part 1-7)</span>
                @endif
            </div>
            @if($exam->description)
                <p style="font-size:0.875rem;color:var(--text-muted);margin:0;max-width:700px;line-height:1.5;">
                    {{ $exam->description }}
                </p>
            @endif
        </div>
    </div>
</div>

{{-- Top 4 KPI Metrics --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(210px, 1fr));gap:1rem;margin-bottom:1.75rem;">
    @php
        $totalQuestions = $exam->parts->sum('questions_count');
        $examPdf = $exam->assets->firstWhere('type', 'exam_pdf');
        $answerPdf = $exam->assets->firstWhere('type', 'answer_pdf');
        $audioCount = $exam->parts->reduce(fn($c, $p) => $c + ($p->relationLoaded('audioFiles') ? $p->audioFiles->count() : 0), 0);
    @endphp

    <div class="card" style="padding:1.125rem 1.25rem;">
        <div style="font-size:0.75rem;color:var(--text-faint);letter-spacing:0.05em;text-transform:uppercase;margin-bottom:0.4rem;">
            Tổng số câu hỏi
        </div>
        <div style="font-size:1.75rem;font-weight:700;color:var(--gold);line-height:1;">
            {{ $totalQuestions }}
            <span style="font-size:0.875rem;font-weight:400;color:var(--text-faint);">/ {{ $exam->is_full_test ? '200' : '—' }} câu</span>
        </div>
    </div>

    <div class="card" style="padding:1.125rem 1.25rem;">
        <div style="font-size:0.75rem;color:var(--text-faint);letter-spacing:0.05em;text-transform:uppercase;margin-bottom:0.4rem;">
            Phần thi cấu hình
        </div>
        <div style="font-size:1.75rem;font-weight:700;color:var(--patina);line-height:1;">
            {{ $exam->parts->count() }}
            <span style="font-size:0.875rem;font-weight:400;color:var(--text-faint);">/ 7 parts</span>
        </div>
    </div>

    <div class="card" style="padding:1.125rem 1.25rem;">
        <div style="font-size:0.75rem;color:var(--text-faint);letter-spacing:0.05em;text-transform:uppercase;margin-bottom:0.4rem;">
            Thời lượng quy định
        </div>
        <div style="font-size:1.75rem;font-weight:700;color:var(--text-warm);line-height:1;">
            {{ $exam->duration_minutes }}
            <span style="font-size:0.875rem;font-weight:400;color:var(--text-faint);">phút</span>
        </div>
    </div>

    <div class="card" style="padding:1.125rem 1.25rem;">
        <div style="font-size:0.75rem;color:var(--text-faint);letter-spacing:0.05em;text-transform:uppercase;margin-bottom:0.4rem;">
            File đề & đáp án
        </div>
        <div style="font-size:1.125rem;font-weight:600;color:var(--text-bright);display:flex;align-items:center;gap:8px;margin-top:4px;">
            <span style="display:inline-flex;align-items:center;gap:4px;font-size:0.8125rem;color:{{ $examPdf ? 'var(--patina)' : 'var(--text-faint)' }}">
                ● PDF Đề
            </span>
            <span style="display:inline-flex;align-items:center;gap:4px;font-size:0.8125rem;color:{{ $answerPdf ? 'var(--patina)' : 'var(--text-faint)' }}">
                ● Đáp án
            </span>
        </div>
    </div>
</div>

{{-- Main Grid: 2 Columns --}}
<div style="display:grid;grid-template-columns:1fr 340px;gap:1.5rem;align-items:start;">

    {{-- Left: Parts List --}}
    <div style="display:flex;flex-direction:column;gap:1.5rem;">
        <div class="card" style="padding:0;overflow:hidden;">
            <div class="gold-bar"></div>
            <div style="display:flex;align-items:center;justify-content:space-between;padding:1.125rem 1.5rem;border-bottom:1px solid var(--border);">
                <div>
                    <h3 style="font-size:0.9375rem;font-weight:600;color:var(--text-bright);margin:0;">
                        Các phần thi (Parts) trong đề
                    </h3>
                    <p style="font-size:0.8125rem;color:var(--text-faint);margin:0.25rem 0 0;">
                        Quản lý câu hỏi, hình ảnh và audio tương ứng cho từng part
                    </p>
                </div>
            </div>

            @if($exam->parts->isEmpty())
                <div style="padding:3rem 1.5rem;text-align:center;">
                    <p style="color:var(--text-muted);font-size:0.875rem;">Đề thi chưa có Part nào được kích hoạt.</p>
                </div>
            @else
                <div style="divide-y:1px solid var(--border);">
                    @foreach($exam->parts as $part)
                    <div style="display:flex;align-items:center;justify-content:space-between;padding:1rem 1.5rem;border-bottom:1px solid var(--border);transition:background 0.15s;" onmouseover="this.style.background='var(--bg-raised)'" onmouseout="this.style.background='transparent'">
                        <div style="display:flex;align-items:center;gap:1rem;">
                            <div style="width:42px;height:42px;background:oklch(84% 0.19 80.46 / 0.08);border:1px solid oklch(84% 0.19 80.46 / 0.2);border-radius:var(--r-md);display:flex;flex-direction:column;align-items:center;justify-content:center;color:var(--gold);font-weight:700;font-size:0.875rem;flex-shrink:0;">
                                <span style="font-size:0.625rem;text-transform:uppercase;letter-spacing:0.05em;color:var(--text-faint);font-weight:500;">P</span>
                                {{ $part->part_number }}
                            </div>
                            <div>
                                <div style="display:flex;align-items:center;gap:8px;">
                                    <span style="font-weight:600;color:var(--text-bright);font-size:0.9375rem;">
                                        Part {{ $part->part_number }}: {{ $part->title }}
                                    </span>
                                    <span class="badge {{ $part->section === 'listening' ? 'badge-patina' : 'badge-gold' }}" style="font-size:0.6875rem;">
                                        {{ $part->section === 'listening' ? 'Listening' : 'Reading' }}
                                    </span>
                                </div>
                                <div style="display:flex;align-items:center;gap:14px;margin-top:4px;font-size:0.8125rem;color:var(--text-faint);">
                                    <span>
                                        <strong style="color:var(--text-warm);">{{ $part->questions_count }}</strong> câu hỏi
                                    </span>
                                    @if($part->section === 'listening')
                                        <span>
                                            <svg style="width:12px;height:12px;display:inline;vertical-align:-1px;color:var(--patina);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/>
                                            </svg>
                                            Audio có sẵn
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div style="display:flex;align-items:center;gap:8px;">
                            <a href="{{ route('admin.exams.uploads', $exam) }}" class="btn btn-ghost btn-sm" title="Quản lý File & Audio cho Part này">
                                File & Audio
                            </a>
                            <a href="{{ route('admin.exams.questions.index', ['exam' => $exam, 'part_id' => $part->id]) }}" class="btn btn-secondary btn-sm">
                                Quản lý câu hỏi
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif

            {{-- Form to add remaining parts --}}
            @php
                $existingPartNumbers = $exam->parts->pluck('part_number')->toArray();
                $availableParts = array_diff([1, 2, 3, 4, 5, 6, 7], $existingPartNumbers);
            @endphp
            @if(count($availableParts) > 0)
                <div style="padding:1.25rem 1.5rem;background:var(--bg-raised);border-top:1px solid var(--border);">
                    <form method="POST" action="{{ route('admin.exams.add-part', $exam) }}" style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                        @csrf
                        <div style="font-size:0.8125rem;font-weight:500;color:var(--text-warm);">
                            Thêm Part còn thiếu:
                        </div>
                        <select name="part_number" class="select" style="max-width:240px;" required>
                            @foreach($availableParts as $pNum)
                                <option value="{{ $pNum }}">
                                    Part {{ $pNum }}: {{ \App\Models\ExamPart::PART_TITLES[$pNum] ?? "Part {$pNum}" }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <svg style="width:13px;height:13px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Thêm Part
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>

    {{-- Right: Assets & Exam Meta Card --}}
    <div style="display:flex;flex-direction:column;gap:1.5rem;">

        {{-- Assets Card --}}
        <div class="card">
            <h3 style="font-size:0.9375rem;font-weight:600;color:var(--text-bright);margin:0 0 1rem;">
                Tài liệu đính kèm
            </h3>

            {{-- Exam PDF --}}
            <div style="display:flex;align-items:center;justify-content:space-between;padding:0.75rem;background:var(--bg-deep);border:1px solid var(--border);border-radius:var(--r-md);margin-bottom:0.75rem;">
                <div style="display:flex;align-items:center;gap:10px;min-width:0;">
                    <div style="width:32px;height:32px;border-radius:var(--r-xs);background:oklch(84% 0.19 80.46 / 0.1);display:flex;align-items:center;justify-content:center;color:var(--gold);flex-shrink:0;">
                        <svg style="width:16px;height:16px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0121 9.586V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div style="min-width:0;">
                        <div style="font-size:0.8125rem;font-weight:600;color:var(--text-bright);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                            {{ $examPdf ? $examPdf->file_name : 'PDF Đề thi' }}
                        </div>
                        <div style="font-size:0.6875rem;color:var(--text-faint);">
                            {{ $examPdf ? number_format($examPdf->file_size / 1048576, 2) . ' MB' : 'Chưa tải lên' }}
                        </div>
                    </div>
                </div>
                @if($examPdf)
                    <a href="{{ route('admin.files.serve', base64_encode($examPdf->file_path)) }}" target="_blank" class="btn btn-ghost btn-sm btn-icon" title="Xem PDF">
                        <svg style="width:14px;height:14px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </a>
                @endif
            </div>

            {{-- Answer PDF --}}
            <div style="display:flex;align-items:center;justify-content:space-between;padding:0.75rem;background:var(--bg-deep);border:1px solid var(--border);border-radius:var(--r-md);margin-bottom:1rem;">
                <div style="display:flex;align-items:center;gap:10px;min-width:0;">
                    <div style="width:32px;height:32px;border-radius:var(--r-xs);background:oklch(70% 0.12 188 / 0.1);display:flex;align-items:center;justify-content:center;color:var(--patina);flex-shrink:0;">
                        <svg style="width:16px;height:16px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                    </div>
                    <div style="min-width:0;">
                        <div style="font-size:0.8125rem;font-weight:600;color:var(--text-bright);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                            {{ $answerPdf ? $answerPdf->file_name : 'PDF Đáp án & Giải' }}
                        </div>
                        <div style="font-size:0.6875rem;color:var(--text-faint);">
                            {{ $answerPdf ? number_format($answerPdf->file_size / 1048576, 2) . ' MB' : 'Chưa tải lên' }}
                        </div>
                    </div>
                </div>
                @if($answerPdf)
                    <a href="{{ route('admin.files.serve', base64_encode($answerPdf->file_path)) }}" target="_blank" class="btn btn-ghost btn-sm btn-icon" title="Xem PDF">
                        <svg style="width:14px;height:14px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </a>
                @endif
            </div>

            <a href="{{ route('admin.exams.uploads', $exam) }}" class="btn btn-secondary btn-sm" style="width:100%;justify-content:center;">
                <svg style="width:14px;height:14px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                Tải lên PDF / Audio
            </a>
        </div>

        {{-- Meta Info Card --}}
        <div class="card">
            <h3 style="font-size:0.9375rem;font-weight:600;color:var(--text-bright);margin:0 0 1rem;">
                Thông số đề thi
            </h3>

            <div style="font-size:0.8125rem;display:flex;flex-direction:column;gap:0.75rem;">
                <div style="display:flex;justify-content:space-between;">
                    <span style="color:var(--text-faint);">Trạng thái</span>
                    <span style="color:var(--text-bright);font-weight:500;">
                        {{ $exam->status === 'published' ? 'Đã xuất bản' : 'Bản nháp' }}
                    </span>
                </div>
                <div style="display:flex;justify-content:space-between;">
                    <span style="color:var(--text-faint);">Thời gian</span>
                    <span style="color:var(--text-bright);font-weight:500;">{{ $exam->duration_minutes }} phút</span>
                </div>
                <div style="display:flex;justify-content:space-between;">
                    <span style="color:var(--text-faint);">Phân loại</span>
                    <span style="color:var(--text-bright);font-weight:500;">
                        {{ $exam->is_full_test ? 'Full Test (200 câu)' : 'Đề thi tùy chỉnh' }}
                    </span>
                </div>
                <div style="display:flex;justify-content:space-between;">
                    <span style="color:var(--text-faint);">Ngày tạo</span>
                    <span style="color:var(--text-muted);">{{ $exam->created_at->format('d/m/Y H:i') }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;">
                    <span style="color:var(--text-faint);">Cập nhật</span>
                    <span style="color:var(--text-muted);">{{ $exam->updated_at->format('d/m/Y H:i') }}</span>
                </div>
            </div>

            <div style="border-top:1px solid var(--border);margin-top:1.25rem;padding-top:1rem;">
                <form method="POST" action="{{ route('admin.exams.destroy', $exam) }}" onsubmit="return confirm('Bạn có chắc chắn muốn xóa đề thi này không?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm" style="width:100%;justify-content:center;">
                        <svg style="width:14px;height:14px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Xóa đề thi
                    </button>
                </form>
            </div>
        </div>

    </div>

</div>

@endsection
