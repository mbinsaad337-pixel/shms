@extends('layouts.app')

@section('title', 'تعديل حلقة')

@section('content')
    <div
        class="mb-8 flex flex-col md:flex-row justify-between items-center bg-white p-6 rounded-2xl border-l-8 border-gold shadow-sm">
        <div>
            <h1 class="text-3xl font-black text-navy font-cairo">تعديل الحلقة: {{ $quranCircle->name }}</h1>
            <p class="text-gray-400 font-almarai text-sm mt-1">تحديث البيانات الأساسية للمجموعة التعليمية</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="{{ route('quran-circles.index') }}"
                class="px-6 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 shadow-sm font-cairo font-bold transition-all flex items-center gap-2">
                <i class="fas fa-arrow-right"></i>
                <span>العودة للقائمة</span>
            </a>
        </div>
    </div>

    <div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-8">
            <form action="{{ route('quran-circles.update', $quranCircle) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="space-y-6">
                    <div>
                        <label for="name" class="block text-sm font-bold text-gray-700 mb-2 font-cairo">اسم الحلقة</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $quranCircle->name) }}" required
                            placeholder="مثلاً: حلقة الإمام عاصم"
                            class="w-full rounded-xl border-gray-200 focus:border-gold focus:ring-gold shadow-sm bg-gray-50/50 p-3 font-almarai">
                        @error('name') <p class="text-red-500 text-xs mt-1 font-cairo">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="teacher_id" class="block text-sm font-bold text-gray-700 mb-2 font-cairo">مدرس الحلقة</label>
                        <select name="teacher_id" id="teacher_id" required
                            class="w-full rounded-xl border-gray-200 focus:border-gold focus:ring-gold shadow-sm bg-gray-50/50 p-3 font-almarai">
                            @foreach ($teachers as $teacher)
                                <option value="{{ $teacher->id }}" {{ old('teacher_id', $quranCircle->teacher_id) == $teacher->id ? 'selected' : '' }}>
                                    {{ $teacher->name }} 
                                    ({{ $teacher->getRoleNames()->implode(', ') }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="type" class="block text-sm font-bold text-gray-700 mb-2 font-cairo">نوع الحلقة</label>
                        <div class="grid grid-cols-2 gap-4" x-data="{ type: '{{ old('type', $quranCircle->type) }}' }">
                            <label class="relative cursor-pointer">
                                <input type="radio" name="type" value="memorization" class="sr-only" x-model="type">
                                <div :class="type === 'memorization' ? 'border-gold bg-gold/5 ring-1 ring-gold' : 'border-gray-100 bg-white'"
                                    class="p-6 border-2 rounded-2xl transition-all text-center relative">
                                    <div x-show="type === 'memorization'" class="absolute -top-2 -right-2 bg-gold text-white w-6 h-6 rounded-full flex items-center justify-center shadow-sm">
                                        <i class="fas fa-check text-[10px]"></i>
                                    </div>
                                    <i class="fas fa-plus-square text-3xl mb-3 block" :class="type === 'memorization' ? 'text-gold' : 'text-gray-300'"></i>
                                    <span class="font-cairo font-bold block" :class="type === 'memorization' ? 'text-navy' : 'text-gray-400'">تحفيظ مخصص</span>
                                </div>
                            </label>
                            <label class="relative cursor-pointer">
                                <input type="radio" name="type" value="recitation" class="sr-only" x-model="type">
                                <div :class="type === 'recitation' ? 'border-gold bg-gold/5 ring-1 ring-gold' : 'border-gray-100 bg-white'"
                                    class="p-6 border-2 rounded-2xl transition-all text-center relative">
                                    <div x-show="type === 'recitation'" class="absolute -top-2 -right-2 bg-gold text-white w-6 h-6 rounded-full flex items-center justify-center shadow-sm">
                                        <i class="fas fa-check text-[10px]"></i>
                                    </div>
                                    <i class="fas fa-book-reader text-3xl mb-3 block" :class="type === 'recitation' ? 'text-gold' : 'text-gray-300'"></i>
                                    <span class="font-cairo font-bold block" :class="type === 'recitation' ? 'text-navy' : 'text-gray-400'">تلاوة ومراجعة</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-bold text-gray-700 mb-2 font-cairo">الوصف /
                            ملاحظات إضافية</label>
                        <textarea name="description" id="description" rows="4"
                            placeholder="أدخل أي تفاصيل حول مستوى الحلقة أو أوقاتها..."
                            class="w-full rounded-xl border-gray-200 focus:border-gold focus:ring-gold shadow-sm bg-gray-50/50 p-3 font-almarai">{{ old('description', $quranCircle->description) }}</textarea>
                    </div>

                    <div class="pt-6 border-t border-gray-100 flex justify-end">
                        <button type="submit"
                            class="px-10 py-3 bg-navy text-white rounded-xl hover:bg-navy/90 shadow-lg font-cairo font-bold transition-all transform hover:-translate-y-1 flex items-center gap-2">
                            <i class="fas fa-save text-gold"></i>
                            <span>تحديث البيانات</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
