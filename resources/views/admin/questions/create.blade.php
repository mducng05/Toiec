@extends('layouts.admin')

@section('title', 'Thêm câu hỏi mới — Part ' . $part->part_number)
@section('page-title', 'Thêm câu hỏi mới')

@section('topbar-actions')
    <a href="{{ route('admin.exams.questions.index', ['exam' => $part->exam_id, 'part_id' => $part->id]) }}" class="btn btn-ghost btn-sm">
        ← Quay lại danh sách câu hỏi
    </a>
@endsection

@section('content')
<div style="max-width:740px;">

    <form method="POST" action="{{ route('admin.parts.questions.store', $part) }}" enctype="multipart/form-data">
        @csrf

        <div class="card" style="margin-bottom:1.5rem;">
            <div class="gold-bar" style="margin:-1.5rem -1.5rem 1.5rem;border-radius:var(--r-xl) var(--r-xl) 0 0;"></div>

            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;flex-wrap:wrap;gap:8px;">
                <div>
                    <h2 style="font-size:1.125rem;font-weight:700;color:var(--text-bright);margin:0 0 0.25rem;">
                        Tạo câu hỏi mới
                    </h2>
                    <p style="font-size:0.8125rem;color:var(--text-faint);margin:0;">
                        {{ $part->exam->title }} · Part {{ $part->part_number }}: {{ $part->title }}
                    </p>
                </div>
                <span class="badge {{ $part->section === 'listening' ? 'badge-patina' : 'badge-gold' }}">
                    {{ $part->section === 'listening' ? 'Listening Part' : 'Reading Part' }}
                </span>
            </div>

            {{-- Question Number & Grouping --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1.25rem;">
                <div>
                    <label class="label" for="question_number">Số thứ tự câu hỏi <span style="color:var(--error)">*</span></label>
                    <input id="question_number" type="number" name="question_number"
                           value="{{ old('question_number', $nextNumber) }}"
                           min="1" max="200"
                           class="input {{ $errors->has('question_number') ? 'input-error' : '' }}" required>
                    @error('question_number')
                        <p style="font-size:0.8125rem;color:var(--error);margin-top:0.375rem;">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Passage selection if Part has passages --}}
                @if($part->passages->isNotEmpty())
                    <div>
                        <label class="label" for="passage_id">Thuộc đoạn văn / Bài đọc</label>
                        <select id="passage_id" name="passage_id" class="select">
                            <option value="">-- Câu hỏi độc lập --</option>
                            @foreach($part->passages as $idx => $passage)
                                <option value="{{ $passage->id }}" {{ old('passage_id', request('passage_id')) == $passage->id ? 'selected' : '' }}>
                                    #{{ $idx + 1 }}: {{ Str::limit($passage->title ?: $passage->content, 40) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @elseif($part->audioFiles->isNotEmpty())
                    <div>
                        <label class="label" for="audio_file_id">Audio câu hỏi (Tùy chọn)</label>
                        <select id="audio_file_id" name="audio_file_id" class="select">
                            <option value="">-- Sử dụng audio chung của Part --</option>
                            @foreach($part->audioFiles as $audio)
                                <option value="{{ $audio->id }}" {{ old('audio_file_id') == $audio->id ? 'selected' : '' }}>
                                    {{ $audio->file_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif
            </div>

            {{-- Question Content / Prompt --}}
            <div style="margin-bottom:1.25rem;">
                <label class="label" for="content">Nội dung câu hỏi</label>
                <textarea id="content" name="content" rows="3"
                          placeholder="Nhập nội dung câu hỏi (để trống nếu câu hỏi chỉ nghe hoặc xem hình)..."
                          class="input textarea {{ $errors->has('content') ? 'input-error' : '' }}">{{ old('content') }}</textarea>
                @error('content')
                    <p style="font-size:0.8125rem;color:var(--error);margin-top:0.375rem;">{{ $message }}</p>
                @enderror
            </div>

            {{-- Question Image (Part 1 or illustration) --}}
            <div style="margin-bottom:1.5rem;">
                <label class="label" for="image">Hình ảnh câu hỏi (Thường dùng cho Part 1 hoặc biểu đồ)</label>
                <input id="image" type="file" name="image" accept="image/*" class="input" style="padding:0.4rem 0.6rem;font-size:0.8125rem;">
                @error('image')
                    <p style="font-size:0.8125rem;color:var(--error);margin-top:0.375rem;">{{ $message }}</p>
                @enderror
            </div>

            {{-- Answer Options & Correct Selection --}}
            <div style="margin-bottom:1.5rem;padding:1.25rem;background:var(--bg-deep);border:1px solid var(--border);border-radius:var(--r-md);">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
                    <label class="label" style="margin:0;">
                        Các lựa chọn đáp án & Đáp án đúng <span style="color:var(--error)">*</span>
                    </label>
                    <span style="font-size:0.75rem;color:var(--text-faint);">
                        Chọn radio button ở đáp án đúng
                    </span>
                </div>

                @php
                    $labels = ($part->part_number === 2) ? ['A', 'B', 'C'] : ['A', 'B', 'C', 'D'];
                    $selectedCorrect = old('correct_option', 'A');
                @endphp

                <div style="display:flex;flex-direction:column;gap:0.75rem;">
                    @foreach($labels as $idx => $label)
                    <div style="display:flex;align-items:center;gap:12px;background:var(--bg-raised);padding:0.5rem 0.75rem;border-radius:var(--r-md);border:1px solid var(--border);">
                        
                        {{-- Radio Correct --}}
                        <label style="display:flex;align-items:center;gap:6px;cursor:pointer;min-width:60px;">
                            <input type="radio" name="correct_option" value="{{ $label }}"
                                   {{ $selectedCorrect === $label ? 'checked' : '' }}
                                   style="width:16px;height:16px;accent-color:var(--patina);cursor:pointer;" required>
                            <span style="font-weight:700;font-size:0.9375rem;color:var(--gold);">
                                {{ $label }}.
                            </span>
                        </label>

                        <input type="hidden" name="options[{{ $idx }}][label]" value="{{ $label }}">

                        {{-- Option text input --}}
                        <input type="text" name="options[{{ $idx }}][content]"
                               value="{{ old("options.{$idx}.content") }}"
                               placeholder="Nội dung đáp án {{ $label }} (để trống nếu thí sinh chỉ nghe âm thanh)..."
                               class="input" style="flex:1;background:var(--bg-deep);font-size:0.875rem;">
                    </div>
                    @endforeach
                </div>
                @error('correct_option')
                    <p style="font-size:0.8125rem;color:var(--error);margin-top:0.5rem;">{{ $message }}</p>
                @enderror
            </div>

            {{-- Explanation --}}
            <div style="margin-bottom:1.5rem;">
                <label class="label" for="explanation">Lời giải thích chi tiết & Mẹo làm bài</label>
                <textarea id="explanation" name="explanation" rows="3"
                          placeholder="Dịch nghĩa câu, giải thích ngữ pháp hoặc từ vựng tại sao chọn đáp án này..."
                          class="input textarea">{{ old('explanation') }}</textarea>
            </div>

            {{-- Actions --}}
            <div style="display:flex;align-items:center;gap:0.75rem;">
                <button type="submit" class="btn btn-primary">
                    <svg style="width:15px;height:15px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Lưu câu hỏi
                </button>
                <a href="{{ route('admin.exams.questions.index', ['exam' => $part->exam_id, 'part_id' => $part->id]) }}" class="btn btn-ghost">
                    Hủy bỏ
                </a>
            </div>

        </div>

    </form>
</div>
@endsection
