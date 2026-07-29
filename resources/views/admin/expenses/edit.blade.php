@extends('layouts.app')
@section('title', 'تعديل مصروف')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-black text-navy font-cairo">تعديل مصروف</h1>
        <a href="{{ route('center-expenses.index') }}" class="text-gray-400 hover:text-navy transition-colors font-bold font-cairo flex items-center gap-2">
            <i class="fas fa-arrow-right"></i> عودة للقائمة
        </a>
    </div>

    <form action="{{ route('center-expenses.update', $centerExpense) }}" method="POST" enctype="multipart/form-data" class="card-premium p-8 space-y-6" x-data="{ status: '{{ old('status', $centerExpense->status) }}', type: '{{ old('type', $centerExpense->type) }}' }">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Type --}}
            <div>
                <label class="block text-sm font-bold text-navy font-cairo mb-2">نوع المصروف <span class="text-red-500">*</span></label>
                <select name="type" x-model="type" required class="w-full px-4 py-3.5 rounded-2xl border border-gray-100 bg-gray-50 font-almarai text-sm focus:ring-4 focus:ring-navy/10 focus:border-navy transition-all @error('type') border-red-500 @enderror">
                    <option value="rent">إيجار سكن</option>
                    <option value="water">فاتورة ماء</option>
                    <option value="electricity">فاتورة كهرباء</option>
                    <option value="internet">فاتورة إنترنت</option>
                    <option value="other">مصروفات أخرى</option>
                </select>
                @error('type')<p class="text-red-500 text-xs mt-1 font-almarai">{{ $message }}</p>@enderror
            </div>

            {{-- Center --}}
            <div>
                <label class="block text-sm font-bold text-navy font-cairo mb-2">المركز <span class="text-red-500">*</span></label>
                <select name="center_id" required class="w-full px-4 py-3.5 rounded-2xl border border-gray-100 bg-gray-50 font-almarai text-sm focus:ring-4 focus:ring-navy/10 focus:border-navy transition-all @error('center_id') border-red-500 @enderror">
                    @foreach($centers as $c)
                        {{-- Show all for edit to not break existing data, but indicate if it doesn't have rent --}}
                        <option value="{{ $c->id }}" x-show="type !== 'rent' || {{ $c->has_rent ? 'true' : 'false' }} || {{ $c->id === $centerExpense->center_id ? 'true' : 'false' }}" {{ old('center_id', $centerExpense->center_id) == $c->id ? 'selected' : '' }}>
                            {{ $c->name }} {{ !$c->has_rent ? '(ملك - لا إيجار)' : '' }}
                        </option>
                    @endforeach
                </select>
                @error('center_id')<p class="text-red-500 text-xs mt-1 font-almarai">{{ $message }}</p>@enderror
            </div>

            {{-- Amount --}}
            <div>
                <label class="block text-sm font-bold text-navy font-cairo mb-2">المبلغ (ريال سعودي) <span class="text-red-500">*</span></label>
                <input type="number" step="0.01" name="amount" required value="{{ old('amount', $centerExpense->amount) }}"
                       class="w-full px-4 py-3.5 rounded-2xl border border-gray-100 bg-gray-50 font-mono text-sm focus:ring-4 focus:ring-navy/10 focus:border-navy transition-all @error('amount') border-red-500 @enderror">
                @error('amount')<p class="text-red-500 text-xs mt-1 font-almarai">{{ $message }}</p>@enderror
            </div>

            {{-- Due Date --}}
            <div>
                <label class="block text-sm font-bold text-navy font-cairo mb-2">تاريخ الاستحقاق <span class="text-red-500">*</span></label>
                <input type="date" name="due_date" required value="{{ old('due_date', $centerExpense->due_date->format('Y-m-d')) }}"
                       class="w-full px-4 py-3.5 rounded-2xl border border-gray-100 bg-gray-50 font-mono text-sm focus:ring-4 focus:ring-navy/10 focus:border-navy transition-all @error('due_date') border-red-500 @enderror">
                @error('due_date')<p class="text-red-500 text-xs mt-1 font-almarai">{{ $message }}</p>@enderror
            </div>

            {{-- Month / Year --}}
            <div>
                <label class="block text-sm font-bold text-navy font-cairo mb-2">الشهر / السنة <span class="text-red-500">*</span></label>
                <div class="flex gap-2">
                    <select name="month" required class="w-full px-4 py-3.5 rounded-2xl border border-gray-100 bg-gray-50 font-almarai text-sm focus:ring-4 focus:ring-navy/10 focus:border-navy transition-all @error('month') border-red-500 @enderror">
                        @for($i=1; $i<=12; $i++)
                            <option value="{{ $i }}" {{ old('month', $centerExpense->month) == $i ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $i, 10)) }} ({{ $i }})</option>
                        @endfor
                    </select>
                    <select name="year" required class="w-full px-4 py-3.5 rounded-2xl border border-gray-100 bg-gray-50 font-almarai text-sm focus:ring-4 focus:ring-navy/10 focus:border-navy transition-all @error('year') border-red-500 @enderror">
                        @for($i=now()->year - 1; $i<=now()->year + 2; $i++)
                            <option value="{{ $i }}" {{ old('year', $centerExpense->year) == $i ? 'selected' : '' }}>{{ $i }}</option>
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

            {{-- Payment Date & Receipt (Only if paid) --}}
            <div x-show="status === 'paid'" style="display: none;" class="col-span-1 md:col-span-2">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-100">
                    <div>
                        <label class="block text-sm font-bold text-emerald-700 font-cairo mb-2">تاريخ الدفع الفعلي <span class="text-red-500">*</span></label>
                        <input type="date" name="payment_date" :required="status === 'paid'" value="{{ old('payment_date', $centerExpense->payment_date ? $centerExpense->payment_date->format('Y-m-d') : date('Y-m-d')) }}"
                               class="w-full px-4 py-3 rounded-xl border border-emerald-200 bg-white font-mono text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all @error('payment_date') border-red-500 @enderror">
                        @error('payment_date')<p class="text-red-500 text-xs mt-1 font-almarai">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-emerald-700 font-cairo mb-2">إرفاق إيصال جديد (سيستبدل القديم إن وجد)</label>
                        <input type="file" name="receipt" accept=".jpg,.jpeg,.png,.pdf"
                               class="w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:font-cairo file:bg-emerald-100 file:text-emerald-700 hover:file:bg-emerald-200 transition-all border border-emerald-200 rounded-xl bg-white">
                        
                        @if($centerExpense->receipt)
                            <div class="mt-2 text-xs font-almarai">
                                <a href="{{ $centerExpense->receipt_url }}" target="_blank" class="text-blue-600 hover:underline">
                                    <i class="fas {{ $centerExpense->receipt_type === 'pdf' ? 'fa-file-pdf text-red-500' : 'fa-image text-blue-500' }}"></i> 
                                    عرض المرفق الحالي
                                </a>
                            </div>
                        @endif
                        @error('receipt')<p class="text-red-500 text-xs mt-1 font-almarai">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- Notes --}}
            <div class="col-span-1 md:col-span-2">
                <label class="block text-sm font-bold text-navy font-cairo mb-2">ملاحظات <span class="text-gray-400 font-normal text-xs">(اختياري)</span></label>
                <textarea name="notes" rows="3" class="w-full px-4 py-3.5 rounded-2xl border border-gray-100 bg-gray-50 font-almarai text-sm focus:ring-4 focus:ring-navy/10 focus:border-navy transition-all resize-none">{{ old('notes', $centerExpense->notes) }}</textarea>
            </div>
        </div>

        <div class="pt-4 border-t border-gray-100 flex justify-end gap-3">
            <a href="{{ route('center-expenses.index') }}" class="bg-gray-100 text-gray-600 px-6 py-3 rounded-xl font-bold font-cairo hover:bg-gray-200 transition-all">إلغاء</a>
            <button type="submit" class="btn-primary-shms px-8 py-3 rounded-xl">حفظ التعديلات</button>
        </div>
    </form>
</div>
@endsection
