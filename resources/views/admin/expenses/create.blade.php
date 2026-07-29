@extends('layouts.app')
@section('title', 'تسجيل مصروف جديد')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-black text-navy font-cairo">تسجيل مصروف جديد</h1>
        <a href="{{ route('center-expenses.index') }}" class="text-gray-400 hover:text-navy transition-colors font-bold font-cairo flex items-center gap-2">
            <i class="fas fa-arrow-right"></i> عودة للقائمة
        </a>
    </div>

    <form action="{{ route('center-expenses.store') }}" method="POST" enctype="multipart/form-data" class="card-premium p-8 space-y-6" x-data="{ status: 'pending', type: 'rent' }">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Type --}}
            <div>
                <label class="block text-sm font-bold text-navy font-cairo mb-2">نوع المصروف <span class="text-red-500">*</span></label>
                <select name="type" x-model="type" required class="w-full px-4 py-3.5 rounded-2xl border border-gray-100 bg-gray-50 font-almarai text-sm focus:ring-4 focus:ring-navy/10 focus:border-navy transition-all @error('type') border-red-500 @enderror">
                    <option value="rent" {{ old('type', 'rent') === 'rent' ? 'selected' : '' }}>إيجار سكن</option>
                    <option value="water" {{ old('type') === 'water' ? 'selected' : '' }}>فاتورة ماء</option>
                    <option value="electricity" {{ old('type') === 'electricity' ? 'selected' : '' }}>فاتورة كهرباء</option>
                    <option value="internet" {{ old('type') === 'internet' ? 'selected' : '' }}>فاتورة إنترنت</option>
                    <option value="other" {{ old('type') === 'other' ? 'selected' : '' }}>مصروفات أخرى</option>
                </select>
                @error('type')<p class="text-red-500 text-xs mt-1 font-almarai">{{ $message }}</p>@enderror
            </div>

            {{-- Center --}}
            <div>
                <label class="block text-sm font-bold text-navy font-cairo mb-2">المركز <span class="text-red-500">*</span></label>
                <select name="center_id" required class="w-full px-4 py-3.5 rounded-2xl border border-gray-100 bg-gray-50 font-almarai text-sm focus:ring-4 focus:ring-navy/10 focus:border-navy transition-all @error('center_id') border-red-500 @enderror">
                    <option value="">اختر المركز...</option>
                    @foreach($centers as $c)
                        {{-- Only show a center for rent if it actually has rent --}}
                        <option value="{{ $c->id }}" x-show="type !== 'rent' || {{ $c->has_rent ? 'true' : 'false' }}" {{ old('center_id') == $c->id ? 'selected' : '' }}>
                            {{ $c->name }} {{ !$c->has_rent ? '(ملك - لا إيجار)' : '' }}
                        </option>
                    @endforeach
                </select>
                @error('center_id')<p class="text-red-500 text-xs mt-1 font-almarai">{{ $message }}</p>@enderror
                <p x-show="type === 'rent'" class="text-[10px] text-gray-400 mt-1 font-almarai">تظهر فقط المراكز المستأجرة (في الإعدادات)</p>
            </div>

            {{-- Amount --}}
            <div>
                <label class="block text-sm font-bold text-navy font-cairo mb-2">المبلغ (ريال سعودي) <span class="text-red-500">*</span></label>
                <input type="number" step="0.01" name="amount" required value="{{ old('amount') }}" placeholder="0.00"
                       class="w-full px-4 py-3.5 rounded-2xl border border-gray-100 bg-gray-50 font-mono text-sm focus:ring-4 focus:ring-navy/10 focus:border-navy transition-all @error('amount') border-red-500 @enderror">
                @error('amount')<p class="text-red-500 text-xs mt-1 font-almarai">{{ $message }}</p>@enderror
            </div>

            {{-- Due Date --}}
            <div>
                <label class="block text-sm font-bold text-navy font-cairo mb-2">تاريخ الاستحقاق <span class="text-red-500">*</span></label>
                <input type="date" name="due_date" required value="{{ old('due_date', date('Y-m-d')) }}"
                       class="w-full px-4 py-3.5 rounded-2xl border border-gray-100 bg-gray-50 font-mono text-sm focus:ring-4 focus:ring-navy/10 focus:border-navy transition-all @error('due_date') border-red-500 @enderror">
                @error('due_date')<p class="text-red-500 text-xs mt-1 font-almarai">{{ $message }}</p>@enderror
            </div>

            {{-- Month / Year --}}
            <div>
                <label class="block text-sm font-bold text-navy font-cairo mb-2">الشهر / السنة <span class="text-red-500">*</span></label>
                <div class="flex gap-2">
                    <select name="month" required class="w-full px-4 py-3.5 rounded-2xl border border-gray-100 bg-gray-50 font-almarai text-sm focus:ring-4 focus:ring-navy/10 focus:border-navy transition-all @error('month') border-red-500 @enderror">
                        @for($i=1; $i<=12; $i++)
                            <option value="{{ $i }}" {{ old('month', date('n')) == $i ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $i, 10)) }} ({{ $i }})</option>
                        @endfor
                    </select>
                    <select name="year" required class="w-full px-4 py-3.5 rounded-2xl border border-gray-100 bg-gray-50 font-almarai text-sm focus:ring-4 focus:ring-navy/10 focus:border-navy transition-all @error('year') border-red-500 @enderror">
                        @for($i=now()->year - 1; $i<=now()->year + 2; $i++)
                            <option value="{{ $i }}" {{ old('year', date('Y')) == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            {{-- Status --}}
            <div>
                <label class="block text-sm font-bold text-navy font-cairo mb-2">الحالة <span class="text-red-500">*</span></label>
                <select name="status" x-model="status" required class="w-full px-4 py-3.5 rounded-2xl border border-gray-100 bg-gray-50 font-almarai text-sm focus:ring-4 focus:ring-navy/10 focus:border-navy transition-all @error('status') border-red-500 @enderror">
                    <option value="pending">غير مدفوع (مستحق)</option>
                    <option value="paid">تم الدفع</option>
                </select>
            </div>

            {{-- Payment Date (Only if paid) --}}
            <div x-show="status === 'paid'" style="display: none;" class="col-span-1 md:col-span-2">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-100">
                    <div>
                        <label class="block text-sm font-bold text-emerald-700 font-cairo mb-2">تاريخ الدفع الفعلي <span class="text-red-500">*</span></label>
                        <input type="date" name="payment_date" :required="status === 'paid'" value="{{ old('payment_date', date('Y-m-d')) }}"
                               class="w-full px-4 py-3 rounded-xl border border-emerald-200 bg-white font-mono text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all @error('payment_date') border-red-500 @enderror">
                        @error('payment_date')<p class="text-red-500 text-xs mt-1 font-almarai">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-emerald-700 font-cairo mb-2">وصل السداد (إيصال)</label>
                        <input type="file" name="receipt" accept=".jpg,.jpeg,.png,.pdf"
                               class="w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:font-cairo file:bg-emerald-100 file:text-emerald-700 hover:file:bg-emerald-200 transition-all border border-emerald-200 rounded-xl bg-white">
                        <p class="text-[10px] text-gray-500 mt-1 font-almarai">صور (JPG/PNG) أو ملفات PDF (حتى 10MB)</p>
                        @error('receipt')<p class="text-red-500 text-xs mt-1 font-almarai">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- Notes --}}
            <div class="col-span-1 md:col-span-2">
                <label class="block text-sm font-bold text-navy font-cairo mb-2">ملاحظات <span class="text-gray-400 font-normal text-xs">(اختياري)</span></label>
                <textarea name="notes" rows="3" class="w-full px-4 py-3.5 rounded-2xl border border-gray-100 bg-gray-50 font-almarai text-sm focus:ring-4 focus:ring-navy/10 focus:border-navy transition-all resize-none">{{ old('notes') }}</textarea>
            </div>
        </div>

        <div class="pt-4 border-t border-gray-100 flex justify-end gap-3">
            <a href="{{ route('center-expenses.index') }}" class="bg-gray-100 text-gray-600 px-6 py-3 rounded-xl font-bold font-cairo hover:bg-gray-200 transition-all">إلغاء</a>
            <button type="submit" class="btn-primary-shms px-8 py-3 rounded-xl">حفظ المصروف</button>
        </div>
    </form>
</div>
@endsection
