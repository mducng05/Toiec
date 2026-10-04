@extends('layouts.admin')

@section('title', 'Chỉnh sửa: ' . $exam->title)
@section('page-title', 'Chỉnh sửa đề thi')

@section('topbar-actions')
    <a href="{{ route('admin.exams.show', $exam) }}" class="btn btn-ghost btn-sm">
        ← Chi tiết đề thi
    </a>
@endsection

@section('content')
<div style="max-width:680px;">

    <form method="POST" action="{{ route('admin.exams.update', $exam) }}">
        @csrf
        @method('PUT')

        {{-- Thông tin cơ bản --}}
        <div class="card" style="margin-bottom:1.25rem;">
            <div class="gold-bar" style="margin:-1.5rem -1.5rem 1.5rem;border-radius:var(--r-xl) var(--r-xl) 0 0;"></div>

            <h2 style="font-size:0.9375rem;font-weight:600;color:var(--text-bright);margin:0 0 1.25rem;">
                Chỉnh sửa thông tin đề thi
            </h2>

            {{-- Title --}}
            <div style="margin-bottom:1rem;">
                <label class="label" for="title">Tên đề thi <span style="color:var(--error)">*</span></label>
                <input id="title" type="text" name="title"
                       value="{{ old('title', $exam->title) }}"
                       class="input {{ $errors->has('title') ? 'input-error' : '' }}"
                       autocomplete="off" required>
                @error('title')
                    <p style="font-size:0.8125rem;color:var(--error);margin-top:0.375rem;">{{ $message }}</p>
                @enderror
            </div>

            {{-- Description --}}
            <div style="margin-bottom:1rem;">
                <label class="label" for="description">Mô tả</label>
                <textarea id="description" name="description"
                          rows="4"
                          placeholder="Mô tả ngắn về bộ đề thi này..."
                          class="input textarea {{ $errors->has('description') ? 'input-error' : '' }}">{{ old('description', $exam->description) }}</textarea>
                @error('description')
                    <p style="font-size:0.8125rem;color:var(--error);margin-top:0.375rem;">{{ $message }}</p>
                @enderror
            </div>

            {{-- Duration + type row --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                <div>
                    <label class="label" for="duration_minutes">Thời gian làm bài (phút) <span style="color:var(--error)">*</span></label>
                    <input id="duration_minutes" type="number" name="duration_minutes"
                           value="{{ old('duration_minutes', $exam->duration_minutes) }}"
                           min="1" max="480"
                           class="input {{ $errors->has('duration_minutes') ? 'input-error' : '' }}" required>
                    @error('duration_minutes')
                        <p style="font-size:0.8125rem;color:var(--error);margin-top:0.375rem;">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="label" style="margin-bottom:0.85rem;">Loại đề</label>
                    <label style="display:flex;align-items:center;gap:10px;cursor:pointer;padding:0.7rem 0.875rem;background:var(--bg-deep);border:1px solid {{ old('is_full_test', $exam->is_full_test) ? 'var(--border-gold)' : 'var(--border)' }};border-radius:var(--r-sm);transition:all 0.12s;"
                           id="full-test-label">
                        <input type="hidden" name="is_full_test" value="0">
                        <input type="checkbox" id="is_full_test" name="is_full_test" value="1"
                               {{ old('is_full_test', $exam->is_full_test) ? 'checked' : '' }}
                               style="width:16px;height:16px;accent-color:var(--gold);cursor:pointer;"
                               onchange="document.getElementById('full-test-label').style.borderColor = this.checked ? 'var(--border-gold)' : 'var(--border)'">
                        <span style="font-size:0.875rem;color:var(--text-warm);">Full Test (200 câu)</span>
                    </label>
                </div>
            </div>
        </div>

        {{-- Submit --}}
        <div style="display:flex;align-items:center;gap:0.75rem;">
            <button type="submit" class="btn btn-primary">
                <svg style="width:15px;height:15px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Lưu thay đổi
            </button>
            <a href="{{ route('admin.exams.show', $exam) }}" class="btn btn-ghost">Hủy</a>
        </div>

    </form>
</div>
@endsection
