@extends('layouts.admin')

@section('title', 'Dashboard — Admin')
@section('page-title', 'Dashboard')

@section('content')

{{-- Stats grid --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">

    {{-- Total exams --}}
    <div class="bg-white rounded-xl border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-3">
            <span class="text-sm font-medium text-gray-500">Tổng đề thi</span>
            <div class="w-9 h-9 rounded-lg bg-indigo-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0121 9.586V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
        </div>
        <p class="text-3xl font-bold text-gray-900">{{ $stats['total_exams'] }}</p>
        <p class="text-xs text-gray-400 mt-1">{{ $stats['published_exams'] }} đã xuất bản</p>
    </div>

    {{-- Published exams --}}
    <div class="bg-white rounded-xl border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-3">
            <span class="text-sm font-medium text-gray-500">Đã xuất bản</span>
            <div class="w-9 h-9 rounded-lg bg-green-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        <p class="text-3xl font-bold text-gray-900">{{ $stats['published_exams'] }}</p>
        <p class="text-xs text-gray-400 mt-1">Người dùng có thể làm</p>
    </div>

    {{-- Total users --}}
    <div class="bg-white rounded-xl border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-3">
            <span class="text-sm font-medium text-gray-500">Người dùng</span>
            <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
        </div>
        <p class="text-3xl font-bold text-gray-900">{{ $stats['total_users'] }}</p>
        <p class="text-xs text-gray-400 mt-1">Tài khoản đã đăng ký</p>
    </div>

    {{-- Total attempts --}}
    <div class="bg-white rounded-xl border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-3">
            <span class="text-sm font-medium text-gray-500">Bài đã nộp</span>
            <div class="w-9 h-9 rounded-lg bg-purple-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
        </div>
        <p class="text-3xl font-bold text-gray-900">{{ $stats['total_attempts'] }}</p>
        <p class="text-xs text-gray-400 mt-1">Lần thi hoàn thành</p>
    </div>

</div>

{{-- Quick actions --}}
<div class="bg-white rounded-xl border border-gray-100 p-6">
    <h2 class="text-sm font-semibold text-gray-900 mb-4">Thao tác nhanh</h2>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <a href="#"
           class="flex items-center gap-3 px-4 py-3 rounded-lg border border-dashed border-gray-200
                  hover:border-indigo-300 hover:bg-indigo-50 transition-colors group">
            <svg class="w-5 h-5 text-gray-400 group-hover:text-indigo-500 transition-colors"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span class="text-sm font-medium text-gray-600 group-hover:text-indigo-700">Tạo đề thi mới</span>
        </a>
        <a href="#"
           class="flex items-center gap-3 px-4 py-3 rounded-lg border border-dashed border-gray-200
                  hover:border-indigo-300 hover:bg-indigo-50 transition-colors group">
            <svg class="w-5 h-5 text-gray-400 group-hover:text-indigo-500 transition-colors"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
            </svg>
            <span class="text-sm font-medium text-gray-600 group-hover:text-indigo-700">Upload tài liệu</span>
        </a>
        <a href="#"
           class="flex items-center gap-3 px-4 py-3 rounded-lg border border-dashed border-gray-200
                  hover:border-indigo-300 hover:bg-indigo-50 transition-colors group">
            <svg class="w-5 h-5 text-gray-400 group-hover:text-indigo-500 transition-colors"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="text-sm font-medium text-gray-600 group-hover:text-indigo-700">Thêm câu hỏi</span>
        </a>
    </div>
</div>

{{-- Phase note --}}
<div class="mt-6 p-4 bg-indigo-50 rounded-xl border border-indigo-100">
    <p class="text-sm text-indigo-700">
        <strong>Phase 1 hoàn thành.</strong>
        Tiếp theo: Phase 2 — Quản lý bộ đề, upload PDF/MP3.
    </p>
</div>

@endsection
