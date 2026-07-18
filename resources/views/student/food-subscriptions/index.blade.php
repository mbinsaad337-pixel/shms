@extends('layouts.app')
@section('title', 'اشتراكات التغذية')

@section('content')
<div class="p-6 max-w-7xl mx-auto container mt-6">
    <div class="mb-8 flex justify-between items-center bg-white p-6 rounded-2xl border-l-8 border-gold shadow-sm">
        <div>
            <h1 class="text-3xl font-black text-navy font-cairo">اشتراكات التغذية الخاصة بي</h1>
            <p class="text-gray-400 font-almarai text-sm mt-1">تتبع وإدارة طلبات اشتراكك في الوجبات</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-700 font-almarai flex items-center gap-3">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-100 text-red-700 font-almarai flex items-center gap-3">
            <i class="fas fa-exclamation-circle"></i>
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Application Form -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 sticky top-6">
                <h3 class="font-bold text-navy font-cairo mb-4 flex items-center gap-2">
                    <i class="fas fa-utensils text-gold"></i> تقديم طلب اشتراك جديد
                </h3>

                @if($hasActiveOrPending)
                    <div class="bg-amber-50 rounded-xl p-4 border border-amber-100">
                        <p class="text-amber-800 text-sm font-bold font-cairo flex gap-2">
                            <i class="fas fa-info-circle mt-0.5"></i>
                            لك طلب اشتراك حالي (فعال أو قيد المراجعة). لا يمكنك تقديم طلب جديد حتى ينتهي اشتراكك الحالي.
                        </p>
                    </div>
                @elseif($availableBudgets->isEmpty())
                    <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 text-center">
                        <i class="fas fa-calendar-times text-gray-400 text-3xl mb-2"></i>
                        <p class="text-gray-600 text-sm font-cairo font-bold">عذراً، لا توجد ميزانيات معتمدة متاحة للاشتراك في الوقت الحالي.</p>
                    </div>
                @else
                    <form action="{{ route('student.food-subscriptions.store') }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-600 font-cairo mb-2">الشهر المتاح <span class="text-red-500">*</span></label>
                                <select name="budget_id" id="budgetSelect" required onchange="updateDates()"
                                    class="w-full border border-gray-200 rounded-xl px-4 py-3 font-cairo text-sm focus:ring-2 focus:ring-navy focus:border-navy transition-all">
                                    <option value="">— اختر الميزانية —</option>
                                    @foreach($availableBudgets as $budget)
                                        <option value="{{ $budget->id }}" 
                                                data-year="{{ $budget->year }}" 
                                                data-month="{{ $budget->month }}"
                                                data-cost="{{ $budget->cost_per_student }}"
                                                data-rate="{{ $budget->daily_rate }}"
                                                data-last-payment="{{ $budget->last_payment_date ? $budget->last_payment_date->format('Y-m-d') : 'غير محدد' }}">
                                            {{ $budget->month_name }} {{ $budget->year }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-600 font-cairo mb-2">نوع الاشتراك <span class="text-red-500">*</span></label>
                                <select name="subscription_type" id="subscriptionType" required onchange="calculatePreview()"
                                    class="w-full border border-gray-200 rounded-xl px-4 py-3 font-cairo text-sm focus:ring-2 focus:ring-navy focus:border-navy transition-all">
                                    <option value="monthly">شهري (كامل)</option>
                                    <option value="semi_monthly">نصف شهري</option>
                                    <option value="daily">يومي (حسب الأيام المختارة)</option>
                                </select>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-gray-600 font-cairo mb-2">من تاريخ <span class="text-red-500">*</span></label>
                                    <input type="date" name="start_date" id="startDate" required onchange="calculatePreview()"
                                        class="w-full border border-gray-200 rounded-xl px-3 py-3 font-mono text-sm focus:ring-2 focus:ring-navy transition-all">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-600 font-cairo mb-2">إلى تاريخ <span class="text-red-500">*</span></label>
                                    <input type="date" name="end_date" id="endDate" required onchange="calculatePreview()"
                                        class="w-full border border-gray-200 rounded-xl px-3 py-3 font-mono text-sm focus:ring-2 focus:ring-navy transition-all">
                                </div>
                            </div>

                            <div id="subscriptionPreview" class="hidden mt-4 bg-navy/5 border border-navy/10 rounded-xl p-4">
                                <h4 class="font-bold text-navy font-cairo mb-2 text-sm border-b border-navy/10 pb-2">تفاصيل الرسوم المالية</h4>
                                <ul class="space-y-3 text-sm font-cairo">
                                    <li class="flex justify-between items-center">
                                        <span class="text-gray-600 flex items-center gap-2"><i class="fas fa-calendar-times text-red-400"></i> آخر موعد للدفع:</span>
                                        <span class="font-bold text-red-600 font-mono" id="previewLastPayment">غير محدد</span>
                                    </li>
                                    <li class="flex justify-between items-center">
                                        <span class="text-gray-600 flex items-center gap-2"><i class="fas fa-tags text-gold"></i> قيمة الاشتراك المطلوبة:</span>
                                        <span class="font-black text-navy text-lg font-mono"><span id="previewTotal">0</span> <span class="text-xs text-gray-500">ر.ي</span></span>
                                    </li>
                                </ul>
                            </div>

                            <div class="pt-4 border-t border-gray-100">
                                <button type="submit" class="w-full bg-navy text-white rounded-xl py-3 font-cairo font-bold hover:bg-navy/90 hover:shadow-lg transition-all flex items-center justify-center gap-2 group">
                                    <span>إرسال طلب الاشتراك</span>
                                    <i class="fas fa-paper-plane group-hover:-translate-y-1 group-hover:translate-x-1 transition-transform rtl:group-hover:-translate-x-1"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                @endif
            </div>
        </div>

        <!-- Subscriptions History -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-50 flex items-center justify-between">
                    <h3 class="font-bold text-navy font-cairo flex items-center gap-2">
                        <i class="fas fa-history text-gold"></i> سجل اشتراكاتي
                    </h3>
                </div>
                
                @if($subscriptions->isEmpty())
                    <div class="p-10 text-center text-gray-400">
                        <i class="fas fa-folder-open text-4xl mb-3"></i>
                        <p class="font-cairo">لا توجد اشتراكات سابقة لك</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-right">
                            <thead class="bg-gray-50/50">
                                <tr>
                                    <th class="px-6 py-4 text-xs font-bold text-gray-500 font-cairo">الشهر/الميزانية</th>
                                    <th class="px-6 py-4 text-xs font-bold text-gray-500 font-cairo text-center">النوع</th>
                                    <th class="px-6 py-4 text-xs font-bold text-gray-500 font-cairo text-center">المدة</th>
                                    <th class="px-6 py-4 text-xs font-bold text-gray-500 font-cairo text-center">التكلفة</th>
                                    <th class="px-6 py-4 text-xs font-bold text-gray-500 font-cairo text-center">الحالة</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($subscriptions as $sub)
                                    <tr class="hover:bg-gray-50/30 transition-colors">
                                        <td class="px-6 py-4">
                                            <p class="font-bold text-gray-800 font-cairo text-sm">{{ $sub->budget->month_name ?? '-' }} {{ $sub->budget->year ?? '' }}</p>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="text-xs font-bold px-3 py-1 bg-purple-50 text-purple-700 rounded-lg font-cairo">{{ $sub->getTypeLabel() }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-center font-mono text-xs text-gray-600">
                                            {{ $sub->start_date->format('Y-m-d') }}<br>
                                            <span class="text-gray-400 text-[10px]">إلى</span><br>
                                            {{ $sub->end_date->format('Y-m-d') }}
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <p class="font-bold text-navy font-mono">{{ number_format($sub->total_due, 2) }} <span class="text-[10px] text-gray-400 font-cairo">ر.ي</span></p>
                                            @if($sub->total_paid > 0)
                                                <p class="text-[10px] text-emerald-600 font-bold font-almarai mt-1">مدفوع: {{ number_format($sub->total_paid, 2) }}</p>
                                            @endif
                                            @if($sub->total_due - $sub->total_paid > 0)
                                                <p class="text-[10px] text-rose-600 font-bold font-almarai mt-1">متبقي: {{ number_format($sub->total_due - $sub->total_paid, 2) }}</p>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @php
                                                $statusClasses = [
                                                    'pending' => 'bg-amber-100 text-amber-700 border-amber-200',
                                                    'active' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                                    'suspended' => 'bg-red-100 text-red-700 border-red-200',
                                                    'rejected' => 'bg-rose-100 text-rose-700 border-rose-200',
                                                    'expired' => 'bg-gray-100 text-gray-600 border-gray-200',
                                                    'cancelled' => 'bg-gray-100 text-gray-500 border-gray-200'
                                                ];
                                                $class = $statusClasses[$sub->status] ?? 'bg-gray-100 text-gray-600 border-gray-200';
                                            @endphp
                                            <span class="px-3 py-1 rounded-xl text-xs font-bold border {{ $class }} whitespace-nowrap">
                                                {{ $sub->getStatusLabel() }}
                                            </span>
                                            
                                            @if($sub->status === 'rejected' && $sub->rejection_reason)
                                                <p class="text-[10px] font-bold text-rose-500 mt-2 font-almarai max-w-[150px] mx-auto leading-relaxed border-t border-rose-100 pt-1">
                                                    السبب: {{ $sub->rejection_reason }}
                                                </p>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($subscriptions->hasPages())
                        <div class="p-4 border-t border-gray-50">
                            {{ $subscriptions->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <!-- Vouchers History -->
    <div class="mt-8 bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-50 flex items-center justify-between">
            <h3 class="font-bold text-navy font-cairo flex items-center gap-2">
                <i class="fas fa-receipt text-gold"></i> سندات القبض الخاصة بي
            </h3>
        </div>
        
        @if($vouchers->isEmpty())
            <div class="p-10 text-center text-gray-400">
                <i class="fas fa-file-invoice-dollar text-4xl mb-3 block"></i>
                <p class="font-cairo">لا توجد سندات قبض مسجلة لك حتى الآن</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-right">
                    <thead class="bg-gray-50/50">
                        <tr>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 font-cairo">رقم السند</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 font-cairo">التاريخ</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 font-cairo text-center">المبلغ</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 font-cairo w-1/2">البيان</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($vouchers as $voucher)
                            <tr class="hover:bg-gray-50/30 transition-colors">
                                <td class="px-6 py-4 font-mono font-bold text-navy text-sm">
                                    {{ $voucher->voucher_number }}
                                </td>
                                <td class="px-6 py-4 font-mono text-gray-600 text-sm">
                                    {{ $voucher->voucher_date->format('Y-m-d') }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="font-bold text-emerald-600 font-mono">{{ number_format($voucher->amount, 2) }}</span>
                                    <span class="text-[10px] text-gray-400 font-cairo">ر.ي</span>
                                </td>
                                <td class="px-6 py-4 text-gray-700 text-sm font-almarai leading-relaxed">
                                    {{ $voucher->description ?? 'تسديد رسوم' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function updateDates() {
    const select = document.getElementById('budgetSelect');
    if(select.selectedIndex === 0 || select.value === '') return;
    
    const option = select.options[select.selectedIndex];
    const year = option.getAttribute('data-year');
    const month = option.getAttribute('data-month');
    
    if(year && month) {
        // Auto fill start date as 1st of month
        const paddedMonth = month.padStart(2, '0');
        const startStr = `${year}-${paddedMonth}-01`;
        
        // Auto fill end date as last day of month
        const lastDay = new Date(year, month, 0).getDate();
        const endStr = `${year}-${paddedMonth}-${lastDay}`;
        
        document.getElementById('startDate').value = startStr;
        document.getElementById('endDate').value = endStr;
    }
    
    // Call calculate Preview to update the panel
    calculatePreview();
}

function calculatePreview() {
    const select = document.getElementById('budgetSelect');
    const previewPanel = document.getElementById('subscriptionPreview');
    
    if (select.selectedIndex === 0 || select.value === '') {
        previewPanel.classList.add('hidden');
        return;
    }
    
    previewPanel.classList.remove('hidden');
    
    const option = select.options[select.selectedIndex];
    const costPerStudent = parseFloat(option.getAttribute('data-cost')) || 0;
    const dailyRate = parseFloat(option.getAttribute('data-rate')) || 0;
    const lastPayment = option.getAttribute('data-last-payment') || 'غير محدد';
    
    document.getElementById('previewLastPayment').textContent = lastPayment;
    
    const type = document.getElementById('subscriptionType').value;
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    
    let totalDue = 0;
    
    if (type === 'monthly') {
        totalDue = costPerStudent;
    } else if (type === 'semi_monthly') {
        totalDue = costPerStudent / 2;
    } else if (type === 'daily' && startDate && endDate) {
        const start = new Date(startDate);
        const end = new Date(endDate);
        if (end >= start) {
            const diffTime = Math.abs(end - start);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
            totalDue = diffDays * dailyRate;
        }
    }
    
    document.getElementById('previewTotal').textContent = new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(totalDue);
}

</script>
@endpush
@endsection
