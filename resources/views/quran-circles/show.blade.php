@extends('layouts.app')

@section('title', 'تفاصيل الحلقة - ' . $quranCircle->name)

@section('content')
    <div class="mb-8 flex flex-col md:flex-row gap-6 justify-between items-start md:items-center bg-white p-6 rounded-2xl border-l-8 border-gold shadow-sm">
        <div class="flex items-center gap-4">
            <a href="{{ route('quran-circles.index') }}" class="w-10 h-10 bg-white rounded-full shadow-sm flex items-center justify-center text-gray-400 hover:text-navy transition-all border border-gray-100">
                <i class="fas fa-arrow-right"></i>
            </a>
            <div class="bg-navy p-4 rounded-xl text-gold shadow-md">
                <i class="fas fa-quran text-3xl"></i>
            </div>
            <div>
                <h1 class="text-3xl font-black text-navy font-cairo lowercase">{{ $quranCircle->name }}</h1>
                <p class="text-gray-400 font-almarai text-sm mt-1">المدرس: <span class="text-gold font-bold">{{ $quranCircle->teacher->name }}</span></p>
            </div>
        </div>
        <div class="flex flex-col sm:flex-row w-full md:w-auto gap-3">
            @if(auth()->id() == $quranCircle->teacher_id || auth()->user()->can('manage-quran-circles'))
                <a href="{{ route('quran-circles.edit', $quranCircle) }}" class="w-full sm:w-auto justify-center px-6 py-3 bg-white text-blue-600 border-2 border-blue-100 rounded-xl hover:bg-blue-50 shadow-sm font-cairo font-bold transition-all flex items-center gap-2">
                    <i class="fas fa-edit"></i>
                    <span>تعديل الحلقة</span>
                </a>
                <a href="{{ route('quran-circles.students', $quranCircle) }}" class="w-full sm:w-auto justify-center px-6 py-3 bg-white text-navy border-2 border-navy/10 rounded-xl hover:bg-navy hover:text-white shadow-sm font-cairo font-bold transition-all flex items-center gap-2">
                    <i class="fas fa-users-cog"></i>
                    <span>إدارة طلاب الحلقة</span>
                </a>
                <a href="{{ route('circle-sessions.create', $quranCircle) }}" class="w-full sm:w-auto justify-center px-6 py-3 bg-gold text-navy rounded-xl hover:bg-gold/90 shadow-lg font-cairo font-bold transition-all transform hover:-translate-y-1 flex items-center gap-2">
                    <i class="fas fa-plus-circle"></i>
                    <span>تسجيل جلسة حضور</span>
                </a>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-100 border-r-4 border-green-500 text-green-700 font-cairo rounded-xl shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Students List -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-50 flex justify-between items-center">
                    <h2 class="text-xl font-bold text-navy font-cairo">قائمة الطلاب المسجلين</h2>
                    <span class="bg-navy/5 text-navy px-3 py-1 rounded-full text-xs font-bold font-cairo">{{ $quranCircle->students->count() }} طالب</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 text-right">
                        <thead class="bg-gray-50/50">
                            <tr>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider font-cairo">الطالب</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider font-cairo">رقم الغرفة</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider font-cairo">آخر تقدم</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider font-cairo">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-50">
                            @if (count($quranCircle->students) > 0)
                                @foreach ($quranCircle->students as $student)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center gap-3">
                                                <div class="h-8 w-8 rounded-full bg-navy/10 flex items-center justify-center text-navy font-bold text-xs">
                                                    {{ mb_substr($student->name_ar, 0, 1) }}
                                                </div>
                                                <div>
                                                    <div class="text-sm font-bold text-navy font-cairo">{{ $student->name_ar }}</div>
                                                    <div class="text-[10px] text-gray-400 font-almarai italic opacity-60">ID: {{ $student->student_number }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-almarai">
                                            {{ $student->room->number ?? '—' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-600 font-almarai">
                                            @if($student->pivot->last_sura)
                                                <span class="text-navy font-medium opacity-80 border-b border-navy/10 pb-0.5">سورة {{ $student->pivot->last_sura }} ({{ $student->pivot->last_verse }})</span>
                                            @else
                                                <span class="text-gray-300 italic text-[10px]">بدون بيانات</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('students.show', $student) }}" class="text-navy hover:text-gold transition-colors ml-3" title="عرض السجل الكامل">
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-300 font-almarai italic opacity-50">
                                        لا يوجد طلاب مضافين لهذه الحلقة حتى الآن
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sidebar Actions & Recent Sessions -->
        <div class="space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-navy font-cairo mb-4 flex items-center gap-2">
                    <i class="fas fa-history text-gold"></i>
                    آخر الجلسات
                </h3>
                <div class="space-y-4">
                    @if (count($quranCircle->sessions) > 0)
                        @foreach ($quranCircle->sessions as $session)
                            <a href="{{ route('circle-sessions.show', $session) }}" class="block p-4 border border-gray-50 rounded-xl hover:bg-gray-50 transition-all">
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-sm font-bold text-navy font-cairo">{{ $session->title ?? 'جلسة اعتيادية' }}</span>
                                    <span class="text-[10px] text-gray-400 font-almarai">{{ $session->session_date->format('Y-m-d') }}</span>
                                </div>
                                <div class="flex items-center text-[10px] text-gray-500 font-almarai">
                                    <span class="bg-green-50 text-green-700 font-bold px-2 py-0.5 rounded ml-2">{{ $session->attendance()->where('status', 'present')->count() }} حاضر</span>
                                    <span class="bg-red-50 text-red-700 font-bold px-2 py-0.5 rounded">{{ $session->attendance()->where('status', 'absent')->count() }} غائب</span>
                                </div>
                            </a>
                        @endforeach
                    @else
                        <p class="text-gray-400 text-sm italic font-almarai text-center py-4 opacity-50">لم يتم تسجيل أي جلسات مؤخراً</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
