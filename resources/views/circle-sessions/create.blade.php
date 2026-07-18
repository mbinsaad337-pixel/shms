@extends('layouts.app')

@section('title', 'تسجيل جلسة حضور - ' . $circle->name)

@section('content')
    <div
        class="mb-8 flex flex-col md:flex-row justify-between items-center bg-white p-6 rounded-2xl border-l-8 border-gold shadow-sm">
        <div>
            <h1 class="text-3xl font-black text-navy font-cairo lowercase">تسجيل جلسة حضور</h1>
            <p class="text-gray-400 font-almarai text-sm mt-1">الحلقة: <span
                    class="text-gold font-bold">{{ $circle->name }}</span> | التاريخ: <span
                    class="font-bold text-navy">{{ now()->format('Y-m-d') }}</span></p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="{{ route('quran-circles.show', $circle) }}"
                class="px-6 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 shadow-sm font-cairo font-bold transition-all flex items-center gap-2">
                <i class="fas fa-arrow-right"></i>
                <span>العودة للتفاصيل</span>
            </a>
        </div>
    </div>

    <form action="{{ route('circle-sessions.store', $circle) }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Attendance List -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-50 flex justify-between items-center">
                        <h2 class="text-xl font-bold text-navy font-cairo">قائمة حضور الطلاب</h2>
                        <div class="flex items-center gap-2 text-sm text-gray-400 font-almarai">
                            <i class="fas fa-info-circle text-navy opacity-40"></i>
                            <span>حدد الطلاب الحاضرين فقط</span>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100 text-right">
                            <thead class="bg-gray-50/50">
                                <tr>
                                    <th
                                        class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider font-cairo">
                                        الطالب</th>
                                    <th
                                        class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider font-cairo">
                                        الحالة</th>
                                    <th
                                        class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider font-cairo">
                                        تحديث التقدم (اختياري)</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-50">
                                @if(is_countable($circle->students) ? count($circle->students) > 0 : (method_exists($circle->students, 'count') ? $circle->students->count() > 0 : !empty($circle->students)))
    @foreach($circle->students as $student)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="h-8 w-8 rounded-full bg-navy/10 flex items-center justify-center text-navy font-bold text-xs">
                                                    {{ mb_substr($student->name_ar, 0, 1) }}
                                                </div>
                                                <div>
                                                    <div class="text-sm font-bold text-navy font-cairo">{{ $student->name_ar }}
                                                    </div>
                                                    <div class="text-[10px] text-gray-500 font-almarai italic opacity-60">ID:
                                                        {{ $student->student_number }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <label class="relative inline-flex items-center cursor-pointer">
                                                <input type="checkbox" name="marked_present[]" value="{{ $student->id }}"
                                                    class="peer sr-only" checked>
                                                <div
                                                    class="w-20 h-8 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-gold rounded-full peer peer-checked:after:-translate-x-12 rtl:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:right-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-green-500 flex items-center justify-between px-2 text-[10px] font-bold text-white transition-colors duration-300">
                                                    <span class="mr-1">حاضر</span>
                                                    <span class="ml-1 opacity-0 peer-checked:opacity-100">✖</span>
                                                </div>
                                            </label>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex gap-2 items-center">
                                                <input type="text" name="progress[{{ $student->id }}][sura]"
                                                    placeholder="السورة" value="{{ $student->pivot->last_sura }}"
                                                    class="w-32 text-xs rounded-lg border-gray-100 focus:border-gold focus:ring-0 bg-gray-50/50 p-2 font-almarai border shadow-sm">
                                                <input type="number" name="progress[{{ $student->id }}][verse]"
                                                    placeholder="الآية" value="{{ $student->pivot->last_verse }}"
                                                    class="w-16 text-xs rounded-lg border-gray-100 focus:border-gold focus:ring-0 bg-gray-50/50 p-2 font-almarai border shadow-sm">
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
@else
                                    <tr>
                                        <td colspan="3"
                                            class="px-6 py-12 text-center text-gray-400 font-almarai italic opacity-50">لا يوجد
                                            طلاب في هذه الحلقة حالياً</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Session Info -->
            <div class="space-y-6">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col gap-6">
                    <h3 class="text-xl font-bold text-navy font-cairo mb-4 border-b border-gray-50 pb-3">بيانات الجلسة</h3>

                    <div>
                        <label for="session_date" class="block text-sm font-bold text-gray-700 mb-2 font-cairo">تاريخ
                            الجلسة</label>
                        <input type="date" name="session_date" id="session_date" value="{{ now()->format('Y-m-d') }}"
                            required
                            class="w-full rounded-xl border-gray-200 focus:border-gold focus:ring-gold shadow-sm bg-gray-50/50 p-3 font-almarai">
                    </div>

                    <div>
                        <label for="title" class="block text-sm font-bold text-gray-700 mb-2 font-cairo">عنوان الجلسة
                            (اختياري)</label>
                        <input type="text" name="title" id="title" placeholder="مثلاً: مراجعة سورة البقرة"
                            class="w-full rounded-xl border-gray-200 focus:border-gold focus:ring-gold shadow-sm bg-gray-50/50 p-3 font-almarai">
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-bold text-gray-700 mb-2 font-cairo">ملاحظات
                            الدرس</label>
                        <textarea name="notes" id="notes" rows="3" placeholder="ملاحظات حول مستوى الطلاب اليوم..."
                            class="w-full rounded-xl border-gray-200 focus:border-gold focus:ring-gold shadow-sm bg-gray-50/50 p-3 font-almarai"></textarea>
                    </div>

                    <div class="pt-6 border-t border-gray-100">
                        <button type="submit"
                            class="w-full py-4 bg-navy text-white rounded-xl hover:bg-navy/90 shadow-lg font-cairo font-bold transition-all transform hover:-translate-y-1 flex items-center justify-center gap-2">
                            <i class="fas fa-check-double text-gold"></i>
                            <span>تأكيد تسجيل الحضور</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
