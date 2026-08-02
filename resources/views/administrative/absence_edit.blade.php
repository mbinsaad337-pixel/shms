@extends('layouts.app')
@section('title', 'تعديل سجل الغياب')
@section('content')
<div class="container mx-auto px-4 py-6 max-w-2xl">
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
        <h1 class="text-xl font-black text-navy font-cairo mb-6 flex items-center gap-2">
            <i class="fas fa-edit text-navy/60"></i> تعديل سجل الغياب
        </h1>
        <form action="{{ route('absences.update', $absence->id) }}" method="POST" class="space-y-5">
            @csrf @method('PUT')
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1.5 font-cairo">الطالب</label>
                <select name="student_id" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-navy text-sm font-almarai outline-none">
                    @foreach($students as $s)
                    <option value="{{ $s->id }}" {{ $absence->student_id == $s->id ? 'selected' : '' }}>{{ $s->name_ar }} ({{ $s->student_number }})</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5 font-cairo">تاريخ الغياب</label>
                    <input type="date" name="date" value="{{ old('date', $absence->date->format('Y-m-d')) }}" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-sm font-mono outline-none focus:ring-2 focus:ring-navy/10">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5 font-cairo">نوع الغياب</label>
                    <select name="absence_type" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-navy text-sm font-almarai outline-none focus:ring-2 focus:ring-navy/10">
                        <option value="">-- اختر النوع --</option>
                        <option value="housing" {{ $absence->absence_type == 'housing' ? 'selected' : '' }}>غياب سكن</option>
                        <option value="quran" {{ $absence->absence_type == 'quran' ? 'selected' : '' }}>غياب حلقة قرآنية</option>
                        <option value="activity" {{ $absence->absence_type == 'activity' ? 'selected' : '' }}>غياب نشاط</option>
                        <option value="other" {{ $absence->absence_type == 'other' ? 'selected' : '' }}>غياب آخر</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1.5 font-cairo">هل لديه عذر؟</label>
                <div class="flex gap-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="has_excuse" value="0" {{ !$absence->has_excuse ? 'checked' : '' }} class="accent-navy">
                        <span class="text-sm font-cairo text-gray-600">لا</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="has_excuse" value="1" {{ $absence->has_excuse ? 'checked' : '' }} class="accent-green-500">
                        <span class="text-sm font-cairo text-gray-600">نعم</span>
                    </label>
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1.5 font-cairo">نوع العذر (إن وجد)</label>
                <input type="text" name="excuse_type" value="{{ old('excuse_type', $absence->excuse_type) }}" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-navy text-sm font-almarai outline-none focus:ring-2 focus:ring-navy/10">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1.5 font-cairo">ملاحظات</label>
                <textarea name="notes" rows="3" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-navy text-sm font-almarai outline-none focus:ring-2 focus:ring-navy/10 resize-none">{{ old('notes', $absence->notes) }}</textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 bg-navy text-white py-3 rounded-xl font-black font-cairo hover:bg-[#083358] transition-all flex items-center justify-center gap-2">
                    <i class="fas fa-save"></i> حفظ التعديلات
                </button>
                <a href="{{ route('administrative.index', ['tab' => 'absences']) }}"
                   class="px-6 py-3 bg-gray-100 text-gray-600 rounded-xl font-bold font-cairo hover:bg-gray-200 transition-all">
                    إلغاء
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
