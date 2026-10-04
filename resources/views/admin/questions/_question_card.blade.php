<div class="card" style="padding:1.25rem 1.5rem;background:var(--bg-deep);border:1px solid var(--border);border-radius:var(--r-lg);transition:border-color 0.15s;" onmouseover="this.style.borderColor='var(--border-gold)'" onmouseout="this.style.borderColor='var(--border)'">
    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;margin-bottom:0.875rem;">
        
        {{-- Question Title & Prompt --}}
        <div style="display:flex;align-items:flex-start;gap:12px;flex:1;">
            <div style="width:34px;height:34px;background:oklch(84% 0.19 80.46 / 0.1);border:1px solid var(--border-gold);border-radius:var(--r-md);display:flex;align-items:center;justify-content:center;color:var(--gold);font-weight:700;font-size:0.875rem;flex-shrink:0;">
                #{{ $question->question_number }}
            </div>
            <div style="flex:1;">
                <div style="font-size:0.9375rem;font-weight:600;color:var(--text-bright);line-height:1.45;margin-bottom:0.25rem;">
                    {{ $question->content ?: "(Không có văn bản câu hỏi — Thí sinh nghe audio / xem tranh)" }}
                </div>
                @if($question->audioFile)
                    <div style="display:inline-flex;align-items:center;gap:6px;font-size:0.75rem;color:var(--patina);margin-top:2px;">
                        <svg style="width:12px;height:12px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/>
                        </svg>
                        Audio riêng: {{ $question->audioFile->file_name }}
                    </div>
                @endif
            </div>
        </div>

        {{-- Action Buttons --}}
        <div style="display:flex;align-items:center;gap:6px;flex-shrink:0;">
            <a href="{{ route('admin.questions.edit', $question) }}" class="btn btn-ghost btn-sm btn-icon" title="Sửa câu hỏi">
                <svg style="width:14px;height:14px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </a>
            <form method="POST" action="{{ route('admin.questions.destroy', $question) }}" onsubmit="return confirm('Xóa câu hỏi #{{ $question->question_number }}?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm btn-icon" title="Xóa">
                    <svg style="width:14px;height:14px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>

    {{-- Question Image if present --}}
    @if($question->image_path)
        <div style="margin:0.75rem 0 1rem 46px;">
            <img src="{{ route('admin.files.serve', base64_encode($question->image_path)) }}" alt="Question illustration" style="max-height:220px;border-radius:var(--r-md);border:1px solid var(--border);">
        </div>
    @endif

    {{-- Options with 1-click Quick Answer Switcher --}}
    <div class="options-container" style="margin-left:46px;display:grid;grid-template-columns:repeat(auto-fit, minmax(220px, 1fr));gap:8px;">
        @foreach($question->options as $opt)
            @php
                $isCorrect = $opt->is_correct;
            @endphp
            <button type="button"
                    class="btn-quick-answer"
                    data-question-id="{{ $question->id }}"
                    data-label="{{ $opt->label }}"
                    title="Click để chọn đáp án đúng tức thì"
                    style="display:flex;align-items:center;gap:10px;padding:0.6rem 0.85rem;border-radius:var(--r-md);text-align:left;border:1px solid {{ $isCorrect ? 'var(--patina)' : 'var(--border)' }};background:{{ $isCorrect ? 'oklch(70% 0.12 188 / 0.2)' : 'var(--bg-raised)' }};color:{{ $isCorrect ? 'var(--patina)' : 'var(--text-muted)' }};cursor:pointer;font-family:inherit;font-size:0.8125rem;transition:all 0.12s;">
                <span style="font-weight:700;font-size:0.875rem;min-width:18px;">
                    {{ $opt->label }}.
                </span>
                <span style="flex:1;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                    {{ $opt->content ?: "(Tùy chọn trống)" }}
                </span>
                <span class="check-indicator" style="display:{{ $isCorrect ? 'inline' : 'none' }};font-size:0.75rem;font-weight:700;">
                    ✓
                </span>
            </button>
        @endforeach
    </div>

    {{-- Explanation if available --}}
    @if($question->explanation)
        <div style="margin:0.875rem 0 0 46px;padding:0.6rem 0.85rem;background:var(--bg-raised);border-left:2px solid var(--gold);border-radius:0 var(--r-sm) var(--r-sm) 0;font-size:0.8125rem;color:var(--text-muted);line-height:1.5;">
            <strong style="color:var(--gold);">Giải thích:</strong> {{ $question->explanation }}
        </div>
    @endif
</div>
