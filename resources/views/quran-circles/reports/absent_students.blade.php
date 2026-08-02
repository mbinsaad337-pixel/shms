@extends('layouts.app')

@section('title', 'تقرير غياب طلاب الحلقات')

@section('content')
    <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center bg-white p-6 rounded-2xl border-l-8 border-red-500 shadow-sm gap-4">
        <div>
            <h1 class="text-3xl font-black text-navy font-cairo">تقرير غياب الحلقات القرآنية</h1>
            <p class="text-gray-400 font-almarai text-sm mt-1">عرض وتصدير قائمة الطلاب الغائبين عن الجلسات</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('quran-circles.export-absent-report', array_merge(request()->all(), ['format' => 'pdf'])) }}"
                target="_blank"
                class="px-6 py-2 bg-red-600 text-white rounded-xl hover:bg-red-700 shadow-sm font-cairo font-bold transition-all flex items-center gap-2">
                <i class="fas fa-file-pdf"></i>
                <span>طباعة PDF للتقرير</span>
            </a>
            <a href="{{ route('quran-circles.stats') }}"
                class="px-6 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 shadow-sm font-cairo font-bold transition-all flex items-center gap-2">
                <i class="fas fa-arrow-right"></i>
                <span>عودة للإحصائيات</span>
            </a>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="bg-white p-6 rounded-2xl shadow-sm mb-8 font-cairo">
        <form action="{{ route('quran-circles.absent-report') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
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
            <div>
                <button type="submit" class="w-full py-2 bg-navy text-white rounded-xl font-bold hover:bg-navy/90 transition-all flex items-center justify-center gap-2">
                    <i class="fas fa-filter"></i>
                    تصفية النتائج
                </button>
            </div>
        </form>
    </div>

    <!-- Results Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-6 py-4 text-xs font-black text-navy font-cairo uppercase tracking-wider">الطالب</th>
                        <th class="px-6 py-4 text-xs font-black text-navy font-cairo uppercase tracking-wider">الحلقة</th>
                        <th class="px-6 py-4 text-xs font-black text-navy font-cairo uppercase tracking-wider">تاريخ الجلسة</th>
                        <th class="px-6 py-4 text-xs font-black text-navy font-cairo uppercase tracking-wider">رقم الهاتف</th>
                        <th class="px-6 py-4 text-xs font-black text-navy font-cairo uppercase tracking-wider">الحالة</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 font-almarai">
                    @if($absences->count() > 0)
                        @foreach($absences as $absence)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 text-sm font-bold text-navy">{{ $absence->student->name_ar }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $absence->session->circle->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600  ">{{ $absence->session->session_date }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $absence->student->personal_phone ?? '---' }}</td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 bg-red-100 text-red-700 text-xs font-bold rounded-full">غائب</span>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-400 font-almarai italic">
                                لا توجد سجلات غياب للمرشح المحدد.
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <div class="p-6 border-t border-gray-50">
            {{ $absences->links() }}
        </div>
    </div>
@endsection
