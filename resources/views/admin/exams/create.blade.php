@extends('layouts.admin')

@section('title', 'Tạo đề thi mới')
@section('page-title', 'Tạo đề thi mới')

@section('topbar-actions')
    <a href="{{ route('admin.exams.index') }}" class="btn btn-ghost btn-sm">
        ← Danh sách đề thi
    </a>
@endsection

@section('content')
<div style="max-width:680px;">

    <form id="create-exam-form" method="POST" action="{{ route('admin.exams.store') }}">
        @csrf

        {{-- Thông tin cơ bản --}}
        <div class="card" style="margin-bottom:1.25rem;">
            <div class="gold-bar" style="margin:-1.5rem -1.5rem 1.5rem;border-radius:var(--r-xl) var(--r-xl) 0 0;"></div>

            <h2 style="font-size:0.9375rem;font-weight:600;color:var(--text-bright);margin:0 0 1.25rem;">
                Thông tin đề thi
            </h2>

            {{-- Title --}}
            <div style="margin-bottom:1rem;">
                <label class="label" for="title">Tên đề thi <span style="color:var(--error)">*</span></label>
                <input id="title" type="text" name="title"
                       value="{{ old('title') }}"
                       placeholder="VD: TOEIC Test 2024 — Practice Set 1"
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
                          rows="3"
                          placeholder="Mô tả ngắn về bộ đề thi này..."
                          class="input textarea {{ $errors->has('description') ? 'input-error' : '' }}">{{ old('description') }}</textarea>
                @error('description')
                    <p style="font-size:0.8125rem;color:var(--error);margin-top:0.375rem;">{{ $message }}</p>
                @enderror
            </div>

            {{-- Duration + type row --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                <div>
                    <label class="label" for="duration_minutes">Thời gian làm bài (phút)</label>
                    <input id="duration_minutes" type="number" name="duration_minutes"
                           value="{{ old('duration_minutes', 120) }}"
                           min="1" max="480"
                           class="input {{ $errors->has('duration_minutes') ? 'input-error' : '' }}">
                    @error('duration_minutes')
                        <p style="font-size:0.8125rem;color:var(--error);margin-top:0.375rem;">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="label" style="margin-bottom:0.85rem;">Loại đề</label>
                    <label style="display:flex;align-items:center;gap:10px;cursor:pointer;padding:0.7rem 0.875rem;background:var(--bg-deep);border:1px solid var(--border);border-radius:var(--r-sm);transition:all 0.12s;"
                           id="full-test-label">
                        <input type="hidden" name="is_full_test" value="0">
                        <input type="checkbox" id="is_full_test" name="is_full_test" value="1"
                               {{ old('is_full_test') ? 'checked' : '' }}
                               style="width:16px;height:16px;accent-color:var(--gold);cursor:pointer;"
                               onchange="document.getElementById('full-test-label').style.borderColor = this.checked ? 'var(--border-gold)' : 'var(--border)'">
                        <span style="font-size:0.875rem;color:var(--text-warm);">Full Test (7 Parts)</span>
                    </label>
                </div>
            </div>
        </div>

        {{-- Parts --}}
        <div class="card" style="margin-bottom:1.25rem;">
            <h2 style="font-size:0.9375rem;font-weight:600;color:var(--text-bright);margin:0 0 0.375rem;">
                Chọn Parts
            </h2>
            <p style="font-size:0.8125rem;color:var(--text-faint);margin:0 0 1.25rem;">
                Chọn các phần thi muốn đưa vào đề. Có thể thêm sau.
            </p>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.625rem;">
                @foreach($partTitles as $num => $title)
                <label style="display:flex;align-items:flex-start;gap:10px;cursor:pointer;padding:0.75rem 0.875rem;
                              background:var(--bg-deep);border:1px solid var(--border);border-radius:var(--r-md);
                              transition:all 0.12s;" class="part-label" data-part="{{ $num }}">
                    <input type="checkbox" name="parts[]" value="{{ $num }}"
                           {{ in_array($num, old('parts', [])) ? 'checked' : '' }}
                           style="width:15px;height:15px;accent-color:var(--gold);cursor:pointer;margin-top:2px;flex-shrink:0;"
                           class="part-checkbox">
                    <div>
                        <div style="font-size:0.8125rem;font-weight:600;color:var(--text-bright);">
                            Part {{ $num }}
                        </div>
                        <div style="font-size:0.75rem;color:var(--text-faint);">{{ $title }}</div>
                        <div style="font-size:0.6875rem;color:{{ $num <= 4 ? 'var(--gold)' : 'var(--patina)' }};margin-top:2px;letter-spacing:0.04em;text-transform:uppercase;">
                            {{ $num <= 4 ? 'Listening' : 'Reading' }}
                        </div>
                    </div>
                </label>
                @endforeach
            </div>
            @error('parts')
                <p style="font-size:0.8125rem;color:var(--error);margin-top:0.75rem;">{{ $message }}</p>
            @enderror
        </div>

        {{-- Submit --}}
        <div style="display:flex;align-items:center;gap:0.75rem;">
            <button type="submit" id="submit-btn" class="btn btn-primary">
                <svg style="width:15px;height:15px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tạo đề thi
            </button>
            <a href="{{ route('admin.exams.index') }}" class="btn btn-ghost">Hủy</a>
        </div>

    </form>
</div>

@push('scripts')
<script>
// Highlight selected parts
document.querySelectorAll('.part-checkbox').forEach(cb => {
    const label = cb.closest('.part-label');
    const update = () => {
        label.style.borderColor = cb.checked ? 'var(--border-gold)' : 'var(--border)';
        label.style.background  = cb.checked ? 'oklch(84% 0.19 80.46 / 0.04)' : 'var(--bg-deep)';
    };
    update();
    cb.addEventListener('change', update);
});

// Auto-select all parts when Full Test checked
document.getElementById('is_full_test').addEventListener('change', function() {
    if (this.checked) {
        document.querySelectorAll('.part-checkbox').forEach(cb => {
            cb.checked = true;
            cb.dispatchEvent(new Event('change'));
        });
    }
});
</script>
@endpush
@endsection
