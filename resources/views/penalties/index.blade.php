@extends('layouts.app')

@section('title', 'سجل العقوبات المسندة')

@section('content')
    <div class="container mx-auto px-6 py-8">
        <!-- Header Section -->
        <div class="mb-8 flex flex-col md:flex-row justify-between items-center bg-white p-8 rounded-3xl border-l-8 border-orange-500 shadow-sm gap-4">
            <div>
                <h1 class="text-3xl font-black text-navy font-cairo text-right">سجل العقوبات الانضباطية</h1>
                <p class="text-gray-400 font-almarai text-sm mt-2 text-right">متابعة العقوبات المفروضة على الطلاب وحالتها</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('penalties.export-list') }}" target="_blank"
                   class="px-6 py-4 bg-gray-50 text-gray-500 font-black font-cairo rounded-2xl border border-gray-100 hover:bg-navy hover:text-white transition-all flex items-center gap-2 shadow-sm">
                    <i class="fas fa-file-export text-sm"></i>
                    <span>تصدير تقرير</span>
                </a>
                <a href="{{ route('penalties.create') }}" 
                   class="px-8 py-4 bg-orange-600 text-white font-black font-cairo rounded-2xl shadow-xl shadow-orange-600/20 hover:bg-orange-700 hover:-translate-y-1 transition-all flex items-center gap-3">
                    <i class="fas fa-plus-circle"></i>
                    <span>إسناد عقوبة</span>
                </a>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm flex items-center gap-5">
                <div class="w-14 h-14 bg-orange-50 rounded-2xl flex items-center justify-center text-orange-600">
                    <i class="fas fa-gavel text-2xl"></i>
                </div>
                <div>
                    <span class="block text-gray-400 text-xs font-bold font-cairo">إجمالي العقوبات</span>
                    <span class="text-2xl font-black text-navy">{{ $penalties->total() }}</span>
                </div>
            </div>
            <!-- Additional stats can be added -->
        </div>

        <!-- Penalties Table -->
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-0 overflow-x-auto">
                <table class="w-full text-right">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="px-8 py-5 text-xs font-black text-gray-400 uppercase tracking-widest font-cairo">الطالب</th>
                            <th class="px-8 py-5 text-xs font-black text-gray-400 uppercase tracking-widest font-cairo">نوع العقوبة</th>
                            <th class="px-8 py-5 text-xs font-black text-gray-400 uppercase tracking-widest font-cairo text-center">المخالفة المرتبطة</th>
                            <th class="px-8 py-5 text-xs font-black text-gray-400 uppercase tracking-widest font-cairo text-center">الفترة</th>
                            <th class="px-8 py-5 text-xs font-black text-gray-400 uppercase tracking-widest font-cairo text-center">الحالة</th>
                            <th class="px-8 py-5 text-xs font-black text-gray-400 uppercase tracking-widest font-cairo text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @if($penalties->count() > 0)
                            @foreach($penalties as $penalty)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-4">
                                            <div class="w-10 h-10 bg-navy/5 rounded-xl flex items-center justify-center text-navy font-bold text-sm">
                                                {{ mb_substr($penalty->student->name_ar, 0, 1) }}
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="text-navy font-bold font-almarai">{{ $penalty->student->name_ar }}</span>
                                                <span class="text-[10px] text-gray-400   tracking-widest">{{ $penalty->student->student_number }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        @php
                                            $penaltyTypes = [
                                                'verbal_warning' => 'تنبيه شفوي',
                                                'written_warning' => 'إنذار كتابي',
                                                'service_suspension' => 'حرمان من الخدمات',
                                                'temporary_suspension' => 'فصل مؤقت',
                                                'expulsion' => 'فصل نهائي'
                                            ];
                                        @endphp
                                        <span class="text-sm font-bold text-navy font-cairo">{{ $penaltyTypes[$penalty->type] ?? $penalty->type }}</span>
                                        <p class="text-[10px] text-gray-400 mt-1 line-clamp-1 max-w-xs">{{ $penalty->description }}</p>
                                    </td>
                                    <td class="px-8 py-6 text-center">
                                        @if($penalty->violation_id)
                                            <a href="{{ route('violations.show', $penalty->violation_id) }}" class="text-xs font-bold text-blue-600 hover:underline font-cairo">
                                                 عرض المخالفة <i class="fas fa-external-link-alt text-[8px] mr-1"></i>
                                            </a>
                                        @else
                                            <span class="text-xs text-gray-400 italic">بدون مخالفة مسجلة</span>
                                        @endif
                                    </td>
                                    <td class="px-8 py-6 text-center   text-[10px] text-gray-500">
                                        {{ $penalty->start_date?->format('Y-m-d') ?? '---' }} <br>
                                        <span class="text-red-400">{{ $penalty->end_date?->format('Y-m-d') ?? 'مفتوح' }}</span>
                                    </td>
                                    <td class="px-8 py-6 text-center">
                                        @if($penalty->is_active)
                                            <span class="px-3 py-1 bg-green-50 text-green-600 rounded-lg text-[10px] font-black uppercase">سارية</span>
                                        @else
                                            <span class="px-3 py-1 bg-gray-50 text-gray-400 rounded-lg text-[10px] font-black uppercase">منتهية</span>
                                        @endif
                                    </td>
                                    <td class="px-8 py-6 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <form action="{{ route('penalties.destroy', $penalty->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من إلغاء هذه العقوبة؟')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="w-9 h-9 bg-red-50 text-red-500 rounded-xl flex items-center justify-center hover:bg-red-500 hover:text-white transition-all shadow-sm">
                                                    <i class="fas fa-trash-alt text-xs"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6" class="px-8 py-20 text-center">
                                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 opacity-30">
                                        <i class="fas fa-shield-slash text-4xl text-gray-400"></i>
                                    </div>
                                    <h4 class="text-navy font-black font-cairo">لا توجد عقوبات مسندة حالياً</h4>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            @if ($penalties->hasPages())
                <div class="px-8 py-6 border-t border-gray-50">
                    {{ $penalties->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
