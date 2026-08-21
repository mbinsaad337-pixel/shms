@extends('layouts.app')

@section('title', 'تسجيل مركبة جديدة')

@section('content')
    <div class="container mx-auto px-6 py-8">
        <div class="max-w-2xl mx-auto">
            <div class="mb-6">
                <a href="{{ route('vehicles.index') }}" class="px-6 py-3 bg-gray-50 text-navy rounded-2xl hover:bg-gray-100 font-cairo font-bold transition-all inline-flex items-center gap-2 border border-gray-100">
                    <i class="fas fa-arrow-right"></i>
                    <span>رجوع للقائمة</span>
                </a>
            </div>
        </div>
        <div class="max-w-2xl mx-auto bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
            <div class="bg-primary px-10 py-8 text-white relative">
                <h2 class="text-2xl font-bold font-cairo">تسجيل مركبة طالب</h2>
                <p class="text-white/80 font-almarai mt-2">تسجيل بيانات السيارة وربطها بملف الطالب</p>
            </div>

            <form action="{{ route('vehicles.store') }}" method="POST" enctype="multipart/form-data" class="p-10 space-y-6">
                @csrf

                <div>
                    <label class="block text-gray-700 font-cairo font-bold mb-2">اختيار الطالب</label>
                    <select name="student_id" required
                        class="w-full px-5 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent outline-none font-almarai bg-white transition-all">
                        <option value="">-- اختر الطالب --</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}">{{ $student->name_ar }} ({{ $student->university_id }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-gray-700 font-cairo font-bold mb-2">نوع المركبة</label>
                        <input type="text" name="type" required placeholder="مثال: تويوتا"
                            class="w-full px-5 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent outline-none font-almarai transition-all">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-cairo font-bold mb-2">الموديل / السنة</label>
                        <input type="text" name="model" required placeholder="مثال: كامري 2023"
                            class="w-full px-5 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent outline-none font-almarai transition-all">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-gray-700 font-cairo font-bold mb-2">رقم اللوحة</label>
                        <input type="text" name="plate_number" required placeholder="أ ب ج 1234"
                            class="w-full px-5 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent outline-none font-almarai text-center tracking-widest font-bold bg-gray-50 transition-all">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-cairo font-bold mb-2">لون المركبة</label>
                        <input type="text" name="color" placeholder="مثال: أبيض"
                            class="w-full px-5 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent outline-none font-almarai transition-all">
                    </div>
                </div>

                <div>
                    <label class="block text-gray-700 font-cairo font-bold mb-2">صورة وثيقة المركبة (اختياري)</label>
                    <input type="file" name="document_photo" accept="image/*"
                        class="w-full px-5 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent outline-none font-almarai transition-all">
                </div>

                <div class="flex justify-end gap-4 pt-6">
                    <a href="{{ route('vehicles.index') }}"
                        class="px-8 py-3 border border-gray-200 text-gray-600 rounded-xl hover:bg-gray-50 font-cairo font-bold transition-all text-sm">إلغاء</a>
                    <button type="submit"
                        class="px-10 py-3 bg-secondary text-white rounded-xl hover:bg-orange-600 shadow-lg font-cairo font-bold transition-all transform hover:-translate-y-1 text-sm">تسجيل
                        المركبة</button>
                </div>
            </form>
        </div>
    </div>
@endsection
