@extends('layouts.admin')

@section('title', 'Chỉnh sửa bài đọc — Part ' . $part->part_number)
@section('page-title', 'Chỉnh sửa bài đọc')

@section('topbar-actions')
    <a href="{{ route('admin.exams.questions.index', ['exam' => $part->exam_id, 'part_id' => $part->id]) }}" class="btn btn-ghost btn-sm">
        ← Quay lại danh sách câu hỏi
    </a>
@endsection

@section('content')
<div style="max-width:740px;">

    <form method="POST" action="{{ route('admin.passages.update', $passage) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="card" style="margin-bottom:1.5rem;">
            <div class="gold-bar" style="margin:-1.5rem -1.5rem 1.5rem;border-radius:var(--r-xl) var(--r-xl) 0 0;"></div>

            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;">
                <div>
                    <h2 style="font-size:1.125rem;font-weight:700;color:var(--text-bright);margin:0 0 0.25rem;">
                        Chỉnh sửa bài đọc / Transcript
                    </h2>
                    <p style="font-size:0.8125rem;color:var(--text-faint);margin:0;">
                        {{ $part->exam->title }} · Part {{ $part->part_number }}: {{ $part->title }}
                    </p>
                </div>
                <span class="badge badge-gold">Part {{ $part->part_number }}</span>
            </div>

            {{-- Title --}}
            <div style="margin-bottom:1.25rem;">
                <label class="label" for="title">Tiêu đề đoạn văn</label>
                <input id="title" type="text" name="title"
                       value="{{ old('title', $passage->title) }}"
                       placeholder="Questions 32-34 refer to the following conversation..."
                       class="input {{ $errors->has('title') ? 'input-error' : '' }}">
                @error('title')
                    <p style="font-size:0.8125rem;color:var(--error);margin-top:0.375rem;">{{ $message }}</p>
                @enderror
            </div>

            {{-- Audio File Selection --}}
            @if($part->isListening() && $part->audioFiles->isNotEmpty())
                <div style="margin-bottom:1.25rem;">
                    <label class="label" for="audio_file_id">Audio nghe cho đoạn này</label>
                    <select id="audio_file_id" name="audio_file_id" class="select">
                        <option value="">-- Không gán audio riêng --</option>
                        @foreach($part->audioFiles as $audio)
                            <option value="{{ $audio->id }}" {{ old('audio_file_id', $passage->audio_file_id) == $audio->id ? 'selected' : '' }}>
                                {{ $audio->file_name }} ({{ number_format($audio->file_size / 1048576, 2) }} MB)
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            {{-- Content --}}
            <div style="margin-bottom:1.25rem;">
                <label class="label" for="content">Nội dung đoạn văn / Transcript hội thoại</label>
                <textarea id="content" name="content" rows="8"
                          placeholder="Dán nội dung đoạn văn, email, thư từ hoặc lời thoại transcript tại đây..."
                          class="input textarea {{ $errors->has('content') ? 'input-error' : '' }}">{{ old('content', $passage->content) }}</textarea>
                @error('content')
                    <p style="font-size:0.8125rem;color:var(--error);margin-top:0.375rem;">{{ $message }}</p>
                @enderror
            </div>

            {{-- Image Preview & Upload --}}
            <div style="margin-bottom:1.5rem;">
                <label class="label" for="image">Hình ảnh bài đọc</label>
                @if($passage->image_path)
                    <div style="margin-bottom:0.75rem;display:flex;align-items:center;gap:12px;background:var(--bg-deep);padding:0.75rem;border-radius:var(--r-md);border:1px solid var(--border);">
                        <img src="{{ route('admin.files.serve', base64_encode($passage->image_path)) }}" alt="Current passage image" style="height:80px;border-radius:var(--r-xs);border:1px solid var(--border);">
                        <label style="display:flex;align-items:center;gap:6px;font-size:0.8125rem;color:var(--error);cursor:pointer;">
                            <input type="checkbox" name="remove_image" value="1" style="accent-color:var(--error);">
                            Xóa ảnh hiện tại
                        </label>
                    </div>
                @endif
                <input id="image" type="file" name="image" accept="image/*" class="input" style="padding:0.4rem 0.6rem;font-size:0.8125rem;">
            </div>

            {{-- Submit --}}
            <div style="display:flex;align-items:center;gap:0.75rem;">
                <button type="submit" class="btn btn-primary">
                    <svg style="width:15px;height:15px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Cập nhật bài đọc
                </button>
                <a href="{{ route('admin.exams.questions.index', ['exam' => $part->exam_id, 'part_id' => $part->id]) }}" class="btn btn-ghost">
                    Hủy bỏ
                </a>
            </div>

        </div>

    </form>
</div>
@endsection
