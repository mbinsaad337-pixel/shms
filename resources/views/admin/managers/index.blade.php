@extends('layouts.app')

@section('title', 'إدارة مدراء المراكز')

@section('content')
    <div class="container mx-auto px-6 py-8">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h2 class="text-3xl font-bold text-primary font-cairo">إدارة مدراء المراكز الطلابية</h2>
                <p class="text-gray-500 font-almarai mt-1">
                    @if ($selectedCenter)
                        مدراء مركز: <span class="font-bold text-primary">{{ $selectedCenter->name }}</span>
                        <a href="{{ route('managers.index') }}" class="mr-2 text-sm font-bold text-gold hover:underline">عرض
                            جميع المدراء</a>
                    @else
                        إضافة، تعديل، ومتابعة مدراء المواقع.
                    @endif
                </p>
            </div>
            <a href="{{ route('managers.create') }}"
                class="bg-secondary hover:bg-orange-600 text-white px-6 py-3 rounded-xl shadow-lg font-cairo transition-all transform hover:scale-105 active:scale-95">
                + إضافة مدير مركز جديد
            </a>
        </div>

        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="min-w-full text-right leading-normal">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-8 py-5 text-sm font-bold text-gray-600 font-cairo">اسم المدير</th>
                        <th class="px-8 py-5 text-sm font-bold text-gray-600 font-cairo">المركز المعين</th>
                        <th class="px-8 py-5 text-sm font-bold text-gray-600 font-cairo">البريد الإلكتروني</th>
                        <th class="px-8 py-5 text-sm font-bold text-gray-600 font-cairo text-center">الحالة</th>
                        <th class="px-8 py-5 text-sm font-bold text-gray-600 font-cairo">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($managers as $manager)
                        <tr class="hover:bg-blue-50/20 transition-colors">
                            <td class="px-8 py-5">
                                <div class="flex items-center">
                                    <div
                                        class="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center text-primary font-bold ml-3">
                                        {{ mb_substr($manager->name, 0, 1) }}
                                    </div>
                                    <span class="font-bold text-gray-800 font-almarai">{{ $manager->name }}</span>
                                </div>
                            </td>
                            <td class="px-8 py-5">
                                <span class="px-3 py-1 bg-blue-50 text-blue-700 rounded-lg text-xs font-bold font-almarai">
                                    {{ $manager->center->name ?? 'غير معين' }}
                                </span>
                            </td>
                            <td class="px-8 py-5 font-almarai text-gray-500">{{ $manager->email }}</td>
                            <td class="px-8 py-5 text-center">
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold font-almarai {{ $manager->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    <span
                                        class="w-2 h-2 rounded-full mr-2 {{ $manager->is_active ? 'bg-green-500' : 'bg-red-500' }}"></span>
                                    {{ $manager->is_active ? 'نشط' : 'معطل' }}
                                </span>
                            </td>
                            <td class="px-8 py-5">
                                <a href="{{ route('managers.edit', $manager) }}"
                                    class="text-primary hover:text-blue-900 font-bold text-sm font-cairo">تعديل</a>
                                <span class="mx-2 text-gray-300">|</span>
                                <form action="{{ route('managers.toggle', $manager) }}" method="POST" class="inline"
                                    data-confirm='{{ $manager->is_active ? 'هل أنت متأكد من تعطيل هذا الحساب؟' : 'هل تريد تفعيل هذا الحساب؟' }}'>
                                    @csrf
                                    <button type="submit"
                                        class="{{ $manager->is_active ? 'text-red-500 hover:text-red-700' : 'text-green-600 hover:text-green-800' }} font-bold text-sm font-cairo">
                                        {{ $manager->is_active ? 'تعطيل' : 'تفعيل' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
