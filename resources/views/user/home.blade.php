@extends('layouts.app')

@section('title', 'Luyện thi TOEIC — TOEIC Practice Platform')

@section('content')

{{-- Hero section --}}
<section class="bg-gradient-to-br from-indigo-600 via-indigo-700 to-purple-700 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24">
        <div class="max-w-2xl">
            <div class="inline-flex items-center gap-2 bg-white/10 rounded-full px-4 py-1.5 text-sm mb-6 backdrop-blur-sm">
                <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
                {{ $exams->total() }} đề thi đang có sẵn
            </div>
            <h1 class="text-4xl lg:text-5xl font-bold leading-tight mb-4">
                Luyện thi TOEIC<br>
                <span class="text-indigo-200">hiệu quả & thực chiến</span>
            </h1>
            <p class="text-indigo-100 text-lg mb-8 leading-relaxed">
                Làm bài thi TOEIC thực tế với đề thi chuẩn, nghe audio, xem kết quả ngay lập tức.
            </p>
            @guest
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('register') }}"
                       class="bg-white text-indigo-700 px-6 py-3 rounded-xl font-semibold text-sm
                              hover:bg-indigo-50 transition-colors shadow-sm">
                        Bắt đầu miễn phí
                    </a>
                    <a href="{{ route('login') }}"
                       class="bg-white/10 text-white px-6 py-3 rounded-xl font-semibold text-sm
                              hover:bg-white/20 transition-colors backdrop-blur-sm">
                        Đã có tài khoản
                    </a>
                </div>
            @endguest
        </div>
    </div>
</section>

{{-- Exam list --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold text-gray-900">Đề thi TOEIC</h2>
        <span class="text-sm text-gray-500">{{ $exams->total() }} đề thi</span>
    </div>

    @if($exams->isEmpty())
        {{-- Empty state --}}
        <div class="text-center py-20">
            <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0121 9.586V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <h3 class="text-gray-700 font-semibold mb-1">Chưa có đề thi nào</h3>
            <p class="text-gray-500 text-sm">Admin đang chuẩn bị đề thi, vui lòng quay lại sau.</p>
        </div>
    @else
        {{-- Exam grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
            @foreach($exams as $exam)
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md hover:-translate-y-0.5
                        transition-all duration-200 overflow-hidden group">

                {{-- Exam color bar --}}
                <div class="h-1.5 bg-gradient-to-r from-indigo-500 to-purple-500"></div>

                <div class="p-5">
                    {{-- Badge --}}
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-medium text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-full">
                            {{ $exam->is_full_test ? 'Full Test' : 'Mini Test' }}
                        </span>
                        <span class="text-xs text-gray-400">
                            {{ $exam->parts_count }} Part{{ $exam->parts_count !== 1 ? 's' : '' }}
                        </span>
                    </div>

                    {{-- Title --}}
                    <h3 class="font-semibold text-gray-900 text-base leading-snug mb-2 line-clamp-2
                               group-hover:text-indigo-700 transition-colors">
                        {{ $exam->title }}
                    </h3>

                    {{-- Description --}}
                    @if($exam->description)
                        <p class="text-sm text-gray-500 line-clamp-2 mb-4">{{ $exam->description }}</p>
                    @else
                        <div class="mb-4"></div>
                    @endif

                    {{-- Meta --}}
                    <div class="flex items-center gap-3 text-xs text-gray-400 mb-4">
                        <div class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ $exam->duration_minutes }} phút
                        </div>
                        <div class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ $exam->total_questions }} câu
                        </div>
                    </div>

                    {{-- Action --}}
                    @auth
                        <a href="#"
                           class="block w-full text-center bg-indigo-600 text-white text-sm font-semibold
                                  py-2 rounded-lg hover:bg-indigo-700 transition-colors">
                            Làm bài
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                           class="block w-full text-center border border-indigo-200 text-indigo-600 text-sm font-medium
                                  py-2 rounded-lg hover:bg-indigo-50 transition-colors">
                            Đăng nhập để làm bài
                        </a>
                    @endauth
                </div>
            </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($exams->hasPages())
            <div class="mt-8 flex justify-center">
                {{ $exams->links() }}
            </div>
        @endif
    @endif

</section>

@endsection
