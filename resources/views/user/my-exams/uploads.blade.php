@extends('layouts.app')

@section('title', 'Tải lên tài liệu PDF & Audio — ' . $exam->title)

@section('content')
<div style="max-width:960px;margin:2.5rem auto;padding:0 1.5rem;">

    <a href="{{ route('my-exams.show', $exam) }}" style="display:inline-flex;align-items:center;gap:6px;font-size:0.8125rem;color:var(--text-faint);text-decoration:none;margin-bottom:1.25rem;transition:color 0.15s;" onmouseover="this.style.color='var(--gold)'" onmouseout="this.style.color='var(--text-faint)'">
        <svg style="width:14px;height:14px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Quay lại đề thi: {{ $exam->title }}
    </a>

    <h1 style="font-size:1.625rem;font-weight:800;color:var(--text-bright);margin:0 0 0.5rem;">
        Tải lên tài liệu PDF & Audio nghe
    </h1>
    <p style="font-size:0.875rem;color:var(--text-faint);margin:0 0 2rem;">
        Bạn có thể tải lên file PDF đề thi và file âm thanh MP3 để phục vụ việc luyện thi online.
    </p>

    @php
        $examPdf = $exam->assets->firstWhere('type', 'exam_pdf');
        $answerPdf = $exam->assets->firstWhere('type', 'answer_pdf');
    @endphp

    {{-- PDF Uploads --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin-bottom:2.5rem;">
        
        {{-- Exam PDF --}}
        <div class="card">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
                <h3 style="font-size:1rem;font-weight:700;color:var(--text-bright);margin:0;">
                    File PDF Đề thi
                </h3>
                <span class="badge {{ $examPdf ? 'badge-patina' : 'badge-muted' }}">
                    {{ $examPdf ? 'Đã tải lên' : 'Chưa có' }}
                </span>
            </div>

            @if($examPdf)
                <div style="padding:0.75rem 1rem;background:var(--bg-deep);border-radius:var(--r-md);margin-bottom:1rem;font-size:0.8125rem;">
                    <div style="font-weight:600;color:var(--text-bright);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        {{ $examPdf->file_name }}
                    </div>
                    <div style="color:var(--text-faint);font-size:0.75rem;margin-top:2px;">
                        {{ number_format($examPdf->file_size / 1048576, 2) }} MB
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('my-exams.uploads.exam-pdf', $exam) }}" enctype="multipart/form-data">
                @csrf
                <div style="margin-bottom:0.875rem;">
                    <input type="file" name="pdf_file" accept=".pdf" class="input" style="padding:0.4rem 0.6rem;font-size:0.8125rem;" required>
                </div>
                <button type="submit" class="btn btn-primary btn-sm" style="width:100%;justify-content:center;">
                    {{ $examPdf ? 'Tải lên đè file mới' : 'Tải lên PDF Đề thi' }}
                </button>
            </form>
        </div>

        {{-- Answer PDF --}}
        <div class="card">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
                <h3 style="font-size:1rem;font-weight:700;color:var(--text-bright);margin:0;">
                    File PDF Đáp án & Giải
                </h3>
                <span class="badge {{ $answerPdf ? 'badge-patina' : 'badge-muted' }}">
                    {{ $answerPdf ? 'Đã tải lên' : 'Chưa có' }}
                </span>
            </div>

            @if($answerPdf)
                <div style="padding:0.75rem 1rem;background:var(--bg-deep);border-radius:var(--r-md);margin-bottom:1rem;font-size:0.8125rem;">
                    <div style="font-weight:600;color:var(--text-bright);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        {{ $answerPdf->file_name }}
                    </div>
                    <div style="color:var(--text-faint);font-size:0.75rem;margin-top:2px;">
                        {{ number_format($answerPdf->file_size / 1048576, 2) }} MB
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('my-exams.uploads.answer-pdf', $exam) }}" enctype="multipart/form-data">
                @csrf
                <div style="margin-bottom:0.875rem;">
                    <input type="file" name="pdf_file" accept=".pdf" class="input" style="padding:0.4rem 0.6rem;font-size:0.8125rem;" required>
                </div>
                <button type="submit" class="btn btn-secondary btn-sm" style="width:100%;justify-content:center;">
                    {{ $answerPdf ? 'Tải lên đè file mới' : 'Tải lên PDF Đáp án' }}
                </button>
            </form>
        </div>

    </div>

    {{-- Audio Uploads per Part --}}
    <h3 style="font-size:1.125rem;font-weight:700;color:var(--text-bright);margin:0 0 1rem;">
        Audio nghe cho các phần Listening (Part 1 - 4)
    </h3>

    <div style="display:flex;flex-direction:column;gap:1.25rem;">
        @foreach($exam->parts->where('section', 'listening') as $part)
        <div class="card" style="padding:1.25rem 1.5rem;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;flex-wrap:wrap;gap:8px;">
                <div style="display:flex;align-items:center;gap:10px;">
                    <span class="badge badge-patina" style="font-weight:700;">Part {{ $part->part_number }}</span>
                    <span style="font-weight:700;color:var(--text-bright);">{{ $part->title }}</span>
                </div>
                <span style="font-size:0.75rem;color:var(--text-faint);">
                    {{ $part->audioFiles->count() }} file audio đã tải
                </span>
            </div>

            @if($part->audioFiles->isNotEmpty())
                <div style="display:flex;flex-direction:column;gap:6px;margin-bottom:1rem;">
                    @foreach($part->audioFiles as $audio)
                        <div style="display:flex;align-items:center;justify-content:space-between;padding:0.5rem 0.75rem;background:var(--bg-deep);border-radius:var(--r-md);font-size:0.8125rem;">
                            <span>{{ $audio->file_name }}</span>
                            <audio controls style="height:28px;">
                                <source src="{{ route('admin.files.serve', base64_encode($audio->file_path)) }}">
                            </audio>
                        </div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('my-parts.uploads.audio', $part) }}" enctype="multipart/form-data" style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                @csrf
                <input type="file" name="audio_files[]" multiple accept=".mp3,.wav,.ogg" class="input" style="flex:1;min-width:200px;font-size:0.75rem;padding:0.35rem 0.5rem;" required>
                <button type="submit" class="btn btn-secondary btn-sm">
                    Tải lên file Audio
                </button>
            </form>
        </div>
        @endforeach
    </div>

</div>
@endsection
