@extends (auth()->user()->hasRole('nutrition-manager') ? 'layouts.nutrition' : 'layouts.app')
@section ('title', 'تقرير التصفية المالية')

@section ('content')
    @php $preview = $preview ?? false; $previewArchive = $previewArchive ?? null; @endphp
    <div class="p-4 md:p-8 max-w-6xl mx-auto">
        <!-- Web Action Bar -->
        <div class="flex items-center justify-between mb-8 no-print">
            <div class="flex items-center gap-4">
                <a href="{{ route('nutrition.settlements.index') }}" class="w-10 h-10 bg-white rounded-full shadow-sm flex items-center justify-center text-gray-400 hover:text-navy transition-all">
                    <i class="fas fa-arrow-right"></i>
                </a>
                <h2 class="text-2xl font-black text-navy font-cairo">سجل التصفية الشهرية</h2>
            </div>
            <div class="flex gap-3">
                @if(!$preview)
                    @if ($settlement->status !== 'approved')
                        <form action="{{ route('nutrition.settlements.recalculate', $settlement) }}" method="POST">
                            @csrf
                            <button type="submit" class="bg-white text-navy border border-navy px-6 py-2.5 rounded-xl font-bold font-cairo shadow-sm flex items-center gap-2 hover:bg-gray-50 transition-all">
                                <i class="fas fa-sync-alt"></i> تحديث الأرقام
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('nutrition.settlements.export-pdf', $settlement) }}" class="bg-navy text-white px-6 py-2.5 rounded-xl font-bold font-cairo shadow-lg flex items-center gap-2 hover:bg-navy/90 transition-all">
                        <i class="fas fa-file-pdf"></i> تصدير PDF
                    </a>
                    <form action="{{ route('nutrition.settlements.destroy', $settlement) }}" method="POST"
                        data-confirm="هل أنت متأكد من حذف هذه التصفية نهائياً؟ لا يمكن التراجع.">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-rose-50 text-rose-600 border border-rose-200 px-6 py-2.5 rounded-xl font-bold font-cairo shadow-sm flex items-center gap-2 hover:bg-rose-100 transition-all">
                            <i class="fas fa-trash-alt"></i> حذف التصفية
                        </button>
                    </form>
                @elseif($preview && $previewArchive)
                    <a href="{{ route('annual-rollover.export-archive-pdf', $previewArchive) }}" target="_blank"
                        class="bg-navy text-white px-6 py-2.5 rounded-xl font-bold font-cairo shadow-lg flex items-center gap-2 hover:bg-navy/90 transition-all">
                        <i class="fas fa-file-pdf"></i> تصدير PDF
                    </a>
                    <a href="{{ route('annual-rollover.index') }}"
                        class="bg-gray-100 text-gray-700 px-6 py-2.5 rounded-xl font-bold font-cairo flex items-center gap-2 hover:bg-gray-200 transition-all">
                        <i class="fas fa-arrow-right"></i> رجوع للقائمة
                    </a>
                @endif
            </div>
        </div>

        <!-- DOCUMENT START (Standard for Print) -->
        <div class="formal-document-container bg-white shadow-2xl rounded-[2rem] overflow-hidden border border-gray-100 p-10 print:p-0 print:shadow-none print:border-none">

            @include('partials.print_header', [
                'title' => 'تقرير التصفية المالية لقسم التغذية',
                'number' => 'NFS-' . str_pad($settlement->id, 5, '0', STR_PAD_LEFT),
                'department' => 'قسم شؤون الطلاب - وحدة التغذية'
            ])

            <!-- PART 1: SUMMARY DATA TABLE -->
            <div class="mt-10 mb-12">
                <h3 class="text-lg font-black text-navy font-cairo mb-4 border-r-4 border-gold pr-3">أولاً: الملخص المالي للفترة</h3>
                <table class="formal-table summary-table w-full">
                    <thead>
                        <tr>
                            <th class="text-right">الفترة الزمنية</th>
                            <th class="text-right">إجمالي المقبوضات (الإيرادات)</th>
                            <th class="text-right">إجمالي المصروفات (التشغيلية)</th>
                            <th class="text-right text-indigo-300">إجمالي مديونية الموردين</th>
                            <th class="text-center">النتيجة النهائية</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-bold text-navy">{{ $settlement->month_name }} {{ $settlement->year }}</td>
                            <td class="  text-emerald-700 font-black">{{ number_format($settlement->total_revenue, 2) }} ر.ي</td>
                            <td class="  text-rose-700 font-black">{{ number_format($settlement->total_expenses, 2) }} ر.ي</td>
                            <td class="  text-indigo-700 font-black bg-indigo-50/30">{{ number_format($settlement->total_debt, 2) }} ر.ي</td>
                            <td class="text-center">
                                <span class="px-4 py-1.5 rounded-lg font-black text-sm {{ $settlement->net_result >= 0 ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-rose-50 text-rose-800 border border-rose-200' }}">
                                    {{ number_format(abs($settlement->net_result), 2) }} 
                                    ({{ $settlement->getResultTypeLabel() }})
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- PART 2: REVENUE DETAILS -->
            <div class="mb-12">
                <h3 class="text-lg font-black text-navy font-cairo mb-4 border-r-4 border-gold pr-3">ثانياً: تفاصيل الإيرادات والمقبوضات</h3>
                <table class="formal-table w-full">
                    <thead>
                        <tr>
                            <th class="w-16">م</th>
                            <th class="w-32">رقم السند</th>
                            <th>اسم الطالب / المودع</th>
                            <th class="w-32">التاريخ</th>
                            <th>البيان / الملاحظات</th>
                            <th class="w-32 text-left">المبلغ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (is_countable($receipts) ? count($receipts) > 0 : (method_exists($receipts, 'count') ? $receipts->count() > 0 : !empty($receipts)))
    @foreach ($receipts as $index => $receipt)
                            <tr>
                                <td class="text-center text-gray-400  ">{{ $index + 1 }}</td>
                                <td class="  text-xs font-bold">#{{ $receipt->voucher_number }}</td>
                                <td class="font-bold text-gray-800">{{ $receipt->student?->name_ar ?? '---' }}</td>
                                <td class="text-xs text-gray-500  ">{{ $receipt->voucher_date->format('Y-m-d') }}</td>
                                <td class="text-xs text-gray-400 italic">{{ $receipt->description }}</td>
                                <td class="text-left font-black text-emerald-600  ">{{ number_format($receipt->amount, 2) }}</td>
                            </tr>
                            @endforeach
@else
                            <tr><td colspan="6" class="p-8 text-center text-gray-300 italic">لا توجد سجلات مقبوضات لهذه الفترة</td></tr>
                        @endif
                    </tbody>
                    @if ($receipts->count() > 0)
                        <tfoot>
                            <tr>
                                <td colspan="5" class="text-left font-black text-navy">إجمالي المقبوضات</td>
                                <td class="text-left font-black text-emerald-700   bg-emerald-50/50">{{ number_format($receipts->sum('amount'), 2) }}</td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>

            <!-- PART 3: EXPENSE DETAILS -->
            <div class="mb-12 page-break-before">
                <h3 class="text-lg font-black text-navy font-cairo mb-4 border-r-4 border-gold pr-3">ثالثاً: تفاصيل المصروفات والمشتريات</h3>

                <!-- Invoices Sub-table -->
                <div class="bg-gray-50/50 p-4 border-r-4 border-rose-400 mb-2 no-print">
                    <span class="text-xs font-bold text-rose-600 uppercase">1. فواتير الشراء المباشرة</span>
                </div>
                <table class="formal-table w-full mb-6">
                    <thead>
                        <tr class="bg-slate-100">
                            <th class="w-16">م</th>
                            <th class="w-24">رقم الفاتورة</th>
                            <th>المورد</th>
                            <th class="w-24">طريقة الدفع</th>
                            <th class="w-32">تاريخ الفاتورة</th>
                            <th class="w-32 text-left">المبلغ الضريبي</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (is_countable($invoices) ? count($invoices) > 0 : (method_exists($invoices, 'count') ? $invoices->count() > 0 : !empty($invoices)))
    @foreach ($invoices as $index => $invoice)
                            <tr>
                                <td class="text-center text-gray-400  ">{{ $index + 1 }}</td>
                                <td class="  text-xs font-bold">{{ $invoice->invoice_number }}</td>
                                <td class="font-bold text-gray-800">{{ ($supplierNames ?? collect())[$invoice->supplier_id] ?? $invoice->supplier?->name ?? '---' }}</td>
                                <td class="text-center">
                                    <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold {{ $invoice->payment_type === 'cash' ? 'bg-green-100 text-green-700 border border-green-200' : 'bg-orange-100 text-orange-700 border border-orange-200' }}">
                                        {{ $invoice->payment_type === 'cash' ? 'نقدي' : 'آجل' }}
                                    </span>
                                </td>
                                <td class="text-xs text-gray-500  ">{{ $invoice->invoice_date->format('Y-m-d') }}</td>
                                <td class="text-left font-black text-rose-600  ">{{ number_format($invoice->total_amount, 2) }}</td>
                            </tr>
                            @endforeach
@else
                            <tr><td colspan="6" class="p-8 text-center text-gray-300 italic">لا توجد فواتير شراء لهذا الشهر</td></tr>
                        @endif
                    </tbody>
                </table>

                <!-- Payments Sub-table -->
                <div class="bg-gray-50/50 p-4 border-r-4 border-cyan-400 mb-2 no-print">
                    <span class="text-xs font-bold text-cyan-600 uppercase">2. سندات صرف دفعات للموردين</span>
                </div>
                <table class="formal-table w-full">
                    <thead>
                        <tr class="bg-slate-100">
                            <th class="w-16">م</th>
                            <th class="w-40">رقم السند</th>
                            <th>اسم المورد</th>
                            <th class="w-32">التاريخ</th>
                            <th class="w-32 text-left">المبلغ المصروف</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (is_countable($payments) ? count($payments) > 0 : (method_exists($payments, 'count') ? $payments->count() > 0 : !empty($payments)))
    @foreach ($payments as $index => $payment)
                            <tr>
                                <td class="text-center text-gray-400  ">{{ $index + 1 }}</td>
                                <td class="  text-xs font-bold">#{{ $payment->voucher_number }}</td>
                                <td class="font-bold text-gray-800">{{ ($supplierNames ?? collect())[$payment->supplier_id] ?? $payment->supplier?->name ?? '---' }}</td>
                                <td class="text-xs text-gray-500  ">{{ $payment->voucher_date->format('Y-m-d') }}</td>
                                <td class="text-left font-black text-cyan-600  ">{{ number_format($payment->amount, 2) }}</td>
                            </tr>
                            @endforeach
@else
                            <tr><td colspan="5" class="p-8 text-center text-gray-300 italic">لا توجد سندات صرف لموردين</td></tr>
                        @endif
                    </tbody>
                    <tfoot>
                        <tr class="bg-rose-50/40">
                            <td colspan="4" class="text-left font-black text-navy border-t-2 border-navy">إجمالي المصروفات التشغيلية (سندات الصرف)</td>
                            <td class="text-left font-black text-rose-700   border-t-2 border-navy">{{ number_format($payments->sum('amount'), 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- PART 4: SIGNATURES -->
            <div class="mt-20 print:mt-12 grid grid-cols-1 md:grid-cols-4 gap-10 text-center">
                <div class="space-y-8">
                    <p class="font-bold text-gray-800 font-cairo underline decoration-gold underline-offset-8">مسؤول وحدة التغذية</p>
                    <div class="h-24 flex items-end justify-center">
                        <p class="text-[10px] text-gray-400">التوقيع والختم</p>
                    </div>
                    <p class="text-sm font-bold text-navy border-t border-gray-100 pt-2">{{ $settlement->creator?->name }}</p>
                </div>
                <div class="space-y-8">
                    <p class="font-bold text-gray-800 font-cairo underline decoration-gold underline-offset-8">المحاسب المالي</p>
                    <div class="h-24 border-b border-gray-100 border-dashed"></div>
                    <p class="text-[10px] text-gray-400">............................................</p>
                </div>
                <div class="space-y-8">
                    <p class="font-bold text-gray-800 font-cairo underline decoration-gold underline-offset-8">مدير المركز</p>
                    <div class="h-24 flex items-end justify-center">
                        <p class="text-[10px] text-gray-400">التوقيع والختم</p>
                    </div>
                    <p class="text-sm font-bold text-navy border-t border-gray-100 pt-2">{{ $settlement->approver?->name ?? '...........................' }}</p>
                </div>
                <div class="space-y-8">
                    <p class="font-bold text-gray-800 font-cairo underline decoration-gold underline-offset-8">مصادقة الإدارة المركزية</p>
                    <div class="h-24 border-b border-gray-100 border-dashed"></div>
                    <p class="text-[10px] text-gray-400">............................................</p>
                </div>
            </div>

            @include('partials.print_footer')
        </div>

        @if (!$preview && $settlement->status === 'rejected' && $settlement->rejection_reason)
            <div class="mt-8 bg-rose-50 border-2 border-rose-200 rounded-3xl p-6 no-print">
                <h4 class="font-black text-rose-700 font-cairo flex items-center gap-2 mb-2">
                    <i class="fas fa-exclamation-triangle"></i> سبب رفض التصفية:
                </h4>
                <p class="text-rose-600 font-almarai">{{ $settlement->rejection_reason }}</p>
            </div>
        @endif
    </div>

    <style>
        .formal-table {
            border-collapse: collapse;
            font-family: 'Cairo', sans-serif;
        }
        .formal-table th {
            background-color: #004274;
            color: white;
            padding: 12px 10px;
            font-size: 11px;
            border: 1px solid #004274;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .formal-table td {
            padding: 12px 10px;
            border: 1px solid #e2e8f0;
            font-size: 12px;
        }
        .formal-table tr:nth-child(even) {
            background-color: #f8fafc;
        }

        @media print {
            @page {
                size: A4;
                margin: 1.5cm;
            }
            body {
                background: white !important;
                padding: 0 !important;
            }
            .p-4, .p-8 { padding: 0 !important; }
            .formal-document-container {
                padding: 0 !important;
                border: none !important;
                box-shadow: none !important;
            }
            .formal-table th {
                background-color: #f1f5f9 !important;
                color: #000 !important;
                border: 1.5px solid #000 !important;
            }
            .formal-table td {
                border: 1px solid #000 !important;
                color: #000 !important;
            }
            .formal-table tfoot td {
                background-color: #f8fafc !important;
                border-top: 2px solid #000 !important;
                border-bottom: 2px solid #000 !important;
            }
            .text-emerald-700, .text-rose-700, .text-navy, .text-emerald-600, .text-rose-600, .text-cyan-600 {
                color: black !important;
            }
            .bg-emerald-50, .bg-rose-50, .bg-slate-100, .bg-emerald-50\/50, .bg-rose-50\/40 {
                background: transparent !important;
                border: 1px solid #000 !important;
            }
            .page-break-before {
                page-break-before: always;
            }
            .border-gold { border-color: #000 !important; }
        }
    </style>

@if (!$preview && $settlement->status === 'submitted' && (auth()->user()->hasRole('center-manager') || auth()->user()->hasRole('super-admin')))
    <div class="flex items-center gap-4 no-print mt-8 mb-12">
        <div class="h-12 w-px bg-gray-200 mx-2"></div>
        <form action="{{ route('nutrition.settlements.approve', $settlement) }}" method="POST">
            @csrf
            <button type="submit"
                class="inline-flex items-center gap-2 bg-emerald-600 text-white px-8 py-3 rounded-2xl font-bold font-cairo shadow-lg hover:bg-emerald-700 transition-all">
                <i class="fas fa-check"></i> اعتماد التصفية النهائية
            </button>
        </form>
        <button onclick="document.getElementById('rejectModal').classList.remove('hidden')"
            class="inline-flex items-center gap-2 bg-rose-600 text-white px-8 py-3 rounded-2xl font-bold font-cairo shadow-lg hover:bg-rose-700 transition-all">
            <i class="fas fa-times"></i> رفض التصفية
        </button>
    </div>
@endif

@if (session('success'))
    <div class="my-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl font-cairo font-bold no-print flex items-center gap-3">
        <i class="fas fa-check-circle text-xl"></i>
        {{ session('success') }}
    </div>
@endif
@if (session('error'))
    <div class="my-6 bg-rose-50 border border-rose-200 text-rose-700 px-6 py-4 rounded-2xl font-cairo font-bold no-print flex items-center gap-3">
        <i class="fas fa-times-circle text-xl"></i>
        {{ session('error') }}
    </div>
@endif
@if (session('info'))
    <div class="my-6 bg-blue-50 border border-blue-200 text-blue-700 px-6 py-4 rounded-2xl font-cairo font-bold no-print flex items-center gap-3">
        <i class="fas fa-info-circle text-xl"></i>
        {{ session('info') }}
    </div>
@endif

@if (!$preview)
    <!-- Reject Modal (Keep functional) -->
    <div id="rejectModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
            <h3 class="font-bold text-gray-800 font-cairo mb-4 text-center">رفض واعتماد التصفية</h3>
            <form action="{{ route('nutrition.settlements.reject', $settlement) }}" method="POST">
                @csrf
                <textarea name="rejection_reason" rows="4" required placeholder="يرجى كتابة أسباب الرفض بوضوح..."
                    class="w-full border border-gray-200 rounded-xl px-4 py-3 font-almarai text-sm focus:ring-2 focus:ring-rose-400 mb-4"></textarea>
                <div class="grid grid-cols-2 gap-3">
                    <button type="button" onclick="document.getElementById('rejectModal').classList.add('hidden')"
                        class="py-3 border border-gray-100 rounded-xl font-cairo font-bold text-gray-500 hover:bg-gray-50 transition-all">إلغاء</button>
                    <button type="submit" class="py-3 bg-rose-600 text-white rounded-xl font-bold font-cairo shadow-lg hover:bg-rose-700 transition-all">تأكيد الرفض</button>
                </div>
            </form>
        </div>
    </div>
@endif
@endsection
