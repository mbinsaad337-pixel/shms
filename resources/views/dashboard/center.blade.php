@extends('layouts.app')

@section('title', 'لوحة تحكم المركز')

@section('content')
    <div class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-black text-primary font-cairo">لوحة تحكم:
                {{ auth()->user()->center->name ?? 'غير محدد' }}
            </h1>
            <p class="text-gray-500 font-almarai text-xs md:text-sm mt-1">متابعة العمليات التشغيلية اليومية وإدارة موارد
                المركز.</p>
        </div>
        <div class="flex gap-4 w-full sm:w-auto overflow-x-auto pb-1 scrollbar-hide">
            <span
                class="whitespace-nowrap bg-gold/10 text-gold border border-gold/20 px-4 py-2 rounded-xl text-xs md:text-sm font-bold font-almarai shadow-sm">
                <i class="fas fa-shield-alt ml-1"></i>
                مدير المركز
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-6 mb-8">
        @can('view-students')
            <!-- Students -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 card-hover">
                <div class="flex items-center justify-between font-cairo">
                    <div>
                        <p class="text-xs text-gray-500 font-bold mb-1">إحصائيات الطلاب</p>
                        <div class="flex items-baseline space-x-2 space-x-reverse">
                            <h3 class="text-3xl font-bold text-navy">{{ number_format($stats['students_count']) }}</h3>
                            <span class="text-xs text-gray-400">كلي</span>
                        </div>
                        <div class="mt-2 text-xs">
                            <span class="text-red-500 font-bold">{{ $stats['students_suspended'] }}</span>
                            <span class="text-gray-400">خريجين</span>
                        </div>
                    </div>
                    <div class="bg-navy/10 p-4 rounded-2xl text-navy">
                        <i class="fas fa-users text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Pending Approval -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 card-hover">
                <div class="flex items-center justify-between font-cairo">
                    <div>
                        <p class="text-xs text-gray-400 font-bold mb-1">بانتظار الاعتماد</p>
                        <h3 class="text-3xl font-bold text-gold">{{ number_format($stats['pending_approval']) }}</h3>
                        <p class="text-xs text-gray-400 mt-1">تفتقد المراجعة</p>
                    </div>
                    <div class="bg-gold/10 p-4 rounded-2xl text-gold">
                        <i class="fas fa-user-check text-2xl"></i>
                    </div>
                </div>
            </div>
        @endcan

        @can('view-rooms')
            <!-- Remaining Seats -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 card-hover">
                <div class="flex items-center justify-between font-cairo">
                    <div>
                        <p class="text-xs text-gray-500 font-bold mb-1">المقاعد المتبقية</p>
                        <h3 class="text-3xl font-bold text-navy font-almarai">{{ $stats['remaining_seats'] }}</h3>
                        <p class="text-xs text-gray-400 mt-1">من إجمالي {{ $stats['total_capacity'] }}</p>
                    </div>
                    <div class="bg-navy/5 p-4 rounded-2xl text-navy">
                        <i class="fas fa-bed text-2xl"></i>
                    </div>
                </div>
            </div>
        @endcan

        {{-- @can('view-students')
            <!-- On Leave -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 card-hover">
                <div class="flex items-center justify-between font-cairo">
                    <div>
                        <p class="text-xs text-gray-400 font-bold mb-1">طلاب خارج السكن</p>
                        <h3 class="text-3xl font-bold text-navy">{{ $stats['on_leave_count'] }}</h3>
                        <p class="text-xs text-gray-400 mt-1">إجازات سارية</p>
                    </div>
                    <div class="bg-navy/5 p-4 rounded-2xl text-navy">
                        <i class="fas fa-walking text-2xl"></i>
                    </div>
                </div>
            </div>
        @endcan --}}

        @can('view-students')
            <!-- Academic Students -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 card-hover">
                <div class="flex items-center justify-between font-cairo">
                    <div>
                        <p class="text-xs text-gray-500 font-bold mb-1">الطلاب الأكاديميون</p>
                        <h3 class="text-3xl font-bold text-blue-600">{{ number_format($stats['academic_students_count']) }}</h3>
                        <p class="text-xs text-gray-400 mt-1">نظام التسكين الأكاديمي</p>
                    </div>
                    <div class="bg-blue-50 p-4 rounded-2xl text-blue-600">
                        <i class="fas fa-graduation-cap text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Cooperative Students -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 card-hover">
                <div class="flex items-center justify-between font-cairo">
                    <div>
                        <p class="text-xs text-gray-500 font-bold mb-1">الطلاب التعاونيون</p>
                        <h3 class="text-3xl font-bold text-emerald-600">
                            {{ number_format($stats['cooperative_students_count']) }}</h3>
                        <p class="text-xs text-gray-400 mt-1">نظام التسكين التعاوني</p>
                    </div>
                    <div class="bg-emerald-50 p-4 rounded-2xl text-emerald-600">
                        <i class="fas fa-handshake text-2xl"></i>
                    </div>
                </div>
            </div>
        @endcan

        @can('view-meals')
            <!-- Meal Subscribers -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 card-hover">
                <div class="flex items-center justify-between font-cairo">
                    <div>
                        <p class="text-xs text-gray-500 font-bold mb-1">المشتركين في التغذية</p>
                        <h3 class="text-3xl font-bold text-navy font-almarai">{{ $stats['meal_subscribers'] }}</h3>
                        <p class="text-xs text-gray-400 mt-1">مشترك حالي</p>
                    </div>
                    <div class="bg-navy/10 p-4 rounded-2xl text-navy">
                        <i class="fas fa-utensils text-2xl"></i>
                    </div>
                </div>
            </div>
        @endcan

        @can('view-funds')
            <!-- Center Balance -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 card-hover">
                <div class="flex items-center justify-between font-cairo">
                    <div>
                        <p class="text-xs text-gray-500 font-bold mb-1">رصيد الصناديق</p>
                        <h3 class="text-2xl font-bold text-navy font-almarai">
                            {{ number_format($stats['center_funds'], 0) }}
                        </h3>
                        <p class="text-xs text-gray-400 mt-1">ريال يمني</p>
                    </div>
                    <div class="bg-gold/10 p-4 rounded-2xl text-gold">
                        <i class="fas fa-wallet text-2xl"></i>
                    </div>
                </div>
            </div>
        @endcan

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <div class="lg:col-span-2 space-y-8">
            @can('view-vouchers')
                <!-- Recent Vouchers -->
                <div class="card-premium overflow-hidden">
                    <div class="px-8 py-6 border-b border-gray-50 flex justify-between items-center bg-navy/5">
                        <h2 class="text-lg font-bold text-navy font-cairo">سجل السندات المالية الأخير</h2>
                        <a href="{{ route('vouchers.index') }}"
                            class="text-gold text-sm font-bold font-cairo hover:underline">عرض الكل</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-right">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-8 py-4 text-xs font-bold text-gray-400 uppercase font-cairo">رقم السند</th>
                                    <th class="px-8 py-4 text-xs font-bold text-gray-400 uppercase font-cairo">الصندوق</th>
                                    <th class="px-8 py-4 text-xs font-bold text-gray-400 uppercase font-cairo">النوع</th>
                                    <th class="px-8 py-4 text-xs font-bold text-gray-400 uppercase font-cairo">المبلغ</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @if ($recent_vouchers->count() > 0)
                                    @foreach ($recent_vouchers as $voucher)
                                        <tr class="hover:bg-gray-50/50 transition-colors">
                                            <td class="px-8 py-4   text-sm text-primary font-bold">
                                                {{ $voucher->voucher_number }}
                                            </td>
                                            <td class="px-8 py-4 font-almarai text-sm">{{ $voucher->fund->name ?? '---' }}</td>
                                            <td class="px-8 py-4">
                                                @php
                                                    $bagdeClass = match ($voucher->type) {
                                                        'receipt' => 'bg-green-50 text-green-600',
                                                        'payment' => 'bg-red-50 text-red-600',
                                                        'salary' => 'bg-purple-50 text-purple-600',
                                                        'transfer' => 'bg-blue-50 text-blue-600',
                                                        default => 'bg-gray-50 text-gray-600',
                                                    };
                                                    $label = match ($voucher->type) {
                                                        'receipt' => 'قبض',
                                                        'payment' => 'صرف',
                                                        'salary' => 'رواتب',
                                                        'transfer' => 'تحويل',
                                                        default => $voucher->type,
                                                    };
                                                @endphp
                                                <span
                                                    class="px-3 py-1 rounded-full text-xs font-bold font-almarai {{ $bagdeClass }}">
                                                    {{ $label }}
                                                </span>
                                            </td>
                                            <td class="px-8 py-4 font-bold text-gray-800 font-almarai">
                                                {{ number_format($voucher->amount, 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4" class="px-8 py-8 text-center text-gray-400 font-almarai">لا توجد
                                            سندات مسجلة
                                            مؤخراً</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            @endcan

            <!-- Pending Approvals -->
            <div class="card-premium overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-50 flex justify-between items-center bg-gold/10">
                    <h2 class="text-lg font-bold text-navy font-cairo flex items-center gap-2">
                        <i class="fas fa-clipboard-check text-gold"></i> قيد المراجعة والاعتماد
                    </h2>
                </div>

                <div class="p-6 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                    @if (isset($pending_approvals['students']) && auth()->user()->can('manage-students'))
                        <a href="{{ route('students.index', ['status' => 'pending']) }}"
                            class="bg-blue-50/50 hover:bg-blue-50 p-4 rounded-2xl flex items-center justify-between border border-blue-100 transition-colors group">
                            <div>
                                <p class="text-[11px] text-blue-600 font-bold mb-1 font-cairo">بروفايلات</p>
                                <h3 class="text-2xl font-bold text-gray-800 group-hover:text-blue-700">
                                    {{ $pending_approvals['students'] }}
                                </h3>
                            </div>
                            <div class="bg-white p-3 rounded-xl text-blue-500 shadow-sm">
                                <i class="fas fa-user-clock text-lg"></i>
                            </div>
                        </a>
                    @endif

                    @if (isset($pending_approvals['leaves']) && auth()->user()->can('manage-leaves'))
                        <a href="{{ route('administrative.index', ['tab' => 'leaves']) }}"
                            class="bg-teal-50/50 hover:bg-teal-50 p-4 rounded-2xl flex items-center justify-between border border-teal-100 transition-colors group">
                            <div>
                                <p class="text-[11px] text-teal-600 font-bold mb-1 font-cairo">استئذانات</p>
                                <h3 class="text-2xl font-bold text-gray-800 group-hover:text-teal-700">
                                    {{ $pending_approvals['leaves'] }}
                                </h3>
                            </div>
                            <div class="bg-white p-3 rounded-xl text-teal-500 shadow-sm">
                                <i class="fas fa-plane-departure text-lg"></i>
                            </div>
                        </a>
                    @endif

                    @if (isset($pending_approvals['vouchers']) && auth()->user()->can('manage-vouchers'))
                        <a href="{{ route('vouchers.index') }}"
                            class="bg-purple-50/50 hover:bg-purple-50 p-4 rounded-2xl flex items-center justify-between border border-purple-100 transition-colors group">
                            <div>
                                <p class="text-[11px] text-purple-600 font-bold mb-1 font-cairo">سندات</p>
                                <h3 class="text-2xl font-bold text-gray-800 group-hover:text-purple-700">
                                    {{ $pending_approvals['vouchers'] }}
                                </h3>
                            </div>
                            <div class="bg-white p-3 rounded-xl text-purple-500 shadow-sm">
                                <i class="fas fa-file-invoice-dollar text-lg"></i>
                            </div>
                        </a>
                    @endif

                    @if (isset($pending_approvals['budgets']) && auth()->user()->can('view-budgets'))
                        <a href="{{ route('budgets.index') }}"
                            class="bg-orange-50/50 hover:bg-orange-50 p-4 rounded-2xl flex items-center justify-between border border-orange-100 transition-colors group">
                            <div>
                                <p class="text-[11px] text-orange-600 font-bold mb-1 font-cairo">موازنات عامة</p>
                                <h3 class="text-2xl font-bold text-gray-800 group-hover:text-orange-700">
                                    {{ $pending_approvals['budgets'] }}
                                </h3>
                            </div>
                            <div class="bg-white p-3 rounded-xl text-orange-500 shadow-sm">
                                <i class="fas fa-chart-pie text-lg"></i>
                            </div>
                        </a>
                    @endif

                    @if (isset($pending_approvals['food_budgets']) && auth()->user()->can('view-nutrition-budgets'))
                        <a href="{{ route('nutrition.budgets.index') }}"
                            class="bg-emerald-50/50 hover:bg-emerald-50 p-4 rounded-2xl flex items-center justify-between border border-emerald-100 transition-colors group">
                            <div>
                                <p class="text-[11px] text-emerald-600 font-bold mb-1 font-cairo">موازنات التغذية</p>
                                <h3 class="text-2xl font-bold text-gray-800 group-hover:text-emerald-700">
                                    {{ $pending_approvals['food_budgets'] }}
                                </h3>
                            </div>
                            <div class="bg-white p-3 rounded-xl text-emerald-500 shadow-sm">
                                <i class="fas fa-utensils text-lg"></i>
                            </div>
                        </a>
                    @endif

                    @if (isset($pending_approvals['food_settlements']) && auth()->user()->can('view-nutrition-settlements'))
                        <a href="{{ route('nutrition.settlements.index') }}"
                            class="bg-amber-50/50 hover:bg-amber-50 p-4 rounded-2xl flex items-center justify-between border border-amber-100 transition-colors group">
                            <div>
                                <p class="text-[11px] text-amber-600 font-bold mb-1 font-cairo">تصفيات التغذية</p>
                                <h3 class="text-2xl font-bold text-gray-800 group-hover:text-amber-700">
                                    {{ $pending_approvals['food_settlements'] }}
                                </h3>
                            </div>
                            <div class="bg-white p-3 rounded-xl text-amber-500 shadow-sm">
                                <i class="fas fa-file-invoice-dollar text-lg"></i>
                            </div>
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Interaction Panel -->
        <div class="space-y-6">
            @if (auth()->user()->can('manage-users') ||
                    auth()->user()->can('manage-students') ||
                    auth()->user()->can('view-budgets') ||
                    auth()->user()->can('view-funds') ||
                    auth()->user()->can('manage-vouchers') ||
                    auth()->user()->can('view-rooms'))
                <div class="card-premium p-8">
                    <h2 class="text-lg font-bold text-navy mb-6 font-cairo">إجراءات سريعة</h2>
                    <div class="space-y-4">
                        @can('manage-users')
                            <a href="{{ route('admin.users.index') }}"
                                class="flex items-center p-4 bg-navy/5 rounded-2xl hover:bg-navy hover:text-black transition-all group">
                                <div
                                    class="w-10 h-10 bg-white shadow-sm rounded-xl flex items-center justify-center text-navy ml-4">
                                    <i class="fas fa-users-cog text-xl"></i>
                                </div>
                                <span class="font-bold font-cairo">إدارة طاقم العمل</span>
                            </a>
                        @endcan

                        @can('manage-students')
                            <a href="{{ route('students.create') }}"
                                class="flex items-center p-4 bg-gold/5 rounded-2xl hover:bg-gold hover:text-navy transition-all group">
                                <div
                                    class="w-10 h-10 bg-white shadow-sm rounded-xl flex items-center justify-center text-gold ml-4">
                                    <i class="fas fa-user-plus text-xl"></i>
                                </div>
                                <span class="font-bold font-cairo">تسجيل طالب جديد</span>
                            </a>
                        @endcan

                        @can('view-budgets')
                            <a href="{{ route('budgets.index') }}"
                                class="flex items-center p-4 bg-navy/5 rounded-2xl hover:bg-navy hover:text-black transition-all group">
                                <div
                                    class="w-10 h-10 bg-white shadow-sm rounded-xl flex items-center justify-center text-navy ml-4">
                                    <i class="fas fa-calculator text-xl"></i>
                                </div>
                                <span class="font-bold font-cairo">إدارة الموازنات</span>
                            </a>
                        @endcan

                        @can('view-funds')
                            <a href="{{ route('funds.index') }}"
                                class="flex items-center p-4 bg-navy/5 rounded-2xl hover:bg-navy hover:text-black transition-all group">
                                <div
                                    class="w-10 h-10 bg-white shadow-sm rounded-xl flex items-center justify-center text-navy ml-4">
                                    <i class="fas fa-wallet text-xl"></i>
                                </div>
                                <span class="font-bold font-cairo">الصناديق والسندات</span>
                            </a>
                        @endcan



                        @can('view-activities')
                            <a href="{{ route('clubs.index') }}"
                                class="flex items-center p-4 bg-navy/5 rounded-2xl hover:bg-navy hover:text-black transition-all group">
                                <div
                                    class="w-10 h-10 bg-white shadow-sm rounded-xl flex items-center justify-center text-navy ml-4">
                                    <i class="fas fa-users-rectangle text-xl"></i>
                                </div>
                                <span class="font-bold font-cairo">إدارة الأندية الطلابية</span>
                            </a>
                        @endcan

                        @can('manage-vouchers')
                            <a href="{{ route('vouchers.create') }}"
                                class="flex items-center p-4 bg-navy/5 rounded-2xl hover:bg-navy hover:text-black transition-all group">
                                <div
                                    class="w-10 h-10 bg-white shadow-sm rounded-xl flex items-center justify-center text-navy ml-4">
                                    <i class="fas fa-file-invoice-dollar text-xl"></i>
                                </div>
                                <span class="font-bold font-cairo">إصدار سند مالي</span>
                            </a>
                        @endcan

                        @can('view-students')
                            <!-- Administrative Actions Dropdown -->
                            <div class="relative" id="adminActionsDropdown">
                                <button onclick="toggleAdminActions()" id="adminActionsBtn"
                                    class="w-full flex items-center p-4 bg-navy text-white rounded-2xl hover:bg-navy/90 transition-all font-bold font-cairo shadow-lg shadow-navy/20">
                                    <div
                                        class="w-10 h-10 bg-white/10 shadow-sm rounded-xl flex items-center justify-center text-gold ml-4 group-hover:scale-110 transition-transform">
                                        <i class="fas fa-gavel text-lg"></i>
                                    </div>
                                    <span class="flex-1 text-right">الإجراءات الإدارية</span>
                                    <i class="fas fa-chevron-down text-xs mr-2 transition-transform duration-200"
                                        id="adminChevron"></i>
                                </button>
                                <div id="adminMenu"
                                    class="absolute right-0 left-0 top-full mt-2 hidden z-50 animate-fade-in-down">
                                    <div class="bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden font-cairo ring-1 ring-black ring-opacity-5">
                                        <a href="{{ route('administrative.index', ['tab' => 'violations']) }}"
                                            class="block w-full text-right px-6 py-4 hover:bg-navy/5 text-navy font-bold border-b border-gray-50 transition-colors">
                                            <i class="fas fa-exclamation-triangle text-red-500 w-5 inline-block"></i> تسجيل مخالفة
                                        </a>
                                        <a href="{{ route('administrative.index', ['tab' => 'commitments']) }}"
                                            class="block w-full text-right px-6 py-4 hover:bg-gold/5 text-navy font-bold border-b border-gray-50 transition-colors">
                                            <i class="fas fa-file-contract text-gold w-5 inline-block"></i> تسجيل تعهد
                                        </a>
                                        <a href="{{ route('administrative.index', ['tab' => 'penalties']) }}"
                                            class="block w-full text-right px-6 py-4 hover:bg-navy/5 text-navy font-bold border-b border-gray-50 transition-colors">
                                            <i class="fas fa-ban text-red-700 w-5 inline-block"></i> تطبيق عقوبة
                                        </a>
                                        <a href="{{ route('administrative.index', ['tab' => 'absences']) }}"
                                            class="block w-full text-right px-6 py-4 hover:bg-navy/5 text-navy font-bold border-b border-gray-50 transition-colors">
                                            <i class="fas fa-calendar-times text-navy w-5 inline-block"></i> تسجيل غياب
                                        </a>
                                        <a href="{{ route('administrative.index', ['tab' => 'leaves']) }}"
                                            class="block w-full text-right px-6 py-4 hover:bg-navy/5 text-navy font-bold transition-colors">
                                            <i class="fas fa-plane-departure text-navy w-5 inline-block"></i> تسجيل استئذان
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endcan

                        @can('view-rooms')
                            <a href="{{ route('rooms.index', ['vacant' => 1]) }}"
                                class="flex items-center p-4 bg-indigo-50 rounded-2xl hover:bg-indigo-600 hover:text-white transition-all group">
                                <div
                                    class="w-10 h-10 bg-white shadow-sm rounded-xl flex items-center justify-center text-indigo-600 ml-4">
                                    <i class="fas fa-bed text-xl"></i>
                                </div>
                                <span class="font-bold font-cairo">تسكين وتوزيع الطلاب</span>
                            </a>
                        @endcan

                        @can('view-rooms')
                            <a href="{{ route('rooms.index') }}"
                                class="flex items-center p-4 bg-gray-50 rounded-2xl hover:bg-indigo-600 hover:text-white transition-all group">
                                <div
                                    class="w-10 h-10 bg-white shadow-sm rounded-xl flex items-center justify-center text-indigo-600 ml-4">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                    </svg>
                                </div>
                                <span class="font-bold font-cairo">إدارة المرافق والغرف</span>
                            </a>
                        @endcan
                    </div>
                </div>
            @endif
        </div>{{-- end Quick Panel (space-y-6) --}}
    </div>{{-- end lg:grid-cols-3 --}}

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        @can('view-violations')
            <!-- Recent Violations -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-50 flex justify-between items-center bg-red-50/30">
                    <h2 class="text-lg font-bold text-gray-800 font-cairo">آخر المخالفات المسجلة</h2>
                    <a href="{{ route('students.index') }}"
                        class="text-red-600 text-sm font-bold font-cairo hover:underline">متابعة السلوك</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-right">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-8 py-4 text-xs font-bold text-gray-400 uppercase font-cairo">الطالب</th>
                                <th class="px-8 py-4 text-xs font-bold text-gray-400 uppercase font-cairo">نوع المخالفة</th>
                                <th class="px-8 py-4 text-xs font-bold text-gray-400 uppercase font-cairo">الدرجة</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @if ($recent_violations->count() > 0)
                                @foreach ($recent_violations as $violation)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-8 py-5 font-bold text-gray-800 font-almarai">
                                            {{ $violation->student->name_ar ?? 'طالب غير معروف' }}
                                        </td>
                                        <td class="px-8 py-5 text-sm font-almarai text-gray-600">{{ $violation->type }}</td>
                                        <td class="px-8 py-5">
                                            <span
                                                class="px-3 py-1 rounded-full text-[10px] font-bold font-almarai {{ $violation->severity == 'severe' ? 'bg-red-50 text-red-600' : 'bg-orange-50 text-orange-600' }}">
                                                {{ $violation->severity == 'severe' ? 'جسيمة' : 'متوسطة' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="3" class="px-8 py-10 text-center text-gray-400 font-almarai">لا توجد
                                        مخالفات
                                        مسجلة مؤخراً</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        @endcan

        @can('view-absences')
            <!-- Recent Absences -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-50 flex justify-between items-center bg-blue-50/30">
                    <h2 class="text-lg font-bold text-gray-800 font-cairo">سجل الغياب الأخير</h2>
                    <a href="{{ route('students.index') }}"
                        class="text-primary text-sm font-bold font-cairo hover:underline">عرض الكل</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-right">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-8 py-4 text-xs font-bold text-gray-400 uppercase font-cairo">الطالب</th>
                                <th class="px-8 py-4 text-xs font-bold text-gray-400 uppercase font-cairo">تاريخ الغياب</th>
                                <th class="px-8 py-4 text-xs font-bold text-gray-400 uppercase font-cairo">العذر</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @if ($recent_absences->count() > 0)
                                @foreach ($recent_absences as $absence)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-8 py-5 font-bold text-gray-800 font-almarai">
                                            {{ $absence->student->name_ar ?? 'طالب غير معروف' }}
                                        </td>
                                        <td class="px-8 py-5 text-sm   text-gray-500">
                                            {{ $absence->date->format('Y-m-d') }}
                                        </td>
                                        <td class="px-8 py-5">
                                            <span
                                                class="px-3 py-1 rounded-full text-xs font-bold font-almarai {{ $absence->has_excuse ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600' }}">
                                                {{ $absence->has_excuse ? 'بعذر' : 'بدون عذر' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="3" class="px-8 py-10 text-center text-gray-400 font-almarai">لا توجد حالات
                                        غياب
                                        مسجلة مؤخراً</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        @endcan
    </div>{{-- end violations/absences grid --}}

    <!-- Active Activities -->
    
@endsection

@push('scripts')
    <style>
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in-down {
            animation: fadeInDown 0.2s ease-out;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 10px;
        }
    </style>

    <script>
        function toggleAdminActions() {
            const menu = document.getElementById('adminMenu');
            if (!menu)
                return;
            const chevron = document.getElementById('adminChevron');
            menu.classList.toggle('hidden');
            if (chevron) chevron.classList.toggle('rotate-180');
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            const dropdown = document.getElementById('adminActionsDropdown');
            const menu = document.getElementById('adminMenu');
            const chevron = document.getElementById('adminChevron');
            if (dropdown && !dropdown.contains(e.target) && menu) {
                menu.classList.add('hidden');
                if (chevron) chevron.classList.remove('rotate-180');
            }
        });

        function filterSmartStudents(input) {
            const term = input.value.toLowerCase();
            const container = input.closest('.student-selector-container');
            const items = container.querySelectorAll('.student-item');
            items.forEach(item => {
                const name = item.querySelector('.student-name').innerText.toLowerCase();
                if (name.includes(term)) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        function selectAllVisible(btn) {
            const container = btn.closest('.student-selector-container');
            const checkboxes = container.querySelectorAll(
                '.student-item:not([style*="display: none"]) input[type="checkbox"]');
            checkboxes.forEach(cb => {
                cb.checked = true;
                updateSelectedCount(cb);
            });
        }

        function clearSelection(btn) {
            const container = btn.closest('.student-selector-container');
            const checkboxes = container.querySelectorAll('input[type="checkbox"]');
            checkboxes.forEach(cb => {
                cb.checked = false;
                updateSelectedCount(cb);
            });
        }

        function updateSelectedCount(cb) {
            const modal = cb.closest('[id$="Modal"]');
            if (!modal) return;
            const checkboxes = modal.querySelectorAll('input[name="student_id[]"]:checked');
            const badge = modal.querySelector('.selected-badge');
            if (badge) {
                badge.innerText = checkboxes.length + ' مختار';
            }
        }

        function openViolationModal() {
            document.getElementById('violationModal').classList.remove('hidden');
            toggleAdminActions();
        }

        function closeViolationModal() {
            document.getElementById('violationModal').classList.add('hidden');
        }

        function openCommitmentModal() {
            document.getElementById('commitmentModal').classList.remove('hidden');
            toggleAdminActions();
        }

        function closeCommitmentModal() {
            document.getElementById('commitmentModal').classList.add('hidden');
        }

        function openPenaltyModal() {
            document.getElementById('penaltyModal').classList.remove('hidden');
            toggleAdminActions();
        }

        function closePenaltyModal() {
            document.getElementById('penaltyModal').classList.add('hidden');
        }

        function openAbsenceModal() {
            document.getElementById('absenceModal').classList.remove('hidden');
            toggleAdminActions();
        }

        function closeAbsenceModal() {
            document.getElementById('absenceModal').classList.add('hidden');
        }

        function openLeaveModal() {
            document.getElementById('leaveModal').classList.remove('hidden');
            toggleAdminActions();
        }

        function closeLeaveModal() {
            document.getElementById('leaveModal').classList.add('hidden');
        }

        // Close modals on escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                document.querySelectorAll('[id$="Modal"]').forEach(m => m.classList.add('hidden'));
            }
        });
    </script>

    <!-- Modals with Multi-Student Support -->

    <!-- Violation Modal -->
    <div id="violationModal" class="fixed inset-0 z-[100] hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-75" onclick="closeViolationModal()">
            </div>
            <div
                class="inline-block overflow-hidden text-right align-bottom transition-all transform bg-white rounded-3xl shadow-xl sm:my-8 sm:align-middle sm:max-w-xl sm:w-full">
                <div class="px-8 pt-8 pb-6 bg-white">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-2xl font-bold text-gray-800 font-cairo">تسجيل مخالفة انضباطية</h3>
                        <button onclick="closeViolationModal()"
                            class="text-gray-400 hover:text-gray-600 transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <form action="{{ route('violations.store') }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label
                                    class="block text-sm font-bold text-gray-700 mb-2 font-cairo text-rightflex justify-between items-center">
                                    <span
                                        class="selected-badge bg-red-100 text-red-700 px-2 py-0.5 rounded-lg text-[10px]">0
                                        مختار</span>
                                    <span>اختر الطالب / الطلاب</span>
                                </label>
                                <div class="student-selector-container bg-gray-50 rounded-2xl p-3 border border-gray-100">
                                    <div class="relative mb-3">
                                        <i
                                            class="fas fa-search absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                                        <input type="text" onkeyup="filterSmartStudents(this)"
                                            placeholder="بحث بالاسم أو الرقم..."
                                            class="w-full pr-10 pl-4 py-2 border-gray-200 rounded-xl text-xs focus:ring-red-500 font-almarai">
                                    </div>
                                    <div class="flex items-center justify-between mb-2 px-1">
                                        <button type="button" onclick="selectAllVisible(this)"
                                            class="text-[10px] text-red-600 font-bold font-cairo hover:underline">تحديد
                                            الكل</button>
                                        <button type="button" onclick="clearSelection(this)"
                                            class="text-[10px] text-gray-400 font-bold font-cairo hover:underline">مسح</button>
                                    </div>
                                    <div class="max-h-40 overflow-y-auto custom-scrollbar space-y-1 pr-1">
                                        @foreach ($students as $s)
                                            <label
                                                class="flex items-center p-2 hover:bg-white rounded-lg cursor-pointer transition-colors border border-transparent hover:border-red-100 student-item">
                                                <input type="checkbox" name="student_id[]" value="{{ $s->id }}"
                                                    onchange="updateSelectedCount(this)"
                                                    class="rounded text-red-600 focus:ring-red-500 h-4 w-4">
                                                <div class="mr-3 text-right">
                                                    <p class="text-xs font-bold text-gray-800 font-cairo student-name">
                                                        {{ $s->name_ar }}
                                                    </p>
                                                    <p class="text-[10px] text-gray-400 font-almarai">{{ $s->barcode }}
                                                    </p>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1 font-cairo text-right">نوع
                                        المخالفة</label>
                                    <input type="text" name="type" required
                                        class="w-full border-gray-200 rounded-xl p-3 focus:ring-red-500 font-almarai"
                                        placeholder="مثلاً: تأخير">
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-bold text-gray-700 mb-1 font-cairo text-right">الدرجة</label>
                                    <select name="severity" required
                                        class="w-full border-gray-200 rounded-xl p-3 focus:ring-red-500 font-almarai">
                                        <option value="minor">بسيطة</option>
                                        <option value="moderate">متوسطة</option>
                                        <option value="severe">جسيمة</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1 font-cairo text-right">تاريخ
                                    المخالفة</label>
                                <input type="date" name="violation_date" value="{{ date('Y-m-d') }}" required
                                    class="w-full border-gray-200 rounded-xl p-3 focus:ring-red-500 font-almarai">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1 font-cairo text-right">الوصف
                                    والتفاصيل</label>
                                <textarea name="description" rows="3" required
                                    class="w-full border-gray-200 rounded-xl p-3 focus:ring-red-500 font-almarai"></textarea>
                            </div>
                        </div>
                        <div class="mt-8 flex gap-3">
                            <button type="submit"
                                class="flex-1 bg-red-600 text-white font-bold font-cairo py-4 rounded-2xl hover:bg-red-700 transition-all shadow-lg shadow-red-900/20">حفظ
                                المخالفة</button>
                            <button type="button" onclick="closeViolationModal()"
                                class="flex-1 bg-gray-100 text-gray-600 font-bold font-cairo py-4 rounded-2xl hover:bg-gray-200 transition-all">إلغاء</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Commitment Modal -->
    <div id="commitmentModal" class="fixed inset-0 z-[100] hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-75" onclick="closeCommitmentModal()">
            </div>
            <div
                class="inline-block overflow-hidden text-right align-bottom transition-all transform bg-white rounded-3xl shadow-xl sm:my-8 sm:align-middle sm:max-w-xl sm:w-full">
                <div class="px-8 pt-8 pb-6 bg-white">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-2xl font-bold text-gray-800 font-cairo">تسجيل تعهد خطي</h3>
                        <button onclick="closeCommitmentModal()"
                            class="text-gray-400 hover:text-gray-600 transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <form action="{{ route('commitments.store') }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label
                                    class="block text-sm font-bold text-gray-700 mb-2 font-cairo text-right flex justify-between items-center">
                                    <span
                                        class="selected-badge bg-orange-100 text-orange-700 px-2 py-0.5 rounded-lg text-[10px]">0
                                        مختار</span>
                                    <span>اختر الطالب / الطلاب</span>
                                </label>
                                <div class="student-selector-container bg-gray-50 rounded-2xl p-3 border border-gray-100">
                                    <div class="relative mb-3">
                                        <i
                                            class="fas fa-search absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                                        <input type="text" onkeyup="filterSmartStudents(this)"
                                            placeholder="بحث بالاسم..."
                                            class="w-full pr-10 pl-4 py-2 border-gray-200 rounded-xl text-xs focus:ring-orange-500 font-almarai">
                                    </div>
                                    <div class="flex items-center justify-between mb-2 px-1">
                                        <button type="button" onclick="selectAllVisible(this)"
                                            class="text-[10px] text-orange-600 font-bold font-cairo hover:underline">تحديد
                                            الكل</button>
                                        <button type="button" onclick="clearSelection(this)"
                                            class="text-[10px] text-gray-400 font-bold font-cairo hover:underline">مسح</button>
                                    </div>
                                    <div class="max-h-40 overflow-y-auto custom-scrollbar space-y-1 pr-1">
                                        @foreach ($students as $s)
                                            <label
                                                class="flex items-center p-2 hover:bg-white rounded-lg cursor-pointer transition-colors border border-transparent hover:border-orange-100 student-item">
                                                <input type="checkbox" name="student_id[]" value="{{ $s->id }}"
                                                    onchange="updateSelectedCount(this)"
                                                    class="rounded text-orange-600 focus:ring-orange-500 h-4 w-4">
                                                <div class="mr-3 text-right">
                                                    <p class="text-xs font-bold text-gray-800 font-cairo student-name">
                                                        {{ $s->name_ar }}
                                                    </p>
                                                    <p class="text-[10px] text-gray-400 font-almarai">{{ $s->barcode }}
                                                    </p>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1 font-cairo text-right">نص
                                    التعهد</label>
                                <textarea name="text" rows="4" required
                                    class="w-full border-gray-200 rounded-xl p-3 focus:ring-orange-500 font-almarai"
                                    placeholder="مثلاً: أتعهد بالالتزام بموعد إغلاق البوابات..."></textarea>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label
                                        class="block text-sm font-bold text-gray-700 mb-1 font-cairo text-right">التاريخ</label>
                                    <input type="date" name="date" value="{{ date('Y-m-d') }}" required
                                        class="w-full border-gray-200 rounded-xl p-3 focus:ring-orange-500 font-almarai">
                                </div>
                                <div class="flex items-center gap-3 pt-8">
                                    <label class="flex items-center cursor-pointer">
                                        <input type="checkbox" name="requires_guardian_signature" value="1"
                                            class="rounded text-orange-600">
                                        <span class="mr-2 text-sm font-bold font-cairo">التوقيع </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="mt-8 flex gap-3">
                            <button type="submit"
                                class="flex-1 bg-orange-600 text-white font-bold font-cairo py-4 rounded-2xl hover:bg-orange-700 shadow-lg shadow-orange-900/20">حفظ
                                التعهد</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Penalty Modal -->
    <div id="penaltyModal" class="fixed inset-0 z-[100] hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-75" onclick="closePenaltyModal()"></div>
            <div
                class="inline-block overflow-hidden text-right align-bottom transition-all transform bg-white rounded-3xl shadow-xl sm:my-8 sm:align-middle sm:max-w-xl sm:w-full">
                <div class="px-8 pt-8 pb-6 bg-white">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-2xl font-bold text-gray-800 font-cairo text-right">تطبيق عقوبة إدارية</h3>
                        <button onclick="closePenaltyModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <form action="{{ route('penalties.store') }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label
                                    class="block text-sm font-bold text-gray-700 mb-2 font-cairo text-rightflex justify-between items-center">
                                    <span
                                        class="selected-badge bg-red-100 text-red-900 px-2 py-0.5 rounded-lg text-[10px]">0
                                        مختار</span>
                                    <span>اختر الطالب / الطلاب</span>
                                </label>
                                <div class="student-selector-container bg-gray-50 rounded-2xl p-3 border border-gray-100">
                                    <div class="relative mb-3">
                                        <i
                                            class="fas fa-search absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                                        <input type="text" onkeyup="filterSmartStudents(this)"
                                            placeholder="بحث بالاسم..."
                                            class="w-full pr-10 pl-4 py-2 border-gray-200 rounded-xl text-xs focus:ring-red-800 font-almarai">
                                    </div>
                                    <div class="flex items-center justify-between mb-2 px-1">
                                        <button type="button" onclick="selectAllVisible(this)"
                                            class="text-[10px] text-red-800 font-bold font-cairo hover:underline">تحديد
                                            الكل</button>
                                        <button type="button" onclick="clearSelection(this)"
                                            class="text-[10px] text-gray-400 font-bold font-cairo hover:underline">مسح</button>
                                    </div>
                                    <div class="max-h-40 overflow-y-auto custom-scrollbar space-y-1 pr-1">
                                        @foreach ($students as $s)
                                            <label
                                                class="flex items-center p-2 hover:bg-white rounded-lg cursor-pointer transition-colors border border-transparent hover:border-red-200 student-item">
                                                <input type="checkbox" name="student_id[]" value="{{ $s->id }}"
                                                    onchange="updateSelectedCount(this)"
                                                    class="rounded text-red-800 focus:ring-red-900 h-4 w-4">
                                                <div class="mr-3 text-right">
                                                    <p class="text-xs font-bold text-gray-800 font-cairo student-name">
                                                        {{ $s->name_ar }}
                                                    </p>
                                                    <p class="text-[10px] text-gray-400 font-almarai">{{ $s->barcode }}
                                                    </p>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4 text-right">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1 font-cairo">نوع
                                        العقوبة</label>
                                    <select name="type" required
                                        class="w-full border-gray-200 rounded-xl p-3 focus:ring-red-700 font-almarai text-right">
                                        <option value="verbal_warning">تنبيه شفهي</option>
                                        <option value="written_warning">إنذار خطي</option>
                                        <option value="service_suspension">إيقاف خدمات</option>
                                        <option value="temporary_suspension">إيقاف مؤقت</option>
                                        <option value="expulsion">فصل نهائي</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1 font-cairo">تاريخ
                                        البدء</label>
                                    <input type="date" name="start_date" value="{{ date('Y-m-d') }}" required
                                        class="w-full border-gray-200 rounded-xl p-3 focus:ring-red-700 font-almarai">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1 font-cairo text-right">السبب
                                    والتفاصيل</label>
                                <textarea name="description" rows="3" required
                                    class="w-full border-gray-200 rounded-xl p-3 focus:ring-red-700 font-almarai"></textarea>
                            </div>
                        </div>
                        <div class="mt-8">
                            <button type="submit"
                                class="w-full bg-red-700 text-white font-bold font-cairo py-4 rounded-2xl hover:bg-red-800 shadow-xl shadow-red-900/10">تطبيق
                                العقوبة</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Absence Modal -->
    <div id="absenceModal" class="fixed inset-0 z-[100] hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-75" onclick="closeAbsenceModal()"></div>
            <div
                class="inline-block overflow-hidden text-right align-bottom transition-all transform bg-white rounded-3xl shadow-xl sm:my-8 sm:align-middle sm:max-w-xl sm:w-full">
                <div class="px-8 pt-8 pb-6 bg-white">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-2xl font-bold text-gray-800 font-cairo">تسجيل غياب</h3>
                        <button onclick="closeAbsenceModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <form action="{{ route('absences.store') }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label
                                    class="block text-sm font-bold text-gray-700 mb-2 font-cairo text-right flex justify-between items-center">
                                    <span
                                        class="selected-badge bg-blue-100 text-blue-700 px-2 py-0.5 rounded-lg text-[10px]">0
                                        مختار</span>
                                    <span>اختر الطالب / الطلاب</span>
                                </label>
                                <div class="student-selector-container bg-gray-50 rounded-2xl p-3 border border-gray-100">
                                    <div class="relative mb-3">
                                        <i
                                            class="fas fa-search absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                                        <input type="text" onkeyup="filterSmartStudents(this)"
                                            placeholder="بحث بالاسم..."
                                            class="w-full pr-10 pl-4 py-2 border-gray-200 rounded-xl text-xs focus:ring-blue-600 font-almarai">
                                    </div>
                                    <div class="flex items-center justify-between mb-2 px-1">
                                        <button type="button" onclick="selectAllVisible(this)"
                                            class="text-[10px] text-blue-600 font-bold font-cairo hover:underline">تحديد
                                            الكل</button>
                                        <button type="button" onclick="clearSelection(this)"
                                            class="text-[10px] text-gray-400 font-bold font-cairo hover:underline">مسح</button>
                                    </div>
                                    <div class="max-h-40 overflow-y-auto custom-scrollbar space-y-1 pr-1">
                                        @foreach ($students as $s)
                                            <label
                                                class="flex items-center p-2 hover:bg-white rounded-lg cursor-pointer transition-colors border border-transparent hover:border-blue-100 student-item">
                                                <input type="checkbox" name="student_id[]" value="{{ $s->id }}"
                                                    onchange="updateSelectedCount(this)"
                                                    class="rounded text-blue-600 focus:ring-blue-500 h-4 w-4">
                                                <div class="mr-3 text-right">
                                                    <p class="text-xs font-bold text-gray-800 font-cairo student-name">
                                                        {{ $s->name_ar }}
                                                    </p>
                                                    <p class="text-[10px] text-gray-400 font-almarai">{{ $s->barcode }}
                                                    </p>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1 font-cairo text-right">تاريخ
                                        الغياب</label>
                                    <input type="date" name="date" value="{{ date('Y-m-d') }}" required
                                        class="w-full border-gray-200 rounded-xl p-3 focus:ring-blue-600 font-almarai">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1 font-cairo text-right">هل
                                        بعذر؟</label>
                                    <select name="has_excuse" required
                                        class="w-full border-gray-200 rounded-xl p-3 focus:ring-blue-600 font-almarai">
                                        <option value="0">بدون عذر</option>
                                        <option value="1">بعذر مقبول</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-bold text-gray-700 mb-1 font-cairo text-right">ملاحظات</label>
                                <textarea name="notes" rows="2"
                                    class="w-full border-gray-200 rounded-xl p-3 focus:ring-blue-600 font-almarai"></textarea>
                            </div>
                        </div>
                        <div class="mt-8">
                            <button type="submit"
                                class="w-full bg-blue-600 text-white font-bold font-cairo py-4 rounded-2xl hover:bg-blue-700 shadow-xl shadow-blue-900/10">حفظ
                                الغياب</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Leave Modal -->
    <div id="leaveModal" class="fixed inset-0 z-[100] hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-75" onclick="closeLeaveModal()"></div>
            <div
                class="inline-block overflow-hidden text-right align-bottom transition-all transform bg-white rounded-3xl shadow-xl sm:my-8 sm:align-middle sm:max-w-xl sm:w-full">
                <div class="px-8 pt-8 pb-6 bg-white">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-2xl font-bold text-gray-800 font-cairo">تسجيل استئذان / إجازة</h3>
                        <button onclick="closeLeaveModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <form action="{{ route('leaves.store') }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label
                                    class="block text-sm font-bold text-gray-700 mb-2 font-cairo text-right flex justify-between items-center">
                                    <span
                                        class="selected-badge bg-green-100 text-green-700 px-2 py-0.5 rounded-lg text-[10px]">0
                                        مختار</span>
                                    <span>اختر الطالب / الطلاب</span>
                                </label>
                                <div class="student-selector-container bg-gray-50 rounded-2xl p-3 border border-gray-100">
                                    <div class="relative mb-3">
                                        <i
                                            class="fas fa-search absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                                        <input type="text" onkeyup="filterSmartStudents(this)"
                                            placeholder="بحث بالاسم..."
                                            class="w-full pr-10 pl-4 py-2 border-gray-200 rounded-xl text-xs focus:ring-green-600 font-almarai">
                                    </div>
                                    <div class="flex items-center justify-between mb-2 px-1">
                                        <button type="button" onclick="selectAllVisible(this)"
                                            class="text-[10px] text-green-600 font-bold font-cairo hover:underline">تحديد
                                            الكل</button>
                                        <button type="button" onclick="clearSelection(this)"
                                            class="text-[10px] text-gray-400 font-bold font-cairo hover:underline">مسح</button>
                                    </div>
                                    <div class="max-h-40 overflow-y-auto custom-scrollbar space-y-1 pr-1">
                                        @foreach ($students as $s)
                                            <label
                                                class="flex items-center p-2 hover:bg-white rounded-lg cursor-pointer transition-colors border border-transparent hover:border-green-100 student-item">
                                                <input type="checkbox" name="student_id[]" value="{{ $s->id }}"
                                                    onchange="updateSelectedCount(this)"
                                                    class="rounded text-green-600 focus:ring-green-500 h-4 w-4">
                                                <div class="mr-3 text-right">
                                                    <p class="text-xs font-bold text-gray-800 font-cairo student-name">
                                                        {{ $s->name_ar }}
                                                    </p>
                                                    <p class="text-[10px] text-gray-400 font-almarai">{{ $s->barcode }}
                                                    </p>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1 font-cairo text-right">نوع
                                        الإجازة</label>
                                    <select name="type" required
                                        class="w-full border-gray-200 rounded-xl p-3 focus:ring-green-600 font-almarai">
                                        <option value="temporary">استئذان مؤقت</option>
                                        <option value="vacation">إجازة اعتيادية</option>
                                        <option value="medical">إجازة مرضية</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1 font-cairo text-right">تاريخ
                                        الخروج</label>
                                    <input type="datetime-local" name="departure_date" required
                                        class="w-full border-gray-200 rounded-xl p-3 focus:ring-green-600 font-almarai">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1 font-cairo text-right">العودة
                                        المتوقعة</label>
                                    <input type="datetime-local" name="expected_return_date" required
                                        class="w-full border-gray-200 rounded-xl p-3 focus:ring-green-600 font-almarai">
                                </div>
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-bold text-gray-700 mb-1 font-cairo text-right">السبب</label>
                                <textarea name="reason" rows="2" required
                                    class="w-full border-gray-200 rounded-xl p-3 focus:ring-green-600 font-almarai"></textarea>
                            </div>
                        </div>
                        <div class="mt-8">
                            <button type="submit"
                                class="w-full bg-green-600 text-white font-bold font-cairo py-4 rounded-2xl hover:bg-green-700 shadow-xl shadow-green-900/10">حفظ
                                طلب الإجازة</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endpush
