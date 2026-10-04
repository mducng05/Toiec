<div class="question-item" id="q-{{ $question->id }}">
    
    {{-- Header Row: Question number & Flag toggle --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;margin-bottom:1rem;">
        <div style="display:flex;align-items:center;gap:10px;">
            <div style="width:34px;height:34px;background:oklch(84% 0.19 80.46 / 0.1);border:1px solid var(--border-gold);border-radius:var(--r-md);display:flex;align-items:center;justify-content:center;color:var(--gold);font-weight:700;font-size:0.875rem;">
                {{ $question->question_number }}
            </div>
            @if($question->content)
                <div style="font-weight:600;font-size:0.9375rem;color:var(--text-bright);line-height:1.45;">
                    {{ $question->content }}
                </div>
            @endif
        </div>

        {{-- Flag toggle button --}}
        <button type="button"
                @click="toggleFlag({{ $question->id }})"
                class="btn btn-ghost btn-sm"
                :style="flags[{{ $question->id }}] ? 'color:var(--gold);background:oklch(84% 0.19 80.46 / 0.1);' : 'color:var(--text-faint);'"
                title="Đánh dấu câu hỏi để xem lại sau">
            <svg style="width:14px;height:14px" :fill="flags[{{ $question->id }}] ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/>
            </svg>
            <span style="font-size:0.75rem;" x-text="flags[{{ $question->id }}] ? 'Đã ghim' : 'Ghim'">Ghim</span>
        </button>
    </div>

    {{-- Question Image (e.g. Part 1 photo) --}}
    @if($question->image_path)
        <div style="margin:0 0 1.25rem;">
            <img src="{{ route('admin.files.serve', base64_encode($question->image_path)) }}" alt="Question illustration" style="max-height:280px;max-width:100%;border-radius:var(--r-md);border:1px solid var(--border);">
        </div>
    @endif

    {{-- Question Audio (if specific to this question) --}}
    @if($question->audioFile)
        <div style="margin-bottom:1rem;">
            <audio controls style="width:100%;height:32px;">
                <source src="{{ route('admin.files.serve', base64_encode($question->audioFile->file_path)) }}">
            </audio>
        </div>
    @endif

    {{-- Answer Options --}}
    <div style="display:flex;flex-direction:column;gap:6px;">
        @foreach($question->options as $opt)
            <div class="option-label-card"
                 :class="{ 'selected': answers[{{ $question->id }}] === {{ $opt->id }} }"
                 @click="selectOption({{ $question->id }}, {{ $opt->id }})">
                
                {{-- Radio Circle --}}
                <div style="width:20px;height:20px;border-radius:50%;border:2px solid;display:flex;align-items:center;justify-content:center;transition:all 0.12s;flex-shrink:0;"
                     :style="answers[{{ $question->id }}] === {{ $opt->id }} ? 'border-color:var(--patina);background:var(--patina);' : 'border-color:var(--border-strong);background:transparent;'">
                    <div style="width:6px;height:6px;border-radius:50%;background:var(--dark-ink);"
                         x-show="answers[{{ $question->id }}] === {{ $opt->id }}"></div>
                </div>

                {{-- Option Label & Content --}}
                <div style="display:flex;align-items:center;gap:8px;font-size:0.875rem;">
                    <span style="font-weight:700;color:var(--gold);min-width:18px;">
                        ({{ $opt->label }})
                    </span>
                    <span style="color:var(--text-bright);">
                        {{ $opt->content }}
                    </span>
                </div>
            </div>
        @endforeach
    </div>

</div>
