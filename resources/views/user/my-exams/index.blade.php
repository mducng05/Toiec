@extends('layouts.app')

@section('title', 'Bộ đề thi của tôi — TOEIC Practice')

@section('content')
<div style="max-width:1200px;margin:2.5rem auto;padding:0 1.5rem;">

    {{-- Top Header --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:2rem;flex-wrap:wrap;gap:1rem;">
        <div>
            <h1 style="font-size:1.75rem;font-weight:800;color:var(--text-bright);margin:0 0 0.35rem;">
                Bộ đề thi của tôi
            </h1>
            <p style="font-size:0.875rem;color:var(--text-faint);margin:0;">
                Tự tải lên tài liệu PDF, file nghe audio MP3 và quản lý bộ đề thi riêng để tự luyện tập.
            </p>
        </div>

        <a href="{{ route('my-exams.create') }}" class="btn btn-primary">
            <svg style="width:16px;height:16px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tạo đề thi mới
        </a>
    </div>

    {{-- Stats Row --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(220px, 1fr));gap:1rem;margin-bottom:2rem;">
        <div class="card" style="padding:1.25rem;">
            <div style="font-size:0.75rem;color:var(--text-faint);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.4rem;">
                Tổng đề tự tạo
            </div>
            <div style="font-size:1.75rem;font-weight:800;color:var(--gold);line-height:1;">
                {{ $exams->total() }}
            </div>
        </div>
        <div class="card" style="padding:1.25rem;">
            <div style="font-size:0.75rem;color:var(--text-faint);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.4rem;">
                Công khai
            </div>
            <div style="font-size:1.75rem;font-weight:800;color:var(--patina);line-height:1;">
                {{ $exams->getCollection()->where('status', 'published')->count() }}
            </div>
        </div>
        <div class="card" style="padding:1.25rem;">
            <div style="font-size:0.75rem;color:var(--text-faint);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.4rem;">
                Riêng tư
            </div>
            <div style="font-size:1.75rem;font-weight:800;color:var(--text-muted);line-height:1;">
                {{ $exams->getCollection()->where('status', 'draft')->count() }}
            </div>
        </div>
    </div>

    {{-- Exams Grid --}}
    @if($exams->isEmpty())
        <div class="card" style="text-align:center;padding:4rem 2rem;">
            <div style="width:52px;height:52px;background:var(--bg-deep);border-radius:var(--r-xl);display:flex;align-items:center;justify-content:center;margin:0 auto 1.25rem;">
                <svg style="width:24px;height:24px;color:var(--text-faint)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0121 9.586V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <h3 style="color:var(--text-warm);font-size:1.125rem;font-weight:700;margin:0 0 0.5rem;">
                Bạn chưa tạo đề thi nào
            </h3>
            <p style="color:var(--text-faint);font-size:0.875rem;margin:0 0 1.5rem;">
                Bạn có tài liệu PDF hoặc file âm thanh TOEIC riêng? Hãy tạo đề thi để tự luyện tập và chấm điểm!
            </p>
            <a href="{{ route('my-exams.create') }}" class="btn btn-primary">
                + Tải lên đề thi đầu tiên của bạn
            </a>
        </div>
    @else
        <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(320px, 1fr));gap:1.5rem;">
            @foreach($exams as $exam)
            <div class="card card-hover" style="display:flex;flex-direction:column;justify-content:space-between;padding:0;overflow:hidden;">
                
                {{-- Top Bar --}}
                <div class="gold-bar"></div>

                <div style="padding:1.5rem;flex:1;display:flex;flex-direction:column;">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.75rem;">
                        <span class="badge {{ $exam->status === 'published' ? 'badge-patina' : 'badge-muted' }}">
                            {{ $exam->status === 'published' ? 'Công khai' : 'Riêng tư' }}
                        </span>
                        <span style="font-size:0.75rem;color:var(--text-faint);">
                            {{ $exam->parts_count }} Parts · {{ $exam->total_questions }} câu
                        </span>
                    </div>

                    <h3 style="font-size:1.125rem;font-weight:700;color:var(--text-bright);line-height:1.35;margin:0 0 0.5rem;">
                        {{ $exam->title }}
                    </h3>

                    @if($exam->description)
                        <p style="font-size:0.8125rem;color:var(--text-muted);line-height:1.5;margin:0 0 1.25rem;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                            {{ $exam->description }}
                        </p>
                    @endif

                    <div style="display:flex;align-items:center;gap:12px;font-size:0.75rem;color:var(--text-faint);margin-top:auto;padding-top:1rem;border-top:1px solid var(--border);">
                        <span>Thời lượng: {{ $exam->duration_minutes }} phút</span>
                        <span>·</span>
                        <span>{{ $exam->attempts_count }} lượt làm</span>
                    </div>
                </div>

                {{-- Action Footer --}}
                <div style="padding:0.875rem 1.25rem;background:var(--bg-deep);border-top:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;gap:8px;">
                    <a href="{{ route('my-exams.show', $exam) }}" class="btn btn-secondary btn-sm" style="flex:1;justify-content:center;">
                        Quản lý đề
                    </a>
                    <a href="{{ route('exams.show', $exam) }}" class="btn btn-primary btn-sm" style="flex:1;justify-content:center;">
                        Làm bài thi
                    </a>
                </div>

            </div>
            @endforeach
        </div>

        @if($exams->hasPages())
            <div style="margin-top:2.5rem;display:flex;justify-content:center;">
                {{ $exams->links() }}
            </div>
        @endif
    @endif

</div>
@endsection
