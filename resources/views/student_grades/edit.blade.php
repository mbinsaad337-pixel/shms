@extends('layouts.app')
@php /** @var \Illuminate\Support\ViewErrorBag $errors */ @endphp
@section('title', 'تعديل بيان الدرجات')

@section('content')
<div class="py-12 bg-gray-50/50 min-h-screen" dir="rtl">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="mb-8 flex items-center gap-4">
            <a href="{{ route('student-grades.index') }}" 
                class="w-10 h-10 rounded-xl bg-white border border-gray-100 flex items-center justify-center text-gray-500 hover:text-primary hover:border-primary transition-all">
                <i class="fas fa-arrow-right"></i>
            </a>
            <div>
                <h1 class="text-2xl font-black text-gray-900 font-cairo">تعديل بيان درجات الطالب: {{ $studentGrade->student->name_ar }}</h1>
                <p class="mt-1 text-sm text-gray-500 font-almarai">يمكنك تعديل البيانات أو استبدال الملف المرفوع.</p>
            </div>
        </div>

        @if($errors->any())
            <div class="mb-6 p-4 rounded-2xl bg-red-50 border border-red-100">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li class="text-red-600 text-xs font-almarai italic">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-1">
                <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm sticky top-8">
                   <h3 class="text-sm font-bold text-gray-700 font-cairo mb-4 italic">الصورة الحالية:</h3>
                   <div class="aspect-[3/4] rounded-2xl overflow-hidden border border-gray-100 shadow-inner group relative">
                        <img src="{{ asset('storage/' . $studentGrade->file_path) }}" 
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" 
                            alt="الصورة الحالية">
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                            <a href="{{ asset('storage/' . $studentGrade->file_path) }}" target="_blank"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-white rounded-xl text-primary text-xs font-bold font-cairo shadow-sm hover:bg-primary hover:text-white transition-all">
                                <i class="fas fa-search-plus"></i> تكبير الصورة
                            </a>
                        </div>
                   </div>
                   <p class="text-[10px] text-gray-400   mt-3 text-center">تاريخ الرفع: {{ $studentGrade->created_at->format('Y/m/d H:i') }}</p>
                </div>
            </div>

            <div class="lg:col-span-2">
                <form action="{{ route('student-grades.update', $studentGrade) }}" method="POST" enctype="multipart/form-data" 
                    class="bg-white rounded-3xl p-8 border border-gray-100 shadow-xl shadow-gray-200/20 space-y-6">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-1.5">
                        <label class="block text-sm font-bold text-gray-700 font-cairo mb-2">تحديث صورة بيان الدرجات (اختياري)</label>
                        <div class="relative group p-6 border-2 border-dashed border-gray-100 rounded-2xl hover:border-primary/50 hover:bg-primary/5 transition-all text-center">
                            <input type="file" name="grade_file" accept="image/*"
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                onchange="updateFileName(this)">
                            <div class="flex flex-col items-center gap-2 text-gray-400 group-hover:text-primary">
                                <i class="fas fa-cloud-upload-alt text-2xl"></i>
                                <span class="text-xs font-bold font-cairo file-name">اتركه فارغاً للاحتفاظ بالملف الحالي</span>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="space-y-1.5">
                            <label class="block text-sm font-bold text-gray-700 font-cairo mb-1.5">الفصل الدراسي <span class="text-red-500">*</span></label>
                            <input type="text" name="semester" value="{{ old('semester', $studentGrade->semester) }}" required
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary outline-none font-almarai text-sm bg-gray-50/50 transition-all">
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-sm font-bold text-gray-700 font-cairo mb-1.5">السنة الدراسية</label>
                            <input type="text" name="academic_year" value="{{ old('academic_year', $studentGrade->academic_year) }}"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary outline-none font-almarai text-sm bg-gray-50/50 transition-all">
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-sm font-bold text-gray-700 font-cairo mb-1.5">النسبة العامة (%)</label>
                            <input type="number" step="0.01" name="gpa_percentage" value="{{ old('gpa_percentage', $studentGrade->gpa_percentage) }}"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary outline-none font-almarai text-sm bg-gray-50/50 transition-all">
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-sm font-bold text-gray-700 font-cairo mb-1.5">ملاحظات إضافية</label>
                        <textarea name="notes" rows="3"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary outline-none font-almarai text-sm bg-gray-50/50 transition-all resize-none">{{ old('notes', $studentGrade->notes) }}</textarea>
                    </div>

                    <div class="flex flex-col gap-3">
                        <button type="submit"
                            class="w-full py-4 bg-amber-500 hover:bg-amber-600 text-white rounded-2xl font-black font-cairo text-lg transition-all shadow-xl shadow-amber-500/20 hover:-translate-y-0.5 transform flex items-center justify-center gap-3">
                            <i class="fas fa-save text-xl"></i>
                            تحديث البيانات
                        </button>
                        <a href="{{ route('student-grades.index') }}"
                            class="w-full py-4 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-2xl font-bold font-cairo text-center transition-all">
                            إلغاء العملية
                        </a>
                    </div>
                </form>
            </div>
        </div>

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
