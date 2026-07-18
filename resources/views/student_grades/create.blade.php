@extends ('layouts.app')
@php
    /** @var \Illuminate\Support\ViewErrorBag $errors */
@endphp

@section('content')
<div class="py-12 bg-gray-50/50 min-h-screen" dir="rtl">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="mb-8 flex items-center gap-4">
            <a href="{{ route('student-grades.index') }}" 
                class="w-10 h-10 rounded-xl bg-white border border-gray-100 flex items-center justify-center text-gray-500 hover:text-primary hover:border-primary transition-all">
                <i class="fas fa-arrow-right"></i>
            </a>
            <div>
                <h1 class="text-2xl font-black text-gray-900 font-cairo">رفع بيان درجات جديد</h1>
                <p class="mt-1 text-sm text-gray-500 font-almarai">يرجى رفع صور واضحة لبيان الدرجات الخاص بك لضمان صحة البيانات.</p>
            </div>
        </div>

        @if ($errors->any())
            <div class="mb-6 p-4 rounded-2xl bg-red-50 border border-red-100">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li class="text-red-600 text-xs font-almarai italic">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('student-grades.store') }}" method="POST" enctype="multipart/form-data" 
            class="bg-white rounded-3xl p-8 border border-gray-100 shadow-xl shadow-gray-200/20 space-y-6">
            @csrf
            
            <div class="space-y-2">
                <label class="block text-sm font-bold text-gray-700 font-cairo mb-2">اختر صورة بيان الدرجات <span class="text-red-500">*</span></label>
                <div class="relative group p-10 border-2 border-dashed border-gray-200 rounded-2xl hover:border-primary/50 hover:bg-primary/5 transition-all text-center">
                    <input type="file" name="grade_file" accept="image/*" required
                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                        onchange="updateFileName(this)">
                    <div class="flex flex-col items-center gap-3">
                        <div class="w-16 h-16 rounded-2xl bg-gray-50 flex items-center justify-center text-primary group-hover:scale-110 transition-transform">
                            <i class="fas fa-cloud-upload-alt text-3xl"></i>
                        </div>
                        <div class="flex-1 overflow-hidden">
                            <p class="text-sm font-bold text-gray-800 font-cairo truncate file-name">اسحب الصورة هنا أو اختر الملف</p>
                            <p class="text-xs text-gray-400 font-almarai mt-1">الأنواع المسموحة: JPG, PNG • الحجم الأقصى: 2MB</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="space-y-1.5">
                    <label class="block text-sm font-bold text-gray-700 font-cairo mb-1.5">الفصل الدراسي <span class="text-red-500">*</span></label>
                    <select name="semester" required
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary outline-none font-almarai text-sm bg-white transition-all">
                        <option value="">-- اختر --</option>
                        @foreach(['الفصل الدراسي الأول', 'الفصل الدراسي الثاني', 'الفصل الدراسي الثالث', 'الفصل الدراسي الصيفي', 'بيان درجات سنوي'] as $sem)
                            <option value="{{ $sem }}" {{ old('semester') == $sem ? 'selected' : '' }}>{{ $sem }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-1.5">
                    <label class="block text-sm font-bold text-gray-700 font-cairo mb-1.5">السنة الدراسية</label>
                    <input type="text" name="academic_year" value="{{ old('academic_year') }}" placeholder="2024 / 2025"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary outline-none font-almarai text-sm bg-gray-50/50 transition-all">
                </div>
                <div class="space-y-1.5">
                    <label class="block text-sm font-bold text-gray-700 font-cairo mb-1.5">النسبة العامة (%)</label>
                    <input type="number" step="0.01" name="gpa_percentage" value="{{ old('gpa_percentage') }}" placeholder="95.50"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary outline-none font-almarai text-sm bg-gray-50/50 transition-all shadow-inner">
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="block text-sm font-bold text-gray-700 font-cairo mb-1.5">ملاحظات إضافية (اختياري)</label>
                <textarea name="notes" rows="3"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary outline-none font-almarai text-sm bg-gray-50/50 transition-all resize-none"
                    placeholder="أي ملاحظات تود إضافتها للإدارة حول هذا البيان...">{{ old('notes') }}</textarea>
            </div>

            <div class="pt-4 p-4 rounded-xl bg-amber-50 border border-amber-100 flex items-start gap-3 text-amber-800 text-xs font-almarai">
                <i class="fas fa-exclamation-circle mt-0.5"></i>
                <span>تنبيه: بعد عملية الرفع الناجحة لن تتمكن من تعديل أو حذف الصورة، لذا يرجى التأكد من وضوحها وصحتها قبل النقر على زر الحفظ.</span>
            </div>

            <div class="flex flex-col gap-3">
                <button type="submit"
                    class="w-full py-4 bg-primary hover:bg-primary/90 text-white rounded-2xl font-black font-cairo text-lg transition-all shadow-xl shadow-primary/20 hover:shadow-primary/30 hover:-translate-y-0.5 transform flex items-center justify-center gap-3">
                    <i class="fas fa-save text-xl"></i>
                    حفظ ورفع البيان
                </button>
                <a href="{{ route('student-grades.index') }}"
                    class="w-full py-4 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-2xl font-bold font-cairo text-center transition-all">
                    إلغاء العملية
                </a>
            </div>
        </form>

    </div>
</div>
@endsection

@push('scripts')
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
