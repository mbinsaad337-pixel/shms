@extends('layouts.app')

@section('title', 'سجل المخالفات')

@section('content')
    <div class="container mx-auto px-6 py-8">
        <!-- Filters Section -->
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm mb-8 animate-fade-in">
            <form action="{{ route('violations.index') }}" method="GET"
                class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div>
                    <label class="block text-xs font-bold text-gray-400 mb-2 font-cairo mr-2">نوع المخالفة</label>
                    <select name="type"
                        class="w-full px-5 py-3 rounded-xl border border-gray-100 bg-gray-50 text-navy font-almarai text-sm focus:ring-2 focus:ring-navy/5 outline-none">
                        <option value="">كل الأنواع</option>
                        @foreach ($types as $type)
                            <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                                {{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 mb-2 font-cairo mr-2">من تاريخ</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                        class="w-full px-5 py-3 rounded-xl border border-gray-100 bg-gray-50 text-navy font-mono text-sm focus:ring-2 focus:ring-navy/5 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 mb-2 font-cairo mr-2">إلى تاريخ</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                        class="w-full px-5 py-3 rounded-xl border border-gray-100 bg-gray-50 text-navy font-mono text-sm focus:ring-2 focus:ring-navy/5 outline-none">
                </div>
                <div class="flex gap-2">
                    <button type="submit"
                        class="flex-1 bg-navy text-white py-3 rounded-xl font-bold font-cairo hover:bg-navy-light transition-all flex items-center justify-center gap-2 shadow-lg shadow-navy/10">
                        <i class="fas fa-filter text-xs"></i>
                        <span>فلترة</span>
                    </button>
                    @if (request()->anyFilled(['type', 'date_from', 'date_to']))
                        <a href="{{ route('violations.index') }}"
                            class="w-12 h-12 bg-red-50 text-red-500 rounded-xl flex items-center justify-center hover:bg-red-500 hover:text-white transition-all shadow-sm"
                            title="إلغاء الفلترة">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Header Section -->
        <div
            class="mb-8 flex flex-col md:flex-row justify-between items-center bg-white p-8 rounded-3xl border-l-8 border-red-600 shadow-sm gap-4">
            <div>
                <h1 class="text-3xl font-black text-navy font-cairo text-right">سجل المخالفات الانضباطية</h1>
                <p class="text-gray-400 font-almarai text-sm mt-2 text-right">متابعة وإدارة السلوك الانضباطي للطلاب</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('violations.export-list') }}" target="_blank"
                    class="px-6 py-4 bg-gray-50 text-gray-500 font-black font-cairo rounded-2xl border border-gray-100 hover:bg-navy hover:text-black transition-all flex items-center gap-2 shadow-sm">
                    <i class="fas fa-file-export"></i>
                    <span>تصدير تقرير</span>
                </a>
                <a href="{{ route('violations.create') }}"
                    class="px-6 py-4 bg-red-600 text-white rounded-2xl hover:bg-red-700 font-cairo font-black transition-all flex items-center gap-2 shadow-lg shadow-red-600/20">
                    <i class="fas fa-plus-circle"></i>
                    <span>تسجيل مخالفة جديدة</span>
                </a>
            </div>
        </div>

        <!-- Statistics Cards (Optional but good for aesthetics) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm flex items-center gap-5">
                <div class="w-14 h-14 bg-red-50 rounded-2xl flex items-center justify-center text-red-600">
                    <i class="fas fa-exclamation-triangle text-2xl"></i>
                </div>
                <div>
                    <span class="block text-gray-400 text-xs font-bold font-cairo">إجمالي المخالفات</span>
                    <span class="text-2xl font-black text-navy">{{ $violations->total() }}</span>
                </div>
            </div>
            <!-- More stats can be added here if needed -->
        </div>

        <!-- Violations Table -->
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-0 overflow-x-auto">
                <table class="w-full text-right">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="px-8 py-5 text-xs font-black text-gray-400 uppercase tracking-widest font-cairo">
                                الطالب</th>
                            <th class="px-8 py-5 text-xs font-black text-gray-400 uppercase tracking-widest font-cairo">نوع
                                المخالفة</th>
                            <th
                                class="px-8 py-5 text-xs font-black text-gray-400 uppercase tracking-widest font-cairo text-center">
                                المستوى</th>
                            <th
                                class="px-8 py-5 text-xs font-black text-gray-400 uppercase tracking-widest font-cairo text-center">
                                التاريخ</th>
                            <th
                                class="px-8 py-5 text-xs font-black text-gray-400 uppercase tracking-widest font-cairo text-center">
                                بواسطة</th>
                            <th
                                class="px-8 py-5 text-xs font-black text-gray-400 uppercase tracking-widest font-cairo text-center">
                                الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @if (count($violations) > 0)
                            @foreach ($violations as $violation)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-4">
                                            <div
                                                class="w-10 h-10 bg-navy/5 rounded-xl flex items-center justify-center text-navy font-bold text-sm">
                                                {{ mb_substr($violation->student->name_ar, 0, 1) }}
                                            </div>
                                            <div class="flex flex-col">
                                                <span
                                                    class="text-navy font-bold font-almarai">{{ $violation->student->name_ar }}</span>
                                                <span
                                                    class="text-[10px] text-gray-400 font-mono tracking-widest">{{ $violation->student->student_number }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <span
                                            class="text-sm font-bold text-gray-600 font-cairo">{{ $violation->type }}</span>
                                        <p class="text-[10px] text-gray-400 mt-1 line-clamp-1 max-w-xs">
                                            {{ $violation->description }}</p>
                                    </td>
                                    <td class="px-8 py-6 text-center">
                                        @php
                                            $severityClasses = [
                                                'minor' => 'bg-blue-50 text-blue-600',
                                                'moderate' => 'bg-orange-50 text-orange-600',
                                                'severe' => 'bg-red-50 text-red-600',
                                            ];
                                            $severityLabels = [
                                                'minor' => 'بسيطة',
                                                'moderate' => 'متوسطة',
                                                'severe' => 'جسيمة',
                                            ];
                                        @endphp
                                        <span
                                            class="px-3 py-1.5 rounded-lg text-xs font-black {{ $severityClasses[$violation->severity] ?? 'bg-gray-100 text-gray-600' }}">
                                            {{ $severityLabels[$violation->severity] ?? $violation->severity }}
                                        </span>
                                    </td>
                                    <td class="px-8 py-6 text-center">
                                        <span class="text-xs font-black text-gray-500 font-mono">
                                            {{ $violation->violation_date->format('Y-m-d') }}
                                        </span>
                                    </td>
                                    <td class="px-8 py-6 text-center">
                                        <span
                                            class="text-xs font-bold text-navy font-cairo">{{ $violation->recordedBy->name ?? '---' }}</span>
                                    </td>
                                    <td class="px-8 py-6 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="{{ route('violations.export', $violation->id) }}" target="_blank"
                                                class="w-9 h-9 bg-gray-50 text-gray-400 rounded-lg flex items-center justify-center hover:bg-navy hover:text-white transition-all"
                                                title="طباعة التقرير">
                                                <i class="fas fa-print text-xs"></i>
                                            </a>
                                            <a href="{{ route('violations.show', $violation->id) }}"
                                                class="w-9 h-9 bg-navy/5 text-navy rounded-lg flex items-center justify-center hover:bg-navy hover:text-white transition-all">
                                                <i class="fas fa-eye text-xs"></i>
                                            </a>
                                            @can('manage-violations')
                                                <form action="{{ route('violations.destroy', $violation->id) }}" method="POST"
                                                    onsubmit="return confirm('هل أنت متأكد من حذف هذه المخالفة؟')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="w-9 h-9 bg-red-50 text-red-500 rounded-lg flex items-center justify-center hover:bg-red-500 hover:text-white transition-all">
                                                        <i class="fas fa-trash-alt text-xs"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6" class="px-8 py-20 text-center">
                                    <div
                                        class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 opacity-30">
                                        <i class="fas fa-shield-check text-4xl text-gray-400"></i>
                                    </div>
                                    <h4 class="text-navy font-black font-cairo">لا توجد مخالفات مسجلة</h4>
                                    <p class="text-gray-400 font-almarai text-xs mt-2 italic">السجل نظيف تماماً حتى الآن</p>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            @if ($violations->hasPages())
                <div class="px-8 py-6 border-t border-gray-50 bg-gray-50/30">
                    {{ $violations->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
