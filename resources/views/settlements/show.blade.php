@extends('layouts.app')

@php
    /** @var \App\Models\Student $student */
    $preview = $preview ?? false;
    $previewArchive = $previewArchive ?? null;
@endphp

@section('title',$preview ? 'معاينة تفاصيل التصفية الشهرية' :  'تفاصيل التصفية الشهرية')

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
                    <span class="text-gray-500   text-sm">#{{ str_pad($settlement->id, 5, '0', STR_PAD_LEFT) }}</span>
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
        @if ($preview && $previewArchive)
                <div class="mb-6 bg-gradient-to-r from-amber-50 to-orange-50 border-2 border-amber-300 rounded-2xl p-5 flex flex-col sm:flex-row items-center justify-between gap-4 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center">
                            <i class="fas fa-archive text-amber-600 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-amber-800 font-cairo">وضع المعاينة — سجل مؤرشف</h3>
                            <p class="text-xs text-amber-600 font-almarai">
                                بيانات هذا السند مؤرشفة من السنة {{ $previewArchive->year }}
                                &bull; الأرشيف #{{ str_pad($previewArchive->id, 6, '0', STR_PAD_LEFT) }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('annual-rollover.export-archive-pdf', $previewArchive) }}" target="_blank"
                            class="px-4 py-2 bg-red-50 hover:bg-red-100 text-red-700 rounded-xl text-xs font-bold font-cairo flex items-center gap-2 transition-all border border-red-200">
                            <i class="fas fa-file-pdf"></i> تصدير أرشيف PDF
                        </a>
                        <a href="{{ route('annual-rollover.index', $previewArchive) }}"
                            class="px-4 py-2 bg-white hover:bg-gray-50 text-navy rounded-xl text-xs font-bold font-cairo flex items-center gap-2 transition-all border border-gray-200">
                            <i class="fas fa-arrow-right"></i> العودة للأرشيف
                        </a>
                    </div>
                </div>
                @elseif (!$preview)
                  <div class="flex gap-3">
          
            <a href="{{ route('settlements.index') }}" class="bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-xl flex items-center gap-2 text-sm font-bold font-cairo shadow-sm transition-all">
                <i class="fas fa-arrow-right"></i>
                <span>العودة للتصفيات</span>
            </a>
            <a href="{{ route('settlements.export-pdf', $settlement) }}" class="bg-primary text-white hover:bg-primary-dark px-4 py-2 rounded-xl flex items-center gap-2 text-sm font-bold font-cairo shadow-sm transition-all">
                <i class="fas fa-file-pdf"></i>
                <span>تصدير PDF</span>
            </a>
        </div>
                
            @endif
        </div>
      
    </div>

    <!-- Overview Stats -->
    @php
        $currencyBreakdown = [];
        foreach (\App\Models\Fund::CURRENCIES as $code => $label) {
            $currencyBreakdown[$code] = [
                'label' => $label,
                'symbol' => \App\Models\Fund::CURRENCY_SYMBOLS[$code],
                'opening' => 0.0,
                'spent' => 0.0,
                'remaining' => 0.0,
            ];
        }
        foreach ($settlement->details as $d) {
            $code = $d->fund->currency ?? 'YER';
            if (!isset($currencyBreakdown[$code])) {
                $currencyBreakdown[$code] = [
                    'label' => \App\Models\Fund::CURRENCIES[$code] ?? $code,
                    'symbol' => \App\Models\Fund::CURRENCY_SYMBOLS[$code] ?? $code,
                    'opening' => 0.0,
                    'spent' => 0.0,
                    'remaining' => 0.0,
                ];
            }
            $currencyBreakdown[$code]['opening'] += (float) $d->opening_balance;
            $currencyBreakdown[$code]['spent'] += (float) $d->total_expense;
            $currencyBreakdown[$code]['remaining'] += (float) $d->closing_balance;
        }
    @endphp
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8">
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm transition-all hover:shadow-md">
            <p class="text-[10px] text-gray-400 font-almarai mb-3">الرصيد الافتتاحي الكلي</p>
            <div class="space-y-2">
                @foreach($currencyBreakdown as $row)
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-cairo text-gray-400">{{ $row['symbol'] }}</span>
                        <span class="text-base font-bold text-gray-800">{{ number_format($row['opening'], 2) }}</span>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm transition-all hover:shadow-md border-r-4 border-r-red-500">
            <p class="text-[10px] text-gray-400 font-almarai mb-3">إجمالي المنصرف (الشهر)</p>
            <div class="space-y-2">
                @foreach($currencyBreakdown as $row)
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-cairo text-gray-400">{{ $row['symbol'] }}</span>
                        <span class="text-base font-bold text-red-600">{{ number_format($row['spent'], 2) }}</span>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm transition-all hover:shadow-md border-r-4 border-r-green-500">
            <p class="text-[10px] text-gray-400 font-almarai mb-3">الرصيد الختامي المتبقي</p>
            <div class="space-y-2">
                @foreach($currencyBreakdown as $row)
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-cairo text-gray-400">{{ $row['symbol'] }}</span>
                        <span class="text-base font-bold text-green-700">{{ number_format($row['remaining'], 2) }}</span>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm transition-all hover:shadow-md">
            <p class="text-[10px] text-gray-400 font-almarai mb-1">عدد الصناديق</p>
            <p class="text-xl font-bold text-gray-800  ">
                {{ $settlement->details->count() }}
            </p>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm transition-all hover:shadow-md">
            <p class="text-[10px] text-gray-400 font-almarai mb-1">تارِيخ الرفع</p>
            <p class="text-sm font-bold text-gray-800   mt-1">
                {{ $settlement->submitted_at->format('Y-m-d') }}
            </p>
        </div>
    </div>

    @if($settlement->rejection_reason)
        <div class="mb-8 bg-red-50 border border-red-200 rounded-2xl p-6">
            <h3 class="font-bold text-red-800 font-cairo mb-2 flex items-center gap-2">
                <i class="fas fa-exclamation-circle"></i> سبب إعادة التصفية للمراجعة
            </h3>
            <p class="text-red-700 font-almarai leading-relaxed">{{ $settlement->rejection_reason }}</p>
        </div>
    @endif

    <!-- Funds Breakdown & Transactions -->
    <div class="space-y-6">
        <h2 class="text-xl font-bold text-gray-800 font-cairo mb-4 flex items-center gap-2">
            <i class="fas fa-boxes text-orange-500"></i>  
             تفاصيل الصناديق وحركاتها المالية

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
                            <span class="text-[11px] text-emerald-700 font-bold font-cairo">{{ $detail->fund->currency_label }}</span>
                        </div>
                    </div>

                    <div class="flex flex-wrap md:flex-nowrap gap-6 flex-1 lg:justify-end">
                        <div class="text-center md:text-right">
                            <p class="text-[10px] text-gray-400 font-almarai mb-0.5">الرصيد الافتتاحي</p>
                            <p class="text-sm font-bold text-gray-600  ">{{ number_format($detail->opening_balance, 2) }} <span class="text-[10px]">{{ $detail->fund->currency_symbol }}</span></p>
                        </div>
                        <div class="text-center md:text-right">
                            <p class="text-[10px] text-green-500 font-almarai mb-0.5">إجمالي المقبوضات</p>
                            <p class="text-sm font-bold text-green-600  ">+{{ number_format($detail->total_income, 2) }} <span class="text-[10px]">{{ $detail->fund->currency_symbol }}</span></p>
                        </div>
                        <div class="text-center md:text-right">
                            <p class="text-[10px] text-red-500 font-almarai mb-0.5">إجمالي المصروفات</p>
                            <p class="text-sm font-bold text-red-600  ">-{{ number_format($detail->total_expense, 2) }} <span class="text-[10px]">{{ $detail->fund->currency_symbol }}</span></p>
                        </div>
                        <div class="text-center md:text-right bg-gray-100 px-3 py-1 rounded-lg">
                            <p class="text-[10px] text-gray-500 font-almarai mb-0.5">الرصيد الختامي</p>
                            <p class="text-sm font-bold text-gray-800  ">{{ number_format($detail->closing_balance, 2) }} <span class="text-[10px]">{{ $detail->fund->currency_symbol }}</span></p>
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
                                        <th class="px-6 py-3 font-cairo text-xs">المبلغ</th>
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
                                            <td class="px-6 py-3   text-xs">
                                                <a href="{{ route('vouchers.show', $voucher) }}" target="_blank" class="text-blue-600 hover:underline">{{ $voucher->voucher_number }}</a>
                                            </td>
                                            <td class="px-6 py-3 text-xs text-gray-500  ">{{ $voucher->date->format('Y-m-d') }}</td>
                                            <td class="px-6 py-3">
                                                <span class="px-2 py-1 rounded-md text-[10px] font-bold font-almarai {{ $typeInfo['color'] }}">
                                                    {{ $typeInfo['label'] }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-3 font-bold   text-sm {{ $isIncoming ? 'text-green-600' : 'text-red-600' }}" dir="ltr">
                                                {{ $isIncoming ? '+' : '-' }}{{ number_format($voucher->amount, 2) }} {{ $detail->fund->currency_symbol }}
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
                        <span>اعتماد نهائي ( مدير قسم المراكز الطلابية )</span>
                    </button>
                </form>
            @endif

            @if((auth()->user()->can('confirm-settlements') || auth()->user()->can('approve-settlements')) && in_array($settlement->status, ['submitted', 'confirmed']))
                <form action="{{ route('settlements.reject', $settlement) }}" method="POST" data-confirm="هل أنت متأكد من رفض هذه التصفية وإعادتها للمراجعة؟">
                    @csrf
                    <button type="button" onclick="document.getElementById('settlementRejectModal').classList.remove('hidden')" class="bg-red-500 text-white hover:bg-red-600 px-6 py-3 rounded-xl flex items-center gap-2 text-sm font-bold font-cairo shadow-sm transition-all">
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
                <div class="text-blue-700 font-almarai text-sm">بانتظار الاعتماد النهائي من مدير قسم المراكز الطلابية .</div>
            </div>
        @endif
    </div>

</div>

<div id="settlementRejectModal" class="hidden fixed inset-0 z-50 bg-black/50 p-4 flex items-center justify-center" role="dialog" aria-modal="true" aria-labelledby="settlementRejectModalTitle">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
        <h3 id="settlementRejectModalTitle" class="font-bold text-gray-800 font-cairo mb-2">رفض التصفية</h3>
        <p class="text-sm text-gray-500 font-almarai mb-4">يرجى كتابة سبب إعادة التصفية للمراجعة.</p>
        <form action="{{ route('settlements.reject', $settlement) }}" method="POST">
            @csrf
            <textarea name="rejection_reason" rows="4" required maxlength="1000" placeholder="اكتب سبب الرفض..."
                class="w-full border border-gray-200 rounded-xl px-4 py-3 font-almarai text-sm focus:ring-2 focus:ring-red-400 focus:border-red-400 outline-none resize-y">{{ old('rejection_reason') }}</textarea>
            @error('rejection_reason')
                <p class="mt-2 text-sm text-red-600 font-almarai">{{ $message }}</p>
            @enderror
            <div class="flex gap-3 justify-end mt-4">
                <button type="button" onclick="document.getElementById('settlementRejectModal').classList.add('hidden')"
                    class="px-5 py-2.5 border border-gray-200 rounded-xl font-cairo font-bold text-gray-600">إلغاء</button>
                <button type="submit" class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold font-cairo">تأكيد الرفض</button>
            </div>
        </form>
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
