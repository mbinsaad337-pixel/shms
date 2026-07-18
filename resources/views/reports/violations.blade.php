@extends('layouts.app')

@section('title', 'تقرير المخالفات')

@section('content')
    <div class="container mx-auto px-6 py-8">
        @include('partials.print_header', [
            'title' => 'تقرير رصد المخالفات السلوكية', 
            'number' => 'REP-V-' . date('Ymd'),
            'department' => 'قسم الضبط والانضباط السلوكي'
        ])

        <div
            class="mb-8 flex justify-between items-center bg-white p-6 rounded-2xl border-l-8 border-gold shadow-sm no-print">
            <div>
                <h1 class="text-3xl font-black text-navy font-cairo">تقرير رصد المخالفات والعقوبات</h1>
                <p class="text-gray-400 font-almarai text-sm mt-1">تقرير تفصيلي بكافة التجاوزات السلوكية المسجلة بالمراكز
                </p>
            </div>
            <button onclick="window.print()"
                class="bg-navy text-white px-8 py-3 rounded-xl shadow-lg font-cairo font-bold hover:bg-navy/90 transition-all flex items-center gap-2 group">
                <i class="fas fa-print text-gold group-hover:scale-110 transition-transform"></i>
                <span>طباعة التقرير</span>
            </button>
        </div>

        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden mb-8">
            <table class="w-full text-right">
                <thead>
                    <tr class="bg-navy text-white font-cairo">
                        <th class="px-6 py-4 font-bold">الطالب</th>
                        <th class="px-6 py-4 font-bold">المركز</th>
                        <th class="px-6 py-4 font-bold">نوع المخالفة</th>
                        <th class="px-6 py-4 font-bold text-center">التاريخ</th>
                        <th class="px-6 py-4 font-bold text-center">إجراء العقوبة</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($data as $violation)
                        <tr class="hover:bg-red-50/20 transition-colors">
                            <td class="px-6 py-4 font-almarai font-bold">{{ $violation->student->name_ar }}</td>
                            <td class="px-6 py-4 font-almarai text-sm">{{ $violation->center->name }}</td>
                            <td class="px-6 py-4 font-almarai text-red-600">{{ $violation->type }}</td>
                            <td class="px-6 py-4 font-almarai text-gray-500 text-sm">
                                {{ $violation->created_at->format('Y/m/d') }}
                            </td>
                            <td class="px-6 py-4 font-almarai">
                                <span
                                    class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded font-bold text-xs">{{ $violation->penalty->name ?? 'قيد المراجعة' }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @include('partials.print_footer')
    </div>
@endsection
