@extends('layouts.app')

@section('title', 'Đăng ký — TOEIC Practice')

@section('content')
<div class="min-h-[calc(100vh-4rem)] flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">

        {{-- Card --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">

            {{-- Header --}}
            <div class="text-center mb-8">
                <div class="w-14 h-14 bg-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <span class="text-white text-2xl font-bold">T</span>
                </div>
                <h1 class="text-2xl font-bold text-gray-900">Tạo tài khoản</h1>
                <p class="text-gray-500 text-sm mt-1">Bắt đầu luyện TOEIC miễn phí</p>
            </div>

            {{-- Form --}}
            <form id="register-form" method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf

                {{-- Name --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Họ và tên
                    </label>
                    <input id="name" type="text" name="name"
                           value="{{ old('name') }}"
                           required autocomplete="name" autofocus
                           placeholder="Nguyễn Văn A"
                           class="w-full px-4 py-2.5 rounded-xl border text-sm transition-colors
                                  {{ $errors->has('name') ? 'border-red-400 bg-red-50' : 'border-gray-200 focus:border-indigo-500' }}
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                    @error('name')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Email
                    </label>
                    <input id="email" type="email" name="email"
                           value="{{ old('email') }}"
                           required autocomplete="email"
                           placeholder="you@example.com"
                           class="w-full px-4 py-2.5 rounded-xl border text-sm transition-colors
                                  {{ $errors->has('email') ? 'border-red-400 bg-red-50' : 'border-gray-200 focus:border-indigo-500' }}
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                    @error('email')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Mật khẩu
                    </label>
                    <input id="password" type="password" name="password"
                           required autocomplete="new-password"
                           placeholder="Ít nhất 8 ký tự"
                           class="w-full px-4 py-2.5 rounded-xl border text-sm transition-colors
                                  {{ $errors->has('password') ? 'border-red-400 bg-red-50' : 'border-gray-200 focus:border-indigo-500' }}
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                    @error('password')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password confirmation --}}
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Xác nhận mật khẩu
                    </label>
                    <input id="password_confirmation" type="password" name="password_confirmation"
                           required autocomplete="new-password"
                           placeholder="Nhập lại mật khẩu"
                           class="w-full px-4 py-2.5 rounded-xl border text-sm transition-colors
                                  border-gray-200 focus:border-indigo-500
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                </div>

                {{-- Submit --}}
                <button id="register-btn" type="submit"
                        class="w-full bg-indigo-600 text-white py-2.5 px-4 rounded-xl text-sm font-semibold
                               hover:bg-indigo-700 active:scale-[0.98] transition-all focus:outline-none
                               focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Tạo tài khoản
                </button>

            </form>

            {{-- Login link --}}
            <div class="mt-6 text-center text-sm text-gray-500">
                Đã có tài khoản?
                <a href="{{ route('login') }}" class="text-indigo-600 font-medium hover:underline">
                    Đăng nhập
                </a>
            </div>

        </div>
    </div>
</div>
@endsection
