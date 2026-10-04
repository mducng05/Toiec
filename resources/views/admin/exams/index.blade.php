@extends('layouts.admin')

@section('title', 'Quản lý đề thi')
@section('page-title', 'Đề thi')

@section('topbar-actions')
    <a href="{{ route('admin.exams.create') }}" class="btn btn-primary btn-sm">
        <svg style="width:14px;height:14px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Tạo đề thi
    </a>
@endsection

@section('content')

{{-- Stats row --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;margin-bottom:1.75rem;">
    @php
        $total     = $exams->total();
        $published = $exams->getCollection()->where('status','published')->count();
        $draft     = $exams->getCollection()->where('status','draft')->count();
        $deleted   = $exams->getCollection()->whereNotNull('deleted_at')->count();
    @endphp
    @foreach([
        ['label'=>'Tổng đề thi', 'val'=>$exams->total(), 'color'=>'var(--gold)'],
        ['label'=>'Đã xuất bản', 'val'=>$exams->getCollection()->where('status','published')->count(), 'color'=>'var(--patina)'],
        ['label'=>'Nháp', 'val'=>$exams->getCollection()->where('status','draft')->count(), 'color'=>'var(--text-muted)'],
        ['label'=>'Đã xóa', 'val'=>$exams->getCollection()->whereNotNull('deleted_at')->count(), 'color'=>'var(--error)'],
    ] as $stat)
    <div class="card" style="padding:1.125rem 1.25rem;">
        <div style="font-size:0.75rem;color:var(--text-faint);letter-spacing:0.05em;text-transform:uppercase;margin-bottom:0.5rem;">
            {{ $stat['label'] }}
        </div>
        <div style="font-size:1.75rem;font-weight:700;color:{{ $stat['color'] }};line-height:1;">
            {{ $stat['val'] }}
        </div>
    </div>
    @endforeach
</div>

{{-- Table card --}}
<div class="card" style="padding:0;overflow:hidden;">
    <div class="gold-bar"></div>

    {{-- Table header --}}
    <div style="display:flex;align-items:center;justify-content:space-between;padding:1.125rem 1.5rem;border-bottom:1px solid var(--border);">
        <div>
            <h2 style="font-size:0.9375rem;font-weight:600;color:var(--text-bright);margin:0;">
                Danh sách đề thi
            </h2>
            <p style="font-size:0.8125rem;color:var(--text-faint);margin:0.125rem 0 0;">
                {{ $exams->total() }} đề thi · Trang {{ $exams->currentPage() }}/{{ $exams->lastPage() }}
            </p>
        </div>
    </div>

    {{-- Table --}}
    @if($exams->isEmpty())
        <div style="text-align:center;padding:4rem 2rem;">
            <div style="width:48px;height:48px;background:var(--bg-graphite);border-radius:var(--r-xl);display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                <svg style="width:22px;height:22px;color:var(--text-faint)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0121 9.586V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <p style="color:var(--text-muted);font-size:0.9375rem;font-weight:500;">Chưa có đề thi nào</p>
            <p style="color:var(--text-faint);font-size:0.875rem;margin-top:0.25rem;">Tạo đề thi đầu tiên để bắt đầu.</p>
            <a href="{{ route('admin.exams.create') }}" class="btn btn-primary" style="margin-top:1.25rem;">
                Tạo đề thi đầu tiên
            </a>
        </div>
    @else
        <table class="table">
            <thead>
                <tr>
                    <th style="padding-left:1.5rem;">Đề thi</th>
                    <th>Trạng thái</th>
                    <th>Parts</th>
                    <th>Thời gian</th>
                    <th>Lượt thi</th>
                    <th>Ngày tạo</th>
                    <th style="text-align:right;padding-right:1.5rem;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @foreach($exams as $exam)
                <tr style="{{ $exam->trashed() ? 'opacity:0.5;' : '' }}">
                    <td style="padding-left:1.5rem;max-width:280px;">
                        <div style="font-weight:500;color:var(--text-bright);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                            {{ $exam->title }}
                        </div>
                        @if($exam->description)
                        <div style="font-size:0.8125rem;color:var(--text-faint);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-top:2px;">
                            {{ $exam->description }}
                        </div>
                        @endif
                        @if($exam->is_full_test)
                            <span class="badge badge-gold" style="margin-top:4px;">Full Test</span>
                        @endif
                    </td>
                    <td>
                        @if($exam->trashed())
                            <span class="badge badge-error">Đã xóa</span>
                        @elseif($exam->status === 'published')
                            <span class="badge badge-patina">Xuất bản</span>
                        @elseif($exam->status === 'archived')
                            <span class="badge badge-muted">Lưu trữ</span>
                        @else
                            <span class="badge badge-muted">Nháp</span>
                        @endif
                    </td>
                    <td>
                        <span style="font-size:0.875rem;color:var(--text-muted);">{{ $exam->parts_count }}</span>
                    </td>
                    <td>
                        <span style="font-size:0.875rem;color:var(--text-muted);">{{ $exam->duration_minutes }} phút</span>
                    </td>
                    <td>
                        <span style="font-size:0.875rem;color:var(--text-muted);">{{ $exam->attempts_count }}</span>
                    </td>
                    <td>
                        <span style="font-size:0.8125rem;color:var(--text-faint);">
                            {{ $exam->created_at->format('d/m/Y') }}
                        </span>
                    </td>
                    <td style="padding-right:1.5rem;">
                        <div style="display:flex;align-items:center;justify-content:flex-end;gap:6px;">
                            @if(!$exam->trashed())
                                <a href="{{ route('admin.exams.show', $exam) }}"
                                   class="btn btn-ghost btn-sm btn-icon" title="Chi tiết">
                                    <svg style="width:14px;height:14px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                <a href="{{ route('admin.exams.edit', $exam) }}"
                                   class="btn btn-ghost btn-sm btn-icon" title="Sửa">
                                    <svg style="width:14px;height:14px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form method="POST" action="{{ route('admin.exams.toggle-publish', $exam) }}">
                                    @csrf
                                    <button type="submit"
                                            class="btn btn-sm btn-icon {{ $exam->status === 'published' ? 'btn-ghost' : 'btn-secondary' }}"
                                            title="{{ $exam->status === 'published' ? 'Ẩn đề' : 'Xuất bản' }}">
                                        @if($exam->status === 'published')
                                            <svg style="width:14px;height:14px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                            </svg>
                                        @else
                                            <svg style="width:14px;height:14px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        @endif
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.exams.destroy', $exam) }}"
                                      onsubmit="return confirm('Xóa đề thi này?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm btn-icon" title="Xóa">
                                        <svg style="width:14px;height:14px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Pagination --}}
        @if($exams->hasPages())
            <div style="padding:1rem 1.5rem;border-top:1px solid var(--border);">
                {{ $exams->links() }}
            </div>
        @endif
    @endif
</div>
@endsection
