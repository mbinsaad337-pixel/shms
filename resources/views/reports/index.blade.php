@extends('layouts.app')

@section('title', 'التقارير العامة')

@section('content')
    <div class="container mx-auto px-6 py-8">
        <div class="mb-8 flex justify-between items-center bg-white p-6 rounded-2xl border-l-8 border-gold shadow-sm">
            <div>
                <h1 class="text-3xl font-black text-navy font-cairo">مركز التقارير الشامل</h1>
                <p class="text-gray-400 font-almarai text-sm mt-1">تقارير إدارية ومالية وتحليلية لمراقبة الأداء</p>
            </div>
            <div class="w-16 h-16 bg-navy/5 rounded-2xl flex items-center justify-center text-navy shadow-sm">
                <i class="fas fa-chart-line text-2xl"></i>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Administrative Reports -->
            <div class="card-premium p-8">
                <h3 class="text-xl font-black text-navy font-cairo mb-6 border-b border-gray-50 pb-4">التقارير الإدارية</h3>
                <ul class="space-y-3">
                    <li>
                        <a href="{{ route('reports.show', 'students') }}"
                            class="flex items-center justify-between p-3 rounded-xl hover:bg-gray-50 text-gray-700 font-almarai transition">
                            <span class="font-bold">تقرير الطلاب والمستويات</span>
                            <i
                                class="fas fa-chevron-left text-xs opacity-30 group-hover:opacity-100 transition-opacity"></i>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('reports.show', 'violations') }}"
                            class="flex items-center justify-between p-3 rounded-xl hover:bg-gray-50 text-gray-700 font-almarai transition">
                            <span class="font-bold">تقرير المخالفات والعقوبات</span>
                            <i
                                class="fas fa-chevron-left text-xs opacity-30 group-hover:opacity-100 transition-opacity"></i>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Financial Reports -->
            <div class="card-premium p-8">
                <h3 class="text-xl font-black text-navy font-cairo mb-6 border-b border-gray-50 pb-4">التقارير المالية</h3>
                <ul class="space-y-3">
                    <li>
                        <a href="{{ route('reports.show', 'funds') }}"
                            class="flex items-center justify-between p-3 rounded-xl hover:bg-gray-50 text-gray-700 font-almarai transition">
                            <span>تقرير أرصدة الصناديق</span>
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('reports.show', 'vouchers') }}"
                            class="flex items-center justify-between p-3 rounded-xl hover:bg-gray-50 text-gray-700 font-almarai transition">
                            <span>سجل السندات والعمليات</span>
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Nutrition & Activities Reports -->
            <div class="card-premium p-8">
                <h3 class="text-xl font-black text-navy font-cairo mb-6 border-b border-gray-50 pb-4">التغذية والأنشطة</h3>
                <ul class="space-y-3">
                    <li>
                        <div
                            class="flex items-center justify-between p-3 rounded-xl text-gray-400 font-almarai cursor-not-allowed">
                            <span>تقرير الاستهلاك والاشتراكات (قريباً)</span>
                        </div>
                    </li>
                    <li>
                        <div
                            class="flex items-center justify-between p-3 rounded-xl text-gray-400 font-almarai cursor-not-allowed">
                            <span>تقرير المشاركة في الأنشطة (قريباً)</span>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
@endsection
