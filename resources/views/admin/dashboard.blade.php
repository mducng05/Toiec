@extends('layouts.admin')

@section('title', 'Dashboard — Admin')
@section('page-title', 'Tổng quan hệ thống')

@section('topbar-actions')
    <a href="{{ route('admin.exams.create') }}" class="btn btn-primary btn-sm">
        <svg style="width:14px;height:14px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Tạo đề thi
    </a>
@endsection

@section('content')

{{-- Stats grid --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(220px, 1fr));gap:1rem;margin-bottom:1.75rem;">

    {{-- Total exams --}}
    <div class="card" style="padding:1.25rem;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.75rem;">
            <span style="font-size:0.75rem;color:var(--text-faint);letter-spacing:0.05em;text-transform:uppercase;">Tổng đề thi</span>
            <div style="width:34px;height:34px;border-radius:var(--r-md);background:oklch(84% 0.19 80.46 / 0.1);display:flex;align-items:center;justify-content:center;color:var(--gold);">
                <svg style="width:17px;height:17px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0121 9.586V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
        </div>
        <div style="font-size:2rem;font-weight:700;color:var(--text-bright);line-height:1;">
            {{ $stats['total_exams'] }}
        </div>
        <div style="font-size:0.75rem;color:var(--gold);margin-top:0.4rem;">
            {{ $stats['published_exams'] }} đề đã xuất bản
        </div>
    </div>

    {{-- Published exams --}}
    <div class="card" style="padding:1.25rem;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.75rem;">
            <span style="font-size:0.75rem;color:var(--text-faint);letter-spacing:0.05em;text-transform:uppercase;">Đang hoạt động</span>
            <div style="width:34px;height:34px;border-radius:var(--r-md);background:oklch(70% 0.12 188 / 0.1);display:flex;align-items:center;justify-content:center;color:var(--patina);">
                <svg style="width:17px;height:17px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        <div style="font-size:2rem;font-weight:700;color:var(--patina);line-height:1;">
            {{ $stats['published_exams'] }}
        </div>
        <div style="font-size:0.75rem;color:var(--text-faint);margin-top:0.4rem;">
            Học viên có thể vào thi
        </div>
    </div>

    {{-- Total users --}}
    <div class="card" style="padding:1.25rem;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.75rem;">
            <span style="font-size:0.75rem;color:var(--text-faint);letter-spacing:0.05em;text-transform:uppercase;">Người dùng</span>
            <div style="width:34px;height:34px;border-radius:var(--r-md);background:var(--bg-deep);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;color:var(--text-muted);">
                <svg style="width:17px;height:17px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                          d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
        </div>
        <div style="font-size:2rem;font-weight:700;color:var(--text-warm);line-height:1;">
            {{ $stats['total_users'] }}
        </div>
        <div style="font-size:0.75rem;color:var(--text-faint);margin-top:0.4rem;">
            Tài khoản trên hệ thống
        </div>
    </div>

    {{-- Total attempts --}}
    <div class="card" style="padding:1.25rem;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.75rem;">
            <span style="font-size:0.75rem;color:var(--text-faint);letter-spacing:0.05em;text-transform:uppercase;">Lượt làm bài</span>
            <div style="width:34px;height:34px;border-radius:var(--r-md);background:oklch(84% 0.19 80.46 / 0.08);display:flex;align-items:center;justify-content:center;color:var(--gold);">
                <svg style="width:17px;height:17px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                          d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
        </div>
        <div style="font-size:2rem;font-weight:700;color:var(--text-bright);line-height:1;">
            {{ $stats['total_attempts'] }}
        </div>
        <div style="font-size:0.75rem;color:var(--text-faint);margin-top:0.4rem;">
            Lượt nộp bài hoàn thành
        </div>
    </div>

</div>

{{-- Quick actions card --}}
<div class="card" style="padding:0;overflow:hidden;margin-bottom:1.75rem;">
    <div class="gold-bar"></div>
    <div style="padding:1.25rem 1.5rem;border-bottom:1px solid var(--border);">
        <h2 style="font-size:0.9375rem;font-weight:600;color:var(--text-bright);margin:0;">
            Thao tác nhanh
        </h2>
    </div>
    <div style="padding:1.25rem 1.5rem;display:grid;grid-template-columns:repeat(auto-fit, minmax(220px, 1fr));gap:1rem;">
        
        <a href="{{ route('admin.exams.create') }}" class="card card-hover" style="display:flex;align-items:center;gap:12px;padding:1rem;text-decoration:none;background:var(--bg-deep);">
            <div style="width:36px;height:36px;border-radius:var(--r-md);background:oklch(84% 0.19 80.46 / 0.1);display:flex;align-items:center;justify-content:center;color:var(--gold);flex-shrink:0;">
                <svg style="width:18px;height:18px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
            </div>
            <div>
                <div style="font-size:0.875rem;font-weight:600;color:var(--text-bright);">Tạo đề thi mới</div>
                <div style="font-size:0.75rem;color:var(--text-faint);">Khởi tạo bộ đề TOEIC</div>
            </div>
        </a>

        <a href="{{ route('admin.exams.index') }}" class="card card-hover" style="display:flex;align-items:center;gap:12px;padding:1rem;text-decoration:none;background:var(--bg-deep);">
            <div style="width:36px;height:36px;border-radius:var(--r-md);background:oklch(70% 0.12 188 / 0.1);display:flex;align-items:center;justify-content:center;color:var(--patina);flex-shrink:0;">
                <svg style="width:18px;height:18px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
            </div>
            <div>
                <div style="font-size:0.875rem;font-weight:600;color:var(--text-bright);">Quản lý File & Audio</div>
                <div style="font-size:0.75rem;color:var(--text-faint);">Tải lên tài liệu đề thi</div>
            </div>
        </a>

        <a href="{{ route('admin.exams.index') }}" class="card card-hover" style="display:flex;align-items:center;gap:12px;padding:1rem;text-decoration:none;background:var(--bg-deep);">
            <div style="width:36px;height:36px;border-radius:var(--r-md);background:oklch(84% 0.19 80.46 / 0.08);display:flex;align-items:center;justify-content:center;color:var(--gold);flex-shrink:0;">
                <svg style="width:18px;height:18px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0121 9.586V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <div style="font-size:0.875rem;font-weight:600;color:var(--text-bright);">Danh sách đề thi</div>
                <div style="font-size:0.75rem;color:var(--text-faint);">Xem toàn bộ đề thi</div>
            </div>
        </a>

    </div>
</div>

{{-- Phase Status Banner --}}
<div class="card" style="padding:1.25rem 1.5rem;background:oklch(84% 0.19 80.46 / 0.04);border-color:oklch(84% 0.19 80.46 / 0.2);display:flex;align-items:center;gap:14px;">
    <div style="width:8px;height:8px;border-radius:50%;background:var(--gold);flex-shrink:0;box-shadow:0 0 8px var(--gold);"></div>
    <div style="font-size:0.875rem;color:var(--text-warm);">
        <strong>Phase 2 hoàn thành:</strong> Hệ thống quản lý đề thi (Exam CRUD, Part selector, Uploads PDF & Audio) đã sẵn sàng. Sẵn sàng bước sang <strong>Phase 3: Quản lý câu hỏi & cấu trúc bài thi chi tiết (Passage / Question CRUD)</strong>.
    </div>
</div>

@endsection
