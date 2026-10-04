@extends('layouts.app')

@section('title', 'Luyện thi TOEIC — TOEIC Practice Platform')

@section('content')

{{-- ── Hero Section ────────────────────────────────────────── --}}
<section style="position:relative;overflow:hidden;padding:5rem 1.5rem 4.5rem;background:radial-gradient(ellipse 80% 50% at 50% -20%, oklch(84% 0.19 80.46 / 0.12), transparent 70%), var(--bg-ground);border-bottom:1px solid var(--border);">
    <div style="max-width:1200px;margin:0 auto;text-align:center;">
        
        <div style="display:inline-flex;align-items:center;gap:8px;padding:0.35rem 0.85rem;background:oklch(84% 0.19 80.46 / 0.08);border:1px solid oklch(84% 0.19 80.46 / 0.2);border-radius:var(--r-full);font-size:0.8125rem;color:var(--gold);margin-bottom:1.5rem;">
            <span style="width:7px;height:7px;border-radius:50%;background:var(--patina);box-shadow:0 0 8px var(--patina);"></span>
            <span>{{ $exams->total() }} bộ đề thi tuyển chọn đang sẵn sàng</span>
        </div>

        <h1 style="font-size:clamp(2.25rem, 5vw, 3.5rem);font-weight:800;color:var(--text-bright);line-height:1.15;letter-spacing:-0.03em;margin:0 auto 1.25rem;max-width:850px;">
            Luyện thi TOEIC chuẩn định dạng<br>
            <span style="background:linear-gradient(135deg, var(--gold) 0%, oklch(92% 0.12 85) 100%);-webkit-background-clip:text;-webkit-text-fill-color:transparent;">
                Hiệu quả & Bứt phá điểm số
            </span>
        </h1>

        <p style="font-size:1.0625rem;color:var(--text-muted);max-width:620px;margin:0 auto 2.25rem;line-height:1.6;">
            Trải nghiệm làm đề TOEIC bấm giờ thực tế, nghe audio chất lượng cao, đối soát đáp án và nhận phân tích chi tiết từng kỹ năng.
        </p>

        @guest
            <div style="display:flex;align-items:center;justify-content:center;gap:12px;flex-wrap:wrap;">
                <a href="{{ route('register') }}" class="btn btn-primary btn-lg">
                    Bắt đầu làm bài miễn phí
                    <svg style="width:16px;height:16px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
                <a href="{{ route('login') }}" class="btn btn-ghost btn-lg">
                    Đã có tài khoản
                </a>
            </div>
        @else
            <div style="display:flex;align-items:center;justify-content:center;gap:12px;">
                <a href="#exam-list" class="btn btn-primary btn-lg">
                    Khám phá danh sách đề
                    <svg style="width:16px;height:16px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                    </svg>
                </a>
            </div>
        @endguest

    </div>
</section>

{{-- ── Features Banner ─────────────────────────────────────── --}}
<section style="background:var(--bg-deep);border-bottom:1px solid var(--border);padding:1.5rem 1.5rem;">
    <div style="max-width:1200px;margin:0 auto;display:grid;grid-template-columns:repeat(auto-fit, minmax(260px, 1fr));gap:1.5rem;">
        
        <div style="display:flex;align-items:center;gap:12px;">
            <div style="width:38px;height:38px;border-radius:var(--r-md);background:oklch(84% 0.19 80.46 / 0.1);display:flex;align-items:center;justify-content:center;color:var(--gold);flex-shrink:0;">
                <svg style="width:20px;height:20px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0121 9.586V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <div style="font-size:0.875rem;font-weight:600;color:var(--text-bright);">Đề thi TOEIC Chuẩn</div>
                <div style="font-size:0.75rem;color:var(--text-faint);">Cập nhật format mới từ các bộ đề uy tín</div>
            </div>
        </div>

        <div style="display:flex;align-items:center;gap:12px;">
            <div style="width:38px;height:38px;border-radius:var(--r-md);background:oklch(70% 0.12 188 / 0.1);display:flex;align-items:center;justify-content:center;color:var(--patina);flex-shrink:0;">
                <svg style="width:20px;height:20px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/>
                </svg>
            </div>
            <div>
                <div style="font-size:0.875rem;font-weight:600;color:var(--text-bright);">Audio Chuẩn Phòng Thi</div>
                <div style="font-size:0.75rem;color:var(--text-faint);">Tốc độ & giọng đọc thực tế Part 1 - 4</div>
            </div>
        </div>

        <div style="display:flex;align-items:center;gap:12px;">
            <div style="width:38px;height:38px;border-radius:var(--r-md);background:oklch(84% 0.19 80.46 / 0.1);display:flex;align-items:center;justify-content:center;color:var(--gold);flex-shrink:0;">
                <svg style="width:20px;height:20px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <div>
                <div style="font-size:0.875rem;font-weight:600;color:var(--text-bright);">Chấm Điểm Tự Động</div>
                <div style="font-size:0.75rem;color:var(--text-faint);">Biết ngay kết quả theo thang điểm 990</div>
            </div>
        </div>

    </div>
</section>

{{-- ── Exam List Section ───────────────────────────────────── --}}
<section id="exam-list" style="max-width:1200px;margin:0 auto;padding:4rem 1.5rem;">

    <div style="display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:2rem;flex-wrap:wrap;gap:1rem;">
        <div>
            <h2 style="font-size:1.5rem;font-weight:700;color:var(--text-bright);margin:0 0 0.35rem;">
                Danh mục đề thi
            </h2>
            <p style="font-size:0.875rem;color:var(--text-faint);margin:0;">
                Chọn đề để bắt đầu luyện tập bấm giờ
            </p>
        </div>
        <span style="font-size:0.8125rem;color:var(--text-muted);background:var(--bg-deep);padding:0.4rem 0.85rem;border-radius:var(--r-full);border:1px solid var(--border);">
            {{ $exams->total() }} đề thi
        </span>
    </div>

    @if($exams->isEmpty())
        <div class="card" style="text-align:center;padding:4rem 2rem;">
            <div style="width:52px;height:52px;background:var(--bg-graphite);border-radius:var(--r-xl);display:flex;align-items:center;justify-content:center;margin:0 auto 1.25rem;">
                <svg style="width:24px;height:24px;color:var(--text-faint)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0121 9.586V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <h3 style="color:var(--text-warm);font-size:1.125rem;font-weight:600;margin:0 0 0.5rem;">
                Chưa có đề thi nào được xuất bản
            </h3>
            <p style="color:var(--text-faint);font-size:0.875rem;margin:0;">
                Giáo viên & Quản trị viên đang chuẩn bị các bộ đề mới. Vui lòng quay lại sau!
            </p>
        </div>
    @else
        <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(320px, 1fr));gap:1.5rem;">
            @foreach($exams as $exam)
            <div class="card card-hover" style="display:flex;flex-direction:column;justify-content:space-between;padding:0;overflow:hidden;">
                
                {{-- Accent Bar --}}
                <div style="height:3px;background:{{ $exam->is_full_test ? 'linear-gradient(90deg, var(--gold), oklch(92% 0.12 85))' : 'linear-gradient(90deg, var(--patina), oklch(75% 0.15 160))' }};"></div>

                <div style="padding:1.5rem;flex:1;display:flex;flex-direction:column;">
                    
                    {{-- Badges row --}}
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
                        <span class="badge {{ $exam->is_full_test ? 'badge-gold' : 'badge-patina' }}">
                            {{ $exam->is_full_test ? 'Full Test' : 'Mini Test' }}
                        </span>
                        <span style="font-size:0.75rem;color:var(--text-faint);font-weight:500;">
                            {{ $exam->parts_count }} Parts
                        </span>
                    </div>

                    {{-- Title --}}
                    <h3 style="font-size:1.125rem;font-weight:700;color:var(--text-bright);line-height:1.35;margin:0 0 0.5rem;">
                        {{ $exam->title }}
                    </h3>

                    {{-- Description --}}
                    @if($exam->description)
                        <p style="font-size:0.8125rem;color:var(--text-muted);line-height:1.5;margin:0 0 1.25rem;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                            {{ $exam->description }}
                        </p>
                    @else
                        <div style="margin-bottom:1.25rem;"></div>
                    @endif

                    {{-- Meta details --}}
                    <div style="display:flex;align-items:center;gap:16px;font-size:0.75rem;color:var(--text-faint);margin-top:auto;padding-top:1rem;border-top:1px solid var(--border);">
                        <div style="display:flex;align-items:center;gap:5px;">
                            <svg style="width:13px;height:13px;color:var(--gold);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ $exam->duration_minutes }} phút
                        </div>
                        <div style="display:flex;align-items:center;gap:5px;">
                            <svg style="width:13px;height:13px;color:var(--patina);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ $exam->total_questions }} câu
                        </div>
                    </div>

                </div>

                {{-- Action button footer --}}
                <div style="padding:1rem 1.5rem;background:var(--bg-deep);border-top:1px solid var(--border);">
                    <a href="{{ route('exams.show', $exam) }}" class="btn btn-primary" style="width:100%;justify-content:center;">
                        Làm bài thi
                        <svg style="width:14px;height:14px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </a>
                </div>

            </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($exams->hasPages())
            <div style="margin-top:2.5rem;display:flex;justify-content:center;">
                {{ $exams->links() }}
            </div>
        @endif
    @endif

</section>

@endsection
