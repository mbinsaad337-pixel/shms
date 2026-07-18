@extends('layouts.app')

@section('title', 'قائمة رموز QR المجمعة')

@section('content')
    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-extrabold text-navy font-cairo">رموز QR المجمعة</h1>
                <p class="mt-1 text-sm text-gray-500 font-almarai">إدارة الرموز التي تحتوي على بيانات طلاب متعددين</p>
            </div>
            <a href="{{ route('student-qr-groups.create') }}"
                class="inline-flex items-center px-6 py-3 border border-transparent text-base font-bold rounded-xl shadow-sm text-white bg-gold hover:bg-secondary-gold focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gold transition-all duration-300 font-cairo">
                <i class="fas fa-plus-circle ml-2"></i> إنشاء رمز جديد
            </a>
        </div>

        <div class="card-premium overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider font-cairo">
                                تاريخ الإنشاء</th>
                            <th scope="col"
                                class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider font-cairo">
                                عدد الطلاب</th>
                            <th scope="col"
                                class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider font-cairo">
                                تاريخ الانتهاء</th>
                            <th scope="col"
                                class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider font-cairo">
                                النوع</th>
                            <th scope="col"
                                class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider font-cairo">
                                العمليات</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @if(is_countable($groups) ? count($groups) > 0 : (method_exists($groups, 'count') ? $groups->count() > 0 : !empty($groups)))
    @foreach($groups as $group)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-almarai">
                                    {{ $group->created_at->format('Y-m-d H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-almarai">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $group->students->count() + 1 }} طلاب
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-almarai">
                                    {{ $group->expires_at->format('Y-m-d H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-almarai">
                                    @if($group->is_link_only)
                                        <span class="text-orange-600"><i class="fas fa-link ml-1"></i> رابط</span>
                                    @else
                                        <span class="text-green-600"><i class="fas fa-file-code ml-1"></i> بيانات داخلية</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center space-x-3 space-x-reverse">
                                        <a href="{{ route('student-qr-groups.show', $group) }}"
                                            class="text-navy hover:text-blue-900 bg-blue-50 p-2 rounded-lg transition-colors">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <form action="{{ route('student-qr-groups.destroy', $group) }}" method="POST"
                                            class="inline-block" data-confirm="هل أنت متأكد من حذف هذا الرمز؟">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="text-red-600 hover:text-red-900 bg-red-50 p-2 rounded-lg transition-colors">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
@else
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center">
                                    <div class="text-gray-400 mb-2">
                                        <i class="fas fa-qrcode fa-3x"></i>
                                    </div>
                                    <p class="text-gray-500 font-almarai">لا توجد رموز مجمعة حالياً.</p>
                                    <a href="{{ route('student-qr-groups.create') }}"
                                        class="text-gold font-bold mt-2 inline-block">أنشئ أول رمز مجمع الآن</a>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            @if($groups->hasPages())
                <div class="px-6 py-4 bg-gray-50">
                    {{ $groups->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
