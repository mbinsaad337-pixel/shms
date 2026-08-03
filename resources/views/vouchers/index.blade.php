@extends ('layouts.app')
@php
    /** @var \Illuminate\Pagination\LengthAwarePaginator|\App\Models\Voucher[] $vouchers */
@endphp

@section ('title', 'السندات والعمليات المالية')

@section ('content')
    <div class="container mx-auto px-4 py-8">
        <div class="mb-8 flex justify-between items-center bg-white p-6 rounded-2xl border-l-8 border-gold shadow-sm">
            <div>
                <h1 class="text-3xl font-black text-navy font-cairo">السندات المالية</h1>
                <p class="text-gray-400 font-almarai text-sm mt-1">إدارة واعتماد كافة العمليات المالية بنظام السكن</p>
            </div>
            <div class="flex items-center gap-4">
                @if(auth()->user()->hasRole('super-admin'))
                    <form action="{{ route('vouchers.index') }}" method="GET" class="flex items-center gap-2">
                        @if($selectedPeriod)
                            <input type="hidden" name="period" value="{{ $selectedPeriod }}">
                        @endif
                        <select name="center_id" onchange="this.form.submit()" 
                            class="px-4 py-2 rounded-xl border border-gray-100 bg-gray-50 focus:bg-white focus:border-gold outline-none text-sm font-cairo">
                            <option value="">كل المراكز</option>
                            @foreach ($centers as $center)
                                <option value="{{ $center->id }}" {{ request('center_id') == $center->id ? 'selected' : '' }}>
                                    {{ $center->name }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                @endif

                @if(!auth()->user()->hasRole('super-admin'))
                <a href="{{ route('funds.index') }}"
                    class="px-6 py-3 bg-white text-gold border border-gold rounded-xl hover:bg-gold hover:text-navy shadow-sm font-cairo font-bold transition-all transform hover:-translate-y-1 flex items-center gap-2">
                    <i class="fas fa-vault"></i>
                    <span>إدارة الصناديق</span>
                </a>
                <a href="{{ route('vouchers.create') }}"
                    class="px-6 py-3 bg-navy text-white rounded-xl hover:bg-navy/90 shadow-lg font-cairo font-bold transition-all transform hover:-translate-y-1 flex items-center gap-2">
                    <i class="fas fa-plus-circle text-gold"></i>
                    <span>إصدار سند جديد</span>
                </a>
                @endif
            </div>
        </div>

        <form action="{{ route('vouchers.index') }}" method="GET"
            class="mb-6 flex flex-wrap items-end gap-3 rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
            @if(request()->filled('center_id'))
                <input type="hidden" name="center_id" value="{{ request('center_id') }}">
            @endif
            <div>
                <label for="period" class="mb-1 block text-xs font-bold text-gray-500 font-cairo">شهر السندات</label>
                <input id="period" name="period" type="month" value="{{ $selectedPeriod }}"
                    class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm font-cairo outline-none focus:border-gold focus:bg-white">
            </div>
            <button type="submit"
                class="rounded-xl bg-navy px-5 py-2.5 text-sm font-bold text-white transition-colors hover:bg-navy/90 font-cairo">
                <i class="fas fa-filter ml-1"></i> تطبيق الفلترة
            </button>
            @if(request()->filled('period'))
                <a href="{{ route('vouchers.index', request()->only('center_id')) }}"
                    class="px-3 py-2.5 text-sm font-bold text-gray-500 hover:text-navy font-cairo">إلغاء فلترة الشهر</a>
            @endif
        </form>

        <!-- Stats Summary -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-5 mb-8">
            <div
                class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm transition-all group hover:border-navy/20">
                <p class="text-[10px] font-bold text-gray-400 font-cairo mb-1 uppercase tracking-wider">إجمالي السندات</p>
                <p class="text-3xl font-black text-navy  ">{{ $voucherStats['total'] }}</p>
            </div>
            <div
                class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm transition-all group hover:border-gold/20">
                <p class="text-[10px] font-bold text-gray-400 font-cairo mb-1 uppercase tracking-wider">المقبوضات</p>
                <p class="text-3xl font-black text-navy  ">
                    {{ number_format($voucherStats['receipts'], 0) }} <span
                        class="text-xs font-cairo text-gray-400">ر.ي</span>
                </p>
                <div class="h-1 w-12 bg-gold mt-2 rounded-full"></div>
            </div>
            <div
                class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm transition-all group hover:border-red-100">
                <p class="text-[10px] font-bold text-gray-400 font-cairo mb-1 uppercase tracking-wider">المصروفات</p>
                <p class="text-3xl font-black text-red-600  ">
                    {{ number_format($voucherStats['expenses'], 0) }} <span
                        class="text-xs font-cairo text-gray-400">ر.ي</span>
                </p>
                <div class="h-1 w-12 bg-red-500 mt-2 rounded-full"></div>
            </div>
            <div
                class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm transition-all group hover:border-navy/20 text-center">
                <div class="w-12 h-12 bg-navy/5 rounded-xl flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-exchange-alt text-navy"></i>
                </div>
                <p class="text-xs font-bold text-navy font-cairo">{{ $voucherStats['transfers'] }}
                    تحويلات
                </p>
            </div>
        </div>

        <!-- Vouchers Table -->
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-right">
                    <thead>
                        <tr class="bg-gray-50 text-gray-600">
                            <th class="px-6 py-4 font-cairo text-sm">رقم السند</th>
                            <th class="px-6 py-4 font-cairo text-sm">نوع السند</th>
                            <th class="px-6 py-4 font-cairo text-sm">المبلغ</th>
                            <th class="px-6 py-4 font-cairo text-sm">مناولة</th>
                            <th class="px-6 py-4 font-cairo text-sm">الصندوق</th>
                            <th class="px-6 py-4 font-cairo text-sm">التاريخ</th>
                            <th class="px-6 py-4 font-cairo text-sm text-center">المتبقي</th>
                            <th class="px-6 py-4 font-cairo text-sm">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @if ($vouchers->count() > 0)
                            @foreach ($vouchers as $voucher)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4   text-sm">{{ $voucher->voucher_number }}</td>
                                <td class="px-6 py-4">
                                    @php
                                        $types = [
                                            'receipt' => ['label' => 'قبض', 'color' => 'bg-green-100 text-green-700'],
                                            'payment' => ['label' => 'صرف', 'color' => 'bg-red-100 text-red-700'],
                                            'transfer' => ['label' => 'تحويل', 'color' => 'bg-blue-100 text-blue-700'],
                                            'salary' => ['label' => 'رواتب', 'color' => 'bg-purple-100 text-purple-700'],
                                        ];
                                        $typeInfo = $types[$voucher->type] ?? ['label' => $voucher->type, 'color' => 'bg-gray-100'];
                                    @endphp
                                    <span
                                        class="{{ $typeInfo['color'] }} px-3 py-1 rounded-full text-[10px] font-bold font-cairo">
                                        {{ $typeInfo['label'] }}
                                        @if($voucher->type == 'receipt' && $voucher->sub_type)
                                            - {{ $voucher->sub_type == 'housing' ? 'تسكين' : 'إيداع' }}
                                        @endif
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-bold text-gray-800">{{ number_format($voucher->amount, 2) }}</td>
                                <td class="px-6 py-4 text-sm font-almarai">{{ $voucher->payee_or_payer }}</td>
                                <td class="px-6 py-4">
                                    <span class="text-sm font-bold text-gray-600">{{ $voucher->fund->name }}</span>
                                    @if ($voucher->type == 'transfer' && $voucher->targetFund)
                                        <i class="fas fa-arrow-left mx-2 text-gray-300"></i>
                                        <span class="text-sm font-bold text-primary">{{ $voucher->targetFund->name }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $voucher->date ? $voucher->date->format('Y/m/d') : '-' }}</td>
                                <td class="px-6 py-4 text-center">
                                    @if($voucher->student)
                                        <span class="font-bold text-red-600">{{ number_format($voucher->student->remaining_fees, 2) }}</span>
                                    @else
                                        <span class="text-gray-300">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-left">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('vouchers.show', $voucher) }}"
                                            class="w-10 h-10 rounded-xl bg-navy/5 flex items-center justify-center text-navy hover:bg-navy hover:text-white transition-all shadow-sm"
                                            title="عرض التفاصيل">
                                            <i class="fas fa-eye text-sm"></i>
                                        </a>
                                        <a href="{{ route('vouchers.export-pdf', $voucher) }}" target="_blank"
                                            class="w-10 h-10 rounded-xl bg-gold/5 flex items-center justify-center text-gold hover:bg-gold hover:text-navy transition-all shadow-sm"
                                            title="تحميل PDF">
                                            <i class="fas fa-file-pdf text-sm"></i>
                                        </a>

                                        @php
                                            $periodKey = $voucher->center_id . '-' . $voucher->date->format('Y-n');
                                            $isLockedBySettlement = isset($lockedPeriods[$periodKey]);
                                        @endphp
                                        @if (!$isLockedBySettlement)
                                        <form action="{{ route('vouchers.destroy', $voucher) }}" method="POST"
                                            onsubmit="return confirm('هل أنت متأكد من حذف هذا السند؟ سيتم عكس التأثير المالي على الرصيد.');"
                                            class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center text-red-500 hover:bg-red-500 hover:text-white transition-all shadow-sm"
                                                title="حذف">
                                                <i class="fas fa-trash-alt text-sm"></i>
                                            </button>
                                        </form>
                                        @else
                                            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-gray-100 text-gray-400"
                                                title="مقفل بعد اعتماد تصفية الشهر">
                                                <i class="fas fa-lock text-sm"></i>
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-400 font-almarai">لا توجد سندات مسجلة حالياً</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                {{ $vouchers->links() }}
            </div>
        </div>
    </div>
@endsection
