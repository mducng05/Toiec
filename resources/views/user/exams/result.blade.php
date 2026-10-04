@extends('layouts.app')

@section('title', 'Kết quả thi: ' . $attempt->exam->title)

@section('content')
<div style="max-width:960px;margin:3rem auto;padding:0 1.5rem;">

    @php
        $res = $attempt->result;
        $timeSpentMins = $attempt->time_spent_seconds ? floor($attempt->time_spent_seconds / 60) : 0;
        $timeSpentSecs = $attempt->time_spent_seconds ? ($attempt->time_spent_seconds % 60) : 0;
    @endphp

    {{-- Breadcrumb --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;flex-wrap:wrap;gap:8px;">
        <a href="{{ route('home') }}" style="display:inline-flex;align-items:center;gap:6px;font-size:0.8125rem;color:var(--text-faint);text-decoration:none;transition:color 0.15s;" onmouseover="this.style.color='var(--gold)'" onmouseout="this.style.color='var(--text-faint)'">
            <svg style="width:14px;height:14px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Quay lại trang chủ
        </a>
        <span style="font-size:0.8125rem;color:var(--text-faint);">
            Hoàn thành lúc: {{ $attempt->completed_at ? $attempt->completed_at->format('d/m/Y H:i') : '—' }}
        </span>
    </div>

    {{-- ── 1. Hero Score Banner ──────────────────────────────── --}}
    <div class="card" style="padding:0;overflow:hidden;margin-bottom:2rem;text-align:center;">
        <div class="gold-bar"></div>

        <div style="padding:3rem 2rem;background:radial-gradient(ellipse 60% 40% at 50% 0%, oklch(84% 0.19 80.46 / 0.12), transparent 70%), var(--bg-deep);">
            
            <div style="display:inline-flex;align-items:center;gap:8px;padding:0.35rem 0.85rem;background:oklch(84% 0.19 80.46 / 0.08);border:1px solid oklch(84% 0.19 80.46 / 0.2);border-radius:var(--r-full);font-size:0.8125rem;color:var(--gold);margin-bottom:1rem;">
                <span>KẾT QUẢ ĐÁNH GIÁ CHUẨN TOEIC</span>
            </div>

            <h1 style="font-size:1.5rem;font-weight:700;color:var(--text-bright);margin:0 0 1.5rem;">
                {{ $attempt->exam->title }}
            </h1>

            {{-- Big Total Score Circle / Badge --}}
            <div style="display:flex;align-items:baseline;justify-content:center;gap:8px;margin-bottom:2rem;">
                <span style="font-size:clamp(3.5rem, 8vw, 5rem);font-weight:800;color:var(--gold);line-height:1;letter-spacing:-0.03em;">
                    {{ $res ? $res->total_score : 0 }}
                </span>
                <span style="font-size:1.5rem;font-weight:600;color:var(--text-faint);">
                    / 990
                </span>
            </div>

            {{-- Listening & Reading Score Subcards --}}
            <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(240px, 1fr));gap:1.25rem;max-width:600px;margin:0 auto 2.5rem;">
                
                {{-- Listening Score --}}
                <div style="padding:1.25rem;background:var(--bg-raised);border:1px solid var(--border);border-radius:var(--r-xl);">
                    <div style="font-size:0.75rem;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:var(--patina);margin-bottom:0.4rem;">
                        Listening Score
                    </div>
                    <div style="font-size:2rem;font-weight:800;color:var(--text-bright);line-height:1;">
                        {{ $res ? $res->listening_score : 0 }}
                        <span style="font-size:0.875rem;font-weight:400;color:var(--text-faint);">/ 495</span>
                    </div>
                    <div style="font-size:0.75rem;color:var(--text-faint);margin-top:0.4rem;">
                        {{ $res ? $res->listening_correct : 0 }} câu đúng
                    </div>
                </div>

                {{-- Reading Score --}}
                <div style="padding:1.25rem;background:var(--bg-raised);border:1px solid var(--border);border-radius:var(--r-xl);">
                    <div style="font-size:0.75rem;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:var(--gold);margin-bottom:0.4rem;">
                        Reading Score
                    </div>
                    <div style="font-size:2rem;font-weight:800;color:var(--text-bright);line-height:1;">
                        {{ $res ? $res->reading_score : 0 }}
                        <span style="font-size:0.875rem;font-weight:400;color:var(--text-faint);">/ 495</span>
                    </div>
                    <div style="font-size:0.75rem;color:var(--text-faint);margin-top:0.4rem;">
                        {{ $res ? $res->reading_correct : 0 }} câu đúng
                    </div>
                </div>

            </div>

            {{-- Main CTA Buttons --}}
            <div style="display:flex;align-items:center;justify-content:center;gap:12px;flex-wrap:wrap;">
                <a href="{{ route('exams.review', $attempt) }}" class="btn btn-primary btn-lg">
                    <svg style="width:16px;height:16px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    Xem lại đáp án & Giải thích chi tiết
                </a>
                <form method="POST" action="{{ route('exams.start', $attempt->exam) }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-secondary btn-lg">
                        Làm lại đề thi
                    </button>
                </form>
            </div>

        </div>
    </div>

    {{-- ── 2. KPI Metrics Grid ────────────────────────────────── --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(200px, 1fr));gap:1rem;margin-bottom:2rem;">
        
        <div class="card" style="padding:1.25rem;">
            <div style="font-size:0.75rem;color:var(--text-faint);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.4rem;">
                Đúng
            </div>
            <div style="font-size:1.75rem;font-weight:700;color:var(--patina);line-height:1;">
                {{ $res ? $res->correct_answers : 0 }}
                <span style="font-size:0.875rem;font-weight:400;color:var(--text-faint);">/ {{ $res ? $res->total_questions : 0 }}</span>
            </div>
        </div>

        <div class="card" style="padding:1.25rem;">
            <div style="font-size:0.75rem;color:var(--text-faint);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.4rem;">
                Sai
            </div>
            <div style="font-size:1.75rem;font-weight:700;color:var(--error);line-height:1;">
                {{ $res ? $res->wrong_answers : 0 }}
            </div>
        </div>

        <div class="card" style="padding:1.25rem;">
            <div style="font-size:0.75rem;color:var(--text-faint);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.4rem;">
                Chưa làm
            </div>
            <div style="font-size:1.75rem;font-weight:700;color:var(--text-muted);line-height:1;">
                {{ $res ? $res->unanswered : 0 }}
            </div>
        </div>

        <div class="card" style="padding:1.25rem;">
            <div style="font-size:0.75rem;color:var(--text-faint);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.4rem;">
                Thời gian làm bài
            </div>
            <div style="font-size:1.75rem;font-weight:700;color:var(--text-warm);line-height:1;">
                {{ $timeSpentMins }}m {{ $timeSpentSecs }}s
            </div>
        </div>

    </div>

    {{-- ── 3. Part-by-Part Performance Analysis ───────────────── --}}
    <div class="card" style="padding:0;overflow:hidden;margin-bottom:3rem;">
        <div style="padding:1.25rem 1.5rem;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;">
            <h3 style="font-size:1rem;font-weight:700;color:var(--text-bright);margin:0;">
                Phân tích độ chính xác theo từng Part
            </h3>
            <span style="font-size:0.8125rem;color:var(--text-faint);">
                Tỷ lệ chính xác tổng thể: <strong style="color:var(--gold);">{{ $res ? $res->accuracy_percentage : 0 }}%</strong>
            </span>
        </div>

        <div style="overflow-x:auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th style="padding-left:1.5rem;">Phần thi</th>
                        <th>Kỹ năng</th>
                        <th>Tổng câu</th>
                        <th>Đúng</th>
                        <th>Sai</th>
                        <th>Chưa làm</th>
                        <th style="min-width:180px;padding-right:1.5rem;">Độ chính xác</th>
                    </tr>
                </thead>
                <tbody>
                    @if($res && $res->part_results)
                        @foreach($res->part_results as $pKey => $pData)
                        <tr>
                            <td style="padding-left:1.5rem;font-weight:600;color:var(--text-bright);">
                                Part {{ $pData['part_number'] }}: {{ $pData['title'] }}
                            </td>
                            <td>
                                <span class="badge {{ $pData['section'] === 'listening' ? 'badge-patina' : 'badge-gold' }}" style="font-size:0.6875rem;">
                                    {{ $pData['section'] === 'listening' ? 'Listening' : 'Reading' }}
                                </span>
                            </td>
                            <td>{{ $pData['total'] }}</td>
                            <td style="color:var(--patina);font-weight:600;">{{ $pData['correct'] }}</td>
                            <td style="color:var(--error);">{{ $pData['wrong'] }}</td>
                            <td style="color:var(--text-faint);">{{ $pData['unanswered'] }}</td>
                            <td style="padding-right:1.5rem;">
                                <div style="display:flex;align-items:center;gap:10px;">
                                    <div style="flex:1;height:6px;background:var(--bg-deep);border-radius:var(--r-full);overflow:hidden;">
                                        <div style="height:100%;background:{{ $pData['accuracy'] >= 70 ? 'var(--patina)' : ($pData['accuracy'] >= 50 ? 'var(--gold)' : 'var(--error)') }};width:{{ $pData['accuracy'] }}%;"></div>
                                    </div>
                                    <span style="font-size:0.75rem;font-weight:700;color:var(--text-bright);min-width:38px;">
                                        {{ $pData['accuracy'] }}%
                                    </span>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
