@extends('layouts.app')

@section('title', 'إحصائيات الحلقات القرآنية')

@section('content')
    <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center bg-white p-6 rounded-2xl border-l-8 border-gold shadow-sm gap-4">
        <div>
            <h1 class="text-3xl font-black text-navy font-cairo">التقارير والإحصائيات</h1>
            <p class="text-gray-400 font-almarai text-sm mt-1">متابعة دقيقة لمستوى الالتزام والنشاط في الحلقات</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('quran-circles.export-stats', request()->all()) }}" target="_blank"
                class="px-6 py-2 bg-navy text-white rounded-xl hover:bg-navy/90 shadow-sm font-cairo font-bold transition-all flex items-center gap-2">
                <i class="fas fa-file-pdf"></i>
                <span>تصدير PDF</span>
            </a>
            <a href="{{ route('quran-circles.index') }}"
                class="px-6 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 shadow-sm font-cairo font-bold transition-all flex items-center gap-2">
                <i class="fas fa-arrow-right"></i>
                <span>العودة للحلقات</span>
            </a>
        </div>
    </div>

    {{-- Filters Card --}}
    <div class="bg-white p-6 rounded-2xl shadow-sm mb-8 font-cairo">
        <div class="flex items-center gap-2 mb-4">
            <i class="fas fa-sliders-h text-gold"></i>
            <h3 class="text-lg font-bold text-navy">تصفية الإحصائيات</h3>
        </div>
        <form action="{{ route('quran-circles.stats') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">الحلقة</label>
                <select name="circle_id" class="w-full rounded-xl border-gray-200 focus:ring-navy focus:border-navy">
                    <option value="">كل الحلقات</option>
                    @foreach($circles as $circle)
                        <option value="{{ $circle->id }}" {{ request('circle_id') == $circle->id ? 'selected' : '' }}>
                            {{ $circle->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">من تاريخ</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full rounded-xl border-gray-200 focus:ring-navy focus:border-navy">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">إلى تاريخ</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full rounded-xl border-gray-200 focus:ring-navy focus:border-navy">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 py-2.5 bg-navy text-white rounded-xl font-bold hover:bg-navy/90 transition-all flex items-center justify-center gap-2">
                    <i class="fas fa-filter"></i>
                    تصفية
                </button>
                @if(request()->hasAny(['circle_id', 'start_date', 'end_date']))
                    <a href="{{ route('quran-circles.stats') }}" class="py-2.5 px-4 bg-gray-100 text-gray-600 rounded-xl font-bold hover:bg-gray-200 transition-all flex items-center justify-center" title="إزالة الفلترة">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>

        {{-- Active filters display --}}
        @if(request()->hasAny(['circle_id', 'start_date', 'end_date']))
            <div class="mt-4 flex flex-wrap gap-2 items-center">
                <span class="text-xs text-gray-400 font-bold">الفلاتر النشطة:</span>
                @if(request('circle_id'))
                    <span class="px-3 py-1 bg-navy/5 text-navy text-xs font-bold rounded-full flex items-center gap-1">
                        <i class="fas fa-quran text-[10px]"></i>
                        {{ $circles->firstWhere('id', request('circle_id'))?->name }}
                    </span>
                @endif
                @if(request('start_date'))
                    <span class="px-3 py-1 bg-green-50 text-green-700 text-xs font-bold rounded-full flex items-center gap-1">
                        <i class="fas fa-calendar text-[10px]"></i>
                        من: {{ request('start_date') }}
                    </span>
                @endif
                @if(request('end_date'))
                    <span class="px-3 py-1 bg-red-50 text-red-700 text-xs font-bold rounded-full flex items-center gap-1">
                        <i class="fas fa-calendar text-[10px]"></i>
                        إلى: {{ request('end_date') }}
                    </span>
                @endif
            </div>
        @endif
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
            <p class="text-4xl font-black text-navy font-cairo">{{ $commitmentRate }}%</p>
            <p class="text-xs text-gray-400 font-almarai mt-1">{{ $totalPresent }} حضور من {{ $totalPresent + $totalAbsent }} سجل</p>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border-b-4 border-red-500">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 bg-red-50 rounded-xl flex items-center justify-center text-red-600">
                    <i class="fas fa-user-times text-xl"></i>
                </div>
                <span class="text-xs font-bold text-gray-400 font-almarai uppercase">إجمالي الغيابات</span>
            </div>
            <p class="text-4xl font-black text-navy font-cairo">{{ $totalAbsent }}</p>
        </div>
    </div>

    {{-- Per-Circle Breakdown Table --}}
    @if(count($circleStats) > 0)
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8">
        <div class="p-6 border-b border-gray-50 flex justify-between items-center">
            <h3 class="text-xl font-bold text-navy font-cairo flex items-center gap-2">
                <i class="fas fa-chart-bar text-gold"></i>
                إحصائيات الحلقات التفصيلية
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-right border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-6 py-4 text-xs font-black text-navy font-cairo uppercase tracking-wider">الحلقة</th>
                        <th class="px-6 py-4 text-xs font-black text-navy font-cairo uppercase tracking-wider text-center">عدد الطلاب</th>
                        <th class="px-6 py-4 text-xs font-black text-navy font-cairo uppercase tracking-wider text-center">الجلسات</th>
                        <th class="px-6 py-4 text-xs font-black text-navy font-cairo uppercase tracking-wider text-center">الحضور</th>
                        <th class="px-6 py-4 text-xs font-black text-navy font-cairo uppercase tracking-wider text-center">الغياب</th>
                        <th class="px-6 py-4 text-xs font-black text-navy font-cairo uppercase tracking-wider text-center">نسبة الالتزام</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 font-almarai">
                    @foreach($circleStats as $cs)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-navy/5 flex items-center justify-center text-navy">
                                    <i class="fas fa-quran text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-navy">{{ $cs['circle']->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $cs['circle']->type == 'memorization' ? 'تحفيظ' : 'تلاوة' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center text-sm font-bold text-gray-700">{{ $cs['students_count'] }}</td>
                        <td class="px-6 py-4 text-center text-sm font-bold text-gray-700">{{ $cs['sessions_count'] }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-3 py-1 bg-green-50 text-green-700 text-xs font-bold rounded-full">{{ $cs['present_count'] }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-3 py-1 bg-red-50 text-red-700 text-xs font-bold rounded-full">{{ $cs['absent_count'] }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex flex-col items-center">
                                <span class="text-sm font-bold {{ $cs['rate'] >= 80 ? 'text-green-600' : ($cs['rate'] >= 60 ? 'text-yellow-600' : 'text-red-600') }}">{{ $cs['rate'] }}%</span>
                                <div class="w-16 h-1.5 bg-gray-100 rounded-full mt-1 overflow-hidden">
                                    <div class="h-full rounded-full {{ $cs['rate'] >= 80 ? 'bg-green-500' : ($cs['rate'] >= 60 ? 'bg-yellow-500' : 'bg-red-500') }}" style="width: {{ $cs['rate'] }}%"></div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

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
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl mb-2">
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
                        <div class="flex items-center justify-between p-3 bg-red-50/30 rounded-xl border border-red-100/50 mb-2">
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
                    <a href="{{ route('quran-circles.absent-report', request()->only(['circle_id', 'start_date', 'end_date'])) }}" class="block text-center py-2 bg-red-600 text-white rounded-xl hover:bg-red-700 transition-colors font-cairo font-bold text-sm">
                        عرض تقرير الغيابات المفصل
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
