@extends('layouts.app')

@section('title', 'إحصائيات الحلقات القرآنية')

@section('content')
    <div class="mb-8 flex justify-between items-center bg-white p-6 rounded-2xl border-l-8 border-gold shadow-sm">
        <div>
            <h1 class="text-3xl font-black text-navy font-cairo">التقارير والإحصائيات</h1>
            <p class="text-gray-400 font-almarai text-sm mt-1">متابعة دقيقة لمستوى الالتزام والنشاط في الحلقات</p>
        </div>
        <div>
            <a href="{{ route('quran-circles.index') }}"
                class="px-6 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 shadow-sm font-cairo font-bold transition-all flex items-center gap-2">
                <i class="fas fa-arrow-right"></i>
                <span>العودة للحلقات</span>
            </a>
        </div>
    </div>

    <!-- Stats Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-2xl shadow-sm border-b-4 border-navy">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 bg-navy/5 rounded-xl flex items-center justify-center text-navy">
                    <i class="fas fa-quran text-xl"></i>
                </div>
                <span class="text-xs font-bold text-gray-400 font-almarai uppercase">إجمالي الحلقات</span>
            </div>
            <p class="text-4xl font-black text-navy font-cairo">{{ $totalCircles }}</p>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border-b-4 border-gold">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 bg-gold/5 rounded-xl flex items-center justify-center text-gold">
                    <i class="fas fa-calendar-alt text-xl"></i>
                </div>
                <span class="text-xs font-bold text-gray-400 font-almarai uppercase">إجمالي الجلسات</span>
            </div>
            <p class="text-4xl font-black text-navy font-cairo">{{ $totalSessions }}</p>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border-b-4 border-green-500">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center text-green-600">
                    <i class="fas fa-user-check text-xl"></i>
                </div>
                <span class="text-xs font-bold text-gray-400 font-almarai uppercase">نسبة الالتزام</span>
            </div>
            <p class="text-4xl font-black text-navy font-cairo">82%</p>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border-b-4 border-red-500">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 bg-red-50 rounded-xl flex items-center justify-center text-red-600">
                    <i class="fas fa-user-times text-xl"></i>
                </div>
                <span class="text-xs font-bold text-gray-400 font-almarai uppercase">إجمالي الغيابات</span>
            </div>
            <p class="text-4xl font-black text-navy font-cairo">{{ $mostAbsent->sum('absence_count') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Most Committed Students -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-50 flex justify-between items-center">
                <h3 class="text-xl font-bold text-navy font-cairo flex items-center gap-2">
                    <i class="fas fa-trophy text-gold"></i>
                    أكثر الطلاب التزاماً
                </h3>
            </div>
            <div class="p-6">
                <!-- Most Committed List -->
                    @if(count($mostCommitted) > 0)
                        @foreach($mostCommitted as $index => $st)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-full bg-white flex items-center justify-center text-navy font-bold text-xs shadow-sm border border-gold/20">{{ $index + 1 }}</div>
                                <span class="font-bold text-navy font-cairo">{{ $st->student->name_ar }}</span>
                            </div>
                            <div class="text-left">
                                <p class="text-[10px] text-gray-400">{{ $st->attendance_count }} جلسة حضور</p>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <p class="text-center text-gray-400 font-almarai py-4">لا توجد بيانات حضور مسجلة</p>
                    @endif
            </div>
        </div>

        <!-- Most Absent Students -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-50 flex justify-between items-center">
                <h3 class="text-xl font-bold text-navy font-cairo flex items-center gap-2">
                    <i class="fas fa-exclamation-circle text-red-500"></i>
                    الطلاب الأكثر غياباً
                </h3>
            </div>
            <div class="p-6">
                <!-- Most Absent List -->
                    @if(count($mostAbsent) > 0)
                        @foreach($mostAbsent as $st)
                        <div class="flex items-center justify-between p-3 bg-red-50/30 rounded-xl border border-red-100/50">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-full bg-white flex items-center justify-center text-red-600 font-bold text-xs shadow-sm border border-red-100">!</div>
                                <span class="font-bold text-navy font-cairo">{{ $st->student->name_ar }}</span>
                            </div>
                            <div class="text-left">
                                <p class="text-xs font-bold text-red-600">{{ $st->absence_count }} غياب</p>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <p class="text-center text-gray-400 font-almarai py-4">لا توجد سجلات غياب</p>
                    @endif
                
                <div class="mt-6">
                    <a href="{{ route('quran-circles.absent-report') }}" class="block text-center py-2 bg-red-600 text-white rounded-xl hover:bg-red-700 transition-colors font-cairo font-bold text-sm">
                        عرض تقرير الغيابات المفصل
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
