@extends('layouts.admin')

@section('title', 'Quản lý File & Audio — ' . $exam->title)
@section('page-title', 'Uploads & Audio')

@section('topbar-actions')
    <a href="{{ route('admin.exams.show', $exam) }}" class="btn btn-ghost btn-sm">
        ← Chi tiết đề thi
    </a>
@endsection

@section('content')

{{-- Header --}}
<div style="margin-bottom:1.75rem;">
    <a href="{{ route('admin.exams.show', $exam) }}" style="display:inline-flex;align-items:center;gap:6px;font-size:0.8125rem;color:var(--text-faint);text-decoration:none;margin-bottom:0.75rem;transition:color 0.15s;" onmouseover="this.style.color='var(--gold)'" onmouseout="this.style.color='var(--text-faint)'">
        <svg style="width:14px;height:14px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Quay lại đề thi: {{ $exam->title }}
    </a>
    <h2 style="font-size:1.5rem;font-weight:700;color:var(--text-bright);margin:0 0 0.35rem;">
        Quản lý File PDF & Âm thanh
    </h2>
    <p style="font-size:0.875rem;color:var(--text-muted);margin:0;">
        Tải lên tài liệu PDF đề thi, đáp án và file MP3 phục vụ luyện nghe cho học viên.
    </p>
</div>

@php
    $examPdf = $exam->assets->firstWhere('type', 'exam_pdf');
    $answerPdf = $exam->assets->firstWhere('type', 'answer_pdf');
    $listeningParts = $exam->parts->where('section', 'listening')->sortBy('order');
@endphp

{{-- ── 1. PDF DOCUMENTS SECTION ───────────────────────────── --}}
<div style="margin-bottom:2.25rem;">
    <div style="font-size:0.75rem;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:var(--gold);margin-bottom:0.875rem;display:flex;align-items:center;gap:6px;">
        <svg style="width:14px;height:14px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0121 9.586V19a2 2 0 01-2 2z"/>
        </svg>
        Tài liệu PDF Đề thi & Đáp án
    </div>

    <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(340px, 1fr));gap:1.25rem;">

        {{-- Exam PDF Card --}}
        <div class="card" style="display:flex;flex-direction:column;justify-content:space-between;">
            <div>
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div style="width:36px;height:36px;border-radius:var(--r-md);background:oklch(84% 0.19 80.46 / 0.1);display:flex;align-items:center;justify-content:center;color:var(--gold);">
                            <svg style="width:18px;height:18px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0121 9.586V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 style="font-size:0.9375rem;font-weight:600;color:var(--text-bright);margin:0;">
                                PDF Đề thi gốc
                            </h3>
                            <span style="font-size:0.75rem;color:var(--text-faint);">Dùng hiển thị nội dung câu hỏi</span>
                        </div>
                    </div>
                    @if($examPdf)
                        <span class="badge badge-patina">Đã có</span>
                    @else
                        <span class="badge badge-muted">Chưa có</span>
                    @endif
                </div>

                @if($examPdf)
                    <div style="padding:0.875rem 1rem;background:var(--bg-deep);border:1px solid var(--border);border-radius:var(--r-md);margin-bottom:1.25rem;">
                        <div style="font-size:0.8125rem;font-weight:600;color:var(--text-bright);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-bottom:4px;">
                            {{ $examPdf->file_name }}
                        </div>
                        <div style="display:flex;align-items:center;justify-content:space-between;font-size:0.75rem;color:var(--text-faint);">
                            <span>Dung lượng: {{ number_format($examPdf->file_size / 1048576, 2) }} MB</span>
                            <div style="display:flex;align-items:center;gap:8px;">
                                <a href="{{ route('admin.files.serve', base64_encode($examPdf->file_path)) }}" target="_blank" class="btn btn-ghost btn-sm" style="font-size:0.75rem;padding:0.25rem 0.5rem;">
                                    Xem PDF
                                </a>
                                <form method="POST" action="{{ route('admin.assets.destroy', $examPdf) }}" onsubmit="return confirm('Xác nhận xóa PDF đề thi?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm btn-icon" title="Xóa">
                                        <svg style="width:12px;height:12px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <form method="POST" action="{{ route('admin.exams.uploads.exam-pdf', $exam) }}" enctype="multipart/form-data">
                @csrf
                <div style="margin-bottom:0.75rem;">
                    <label class="label" style="font-size:0.75rem;">{{ $examPdf ? 'Tải lên đè file mới' : 'Chọn file PDF đề thi' }}</label>
                    <input type="file" name="pdf_file" accept=".pdf" class="input" style="padding:0.4rem 0.6rem;font-size:0.8125rem;" required>
                </div>
                <button type="submit" class="btn btn-primary btn-sm" style="width:100%;justify-content:center;">
                    <svg style="width:13px;height:13px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    Tải lên PDF Đề thi
                </button>
            </form>
        </div>

        {{-- Answer PDF Card --}}
        <div class="card" style="display:flex;flex-direction:column;justify-content:space-between;">
            <div>
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div style="width:36px;height:36px;border-radius:var(--r-md);background:oklch(70% 0.12 188 / 0.1);display:flex;align-items:center;justify-content:center;color:var(--patina);">
                            <svg style="width:18px;height:18px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                        </div>
                        <div>
                            <h3 style="font-size:0.9375rem;font-weight:600;color:var(--text-bright);margin:0;">
                                PDF Đáp án & Giải chi tiết
                            </h3>
                            <span style="font-size:0.75rem;color:var(--text-faint);">Dùng đối soát và trích xuất lời giải</span>
                        </div>
                    </div>
                    @if($answerPdf)
                        <span class="badge badge-patina">Đã có</span>
                    @else
                        <span class="badge badge-muted">Chưa có</span>
                    @endif
                </div>

                @if($answerPdf)
                    <div style="padding:0.875rem 1rem;background:var(--bg-deep);border:1px solid var(--border);border-radius:var(--r-md);margin-bottom:1.25rem;">
                        <div style="font-size:0.8125rem;font-weight:600;color:var(--text-bright);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-bottom:4px;">
                            {{ $answerPdf->file_name }}
                        </div>
                        <div style="display:flex;align-items:center;justify-content:space-between;font-size:0.75rem;color:var(--text-faint);">
                            <span>Dung lượng: {{ number_format($answerPdf->file_size / 1048576, 2) }} MB</span>
                            <div style="display:flex;align-items:center;gap:8px;">
                                <a href="{{ route('admin.files.serve', base64_encode($answerPdf->file_path)) }}" target="_blank" class="btn btn-ghost btn-sm" style="font-size:0.75rem;padding:0.25rem 0.5rem;">
                                    Xem PDF
                                </a>
                                <form method="POST" action="{{ route('admin.assets.destroy', $answerPdf) }}" onsubmit="return confirm('Xác nhận xóa PDF đáp án?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm btn-icon" title="Xóa">
                                        <svg style="width:12px;height:12px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <form method="POST" action="{{ route('admin.exams.uploads.answer-pdf', $exam) }}" enctype="multipart/form-data">
                @csrf
                <div style="margin-bottom:0.75rem;">
                    <label class="label" style="font-size:0.75rem;">{{ $answerPdf ? 'Tải lên đè file mới' : 'Chọn file PDF đáp án' }}</label>
                    <input type="file" name="pdf_file" accept=".pdf" class="input" style="padding:0.4rem 0.6rem;font-size:0.8125rem;" required>
                </div>
                <button type="submit" class="btn btn-secondary btn-sm" style="width:100%;justify-content:center;">
                    <svg style="width:13px;height:13px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    Tải lên PDF Đáp án
                </button>
            </form>
        </div>

    </div>
</div>

{{-- ── 2. LISTENING AUDIO SECTION ─────────────────────────── --}}
<div>
    <div style="font-size:0.75rem;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:var(--patina);margin-bottom:0.875rem;display:flex;align-items:center;gap:6px;">
        <svg style="width:14px;height:14px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/>
        </svg>
        Audio các phần Listening (Part 1 – Part 4)
    </div>

    @if($listeningParts->isEmpty())
        <div class="card" style="text-align:center;padding:2.5rem 1.5rem;">
            <p style="color:var(--text-muted);font-size:0.875rem;margin:0;">
                Đề thi hiện chưa có phần thi Listening nào (Part 1 - 4).
            </p>
            <a href="{{ route('admin.exams.show', $exam) }}" class="btn btn-secondary btn-sm" style="margin-top:1rem;">
                + Thêm Part Listening vào đề
            </a>
        </div>
    @else
        <div style="display:flex;flex-direction:column;gap:1.5rem;">
            @foreach($listeningParts as $part)
            <div class="card" style="padding:0;overflow:hidden;">
                {{-- Part bar --}}
                <div style="display:flex;align-items:center;justify-content:space-between;padding:1rem 1.5rem;background:var(--bg-deep);border-bottom:1px solid var(--border);">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <span class="badge badge-patina" style="font-weight:700;">
                            Part {{ $part->part_number }}
                        </span>
                        <span style="font-weight:600;color:var(--text-bright);font-size:0.9375rem;">
                            {{ $part->title }}
                        </span>
                        <span style="font-size:0.75rem;color:var(--text-faint);">
                            ({{ $part->audioFiles->count() }} audio file)
                        </span>
                    </div>
                </div>

                {{-- Audio files list --}}
                <div style="padding:1.25rem 1.5rem;">
                    @if($part->audioFiles->isEmpty())
                        <div style="padding:1.5rem 1rem;background:var(--bg-raised);border-radius:var(--r-md);text-align:center;margin-bottom:1.25rem;">
                            <p style="font-size:0.8125rem;color:var(--text-faint);margin:0;">
                                Chưa có audio nào cho Part này. Vui lòng tải lên file MP3 / WAV.
                            </p>
                        </div>
                    @else
                        <div style="display:flex;flex-direction:column;gap:0.75rem;margin-bottom:1.25rem;">
                            @foreach($part->audioFiles as $idx => $audio)
                            <div style="display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:0.75rem 1rem;background:var(--bg-deep);border:1px solid var(--border);border-radius:var(--r-md);flex-wrap:wrap;">
                                <div style="display:flex;align-items:center;gap:10px;min-width:200px;flex:1;">
                                    <span style="font-size:0.75rem;font-weight:700;color:var(--gold);min-width:20px;">
                                        #{{ $idx + 1 }}
                                    </span>
                                    <div style="min-width:0;">
                                        <div style="font-size:0.8125rem;font-weight:600;color:var(--text-bright);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                            {{ $audio->file_name }}
                                        </div>
                                        <div style="font-size:0.6875rem;color:var(--text-faint);">
                                            {{ number_format($audio->file_size / 1048576, 2) }} MB
                                        </div>
                                    </div>
                                </div>

                                {{-- Audio player preview --}}
                                <div style="flex:2;min-width:240px;max-width:400px;">
                                    <audio controls preload="none" style="width:100%;height:32px;">
                                        <source src="{{ route('admin.files.serve', base64_encode($audio->file_path)) }}" type="{{ $audio->mime_type ?? 'audio/mpeg' }}">
                                        Trình duyệt của bạn không hỗ trợ audio element.
                                    </audio>
                                </div>

                                {{-- Delete --}}
                                <form method="POST" action="{{ route('admin.audio.destroy', $audio) }}" onsubmit="return confirm('Xóa file audio này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm btn-icon" title="Xóa file audio">
                                        <svg style="width:13px;height:13px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Upload new audio files to this part --}}
                    <form method="POST" action="{{ route('admin.parts.uploads.audio', $part) }}" enctype="multipart/form-data" style="display:flex;align-items:center;gap:12px;background:var(--bg-raised);padding:0.875rem 1rem;border-radius:var(--r-md);border:1px dashed var(--border);flex-wrap:wrap;">
                        @csrf
                        <div style="font-size:0.8125rem;color:var(--text-warm);font-weight:500;">
                            + Tải thêm Audio:
                        </div>
                        <input type="file" name="audio_files[]" multiple accept=".mp3,.wav,.ogg" class="input" style="padding:0.35rem 0.5rem;font-size:0.75rem;flex:1;min-width:200px;" required>
                        <button type="submit" class="btn btn-secondary btn-sm">
                            <svg style="width:13px;height:13px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                            Tải lên
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>

@endsection
