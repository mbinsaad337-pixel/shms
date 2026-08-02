@extends('layouts.app')
@section('title', 'تعديل طلب الاستئذان')
@section('content')
<div class="container mx-auto px-4 py-6 max-w-2xl">
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
        <h1 class="text-xl font-black text-navy font-cairo mb-6 flex items-center gap-2">
            <i class="fas fa-edit text-navy/60"></i> تعديل طلب الاستئذان
        </h1>
        <form action="{{ route('leaves.update', $leave->id) }}" method="POST" class="space-y-5">
            @csrf @method('PUT')
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5 font-cairo">الطالب</label>
                    <select name="student_id" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-navy text-sm font-almarai outline-none">
                        @foreach($students as $s)
                        <option value="{{ $s->id }}" {{ $leave->student_id == $s->id ? 'selected' : '' }}>{{ $s->name_ar }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5 font-cairo">الحالة</label>
                    <select name="status" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-navy text-sm font-almarai outline-none font-bold">
                        <option value="pending" {{ $leave->status == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                        <option value="approved" {{ $leave->status == 'approved' ? 'selected' : '' }}>موافق عليه</option>
                        <option value="rejected" {{ $leave->status == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                        <option value="returned" {{ $leave->status == 'returned' ? 'selected' : '' }}>عاد</option>
                        <option value="not_returned" {{ $leave->status == 'not_returned' ? 'selected' : '' }}>لم يعد</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1.5 font-cairo">نوع الاستئذان</label>
                <select name="type" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-navy text-sm font-almarai outline-none focus:ring-2 focus:ring-navy/10">
                    <option value="temporary" {{ $leave->type == 'temporary' ? 'selected' : '' }}>استئذان مؤقت</option>
                    <option value="vacation" {{ $leave->type == 'vacation' ? 'selected' : '' }}>إجازة</option>
                    <option value="medical" {{ $leave->type == 'medical' ? 'selected' : '' }}>إجازة طبية</option>
                    <option value="lateness" {{ $leave->type == 'lateness' ? 'selected' : '' }}>تأخير</option>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5 font-cairo">تاريخ المغادرة</label>
                    <input type="date" name="departure_date" value="{{ old('departure_date', $leave->departure_date->format('Y-m-d')) }}" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-sm font-mono outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5 font-cairo">وقت المغادرة</label>
                    <input type="time" name="departure_time" value="{{ old('departure_time', $leave->departure_time ? \Carbon\Carbon::parse($leave->departure_time)->format('H:i') : '') }}" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-sm font-mono outline-none">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5 font-cairo">تاريخ العودة المتوقع</label>
                    <input type="date" name="expected_return_date" value="{{ old('expected_return_date', $leave->expected_return_date ? $leave->expected_return_date->format('Y-m-d') : '') }}" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-sm font-mono outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5 font-cairo">وقت العودة المتوقع</label>
                    <input type="time" name="expected_return_time" value="{{ old('expected_return_time', $leave->expected_return_time ? \Carbon\Carbon::parse($leave->expected_return_time)->format('H:i') : '') }}" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-sm font-mono outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1.5 font-cairo">سبب الاستئذان</label>
                <textarea name="reason" rows="3" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-navy text-sm font-almarai outline-none resize-none">{{ old('reason', $leave->reason) }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1.5 font-cairo text-red-500">سبب الرفض (إن وجد)</label>
                <textarea name="rejection_reason" rows="2" placeholder="يكتب في حالة رفض الطلب..." class="w-full px-4 py-3 rounded-xl border border-red-200 bg-red-50 text-red-700 text-sm font-almarai outline-none resize-none">{{ old('rejection_reason', $leave->rejection_reason) }}</textarea>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 bg-navy text-white py-3 rounded-xl font-black font-cairo hover:bg-[#083358] transition-all flex items-center justify-center gap-2">
                    <i class="fas fa-save"></i> حفظ التعديلات
                </button>
                <a href="{{ route('administrative.index', ['tab' => 'leaves']) }}"
                   class="px-6 py-3 bg-gray-100 text-gray-600 rounded-xl font-bold font-cairo hover:bg-gray-200 transition-all">
                    إلغاء
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
