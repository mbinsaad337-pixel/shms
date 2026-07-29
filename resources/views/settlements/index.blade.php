@extends('layouts.app')

@section('title', 'التصفيات الشهرية')

@section('content')
    <div class="container mx-auto px-4 py-8 max-w-7xl">
        <div class="mb-8 flex justify-between items-center bg-white p-6 rounded-2xl border-l-8 border-gold shadow-sm">
            <div>
                <h1 class="text-3xl font-black text-navy font-cairo">التصفيات الشهرية (إغلاق الصناديق)</h1>
                <p class="text-gray-400 font-almarai text-sm mt-1">استعراض العمليات المالية للصناديق وإغلاق العهد الشهرية للمراكز</p>
            </div>
            <div class="flex items-center gap-4">
                @if(auth()->user()->hasRole('super-admin'))
                    <form action="{{ route('settlements.index') }}" method="GET" class="flex items-center gap-2">
                        <select name="center_id" onchange="this.form.submit()" 
                            class="px-4 py-2 rounded-xl border border-gray-100 bg-gray-50 focus:bg-white focus:border-gold outline-none text-sm font-cairo">
                            <option value="">كل المراكز</option>
                            @foreach($centers as $center)
                                <option value="{{ $center->id }}" {{ request('center_id') == $center->id ? 'selected' : '' }}>
                                    {{ $center->name }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                @endif
            </div>
        </div>

        @php
            $canSubmit = !$currentSettlementStatus || in_array($currentSettlementStatus->status, ['returned', 'rejected', 'deleted']);
        @endphp

        @if(!auth()->user()->hasRole('super-admin'))
        <div class="flex justify-between items-center mb-8">
            <div></div> @php /* Placeholder to keep header space consistent if needed or remove */ @endphp


            @if($centerId && $canSubmit)
                <div x-data="{ selectedFunds: [] }">
                    <form id="settlementForm" action="{{ route('settlements.store') }}" method="POST"
                        data-confirm="هل أنت متأكد من إغلاق الصناديق المختارة ورفع التصفية المالية لشهر {{ $month }}؟ لا يمكن التراجع بعد الرفع.">
                        @csrf
                        <input type="hidden" name="month" value="{{ $month }}">
                        <input type="hidden" name="year" value="{{ $year }}">
                        
                        <div class="flex items-center gap-3">
                            <button type="submit" :disabled="selectedFunds.length === 0"
                                :class="selectedFunds.length === 0 ? 'opacity-50 cursor-not-allowed bg-gray-400' : 'bg-primary hover:bg-primary-dark'"
                                class="text-white px-6 py-3 rounded-xl flex items-center gap-2 text-sm font-bold font-cairo shadow-sm transition-all focus:ring-2 focus:ring-primary focus:ring-offset-2">
                                <i class="fas fa-file-contract"></i>
                                <span>إغلاق ورفع التصفية للصناديق المختارة ({{ $month }} / {{ $year }})</span>
                            </button>
                            
                            <template x-if="selectedFunds.length > 0">
                                <span class="bg-blue-100 text-blue-700 px-4 py-2 rounded-lg text-xs font-bold font-almarai anim-fade-in">
                                    تم اختيار عدد (<span x-text="selectedFunds.length"></span>) صناديق
                                </span>
                            </template>
                        </div>

                        <div class="mt-8 mb-10">
                            <div class="flex justify-between items-center mb-4">
                                <h2 class="text-xl font-bold text-gray-800 font-cairo flex items-center gap-2">
                                    <i class="fas fa-chart-line text-blue-500"></i> ملخص حركة الصناديق الحالية (شهر {{ $month }})
                                </h2>
                                @php
                                    $allFundIds = json_encode($funds->pluck('id')->toArray());
                                @endphp
                                <div class="flex items-center gap-2 bg-white px-4 py-2 rounded-lg border border-gray-100 shadow-sm cursor-pointer hover:bg-gray-50 transition-colors" @click="if(selectedFunds.length === {{ count($funds) }}) { selectedFunds = [] } else { selectedFunds = {{ $allFundIds }} }">
                                    <input type="checkbox" :checked="selectedFunds.length === {{ count($funds) }}" class="rounded text-primary focus:ring-primary pointer-events-none">
                                    <span class="text-sm font-bold font-cairo text-gray-700">تحديد الكل</span>
                                </div>
                            </div>

                            <div class="space-y-4">
                                @if(count($funds) > 0)
                                    @foreach($funds as $fund)
                                    @php /** @var \App\Models\Fund $fund */ @endphp
                                    @php
                                        // Calculate real-time income and expense for this fund this month
                                        $fundVouchers = $currentMonthVouchers->filter(function ($v) use ($fund) {
                                            return $v->fund_id == $fund->id || ($v->type == 'transfer' && $v->target_fund_id == $fund->id);
                                        })->sortByDesc('created_at');

                                        $totalIncome = $fundVouchers->filter(function ($v) use ($fund) {
                                            return in_array($v->type, ['receipt', 'sales_invoice']) || ($v->type == 'transfer' && $v->target_fund_id == $fund->id);
                                        })->sum('amount');

                                        $totalExpense = $fundVouchers->filter(function ($v) use ($fund) {
                                            return in_array($v->type, ['payment', 'salary', 'purchase_invoice']) || ($v->type == 'transfer' && $v->fund_id == $fund->id);
                                        })->sum('amount');
                                    @endphp

                                    <div class="bg-white rounded-3xl shadow-sm border transition-all duration-300"
                                        :class="selectedFunds.includes('{{ $fund->id }}') ? 'border-primary ring-1 ring-primary' : 'border-gray-100'"
                                        x-data="{ expanded: false }">
                                        
                                        <div class="px-6 py-4 flex flex-wrap lg:flex-nowrap justify-between items-center gap-4">
                                            <!-- Checkbox and Name -->
                                            <div class="flex items-center gap-4 flex-1">
                                                <input type="checkbox" name="fund_ids[]" value="{{ $fund->id }}" x-model="selectedFunds"
                                                    class="w-6 h-6 rounded-lg text-primary focus:ring-primary border-gray-300 transition-all cursor-pointer">
                                                
                                                <div class="flex items-center gap-3 cursor-pointer" @click="expanded = !expanded">
                                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center transition-colors"
                                                        :class="selectedFunds.includes('{{ $fund->id }}') ? 'bg-primary text-white' : 'bg-blue-100 text-blue-600'">
                                                        <i class="fas fa-box-open"></i>
                                                    </div>
                                                    <div>
                                                        <h3 class="text-md font-bold text-gray-800 font-cairo">{{ $fund->name }}</h3>
                                                        <p class="text-[10px] text-gray-500 font-almarai mt-0.5">{{ $fundVouchers->count() }} حركة مالية</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="flex gap-6 items-center" @click="expanded = !expanded">
                                                <div class="hidden md:block text-right">
                                                    <p class="text-[10px] text-green-500 font-almarai">مقبوض (+)</p>
                                                    <p class="text-sm font-bold text-green-600 font-mono">{{ number_format($totalIncome, 2) }}</p>
                                                </div>
                                                <div class="hidden md:block text-right">
                                                    <p class="text-[10px] text-red-500 font-almarai">مصروف (-)</p>
                                                    <p class="text-sm font-bold text-red-600 font-mono">{{ number_format($totalExpense, 2) }}</p>
                                                </div>
                                                <div class="text-right bg-gray-50 px-4 py-2 rounded-xl border border-gray-100 min-w-[120px]">
                                                    <p class="text-[10px] text-gray-500 font-almarai">الرصيد</p>
                                                    <p class="text-md font-bold text-gray-800 font-mono">{{ number_format($fund->balance, 2) }}</p>
                                                </div>
                                                <div class="text-gray-300">
                                                    <i class="fas fa-chevron-down transition-transform duration-300" :class="{'rotate-180': expanded}"></i>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Expanded Transactions (same as before) -->
                                        <div class="border-t border-gray-50" x-show="expanded" x-collapse>
                                            @if($fundVouchers->count() > 0)
                                                <div class="overflow-x-auto">
                                                    <table class="w-full text-right">
                                                        <thead>
                                                            <tr class="bg-gray-50/50 text-gray-500 text-xs">
                                                                <th class="px-6 py-3 font-cairo">رقم السند</th>
                                                                <th class="px-6 py-3 font-cairo">التاريخ</th>
                                                                <th class="px-6 py-3 font-cairo">النوع</th>
                                                                <th class="px-6 py-3 font-cairo">مناولة</th>
                                                                <th class="px-6 py-3 font-cairo">المبلغ (ر.ي)</th>
                                                                <th class="px-6 py-3 font-cairo text-center">المتبقي</th>
                                                                <th class="px-6 py-3 font-cairo">البيان</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="divide-y divide-gray-50">
                                                            @foreach($fundVouchers as $voucher)
                                                                @php
                                                                    $isIncoming = false;
                                                                    if (in_array($voucher->type, ['receipt', 'sales_invoice']) || ($voucher->type == 'transfer' && $voucher->target_fund_id == $fund->id)) {
                                                                        $isIncoming = true;
                                                                    }
                                                                    $types = [
                                                                        'receipt' => ['label' => 'سند قبض', 'color' => 'text-green-600 bg-green-50'],
                                                                        'payment' => ['label' => 'سند صرف', 'color' => 'text-red-600 bg-red-50'],
                                                                        'transfer' => ['label' => 'تحويل', 'color' => 'text-blue-600 bg-blue-50'],
                                                                        'salary' => ['label' => 'رواتب', 'color' => 'text-purple-600 bg-purple-50'],
                                                                        'purchase_invoice' => ['label' => 'مشتريات', 'color' => 'text-orange-600 bg-orange-50']
                                                                    ];
                                                                    $typeInfo = $types[$voucher->type] ?? ['label' => $voucher->type, 'color' => 'text-gray-600 bg-gray-50'];
                                                                @endphp
                                                                <tr class="hover:bg-gray-50 transition-colors">
                                                                    <td class="px-6 py-3 font-mono text-sm"><a
                                                                            href="{{ route('vouchers.show', $voucher) }}" target="_blank"
                                                                            class="text-primary font-bold hover:underline">{{ $voucher->voucher_number }}</a>
                                                                    </td>
                                                                    <td class="px-6 py-3 text-xs text-gray-500 font-mono">
                                                                        {{ $voucher->date->format('Y-m-d') }}</td>
                                                                    <td class="px-6 py-3">
                                                                        <span
                                                                            class="px-2 py-1 rounded-md text-[10px] font-bold font-almarai {{ $typeInfo['color'] }}">{{ $typeInfo['label'] }}</span>
                                                                    </td>
                                                                    <td class="px-6 py-3 text-xs font-almarai text-gray-700">
                                                                        {{ $voucher->type == 'transfer' ? ($isIncoming ? 'من: ' . $voucher->fund->name : 'إلى: ' . $voucher->targetFund->name) : $voucher->payee_or_payer }}
                                                                    </td>
                                                                    <td class="px-6 py-3 font-bold font-mono text-sm {{ $isIncoming ? 'text-green-600' : 'text-red-600' }}"
                                                                        dir="ltr">
                                                                        {{ $isIncoming ? '+' : '-' }}{{ number_format($voucher->amount, 2) }}
                                                                    </td>
                                                                    <td class="px-6 py-3 text-center">
                                                                        @if($voucher->student)
                                                                            <span class="font-bold text-red-600 text-[10px]">{{ number_format($voucher->student->remaining_fees, 2) }}</span>
                                                                        @else
                                                                            <span class="text-gray-300">-</span>
                                                                        @endif
                                                                    </td>
                                                                    <td class="px-6 py-3 text-xs text-gray-500 font-almarai truncate max-w-[150px]"
                                                                        title="{{ $voucher->description }}">
                                                                        {{ $voucher->description }}
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @else
                                                <div class="p-6 text-center">
                                                    <p class="text-sm text-gray-500 font-almarai">لم يتم تسجيل أي عمليات مالية على هذا الصندوق خلال
                                                        هذا الشهر.</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    @endforeach
                                @else
                                    <div class="bg-gray-50 rounded-2xl p-8 text-center border border-gray-100">
                                        <i class="fas fa-box-open text-gray-300 text-3xl mb-3"></i>
                                        <p class="text-gray-500 font-almarai text-sm font-bold">لا توجد صناديق تابعة لهذا المركز حالياً.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            @elseif($currentSettlementStatus)
                <div class="bg-{{ $currentSettlementStatus->status == 'approved' ? 'green' : ($currentSettlementStatus->status == 'confirmed' ? 'indigo' : ($currentSettlementStatus->status == 'returned' ? 'orange' : 'blue')) }}-50 text-{{ $currentSettlementStatus->status == 'approved' ? 'green' : ($currentSettlementStatus->status == 'confirmed' ? 'indigo' : ($currentSettlementStatus->status == 'returned' ? 'orange' : 'blue')) }}-700 px-6 py-3 rounded-xl flex items-center gap-2 text-sm font-bold font-cairo border border-{{ $currentSettlementStatus->status == 'approved' ? 'green' : ($currentSettlementStatus->status == 'confirmed' ? 'indigo' : ($currentSettlementStatus->status == 'returned' ? 'orange' : 'blue')) }}-200 shadow-sm">
                    <i
                        class="fas max-h-[20px] {{ $currentSettlementStatus->status == 'approved' ? 'fa-check-circle' : 'fa-info-circle' }}"></i>
                    <span>
                        @if($currentSettlementStatus->status == 'approved')
                            تم الاعتماد النهائي لتصفية هذا الشهر
                        @elseif($currentSettlementStatus->status == 'confirmed')
                            تم تأكيد التصفية وبانتظار الاعتماد النهائي من المدير العام
                        @elseif($currentSettlementStatus->status == 'returned')
                            تمت إعادة التصفية للمراجعة (بإمكانك إعادة الرفع الآن)
                        @elseif($currentSettlementStatus->status == 'rejected')
                            تم رفض التصفية، يرجى المراجعة والتعديل
                        @else
                            تصفية هذا الشهر قيد المراجعة (بانتظار تأكيد مدير المركز)
                        @endif
                        ({{ $month }} / {{ $year }})
                    </span>
                </div>
            @endif
        </div>
        @endif

        @if(session('success'))
            <div
                class="mb-6 bg-green-50 border border-green-200 text-green-700 px-5 py-4 rounded-2xl flex items-center gap-3 font-almarai shadow-sm font-bold">
                <i class="fas fa-check-circle text-xl"></i>
                <p>{{ session('success') }}</p>
            </div>
        @endif

        @php $errorList = isset($errors) ? $errors->all() : []; @endphp
        @if(count($errorList) > 0)
            <div
                class="mb-6 bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-2xl flex items-center gap-3 font-almarai shadow-sm font-bold">
                <i class="fas fa-exclamation-triangle text-xl"></i>
                <div>
                    @foreach($errorList as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            </div>
        @endif

        @if($centerId && !$canSubmit) {{-- This block is for view-only when submission is not allowed --}}
            <div class="mb-10">
                <h2 class="text-xl font-bold text-gray-800 font-cairo mb-4 flex items-center gap-2">
                    <i class="fas fa-chart-line text-blue-500"></i> ملخص حركة الصناديق (مشاهدة فقط)
                </h2>

                <div class="space-y-4">
                    @if(count($funds) > 0)
                        @foreach($funds as $fund)
                        @php
                            // Calculate real-time income and expense for this fund this month
                            $fundVouchers = $currentMonthVouchers->filter(function ($v) use ($fund) {
                                return $v->fund_id == $fund->id || ($v->type == 'transfer' && $v->target_fund_id == $fund->id);
                            })->sortByDesc('created_at');

                            $totalIncome = $fundVouchers->filter(function ($v) use ($fund) {
                                return in_array($v->type, ['receipt', 'sales_invoice']) || ($v->type == 'transfer' && $v->target_fund_id == $fund->id);
                            })->sum('amount');

                            $totalExpense = $fundVouchers->filter(function ($v) use ($fund) {
                                return in_array($v->type, ['payment', 'salary', 'purchase_invoice']) || ($v->type == 'transfer' && $v->fund_id == $fund->id);
                            })->sum('amount');
                        @endphp

                        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden"
                            x-data="{ expanded: false }">
                            <div class="px-6 py-5 border-b border-gray-50 bg-gray-50/30 flex flex-wrap lg:flex-nowrap justify-between items-center gap-4 cursor-pointer hover:bg-gray-50/80 transition-colors"
                                @click="expanded = !expanded">
                                <div class="flex items-center gap-3 w-full lg:w-auto">
                                    <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center">
                                        <i class="fas fa-box-open text-xl"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-800 font-cairo">{{ $fund->name }}</h3>
                                        <p class="text-xs text-gray-500 font-almarai mt-1">{{ $fundVouchers->count() }} حركة مالية
                                            هذا الشهر</p>
                                    </div>
                                </div>

                                <div class="flex gap-8 justify-end">
                                    <div class="text-center md:text-right">
                                        <p class="text-xs text-green-500 font-almarai mb-1">تم قبضه (+)</p>
                                        <p class="text-lg font-bold text-green-600 font-mono">{{ number_format($totalIncome, 2) }}
                                        </p>
                                    </div>
                                    <div class="text-center md:text-right">
                                        <p class="text-xs text-red-500 font-almarai mb-1">تم صرفه (-)</p>
                                        <p class="text-lg font-bold text-red-600 font-mono">{{ number_format($totalExpense, 2) }}
                                        </p>
                                    </div>
                                    <div class="text-center md:text-right bg-gray-100 px-4 py-2 rounded-xl">
                                        <p class="text-xs text-gray-500 font-almarai mb-1">الرصيد المتاح</p>
                                        <p class="text-lg font-bold text-gray-800 font-mono">{{ number_format($fund->balance, 2) }}
                                        </p>
                                    </div>
                                    <div class="text-gray-400 self-center border-r border-gray-200 pr-6 mr-2">
                                        <i class="fas fa-chevron-down transition-transform duration-300"
                                            :class="{'rotate-180': expanded}"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Dropdown Content -->
                            <div class="p-0 border-t border-gray-100 transition-all duration-300" x-show="expanded" x-collapse>
                                @if($fundVouchers->count() > 0)
                                    <div class="overflow-x-auto">
                                        <table class="w-full text-right">
                                            <thead>
                                                <tr class="bg-gray-50/50 text-gray-500 text-xs">
                                                    <th class="px-6 py-3 font-cairo">رقم السند</th>
                                                    <th class="px-6 py-3 font-cairo">التاريخ</th>
                                                    <th class="px-6 py-3 font-cairo">النوع</th>
                                                    <th class="px-6 py-3 font-cairo">مناولة</th>
                                                    <th class="px-6 py-3 font-cairo">المبلغ (ر.ي)</th>
                                                    <th class="px-6 py-3 font-cairo text-center">المتبقي</th>
                                                    <th class="px-6 py-3 font-cairo">البيان</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-50">
                                                @foreach($fundVouchers as $voucher)
                                                    @php
                                                        $isIncoming = false;
                                                        if (in_array($voucher->type, ['receipt', 'sales_invoice']) || ($voucher->type == 'transfer' && $voucher->target_fund_id == $fund->id)) {
                                                            $isIncoming = true;
                                                        }
                                                        $types = [
                                                            'receipt' => ['label' => 'سند قبض', 'color' => 'text-green-600 bg-green-50'],
                                                            'payment' => ['label' => 'سند صرف', 'color' => 'text-red-600 bg-red-50'],
                                                            'transfer' => ['label' => 'تحويل', 'color' => 'text-blue-600 bg-blue-50'],
                                                            'salary' => ['label' => 'رواتب', 'color' => 'text-purple-600 bg-purple-50'],
                                                            'purchase_invoice' => ['label' => 'مشتريات', 'color' => 'text-orange-600 bg-orange-50']
                                                        ];
                                                        $typeInfo = $types[$voucher->type] ?? ['label' => $voucher->type, 'color' => 'text-gray-600 bg-gray-50'];
                                                    @endphp
                                                    <tr class="hover:bg-gray-50 transition-colors">
                                                        <td class="px-6 py-3 font-mono text-sm"><a
                                                                href="{{ route('vouchers.show', $voucher) }}" target="_blank"
                                                                class="text-primary font-bold hover:underline">{{ $voucher->voucher_number }}</a>
                                                        </td>
                                                        <td class="px-6 py-3 text-xs text-gray-500 font-mono">
                                                            {{ $voucher->date->format('Y-m-d') }}</td>
                                                        <td class="px-6 py-3">
                                                            <span
                                                                class="px-2 py-1 rounded-md text-[10px] font-bold font-almarai {{ $typeInfo['color'] }}">{{ $typeInfo['label'] }}</span>
                                                        </td>
                                                        <td class="px-6 py-3 text-xs font-almarai text-gray-700">
                                                            {{ $voucher->type == 'transfer' ? ($isIncoming ? 'من: ' . $voucher->fund->name : 'إلى: ' . $voucher->targetFund->name) : $voucher->payee_or_payer }}
                                                        </td>
                                                        <td class="px-6 py-3 font-bold font-mono text-sm {{ $isIncoming ? 'text-green-600' : 'text-red-600' }}"
                                                            dir="ltr">
                                                            {{ $isIncoming ? '+' : '-' }}{{ number_format($voucher->amount, 2) }}
                                                        </td>
                                                        <td class="px-6 py-3 text-center">
                                                            @if($voucher->student)
                                                                <span class="font-bold text-red-600 text-[10px]">{{ number_format($voucher->student->remaining_fees, 2) }}</span>
                                                            @else
                                                                <span class="text-gray-300">-</span>
                                                            @endif
                                                        </td>
                                                        <td class="px-6 py-3 text-xs text-gray-500 font-almarai truncate max-w-[150px]"
                                                            title="{{ $voucher->description }}">
                                                            {{ $voucher->description }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="p-6 text-center">
                                        <p class="text-sm text-gray-500 font-almarai">لم يتم تسجيل أي عمليات مالية على هذا الصندوق خلال
                                            هذا الشهر.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="bg-gray-50 rounded-2xl p-8 text-center border border-gray-100">
                            <i class="fas fa-box-open text-gray-300 text-3xl mb-3"></i>
                            <p class="text-gray-500 font-almarai text-sm font-bold">لا توجد صناديق تابعة لهذا المركز حالياً.</p>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- Historical Settlements Table -->
        <h2 class="text-xl font-bold text-gray-800 font-cairo mb-4 flex items-center gap-2">
            <i class="fas fa-history text-gray-400"></i> أرشيف التصفيات المرفوعة
        </h2>

        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-right">
                    <thead>
                        <tr class="bg-gray-50 text-gray-600">
                            <th class="px-6 py-4 font-cairo text-sm">التصفية (الشهر / السنة)</th>
                            <th class="px-6 py-4 font-cairo text-sm">المركز</th>
                            <th class="px-6 py-4 font-cairo text-sm">مُنشئ التصفية</th>
                            <th class="px-6 py-4 font-cairo text-sm">إجمالي المنصرف</th>
                            <th class="px-6 py-4 font-cairo text-sm">الحالة</th>
                            <th class="px-6 py-4 font-cairo text-sm">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @if(count($settlements) > 0)
                            @foreach($settlements as $settlement)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <p class="font-bold text-gray-800 font-cairo">{{ $settlement->month }} /
                                        {{ $settlement->year }}</p>
                                    <p class="text-xs text-gray-400 font-almarai mt-1">
                                        {{ $settlement->submitted_at->format('Y-m-d H:i') }}</p>
                                </td>
                                <td class="px-6 py-4 text-sm font-almarai font-bold text-gray-700">
                                    {{ $settlement->center->name ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $settlement->submitter->name ?? '-' }}
                                </td>
                                <td class="px-6 py-4 font-bold text-red-600 font-mono">
                                    {{ number_format($settlement->total_spent, 2) }} ر.ي
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $statuses = [
                                            'submitted' => ['label' => 'قيد المراجعة', 'color' => 'bg-blue-100 text-blue-700 border border-blue-200'],
                                            'confirmed' => ['label' => 'مؤكدة (بانتظار الاعتماد)', 'color' => 'bg-indigo-100 text-indigo-700 border border-indigo-200'],
                                            'approved' => ['label' => 'معتمدة', 'color' => 'bg-green-100 text-green-700 border border-green-200'],
                                            'returned' => ['label' => 'مُعادة للتعديل', 'color' => 'bg-orange-100 text-orange-700 border border-orange-200'],
                                            'rejected' => ['label' => 'مرفوضة', 'color' => 'bg-red-100 text-red-700 border border-red-200'],
                                            'deleted' => ['label' => 'محذوفة', 'color' => 'bg-gray-100 text-gray-700 border border-gray-200'],
                                        ];
                                        $szInfo = $statuses[$settlement->status] ?? ['label' => $settlement->status, 'color' => 'bg-gray-100'];
                                    @endphp
                                    <span
                                        class="{{ $szInfo['color'] }} px-3 py-1.5 rounded-full text-xs font-bold font-almarai">
                                        {{ $szInfo['label'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-left flex gap-2 justify-end">
                                    <a href="{{ route('settlements.show', $settlement) }}"
                                        class="text-primary hover:text-primary-dark transition-colors bg-blue-50 hover:bg-blue-100 px-4 py-2 rounded-xl flex items-center justify-center w-max gap-2 text-sm font-bold font-cairo">
                                        <i class="fas fa-file-contract"></i> <span>التفاصيل</span>
                                    </a>
                                    
                                    @if(auth()->user()->hasRole('super-admin') || ($settlement->status !== 'approved' && $settlement->submitted_by === auth()->id()))
                                    <form action="{{ route('settlements.destroy', $settlement) }}" method="POST" data-confirm="{{ auth()->user()->hasRole('super-admin') && $settlement->status === 'approved' ? 'تحذير أمني: هذه التصفية معتمدة ومؤرشفة. هل أنت متأكد بصفة استثنائية من رغبتك بحذفها كمدير عام؟!' : 'هل أنت متأكد من حذف هذا الطلب؟ لا يمكن التراجع.' }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-700 transition-colors bg-red-50 hover:bg-red-100 px-3 py-2 rounded-xl flex items-center justify-center" title="حذف التصفية">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-archive text-gray-300 text-4xl mb-3"></i>
                                        <p class="text-gray-500 font-almarai">لا توجد تصفيات مرسلة حالياً في الأرشيف.</p>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                {{ $settlements->links() }}
            </div>
        </div>
    </div>
@endsection
