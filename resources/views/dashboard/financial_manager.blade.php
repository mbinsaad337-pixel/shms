@extends('layouts.app')

@section('title', 'لوحة تحكم الإدارة المالية')

@section('content')
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-primary font-cairo">الإدارة المالية - مركز:
                {{ auth()->user()->center->name ?? 'غير محدد' }}
            </h1>
            <p class="text-gray-500 font-almarai mt-1">متابعة العمليات المالية والاعتمادات لموارد المركز.</p>
        </div>
        <div class="flex flex-wrap items-center justify-end gap-4">
            <form action="{{ route('dashboard') }}" method="GET" class="flex items-end gap-2">
                <div>
                    <label for="period" class="mb-1 block text-xs font-bold text-gray-500 font-cairo">شهر الإحصاءات</label>
                    <input id="period" name="period" type="month" value="{{ $selectedPeriod }}"
                        class="rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm outline-none focus:border-gold">
                </div>
                <button type="submit" class="rounded-xl bg-navy px-4 py-2 text-sm font-bold text-white font-cairo">تطبيق</button>
            </form>
            <span
                class="bg-gold/10 text-gold border border-gold/20 px-4 py-2 rounded-xl text-sm font-bold font-almarai shadow-sm">
                المسؤول المالي
            </span>
        </div>
    </div>

    <!-- Financial Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
        <!-- Center Balance -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 card-hover">
            <div class="flex items-center justify-between font-cairo">
                <div>
                    <p class="text-[10px] text-gray-500 font-bold mb-1">السيولة (الصناديق)</p>
                    <h3 class="text-xl font-bold text-navy font-almarai">{{ number_format($stats['total_liquidity'], 2) }}
                    </h3>
                    <div class="flex gap-1.5 mt-2">
                        <span class="text-[8px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100 px-1.5 py-0.5 rounded-full">ر.ي {{ number_format($stats['funds_by_currency']['YER'] ?? 0, 0) }}</span>
                        <span class="text-[8px] font-bold bg-blue-50 text-blue-700 border border-blue-100 px-1.5 py-0.5 rounded-full">ر.س {{ number_format($stats['funds_by_currency']['SAR'] ?? 0, 0) }}</span>
                        <span class="text-[8px] font-bold bg-amber-50 text-amber-700 border border-amber-100 px-1.5 py-0.5 rounded-full">$ {{ number_format($stats['funds_by_currency']['USD'] ?? 0, 0) }}</span>
                    </div>
                </div>
                <div class="bg-navy/10 p-3 rounded-xl text-navy">
                    <i class="fas fa-wallet text-xl"></i>
                </div>
            </div>
        </div>

        <!-- General Revenues -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 card-hover">
            <div class="flex items-center justify-between font-cairo">
                <div>
                    <p class="text-[10px] text-gray-500 font-bold mb-1">المقبوضات العامة</p>
                    <h3 class="text-xl font-bold text-emerald-600 font-almarai">
                        {{ number_format($stats['total_revenues'], 2) }}</h3>
                </div>
                <div class="bg-emerald-50 p-3 rounded-xl text-emerald-600">
                    <i class="fas fa-arrow-down text-xl"></i>
                </div>
            </div>
        </div>

        <!-- General Expenses -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 card-hover">
            <div class="flex items-center justify-between font-cairo">
                <div>
                    <p class="text-[10px] text-gray-500 font-bold mb-1">المصروفات العامة</p>
                    <h3 class="text-xl font-bold text-rose-600 font-almarai">
                        {{ number_format($stats['total_expenses'], 2) }}</h3>
                </div>
                <div class="bg-rose-50 p-3 rounded-xl text-rose-600">
                    <i class="fas fa-arrow-up text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Nutrition Revenues -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 card-hover">
            <div class="flex items-center justify-between font-cairo">
                <div>
                    <p class="text-[10px] text-gray-500 font-bold mb-1">مقبوضات التغذية</p>
                    <h3 class="text-xl font-bold text-teal-600 font-almarai">
                        {{ number_format($stats['nutrition_total_revenues'], 2) }}</h3>
                </div>
                <div class="bg-teal-50 p-3 rounded-xl text-teal-600">
                    <i class="fas fa-utensils text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Nutrition Expenses -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 card-hover">
            <div class="flex items-center justify-between font-cairo">
                <div>
                    <p class="text-[10px] text-gray-500 font-bold mb-1">مصروفات التغذية</p>
                    <h3 class="text-xl font-bold text-orange-600 font-almarai">
                        {{ number_format($stats['nutrition_total_expenses'], 2) }}</h3>
                </div>
                <div class="bg-orange-50 p-3 rounded-xl text-orange-600">
                    <i class="fas fa-receipt text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid  xl:grid-cols-4 gap-8 mb-8">
        <div class="xl:col-span-2 space-y-8">
            <!-- Pending Approvals & Checks -->
            

            <!-- Recent General Vouchers -->
            @can('view-vouchers')
                <div class="card-premium overflow-hidden">
                    <div class="px-8 py-6 border-b border-gray-50 flex justify-between items-center bg-navy/5">
                        <h2 class="text-lg font-bold text-navy font-cairo">أحدث السندات المالية (العامة)</h2>
                        <a href="{{ route('vouchers.index', ['period' => $selectedPeriod]) }}"
                            class="text-gold text-sm font-bold font-cairo hover:underline">عرض جميع السندات</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-right">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-8 py-4 text-xs font-bold text-gray-400 uppercase font-cairo">رقم السند</th>
                                    <th class="px-8 py-4 text-xs font-bold text-gray-400 uppercase font-cairo">الصندوق</th>
                                    <th class="px-8 py-4 text-xs font-bold text-gray-400 uppercase font-cairo">النوع</th>
                                    <th class="px-8 py-4 text-xs font-bold text-gray-400 uppercase font-cairo">المبلغ</th>
                                    <th class="px-8 py-4 text-xs font-bold text-gray-400 uppercase font-cairo">التاريخ</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @if($recent_vouchers->count() > 0)
                                    @foreach($recent_vouchers as $voucher)
                                        <tr class="hover:bg-gray-50/50 transition-colors">
                                            <td class="px-8 py-4   text-sm text-primary font-bold">
                                                {{ $voucher->voucher_number }}
                                            </td>
                                            <td class="px-8 py-4 font-almarai text-sm">{{ $voucher->fund->name ?? '---' }}</td>
                                            <td class="px-8 py-4">
                                                @php
                                                    $bagdeClass = match($voucher->type) {
                                                        'receipt' => 'bg-green-50 text-green-600',
                                                        'payment' => 'bg-red-50 text-red-600',
                                                        'salary' => 'bg-purple-50 text-purple-600',
                                                        'transfer' => 'bg-blue-50 text-blue-600',
                                                        default => 'bg-gray-50 text-gray-600'
                                                    };
                                                    $label = match($voucher->type) {
                                                        'receipt' => 'قبض',
                                                        'payment' => 'صرف',
                                                        'salary' => 'رواتب',
                                                        'transfer' => 'تحويل',
                                                        default => $voucher->type
                                                    };
                                                @endphp
                                                <span class="px-3 py-1 rounded-full text-xs font-bold font-almarai {{ $bagdeClass }}">
                                                    {{ $label }}
                                                </span>
                                            </td>
                                            <td class="px-8 py-4 font-bold text-gray-800 font-almarai">
                                                {{ number_format($voucher->amount, 2) }}
                                            </td>
                                            <td class="px-8 py-4   text-xs text-gray-500">
                                                {{ $voucher->date->format('Y-m-d') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="5" class="px-8 py-8 text-center text-gray-400 font-almarai">لا توجد سندات مسجلة
                                            مؤخراً</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            @endcan
        </div>

        <!-- Quick Interaction Panel -->
        <div class="space-y-4">
            <div class="mt-10 border-t border-gray-200 pt-6">
    <h2 class="text-lg font-bold text-navy mb-6 font-cairo ">
        الإجراءات السريعة
    </h2>

    <div class="flex flex-wrap justify-center gap-4">

        @can('view-funds')
            <a href="{{ route('funds.index') }}"
                class="flex items-center justify-center gap-3 px-6 py-4 bg-navy text-white rounded-2xl shadow-md hover:shadow-xl hover:-translate-y-1 transition-all duration-300 min-w-[220px]">
                <i class="fas fa-wallet text-lg"></i>
                <span class="font-bold font-cairo">الصناديق</span>
            </a>
        @endcan

      

        @can('view-budgets')
            <a href="{{ route('budgets.index') }}"
                class="flex items-center justify-center gap-3 px-6 py-4 bg-indigo-600 text-white rounded-2xl shadow-md hover:shadow-xl hover:-translate-y-1 transition-all duration-300 min-w-[220px]">
                <i class="fas fa-chart-pie text-lg"></i>
                <span class="font-bold font-cairo">الموازنات</span>
            </a>
        @endcan

        @can('view-settlements')
            <a href="{{ route('settlements.index') }}"
                class="flex items-center justify-center gap-3 px-6 py-4 bg-cyan-600 text-white rounded-2xl shadow-md hover:shadow-xl hover:-translate-y-1 transition-all duration-300 min-w-[220px]">
                <i class="fas fa-tasks text-lg"></i>
                <span class="font-bold font-cairo">التصفيات</span>
            </a>
        @endcan

        @can('view-nutrition-budgets')
            <a href="{{ route('nutrition.budgets.index') }}"
                class="flex items-center justify-center gap-3 px-6 py-4 bg-emerald-600 text-white rounded-2xl shadow-md hover:shadow-xl hover:-translate-y-1 transition-all duration-300 min-w-[220px]">
                <i class="fas fa-utensils text-lg"></i>
                <span class="font-bold font-cairo">موازنات التغذية</span>
            </a>
        @endcan

        @can('view-nutrition-settlements')
            <a href="{{ route('nutrition.settlements.index') }}"
                class="flex items-center justify-center gap-3 px-6 py-4 bg-amber-500 text-white rounded-2xl shadow-md hover:shadow-xl hover:-translate-y-1 transition-all duration-300 min-w-[220px]">
                <i class="fas fa-receipt text-lg"></i>
                <span class="font-bold font-cairo">تصفيات التغذية</span>
            </a>
        @endcan

    </div>
</div>
        </div>
    </div>
    </div>
@endsection
