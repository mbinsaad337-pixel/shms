@extends('layouts.app')

@section('title', 'تقرير الطلاب الشامل')

@section('content')
    <div class="container mx-auto px-6 py-8">
        @include('partials.print_header', [
            'title' => 'تقرير بيانات الطلاب وحالات السكن', 
            'number' => 'REP-ST-' . date('Ymd'),
            'department' => 'قسم الإسكان وشؤون الطلاب'
        ])

        <div
            class="mb-8 flex justify-between items-center bg-white p-6 rounded-2xl border-l-8 border-gold shadow-sm no-print">
            <div>
                <h1 class="text-3xl font-black text-navy font-cairo">تقرير الطلاب والمستويات</h1>
                <p class="text-gray-400 font-almarai text-sm mt-1">تقرير مفصل ببيانات الطلاب وحالات السكن بالمراكز</p>
            </div>
            <button onclick="window.print()"
                class="bg-navy text-white px-8 py-3 rounded-xl shadow-lg font-cairo font-bold hover:bg-navy/90 transition-all flex items-center gap-2 group">
                <i class="fas fa-print text-gold group-hover:scale-110 transition-transform"></i>
                <span>طباعة التقرير</span>
            </button>
        </div>

        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-right">
                <thead>
                    <tr class="bg-navy text-white font-cairo">
                        <th class="px-6 py-4 font-bold">اسم الطالب</th>
                        <th class="px-6 py-4 font-bold">الرقم الجامعي</th>
                        <th class="px-6 py-4 font-bold">المركز</th>
                        <th class="px-6 py-4 font-bold">الغرفة</th>
                        <th class="px-6 py-4 font-bold">المستوى الدراسي</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($data as $student)
                        <tr class="hover:bg-blue-50/50 transition-colors">
                            <td class="px-6 py-4 font-almarai font-bold text-gray-800">{{ $student->name_ar }}</td>
                            <td class="px-6 py-4 font-almarai text-gray-600">{{ $student->university_id }}</td>
                            <td class="px-6 py-4">
                                <span
                                    class="bg-blue-100 text-blue-700 px-2 py-1 rounded-lg text-xs font-bold font-almarai">{{ $student->center->name }}</span>
                            </td>
                            <td class="px-6 py-4 font-almarai">{{ $student->room->room_number ?? '---' }}</td>
                            <td class="px-6 py-4 font-almarai">{{ $student->academic_level ?? '---' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @include('partials.print_footer')
    </div>
@endsection
