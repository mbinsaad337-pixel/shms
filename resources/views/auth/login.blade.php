@extends('layouts.guest')

@section('content')
    <div class="text-center mb-10">
        <h2 class="text-3xl font-black text-navy font-cairo">تسجيل الدخول للمنصة</h2>
        <p class="text-gray-400 font-almarai text-sm mt-2">نظام إدارة الإسكان الطلابي - النسخة المطورة</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <div>
            <label for="email" class="block text-sm font-black text-navy mb-2 font-cairo">البريد الإلكتروني أو اسم
                المستخدم</label>
            <div class="relative">
                <i class="fas fa-user absolute right-4 top-1/2 -translate-y-1/2 text-gray-300"></i>
                <input type="text" name="email" id="email" value="{{ old('email') }}" required autofocus
                    placeholder="s442100 أو البريد الجامعي"
                    class="block w-full pr-12 pl-4 py-3.5 rounded-xl border border-gray-100 bg-gray-50 focus:ring-2 focus:ring-navy focus:bg-white transition-all font-almarai text-sm">
            </div>
            @error('email')
                <p class="mt-2 text-xs text-red-600 font-cairo">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="block text-sm font-black text-navy mb-2 font-cairo">كلمة المرور</label>
            <div class="relative">
                <i class="fas fa-lock absolute right-4 top-1/2 -translate-y-1/2 text-gray-300"></i>
                <input type="password" name="password" id="password" required placeholder="••••••••"
                    class="block w-full pr-12 pl-4 py-3.5 rounded-xl border border-gray-100 bg-gray-50 focus:ring-2 focus:ring-navy focus:bg-white transition-all">
            </div>
            @error('password')
                <p class="mt-2 text-xs text-red-600 font-cairo">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-between">
            <label class="inline-flex items-center">
                <input type="checkbox" name="remember" class="rounded border-gray-300 text-secondary focus:ring-secondary">
                <span class="mr-2 text-sm text-gray-600">تذكرني</span>
            </label>

            @if (\Illuminate\Support\Facades\Route::has('password.request'))
                <a href="#" class="text-sm text-secondary hover:underline">نسيت كلمة المرور؟</a>
            @endif
        </div>

        <div>
            <button type="submit"
                class="w-full bg-navy text-white rounded-xl py-4 font-black font-cairo shadow-lg shadow-navy/20 hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center justify-center gap-3">
                <span class="text-gold"><i class="fas fa-sign-in-alt"></i></span>
                <span>تسجيل الدخول</span>
            </button>
        </div>
    </form>
@endsection
