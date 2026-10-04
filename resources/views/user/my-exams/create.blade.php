@extends('layouts.app')

@section('title', 'Tải lên & Tạo đề thi mới — TOEIC Practice')

@section('content')
<div style="max-width:720px;margin:2.5rem auto;padding:0 1.5rem;">

    <a href="{{ route('my-exams.index') }}" style="display:inline-flex;align-items:center;gap:6px;font-size:0.8125rem;color:var(--text-faint);text-decoration:none;margin-bottom:1.25rem;transition:color 0.15s;" onmouseover="this.style.color='var(--gold)'" onmouseout="this.style.color='var(--text-faint)'">
        <svg style="width:14px;height:14px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Quay lại bộ đề của tôi
    </a>

    <form method="POST" action="{{ route('my-exams.store') }}">
        @csrf

        <div class="card" style="margin-bottom:1.5rem;">
            <div class="gold-bar" style="margin:-1.5rem -1.5rem 1.5rem;border-radius:var(--r-xl) var(--r-xl) 0 0;"></div>

            <h1 style="font-size:1.375rem;font-weight:800;color:var(--text-bright);margin:0 0 0.35rem;">
                Tạo bộ đề thi mới
            </h1>
            <p style="font-size:0.8125rem;color:var(--text-faint);margin:0 0 1.5rem;">
                Sau khi tạo thông tin cơ bản, bạn có thể tải lên PDF đề thi, PDF đáp án, audio MP3 hoặc dán bảng đáp án tự động.
            </p>

            {{-- Title --}}
            <div style="margin-bottom:1.25rem;">
                <label class="label" for="title">Tên đề thi <span style="color:var(--error)">*</span></label>
                <input id="title" type="text" name="title"
                       value="{{ old('title') }}"
                       placeholder="VD: Đề thi thử tháng 10 — ETS TOEIC"
                       class="input {{ $errors->has('title') ? 'input-error' : '' }}" required>
                @error('title')
                    <p style="font-size:0.8125rem;color:var(--error);margin-top:0.375rem;">{{ $message }}</p>
                @enderror
            </div>

            {{-- Description --}}
            <div style="margin-bottom:1.25rem;">
                <label class="label" for="description">Mô tả bộ đề (Tùy chọn)</label>
                <textarea id="description" name="description" rows="3"
                          placeholder="Mô tả nguồn đề, mục tiêu điểm số..."
                          class="input textarea">{{ old('description') }}</textarea>
            </div>

            {{-- Duration & Privacy --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1.5rem;">
                <div>
                    <label class="label" for="duration_minutes">Thời gian làm bài (phút)</label>
                    <input id="duration_minutes" type="number" name="duration_minutes"
                           value="{{ old('duration_minutes', 120) }}" min="1" max="480"
                           class="input" required>
                </div>
                <div>
                    <label class="label">Chế độ hiển thị</label>
                    <label style="display:flex;align-items:center;gap:10px;padding:0.65rem 0.85rem;background:var(--bg-deep);border:1px solid var(--border);border-radius:var(--r-md);cursor:pointer;">
                        <input type="checkbox" name="is_public" value="1" {{ old('is_public') ? 'checked' : '' }} style="width:16px;height:16px;accent-color:var(--gold);">
                        <span style="font-size:0.875rem;color:var(--text-warm);font-weight:500;">Công khai cho cộng đồng</span>
                    </label>
                </div>
            </div>

            {{-- Parts Selection --}}
            <div style="margin-bottom:1.5rem;padding:1.25rem;background:var(--bg-deep);border-radius:var(--r-md);border:1px solid var(--border);">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
                    <label class="label" style="margin:0;">Chọn các phần thi trong đề</label>
                    <label style="display:flex;align-items:center;gap:6px;font-size:0.8125rem;color:var(--gold);font-weight:600;cursor:pointer;">
                        <input type="checkbox" id="select_all_parts" checked style="accent-color:var(--gold);">
                        Chọn Full Test (Part 1 - 7)
                    </label>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                    @foreach($partTitles as $num => $title)
                    <label style="display:flex;align-items:flex-start;gap:10px;padding:0.6rem 0.85rem;background:var(--bg-raised);border:1px solid var(--border);border-radius:var(--r-md);cursor:pointer;">
                        <input type="checkbox" name="parts[]" value="{{ $num }}" checked class="part-checkbox" style="width:16px;height:16px;accent-color:var(--gold);margin-top:2px;">
                        <div>
                            <div style="font-size:0.8125rem;font-weight:700;color:var(--text-bright);">Part {{ $num }}</div>
                            <div style="font-size:0.75rem;color:var(--text-faint);">{{ $title }}</div>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- Submit --}}
            <div style="display:flex;align-items:center;gap:10px;">
                <button type="submit" class="btn btn-primary btn-lg">
                    Tạo đề & Tiếp tục tải file →
                </button>
                <a href="{{ route('my-exams.index') }}" class="btn btn-ghost">
                    Hủy
                </a>
            </div>

        </div>

    </form>
</div>

@push('scripts')
<script>
    document.getElementById('select_all_parts')?.addEventListener('change', function() {
        document.querySelectorAll('.part-checkbox').forEach(cb => {
            cb.checked = this.checked;
        });
    });
</script>
@endpush

@endsection
