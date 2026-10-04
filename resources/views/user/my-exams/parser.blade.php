@extends('layouts.app')

@section('title', 'Bóc tách & Tự động hóa đề: ' . $exam->title . ' — TOEIC Practice')

@section('content')
<div style="max-width:1150px;margin:2.5rem auto;padding:0 1.5rem;" x-data="userParserStudio()">

    {{-- Header & Breadcrumb --}}
    <div style="margin-bottom:1.5rem;">
        <a href="{{ route('my-exams.show', $exam) }}" style="display:inline-flex;align-items:center;gap:6px;font-size:0.875rem;color:var(--text-muted);text-decoration:none;margin-bottom:0.75rem;transition:color 0.15s;" onmouseover="this.style.color='var(--gold)'" onmouseout="this.style.color='var(--text-muted)'">
            <svg style="width:16px;height:16px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Quay lại đề thi: {{ $exam->title }}
        </a>
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
            <div>
                <h1 style="font-size:1.625rem;font-weight:800;color:var(--text-bright);margin:0 0 0.35rem;letter-spacing:-0.02em;">
                    Công cụ bóc tách & Nạp đề tự động
                </h1>
                <p style="font-size:0.875rem;color:var(--text-muted);margin:0;">
                    Dán nhanh bảng đáp án 200 câu, bóc tách danh sách câu hỏi hoặc soi file PDF để hoàn thiện đề thi riêng của bạn.
                </p>
            </div>
            <div style="display:flex;gap:8px;">
                <a href="{{ route('my-exams.uploads', $exam) }}" class="btn btn-secondary btn-sm">
                    📁 Tải PDF & Audio
                </a>
                <a href="{{ route('my-exams.show', $exam) }}" class="btn btn-ghost btn-sm">
                    Xem tổng quan
                </a>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div style="display:flex;gap:8px;border-bottom:1px solid var(--border);margin-bottom:2rem;background:#ffffff;padding:0.5rem 1rem;border-radius:var(--r-xl);box-shadow:var(--shadow-sm);">
        <button type="button" @click="activeTab = 'answers'"
                style="padding:0.65rem 1.25rem;font-size:0.875rem;font-weight:600;background:none;border:none;border-radius:var(--r-lg);cursor:pointer;display:flex;align-items:center;gap:8px;transition:all 0.15s;"
                :style="activeTab === 'answers' ? 'color:var(--gold);background:oklch(55% 0.22 265 / 0.08);' : 'color:var(--text-muted);'">
            <svg style="width:16px;height:16px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
            </svg>
            Nạp bảng đáp án nhanh
        </button>

        <button type="button" @click="activeTab = 'questions'"
                style="padding:0.65rem 1.25rem;font-size:0.875rem;font-weight:600;background:none;border:none;border-radius:var(--r-lg);cursor:pointer;display:flex;align-items:center;gap:8px;transition:all 0.15s;"
                :style="activeTab === 'questions' ? 'color:var(--gold);background:oklch(55% 0.22 265 / 0.08);' : 'color:var(--text-muted);'">
            <svg style="width:16px;height:16px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
            Bóc tách văn bản câu hỏi
        </button>

        <button type="button" @click="activeTab = 'pdf'"
                style="padding:0.65rem 1.25rem;font-size:0.875rem;font-weight:600;background:none;border:none;border-radius:var(--r-lg);cursor:pointer;display:flex;align-items:center;gap:8px;transition:all 0.15s;"
                :style="activeTab === 'pdf' ? 'color:var(--gold);background:oklch(55% 0.22 265 / 0.08);' : 'color:var(--text-muted);'">
            <svg style="width:16px;height:16px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0121 9.586V19a2 2 0 01-2 2z"/>
            </svg>
            Soi file PDF song song
        </button>
    </div>

    {{-- TAB 1: BULK ANSWER KEYS --}}
    <div x-show="activeTab === 'answers'">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:2rem;align-items:start;">

            <div class="card">
                <h3 style="font-size:1.0625rem;font-weight:700;color:var(--text-bright);margin:0 0 0.35rem;">
                    Dán chuỗi bảng đáp án
                </h3>
                <p style="font-size:0.8125rem;color:var(--text-muted);margin:0 0 1.25rem;line-height:1.5;">
                    Hỗ trợ tất cả định dạng phổ biến: <code>1A 2B 3C</code>, <code>1. A  2. B</code>, <code>1-A, 2-B</code>, hoặc từng dòng <code>1: A</code>.
                </p>

                <form method="POST" action="{{ route('my-exams.parser.answers', $exam) }}">
                    @csrf
                    <input type="hidden" name="apply" value="1">

                    <div style="margin-bottom:1rem;">
                        <textarea name="raw_text" x-model="rawAnswerText" rows="11"
                                  placeholder="Ví dụ dán chuỗi:&#10;1. C&#10;2. A&#10;3. B&#10;4. D&#10;5. A&#10;hoặc: 1C 2A 3B 4D 5A 6B..."
                                  class="input textarea" style="font-family:monospace;font-size:0.875rem;" required></textarea>
                    </div>

                    <div style="margin-bottom:1.25rem;">
                        <label style="display:flex;align-items:center;gap:8px;font-size:0.8125rem;color:var(--text-warm);cursor:pointer;">
                            <input type="checkbox" name="create_missing" value="1" checked style="accent-color:var(--gold);width:16px;height:16px;">
                            <span>Tự động tạo câu hỏi nếu câu chưa có trong đề</span>
                        </label>
                    </div>

                    <div style="display:flex;align-items:center;gap:10px;">
                        <button type="button" @click="previewAnswers()" class="btn btn-secondary">
                            Kiểm tra trước (Preview)
                        </button>
                        <button type="submit" class="btn btn-primary">
                            Áp dụng vào đề thi
                        </button>
                    </div>
                </form>
            </div>

            {{-- Preview Box --}}
            <div class="card" style="min-height:360px;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
                    <h3 style="font-size:1.0625rem;font-weight:700;color:var(--text-bright);margin:0;">
                        Kết quả nhận diện: <span style="color:var(--gold);" x-text="previewAnswerCount">0</span> câu
                    </h3>
                </div>

                <div x-show="previewAnswerCount === 0" style="text-align:center;padding:4rem 1rem;color:var(--text-muted);font-size:0.875rem;">
                    Dán văn bản đáp án và bấm <strong>"Kiểm tra trước"</strong> để xem danh sách đáp án nhận diện.
                </div>

                <div x-show="previewAnswerCount > 0" style="display:grid;grid-template-columns:repeat(auto-fill, minmax(65px, 1fr));gap:6px;max-height:480px;overflow-y:auto;padding-right:4px;">
                    <template x-for="(ans, qNum) in previewAnswerMap" :key="qNum">
                        <div style="padding:0.4rem;background:var(--bg-deep);border:1px solid var(--border);border-radius:var(--r-md);text-align:center;">
                            <span style="font-size:0.75rem;color:var(--text-muted);" x-text="'#' + qNum"></span>
                            <div style="font-size:1.05rem;font-weight:800;color:var(--patina);" x-text="ans"></div>
                        </div>
                    </template>
                </div>
            </div>

        </div>
    </div>

    {{-- TAB 2: QUESTION TEXT PARSER --}}
    <div x-show="activeTab === 'questions'" style="display:none;">
        <div class="card" style="margin-bottom:1.5rem;">
            <h3 style="font-size:1.0625rem;font-weight:700;color:var(--text-bright);margin:0 0 0.35rem;">
                Bóc tách câu hỏi & 4 lựa chọn từ văn bản
            </h3>
            <p style="font-size:0.8125rem;color:var(--text-muted);margin:0 0 1.25rem;">
                Hệ thống tự động tách số câu, lời dẫn, các lựa chọn (A), (B), (C), (D), đáp án đúng và lời giải thích.
            </p>

            <form method="POST" action="{{ route('my-exams.parser.questions', $exam) }}">
                @csrf
                <input type="hidden" name="apply" value="1">

                <div style="margin-bottom:1rem;max-width:400px;">
                    <label class="label">Phần thi cần nhập vào <span style="color:var(--error)">*</span></label>
                    <select name="exam_part_id" class="input select" required>
                        @foreach($exam->parts as $part)
                            <option value="{{ $part->id }}">
                                Part {{ $part->part_number }}: {{ $part->title }} ({{ $part->questions_count }} câu hiện có)
                            </option>
                        @endforeach
                    </select>
                </div>

                <div style="margin-bottom:1.25rem;">
                    <label class="label">Dán văn bản khối câu hỏi</label>
                    <textarea name="raw_text" x-model="rawQuestionText" rows="12"
                              placeholder="Ví dụ dán:&#10;101. Customer satisfaction is our highest _______.&#10;(A) prioritize&#10;(B) priority&#10;(C) prior&#10;(D) priorly&#10;Answer: B&#10;Explanation: Cần danh từ sau tính từ sở hữu our highest."
                              class="input textarea" style="font-family:monospace;font-size:0.875rem;" required></textarea>
                </div>

                <div style="display:flex;align-items:center;gap:10px;">
                    <button type="button" @click="previewQuestions()" class="btn btn-secondary">
                        Kiểm tra cấu trúc trước
                    </button>
                    <button type="submit" class="btn btn-primary">
                        Bóc tách & Nhập vào đề
                    </button>
                </div>
            </form>
        </div>

        {{-- Questions Preview Result --}}
        <div class="card" x-show="previewQuestionsList.length > 0" style="display:none;">
            <h3 style="font-size:1.0625rem;font-weight:700;color:var(--text-bright);margin:0 0 1rem;">
                Nhận diện được <span style="color:var(--gold);" x-text="previewQuestionsList.length"></span> câu hỏi
            </h3>
            <div style="display:flex;flex-direction:column;gap:1rem;">
                <template x-for="q in previewQuestionsList" :key="q.question_number">
                    <div style="padding:1rem;background:var(--bg-deep);border-radius:var(--r-md);border:1px solid var(--border);">
                        <div style="display:flex;align-items:center;gap:8px;margin-bottom:0.5rem;">
                            <span class="badge badge-gold" x-text="'Câu ' + q.question_number"></span>
                            <span style="font-weight:600;color:var(--text-bright);" x-text="q.prompt"></span>
                        </div>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px;margin:0.75rem 0;">
                            <div style="font-size:0.8125rem;" :style="q.correct_answer === 'A' ? 'color:var(--patina);font-weight:700;' : 'color:var(--text-muted);'" x-text="'(A) ' + (q.options?.A || '...')"></div>
                            <div style="font-size:0.8125rem;" :style="q.correct_answer === 'B' ? 'color:var(--patina);font-weight:700;' : 'color:var(--text-muted);'" x-text="'(B) ' + (q.options?.B || '...')"></div>
                            <div style="font-size:0.8125rem;" :style="q.correct_answer === 'C' ? 'color:var(--patina);font-weight:700;' : 'color:var(--text-muted);'" x-text="'(C) ' + (q.options?.C || '...')"></div>
                            <div style="font-size:0.8125rem;" :style="q.correct_answer === 'D' ? 'color:var(--patina);font-weight:700;' : 'color:var(--text-muted);'" x-text="'(D) ' + (q.options?.D || '...')"></div>
                        </div>
                        <template x-if="q.correct_answer">
                            <div style="font-size:0.8125rem;color:var(--patina);font-weight:600;">
                                Đáp án đúng: <span x-text="q.correct_answer"></span>
                            </div>
                        </template>
                    </div>
                </template>
            </div>
        </div>
    </div>

    {{-- TAB 3: SPLIT PDF VIEWER --}}
    <div x-show="activeTab === 'pdf'" style="display:none;">
        <div class="card">
            <h3 style="font-size:1.0625rem;font-weight:700;color:var(--text-bright);margin:0 0 0.5rem;">
                Tài liệu PDF đề thi đính kèm
            </h3>
            @if($examPdf)
                <p style="font-size:0.8125rem;color:var(--text-muted);margin:0 0 1.25rem;">
                    File đề: <strong>{{ $examPdf->file_name }}</strong> ({{ number_format($examPdf->file_size / 1024 / 1024, 2) }} MB).
                </p>
                <div style="border:1px solid var(--border);border-radius:var(--r-md);overflow:hidden;height:650px;">
                    <iframe src="{{ Storage::url($examPdf->file_path) }}" style="width:100%;height:100%;border:none;"></iframe>
                </div>
            @else
                <div style="text-align:center;padding:4rem 1rem;">
                    <p style="color:var(--text-muted);margin-bottom:1rem;">Đề thi này chưa được tải lên file PDF.</p>
                    <a href="{{ route('my-exams.uploads', $exam) }}" class="btn btn-primary">
                        + Tải lên PDF đề thi ngay
                    </a>
                </div>
            @endif
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function userParserStudio() {
    return {
        activeTab: 'answers',
        rawAnswerText: '',
        previewAnswerMap: {},
        previewAnswerCount: 0,
        rawQuestionText: '',
        previewQuestionsList: [],

        async previewAnswers() {
            if (!this.rawAnswerText.trim()) return;
            try {
                const res = await fetch("{{ route('my-exams.parser.answers', $exam) }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        raw_text: this.rawAnswerText,
                        apply: 0
                    })
                });
                const data = await res.json();
                if (data.success) {
                    this.previewAnswerMap = data.keys;
                    this.previewAnswerCount = data.count;
                }
            } catch (err) {
                alert('Có lỗi khi kiểm tra đáp án: ' + err.message);
            }
        },

        async previewQuestions() {
            if (!this.rawQuestionText.trim()) return;
            const partSelect = document.querySelector('select[name="exam_part_id"]');
            try {
                const res = await fetch("{{ route('my-exams.parser.questions', $exam) }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        exam_part_id: partSelect ? partSelect.value : 1,
                        raw_text: this.rawQuestionText,
                        apply: 0
                    })
                });
                const data = await res.json();
                if (data.success) {
                    this.previewQuestionsList = data.questions;
                }
            } catch (err) {
                alert('Có lỗi khi bóc tách câu hỏi: ' + err.message);
            }
        }
    };
}
</script>
@endpush
