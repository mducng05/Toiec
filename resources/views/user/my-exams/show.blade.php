@extends('layouts.app')

@section('title', 'Quản lý đề thi: ' . $exam->title)

@section('content')
<div style="max-width:1100px;margin:2.5rem auto;padding:0 1.5rem;">

    {{-- Breadcrumb & Actions --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem;">
        <div>
            <a href="{{ route('my-exams.index') }}" style="display:inline-flex;align-items:center;gap:6px;font-size:0.8125rem;color:var(--text-faint);text-decoration:none;margin-bottom:0.4rem;transition:color 0.15s;" onmouseover="this.style.color='var(--gold)'" onmouseout="this.style.color='var(--text-faint)'">
                <svg style="width:14px;height:14px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Quay lại danh sách đề của tôi
            </a>
            <div style="display:flex;align-items:center;gap:10px;">
                <h1 style="font-size:1.625rem;font-weight:800;color:var(--text-bright);margin:0;">
                    {{ $exam->title }}
                </h1>
                <span class="badge {{ $exam->status === 'published' ? 'badge-patina' : 'badge-muted' }}">
                    {{ $exam->status === 'published' ? 'Đang công khai' : 'Bản riêng tư' }}
                </span>
            </div>
        </div>

        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
            <a href="{{ route('my-exams.uploads', $exam) }}" class="btn btn-secondary btn-sm">
                <svg style="width:14px;height:14px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                Upload PDF & Audio
            </a>
            <a href="{{ route('my-exams.parser', $exam) }}" class="btn btn-secondary btn-sm">
                <svg style="width:14px;height:14px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                Nạp đáp án tự động
            </a>
            <a href="{{ route('exams.show', $exam) }}" class="btn btn-primary btn-sm">
                Làm bài thi thử
            </a>
        </div>
    </div>

    {{-- Metrics Grid --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(200px, 1fr));gap:1rem;margin-bottom:2rem;">
        <div class="card" style="padding:1.25rem;">
            <div style="font-size:0.75rem;color:var(--text-faint);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.4rem;">
                Tổng số câu hỏi
            </div>
            <div style="font-size:1.75rem;font-weight:800;color:var(--gold);line-height:1;">
                {{ $exam->parts->sum('questions_count') }} câu
            </div>
        </div>
        <div class="card" style="padding:1.25rem;">
            <div style="font-size:0.75rem;color:var(--text-faint);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.4rem;">
                Phần thi
            </div>
            <div style="font-size:1.75rem;font-weight:800;color:var(--patina);line-height:1;">
                {{ $exam->parts->count() }} Parts
            </div>
        </div>
        <div class="card" style="padding:1.25rem;">
            <div style="font-size:0.75rem;color:var(--text-faint);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.4rem;">
                Thời gian làm bài
            </div>
            <div style="font-size:1.75rem;font-weight:800;color:var(--text-warm);line-height:1;">
                {{ $exam->duration_minutes }} phút
            </div>
        </div>
        <div class="card" style="padding:1.25rem;">
            <div style="font-size:0.75rem;color:var(--text-faint);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.4rem;">
                Tài liệu PDF
            </div>
            <div style="font-size:1rem;font-weight:700;color:var(--text-bright);margin-top:4px;">
                {{ $exam->assets->count() }} file đã tải
            </div>
        </div>
    </div>

    {{-- 2 Columns: Parts List & Quick Tools --}}
    <div style="display:grid;grid-template-columns:1fr 340px;gap:2rem;align-items:start;">
        
        {{-- Parts List --}}
        <div class="card" style="padding:0;overflow:hidden;">
            <div class="gold-bar"></div>
            <div style="padding:1.25rem 1.5rem;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;">
                <h3 style="font-size:1rem;font-weight:700;color:var(--text-bright);margin:0;">
                    Các phần thi trong bộ đề
                </h3>
            </div>

            <div>
                @foreach($exam->parts as $part)
                <div style="display:flex;align-items:center;justify-content:space-between;padding:1rem 1.5rem;border-bottom:1px solid var(--border);">
                    <div style="display:flex;align-items:center;gap:12px;">
                        <span class="badge {{ $part->section === 'listening' ? 'badge-patina' : 'badge-gold' }}" style="font-weight:700;">
                            Part {{ $part->part_number }}
                        </span>
                        <div>
                            <div style="font-weight:600;font-size:0.875rem;color:var(--text-bright);">
                                {{ $part->title }}
                            </div>
                            <div style="font-size:0.75rem;color:var(--text-faint);">
                                {{ $part->questions_count }} câu hỏi
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('my-exams.uploads', $exam) }}" class="btn btn-ghost btn-sm" style="font-size:0.75rem;">
                        Tải audio / PDF
                    </a>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Right Side Actions --}}
        <div style="display:flex;flex-direction:column;gap:1.5rem;">
            
            <div class="card">
                <h3 style="font-size:1rem;font-weight:700;color:var(--text-bright);margin:0 0 1rem;">
                    Bóc tách tự động
                </h3>
                <p style="font-size:0.8125rem;color:var(--text-muted);line-height:1.5;margin:0 0 1.25rem;">
                    Dán nhanh chuỗi đáp án (VD: <code>1A 2B 3C...</code>) để hệ thống tự động gán đáp án cho toàn bộ câu hỏi trong 1 giây.
                </p>
                <a href="{{ route('my-exams.parser', $exam) }}" class="btn btn-primary" style="width:100%;justify-content:center;">
                    Mở công cụ nạp tự động →
                </a>
            </div>

            <div class="card">
                <h3 style="font-size:1rem;font-weight:700;color:var(--text-bright);margin:0 0 1rem;">
                    Tùy chọn đề thi
                </h3>
                <div style="display:flex;flex-direction:column;gap:8px;">
                    <a href="{{ route('my-exams.edit', $exam) }}" class="btn btn-secondary btn-sm" style="width:100%;justify-content:center;">
                        Sửa thông tin đề
                    </a>
                    <form method="POST" action="{{ route('my-exams.destroy', $exam) }}" onsubmit="return confirm('Bạn có chắc chắn muốn xóa đề thi này?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" style="width:100%;justify-content:center;">
                            Xóa đề thi
                        </button>
                    </form>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
