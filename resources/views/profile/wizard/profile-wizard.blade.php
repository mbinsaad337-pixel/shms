@extends('layouts.app')

@section('title', 'استكمال الملف الشخصي')

@push('styles')
<style>
    /* Progress bar styles */
    .progress-container {
        @apply w-full bg-gray-200 rounded-full h-2.5 mb-8;
    }
    .progress-bar {
        @apply h-2.5 rounded-full transition-all duration-500 ease-in-out;
    }
    .step {
        @apply flex flex-col items-center justify-center;
    }
    .step-circle {
        @apply w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm transition-all;
    }
    .step.active .step-circle {
        @apply bg-primary shadow-lg scale-110;
    }
    .step.completed .step-circle {
        @apply bg-green-500;
    }
    .step.inactive .step-circle {
        @apply bg-gray-300;
    }
    .step-label {
        @apply text-xs font-bold mt-2 text-gray-700;
    }
    .step.active .step-label {
        @apply text-primary;
    }

    /* Form section styles */
    .form-section {
        @apply bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-8 transition-all duration-300;
    }
    .section-header {
        @apply flex items-center gap-3 px-6 py-4 border-b border-gray-100;
    }
    .section-body {
        @apply p-6;
    }
    .field-label {
        @apply block text-sm font-bold text-gray-700 font-cairo mb-1.5;
    }
    .field-input {
        @apply w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary outline-none font-almarai text-sm transition-all bg-gray-50/50;
    }
    .field-select {
        @apply w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary outline-none font-almarai text-sm bg-white transition-all;
    }
    .required-star {
        @apply text-red-500 ml-0.5;
    }

    /* Review card styles */
    .review-card {
        @apply bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6;
    }
    .review-header {
        @apply bg-gradient-to-l from-blue-50 to-indigo-50 px-6 py-4 border-b border-gray-100;
    }
    .review-body {
        @apply p-6;
    }
    .review-item {
        @apply flex justify-between items-center py-3 border-b border-gray-100 last:border-b-0;
    }
    .review-label {
        @apply font-bold text-gray-700 font-cairo text-sm;
    }
    .review-value {
        @apply text-gray-600 font-almarai text-sm;
    }

    /* Button styles */
    .btn-primary {
        @apply bg-primary hover:bg-primary/90 text-white font-bold py-3 px-6 rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl;
    }
    .btn-secondary {
        @apply bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-3 px-6 rounded-xl transition-all duration-300;
    }

    /* Animation */
    .fade-in {
        animation: fadeIn 0.5s ease-in-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50/30 py-8" dir="rtl">
    <div class="container mx-auto px-4 max-w-4xl">
        {{-- Header --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-primary/10 border border-primary/20 mb-4">
                <i class="fas fa-id-card text-primary text-2xl"></i>
            </div>
            <h1 class="text-2xl font-black text-gray-800 font-cairo">استكمال الملف الشخصي</h1>
            <p class="text-gray-500 font-almarai text-sm mt-1">يرجى تعبئة البيانات خطوة بخطوة</p>
        </div>

        {{-- Progress Bar --}}
        <div class="mb-10">
            <div class="progress-container">
                <div id="progressBar" class="progress-bar bg-primary" style="width: 25%"></div>
            </div>
            <div class="flex justify-between mt-6">
                <div class="step active" id="step1">
                    <div class="step-circle">
                        <i class="fas fa-user text-xs"></i>
                    </div>
                    <div class="step-label">المعلومات الشخصية</div>
                </div>
                <div class="step inactive" id="step2">
                    <div class="step-circle">
                        <i class="fas fa-map-marker-alt text-xs"></i>
                    </div>
                    <div class="step-label">العنوان الدائم</div>
                </div>
                <div class="step inactive" id="step3">
                    <div class="step-circle">
                        <i class="fas fa-graduation-cap text-xs"></i>
                    </div>
                    <div class="step-label">المؤهل التعليمي</div>
                </div>
                <div class="step inactive" id="step4">
                    <div class="step-circle">
                        <i class="fas fa-university text-xs"></i>
                    </div>
                    <div class="step-label">الجامعة الحالية</div>
                </div>
            </div>
        </div>

        {{-- Success Message --}}
        @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-4 mb-6 flex items-center gap-3">
            <i class="fas fa-check-circle text-emerald-500"></i>
            <p class="text-emerald-800 font-bold font-cairo text-sm">{{ session('success') }}</p>
        </div>
        @endif

        {{-- Form Container --}}
        <form id="profileForm" action="{{ route('profile.complete.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            {{-- Global Errors Alert --}}
            @if($errors->any())
                <div class="bg-red-50 border-r-4 border-red-500 rounded-2xl p-6 mb-6 shadow-sm">
                    <div class="flex items-center gap-3 mb-4 text-red-700">
                        <i class="fas fa-exclamation-triangle text-xl"></i>
                        <h3 class="font-black font-cairo">يرجى تصحيح الأخطاء التالية قبل المحاولة مرة أخرى:</h3>
                    </div>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li class="text-red-600 font-almarai text-xs font-bold leading-relaxed">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Step 1: Personal Information --}}
            <div id="step1Content" class="form-section fade-in">
                <div class="section-header bg-gradient-to-l from-blue-50 to-indigo-50">
                    <div class="w-9 h-9 rounded-xl bg-blue-600 flex items-center justify-center">
                        <i class="fas fa-user text-white text-sm"></i>
                    </div>
                    <div>
                        <h2 class="font-black text-gray-800 font-cairo text-sm">١. المعلومات الشخصية</h2>
                        <p class="text-gray-400 text-xs font-almarai">البيانات الأساسية للطالب</p>
                    </div>
                </div>
                <div class="section-body grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-2">
                        <label class="field-label">الاسم الرباعي <span class="required-star">*</span></label>
                        <input type="text" name="name_ar" value="{{ old('name_ar', $student->name_ar) }}" required
                            class="field-input @error('name_ar') border-red-500 @enderror">
                        @error('name_ar') <p class="text-red-500 text-[10px] font-bold mt-1 font-cairo"><i class="fas fa-exclamation-circle ml-1"></i> {{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="field-label">Full Name (English)</label>
                        <input type="text" name="name_en" value="{{ old('name_en', $student->name_en) }}" dir="ltr"
                            class="field-input @error('name_en') border-red-500 @enderror" placeholder="As in Passport (Optional)">
                        @error('name_en') <p class="text-red-500 text-[10px] font-bold mt-1 font-cairo"><i class="fas fa-exclamation-circle ml-1"></i> {{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="field-label">اللقب</label>
                        <input type="text" name="surname" value="{{ old('surname', $student->surname) }}"
                            class="field-input @error('surname') border-red-500 @enderror"
                            placeholder="اللقب / الكنية">
                        @error('surname') <p class="text-red-500 text-[10px] font-bold mt-1 font-cairo"><i class="fas fa-exclamation-circle ml-1"></i> {{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="field-label">تاريخ الميلاد <span class="required-star">*</span></label>
                        <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $student->date_of_birth?->format('Y-m-d')) }}" required
                            class="field-input @error('date_of_birth') border-red-500 @enderror">
                        @error('date_of_birth') <p class="text-red-500 text-[10px] font-bold mt-1 font-cairo"><i class="fas fa-exclamation-circle ml-1"></i> {{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="field-label">مكان الميلاد</label>
                        <input type="text" name="place_of_birth" value="{{ old('place_of_birth', $student->place_of_birth) }}"
                            class="field-input @error('place_of_birth') border-red-500 @enderror"
                            placeholder="المدينة / المحافظة">
                        @error('place_of_birth') <p class="text-red-500 text-[10px] font-bold mt-1 font-cairo"><i class="fas fa-exclamation-circle ml-1"></i> {{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="field-label">المدينة الحالية</label>
                        <input type="text" name="city" value="{{ old('city', $student->city) }}"
                            class="field-input @error('city') border-red-500 @enderror"
                            placeholder="المدينة التي تقيم فيها">
                        @error('city') <p class="text-red-500 text-[10px] font-bold mt-1 font-cairo"><i class="fas fa-exclamation-circle ml-1"></i> {{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="field-label">رقم البطاقة / الجواز</label>
                        <input type="text" name="id_card_number" value="{{ old('id_card_number', $student->id_card_number) }}"
                            class="field-input @error('id_card_number') border-red-500 @enderror"
                            placeholder="رقم الوثيقة">
                        @error('id_card_number') <p class="text-red-500 text-[10px] font-bold mt-1 font-cairo"><i class="fas fa-exclamation-circle ml-1"></i> {{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="field-label">مصدر البطاقة</label>
                        <input type="text" name="id_card_source" value="{{ old('id_card_source', $student->id_card_source) }}"
                            class="field-input @error('id_card_source') border-red-500 @enderror"
                            placeholder="الجهة المُصدِرة">
                        @error('id_card_source') <p class="text-red-500 text-[10px] font-bold mt-1 font-cairo"><i class="fas fa-exclamation-circle ml-1"></i> {{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="field-label">تاريخ البطاقة</label>
                        <input type="date" name="id_card_date" value="{{ old('id_card_date', $student->id_card_date?->format('Y-m-d')) }}"
                            class="field-input @error('id_card_date') border-red-500 @enderror">
                        @error('id_card_date') <p class="text-red-500 text-[10px] font-bold mt-1 font-cairo"><i class="fas fa-exclamation-circle ml-1"></i> {{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="field-label">الجنسية <span class="required-star">*</span></label>
                        <input type="text" name="nationality" value="{{ old('nationality', $student->nationality) }}" required
                            class="field-input @error('nationality') border-red-500 @enderror"
                            placeholder="يمني / ...">
                        @error('nationality') <p class="text-red-500 text-[10px] font-bold mt-1 font-cairo"><i class="fas fa-exclamation-circle ml-1"></i> {{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="field-label">الحالة الاجتماعية</label>
                        <select name="marital_status"
                            class="field-select">
                            <option value="">-- اختر --</option>
                            @foreach(['single'=>'أعزب','married'=>'متزوج','divorced'=>'مطلق','widowed'=>'أرمل'] as $val=>$lbl)
                                <option value="{{ $val }}" {{ old('marital_status',$student->marital_status)==$val?'selected':'' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="field-label">عدد أفراد الأسرة المعالة</label>
                        <input type="number" name="dependents_count" value="{{ old('dependents_count', $student->dependents_count ?? 0) }}" min="0" max="30"
                            class="field-input @error('dependents_count') border-red-500 @enderror">
                        @error('dependents_count') <p class="text-red-500 text-[10px] font-bold mt-1 font-cairo"><i class="fas fa-exclamation-circle ml-1"></i> {{ $message }}</p> @enderror
                    </div>
                    <div class="md:col-span-3">
                        <label class="field-label">الحالة الصحية</label>
                        <div class="flex gap-6">
                            @foreach(['good'=>'جيدة','average'=>'متوسطة','weak'=>'ضعيفة'] as $val=>$lbl)
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="radio" name="health_status" value="{{ $val }}"
                                    {{ old('health_status',$student->health_status??'good')==$val?'checked':'' }}
                                    class="w-4 h-4 text-primary border-gray-300 focus:ring-primary">
                                <span class="font-almarai text-sm text-gray-700 group-hover:text-primary transition-colors">{{ $lbl }}</span>
                            </label>
                            @endforeach
                        </div>
                        @error('health_status') <p class="text-red-500 text-[10px] font-bold mt-1 font-cairo"><i class="fas fa-exclamation-circle ml-1"></i> {{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="field-label">رقم الجوال <span class="required-star">*</span></label>
                        <input type="tel" name="phone" value="{{ old('phone', $student->phone) }}" required
                            class="field-input @error('phone') border-red-500 @enderror"
                            placeholder="07XXXXXXXX">
                        @error('phone') <p class="text-red-500 text-[10px] font-bold mt-1 font-cairo"><i class="fas fa-exclamation-circle ml-1"></i> {{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="field-label">البريد الإلكتروني <span class="required-star">*</span></label>
                        <input type="email" name="email" value="{{ old('email', $student->email) }}" required
                            class="field-input @error('email') border-red-500 @enderror"
                            placeholder="student@example.com">
                        @error('email') <p class="text-red-500 text-[10px] font-bold mt-1 font-cairo"><i class="fas fa-exclamation-circle ml-1"></i> {{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- Step 2: Permanent Address --}}
            <div id="step2Content" class="form-section hidden">
                <div class="section-header bg-gradient-to-l from-emerald-50 to-teal-50">
                    <div class="w-9 h-9 rounded-xl bg-emerald-600 flex items-center justify-center">
                        <i class="fas fa-map-marker-alt text-white text-sm"></i>
                    </div>
                    <div>
                        <h2 class="font-black text-gray-800 font-cairo text-sm">٢. العنوان الدائم</h2>
                        <p class="text-gray-400 text-xs font-almarai">موقع السكن الأصلي</p>
                    </div>
                </div>
                <div class="section-body grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="field-label">المحافظة</label>
                        <input type="text" name="governorate" value="{{ old('governorate', $student->governorate) }}"
                            class="field-input"
                            placeholder="المحافظة">
                    </div>
                    <div>
                        <label class="field-label">المديرية</label>
                        <input type="text" name="district" value="{{ old('district', $student->district) }}"
                            class="field-input"
                            placeholder="المديرية">
                    </div>
                    <div>
                        <label class="field-label">القرية</label>
                        <input type="text" name="village" value="{{ old('village', $student->village) }}"
                            class="field-input"
                            placeholder="القرية / الحي">
                    </div>
                    <div>
                        <label class="field-label">هاتف المنزل</label>
                        <input type="tel" name="home_phone" value="{{ old('home_phone', $student->home_phone) }}"
                            class="field-input"
                            placeholder="هاتف ثابت">
                    </div>
                    <div class="md:col-span-2">
                        <label class="field-label">العنوان التفصيلي</label>
                        <input type="text" name="permanent_address" value="{{ old('permanent_address', $student->permanent_address) }}"
                            class="field-input"
                            placeholder="وصف تفصيلي للعنوان">
                    </div>
                </div>
            </div>

            {{-- Step 3: Educational Qualification --}}
            <div id="step3Content" class="form-section hidden">
                <div class="section-header bg-gradient-to-l from-violet-50 to-purple-50">
                    <div class="w-9 h-9 rounded-xl bg-violet-600 flex items-center justify-center">
                        <i class="fas fa-graduation-cap text-white text-sm"></i>
                    </div>
                    <div>
                        <h2 class="font-black text-gray-800 font-cairo text-sm">٣. المؤهل التعليمي</h2>
                        <p class="text-gray-400 text-xs font-almarai">آخر شهادة حصلت عليها</p>
                    </div>
                </div>
                <div class="section-body grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-2">
                        <label class="field-label">آخر شهادة حصل عليها</label>
                        <input type="text" name="last_certificate" value="{{ old('last_certificate', $student->last_certificate) }}"
                            class="field-input"
                            placeholder="ثانوية عامة / بكالوريوس / ...">
                    </div>
                    <div>
                        <label class="field-label">التخصص</label>
                        <input type="text" name="last_cert_major" value="{{ old('last_cert_major', $student->last_cert_major) }}"
                            class="field-input"
                            placeholder="التخصص">
                    </div>
                    <div>
                        <label class="field-label">التقدير</label>
                        <select name="last_cert_grade"
                            class="field-select">
                            <option value="">-- التقدير --</option>
                            @foreach(['امتياز','ممتاز','جيد جداً','جيد','مقبول','ضعيف'] as $g)
                                <option value="{{ $g }}" {{ old('last_cert_grade',$student->last_cert_grade)==$g?'selected':'' }}>{{ $g }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="field-label">سنة التخرج</label>
                        <input type="number" name="graduation_year" value="{{ old('graduation_year', $student->graduation_year) }}"
                            min="1990" max="2030" placeholder="2024"
                            class="field-input">
                    </div>
                    <div>
                        <label class="field-label">المدرسة / الجهة المتخرج منها</label>
                        <input type="text" name="graduated_school" value="{{ old('graduated_school', $student->graduated_school) }}"
                            class="field-input"
                            placeholder="اسم المدرسة أو الجامعة">
                    </div>
                </div>
            </div>

            {{-- Step 4: University Information --}}
            <div id="step4Content" class="form-section hidden">
                <div class="section-header bg-gradient-to-l from-amber-50 to-orange-50">
                    <div class="w-9 h-9 rounded-xl bg-amber-500 flex items-center justify-center">
                        <i class="fas fa-university text-white text-sm"></i>
                    </div>
                    <div>
                        <h2 class="font-black text-gray-800 font-cairo text-sm">٤. الجامعة الحالية</h2>
                        <p class="text-gray-400 text-xs font-almarai">بيانات الدراسة الجامعية الحالية</p>
                    </div>
                </div>
                <div class="section-body grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="field-label">الرقم الجامعي <span class="required-star">*</span></label>
                        <input type="text" name="student_number" value="{{ old('student_number', $student->student_number) }}" required
                            class="field-input @error('student_number') border-red-500 @enderror"
                            placeholder="442XXXXXX">
                        @error('student_number') <p class="text-red-500 text-[10px] font-bold mt-1 font-cairo"><i class="fas fa-exclamation-circle ml-1"></i> {{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="field-label">الجامعة <span class="required-star">*</span></label>
                        <input type="text" name="university" value="{{ old('university', $student->university) }}" required
                            class="field-input @error('university') border-red-500 @enderror">
                        @error('university') <p class="text-red-500 text-[10px] font-bold mt-1 font-cairo"><i class="fas fa-exclamation-circle ml-1"></i> {{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="field-label">الكلية <span class="required-star">*</span></label>
                        <input type="text" name="college" value="{{ old('college', $student->college) }}" required
                            class="field-input @error('college') border-red-500 @enderror">
                        @error('college') <p class="text-red-500 text-[10px] font-bold mt-1 font-cairo"><i class="fas fa-exclamation-circle ml-1"></i> {{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="field-label">التخصص <span class="required-star">*</span></label>
                        <input type="text" name="major" value="{{ old('major', $student->major) }}" required
                            class="field-input @error('major') border-red-500 @enderror">
                        @error('major') <p class="text-red-500 text-[10px] font-bold mt-1 font-cairo"><i class="fas fa-exclamation-circle ml-1"></i> {{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="field-label">المستوى الدراسي <span class="required-star">*</span></label>
                        <select name="academic_level" required
                            class="field-select">
                            @foreach(['1'=>'الأول','2'=>'الثاني','3'=>'الثالث','4'=>'الرابع','5'=>'الخامس','6'=>'السادس','7'=>'السابع','8'=>'الثامن','superior'=>'دراسات عليا'] as $v=>$l)
                                <option value="{{ $v }}" {{ old('academic_level',$student->academic_level)==$v?'selected':'' }}>المستوى {{ $l }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="field-label">السنة الدراسية الحالية</label>
                        <input type="text" name="current_academic_year" value="{{ old('current_academic_year', $student->current_academic_year) }}"
                            placeholder="2024/2025"
                            class="field-input">
                    </div>
                    <div>
                        <label class="field-label">تاريخ الالتحاق</label>
                        <input type="date" name="enrollment_date" value="{{ old('enrollment_date', $student->enrollment_date?->format('Y-m-d')) }}"
                            class="field-input">
                    </div>
                    <div>
                        <label class="field-label">مدة الدراسة</label>
                        <input type="text" name="study_duration" value="{{ old('study_duration', $student->study_duration) }}"
                            placeholder="4 سنوات"
                            class="field-input">
                    </div>
                    <div>
                        <label class="field-label">الفترة المتبقية</label>
                        <input type="text" name="remaining_period" value="{{ old('remaining_period', $student->remaining_period) }}"
                            placeholder="سنتان"
                            class="field-input">
                    </div>
                    <div>
                        <label class="field-label">التاريخ المتوقع للتخرج</label>
                        <input type="date" name="expected_graduation" value="{{ old('expected_graduation', $student->expected_graduation?->format('Y-m-d')) }}"
                            class="field-input">
                    </div>
                    <div class="md:col-span-3">
                        <label class="field-label">الأعمال والمهارات التي تجيدها</label>
                        <textarea name="skills" rows="2"
                            class="field-input resize-none"
                            placeholder="مثال: الحاسوب، التصوير، الخطابة، الصيانة...">{{ old('skills', $student->skills) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Navigation Buttons --}}
            <div class="flex justify-between mt-8">
                <button type="button" id="prevBtn" class="btn-secondary hidden">
                    <i class="fas fa-arrow-left mr-2"></i>السابق
                </button>
                <button type="button" id="nextBtn" class="btn-primary ml-auto">
                    التالي<i class="fas fa-arrow-right ml-2"></i>
                </button>
                <button type="submit" id="submitBtn" class="btn-primary hidden">
                    <i class="fas fa-check mr-2"></i>إرسال البيانات
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let currentStep = 1;
        const totalSteps = 4;
        const progressBar = document.getElementById('progressBar');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const submitBtn = document.getElementById('submitBtn');

        // Update progress bar
        function updateProgressBar(step) {
            const progress = (step / totalSteps) * 100;
            progressBar.style.width = progress + '%';
        }

        // Show step content
        function showStep(step) {
            // Hide all steps
            for (let i = 1; i <= totalSteps; i++) {
                document.getElementById(`step${i}Content`).classList.add('hidden');
                document.getElementById(`step${i}`).classList.remove('active');
                document.getElementById(`step${i}`).classList.add(step > i ? 'completed' : 'inactive');
            }

            // Show current step
            document.getElementById(`step${step}Content`).classList.remove('hidden');
            document.getElementById(`step${step}`).classList.add('active');
            document.getElementById(`step${step}`).classList.remove('completed', 'inactive');

            // Update progress bar
            updateProgressBar(step);

            // Update navigation buttons
            if (step === 1) {
                prevBtn.classList.add('hidden');
            } else {
                prevBtn.classList.remove('hidden');
            }

            if (step === totalSteps) {
                nextBtn.classList.add('hidden');
                submitBtn.classList.remove('hidden');
            } else {
                nextBtn.classList.remove('hidden');
                submitBtn.classList.add('hidden');
            }

            // Add fade-in animation
            const currentContent = document.getElementById(`step${step}Content`);
            currentContent.classList.add('fade-in');
            setTimeout(() => {
                currentContent.classList.remove('fade-in');
            }, 500);
        }

        // Next button click handler
        nextBtn.addEventListener('click', function() {
            if (validateStep(currentStep)) {
                currentStep++;
                showStep(currentStep);
            }
        });

        // Previous button click handler
        prevBtn.addEventListener('click', function() {
            currentStep--;
            showStep(currentStep);
        });

        // Form submission handler
        document.getElementById('profileForm').addEventListener('submit', function(e) {
            e.preventDefault();
            if (validateStep(currentStep)) {
                this.submit();
            }
        });

        // Validate current step
        function validateStep(step) {
            let isValid = true;
            const currentStepContent = document.getElementById(`step${step}Content`);
            const requiredFields = currentStepContent.querySelectorAll('input[required], select[required]');

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('border-red-500');
                    isValid = false;
                } else {
                    field.classList.remove('border-red-500');
                }
            });

            return isValid;
        }

        // Initialize
        showStep(1);
    });
</script>
@endpush
