@extends('layouts.app')
@section('title', 'طلبات التخرج المعلقة')

@section('content')
<div dir="rtl">

    {{-- Header --}}
    <div class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-2xl border-r-8 border-navy shadow-sm">
        <div>
            <h1 class="text-2xl md:text-3xl font-black text-navy font-cairo">طلبات التخرج المعلقة</h1>
            <p class="text-gray-400 font-almarai text-xs mt-1">مراجعة واعتماد طلبات التخرج المقدمة من الطلاب</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('students.alumni') }}"
               class="px-5 py-2.5 bg-navy/10 text-navy rounded-xl font-cairo font-bold text-sm hover:bg-navy/20 transition-all flex items-center gap-2">
                <i class="fas fa-user-graduate"></i> قائمة الخريجين
            </a>
        </div>
    </div>

    {{-- Search --}}
    <div class="bg-white p-4 rounded-2xl shadow-sm mb-6 border border-gray-100">
        <form action="{{ route('graduation.pending') }}" method="GET" class="flex gap-3 items-end">
            <div class="flex-1">
                <label class="block text-xs font-bold text-gray-600 font-cairo mb-2">بحث</label>
                <div class="relative">
                    <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400">
                        <i class="fas fa-search text-xs"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="الاسم، الرقم الجامعي، الهوية..."
                           class="w-full pr-9 rounded-xl border-gray-200 focus:border-navy focus:ring-navy transition-all text-sm bg-gray-50">
                </div>
            </div>
            <button type="submit"
                    class="px-5 py-2.5 bg-navy text-white rounded-xl font-cairo font-bold text-sm hover:bg-navy/90 transition-all">
                <i class="fas fa-filter ml-1"></i> بحث
            </button>
            @if(request('search'))
            <a href="{{ route('graduation.pending') }}"
               class="px-5 py-2.5 bg-gray-100 text-gray-600 rounded-xl font-cairo font-bold text-sm hover:bg-gray-200 transition-all">
                إعادة تعيين
            </a>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100">
        @if($students->count() === 0)
            <div class="py-20 text-center">
                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-graduation-cap text-3xl text-gray-300"></i>
                </div>
                <p class="text-gray-500 font-cairo font-bold">لا توجد طلبات تخرج معلقة حالياً</p>
                <p class="text-gray-400 text-sm font-almarai mt-1">ستظهر الطلبات هنا عندما يقدمها الطلاب</p>
            </div>
        @else
            <table class="min-w-full divide-y divide-gray-100 text-right">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">الطالب</th>
                        <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">الرقم الجامعي</th>
                        <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">الجامعة / التخصص</th>
                        <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">المسمى الوظيفي</th>
                        <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">تاريخ الطلب</th>
                        <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">المرفقات</th>
                        <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($students as $student)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        {{-- Student --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <img class="w-10 h-10 rounded-full object-cover border-2 border-gold/30"
                                     src="{{ $student->photo ? asset('storage/'.$student->photo) : 'https://ui-avatars.com/api/?name='.urlencode($student->name_ar).'&background=0f172a&color=fff' }}"
                                     alt="">
                                <div>
                                    <div class="font-bold text-sm text-gray-900">{{ $student->name_ar }}</div>
                                    <div class="text-xs text-gray-400 font-almarai">{{ $student->center->name ?? '-' }}</div>
                                </div>
                            </div>
                        </td>
                        {{-- Student Number --}}
                        <td class="px-6 py-4 text-sm text-gray-500 font-almarai">{{ $student->student_number }}</td>
                        {{-- University / Major --}}
                        <td class="px-6 py-4">
                            <div class="text-sm font-bold text-gray-800">{{ $student->university }}</div>
                            <div class="text-xs text-gray-400 font-almarai">{{ $student->major }}</div>
                        </td>
                        {{-- Job Title --}}
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1 px-3 py-1 bg-navy/10 text-navy rounded-full text-xs font-bold font-cairo">
                                <i class="fas fa-briefcase text-[10px]"></i>
                                {{ $student->job_title ?? '-' }}
                            </span>
                        </td>
                        {{-- Date --}}
                        <td class="px-6 py-4 text-sm text-gray-500 font-almarai whitespace-nowrap">
                            {{ $student->graduation_requested_at?->format('Y/m/d') }}
                        </td>
                        {{-- Attachments --}}
                        <td class="px-6 py-4">
                            <div class="flex flex-col gap-1">
                                @foreach($student->graduationAttachments as $att)
                                <a href="{{ asset('storage/'.$att->file_path) }}" target="_blank"
                                   class="inline-flex items-center gap-1.5 text-xs text-navy hover:text-gold font-almarai transition-colors group">
                                    <i class="fas fa-paperclip text-[10px] group-hover:text-gold"></i>
                                    <span class="underline-offset-2 group-hover:underline">{{ $att->name }}</span>
                                </a>
                                @endforeach
                            </div>
                        </td>
                        {{-- Actions --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2 flex-wrap">
                                <a href="{{ route('students.show', $student) }}" target="_blank"
                                   class="px-3 py-1.5 bg-gray-100 text-gray-600 rounded-lg text-xs font-bold font-cairo hover:bg-gray-200 transition-all flex items-center gap-1">
                                    <i class="fas fa-eye text-[10px]"></i> عرض الملف
                                </a>
                                {{-- Approve --}}
                                <form action="{{ route('graduation.approve', $student) }}" method="POST" class="inline"
                                      data-confirm="هل تريد الموافقة على طلب تخرج الطالب {{ $student->name_ar }}؟ سيتم نقله فوراً لقائمة الخريجين.">
                                    @csrf
                                    <button type="submit"
                                            class="px-3 py-1.5 bg-green-600 text-white rounded-lg text-xs font-bold font-cairo hover:bg-green-700 transition-all flex items-center gap-1">
                                        <i class="fas fa-check text-[10px]"></i> موافقة
                                    </button>
                                </form>
                                {{-- Reject --}}
                                <button type="button" onclick="openRejectModal({{ $student->id }}, '{{ addslashes($student->name_ar) }}')"
                                        class="px-3 py-1.5 bg-red-600 text-white rounded-lg text-xs font-bold font-cairo hover:bg-red-700 transition-all flex items-center gap-1">
                                    <i class="fas fa-times text-[10px]"></i> رفض
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                {{ $students->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Reject Modal --}}
<div id="rejectModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-3xl w-full max-w-md shadow-2xl overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-red-50">
            <h3 class="text-lg font-bold font-cairo text-red-700 flex items-center gap-2">
                <i class="fas fa-times-circle"></i> رفض طلب التخرج
            </h3>
            <button onclick="closeRejectModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="rejectForm" method="POST" class="p-6 space-y-4">
            @csrf
            <p class="text-sm text-gray-600 font-almarai" id="rejectStudentName"></p>
            <div>
                <label class="block text-sm font-bold text-gray-700 font-cairo mb-2">
                    سبب الرفض <span class="text-red-500">*</span>
                </label>
                <textarea name="rejection_reason" rows="4" required
                          placeholder="اكتب سبب الرفض ليتمكن الطالب من تصحيح بياناته..."
                          class="w-full bg-gray-50 border border-gray-200 rounded-2xl p-4 font-almarai text-sm focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all"></textarea>
            </div>
            <div class="flex gap-3">
                <button type="submit"
                        class="flex-1 bg-red-600 text-white py-3 rounded-xl font-bold font-cairo hover:bg-red-700 transition-all">
                    تأكيد الرفض
                </button>
                <button type="button" onclick="closeRejectModal()"
                        class="flex-1 bg-gray-100 text-gray-700 py-3 rounded-xl font-bold font-cairo hover:bg-gray-200 transition-all">
                    إلغاء
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openRejectModal(studentId, studentName) {
    document.getElementById('rejectForm').action = `/graduation/${studentId}/reject`;
    document.getElementById('rejectStudentName').textContent = `سيتم رفض طلب تخرج الطالب: ${studentName}`;
    const modal = document.getElementById('rejectModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}
function closeRejectModal() {
    const modal = document.getElementById('rejectModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
</script>
@endsection
