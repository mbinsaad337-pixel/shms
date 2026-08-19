@extends('layouts.app')

@section('title', 'تغيير كلمة المرور')

@section('content')
    <div class="container mx-auto px-4 lg:px-6 py-4 md:py-8 mt-4">
        <div class="max-w-md mx-auto bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
            <div class="bg-primary px-5 sm:px-8 py-5 sm:py-6 text-white text-center">
                <i class="fas fa-lock text-3xl mb-3"></i>
                <h2 class="text-xl font-bold font-cairo">تحديث كلمة المرور</h2>
                <p class="text-white/80 font-almarai text-sm mt-1">يجب عليك تغيير كلمة المرور للمتابعة</p>
            </div>

            <form action="{{ route('profile.change_password.update') }}" method="POST" class="p-5 sm:p-8 space-y-6">
                @csrf

                <div>
                    <label class="block text-gray-700 font-cairo font-bold mb-2 text-sm">كلمة المرور الحالية</label>
                    <input type="password" name="current_password" required
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent outline-none font-almarai transition-all"
                        placeholder="••••••••">
                    @error('current_password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="pt-4 border-t">
                    <label class="block text-gray-700 font-cairo font-bold mb-2 text-sm">كلمة المرور الجديدة</label>
                    <input type="password" name="password" required
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent outline-none font-almarai transition-all"
                        placeholder="••••••••">
                    @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-gray-700 font-cairo font-bold mb-2 text-sm">تأكيد كلمة المرور الجديدة</label>
                    <input type="password" name="password_confirmation" required
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent outline-none font-almarai transition-all"
                        placeholder="••••••••">
                </div>

                <div class="pt-4">
                    <button type="submit"
                        class="w-full py-3 bg-secondary text-white rounded-xl hover:bg-orange-600 shadow-lg font-cairo font-bold transition-all transform hover:-translate-y-1">
                        تحديث كلمة المرور والدخول
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
