@extends('layouts.app')

@section('title', 'تفاصيل الجلسة - ' . ($session->title ?? 'جلسة اعتيادية'))

@section('content')
    <div class="mb-8 flex justify-between items-center bg-white p-6 rounded-2xl border-l-8 border-gold shadow-sm">
        <div>
            <h1 class="text-3xl font-black text-navy font-cairo lowercase">{{ $session->title ?? 'جلسة اعتيادية' }}</h1>
            <p class="text-gray-400 font-almarai text-sm mt-1">الحلقة: <span class="text-gold font-bold">{{ $session->circle->name }}</span> | التاريخ: <span class="font-bold text-navy">{{ $session->session_date->format('Y-m-d') }}</span></p>
        </div>
        <div>
            <a href="{{ route('quran-circles.show', $session->circle) }}"
                class="px-6 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 shadow-sm font-cairo font-bold transition-all flex items-center gap-2">
                <i class="fas fa-arrow-right"></i>
                <span>العودة للحلقة</span>
            </a>
        </div>
    </div>

    @if($session->notes)
        <div class="bg-blue-50/50 border-r-4 border-blue-400 p-6 rounded-xl mb-8">
            <h4 class="text-sm font-bold text-blue-800 font-cairo mb-2 flex items-center gap-2">
                <i class="fas fa-sticky-note"></i> ملاحظات المعلم
            </h4>
            <p class="text-blue-700 font-almarai text-sm leading-relaxed">{{ $session->notes }}</p>
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-50 flex justify-between items-center bg-gray-50/50">
            <h2 class="text-xl font-bold text-navy font-cairo">سجل الحضور والتقدم</h2>
            <div class="flex gap-4">
                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold font-cairo">{{ $session->attendance()->where('status', 'present')->count() }} حاضر</span>
                <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-bold font-cairo">{{ $session->attendance()->where('status', 'absent')->count() }} غائب</span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 text-right">
                <thead class="bg-gray-50/20">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider font-cairo">الطالب</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider font-cairo">الحالة</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider font-cairo">التقدم المسجل</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-50">
                    @foreach($session->attendance as $attendance)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="h-8 w-8 rounded-full bg-navy/10 flex items-center justify-center text-navy font-bold text-xs uppercase">{{ mb_substr($attendance->student->name_ar, 0, 1) }}</div>
                                    <div class="text-sm font-bold text-navy font-cairo">{{ $attendance->student->name_ar }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($attendance->status == 'present')
                                    <span class="flex items-center gap-1.5 text-green-600 font-bold font-cairo text-xs">
                                        <i class="fas fa-check-circle"></i> حاضر
                                    </span>
                                @else
                                    <span class="flex items-center gap-1.5 text-red-400 font-bold font-cairo text-xs opacity-60">
                                        <i class="fas fa-times-circle"></i> غائب
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap font-almarai text-xs text-gray-600">
                                @if($attendance->sura)
                                    <span class="bg-navy/5 px-2 py-1 rounded border border-navy/5">سورة {{ $attendance->sura }} | آية {{ $attendance->verse ?? '—' }}</span>
                                @else
                                    <span class="text-gray-300 italic">لا يوجد</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
