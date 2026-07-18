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
        <div class="flex gap-4">
            <span
                class="bg-gold/10 text-gold border border-gold/20 px-4 py-2 rounded-xl text-sm font-bold font-almarai shadow-sm">
                {{ auth()->user()->getRoleNames()->first() }}
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

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8 mb-8">
        <div class="xl:col-span-2 space-y-8">
            <!-- Pending Approvals & Checks -->
            <div class="card-premium overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-50 flex justify-between items-center bg-gold/5">
                    <h2 class="text-lg font-bold text-navy font-cairo flex items-center gap-2">
                        <i class="fas fa-clipboard-check text-gold"></i> بانتظار المراجعة والاعتماد
                    </h2>
                </div>

                <div class="p-6 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    @if(isset($pending_approvals['vouchers']) && auth()->user()->can('manage-vouchers'))
                        <a href="{{ route('vouchers.index') }}"
                            class="bg-purple-50/50 hover:bg-purple-50 p-4 rounded-2xl flex items-center justify-between border border-purple-100 transition-colors group">
                            <div>
                                <p class="text-[11px] text-purple-600 font-bold mb-1 font-cairo">سندات مالية (عامة)</p>
                                <h3 class="text-2xl font-bold text-gray-800 group-hover:text-purple-700">
                                    {{ $pending_approvals['vouchers'] }}
                                </h3>
                            </div>
                            <div class="bg-white p-3 rounded-xl text-purple-500 shadow-sm">
                                <i class="fas fa-file-invoice-dollar text-lg"></i>
                            </div>
                        </a>
                    @endif

                    @if(isset($pending_approvals['budgets']) && auth()->user()->can('view-budgets'))
                        <a href="{{ route('budgets.index') }}"
                            class="bg-orange-50/50 hover:bg-orange-50 p-4 rounded-2xl flex items-center justify-between border border-orange-100 transition-colors group">
                            <div>
                                <p class="text-[11px] text-orange-600 font-bold mb-1 font-cairo">موازنات (عامة)</p>
                                <h3 class="text-2xl font-bold text-gray-800 group-hover:text-orange-700">
                                    {{ $pending_approvals['budgets'] }}
                                </h3>
                            </div>
                            <div class="bg-white p-3 rounded-xl text-orange-500 shadow-sm">
                                <i class="fas fa-chart-pie text-lg"></i>
                            </div>
                        </a>
                    @endif

                    @if(isset($pending_approvals['settlements']) && auth()->user()->can('view-settlements'))
                        <a href="{{ route('settlements.index') }}"
                            class="bg-blue-50/50 hover:bg-blue-50 p-4 rounded-2xl flex items-center justify-between border border-blue-100 transition-colors group">
                            <div>
                                <p class="text-[11px] text-blue-600 font-bold mb-1 font-cairo">تصفيات (عامة)</p>
                                <h3 class="text-2xl font-bold text-gray-800 group-hover:text-blue-700">
                                    {{ $pending_approvals['settlements'] }}
                                </h3>
                            </div>
                            <div class="bg-white p-3 rounded-xl text-blue-500 shadow-sm">
                                <i class="fas fa-tasks text-lg"></i>
                            </div>
                        </a>
                    @endif

                    @if(isset($pending_approvals['food_budgets']) && auth()->user()->can('view-nutrition-budgets'))
                        <a href="{{ route('nutrition.budgets.index') }}"
                            class="bg-emerald-50/50 hover:bg-emerald-50 p-4 rounded-2xl flex items-center justify-between border border-emerald-100 transition-colors group">
                            <div>
                                <p class="text-[11px] text-emerald-600 font-bold mb-1 font-cairo">موازنات התמذية</p>
                                <h3 class="text-2xl font-bold text-gray-800 group-hover:text-emerald-700">
                                    {{ $pending_approvals['food_budgets'] }}
                                </h3>
                            </div>
                            <div class="bg-white p-3 rounded-xl text-emerald-500 shadow-sm">
                                <i class="fas fa-utensils text-lg"></i>
                            </div>
                        </a>
                    @endif

                    @if(isset($pending_approvals['food_settlements']) && auth()->user()->can('view-nutrition-settlements'))
                        <a href="{{ route('nutrition.settlements.index') }}"
                            class="bg-amber-50/50 hover:bg-amber-50 p-4 rounded-2xl flex items-center justify-between border border-amber-100 transition-colors group">
                            <div>
                                <p class="text-[11px] text-amber-600 font-bold mb-1 font-cairo">تصفيات التغذية</p>
                                <h3 class="text-2xl font-bold text-gray-800 group-hover:text-amber-700">
                                    {{ $pending_approvals['food_settlements'] }}
                                </h3>
                            </div>
                            <div class="bg-white p-3 rounded-xl text-amber-500 shadow-sm">
                                <i class="fas fa-receipt text-lg"></i>
                            </div>
                        </a>
                    @endif
                </div>
            </div>

            <!-- Recent General Vouchers -->
            @can('view-vouchers')
                <div class="card-premium overflow-hidden">
                    <div class="px-8 py-6 border-b border-gray-50 flex justify-between items-center bg-navy/5">
                        <h2 class="text-lg font-bold text-navy font-cairo">أحدث السندات المالية (العامة)</h2>
                        <a href="{{ route('vouchers.index') }}"
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
                                            <td class="px-8 py-4 font-mono text-sm text-primary font-bold">
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
                                            <td class="px-8 py-4 font-mono text-xs text-gray-500">
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
        <div class="space-y-6">
            <div class="card-premium p-8">
                <h2 class="text-lg font-bold text-navy mb-6 font-cairo">إجراءات وروابط سريعة</h2>
                <div class="space-y-4">
                    @can('view-funds')
                        <a href="{{ route('funds.index') }}"
                            class="flex items-center p-4 bg-navy/5 rounded-2xl hover:bg-navy hover:text-white transition-all group">
                            <div
                                class="w-10 h-10 bg-white shadow-sm rounded-xl flex items-center justify-center text-navy ml-4 group-hover:scale-110 transition-transform">
                                <i class="fas fa-wallet text-xl"></i>
                            </div>
                            <span class="font-bold font-cairo">إدارة الصناديق المستديمة</span>
                        </a>
                    @endcan

                    @can('manage-vouchers')
                        <a href="{{ route('vouchers.create') }}"
                            class="flex items-center p-4 bg-gold/5 rounded-2xl hover:bg-gold hover:text-navy transition-all group">
                            <div
                                class="w-10 h-10 bg-white shadow-sm rounded-xl flex items-center justify-center text-gold ml-4 group-hover:scale-110 transition-transform">
                                <i class="fas fa-file-invoice-dollar text-xl"></i>
                            </div>
                            <span class="font-bold font-cairo">إصدار سند مالي جديد</span>
                        </a>
                    @endcan

                    @can('view-budgets')
                        <a href="{{ route('budgets.index') }}"
                            class="flex items-center p-4 bg-navy/5 rounded-2xl hover:bg-navy hover:text-white transition-all group">
                            <div
                                class="w-10 h-10 bg-white shadow-sm rounded-xl flex items-center justify-center text-navy ml-4 group-hover:scale-110 transition-transform">
                                <i class="fas fa-chart-pie text-xl"></i>
                            </div>
                            <span class="font-bold font-cairo">الموازنات التقديرية (العامة)</span>
                        </a>
                    @endcan

                    @can('view-settlements')
                        <a href="{{ route('settlements.index') }}"
                            class="flex items-center p-4 bg-navy/5 rounded-2xl hover:bg-navy hover:text-white transition-all group">
                            <div
                                class="w-10 h-10 bg-white shadow-sm rounded-xl flex items-center justify-center text-navy ml-4 group-hover:scale-110 transition-transform">
                                <i class="fas fa-tasks text-xl"></i>
                            </div>
                            <span class="font-bold font-cairo">التصفيات الشهرية (العامة)</span>
                        </a>
                    @endcan

                    <hr class="border-gray-100 my-4">

                    @can('view-nutrition-budgets')
                        <a href="{{ route('nutrition.budgets.index') }}"
                            class="flex items-center p-4 bg-emerald-50 rounded-2xl hover:bg-emerald-600 hover:text-white transition-all group">
                            <div
                                class="w-10 h-10 bg-white shadow-sm rounded-xl flex items-center justify-center text-emerald-600 ml-4 group-hover:scale-110 transition-transform">
                                <i class="fas fa-utensils text-xl"></i>
                            </div>
                            <span class="font-bold font-cairo">موازنات التغذية</span>
                        </a>
                    @endcan

                    @can('view-nutrition-settlements')
                        <a href="{{ route('nutrition.settlements.index') }}"
                            class="flex items-center p-4 bg-amber-50 rounded-2xl hover:bg-amber-600 hover:text-white transition-all group">
                            <div
                                class="w-10 h-10 bg-white shadow-sm rounded-xl flex items-center justify-center text-amber-600 ml-4 group-hover:scale-110 transition-transform">
                                <i class="fas fa-receipt text-xl"></i>
                            </div>
                            <span class="font-bold font-cairo">تصفيات التغذية</span>
                        </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>
@endsection
