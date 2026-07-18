@extends(auth()->user()->hasRole('nutrition-manager') ? 'layouts.nutrition' : 'layouts.app')
@section('title', 'تقارير حضور الوجبات')

@section('content')
<div class="p-6 max-w-6xl mx-auto">
    <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-end gap-6">
        <div>
            <h2 class="text-3xl font-bold text-gray-800 font-cairo">تقارير إشعارات الحضور</h2>
            <p class="text-gray-500 font-almarai mt-2">عرض قائمة الطلاب المتأخرين والغايبين حسب الوجبة اليومية</p>
        </div>
        <div class="flex items-center gap-4 no-print">
            <a href="{{ route('nutrition.schedules.index') }}" class="bg-indigo-50 text-indigo-700 px-6 py-3 rounded-2xl font-bold font-cairo hover:bg-indigo-100 transition-all border border-indigo-100 flex items-center gap-2">
                <i class="fas fa-clock"></i> ضبط مواعيد الوجبات
            </a>
            <button onclick="window.print()" class="bg-gray-800 text-white px-6 py-3 rounded-2xl font-bold font-cairo hover:bg-gray-900 transition-all shadow-lg flex items-center gap-2">
                <i class="fas fa-print"></i> طباعة التقرير
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-gray-100 mb-8 no-print">
        <form action="{{ route('nutrition.attendance-reports') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-xs font-bold text-gray-400 mb-2 font-cairo">التاريخ</label>
                <input type="date" name="date" value="{{ request('date', today()->toDateString()) }}" class="w-full bg-gray-50 border-gray-100 rounded-xl focus:ring-primary focus:border-primary font-mono">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-400 mb-2 font-cairo">الوجبة</label>
                <select name="meal_type" class="w-full bg-gray-50 border-gray-100 rounded-xl focus:ring-primary focus:border-primary font-cairo">
                    <option value="">كل الوجبات</option>
                    <option value="breakfast" {{ request('meal_type') == 'breakfast' ? 'selected' : '' }}>الفطور</option>
                    <option value="lunch" {{ request('meal_type') == 'lunch' ? 'selected' : '' }}>الغداء</option>
                    <option value="dinner" {{ request('meal_type') == 'dinner' ? 'selected' : '' }}>العشاء</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-400 mb-2 font-cairo">الحالة</label>
                <select name="status" class="w-full bg-gray-50 border-gray-100 rounded-xl focus:ring-primary focus:border-primary font-cairo text-sm">
                    <option value="">التأخر والغياب</option>
                    <option value="late" {{ request('status') == 'late' ? 'selected' : '' }}>المتأخرين فقط</option>
                    <option value="absent" {{ request('status') == 'absent' ? 'selected' : '' }}>الغايبين فقط</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-primary text-white py-3 rounded-xl font-bold font-cairo hover:bg-primary/90 transition-all shadow-md">
                    <i class="fas fa-filter ml-1"></i> تصفية
                </button>
                <a href="{{ route('nutrition.attendance-reports') }}" class="w-12 h-12 flex items-center justify-center bg-gray-100 text-gray-500 rounded-xl hover:bg-gray-200 transition-all">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Results -->
    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-8 py-5 text-gray-400 font-bold font-cairo text-sm">الطالب</th>
                        <th class="px-8 py-5 text-gray-400 font-bold font-cairo text-sm">الوجبة</th>
                        <th class="px-8 py-5 text-gray-400 font-bold font-cairo text-sm">الحالة</th>
                        <th class="px-8 py-5 text-gray-400 font-bold font-cairo text-sm">توقيت الإشعار</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @if(is_countable($reports) ? count($reports) > 0 : (method_exists($reports, 'count') ? $reports->count() > 0 : !empty($reports)))
    @foreach($reports as $report)
                        <tr class="hover:bg-gray-50/50 transition-all">
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center text-gray-400 font-bold text-xs">
                                        {{ mb_substr($report->student?->name_ar ?? '?', 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-800 font-cairo">{{ $report->student?->name_ar ?? 'طالب غير موجود' }}</p>
                                        <p class="text-xs text-gray-400 font-mono">{{ $report->student?->university_id }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-5">
                                @php 
                                    $mealNames = ['breakfast' => 'الفطور', 'lunch' => 'الغداء', 'dinner' => 'العشاء'];
                                @endphp
                                <span class="text-sm font-bold font-cairo text-gray-600">
                                    {{ $mealNames[$report->meal_type] ?? $report->meal_type }}
                                </span>
                            </td>
                            <td class="px-8 py-5">
                                @if($report->status == 'late')
                                    <span class="px-3 py-1.5 rounded-lg bg-yellow-50 text-yellow-700 font-bold font-cairo text-xs border border-yellow-100 flex items-center gap-2 w-max">
                                        <i class="fas fa-user-clock text-xs"></i> سيتأخر
                                    </span>
                                @else
                                    <span class="px-3 py-1.5 rounded-lg bg-red-50 text-red-700 font-bold font-cairo text-xs border border-red-100 flex items-center gap-2 w-max">
                                        <i class="fas fa-times-circle text-xs"></i> غائب
                                    </span>
                                @endif
                            </td>
                            <td class="px-8 py-5 font-mono text-gray-400 text-sm">
                                {{ $report->updated_at->format('H:i:s') }}
                            </td>
                        </tr>
                        @endforeach
@else
                        <tr>
                            <td colspan="4" class="px-8 py-12 text-center text-gray-400 font-almarai">
                                <i class="fas fa-clipboard-list text-3xl mb-4 opacity-20 block"></i>
                                لا توجد إشعارات حضور مسجلة لهذا اليوم بنفس المعايير المحددة.
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        
        @if($reports->hasPages())
            <div class="p-8 border-t border-gray-100">
                {{ $reports->links() }}
            </div>
        @endif
    </div>

    <!-- Stats Summary -->
    <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6 no-print">
        <div class="bg-gradient-to-br from-yellow-50 to-orange-50 border border-yellow-100 p-8 rounded-[2rem] flex items-center justify-between">
            <div>
                <p class="text-yellow-800 font-bold font-cairo mb-1">إجمالي المتأخرين اليوم</p>
                <p class="text-3xl font-black text-yellow-900 font-mono">
                    {{ \App\Models\FoodAttendanceReport::where('meal_date', today())->where('status', 'late')->count() }}
                </p>
            </div>
            <div class="w-16 h-16 bg-white rounded-2xl shadow-sm flex items-center justify-center text-yellow-500">
                <i class="fas fa-user-clock text-2xl"></i>
            </div>
        </div>
        <div class="bg-gradient-to-br from-red-50 to-rose-50 border border-red-100 p-8 rounded-[2rem] flex items-center justify-between">
            <div>
                <p class="text-red-800 font-bold font-cairo mb-1">إجمالي الغائبين اليوم</p>
                <p class="text-3xl font-black text-red-900 font-mono">
                    {{ \App\Models\FoodAttendanceReport::where('meal_date', today())->where('status', 'absent')->count() }}
                </p>
            </div>
            <div class="w-16 h-16 bg-white rounded-2xl shadow-sm flex items-center justify-center text-red-500">
                <i class="fas fa-times-circle text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        body { background: white !important; }
        .no-print { display: none !important; }
        .bg-white { border: none !important; box-shadow: none !important; }
        table { border: 1px solid #eee; }
        th, td { border-bottom: 1px solid #eee !important; }
    }
</style>
@endsection
