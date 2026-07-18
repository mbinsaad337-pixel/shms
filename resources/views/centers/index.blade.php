@extends('layouts.app')

@section('title', 'إدارة المراكز الطلابية')

@section('content')
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800">قائمة المراكز</h1>
        <a href="{{ route('centers.create') }}" class="btn-secondary">
            + إضافة مركز جديد
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100">
        <table class="min-w-full divide-y divide-gray-200 text-right">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">المركز</th>
                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">العنوان</th>
                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">الطلاب</th>
                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">الغرف</th>
                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($centers as $center)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="h-10 w-10 flex-shrink-0">
                                    <img class="h-10 w-10 rounded-full object-cover border"
                                        src="{{ $center->logo ? asset('storage/' . $center->logo) : 'https://ui-avatars.com/api/?name=' . urlencode($center->name) . '&background=0f172a&color=fff' }}"
                                        alt="">
                                </div>
                                <div class="mr-4">
                                    <div class="text-sm font-bold text-gray-900">{{ $center->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $center->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $center->address }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                            {{ $center->students_count }} طالب
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                            {{ $center->rooms_count }} غرفة
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($center->is_active)
                                <span
                                    class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">نشط</span>
                            @else
                                <span
                                    class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">غير
                                    نشط</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('centers.show', $center) }}"
                                class="text-navy hover:text-gold ml-3">عرض</a>
                            <a href="{{ route('centers.edit', $center) }}"
                                class="text-primary hover:text-secondary ml-3">تعديل</a>
                            <a href="#" class="text-gray-400 hover:text-red-600">حذف</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
