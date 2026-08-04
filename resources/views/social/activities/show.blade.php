@extends('layouts.app')

@section('title', 'تفاصيل الفعالية')

@section('content')
    <div class="container mx-auto px-6 py-8">
        <!-- Header Section -->
        <div class="mb-8 flex justify-between items-center bg-white p-8 rounded-3xl border-l-8 border-navy shadow-sm">
            <div>
                <h1 class="text-3xl font-black text-navy font-cairo text-right">{{ $activity->name }}</h1>
                <p class="text-gray-400 font-almarai text-sm mt-2 text-right">نادي: {{ $activity->club->name ?? 'نادي عام' }}</p>
            </div>
            <div class="flex gap-4">
                @if(!auth()->user()->hasRole('super-admin') && !auth()->user()->hasRole('activity-assistant'))
                <form action="{{ route('activities.update-status', $activity->id) }}" method="POST" class="relative">
                    @csrf
                    @method('PATCH')
                    <select name="status" onchange="this.form.submit()" class="pl-10 pr-6 py-3 bg-white rounded-2xl font-cairo font-bold transition-all border border-gray-200 shadow-sm appearance-none cursor-pointer focus:ring-0
                        @if($activity->status == 'planned') text-blue-700 
                        @elseif($activity->status == 'published') text-green-700
                        @elseif($activity->status == 'cancelled') text-red-700
                        @else text-gray-500 @endif">
                        <option value="planned" {{ $activity->status == 'planned' ? 'selected' : '' }}>مجدولة</option>
                        <option value="published" {{ $activity->status == 'published' ? 'selected' : '' }}>مستمرة</option>
                        <option value="completed" {{ $activity->status == 'completed' ? 'selected' : '' }}>منتهية</option>
                        <option value="cancelled" {{ $activity->status == 'cancelled' ? 'selected' : '' }}>ملغاة</option>
                    </select>
                    <i class="fas fa-chevron-down absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none text-xs"></i>
                </form>

                <a href="{{ route('activities.edit', $activity->id) }}"
                    class="px-6 py-3 bg-gold/10 text-gold rounded-2xl hover:bg-gold hover:text-white font-cairo font-bold transition-all flex items-center gap-2 border border-gold/10 shadow-sm">
                    <i class="fas fa-edit"></i>
                    <span>تعديل الفعالية</span>
                </a>
                @endif
                <a href="{{ route('activities.index') }}"
                    class="px-6 py-3 bg-gray-50 text-navy rounded-2xl hover:bg-gray-100 font-cairo font-bold transition-all flex items-center gap-2 border border-gray-100">
                    <i class="fas fa-arrow-right"></i>
                    <span>رجوع للقائمة</span>
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Details Column -->
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white rounded-[2rem] p-8 shadow-sm border border-gray-50">
                    <h3 class="text-xl font-black text-navy font-cairo mb-6 border-b pb-4">معلومات الفعالية</h3>
                    
                    <div class="space-y-6">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600">
                                <i class="fas fa-calendar-day text-lg"></i>
                            </div>
                            <div>
                                <span class="block text-xs text-gray-400 font-bold uppercase font-cairo">الفترة الزمنية</span>
                                <span class="text-navy font-black text-sm">
                                    {{ $activity->start_date?->format('Y-m-d') }}
                                    @if($activity->end_date && $activity->end_date != $activity->start_date)
                                         - {{ $activity->end_date?->format('Y-m-d') }}
                                    @endif
                                </span>
                            </div>
                        </div>

                        @if($activity->start_time)
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-pink-50 rounded-xl flex items-center justify-center text-pink-600">
                                <i class="fas fa-clock text-lg"></i>
                            </div>
                            <div>
                                <span class="block text-xs text-gray-400 font-bold uppercase font-cairo">التوقيت</span>
                                <span class="text-navy font-black text-sm">
                                    {{ $activity->start_time }}
                                    @if($activity->end_time)
                                         - {{ $activity->end_time }}
                                    @endif
                                </span>
                            </div>
                        </div>
                        @endif

                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-purple-50 rounded-xl flex items-center justify-center text-purple-600">
                                <i class="fas fa-location-dot text-lg"></i>
                            </div>
                            <div>
                                <span class="block text-xs text-gray-400 font-bold uppercase font-cairo">الموقع</span>
                                <span class="text-navy font-black text-lg">{{ $activity->location }}</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center text-green-600">
                                <i class="fas fa-users-viewfinder text-lg"></i>
                            </div>
                            <div>
                                <span class="block text-xs text-gray-400 font-bold uppercase font-cairo">المشاركون الملحقون</span>
                                <span class="text-navy font-black text-lg">{{ $activity->participants->count() }} / {{ $activity->targetedStudents->count() ?: ($activity->max_participants ?? '∞') }}</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-orange-50 rounded-xl flex items-center justify-center text-orange-600">
                                <i class="fas fa-id-badge text-lg"></i>
                            </div>
                            <div>
                                <span class="block text-xs text-gray-400 font-bold uppercase font-cairo">الحالة</span>
                                <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-lg text-xs font-black uppercase tracking-wider">
                                    @if($activity->status == 'planned') مجدولة
                                    @elseif($activity->status == 'published') منشورة
                                    @elseif($activity->status == 'completed') مكتملة
                                    @else ملغاة @endif
                                </span>
                            </div>
                            <div>
                                <span class="block text-xs text-gray-400 font-bold uppercase font-cairo">الجمهور المستهدف</span>
                                <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-lg text-xs font-black uppercase tracking-wider">
                                    {{ $activity->target_audience ?? 'غير محدد' }}
                                </span>
                            </div>
                            
                        </div>
                    </div>

                    @if(!auth()->user()->hasRole('super-admin'))
                    <div class="mt-10 pt-8 border-t border-gray-50 flex flex-col gap-3">
                        <form action="{{ route('activities.destroy', $activity->id) }}" method="POST" data-confirm="حذف الفعالية سيؤدي لإزالة سجل الحضور أيضاً. هل أنت متأكد؟">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full py-4 bg-red-50 text-red-500 rounded-2xl font-bold font-cairo hover:bg-red-500 hover:text-white transition-all flex items-center justify-center gap-2">
                                <i class="fas fa-trash-alt text-xs"></i>
                                <span>حذف الفعالية</span>
                            </button>
                        </form>
                    </div>
                    @endif
                </div>

                <!-- Targeted Students Summary -->
                @if($activity->targetedStudents->count() > 0)
                <div class="bg-navy rounded-[2rem] p-8 shadow-xl text-white">
                    <h3 class="text-lg font-black font-cairo mb-4 flex items-center gap-2">
                        <i class="fas fa-bullseye text-gold"></i>
                        <span>الطلاب المستهدفون</span>
                    </h3>
                    <div class="space-y-2 max-h-48 overflow-y-auto pr-2 custom-scrollbar">
                        @foreach($activity->targetedStudents as $target)
                            <div class="flex items-center justify-between py-2 border-b border-white/10 last:border-0 text-sm">
                                <span>{{ $target->name_ar }}</span>
                                <span class="text-[10px] opacity-60">{{ $target->student_number }}</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4 pt-4 border-t border-white/10 text-xs text-center opacity-70">
                        تم استهداف {{ $activity->targetedStudents->count() }} طالب بشكل مباشر
                    </div>
                </div>
                @endif
            </div>

            <!-- Participants Column -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Participants Table -->
                <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-8 py-6 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-xl font-black text-navy font-cairo">سجل حضور الطلاب</h3>
                        <span class="px-4 py-2 bg-navy text-white rounded-xl text-xs font-black">{{ $activity->participants->count() }} طالب</span>
                    </div>

                    <div class="p-0 overflow-x-auto">
                        <table class="w-full text-right">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-8 py-4 text-xs font-black text-gray-400 uppercase tracking-widest font-cairo">اسم الطالب</th>
                                    <th class="px-8 py-4 text-xs font-black text-gray-400 uppercase tracking-widest font-cairo">الرقم الجامعي</th>
                                    <th class="px-8 py-4 text-xs font-black text-gray-400 uppercase tracking-widest font-cairo text-center">وقت التحضير</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @if ( $activity->participants->count() > 0 )
                                    @foreach ( $activity->participants as $participant )
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-8 py-5">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 bg-navy/5 rounded-xl flex items-center justify-center text-navy font-bold text-sm">
                                                    {{ mb_substr($participant->student->name_ar, 0, 1) }}
                                                </div>
                                                <span class="text-navy font-bold font-almarai">{{ $participant->student->name_ar }}</span>
                                            </div>
                                        </td>
                                        <td class="px-8 py-5 text-gray-500   text-sm tracking-widest">
                                            {{ $participant->student->student_number }}
                                        </td>
                                        <td class="px-8 py-5 text-center">
                                            <span class="px-3 py-1.5 bg-green-50 text-green-600 rounded-lg text-xs font-black  ">
                                                {{ $participant->registered_at?->format('H:i:s') ?? '--:--:--' }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="3" class="px-8 py-20 text-center">
                                            <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 opacity-30">
                                                <i class="fas fa-user-slash text-4xl text-gray-400"></i>
                                            </div>
                                            <h4 class="text-navy font-black font-cairo">لا يوجد طلاب محضرين حتى الآن</h4>
                                            <p class="text-gray-400 font-almarai text-xs mt-2 italic">ابدأ بمسح الباركود من القائمة الرئيسية لتسجيل الحضور</p>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Absent Students Table & Violation Form -->
                @php
                    $participantIds = $activity->participants->pluck('student_id')->toArray();
                    $absentStudents = $activity->targetedStudents->filter(function($student) use ($participantIds) {
                        return !in_array($student->id, $participantIds);
                    });
                @endphp
                
                @if($activity->targetedStudents->count() > 0)
                <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-8 py-6 bg-red-50 border-b border-red-100 flex justify-between items-center">
                        <div>
                            <h3 class="text-xl font-black text-red-600 font-cairo">الطلاب الغائبون عن الفعالية</h3>
                            <span class="text-xs text-red-400 font-bold opacity-70">{{ $absentStudents->count() }} طالب مستهدف لم يحضر</span>
                        </div>
                        <div class="flex gap-3">
                            <a href="{{ route('activities.export-absentees', $activity->id) }}"
                                class="px-4 py-2 bg-white text-red-600 border border-red-200 rounded-xl text-xs font-black shadow-sm hover:bg-red-600 hover:text-white transition-all flex items-center gap-2">
                                <i class="fas fa-file-pdf"></i>
                                <span>تصدير كـ PDF</span>
                            </a>
                            <span class="hidden md:inline-flex px-4 py-2 bg-red-600 text-white rounded-xl text-xs font-black items-center">
                                سجل الغياب
                            </span>
                        </div>
                    </div>

                    <form action="{{ route('violations.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="type" value="غياب عن نشاط مباشر">
                        <input type="hidden" name="description" value="التغيب عن حضور الفعالية/النشاط: {{ $activity->name }} (تاريخ: {{ $activity->start_date?->format('Y-m-d') }})">
                        <input type="hidden" name="violation_date" value="{{ $activity->start_date?->format('Y-m-d') ?? now()->format('Y-m-d') }}">
                        
                        @if($absentStudents->count() > 0 && !auth()->user()->hasRole('super-admin'))
                        <div class="p-6 border-b border-gray-50 flex items-end gap-6 bg-white">
                            <div class="flex-[2]">
                                <label class="block text-sm font-black text-navy mb-3 font-cairo text-right">اختر مستوى المخالفة للطلاب المحددين</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                        <i class="fas fa-gavel text-gray-400"></i>
                                    </div>
                                    <select name="severity" required
                                        class="w-full pl-5 pr-12 py-4 rounded-xl border-2 border-gray-100 bg-gray-50 focus:bg-white focus:border-red-300 outline-none text-right font-almarai appearance-none transition-all">
                                        <option value="">-- حدد العقوبة الانضباطية --</option>
                                        <option value="minor">مخالفة بسيطة (تنبيه / لفت نظر)</option>
                                        <option value="moderate">مخالفة متوسطة (إنذار / حرمان من بعض الامتيازات)</option>
                                        <option value="severe">مخالفة جسيمة (استدعاء ولي الأمر / فصل)</option>
                                    </select>
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                                        <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="flex-1 px-8 py-4 bg-red-600 text-white rounded-xl font-black font-cairo hover:bg-red-700 shadow-lg shadow-red-600/30 transition-all flex items-center justify-center gap-3">
                                <i class="fas fa-file-signature"></i>
                                <span>تسجيل المخالفة</span>
                            </button>
                        </div>
                        @endif

                        <div class="p-0 overflow-x-auto">
                            <table class="w-full text-right">
                                <thead>
                                    <tr class="bg-gray-50">
                                        <th class="px-8 py-4 w-16 text-center border-l border-gray-100">
                                            <input type="checkbox" id="selectAllAbsents" class="w-5 h-5 rounded border-gray-300 text-red-600 focus:ring-red-600 cursor-pointer">
                                        </th>
                                        <th class="px-8 py-4 text-xs font-black text-gray-400 uppercase tracking-widest font-cairo">اسم الطالب</th>
                                        <th class="px-8 py-4 text-xs font-black text-gray-400 uppercase tracking-widest font-cairo">الرقم الجامعي</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @if($absentStudents->count() > 0)
                                        @foreach($absentStudents as $student)
                                            <tr class="hover:bg-red-50/50 transition-colors">
                                                <td class="px-8 py-5 text-center border-l border-gray-50">
                                                    <input type="checkbox" name="student_id[]" value="{{ $student->id }}" class="absent-checkbox w-5 h-5 rounded border-gray-300 text-red-600 focus:ring-red-600 cursor-pointer">
                                                </td>
                                                <td class="px-8 py-5">
                                                    <div class="flex items-center gap-4">
                                                        <div class="w-10 h-10 bg-red-100 text-red-600 rounded-xl flex items-center justify-center font-bold text-sm shadow-inner mt-1">
                                                            {{ mb_substr($student->name_ar, 0, 1) }}
                                                        </div>
                                                        <div class="flex flex-col">
                                                            <span class="text-navy font-bold font-almarai">{{ $student->name_ar }}</span>
                                                            <span class="text-[10px] text-gray-400 font-bold mt-1">لم يحضر</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-8 py-5 text-gray-500   text-sm tracking-widest">
                                                    {{ $student->student_number }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="3" class="px-8 py-16 text-center">
                                                <div class="w-20 h-20 bg-green-50 rounded-full flex items-center justify-center mx-auto mb-5 text-green-500 shadow-sm border border-green-100">
                                                    <i class="fas fa-check-double text-4xl"></i>
                                                </div>
                                                <h4 class="text-navy text-xl font-black font-cairo">جميع المستهدفين حاضرون</h4>
                                                <p class="text-gray-400 font-almarai mt-2 italic">لا توجد حالات غياب عن هذه الفعالية</p>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </form>
                </div>
                @endif
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const selectAll = document.getElementById('selectAllAbsents');
                    const checkboxes = document.querySelectorAll('.absent-checkbox');
                    
                    if(selectAll) {
                        selectAll.addEventListener('change', function() {
                            checkboxes.forEach(cb => cb.checked = this.checked);
                        });
                    }
                });
            </script>
        </div>
    </div>
    
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: rgba(255,255,255,0.05); }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 10px; }
    </style>
@endsection
