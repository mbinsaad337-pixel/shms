@extends('layouts.app')
@section('title', 'مصروفات المراكز')

@section('content')
<div class="space-y-6">

    {{-- Header & Stats --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-navy font-cairo">مصروفات المراكز</h1>
            <p class="text-sm text-gray-400 font-almarai">متابعة الإيجارات، فواتير المياه والكهرباء</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('center-expenses.create') }}" class="btn-primary-shms px-5 py-2.5 inline-flex items-center gap-2">
                <i class="fas fa-plus"></i> تسجيل مصروف جديد
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card-premium p-6">
        <form action="{{ route('center-expenses.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-xs font-bold text-navy font-cairo mb-2">المركز</label>
                <select name="center_id" class="w-full px-4 py-2.5 rounded-xl border border-gray-100 bg-gray-50 font-almarai text-sm focus:ring-2 focus:ring-navy/10 focus:border-navy transition-all">
                    <option value="">الكل</option>
                    @foreach($centers as $c)
                        <option value="{{ $c->id }}" {{ request('center_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-navy font-cairo mb-2">نوع المصروف</label>
                <select name="type" class="w-full px-4 py-2.5 rounded-xl border border-gray-100 bg-gray-50 font-almarai text-sm focus:ring-2 focus:ring-navy/10 focus:border-navy transition-all">
                    <option value="">الكل</option>
                    <option value="rent" {{ request('type') === 'rent' ? 'selected' : '' }}>إيجار سكن</option>
                    <option value="water" {{ request('type') === 'water' ? 'selected' : '' }}>فاتورة ماء</option>
                    <option value="electricity" {{ request('type') === 'electricity' ? 'selected' : '' }}>فاتورة كهرباء</option>
                    <option value="internet" {{ request('type') === 'internet' ? 'selected' : '' }}>فاتورة إنترنت</option>
                    <option value="other" {{ request('type') === 'other' ? 'selected' : '' }}>مصروفات أخرى</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-navy font-cairo mb-2">الحالة</label>
                <select name="status" class="w-full px-4 py-2.5 rounded-xl border border-gray-100 bg-gray-50 font-almarai text-sm focus:ring-2 focus:ring-navy/10 focus:border-navy transition-all">
                    <option value="">الكل</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>غير مدفوع</option>
                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>مدفوع</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-navy font-cairo mb-2">الشهر / السنة</label>
                <div class="flex gap-2">
                    <select name="month" class="w-full px-2 py-2.5 rounded-xl border border-gray-100 bg-gray-50 font-almarai text-sm focus:ring-2 focus:ring-navy/10 focus:border-navy transition-all">
                        <option value="">الشهر</option>
                        @for($i=1; $i<=12; $i++)
                            <option value="{{ $i }}" {{ request('month') == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                    <select name="year" class="w-full px-2 py-2.5 rounded-xl border border-gray-100 bg-gray-50 font-almarai text-sm focus:ring-2 focus:ring-navy/10 focus:border-navy transition-all">
                        <option value="">السنة</option>
                        @for($i=now()->year - 2; $i<=now()->year + 2; $i++)
                            <option value="{{ $i }}" {{ request('year') == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="w-full bg-navy text-white px-4 py-2.5 rounded-xl font-bold font-cairo hover:bg-gold hover:text-navy transition-all">
                    <i class="fas fa-search"></i> بحث
                </button>
                <a href="{{ route('center-expenses.index') }}" class="w-full text-center bg-gray-100 text-gray-600 px-4 py-2.5 rounded-xl font-bold font-cairo hover:bg-gray-200 transition-all">
                    <i class="fas fa-times"></i> مسح
                </a>
            </div>
        </form>
    </div>

    {{-- List --}}
    <div class="card-premium overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="p-4 text-xs font-black text-navy font-cairo">المركز</th>
                        <th class="p-4 text-xs font-black text-navy font-cairo">النوع</th>
                        <th class="p-4 text-xs font-black text-navy font-cairo">الفترة</th>
                        <th class="p-4 text-xs font-black text-navy font-cairo">المبلغ</th>
                        <th class="p-4 text-xs font-black text-navy font-cairo">الاستحقاق / الدفع</th>
                        <th class="p-4 text-xs font-black text-navy font-cairo">الحالة</th>
                        <th class="p-4 text-xs font-black text-navy font-cairo">المرفق</th>
                        <th class="p-4 text-xs font-black text-navy font-cairo w-20 text-center"><i class="fas fa-cog"></i></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($expenses as $expense)
                        <tr class="hover:bg-blue-50/20 transition-colors group">
                            <td class="p-4">
                                <div class="font-bold text-navy font-cairo text-sm">{{ $expense->center->name }}</div>
                            </td>
                            <td class="p-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold font-cairo flex items-center gap-1.5 w-max {{ $expense->type_color }}">
                                    <i class="fas {{ $expense->type_icon }}"></i> {{ $expense->type_label }}
                                </span>
                            </td>
                            <td class="p-4 font-almarai text-sm text-gray-600">
                                {{ str_pad($expense->month, 2, '0', STR_PAD_LEFT) }} / {{ $expense->year }}
                            </td>
                            <td class="p-4">
                                <span class="font-bold text-navy font-mono">{{ number_format($expense->amount, 2) }}</span> <span class="text-xs text-gray-400">ريال يمني</span>
                            </td>
                            <td class="p-4">
                                <div class="text-xs text-gray-500 font-mono"><span class="text-gray-400 font-almarai">مستحق:</span> {{ $expense->due_date->format('Y-m-d') }}</div>
                                @if($expense->payment_date)
                                    <div class="text-xs text-emerald-600 font-mono mt-1"><span class="font-almarai text-emerald-600/70">دفع في:</span> {{ $expense->payment_date->format('Y-m-d') }}</div>
                                @endif
                            </td>
                            <td class="p-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold font-cairo {{ $expense->status_color }}">
                                    {{ $expense->status_label }}
                                </span>
                            </td>
                            <td class="p-4">
                                @if($expense->receipt)
                                    <a href="{{ $expense->receipt_url }}" target="_blank" class="text-blue-500 hover:text-blue-700 text-xl" title="عرض الإيصال">
                                        <i class="fas {{ $expense->receipt_type === 'pdf' ? 'fa-file-pdf text-red-500' : 'fa-image' }}"></i>
                                    </a>
                                @else
                                    <span class="text-gray-300"><i class="fas fa-minus"></i></span>
                                @endif
                            </td>
                            <td class="p-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    {{-- If pending, quick pay button --}}
                                    @if($expense->status === 'pending')
                                        <button type="button" x-data @click="$dispatch('open-pay-modal', { id: {{ $expense->id }}, amount: '{{ number_format($expense->amount, 2) }}' })"
                                                class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 hover:bg-emerald-500 hover:text-white transition-all flex items-center justify-center" title="تسديد">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    @endif

                                    <a href="{{ route('center-expenses.edit', $expense) }}" class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 hover:bg-blue-500 hover:text-white transition-all flex items-center justify-center" title="تعديل">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <form action="{{ route('center-expenses.destroy', $expense) }}" method="POST" data-confirm="هل أنت متأكد من حذف هذا المصروف؟" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-8 h-8 rounded-xl bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-all flex items-center justify-center" title="حذف">
                                            <i class="fas fa-trash-alt text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-10 text-center">
                                <i class="fas fa-clipboard-list text-5xl text-gray-200 mb-4 block"></i>
                                <p class="text-gray-400 font-almarai text-sm">لا توجد مصروفات مسجلة تطابق الفلتر الحالي</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($expenses->hasPages())
            <div class="p-4 border-t border-gray-100">
                {{ $expenses->links() }}
            </div>
        @endif
    </div>

    {{-- Quick Pay Modal --}}
    <div x-data="{ open: false, id: null, amount: '' }" 
         @open-pay-modal.window="open = true; id = $event.detail.id; amount = $event.detail.amount"
         class="relative z-[999]" aria-labelledby="modal-title" role="dialog" aria-modal="true" x-show="open" style="display: none;">
        
        <div x-show="open" x-transition.opacity class="fixed inset-0 bg-navy/40 backdrop-blur-sm transition-opacity z-[999]"></div>
      
        <div class="fixed inset-0 z-[1000] overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div x-show="open" 
                     x-transition:enter="ease-out duration-300" 
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave="ease-in duration-200" 
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     @click.away="open = false"
                     class="relative transform overflow-hidden rounded-3xl bg-white text-right shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100">
                    
                    <form :action="'/center-expenses/' + id + '/mark-paid'" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="bg-white px-6 pb-4 pt-6 sm:p-8 sm:pb-6">
                            <div class="sm:flex sm:items-start gap-4">
                                <div class="mx-auto flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-2xl bg-emerald-100 sm:mx-0">
                                    <i class="fas fa-check-circle text-2xl text-emerald-600"></i>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:text-right w-full">
                                    <h3 class="text-xl font-black text-navy font-cairo mb-2" id="modal-title">تسديد مصروف</h3>
                                    <p class="text-sm text-gray-500 font-almarai mb-6">
                                        تأكيد دفع مبلغ <span class="font-bold text-navy" x-text="amount"></span> ريال يمني.
                                    </p>
                                    
                                    <div class="space-y-4">
                                        <div class="text-right w-full overflow-hidden">
                                            <label class="block text-sm font-bold text-navy font-cairo mb-2">تاريخ الدفع <span class="text-red-500">*</span></label>
                                            <input type="date" name="payment_date" required value="{{ date('Y-m-d') }}"
                                                   class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all font-mono">
                                        </div>
                                        <div class="text-right w-full overflow-hidden">
                                            <label class="block text-sm font-bold text-navy font-cairo mb-2">إرفاق إيصال الدفع <span class="text-gray-400 font-normal">(اختياري)</span></label>
                                            <input type="file" name="receipt" accept=".jpg,.jpeg,.png,.pdf"
                                                   class="w-full max-w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:font-cairo file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 transition-all border border-gray-100 rounded-xl bg-gray-50 overflow-hidden text-ellipsis whitespace-nowrap">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-6 py-4 sm:flex sm:flex-row-reverse sm:px-8 gap-2">
                            <button type="submit" class="w-full inline-flex justify-center rounded-xl bg-emerald-600 px-6 py-3 text-sm font-bold text-white shadow-sm hover:bg-emerald-500 sm:w-auto font-cairo transition-all">
                                تأكيد السداد
                            </button>
                            <button type="button" @click="open = false" class="mt-3 w-full inline-flex justify-center rounded-xl bg-white px-6 py-3 text-sm font-bold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto font-cairo transition-all">
                                إلغاء
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
