@extends('layouts.app')
@section('title', 'استكمال بيانات التخرج')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8" dir="rtl">

    {{-- Header --}}
    <div class="mb-8 flex items-center gap-4">
        <a href="{{ route('student.dashboard') }}" class="w-10 h-10 bg-white rounded-full shadow-sm flex items-center justify-center text-gray-400 hover:text-navy transition-all border border-gray-100">
            <i class="fas fa-arrow-right"></i>
        </a>
        <div class="w-14 h-14 rounded-2xl bg-navy flex items-center justify-center shadow-lg">
            <i class="fas fa-graduation-cap text-gold text-2xl"></i>
        </div>
        <div>
            <h1 class="text-2xl font-black text-navy font-cairo">استكمال بيانات التخرج</h1>
            <p class="text-gray-400 font-almarai text-sm">يرجى تعبئة النموذج أدناه لتقديم طلب التخرج</p>
        </div>
    </div>

    {{-- Rejection Notice --}}
    @if($student->graduation_request_status === 'rejected')
    <div class="bg-red-50 border-r-4 border-red-500 p-5 rounded-2xl mb-6 flex items-start gap-3">
        <i class="fas fa-times-circle text-red-500 text-xl mt-0.5"></i>
        <div>
            <p class="font-bold text-red-700 font-cairo">تم رفض طلبك السابق</p>
            <p class="text-red-600 text-sm font-almarai mt-1">{{ $student->graduation_rejection_reason }}</p>
            <p class="text-red-400 text-xs mt-2">يمكنك تقديم الطلب مجدداً بعد تصحيح البيانات.</p>
        </div>
    </div>
    @endif

    {{-- Form --}}
    <form action="{{ route('graduation.submit', $student) }}" method="POST" enctype="multipart/form-data"
          class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 space-y-8" id="graduationForm">
        @csrf

        {{-- Job Title --}}
        <div>
            <label class="block text-sm font-bold text-gray-700 font-cairo mb-3">
                <i class="fas fa-briefcase text-navy ml-1"></i> المسمى الوظيفي
                <span class="text-red-500">*</span>
            </label>

            @php
                $jobs = [
                    'مهندس', 'طبيب', 'محاسب', 'مدرس / معلم', 'محامٍ', 'مبرمج / مطور',
                    'موظف حكومي', 'موظف قطاع خاص', 'رجل أعمال / عمل حر', 'باحث / أكاديمي',
                    'طالب دراسات عليا', 'لا يعمل حالياً', 'أخرى'
                ];
            @endphp

            <select name="job_title" id="jobTitleSelect"
                    class="w-full bg-gray-50 border border-gray-200 rounded-2xl p-4 font-almarai focus:ring-2 focus:ring-navy/20 focus:border-navy transition-all text-right"
                    onchange="toggleCustomJob(this.value)">
                <option value="">-- اختر المسمى الوظيفي --</option>
                @foreach($jobs as $job)
                    <option value="{{ $job }}" {{ old('job_title') === $job ? 'selected' : '' }}>{{ $job }}</option>
                @endforeach
            </select>

            {{-- Custom Job Input (hidden by default) --}}
            <div id="customJobWrapper" class="mt-3 hidden">
                <input type="text" id="customJobInput" name="custom_job_title"
                       placeholder="اكتب المسمى الوظيفي..."
                       class="w-full bg-gray-50 border border-gray-200 rounded-2xl p-4 font-almarai focus:ring-2 focus:ring-navy/20 focus:border-navy transition-all"
                       value="{{ old('custom_job_title') }}">
            </div>

            @error('job_title')
                <p class="text-red-500 text-xs mt-2 font-almarai">{{ $message }}</p>
            @enderror
        </div>

        {{-- Attachments --}}
        <div>
            <div class="flex items-center justify-between mb-3">
                <label class="text-sm font-bold text-gray-700 font-cairo">
                    <i class="fas fa-paperclip text-navy ml-1"></i> مرفقات التخرج
                    <span class="text-red-500">*</span>
                </label>
                <button type="button" onclick="addAttachment()"
                        class="text-xs bg-navy text-white px-4 py-2 rounded-xl font-cairo font-bold hover:bg-navy/90 transition-all flex items-center gap-2">
                    <i class="fas fa-plus"></i> إضافة مرفق
                </button>
            </div>

            <p class="text-xs text-gray-400 font-almarai mb-4">
                <i class="fas fa-info-circle ml-1"></i>
                أرفق شهادة التخرج، كشف الدرجات، أو أي وثائق ذات صلة. الأنواع المقبولة: PDF، صور، Word.
            </p>

            <div id="attachmentsContainer" class="space-y-3">
                {{-- Initial attachment row --}}
                <div class="attachment-row bg-gray-50 rounded-2xl p-4 border border-gray-100" data-index="0">
                    <div class="flex items-start gap-3">
                        <div class="flex-1 space-y-3">
                            <input type="text" name="attachments[0][name]"
                                   placeholder="اسم المرفق (مثال: شهادة التخرج)"
                                   class="w-full bg-white border border-gray-200 rounded-xl p-3 text-sm font-almarai focus:ring-2 focus:ring-navy/20 focus:border-navy transition-all"
                                   required>
                            <input type="file" name="attachments[0][file]"
                                   accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                                   class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-navy file:text-white hover:file:bg-navy/90 file:cursor-pointer"
                                   required>
                        </div>
                        <button type="button" onclick="removeAttachment(this)"
                                class="mt-1 w-8 h-8 rounded-full bg-red-50 text-red-400 hover:bg-red-100 hover:text-red-600 transition-all flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-times text-xs"></i>
                        </button>
                    </div>
                </div>
            </div>

            @error('attachments')
                <p class="text-red-500 text-xs mt-2 font-almarai">{{ $message }}</p>
            @enderror
        </div>

        {{-- Submit --}}
        <div class="pt-4 border-t border-gray-100">
            <button type="button" onclick="confirmSubmit()"
                    class="w-full bg-navy text-white py-4 rounded-2xl font-bold font-cairo shadow-lg hover:bg-navy/90 transition-all flex items-center justify-center gap-3 text-base">
                <i class="fas fa-paper-plane text-gold"></i>
                تقديم طلب التخرج
            </button>
            <a href="{{ route('students.show', $student) }}"
               class="mt-3 block text-center text-gray-400 font-almarai text-sm hover:text-gray-600 transition-colors">
                إلغاء والعودة
            </a>
        </div>
    </form>
</div>

<script>
    let attachmentIndex = 1;

    function toggleCustomJob(value) {
        const wrapper = document.getElementById('customJobWrapper');
        const select  = document.getElementById('jobTitleSelect');
        const input   = document.getElementById('customJobInput');

        if (value === 'أخرى') {
            wrapper.classList.remove('hidden');
            input.required = true;
            // Remove name from select so it's not submitted
            select.removeAttribute('name');
            input.setAttribute('name', 'job_title');
        } else {
            wrapper.classList.add('hidden');
            input.required = false;
            select.setAttribute('name', 'job_title');
            input.removeAttribute('name');
        }
    }

    function addAttachment() {
        const container = document.getElementById('attachmentsContainer');
        const div = document.createElement('div');
        div.className = 'attachment-row bg-gray-50 rounded-2xl p-4 border border-gray-100';
        div.dataset.index = attachmentIndex;
        div.innerHTML = `
            <div class="flex items-start gap-3">
                <div class="flex-1 space-y-3">
                    <input type="text" name="attachments[${attachmentIndex}][name]"
                           placeholder="اسم المرفق (مثال: كشف الدرجات)"
                           class="w-full bg-white border border-gray-200 rounded-xl p-3 text-sm font-almarai focus:ring-2 focus:ring-navy/20 focus:border-navy transition-all"
                           required>
                    <input type="file" name="attachments[${attachmentIndex}][file]"
                           accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                           class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-navy file:text-white hover:file:bg-navy/90 file:cursor-pointer"
                           required>
                </div>
                <button type="button" onclick="removeAttachment(this)"
                        class="mt-1 w-8 h-8 rounded-full bg-red-50 text-red-400 hover:bg-red-100 hover:text-red-600 transition-all flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>`;
        container.appendChild(div);
        attachmentIndex++;
    }

    function removeAttachment(btn) {
        const rows = document.querySelectorAll('.attachment-row');
        if (rows.length <= 1) {
            if (window.Swal) {
                Swal.fire({ icon: 'warning', title: 'تنبيه', text: 'يجب الإبقاء على مرفق واحد على الأقل.', confirmButtonColor: '#004274' });
            } else {
                alert('يجب الإبقاء على مرفق واحد على الأقل.');
            }
            return;
        }
        btn.closest('.attachment-row').remove();
    }

    function confirmSubmit() {
        if (window.Swal) {
            Swal.fire({
                icon: 'info',
                title: 'تنبيه مهم',
                html: '<p class="font-almarai text-right" dir="rtl">سيتم إرسال هذه البيانات إلى <strong>مدير السكن</strong> لمراجعتها واعتمادها.<br>بعد الموافقة، سيتم نقلك تلقائياً إلى قائمة الخريجين.</p>',
                confirmButtonText: 'تأكيد التقديم',
                cancelButtonText: 'إلغاء',
                showCancelButton: true,
                confirmButtonColor: '#004274',
                cancelButtonColor: '#6b7280',
            }).then(result => {
                if (result.isConfirmed) {
                    document.getElementById('graduationForm').submit();
                }
            });
        } else {
            if (confirm('سيتم إرسال البيانات لمدير السكن للمراجعة. هل تريد المتابعة؟')) {
                document.getElementById('graduationForm').submit();
            }
        }
    }
</script>
@endsection
