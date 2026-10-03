<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="TOEIC Practice Platform - Luyện thi TOEIC hiệu quả">
    <title>@yield('title', 'TOEIC Practice') — Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-gray-50 min-h-screen">

    <div class="flex min-h-screen">

        {{-- ── Sidebar ──────────────────────────────────────────────── --}}
        <aside class="w-64 bg-gray-900 text-white flex flex-col flex-shrink-0">

            {{-- Brand --}}
            <div class="h-16 flex items-center px-6 border-b border-gray-700">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
                    <span class="text-indigo-400 text-2xl font-bold">T</span>
                    <span class="text-white font-semibold tracking-wide">TOEIC Admin</span>
                </a>
            </div>

            {{-- Nav --}}
            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">

                <a href="{{ route('admin.dashboard') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                          {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 7h18M3 12h18M3 17h18"/>
                    </svg>
                    Dashboard
                </a>

                <a href="#"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-400 hover:bg-gray-800 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0121 9.586V19a2 2 0 01-2 2z"/>
                    </svg>
                    Quản lý đề thi
                </a>

                <a href="#"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-400 hover:bg-gray-800 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Câu hỏi
                </a>

                <a href="#"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-400 hover:bg-gray-800 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    Người dùng
                </a>

                <a href="#"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-400 hover:bg-gray-800 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Lịch sử thi
                </a>

            </nav>

            {{-- User info & logout --}}
            <div class="border-t border-gray-700 p-4">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-8 h-8 rounded-full bg-indigo-500 flex items-center justify-center text-sm font-bold">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-400 truncate">{{ auth()->user()->email }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full text-left text-xs text-gray-400 hover:text-red-400 transition-colors px-2 py-1 rounded">
                        Đăng xuất
                    </button>
                </form>
            </div>

        </aside>

        {{-- ── Main content ─────────────────────────────────────────── --}}
        <div class="flex-1 flex flex-col overflow-hidden">

            {{-- Top bar --}}
            <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-6">
                <h1 class="text-lg font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h1>
                <div class="flex items-center gap-3">
                    <span class="text-xs text-gray-500 bg-indigo-50 text-indigo-600 px-2 py-1 rounded-full font-medium">Admin</span>
                    <a href="{{ route('home') }}" target="_blank"
                       class="text-xs text-gray-500 hover:text-indigo-600 transition-colors">
                        Xem trang web →
                    </a>
                </div>
            </header>

            {{-- Flash messages --}}
            @if (session('success'))
                <div class="mx-6 mt-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mx-6 mt-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Page content --}}
            <main class="flex-1 overflow-y-auto p-6">
                @yield('content')
            </main>

        </div>
    </div>

    @stack('scripts')
</body>
</html>
