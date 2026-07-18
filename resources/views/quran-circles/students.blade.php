@extends('layouts.app')

@section('title', 'إدارة طلاب الحلقة - ' . $quranCircle->name)

@section('content')
    <div
        class="mb-8 flex flex-col md:flex-row justify-between items-center bg-white p-6 rounded-2xl border-l-8 border-gold shadow-sm">
        <div class="flex items-center gap-4">
            <div class="bg-navy p-4 rounded-xl text-gold shadow-md">
                <i class="fas fa-users-cog text-3xl"></i>
            </div>
            <div>
                <h1 class="text-3xl font-black text-navy font-cairo lowercase">{{ $quranCircle->name }}</h1>
                <p class="text-gray-400 font-almarai text-sm mt-1">إضافة وإزالة الطلاب من الحلقة</p>
            </div>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="{{ route('quran-circles.show', $quranCircle) }}"
                class="px-6 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 shadow-sm font-cairo font-bold transition-all flex items-center gap-2">
                <i class="fas fa-arrow-right"></i>
                <span>العودة للتفاصيل</span>
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-100 border-r-4 border-green-500 text-green-700 font-cairo rounded-xl shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Add Student -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col h-full">
            <h3 class="text-xl font-bold text-navy font-cairo mb-6 flex items-center gap-2">
                <i class="fas fa-user-plus text-gold"></i>
                إضافة طالب جديد
            </h3>

            <form action="{{ route('quran-circles.students.add', $quranCircle) }}" method="POST" class="mb-6">
                @csrf
                <div class="flex gap-4">
                    <div class="flex-1">
                        <select name="student_id"
                            class="w-full rounded-xl border-gray-200 focus:border-gold focus:ring-gold shadow-sm bg-gray-50/50 p-3 font-almarai"
                            required>
                            <option value="">اختر طالباً من المركز...</option>
                            @foreach($availableStudents as $student)
                                <option value="{{ $student->id }}">{{ $student->name_ar }} ({{ $student->student_number }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit"
                        class="px-8 py-3 bg-navy text-white rounded-xl hover:bg-navy/90 shadow-lg font-cairo font-bold transition-all">
                        إضافة
                    </button>
                </div>
            </form>

            <div class="bg-blue-50/50 border-r-4 border-blue-200 p-4 rounded-r-xl flex items-start gap-3 mt-auto">
                <i class="fas fa-info-circle text-blue-400 mt-1"></i>
                <p class="text-xs text-blue-700 font-almarai leading-5 font-medium">يتم عرض الطلاب المقيمين في نفس المركز
                    والذين لم يتم إضافتهم لهذه الحلقة بعد.</p>
            </div>
        </div>

        <!-- Current Students -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 h-full">
            <div class="p-6 border-b border-gray-50">
                <h3 class="text-xl font-bold text-navy font-cairo">طلاب الحلقة حالياً</h3>
            </div>
            <div class="max-h-[500px] overflow-y-auto custom-scrollbar">
                <table class="min-w-full divide-y divide-gray-50 text-right">
                    <tbody class="bg-white divide-y divide-gray-50">
                        @if(is_countable($quranCircle->students) ? count($quranCircle->students) > 0 : (method_exists($quranCircle->students, 'count') ? $quranCircle->students->count() > 0 : !empty($quranCircle->students)))
    @foreach($quranCircle->students as $student)
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="h-8 w-8 rounded-full bg-navy/10 flex items-center justify-center text-navy font-bold text-xs uppercase">
                                            {{ mb_substr($student->name_ar, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-navy font-cairo">{{ $student->name_ar }}</div>
                                            <div class="flex items-center gap-2 mt-0.5">
                                                <span
                                                    class="text-[9px] bg-gray-50 text-gray-400 px-1.2 py-0.5 rounded border border-gray-100 font-bold font-cairo">طالب</span>
                                                <div class="text-[9px] text-gray-400 font-almarai">
                                                    {{ $student->room->number ?? '—' }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-left">
                                    <form action="{{ route('quran-circles.students.remove', [$quranCircle, $student]) }}"
                                        method="POST" onsubmit="return confirm('هل أنت متأكد من إزالة هذا الطالب من الحلقة؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-red-400 hover:text-red-700 transition-colors p-2 bg-red-50/50 rounded-lg"
                                            title="إزالة من الحلقة">
                                            <i class="fas fa-user-minus"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
@else
                            <tr>
                                <td class="px-6 py-12 text-center text-gray-400 font-almarai italic opacity-50">لا يوجد طلاب في
                                    هذه الحلقة حالياً</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
