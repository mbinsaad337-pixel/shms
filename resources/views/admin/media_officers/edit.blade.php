@extends('layouts.app')
@section('title', 'تعديل بيانات مسؤول الإعلام')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-black text-navy font-cairo">تعديل بيانات مسؤول الإعلام</h1>
            <p class="text-gray-400 font-almarai text-sm mt-1">تحديث معلومات الحساب أو تغيير كلمة المرور ورقم الواتساب</p>
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

    <form action="{{ route('media-officers.update', $mediaOfficer) }}" method="POST" class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Name --}}
            <div>
                <label class="block text-sm font-bold text-navy mb-2 font-cairo">الاسم الكامل <span class="text-rose-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $mediaOfficer->name) }}" required
                       class="w-full px-4 py-3.5 rounded-2xl border border-gray-200 font-almarai text-sm focus:border-gold outline-none">
            </div>

            {{-- Email --}}
            <div>
                <label class="block text-sm font-bold text-navy mb-2 font-cairo">البريد الإلكتروني <span class="text-rose-500">*</span></label>
                <input type="email" name="email" value="{{ old('email', $mediaOfficer->email) }}" required
                       class="w-full px-4 py-3.5 rounded-2xl border border-gray-200 font-almarai text-sm focus:border-gold outline-none text-left">
            </div>

            {{-- Phone --}}
            <div>
                <label class="block text-sm font-bold text-navy mb-2 font-cairo">رقم الجوال (واتساب) <span class="text-rose-500">*</span></label>
                <input type="text" name="phone" value="{{ old('phone', $mediaOfficer->phone) }}" required
                       class="w-full px-4 py-3.5 rounded-2xl border border-gray-200 font-almarai text-sm focus:border-gold outline-none text-left">
            </div>

            {{-- Center assignment (optional) --}}
            <div>
                <label class="block text-sm font-bold text-navy mb-2 font-cairo">المركز التابع له (اختياري)</label>
                <select name="center_id" class="w-full px-4 py-3.5 rounded-2xl border border-gray-200 font-almarai text-sm focus:border-gold outline-none">
                    <option value="">عام (يشرف على جميع المراكز الطلابية)</option>
                    @foreach($centers as $center)
                        <option value="{{ $center->id }}" {{ old('center_id', $mediaOfficer->center_id) == $center->id ? 'selected' : '' }}>{{ $center->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Password (optional update) --}}
            <div>
                <label class="block text-sm font-bold text-navy mb-2 font-cairo">كلمة المرور جديدة (اتركها فارغة للتغاضي)</label>
                <input type="password" name="password" placeholder="••••••••"
                       class="w-full px-4 py-3.5 rounded-2xl border border-gray-200 font-almarai text-sm focus:border-gold outline-none">
            </div>
        </div>

        <div class="pt-6 border-t border-gray-100 flex items-center justify-end gap-3">
            <button type="submit" class="px-8 py-4 bg-navy text-white font-black font-cairo rounded-2xl shadow-lg hover:bg-navy/90 transition-all text-base flex items-center gap-2">
                <i class="fas fa-save text-gold"></i> تحديث البيانات
            </button>
        </div>
    </form>
@endsection
