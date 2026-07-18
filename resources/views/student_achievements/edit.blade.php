@extends ('layouts.app')

@section ('title', 'تعديل إنجاز الطالب')

@section ('content')
<div class="py-12 bg-gray-50/50 min-h-screen" dir="rtl">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="mb-8 flex items-center gap-4">
            <a href="{{ route('student-achievements.index') }}" 
                class="w-10 h-10 rounded-xl bg-white border border-gray-100 flex items-center justify-center text-gray-500 hover:text-primary hover:border-primary transition-all">
                <i class="fas fa-arrow-right"></i>
            </a>
            <div>
                <h1 class="text-2xl font-black text-gray-900 font-cairo">تعديل بيانات الإنجاز</h1>
                <p class="mt-1 text-sm text-gray-500 font-almarai">يمكنك تعديل تفاصيل الإنجاز المسجل للطالب: {{ $studentAchievement->student->name_ar }}</p>
            </div>
        </div>

        @if ($errors->any())
            <div class="mb-6 p-4 rounded-2xl bg-red-50 border border-red-100 shadow-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li class="text-red-600 text-[11px] font-almarai italic">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('student-achievements.update', $studentAchievement) }}" method="POST" enctype="multipart/form-data" 
            class="bg-white rounded-3xl p-8 border border-gray-100 shadow-xl shadow-gray-200/20 space-y-5">
            @csrf
            @method('PUT')
            
            <div class="space-y-1.5 opacity-60">
                <label class="block text-sm font-bold text-gray-700 font-cairo mb-1.5">الطالب (لا يمكن تغييره)</label>
                <input type="text" value="{{ $studentAchievement->student->name_ar }} (#{{ $studentAchievement->student->student_number }})" 
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl font-almarai text-sm bg-gray-100 cursor-not-allowed" readonly>
                <input type="hidden" name="student_id" value="{{ $studentAchievement->student_id }}">
            </div>

            <div class="space-y-1.5">
                <label class="block text-sm font-bold text-gray-700 font-cairo mb-1.5">عنوان الإنجاز <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title', $studentAchievement->title) }}" required 
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary outline-none font-almarai text-sm bg-gray-50/50 transition-all">
            </div>

            <div class="space-y-1.5">
                <label class="block text-sm font-bold text-gray-700 font-cairo mb-1.5">وصف الإنجاز <span class="text-red-500">*</span></label>
                <textarea name="description" rows="4" required
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary outline-none font-almarai text-sm bg-gray-50/50 transition-all resize-none">{{ old('description', $studentAchievement->description) }}</textarea>
            </div>

            <div class="space-y-1.5">
                <label class="block text-sm font-bold text-gray-700 font-cairo mb-1.5">تاريخ الإنجاز <span class="text-red-500">*</span></label>
                <input type="date" name="achievement_date" value="{{ old('achievement_date', $studentAchievement->achievement_date?->format('Y-m-d')) }}" required
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary outline-none font-almarai text-sm bg-white transition-all shadow-sm">
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-bold text-gray-700 font-cairo mb-1.5">تحديث شهادة الإنجاز / الملفات (اختياري)</label>
                
                @if ($studentAchievement->certificate_file)
                    <div class="mb-3 p-3 bg-emerald-50 rounded-xl border border-emerald-100 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-file-alt text-emerald-600"></i>
                            <span class="text-xs text-emerald-700 font-almarai">يوجد ملف مرفوع حالياً</span>
                        </div>
                        <a href="{{ asset('storage/' . $studentAchievement->certificate_file) }}" target="_blank"
                            class="text-[10px] font-bold text-emerald-800 hover:underline">عرض الملف</a>
                    </div>
                @endif

                <div class="relative group p-6 border-2 border-dashed border-gray-100 rounded-2xl hover:border-primary/50 hover:bg-primary/5 transition-all text-center">
                    <input type="file" name="certificate_file" accept=".pdf,image/*"
                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                        onchange="updateFileName(this)">
                    <div class="flex flex-col items-center gap-2 text-gray-400 group-hover:text-primary">
                        <i class="fas fa-cloud-upload-alt text-2xl"></i>
                        <span class="text-xs font-bold font-cairo file-name">اتركه فارغاً للاحتفاظ بالملف الحالي</span>
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-3 pt-4">
                <button type="submit"
                    class="w-full py-4 bg-amber-500 hover:bg-amber-600 text-white rounded-2xl font-black font-cairo text-lg transition-all shadow-xl shadow-amber-500/20 hover:-translate-y-0.5 transform flex items-center justify-center gap-3">
                    <i class="fas fa-save text-xl"></i>
                    تحديث بيانات الإنجاز
                </button>
                <a href="{{ route('student-achievements.index') }}"
                    class="w-full py-4 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-2xl font-bold font-cairo text-center transition-all">
                    إلغاء التعديل
                </a>
            </div>
        </form>

    </div>
</div>
@endsection

@push ('scripts')
<script>
    function updateFileName(input) {
        if (input.files && input.files[0]) {
            const fileName = input.files[0].name;
            const label = input.closest('.relative').querySelector('.file-name');
            if (label) {
                label.textContent = fileName;
                label.classList.remove('text-gray-400');
                label.classList.add('text-primary');
            }
        }
    }
</script>
@endpush
