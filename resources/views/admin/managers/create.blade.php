@extends('layouts.app')

@section('title', 'إضافة مدير مركز')

@section('content')
    <div class="container mx-auto px-6 py-8">
        <div class="max-w-2xl mx-auto">
            <div class="mb-6">
                <a href="{{ route('managers.index') }}" class="px-6 py-3 bg-gray-50 text-navy rounded-2xl hover:bg-gray-100 font-cairo font-bold transition-all inline-flex items-center gap-2 border border-gray-100">
                    <i class="fas fa-arrow-right"></i>
                    <span>رجوع للقائمة</span>
                </a>
            </div>
        </div>
        <div class="max-w-2xl mx-auto bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">
            <div class="bg-primary px-10 py-8 text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16"></div>
                <h2 class="text-2xl font-bold font-cairo relative z-10">إضافة مدير مركز جديد</h2>
                <p class="text-white/80 font-almarai mt-2 relative z-10">قم بتعبئة البيانات لتعيين مدير لموقع محدد.</p>
            </div>

            <form action="{{ route('managers.store') }}" method="POST" class="p-10 space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-gray-700 font-cairo font-bold mb-2">الاسم الكامل</label>
                        <input type="text" name="name" required
                            class="w-full px-5 py-3 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-primary focus:border-transparent outline-none font-almarai transition-all"
                            placeholder="مثلاً: محمد أحمد">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-cairo font-bold mb-2">رقم الجوال</label>
                        <input type="text" name="phone" required
                            class="w-full px-5 py-3 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-primary focus:border-transparent outline-none font-almarai transition-all"
                            placeholder="05xxxxxxxx">
                    </div>
                </div>

                <div>
                    <label class="block text-gray-700 font-cairo font-bold mb-2">البريد الإلكتروني</label>
                    <input type="email" name="email" required
                        class="w-full px-5 py-3 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-primary focus:border-transparent outline-none font-almarai transition-all"
                        placeholder="manager@shms.com">
                </div>

                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label class="block text-gray-700 font-cairo font-bold mb-2">المركز المعين</label>
                        <select name="center_id" required
                            class="w-full px-5 py-3 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-primary focus:border-transparent outline-none font-almarai transition-all bg-white">
                            <option value="">اختر المركز...</option>
                            @foreach($centers as $center)
                                <option value="{{ $center->id }}">{{ $center->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-gray-700 font-cairo font-bold mb-2">كلمة المرور المؤقتة</label>
                    <input type="password" name="password" required
                        class="w-full px-5 py-3 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-primary focus:border-transparent outline-none font-almarai transition-all">
                    <p class="text-xs text-gray-400 mt-2 font-almarai">سيطلب من المدير تغيير كلمة المرور عند أول تسجيل دخول.
                    </p>
                </div>

                <div class="flex justify-end gap-4 pt-6">
                    <a href="{{ route('managers.index') }}"
                        class="px-8 py-3 border border-gray-200 text-gray-600 rounded-2xl hover:bg-gray-50 font-cairo font-bold transition-all">إلغاء</a>
                    <button type="submit"
                        class="px-10 py-3 bg-secondary text-white rounded-2xl hover:bg-orange-600 shadow-xl font-cairo font-bold transition-all transform hover:-translate-y-1">حفظ
                        البيانات</button>
                </div>
            </form>
        </div>
    </div>
@endsection
