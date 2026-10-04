@extends('layouts.app')

@section('title', 'Lịch sử luyện thi — TOEIC Practice')

@section('content')
<div style="max-width:1100px;margin:2.5rem auto;padding:0 1.5rem;">

    {{-- Header --}}
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;margin-bottom:2rem;">
        <div>
            <h1 style="font-size:1.75rem;font-weight:800;color:var(--text-bright);margin:0 0 0.35rem;letter-spacing:-0.02em;">
                Lịch sử làm bài thi
            </h1>
            <p style="font-size:0.875rem;color:var(--text-muted);margin:0;">
                Theo dõi tiến trình ôn luyện, điểm số Listening, Reading và xem lại chi tiết từng câu hỏi.
            </p>
        </div>
        <a href="{{ route('home') }}" class="btn btn-primary">
            Làm bài thi mới →
        </a>
    </div>

    @if($attempts->isEmpty())
        <div class="card" style="text-align:center;padding:4rem 2rem;">
            <div style="width:56px;height:56px;border-radius:var(--r-xl);background:var(--bg-deep);display:flex;align-items:center;justify-content:center;margin:0 auto 1.25rem;font-size:1.5rem;">
                📝
            </div>
            <h3 style="font-size:1.125rem;font-weight:700;color:var(--text-bright);margin:0 0 0.5rem;">
                Bạn chưa có lượt làm bài nào
            </h3>
            <p style="font-size:0.875rem;color:var(--text-muted);max-width:440px;margin:0 auto 1.5rem;">
                Hãy chọn một bộ đề thi trong thư viện hoặc tải lên đề riêng của bạn để bắt đầu luyện tập ngay hôm nay!
            </p>
            <div style="display:flex;justify-content:center;gap:10px;">
                <a href="{{ route('home') }}" class="btn btn-primary">
                    Khám phá thư viện đề
                </a>
                <a href="{{ route('my-exams.create') }}" class="btn btn-secondary">
                    + Tải lên đề riêng
                </a>
            </div>
        </div>
    @else
        <div class="card" style="padding:0;overflow:hidden;">
            <table class="table" style="margin:0;">
                <thead>
                    <tr>
                        <th style="padding-left:1.5rem;">Đề thi</th>
                        <th>Thời gian</th>
                        <th>Điểm tổng</th>
                        <th>Listening</th>
                        <th>Reading</th>
                        <th>Độ chính xác</th>
                        <th>Trạng thái</th>
                        <th style="text-align:right;padding-right:1.5rem;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($attempts as $attempt)
                        <tr>
                            <td style="padding-left:1.5rem;">
                                <div style="font-weight:700;color:var(--text-bright);font-size:0.9375rem;">
                                    {{ $attempt->exam->title ?? 'Đề thi đã bị xóa' }}
                                </div>
                                <div style="font-size:0.75rem;color:var(--text-muted);">
                                    Bắt đầu: {{ $attempt->started_at?->format('d/m/Y H:i') ?? 'N/A' }}
                                </div>
                            </td>
                            <td>
                                @if($attempt->result)
                                    <span style="font-size:0.875rem;color:var(--text-warm);">
                                        {{ floor($attempt->result->time_spent_seconds / 60) }} phút
                                    </span>
                                @else
                                    <span style="color:var(--text-muted);font-size:0.8125rem;">—</span>
                                @endif
                            </td>
                            <td>
                                @if($attempt->result)
                                    <div style="font-size:1.15rem;font-weight:800;color:var(--gold);">
                                        {{ $attempt->result->total_score }}
                                        <span style="font-size:0.75rem;font-weight:500;color:var(--text-muted);">/ 990</span>
                                    </div>
                                @else
                                    <span style="color:var(--text-muted);font-size:0.8125rem;">Chưa có</span>
                                @endif
                            </td>
                            <td>
                                @if($attempt->result)
                                    <span style="font-weight:600;color:var(--text-bright);">
                                        {{ $attempt->result->listening_score ?? '—' }}
                                    </span>
                                @else
                                    <span style="color:var(--text-muted);">—</span>
                                @endif
                            </td>
                            <td>
                                @if($attempt->result)
                                    <span style="font-weight:600;color:var(--text-bright);">
                                        {{ $attempt->result->reading_score ?? '—' }}
                                    </span>
                                @else
                                    <span style="color:var(--text-muted);">—</span>
                                @endif
                            </td>
                            <td>
                                @if($attempt->result)
                                    <div style="display:flex;align-items:center;gap:6px;">
                                        <span style="font-weight:700;color:var(--patina);">
                                            {{ $attempt->result->accuracy_percentage }}%
                                        </span>
                                        <span style="font-size:0.75rem;color:var(--text-muted);">
                                            ({{ $attempt->result->correct_count }}/{{ $attempt->result->total_questions }})
                                        </span>
                                    </div>
                                @else
                                    <span style="color:var(--text-muted);">—</span>
                                @endif
                            </td>
                            <td>
                                @if($attempt->status === 'completed')
                                    <span class="badge badge-green">Hoàn thành</span>
                                @elseif($attempt->status === 'in_progress')
                                    <span class="badge badge-gold">Đang làm</span>
                                @else
                                    <span class="badge badge-gray">Bỏ dở</span>
                                @endif
                            </td>
                            <td style="text-align:right;padding-right:1.5rem;">
                                @if($attempt->status === 'completed')
                                    <div style="display:inline-flex;gap:6px;">
                                        <a href="{{ route('exams.results', $attempt) }}" class="btn btn-secondary btn-sm">
                                            Kết quả
                                        </a>
                                        <a href="{{ route('exams.review', $attempt) }}" class="btn btn-ghost btn-sm">
                                            Xem lại
                                        </a>
                                    </div>
                                @else
                                    <a href="{{ route('exams.take', $attempt) }}" class="btn btn-primary btn-sm">
                                        Tiếp tục làm bài →
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if($attempts->hasPages())
                <div style="padding:1.25rem 1.5rem;border-top:1px solid var(--border);background:var(--bg-deep);">
                    {{ $attempts->links() }}
                </div>
            @endif
        </div>
    @endif

</div>
@endsection
