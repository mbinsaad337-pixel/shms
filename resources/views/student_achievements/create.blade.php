@extends ('layouts.app')
@php
    /** @var \Illuminate\Support\ViewErrorBag $errors */
@endphp
@section ('title', 'إضافة إنجاز لطالب')

@section ('content')
<div class="py-12 bg-gray-50/50 min-h-screen" dir="rtl">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="mb-8 flex items-center gap-4">
            <a href="{{ route('student-achievements.index') }}" 
                class="w-10 h-10 rounded-xl bg-white border border-gray-100 flex items-center justify-center text-gray-500 hover:text-primary hover:border-primary transition-all">
                <i class="fas fa-arrow-right"></i>
            </a>
            <div>
                <h1 class="text-2xl font-black text-gray-900 font-cairo">تسجيل إنجاز جديد للطالب</h1>
                <p class="mt-1 text-sm text-gray-500 font-almarai">قم بتعبئة بيانات الإنجاز أو الجائزة التي حصل عليها الطالب.</p>
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

        <form action="{{ route('student-achievements.store') }}" method="POST" enctype="multipart/form-data" 
            class="bg-white rounded-3xl p-8 border border-gray-100 shadow-xl shadow-gray-200/20 space-y-5">
            @csrf
            
            <div class="space-y-1.5">
                <label class="block text-sm font-bold text-gray-700 font-cairo mb-1.5">اختيار الطالب <span class="text-red-500">*</span></label>
                <select name="student_id" required
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary outline-none font-almarai text-sm bg-white transition-all shadow-sm">
                    <option value="">-- اختر الطالب --</option>
                    @if (isset($students) && count($students) > 0)
                        @php foreach ($students as $st): @endphp
                        <option value="{{ $st->id }}" {{ old('student_id') == $st->id ? 'selected' : '' }}>
                            {{ $st->name_ar }} (#{{ $st->student_number }})
                        </option>
                        @php endforeach; @endphp
                    @endif
                </select>
            </div>

            <div class="space-y-1.5">
                <label class="block text-sm font-bold text-gray-700 font-cairo mb-1.5">عنوان الإنجاز <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" required placeholder="مثال: المركز الأول في مسابقة حفظ القرآن"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary outline-none font-almarai text-sm bg-gray-50/50 transition-all">
            </div>

            <div class="space-y-1.5">
                <label class="block text-sm font-bold text-gray-700 font-cairo mb-1.5">وصف الإنجاز <span class="text-red-500">*</span></label>
                <textarea name="description" rows="4" required
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary outline-none font-almarai text-sm bg-gray-50/50 transition-all resize-none"
                    placeholder="تفاصيل الإنجاز والجهة المانحة...">{{ old('description') }}</textarea>
            </div>

            <div class="space-y-1.5">
                <label class="block text-sm font-bold text-gray-700 font-cairo mb-1.5">تاريخ الإنجاز <span class="text-red-500">*</span></label>
                <input type="date" name="achievement_date" value="{{ old('achievement_date', date('Y-m-d')) }}" required
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary outline-none font-almarai text-sm bg-white transition-all">
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-bold text-gray-700 font-cairo mb-1.5">شهادة الإنجاز / صورة توثيقية (اختياري)</label>
                <div class="relative group p-6 border-2 border-dashed border-gray-100 rounded-2xl hover:border-primary/50 hover:bg-primary/5 transition-all text-center">
                    <input type="file" name="certificate_file" accept=".pdf,image/*"
                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                        onchange="updateFileName(this)">
                    <div class="flex flex-col items-center gap-2">
                        <div class="w-12 h-12 rounded-xl bg-gray-50 flex items-center justify-center text-primary group-hover:scale-110 transition-transform">
                            <i class="fas fa-file-upload text-xl"></i>
                        </div>
                        <p class="text-[11px] font-bold text-gray-800 font-cairo truncate file-name">اختر ملف التوثيق</p>
                        <p class="text-[10px] text-gray-400 font-almarai">JPG, PNG, PDF • Max 2MB</p>
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-3 pt-4">
                <button type="submit"
                    class="w-full py-4 bg-primary hover:bg-primary/90 text-white rounded-2xl font-black font-cairo text-lg transition-all shadow-xl shadow-primary/20 hover:shadow-primary/30 hover:-translate-y-0.5 transform flex items-center justify-center gap-3">
                    <i class="fas fa-save text-xl"></i>
                    حفظ وإضافة الإنجاز
                </button>
                <a href="{{ route('student-achievements.index') }}"
                    class="w-full py-4 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-2xl font-bold font-cairo text-center transition-all">
                    إلغاء
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
