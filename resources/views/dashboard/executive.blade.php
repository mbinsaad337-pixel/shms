@extends('layouts.app')

@section('title', ' لوحة تحكم : قسم ادارة المراكز الطلابية')

@section('content')
    <div class="mb-8 flex justify-between items-center bg-white p-6 rounded-2xl border-l-8 border-gold shadow-sm">
        <div>
            <h1 class="text-3xl font-black text-navy font-cairo">نظرة عامة شاملة</h1>
            <p class="text-gray-400 font-almarai text-sm mt-1">متابعة الأداء العام لكافة المراكز الطلابية</p>
        </div>
        <div class="flex gap-4">
            <div class="bg-navy/5 px-6 py-3 rounded-2xl border border-navy/10 flex items-center gap-3">
                <div class="w-2 h-2 bg-gold rounded-full animate-pulse shadow-[0_0_10px_rgba(212,160,68,0.8)]"></div>
                <span class="text-sm font-bold text-navy font-cairo">تحليل مباشر للنظام</span>
            </div>
            @can('manage-users')
                <a href="{{ route('admin.users.index') }}" 
                   class="bg-navy text-white px-6 py-3 rounded-2xl font-bold font-cairo shadow-lg hover:bg-navy/90 transition-all flex items-center gap-2 group">
                   <i class="fas fa-users-cog text-gold group-hover:rotate-45 transition-transform"></i>
                   <span>إدارة الطاقم</span>
                </a>
            @endcan
        </div>
    </div>

    <!-- Strategic KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        {{-- Total Students --}}
        <div class="card-premium p-6 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 opacity-[0.05] group-hover:scale-110 transition-transform duration-500">
                <i class="fas fa-user-graduate text-8xl text-navy"></i>
            </div>
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-navy/5 text-navy rounded-2xl flex items-center justify-center text-2xl shadow-inner">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest font-cairo">إجمالي الطلاب</p>
                    <h3 class="text-3xl font-black text-navy">{{ number_format($stats['students_count']) }}</h3>
                </div>
            </div>
            <div class="mt-4 flex items-center justify-between text-[10px] font-bold">
                <span class="text-blue-600">نسبة الإشغال العام:</span>
                @php $occupancy = $stats['total_capacity'] > 0 ? ($stats['occupied_seats'] / $stats['total_capacity']) * 100 : 0; @endphp
                <span class="bg-blue-50 text-blue-700 px-2 py-1 rounded">{{ round($occupancy) }}%</span>
            </div>
            <div class="mt-2 w-full bg-gray-50 h-1.5 rounded-full overflow-hidden">
                <div class="bg-blue-600 h-full transition-all duration-1000" style="width: {{ $occupancy }}%"></div>
            </div>
        </div>

        {{-- Total Liquidity --}}
        <div class="card-premium p-6 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 opacity-[0.05] group-hover:scale-110 transition-transform duration-500">
                <i class="fas fa-wallet text-8xl text-gold"></i>
            </div>
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-gold/10 text-gold rounded-2xl flex items-center justify-center text-2xl shadow-inner">
                    <i class="fas fa-coins"></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest font-cairo">إجمالي السيولة</p>
                    <h3 class="text-3xl font-black text-navy">{{ number_format($stats['total_liquidity'], 0) }} <span class="text-xs font-normal text-gray-400 font-cairo">ر.ي</span></h3>
                </div>
            </div>
            <p class="text-[10px] text-gray-500 mt-4 font-almarai italic">إجمالي الأرصدة المتوفرة في كافة الصناديق</p>
        </div>

        {{-- Pending Approvals --}}
        <div class="card-premium p-6 relative overflow-hidden group border-r-4 border-r-gold">
            <div class="absolute -right-4 -bottom-4 opacity-[0.05] group-hover:scale-110 transition-transform duration-500">
                <i class="fas fa-file-signature text-8xl text-navy"></i>
            </div>
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-navy/5 text-navy rounded-2xl flex items-center justify-center text-2xl shadow-inner">
                    <i class="fas fa-tasks"></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest font-cairo">اعتمادات معلقة</p>
                    <h3 class="text-3xl font-black text-navy">{{ $stats['pending_budgets'] + $stats['pending_settlements'] }}</h3>
                </div>
            </div>
            <div class="mt-4 flex gap-2">
                <span class="text-[9px] font-bold bg-orange-100 text-orange-700 px-2 py-0.5 rounded">{{ $stats['pending_budgets'] }} طلبات عهد</span>
                <span class="text-[9px] font-bold bg-blue-100 text-blue-700 px-2 py-0.5 rounded">{{ $stats['pending_settlements'] }} تصفيات</span>
            </div>
        </div>

        {{-- Active Centers --}}
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 opacity-[0.03] group-hover:scale-110 transition-transform duration-500">
                <i class="fas fa-university text-8xl"></i>
            </div>
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-purple-50 text-purple-600 rounded-2xl flex items-center justify-center text-2xl shadow-inner">
                    <i class="fas fa-building"></i>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider font-cairo">المراكز النشطة</p>
                    <h3 class="text-3xl font-extrabold text-purple-700">{{ $stats['centers_count'] }}</h3>
                </div>
            </div>
            <p class="text-[10px] text-gray-500 mt-4 font-almarai underline cursor-pointer hover:text-primary">إدارة المراكز والمدراء الفرعيين</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- High Priority Feed (Approvals Queue) --}}
        <div class="lg:col-span-1 flex flex-col gap-6">
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden flex flex-col h-full">
                <div class="px-8 py-5 border-b border-gray-50 bg-gray-50/50 flex justify-between items-center">
                    <h2 class="text-lg font-bold text-blue-900 font-cairo flex items-center gap-2">
                        <i class="fas fa-shield-alt text-orange-500"></i>
                        <span>قيد المراجعة والاعتماد</span>
                    </h2>
                </div>
                <div class="p-6 overflow-y-auto max-h-[600px] flex flex-col gap-4">
                    @php
                        $pendingItems = array_merge($recent_budgets->toArray(), $recent_settlements->toArray());
                    @endphp
                    @if(count($pendingItems) > 0)
                        @foreach($pendingItems as $item)
                            @php 
                                $isBudget = isset($item['total_amount']);
                                $typeLabel = $isBudget ? 'طلب عهدة' : 'تقرير تصفية';
                                $color = $isBudget ? 'orange' : 'blue';
                                $centerName = $item['center']['name'] ?? 'مركز غير معروف';
                                $amount = $isBudget ? ($item['total_amount'] ?? 0) : ($item['total_spent'] ?? 0);
                            @endphp
                          <a href="{{ $isBudget ? route('budgets.show', $item['id']) : route('settlements.show', $item['id']) }}" 
                                           class="text-[10px] font-bold text-{{ $color }}-600 hover:underline">   
                                    
                            <div class="p-4 bg-gray-50 border border-gray-100 rounded-2xl transition-all hover:bg-white hover:shadow-md hover:border-{{ $color }}-200 relative group">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-{{ $color }}-100 text-{{ $color }}-700 uppercase tracking-tighter">
                                        {{ $typeLabel }}
                                    </span>
                                </div>
                                <h4 class="text-sm font-bold text-gray-800 font-cairo mb-1 truncate">{{ $centerName }}</h4>
                                <div class="flex justify-between items-end">
                                    <div>
                                        <p class="text-[10px] text-gray-500 mb-2">المبلغ: {{ number_format($amount, 2) }} ر.ي</p>
                                        
                                    </div>
                                    <span class="text-[9px] text-gray-400">{{ \Carbon\Carbon::parse($item['created_at'])->diffForHumans() }}</span>
                                </div>
                            </div>
                             </a>
                        @endforeach
                    @else
                        <div class="text-center py-12 flex flex-col items-center">
                            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                <i class="fas fa-check-double text-gray-200 text-2xl"></i>
                            </div>
                            <p class="text-sm text-gray-400 font-cairo">لا يوجد طلبات اعتماد حالياً</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Center Comparison Table --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-8 py-5 border-b border-gray-50 bg-gray-50/50 flex justify-between items-center">
                    <h2 class="text-lg font-bold text-blue-900 font-cairo flex items-center gap-2">
                        <i class="fas fa-chart-line text-blue-600"></i>
                        <span>أداء المراكز الطلابية</span>
                    </h2>
                    <a href="{{ route('centers.index') }}" class="text-xs font-bold text-gray-400 hover:text-primary transition-colors">عرض جميع المراكز <i class="fas fa-external-link-alt text-[9px]"></i></a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-right">
                        <thead>
                            <tr class="bg-gray-50 text-[11px] font-bold text-gray-400 uppercase tracking-wider font-cairo">
                                <th class="px-8 py-4">اسم المركز</th>
                                <th class="px-8 py-4 text-center">الطلاب المسكنين</th>
                                <th class="px-8 py-4">السيولة المتاحة</th>
                                <th class="px-8 py-4">تحديثات النشاط</th>
                                <th class="px-8 py-4"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 font-almarai">
                            @foreach($centers_performance as $center)
                                <tr class="hover:bg-blue-50/20 transition-colors group">
                                    <td class="px-8 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-gray-50 rounded-xl flex items-center justify-center text-primary font-bold text-xs border border-gray-100 transition-colors group-hover:bg-white group-hover:shadow-sm">
                                                {{ mb_substr($center->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <span class="font-bold text-gray-700 block text-sm">{{ $center->name }}</span>
                                                <span class="text-[10px] text-gray-400">كود: CTR-{{ $center->id }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-4">
                                        <div class="flex flex-col items-center">
                                            <span class="text-sm font-bold text-gray-800">{{ $center->students_count }}</span>
                                            <div class="w-16 bg-gray-100 h-1 rounded-full mt-1 overflow-hidden">
                                                @php $cp = $center->total_capacity > 0 ? ($center->students_count / $center->total_capacity) * 100 : 0; @endphp
                                                <div class="bg-blue-500 h-full" style="width: {{ $cp }}%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-4 font-bold text-gray-700 text-sm">
                                        {{ number_format($center->funds_sum_balance ?? 0, 2) }} ر.ي
                                    </td>
                                    <td class="px-8 py-4">
                                        <div class="flex items-center gap-1">
                                            <span class="w-2 h-2 rounded-full bg-green-500"></span>
                                            <span class="text-[10px] font-bold text-gray-500 uppercase">عمليات اعتيادية</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-4 text-left">
                                        <a href="{{ route('centers.show', $center) }}" class="text-gray-300 hover:text-primary transition-colors">
                                            <i class="fas fa-chevron-left"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @if($centers_performance->isEmpty())
                        <div class="text-center py-20">
                            <i class="fas fa-folder-open text-gray-100 text-7xl mb-4"></i>
                            <p class="text-gray-400 font-cairo italic">لا توجد مراكز مسجلة حتى الآن</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Recent Activity Log (Simplified for Manager) --}}
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-gray-50 text-gray-400 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-history"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-800 text-sm">سجل العمليات العام</h4>
                        <p class="text-[10px] text-gray-400">تتبع كافة التحركات الإدارية والمالية عبر النظام</p>
                    </div>
                </div>
                <a href="#" class="px-4 py-2 bg-gray-50 text-gray-600 rounded-xl text-xs font-bold font-cairo hover:bg-gray-100 transition-colors">عرض السجل الكامل</a>
            </div>
        </div>
    </div>
@endsection
