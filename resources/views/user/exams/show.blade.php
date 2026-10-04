@extends('layouts.app')

@section('title', $exam->title . ' — Chuẩn bị làm bài thi')

@section('content')
<div style="max-width:960px;margin:3rem auto;padding:0 1.5rem;">

    {{-- Breadcrumb --}}
    <a href="{{ route('home') }}" style="display:inline-flex;align-items:center;gap:6px;font-size:0.8125rem;color:var(--text-faint);text-decoration:none;margin-bottom:1.5rem;transition:color 0.15s;" onmouseover="this.style.color='var(--gold)'" onmouseout="this.style.color='var(--text-faint)'">
        <svg style="width:14px;height:14px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Quay lại danh mục đề thi
    </a>

    {{-- Hero Intro Card --}}
    <div class="card" style="padding:0;overflow:hidden;margin-bottom:2rem;">
        <div class="gold-bar"></div>

        <div style="padding:2.5rem 2rem;">
            
            {{-- Header Tags --}}
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:1rem;flex-wrap:wrap;">
                <span class="badge {{ $exam->is_full_test ? 'badge-gold' : 'badge-patina' }}">
                    {{ $exam->is_full_test ? 'TOEIC Full Test' : 'Mini Practice Test' }}
                </span>
                <span class="badge badge-muted">
                    Thời lượng: {{ $exam->duration_minutes }} phút
                </span>
                <span class="badge badge-muted">
                    Tổng số câu: {{ $exam->total_questions }} câu
                </span>
            </div>

            {{-- Title & Description --}}
            <h1 style="font-size:2rem;font-weight:800;color:var(--text-bright);line-height:1.25;margin:0 0 1rem;">
                {{ $exam->title }}
            </h1>

            @if($exam->description)
                <p style="font-size:1rem;color:var(--text-muted);line-height:1.6;margin:0 0 2rem;max-width:760px;">
                    {{ $exam->description }}
                </p>
            @endif

            {{-- Important Rules / Notes --}}
            <div style="background:var(--bg-deep);border:1px solid var(--border);border-radius:var(--r-md);padding:1.25rem 1.5rem;margin-bottom:2rem;">
                <div style="font-size:0.875rem;font-weight:600;color:var(--gold);margin-bottom:0.75rem;display:flex;align-items:center;gap:6px;">
                    <svg style="width:16px;height:16px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Hướng dẫn & Quy chế làm bài thi:
                </div>
                <ul style="margin:0;padding-left:1.25rem;font-size:0.8125rem;color:var(--text-muted);line-height:1.7;">
                    <li>Bài thi sẽ tự động tính giờ đếm ngược ngay khi bạn bấm nút bắt đầu.</li>
                    <li>Hệ thống <strong>tự động lưu đáp án (Auto-save)</strong> sau mỗi lần chọn, bạn có thể yên tâm không bị mất bài làm nếu mạng chập chờn.</li>
                    <li>Bạn có thể dùng bảng điều hướng bên phải để chuyển nhanh giữa các câu hỏi hoặc gắn cờ phân vân để kiểm tra lại.</li>
                    <li>Khi hết giờ, hệ thống sẽ tự động thu bài và hiển thị bảng điểm phân tích chi tiết.</li>
                </ul>
            </div>

            {{-- Start Form Action --}}
            @auth
                <form method="POST" action="{{ route('exams.start', $exam) }}">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-lg" style="padding:0.9rem 2rem;font-size:1rem;">
                        <svg style="width:18px;height:18px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Bắt đầu làm bài thi ngay
                    </button>
                </form>
            @else
                <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                    <a href="{{ route('login') }}" class="btn btn-primary btn-lg">
                        Đăng nhập để bắt đầu thi
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-ghost btn-lg">
                        Tạo tài khoản miễn phí
                    </a>
                </div>
            @endauth

        </div>
    </div>

    {{-- Parts Breakdown Grid --}}
    <h3 style="font-size:1.125rem;font-weight:700;color:var(--text-bright);margin:0 0 1rem;">
        Cấu trúc các phần thi trong đề ({{ $exam->parts->count() }} Parts)
    </h3>

    <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(280px, 1fr));gap:1rem;margin-bottom:3rem;">
        @foreach($exam->parts as $part)
        <div class="card" style="padding:1.125rem 1.25rem;background:var(--bg-deep);display:flex;align-items:center;justify-content:space-between;">
            <div style="display:flex;align-items:center;gap:12px;">
                <div style="width:36px;height:36px;background:oklch(84% 0.19 80.46 / 0.1);border:1px solid var(--border-gold);border-radius:var(--r-md);display:flex;align-items:center;justify-content:center;color:var(--gold);font-weight:700;font-size:0.875rem;">
                    P{{ $part->part_number }}
                </div>
                <div>
                    <div style="font-size:0.875rem;font-weight:600;color:var(--text-bright);">
                        {{ $part->title }}
                    </div>
                    <div style="font-size:0.75rem;color:var(--text-faint);margin-top:2px;">
                        {{ $part->section === 'listening' ? 'Listening' : 'Reading' }}
                    </div>
                </div>
            </div>
            <div style="text-align:right;">
                <span style="font-size:0.875rem;font-weight:700;color:var(--text-warm);">
                    {{ $part->questions_count }}
                </span>
                <span style="font-size:0.75rem;color:var(--text-faint);">câu</span>
            </div>
        </div>
        @endforeach
    </div>

</div>
@endsection
