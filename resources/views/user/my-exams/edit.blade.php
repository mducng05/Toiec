@extends('layouts.app')

@section('title', 'Chỉnh sửa bộ đề: ' . $exam->title . ' — TOEIC Practice')

@section('content')
<div style="max-width:720px;margin:2.5rem auto;padding:0 1.5rem;">

    <a href="{{ route('my-exams.show', $exam) }}" style="display:inline-flex;align-items:center;gap:6px;font-size:0.875rem;color:var(--text-muted);text-decoration:none;margin-bottom:1.25rem;transition:color 0.15s;" onmouseover="this.style.color='var(--gold)'" onmouseout="this.style.color='var(--text-muted)'">
        <svg style="width:16px;height:16px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Quay lại chi tiết đề thi
    </a>

    <form method="POST" action="{{ route('my-exams.update', $exam) }}">
        @csrf
        @method('PUT')

        <div class="card" style="margin-bottom:1.5rem;">
            <div class="gold-bar" style="margin:-1.5rem -1.5rem 1.5rem;border-radius:var(--r-xl) var(--r-xl) 0 0;"></div>

            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.35rem;">
                <h1 style="font-size:1.375rem;font-weight:800;color:var(--text-bright);margin:0;">
                    Chỉnh sửa thông tin đề thi
                </h1>
                <span class="badge {{ $exam->is_active ? 'badge-green' : 'badge-gold' }}">
                    {{ $exam->is_active ? 'Đang kích hoạt' : 'Bản nháp' }}
                </span>
            </div>
            <p style="font-size:0.875rem;color:var(--text-muted);margin:0 0 1.5rem;">
                Cập nhật tiêu đề, thời lượng và quyền riêng tư cho đề thi của bạn.
            </p>

            {{-- Title --}}
            <div style="margin-bottom:1.25rem;">
                <label class="label" for="title">Tên đề thi <span style="color:var(--error)">*</span></label>
                <input id="title" type="text" name="title"
                       value="{{ old('title', $exam->title) }}"
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
                          class="input textarea">{{ old('description', $exam->description) }}</textarea>
            </div>

            {{-- Duration & Privacy & Active --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1.5rem;">
                <div>
                    <label class="label" for="duration_minutes">Thời gian làm bài (phút)</label>
                    <input id="duration_minutes" type="number" name="duration_minutes"
                           value="{{ old('duration_minutes', $exam->duration_minutes) }}" min="1" max="480"
                           class="input" required>
                </div>
                <div>
                    <label class="label">Chế độ hiển thị</label>
                    <label style="display:flex;align-items:center;gap:10px;padding:0.65rem 0.85rem;background:var(--bg-deep);border:1px solid var(--border);border-radius:var(--r-md);cursor:pointer;">
                        <input type="checkbox" name="is_public" value="1" {{ old('is_public', $exam->is_public) ? 'checked' : '' }} style="width:16px;height:16px;accent-color:var(--gold);">
                        <span style="font-size:0.875rem;color:var(--text-warm);font-weight:500;">Công khai cho cộng đồng</span>
                    </label>
                </div>
            </div>

            <div style="margin-bottom:1.5rem;">
                <label class="label">Trạng thái phát hành</label>
                <label style="display:flex;align-items:center;gap:10px;padding:0.65rem 0.85rem;background:var(--bg-deep);border:1px solid var(--border);border-radius:var(--r-md);cursor:pointer;">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $exam->is_active) ? 'checked' : '' }} style="width:16px;height:16px;accent-color:var(--gold);">
                    <span style="font-size:0.875rem;color:var(--text-warm);font-weight:500;">Kích hoạt sẵn sàng làm bài</span>
                </label>
            </div>

            {{-- Actions --}}
            <div style="display:flex;align-items:center;justify-content:space-between;padding-top:1rem;border-top:1px solid var(--border);">
                <div style="display:flex;align-items:center;gap:10px;">
                    <button type="submit" class="btn btn-primary">
                        Lưu thay đổi
                    </button>
                    <a href="{{ route('my-exams.show', $exam) }}" class="btn btn-ghost">
                        Hủy
                    </a>
                </div>

                <a href="{{ route('my-exams.uploads', $exam) }}" class="btn btn-secondary btn-sm" style="display:inline-flex;align-items:center;gap:6px;">
                    📁 Quản lý File PDF & Audio →
                </a>
            </div>

        </div>

    </form>
</div>
@endsection
