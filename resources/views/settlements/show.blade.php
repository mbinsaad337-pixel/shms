@extends('layouts.app')

@section('title', 'تفاصيل التصفية الشهرية')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-7xl">
    @include('partials.print_header', (array)['title' => 'تقرير التصفية المالية الشهرية', 'number' => "SET-" . str_pad($settlement->id, 5, '0', STR_PAD_LEFT)])
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-white rounded-2xl shadow-sm border border-gray-100 flex items-center justify-center text-primary">
                <i class="fas fa-file-contract text-xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-800 font-cairo">
                    التصفية المالية لشهر {{ $settlement->month }} / {{ $settlement->year }}
                </h1>
                <div class="flex items-center gap-3 mt-1">
                    <span class="text-gray-500 text-sm font-almarai">المركز: {{ $settlement->center->name }}</span>
                    <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                    <span class="text-gray-500 font-mono text-sm">#{{ str_pad($settlement->id, 5, '0', STR_PAD_LEFT) }}</span>
                    <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                    <span class="bg-{{ $settlement->status == 'approved' ? 'green' : ($settlement->status == 'rejected' ? 'red' : 'blue') }}-100 text-{{ $settlement->status == 'approved' ? 'green' : ($settlement->status == 'rejected' ? 'red' : 'blue') }}-700 px-2 py-0.5 rounded-full text-xs font-bold">
                        @if($settlement->status == 'approved')
                            معتمدة
                        @elseif($settlement->status == 'rejected')
                            مرفوضة
                        @else
                            قيد المراجعة
                        @endif
                    </span>
                </div>
            </div>
        </div>
        
        <div class="flex gap-3">
            @if($settlement->status !== 'approved')
                <form action="{{ route('settlements.recalculate', $settlement) }}" method="POST" class="no-print">
                    @csrf
                    <button type="submit" class="bg-amber-500 text-white hover:bg-amber-600 px-4 py-2 rounded-xl flex items-center gap-2 text-sm font-bold font-cairo shadow-sm transition-all" title="إعادة حساب الأرصدة بناءً على السندات المسجلة">
                        <i class="fas fa-sync-alt"></i>
                        <span>تحديث الحسابات</span>
                    </button>
                </form>
            @endif
            <a href="{{ route('settlements.index') }}" class="bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-xl flex items-center gap-2 text-sm font-bold font-cairo shadow-sm transition-all">
                <i class="fas fa-arrow-right"></i>
                <span>العودة للتصفيات</span>
            </a>
            <button onclick="window.print()" class="bg-primary text-white hover:bg-primary-dark px-4 py-2 rounded-xl flex items-center gap-2 text-sm font-bold font-cairo shadow-sm transition-all">
                <i class="fas fa-print"></i>
                <span>طباعة التقرير</span>
            </button>
        </div>
    </div>

    <!-- Overview Stats -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8">
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm transition-all hover:shadow-md">
            <p class="text-[10px] text-gray-400 font-almarai mb-1">الرصيد الافتتاحي الكلي</p>
            <p class="text-xl font-bold text-gray-800 font-mono">
                {{ number_format($settlement->total_budget, 2) }}
                <span class="text-[10px] text-gray-500 font-cairo">ر.ي</span>
            </p>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm transition-all hover:shadow-md border-r-4 border-r-red-500">
            <p class="text-[10px] text-gray-400 font-almarai mb-1">إجمالي المنصرف (الشهر)</p>
            <p class="text-xl font-bold text-red-600 font-mono">
                {{ number_format($settlement->total_spent, 2) }}
                <span class="text-[10px] text-gray-500 font-cairo">ر.ي</span>
            </p>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm transition-all hover:shadow-md border-r-4 border-r-green-500">
            <p class="text-[10px] text-gray-400 font-almarai mb-1">الرصيد الختامي المتبقي</p>
            <p class="text-xl font-bold text-green-700 font-mono">
                {{ number_format($settlement->total_remaining, 2) }}
                <span class="text-[10px] text-gray-500 font-cairo">ر.ي</span>
            </p>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm transition-all hover:shadow-md">
            <p class="text-[10px] text-gray-400 font-almarai mb-1">عدد الصناديق</p>
            <p class="text-xl font-bold text-gray-800 font-mono">
                {{ $settlement->details->count() }}
            </p>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm transition-all hover:shadow-md">
            <p class="text-[10px] text-gray-400 font-almarai mb-1">تارِيخ الرفع</p>
            <p class="text-sm font-bold text-gray-800 font-mono mt-1">
                {{ $settlement->submitted_at->format('Y-m-d') }}
            </p>
        </div>
    </div>

    <!-- Funds Breakdown & Transactions -->
    <div class="space-y-6">
        <h2 class="text-xl font-bold text-gray-800 font-cairo mb-4 flex items-center gap-2">
            <i class="fas fa-boxes text-orange-500"></i> حركة الصناديق والعمليات التفصيلية
        </h2>

        @foreach($settlement->details as $detail)
            @php
                // Get vouchers related to this fund (either as source or destination for transfers)
                $fundVouchers = $vouchers->filter(function($v) use ($detail) {
                    return $v->fund_id == $detail->fund_id || ($v->type == 'transfer' && $v->target_fund_id == $detail->fund_id);
                })->sortByDesc('created_at');
            @endphp
            
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden" x-data="{ expanded: true }">
                <!-- Fund Header -->
                <div class="px-6 py-5 border-b border-gray-50 bg-gray-50/50 flex flex-wrap lg:flex-nowrap justify-between items-center gap-4 cursor-pointer" @click="expanded = !expanded">
                    <div class="flex items-center gap-3 w-full lg:w-auto">
                        <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center">
                            <i class="fas fa-box-open text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-800 font-cairo">{{ $detail->fund->name }}</h3>
                            <p class="text-xs text-gray-500 font-almarai">{{ $fundVouchers->count() }} حركة مالية مسجلة</p>
                        </div>
                    </div>

                    <div class="flex flex-wrap md:flex-nowrap gap-6 flex-1 lg:justify-end">
                        <div class="text-center md:text-right">
                            <p class="text-[10px] text-gray-400 font-almarai mb-0.5">الرصيد الافتتاحي</p>
                            <p class="text-sm font-bold text-gray-600 font-mono">{{ number_format($detail->opening_balance, 2) }} <span class="text-[10px]">ر.ي</span></p>
                        </div>
                        <div class="text-center md:text-right">
                            <p class="text-[10px] text-green-500 font-almarai mb-0.5">إجمالي المقبوضات</p>
                            <p class="text-sm font-bold text-green-600 font-mono">+{{ number_format($detail->total_income, 2) }} <span class="text-[10px]">ر.ي</span></p>
                        </div>
                        <div class="text-center md:text-right">
                            <p class="text-[10px] text-red-500 font-almarai mb-0.5">إجمالي المصروفات</p>
                            <p class="text-sm font-bold text-red-600 font-mono">-{{ number_format($detail->total_expense, 2) }} <span class="text-[10px]">ر.ي</span></p>
                        </div>
                        <div class="text-center md:text-right bg-gray-100 px-3 py-1 rounded-lg">
                            <p class="text-[10px] text-gray-500 font-almarai mb-0.5">الرصيد الختامي</p>
                            <p class="text-sm font-bold text-gray-800 font-mono">{{ number_format($detail->closing_balance, 2) }} <span class="text-[10px]">ر.ي</span></p>
                        </div>
                    </div>
                    
                    <div class="text-gray-400">
                        <i class="fas fa-chevron-down transition-transform duration-300" :class="{'rotate-180': expanded}"></i>
                    </div>
                </div>

                <!-- Fund Transactions List -->
                <div class="p-0 border-t border-gray-100 transition-all duration-300" x-show="expanded" x-collapse>
                    @if($fundVouchers->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-right">
                                <thead>
                                    <tr class="bg-gray-50/50 text-gray-500">
                                        <th class="px-6 py-3 font-cairo text-xs">رقم السند</th>
                                        <th class="px-6 py-3 font-cairo text-xs">التاريخ</th>
                                        <th class="px-6 py-3 font-cairo text-xs">النوع</th>
                                        <th class="px-6 py-3 font-cairo text-xs">المبلغ (ر.ي)</th>
                                        <th class="px-6 py-3 font-cairo text-xs text-center">المتبقي</th>
                                        <th class="px-6 py-3 font-cairo text-xs">من / إلى</th>
                                        <th class="px-6 py-3 font-cairo text-xs">البيان</th>
                                        <th class="px-6 py-3 font-cairo text-xs">المرفق</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @foreach($fundVouchers as $voucher)
                                        @php
                                            // Determine direction based on if the current fund is the source or destination
                                            $isIncoming = false;
                                            if (in_array($voucher->type, ['receipt', 'sales_invoice'])) {
                                                $isIncoming = true;
                                            } elseif ($voucher->type == 'transfer' && $voucher->target_fund_id == $detail->fund_id) {
                                                $isIncoming = true;
                                            }
                                            
                                            $types = [
                                                'receipt' => ['label' => 'سند قبض', 'color' => 'text-green-600 bg-green-50'],
                                                'payment' => ['label' => 'سند صرف', 'color' => 'text-red-600 bg-red-50'],
                                                'transfer' => ['label' => 'سند تحويل', 'color' => 'text-blue-600 bg-blue-50'],
                                                'salary' => ['label' => 'مسير رواتب', 'color' => 'text-purple-600 bg-purple-50'],
                                                'purchase_invoice' => ['label' => 'فاتورة مشتريات', 'color' => 'text-orange-600 bg-orange-50'],
                                                'sales_invoice' => ['label' => 'فاتورة مبيعات', 'color' => 'text-teal-600 bg-teal-50'],
                                            ];
                                            $typeInfo = $types[$voucher->type] ?? ['label' => $voucher->type, 'color' => 'text-gray-600 bg-gray-50'];
                                        @endphp
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-3 font-mono text-xs">
                                                <a href="{{ route('vouchers.show', $voucher) }}" target="_blank" class="text-blue-600 hover:underline">{{ $voucher->voucher_number }}</a>
                                            </td>
                                            <td class="px-6 py-3 text-xs text-gray-500 font-mono">{{ $voucher->date->format('Y-m-d') }}</td>
                                            <td class="px-6 py-3">
                                                <span class="px-2 py-1 rounded-md text-[10px] font-bold font-almarai {{ $typeInfo['color'] }}">
                                                    {{ $typeInfo['label'] }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-3 font-bold font-mono text-sm {{ $isIncoming ? 'text-green-600' : 'text-red-600' }}" dir="ltr">
                                                {{ $isIncoming ? '+' : '-' }}{{ number_format($voucher->amount, 2) }}
                                            </td>
                                            <td class="px-6 py-3 text-center">
                                                @if($voucher->student)
                                                    <span class="font-bold text-red-600 text-[10px]">{{ number_format($voucher->student->remaining_fees, 2) }}</span>
                                                @else
                                                    <span class="text-gray-300">-</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-3 text-xs font-almarai text-gray-700">
                                                @if($voucher->type == 'transfer')
                                                    @if($isIncoming)
                                                        <span class="text-gray-400">من:</span> {{ $voucher->fund->name }}
                                                    @else
                                                        <span class="text-gray-400">إلى:</span> {{ $voucher->targetFund->name }}
                                                    @endif
                                                @else
                                                    {{ $voucher->payee_or_payer }}
                                                @endif
                                            </td>
                                            <td class="px-6 py-3 text-xs text-gray-500 font-almarai max-w-[200px] truncate" title="{{ $voucher->description }}">
                                                {{ \Illuminate\Support\Str::limit($voucher->description, 40) }}
                                            </td>
                                            <td class="px-6 py-3 text-center">
                                                @if($voucher->attachment)
                                                    <a href="{{ asset('storage/' . $voucher->attachment) }}" target="_blank" class="text-gray-400 hover:text-primary transition-colors">
                                                        <i class="fas fa-paperclip"></i>
                                                    </a>
                                                @else
                                                    <span class="text-gray-300">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-8 text-center flex flex-col items-center">
                            <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center text-gray-300 mb-3">
                                <i class="fas fa-receipt text-xl"></i>
                            </div>
                            <p class="text-sm text-gray-500 font-almarai">لم يتم تسجيل أي عمليات مالية على هذا الصندوق خلال هذا الشهر.</p>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <!-- Approval Actions -->
    <div class="mt-12 bg-white rounded-3xl shadow-sm border border-gray-100 p-8 no-print mb-10">
        <h3 class="text-xl font-bold text-gray-800 font-cairo mb-6 flex items-center gap-2">
            <i class="fas fa-check-double text-primary"></i> إجراءات الاعتماد والمراجعة
        </h3>
        
        <div class="flex flex-wrap gap-4">
            @if(auth()->user()->can('confirm-settlements') && !auth()->user()->hasRole('super-admin') && $settlement->status === 'submitted')
                <form action="{{ route('settlements.confirm', $settlement) }}" method="POST" data-confirm="هل أنت متأكد من تأكيد هذه التصفية وإرسالها للمدير العام؟">
                    @csrf
                    <button type="submit" class="bg-blue-600 text-white hover:bg-blue-700 px-6 py-3 rounded-xl flex items-center gap-2 text-sm font-bold font-cairo shadow-sm transition-all">
                        <i class="fas fa-check"></i>
                        <span>تأكيد التصفية (مدير المركز)</span>
                    </button>
                </form>
            @endif

            @if(auth()->user()->can('approve-settlements') && in_array($settlement->status, ['submitted', 'confirmed']))
                <form action="{{ route('settlements.approve', $settlement) }}" method="POST" data-confirm="هل أنت متأكد من الاعتماد النهائي لهذه التصفية؟ سيتم تصفير جميع أرصدة الصناديق المتضمنة.">
                    @csrf
                    <button type="submit" class="bg-green-600 text-white hover:bg-green-700 px-6 py-3 rounded-xl flex items-center gap-2 text-sm font-bold font-cairo shadow-sm transition-all">
                        <i class="fas fa-stamp"></i>
                        <span>اعتماد نهائي (المدير العام)</span>
                    </button>
                </form>
            @endif

            @if((auth()->user()->can('confirm-settlements') || auth()->user()->can('approve-settlements')) && in_array($settlement->status, ['submitted', 'confirmed']))
                <form action="{{ route('settlements.reject', $settlement) }}" method="POST" data-confirm="هل أنت متأكد من رفض هذه التصفية وإعادتها للمراجعة؟">
                    @csrf
                    <button type="submit" class="bg-red-500 text-white hover:bg-red-600 px-6 py-3 rounded-xl flex items-center gap-2 text-sm font-bold font-cairo shadow-sm transition-all">
                        <i class="fas fa-times"></i>
                        <span>رفض التصفية</span>
                    </button>
                </form>
            @endif

            @if(auth()->user()->hasRole('super-admin'))
                <form action="{{ route('settlements.destroy', $settlement) }}" method="POST" data-confirm="{{ $settlement->status === 'approved' ? 'تحذير أمني: هذه التصفية معتمدة ومؤرشفة. هل أنت متأكد بصفة استثنائية من رغبتك بحذفها كمدير عام؟!' : 'هل أنت متأكد من حذف هذه التصفية؟ لا يمكن التراجع عن هذا الإجراء.' }}" class="mr-auto">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="border-2 border-red-200 text-red-600 bg-red-50 hover:bg-red-600 hover:text-white px-6 py-3 rounded-xl flex items-center gap-2 text-sm font-bold font-cairo shadow-sm transition-all">
                        <i class="fas fa-trash-alt"></i>
                        <span>حذف التصفية نهائياً</span>
                    </button>
                </form>
            @endif
        </div>

        @if($settlement->status === 'approved')
            <div class="bg-green-50 border border-green-100 rounded-2xl p-6 flex flex-col md:flex-row items-center gap-4">
                <div class="w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center text-2xl border-4 border-white shadow-sm">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div>
                    <h4 class="text-lg font-bold text-green-800 font-cairo">تم الاعتماد النهائي لهذه التصفية</h4>
                    <p class="text-sm text-green-600 font-almarai mt-1">
                        بواسطة: {{ $settlement->approver->name ?? '-' }} | بتارِيخ: {{ $settlement->approved_at ? $settlement->approved_at->format('Y-m-d H:i') : '-' }}
                    </p>
                </div>
            </div>
        @elseif($settlement->status === 'confirmed')
            <div class="bg-blue-50 border border-blue-100 rounded-2xl p-6 flex items-center gap-4">
                <i class="fas fa-info-circle text-blue-500 text-2xl"></i>
                <div class="text-blue-700 font-almarai text-sm">بانتظار الاعتماد النهائي من المدير العام.</div>
            </div>
        @endif
    </div>

</div>

<!-- Print Styles -->
<style>
    @media print {
        body { background: white !important; }
        .sidebar, .topbar, header, nav, footer, button, a.btn-primary, .no-print { display: none !important; }
        .container { max-width: 100% !important; padding: 0 !important; margin: 0 !important; }
        .shadow-sm { box-shadow: none !important; border-color: #e5e7eb !important; }
        .bg-white { background: white !important; }
        .overflow-x-auto { overflow: visible !important; }
        * { print-color-adjust: exact !important; -webkit-print-color-adjust: exact !important; }
        div[x-show="expanded"] { display: block !important; }
        .rotate-180 { display: none !important; }
    }
</style>
@include('partials.print_footer')
@endsection
