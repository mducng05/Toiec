@extends('layouts.admin')

@section('title', 'Bóc tách & Tự động hóa đề thi — ' . $exam->title)
@section('page-title', 'Công cụ tự động hóa & Parser')

@section('topbar-actions')
    <a href="{{ route('admin.exams.show', $exam) }}" class="btn btn-ghost btn-sm">
        ← Chi tiết đề thi
    </a>
    <a href="{{ route('admin.exams.questions.index', $exam) }}" class="btn btn-secondary btn-sm">
        Ngân hàng câu hỏi
    </a>
@endsection

@section('content')
<div x-data="parserStudio()">

    {{-- Breadcrumb & Header --}}
    <div style="margin-bottom:1.5rem;">
        <a href="{{ route('admin.exams.show', $exam) }}" style="display:inline-flex;align-items:center;gap:6px;font-size:0.8125rem;color:var(--text-faint);text-decoration:none;margin-bottom:0.75rem;transition:color 0.15s;" onmouseover="this.style.color='var(--gold)'" onmouseout="this.style.color='var(--text-faint)'">
            <svg style="width:14px;height:14px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Quay lại đề thi: {{ $exam->title }}
        </a>
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
            <div>
                <h2 style="font-size:1.5rem;font-weight:700;color:var(--text-bright);margin:0 0 0.35rem;">
                    Bóc tách & Nạp đề thi tự động
                </h2>
                <p style="font-size:0.875rem;color:var(--text-muted);margin:0;">
                    Nhập nhanh bảng đáp án 200 câu, bóc tách câu hỏi từ văn bản thô hoặc soi tài liệu PDF song song.
                </p>
            </div>
        </div>
    </div>

    {{-- ── Studio Tabs Navigation ────────────────────────────── --}}
    <div style="display:flex;gap:8px;border-bottom:1px solid var(--border);margin-bottom:2rem;">
        <button type="button" @click="activeTab = 'answers'"
                style="padding:0.75rem 1.25rem;font-size:0.875rem;font-weight:600;background:none;border:none;border-bottom:2px solid;cursor:pointer;display:flex;align-items:center;gap:8px;transition:all 0.15s;"
                :style="activeTab === 'answers' ? 'color:var(--gold);border-color:var(--gold);' : 'color:var(--text-muted);border-color:transparent;'">
            <svg style="width:16px;height:16px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
            </svg>
            Nạp bảng đáp án hàng loạt
        </button>

        <button type="button" @click="activeTab = 'questions'"
                style="padding:0.75rem 1.25rem;font-size:0.875rem;font-weight:600;background:none;border:none;border-bottom:2px solid;cursor:pointer;display:flex;align-items:center;gap:8px;transition:all 0.15s;"
                :style="activeTab === 'questions' ? 'color:var(--gold);border-color:var(--gold);' : 'color:var(--text-muted);border-color:transparent;'">
            <svg style="width:16px;height:16px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
            Bóc tách văn bản câu hỏi
        </button>

        <button type="button" @click="activeTab = 'pdf'"
                style="padding:0.75rem 1.25rem;font-size:0.875rem;font-weight:600;background:none;border:none;border-bottom:2px solid;cursor:pointer;display:flex;align-items:center;gap:8px;transition:all 0.15s;"
                :style="activeTab === 'pdf' ? 'color:var(--gold);border-color:var(--gold);' : 'color:var(--text-muted);border-color:transparent;'">
            <svg style="width:16px;height:16px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0121 9.586V19a2 2 0 01-2 2z"/>
            </svg>
            Soi đề PDF song song
        </button>
    </div>

    {{-- ── TAB 1: BULK ANSWER KEYS ────────────────────────────── --}}
    <div x-show="activeTab === 'answers'">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:2rem;align-items:start;">
            
            {{-- Input Form --}}
            <div class="card">
                <h3 style="font-size:1rem;font-weight:700;color:var(--text-bright);margin:0 0 0.5rem;">
                    Dán chuỗi bảng đáp án
                </h3>
                <p style="font-size:0.8125rem;color:var(--text-faint);margin:0 0 1.25rem;">
                    Hỗ trợ tất cả định dạng: <code>1A 2B 3C</code>, <code>1. A  2. B</code>, <code>1-A, 2-B</code>, hoặc từng dòng <code>1: A</code>.
                </p>

                <form method="POST" action="{{ route('admin.exams.parser.answers', $exam) }}">
                    @csrf
                    <input type="hidden" name="apply" value="1">

                    <div style="margin-bottom:1rem;">
                        <textarea name="raw_text" x-model="rawAnswerText" rows="10"
                                  placeholder="Ví dụ dán chuỗi:&#10;1. C&#10;2. A&#10;3. B&#10;4. D&#10;5. A&#10;hoặc: 1C 2A 3B 4D 5A 6B..."
                                  class="input textarea" style="font-family:monospace;font-size:0.875rem;" required></textarea>
                    </div>

                    <div style="margin-bottom:1.25rem;">
                        <label style="display:flex;align-items:center;gap:8px;font-size:0.8125rem;color:var(--text-warm);cursor:pointer;">
                            <input type="checkbox" name="create_missing" value="1" checked style="accent-color:var(--gold);width:16px;height:16px;">
                            <span>Tự động tạo câu hỏi mới nếu câu hỏi chưa tồn tại trong đề thi</span>
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
            <div class="card" style="min-height:300px;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
                    <h3 style="font-size:1rem;font-weight:700;color:var(--text-bright);margin:0;">
                        Kết quả nhận diện: <span style="color:var(--gold);" x-text="previewAnswerCount">0</span> câu
                    </h3>
                </div>

                <div x-show="previewAnswerCount === 0" style="text-align:center;padding:3rem 1rem;color:var(--text-faint);font-size:0.875rem;">
                    Dán văn bản đáp án và bấm <strong>"Kiểm tra trước"</strong> để xem danh sách đáp án nhận diện.
                </div>

                <div x-show="previewAnswerCount > 0" style="display:grid;grid-template-columns:repeat(auto-fill, minmax(70px, 1fr));gap:6px;max-height:450px;overflow-y:auto;padding-right:4px;">
                    <template x-for="(ans, qNum) in previewAnswerMap" :key="qNum">
                        <div style="padding:0.4rem;background:var(--bg-deep);border:1px solid var(--border);border-radius:var(--r-sm);text-align:center;">
                            <span style="font-size:0.75rem;color:var(--text-faint);" x-text="'#' + qNum"></span>
                            <div style="font-size:1rem;font-weight:700;color:var(--patina);" x-text="ans"></div>
                        </div>
                    </template>
                </div>
            </div>

        </div>
    </div>

    {{-- ── TAB 2: QUESTION TEXT PARSER ────────────────────────── --}}
    <div x-show="activeTab === 'questions'" style="display:none;">
        <div class="card" style="margin-bottom:1.5rem;">
            <h3 style="font-size:1rem;font-weight:700;color:var(--text-bright);margin:0 0 0.5rem;">
                Bóc tách câu hỏi & 4 lựa chọn từ văn bản
            </h3>
            <p style="font-size:0.8125rem;color:var(--text-faint);margin:0 0 1.25rem;">
                Hệ thống tự động tách số câu, lời dẫn, các lựa chọn (A), (B), (C), (D), đáp án đúng và lời giải thích.
            </p>

            <form method="POST" action="{{ route('admin.exams.parser.questions', $exam) }}">
                @csrf
                <input type="hidden" name="apply" value="1">

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem;">
                    <div>
                        <label class="label">Phần thi cần nhập vào <span style="color:var(--error)">*</span></label>
                        <select name="exam_part_id" class="select" required>
                            @foreach($exam->parts as $part)
                                <option value="{{ $part->id }}">
                                    Part {{ $part->part_number }}: {{ $part->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div style="margin-bottom:1rem;">
                    <label class="label">Văn bản câu hỏi thô</label>
                    <textarea name="raw_text" rows="12"
                              placeholder="101. Customer satisfaction is our top -------.&#10;(A) prioritize&#10;(B) priority&#10;(C) prior&#10;(D) primarily&#10;Key: B&#10;Explanation: Cần danh từ sau tính từ sở hữu our top.&#10;&#10;102. All staff members must submit their reports by Friday.&#10;(A) on&#10;*(B) by&#10;(C) in&#10;(D) at"
                              class="input textarea" style="font-family:monospace;font-size:0.875rem;" required></textarea>
                </div>

                <div style="display:flex;align-items:center;gap:10px;">
                    <button type="submit" class="btn btn-primary">
                        <svg style="width:15px;height:15px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                        Bóc tách & Nhập vào đề thi
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── TAB 3: SIDE-BY-SIDE PDF ASSISTANT ─────────────────── --}}
    <div x-show="activeTab === 'pdf'" style="display:none;">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;align-items:start;min-height:700px;">
            
            {{-- Left: PDF Viewer --}}
            <div class="card" style="padding:0;overflow:hidden;height:750px;display:flex;flex-direction:column;">
                <div style="padding:0.75rem 1rem;background:var(--bg-deep);border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;">
                    <span style="font-size:0.8125rem;font-weight:600;color:var(--text-bright);">Tài liệu PDF</span>
                    <div style="display:flex;gap:6px;">
                        @if($examPdf)
                            <button type="button" @click="pdfSrc = '{{ route('admin.files.serve', base64_encode($examPdf->file_path)) }}'"
                                    class="btn btn-sm btn-ghost" style="font-size:0.75rem;">
                                PDF Đề thi
                            </button>
                        @endif
                        @if($answerPdf)
                            <button type="button" @click="pdfSrc = '{{ route('admin.files.serve', base64_encode($answerPdf->file_path)) }}'"
                                    class="btn btn-sm btn-ghost" style="font-size:0.75rem;">
                                PDF Đáp án
                            </button>
                        @endif
                    </div>
                </div>

                <div style="flex:1;background:var(--bg-ground);">
                    @if($examPdf || $answerPdf)
                        <iframe :src="pdfSrc" style="width:100%;height:100%;border:none;"></iframe>
                    @else
                        <div style="text-align:center;padding:4rem 1rem;color:var(--text-faint);">
                            Chưa có file PDF đề thi hoặc đáp án nào được tải lên cho đề này.
                            <a href="{{ route('admin.exams.uploads', $exam) }}" class="btn btn-secondary btn-sm" style="margin-top:1rem;">
                                Tải lên PDF ngay
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Right: Quick Action Parser Box --}}
            <div class="card" style="height:750px;display:flex;flex-direction:column;justify-content:space-between;">
                <div>
                    <h3 style="font-size:1rem;font-weight:700;color:var(--text-bright);margin:0 0 0.5rem;">
                        Vừa xem PDF vừa nạp dữ liệu
                    </h3>
                    <p style="font-size:0.8125rem;color:var(--text-faint);margin:0 0 1rem;">
                        Bạn có thể copy văn bản từ khung PDF bên trái hoặc nhìn đáp án rồi dán vào đây:
                    </p>

                    <form method="POST" action="{{ route('admin.exams.parser.answers', $exam) }}">
                        @csrf
                        <input type="hidden" name="apply" value="1">
                        <textarea name="raw_text" rows="14"
                                  placeholder="Nhìn bảng đáp án bên trái và gõ hoặc dán chuỗi:&#10;1A 2B 3C 4D 5A 6B 7C...&#10;101B 102C 103A..."
                                  class="input textarea" style="font-family:monospace;font-size:0.875rem;" required></textarea>

                        <div style="margin:1rem 0;">
                            <label style="display:flex;align-items:center;gap:8px;font-size:0.8125rem;color:var(--text-warm);cursor:pointer;">
                                <input type="checkbox" name="create_missing" value="1" checked style="accent-color:var(--gold);">
                                <span>Tự động tạo câu hỏi slot nếu chưa có</span>
                            </label>
                        </div>

                        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">
                            Áp dụng đáp án vào đề thi
                        </button>
                    </form>
                </div>

                <div style="padding:1rem;background:var(--bg-deep);border-radius:var(--r-md);border:1px solid var(--border);font-size:0.75rem;color:var(--text-faint);">
                    💡 <strong>Mẹo chuyên gia:</strong> Mở song song file PDF đáp án, copy bảng đáp án dạng cột và dán vào ô trên, hệ thống sẽ tự động ghép với 200 câu hỏi trong 1 giây.
                </div>
            </div>

        </div>
    </div>

</div>

@push('scripts')
<script>
    function parserStudio() {
        return {
            activeTab: 'answers',
            rawAnswerText: '',
            previewAnswerCount: 0,
            previewAnswerMap: {},
            pdfSrc: "{{ $examPdf ? route('admin.files.serve', base64_encode($examPdf->file_path)) : ($answerPdf ? route('admin.files.serve', base64_encode($answerPdf->file_path)) : '') }}",

            async previewAnswers() {
                if (!this.rawAnswerText.trim()) {
                    alert('Vui lòng nhập văn bản đáp án.');
                    return;
                }

                try {
                    const res = await fetch("{{ route('admin.exams.parser.answers', $exam) }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            raw_text: this.rawAnswerText,
                            apply: 0
                        })
                    });

                    const data = await res.json();
                    if (data.success) {
                        this.previewAnswerCount = data.count;
                        this.previewAnswerMap = data.keys;
                    }
                } catch (e) {
                    console.error(e);
                    alert('Lỗi phân tích văn bản đáp án.');
                }
            }
        };
    }
</script>
@endpush

@endsection
