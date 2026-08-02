@extends('layouts.app')
@section('title', 'إضافة مسؤول إعلام جديد')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-black text-navy font-cairo">إضافة مسؤول إعلام جديد</h1>
            <p class="text-gray-400 font-almarai text-sm mt-1">إنشاء حساب مستقل لمسؤول الإعلام وتحديد بيانات الدخول والتنبيهات</p>
        </div>
        <a href="{{ route('media-officers.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-600 rounded-2xl font-bold font-cairo hover:bg-gray-200 transition-all text-sm">
            <i class="fas fa-arrow-right ml-1"></i> العودة للقائمة
        </a>
    </div>

    @if($errors->any())
        <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-700 px-6 py-4 rounded-2xl font-cairo text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('media-officers.store') }}" method="POST" class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Name --}}
            <div>
                <label class="block text-sm font-bold text-navy mb-2 font-cairo">الاسم الكامل <span class="text-rose-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="أدخل اسم مسؤول الإعلام"
                       class="w-full px-4 py-3.5 rounded-2xl border border-gray-200 font-almarai text-sm focus:border-gold outline-none">
            </div>

            {{-- Email --}}
            <div>
                <label class="block text-sm font-bold text-navy mb-2 font-cairo">البريد الإلكتروني (لتسجيل الدخول) <span class="text-rose-500">*</span></label>
                <input type="email" name="email" value="{{ old('email') }}" required placeholder="example@domain.com"
                       class="w-full px-4 py-3.5 rounded-2xl border border-gray-200 font-almarai text-sm focus:border-gold outline-none text-left">
            </div>

            {{-- Phone --}}
            <div>
                <label class="block text-sm font-bold text-navy mb-2 font-cairo">رقم الجوال (لتلقي إشعارات الواتساب) <span class="text-rose-500">*</span></label>
                <input type="text" name="phone" value="{{ old('phone') }}" required placeholder="967770000000"
                       class="w-full px-4 py-3.5 rounded-2xl border border-gray-200 font-almarai text-sm focus:border-gold outline-none text-left">
                <span class="text-[11px] text-gray-400 font-almarai mt-1 block">سيُستخدم هذا الرقم لإرسال إشعارات الواتساب عند وجود إعلانات جديدة في الانتظار</span>
            </div>

            {{-- Center assignment (optional) --}}
            <div>
                <label class="block text-sm font-bold text-navy mb-2 font-cairo">المركز التابع له (اختياري)</label>
                <select name="center_id" class="w-full px-4 py-3.5 rounded-2xl border border-gray-200 font-almarai text-sm focus:border-gold outline-none">
                    <option value="">عام (يشرف على جميع المراكز الطلابية)</option>
                    @foreach($centers as $center)
                        <option value="{{ $center->id }}" {{ old('center_id') == $center->id ? 'selected' : '' }}>{{ $center->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Password --}}
            <div>
                <label class="block text-sm font-bold text-navy mb-2 font-cairo">كلمة المرور <span class="text-rose-500">*</span></label>
                <input type="password" name="password" required placeholder="••••••••"
                       class="w-full px-4 py-3.5 rounded-2xl border border-gray-200 font-almarai text-sm focus:border-gold outline-none">
            </div>
        </div>

        <div class="pt-6 border-t border-gray-100 flex items-center justify-end gap-3">
            <button type="submit" class="px-8 py-4 bg-navy text-white font-black font-cairo rounded-2xl shadow-lg hover:bg-navy/90 transition-all text-base flex items-center gap-2">
                <i class="fas fa-check-circle text-gold"></i> حفظ وإسهام حساب مسؤول الإعلام
            </button>
        </div>
    </form>
@endsection
