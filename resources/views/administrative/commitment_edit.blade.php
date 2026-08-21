@extends('layouts.app')
@section('title', 'تعديل التعهد')
@section('content')
<div class="container mx-auto px-4 py-6 max-w-2xl">
    <div class="mb-6">
        <a href="{{ route('administrative.index', ['tab' => 'commitments']) }}" class="px-6 py-3 bg-gray-50 text-navy rounded-2xl hover:bg-gray-100 font-cairo font-bold transition-all inline-flex items-center gap-2 border border-gray-100">
            <i class="fas fa-arrow-right"></i>
            <span>رجوع للقائمة</span>
        </a>
    </div>
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
        <h1 class="text-xl font-black text-navy font-cairo mb-6 flex items-center gap-2">
            <i class="fas fa-edit text-navy/60"></i> تعديل التعهد
        </h1>
        <form action="{{ route('commitments.update', $commitment->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf @method('PUT')
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1.5 font-cairo">الطالب</label>
                <select name="student_id" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-navy text-sm font-almarai outline-none">
                    @foreach($students as $s)
                    <option value="{{ $s->id }}" {{ $commitment->student_id == $s->id ? 'selected' : '' }}>{{ $s->name_ar }} ({{ $s->student_number }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1.5 font-cairo">عنوان التعهد</label>
                <input type="text" name="title" value="{{ old('title', $commitment->title) }}" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-navy text-sm font-almarai outline-none focus:ring-2 focus:ring-navy/10">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1.5 font-cairo">نص التعهد</label>
                <textarea name="text" rows="5" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-navy text-sm font-almarai outline-none focus:ring-2 focus:ring-navy/10 resize-none">{{ old('text', $commitment->text) }}</textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5 font-cairo">التاريخ</label>
                    <input type="date" name="date" value="{{ old('date', $commitment->date->format('Y-m-d')) }}" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-sm   outline-none focus:ring-2 focus:ring-navy/10">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5 font-cairo">الحالة</label>
                    <select name="status" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-navy text-sm font-almarai outline-none">
                        <option value="active" {{ $commitment->status === 'active' ? 'selected' : '' }}>نشط</option>
                        <option value="expired" {{ $commitment->status === 'expired' ? 'selected' : '' }}>منتهي</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1.5 font-cairo">مخالفة مرتبطة</label>
                <select name="violation_id" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-navy text-sm font-almarai outline-none">
                    <option value="">-- لا توجد مخالفة مرتبطة --</option>
                    @foreach($violations as $v)
                    <option value="{{ $v->id }}" {{ $commitment->violation_id == $v->id ? 'selected' : '' }}>{{ $v->type }} – {{ $v->student->name_ar ?? '' }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center gap-3 bg-amber-50 rounded-xl p-3 border border-amber-100">
                <input type="checkbox" name="requires_guardian_signature" id="guardian_sig_edit" value="1" class="w-4 h-4 accent-amber-500" {{ $commitment->requires_guardian_signature ? 'checked' : '' }}>
                <label for="guardian_sig_edit" class="text-sm font-bold text-amber-700 font-cairo">يتطلب توقيع ولي الأمر</label>
            </div>
            @if($commitment->image_path)
            <div class="bg-blue-50 rounded-xl p-3 border border-blue-100">
                <p class="text-xs text-blue-600 font-cairo mb-2"><i class="fas fa-image ml-1"></i> الصورة الحالية:</p>
                <img src="{{ Storage::url($commitment->image_path) }}" class="h-24 rounded-xl object-cover border border-blue-200">
            </div>
            @endif
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1.5 font-cairo">رفع صورة جديدة (اختياري)</label>
                <input type="file" name="image" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-navy/5 file:text-navy file:font-bold hover:file:bg-navy/10 cursor-pointer">
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 bg-navy text-white py-3 rounded-xl font-black font-cairo hover:bg-[#083358] transition-all flex items-center justify-center gap-2">
                    <i class="fas fa-save"></i> حفظ التعديلات
                </button>
                <a href="{{ route('administrative.index', ['tab' => 'commitments']) }}"
                   class="px-6 py-3 bg-gray-100 text-gray-600 rounded-xl font-bold font-cairo hover:bg-gray-200 transition-all">
                    إلغاء
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
