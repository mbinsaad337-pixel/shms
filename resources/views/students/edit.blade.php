@extends ('layouts.app')
@php
    /** @var \App\Models\Student $student */
@endphp

@section ('title', 'تعديل بيانات الطالب: ' . $student->name_ar)

@section ('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-5xl mx-auto">

            <div class="mb-6 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <a href="{{ route('students.show', $student) }}" class="w-10 h-10 bg-white rounded-full shadow-sm flex items-center justify-center text-gray-400 hover:text-navy transition-all">
                        <i class="fas fa-arrow-right"></i>
                    </a>
                    <h1 class="text-2xl font-bold font-cairo text-gray-800">تعديل الملف الشخصي للطلب</h1>
                </div>
            </div>

            @if ($errors->any())
                <div class="bg-red-50 border-r-4 border-red-500 p-4 rounded-xl mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-500"></i>
                        </div>
                        <div class="mr-3">
                            <h3 class="text-sm font-bold text-red-800 font-almarai">يرجى تصحيح الأخطاء التالية:</h3>
                            <ul class="mt-2 list-disc list-inside text-sm text-red-700 font-almarai">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <form action="{{ route('students.update', $student) }}" method="POST" class="space-y-8 text-right" dir="rtl">
                @csrf
                @method('PUT')

                <!-- Section 1: Basic Information -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                    <h3 class="text-xl font-bold font-cairo text-gray-800 mb-6 flex items-center gap-3 border-b border-gray-100 pb-4">
                        <i class="fas fa-user-circle text-primary"></i> المعلومات الأساسية والعامة
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 font-almarai text-sm">
                        <div class="md:col-span-2">
                            <label class="block text-gray-700 font-bold mb-2">الاسم الرباعي (عربي) *</label>
                            <input type="text" name="name_ar" value="{{ old('name_ar', $student->name_ar) }}" required
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all">
                        </div>

                        <div>
                            <label class="block text-gray-700 font-bold mb-2">اللقب / القبيلة</label>
                            <input type="text" name="surname" value="{{ old('surname', $student->surname) }}"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all">
                        </div>

                        <div>
                            <label class="block text-gray-700 font-bold mb-2">نظام التسكين التابع له *</label>
                            <select name="program_id" required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary outline-none bg-white">
                                @foreach($programs ?? [] as $program)
                                    <option value="{{ $program->id }}" {{ old('program_id', $student->program_id) == $program->id ? 'selected' : '' }}>
                                        {{ $program->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="md:col-span-3">
                            <label class="block text-gray-700 font-bold mb-2">Full Name (English)</label>
                            <input type="text" name="name_en" value="{{ old('name_en', $student->name_en) }}" dir="ltr"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all text-left  ">
                        </div>

                        <div>
                            <label class="block text-gray-700 font-bold mb-2">الجنسية</label>
                            <input type="text" name="nationality" value="{{ old('nationality', $student->nationality) }}"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all">
                        </div>

                        <div>
                            <label class="block text-gray-700 font-bold mb-2">تاريخ الميلاد</label>
                            <input type="date" name="date_of_birth" 
                                value="{{ old('date_of_birth', $student->date_of_birth ? $student->date_of_birth->format('Y-m-d') : '') }}"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all">
                        </div>

                        <div>
                            <label class="block text-gray-700 font-bold mb-2">مكان الميلاد</label>
                            <input type="text" name="place_of_birth" value="{{ old('place_of_birth', $student->place_of_birth) }}"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all">
                        </div>

                        <div>
                            <label class="block text-gray-700 font-bold mb-2">فصيلة الدم</label>
                            <select name="blood_type" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary outline-none bg-white">
                                <option value="">اختر...</option>
                                @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $type)
                                    <option value="{{ $type }}" {{ old('blood_type', $student->blood_type) == $type ? 'selected' : '' }}>{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-gray-700 font-bold mb-2">الحالة الاجتماعية</label>
                            <select name="marital_status" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary outline-none bg-white">
                                <option value="single" {{ old('marital_status', $student->marital_status) == 'single' ? 'selected' : '' }}>أعزب</option>
                                <option value="married" {{ old('marital_status', $student->marital_status) == 'married' ? 'selected' : '' }}>متزوج</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-gray-700 font-bold mb-2">الحالة الصحية</label>
                            <input type="text" name="health_status" value="{{ old('health_status', $student->health_status) }}" placeholder="سليم، أو اذكر أي أمراض"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary outline-none">
                        </div>
                    </div>
                </div>

                <!-- Section 2: Identification -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                    <h3 class="text-xl font-bold font-cairo text-gray-800 mb-6 flex items-center gap-3 border-b border-gray-100 pb-4">
                        <i class="fas fa-id-card text-indigo-500"></i> بيانات الهوية
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 font-almarai text-sm">
                        <div>
                            <label class="block text-gray-700 font-bold mb-2">رقم الهوية الوطنية / الإقامة *</label>
                            <input type="text" name="national_id" value="{{ old('national_id', $student->national_id) }}" required
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none  ">
                        </div>

                        <div>
                            <label class="block text-gray-700 font-bold mb-2">رقم البطاقة الشخصية (إن وجد)</label>
                            <input type="text" name="id_card_number" value="{{ old('id_card_number', $student->id_card_number) }}"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none  ">
                        </div>

                        <div>
                            <label class="block text-gray-700 font-bold mb-2">مصدر البطاقة</label>
                            <input type="text" name="id_card_source" value="{{ old('id_card_source', $student->id_card_source) }}"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none">
                        </div>

                        <div>
                            <label class="block text-gray-700 font-bold mb-2">تاريخ إصدار البطاقة</label>
                            <input type="date" name="id_card_date" 
                                value="{{ old('id_card_date', $student->id_card_date ? $student->id_card_date->format('Y-m-d') : '') }}"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none">
                        </div>
                    </div>
                </div>

                <!-- Section 3: Contact and Address -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                    <h3 class="text-xl font-bold font-cairo text-gray-800 mb-6 flex items-center gap-3 border-b border-gray-100 pb-4">
                        <i class="fas fa-map-marker-alt text-green-500"></i> بيانات التواصل والعنوان
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 font-almarai text-sm">
                        <div>
                            <label class="block text-gray-700 font-bold mb-2">رقم الجوال *</label>
                            <input type="text" name="phone" value="{{ old('phone', $student->phone) }}" required dir="ltr"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 outline-none   text-left">
                        </div>

                        <div>
                            <label class="block text-gray-700 font-bold mb-2">البريد الإلكتروني</label>
                            <input type="email" name="email" value="{{ old('email', $student->email ?? ($student->user->email ?? '')) }}" dir="ltr"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 outline-none   text-left">
                        </div>

                        <div>
                            <label class="block text-gray-700 font-bold mb-2">تلفون المنزل (إن وجد)</label>
                            <input type="text" name="home_phone" value="{{ old('home_phone', $student->home_phone) }}" dir="ltr"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 outline-none   text-left">
                        </div>

                        <div>
                            <label class="block text-gray-700 font-bold mb-2">المحافظة</label>
                            <input type="text" name="governorate" value="{{ old('governorate', $student->governorate) }}"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 outline-none">
                        </div>

                        <div>
                            <label class="block text-gray-700 font-bold mb-2">المديرية</label>
                            <input type="text" name="district" value="{{ old('district', $student->district) }}"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 outline-none">
                        </div>

                        <div>
                            <label class="block text-gray-700 font-bold mb-2">العزلة / القرية</label>
                            <input type="text" name="village" value="{{ old('village', $student->village) }}"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 outline-none">
                        </div>

                        <div class="md:col-span-3">
                            <label class="block text-gray-700 font-bold mb-2">العنوان الدائم بالتفصيل</label>
                            <textarea name="permanent_address" rows="2"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 outline-none transition-all">{{ old('permanent_address', $student->permanent_address) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Section 4: Academic Information -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                    <h3 class="text-xl font-bold font-cairo text-gray-800 mb-6 flex items-center gap-3 border-b border-gray-100 pb-4">
                        <i class="fas fa-graduation-cap text-blue-500"></i> البيانات الأكاديمية والجامعية
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 font-almarai text-sm">
                        <div>
                            <label class="block text-gray-700 font-bold mb-2">الرقم الجامعي</label>
                            <input type="text" name="student_number" value="{{ old('student_number', $student->student_number) }}"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none  ">
                        </div>

                        <div>
                            <label class="block text-gray-700 font-bold mb-2">الجامعة</label>
                            <input type="text" name="university" value="{{ old('university', $student->university) }}"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>

                        <div>
                            <label class="block text-gray-700 font-bold mb-2">الكلية</label>
                            <input type="text" name="college" value="{{ old('college', $student->college) }}"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>

                        <div>
                            <label class="block text-gray-700 font-bold mb-2">التخصص</label>
                            <input type="text" name="major" value="{{ old('major', $student->major) }}"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>

                        <div>
                            <label class="block text-gray-700 font-bold mb-2">المستوى الدراسي الحالي</label>
                            <select name="academic_level" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none bg-white font-almarai">
                                <option value="">اختر المستوى...</option>
                                @for($i=1; $i<=10; $i++)
                                    <option value="{{ $i }}" {{ old('academic_level', $student->academic_level) == $i ? 'selected' : '' }}>المستوى {{ $i }}</option>
                                @endfor
                                <option value="superior" {{ old('academic_level', $student->academic_level) == 'superior' ? 'selected' : '' }}>دراسات عليا</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-gray-700 font-bold mb-2">تاريخ القبول بالجامعة</label>
                            <input type="date" name="enrollment_date" 
                                value="{{ old('enrollment_date', $student->enrollment_date ? $student->enrollment_date->format('Y-m-d') : '') }}"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>

                        <div>
                            <label class="block text-gray-700 font-bold mb-2">سنة التخرج المتوقعة</label>
                            <input type="text" name="expected_graduation" value="{{ old('expected_graduation', $student->expected_graduation instanceof \Carbon\Carbon ? $student->expected_graduation->format('Y-m-d') : $student->expected_graduation) }}"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none" placeholder="مثلاً: 2027">
                        </div>

                        <div>
                            <label class="block text-gray-700 font-bold mb-2">الرسوم السنوية المطلوبة</label>
                            <input type="number" name="annual_fees" step="0.01" value="{{ old('annual_fees', $student->annual_fees) }}"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none font-bold text-blue-700">
                            <select name="annual_fee_currency" class="mt-2 w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                                @foreach(\App\Models\Fund::CURRENCIES as $code => $label)
                                    <option value="{{ $code }}" {{ old('annual_fee_currency', $student->annual_fee_currency ?? 'YER') === $code ? 'selected' : '' }}>{{ $label }} ({{ \App\Models\Fund::CURRENCY_SYMBOLS[$code] }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mt-8 border-t border-gray-50 pt-6">
                        <h4 class="text-md font-bold text-gray-600 mb-4 font-cairo">بيانات آخر مؤهل دراسي حصلت عليه</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 font-almarai">
                            <div>
                                <label class="block text-gray-600 text-xs mb-1 font-bold">آخر مؤهل حاصل عليه (مثلاً ثانوية عامة)</label>
                                <input type="text" name="last_certificate" value="{{ old('last_certificate', $student->last_certificate) }}"
                                    class="w-full px-4 py-2 border border-gray-100 rounded-lg outline-none focus:ring-1 focus:ring-blue-300">
                            </div>
                            <div>
                                <label class="block text-gray-600 text-xs mb-1 font-bold">المدرسة أو جهة التخرج</label>
                                <input type="text" name="graduated_school" value="{{ old('graduated_school', $student->graduated_school) }}"
                                    class="w-full px-4 py-2 border border-gray-100 rounded-lg outline-none focus:ring-1 focus:ring-blue-300">
                            </div>
                            <div>
                                <label class="block text-gray-600 text-xs mb-1 font-bold">التخصص في المؤهل</label>
                                <input type="text" name="last_cert_major" value="{{ old('last_cert_major', $student->last_cert_major) }}"
                                    class="w-full px-4 py-2 border border-gray-100 rounded-lg outline-none focus:ring-1 focus:ring-blue-300">
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-gray-600 text-xs mb-1 font-bold">سنة الحصول عليه</label>
                                    <input type="text" name="graduation_year" value="{{ old('graduation_year', $student->graduation_year) }}"
                                        class="w-full px-4 py-2 border border-gray-100 rounded-lg outline-none focus:ring-1 focus:ring-blue-300">
                                </div>
                                <div>
                                    <label class="block text-gray-600 text-xs mb-1 font-bold">التقدير / النسبة</label>
                                    <input type="text" name="last_cert_grade" value="{{ old('last_cert_grade', $student->last_cert_grade) }}"
                                        class="w-full px-4 py-2 border border-gray-100 rounded-lg outline-none focus:ring-1 focus:ring-blue-300">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 5: Guardian and Family -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                    <h3 class="text-xl font-bold font-cairo text-gray-800 mb-6 flex items-center gap-3 border-b border-gray-100 pb-4">
                        <i class="fas fa-users text-orange-500"></i> بيانات ولي الأمر والأسرة
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 font-almarai text-sm">
                        <div class="md:col-span-2">
                            <label class="block text-gray-700 font-bold mb-2">اسم ولي الأمر</label>
                            <input type="text" name="guardian_name" value="{{ old('guardian_name', $student->guardian_name) }}"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 outline-none">
                        </div>

                        <div>
                            <label class="block text-gray-700 font-bold mb-2">صلة القرابة</label>
                            <input type="text" name="guardian_relation" value="{{ old('guardian_relation', $student->guardian_relation) }}"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 outline-none">
                        </div>

                        <div>
                            <label class="block text-gray-700 font-bold mb-2">رقم هاتف ولي الأمر</label>
                            <input type="text" name="guardian_phone" value="{{ old('guardian_phone', $student->guardian_phone) }}" dir="ltr"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 outline-none   text-left">
                        </div>

                        <div>
                            <label class="block text-gray-700 font-bold mb-2">مهنة ولي الأمر</label>
                            <input type="text" name="guardian_job" value="{{ old('guardian_job', $student->guardian_job) }}"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 outline-none">
                        </div>

                        <div>
                            <label class="block text-gray-700 font-bold mb-2">المستوى التعليمي لولي الأمر</label>
                            <input type="text" name="guardian_education" value="{{ old('guardian_education', $student->guardian_education) }}"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 outline-none">
                        </div>
                    </div>

                    <div class="mt-8 border-t border-gray-50 pt-6">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-md font-bold text-gray-600 font-cairo">العاملون من أفراد الأسرة</h4>
                            <button type="button" onclick="addWorkerRow()" class="px-3 py-1.5 bg-cyan-600 text-white rounded-lg text-xs font-bold font-cairo shadow-sm hover:bg-cyan-700">
                                <i class="fas fa-plus ml-1"></i> إضافة فرد عامل
                            </button>
                        </div>
                        
                        <div class="overflow-x-auto rounded-xl border border-gray-100 mb-2">
                            <table class="w-full text-right text-xs font-almarai">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="p-3 border-b text-center w-12">م</th>
                                        <th class="p-3 border-b">الاسم</th>
                                        <th class="p-3 border-b">الوظيفة / العمل</th>
                                        <th class="p-3 border-b">المؤسسة / الجهة</th>
                                        <th class="p-3 border-b">رقم الهاتف</th>
                                        <th class="p-3 border-b w-12 text-center">حذف</th>
                                    </tr>
                                </thead>
                                <tbody id="workersBody">
                                    @php $workers = old('workers', $student->family_workers ?? []); @endphp
                                    @forelse($workers as $index => $worker)
                                        <tr class="border-b border-gray-50 hover:bg-gray-50/50">
                                            <td class="p-2 text-center text-gray-400   worker-num">{{ $loop->iteration }}</td>
                                            <td class="p-2"><input type="text" name="workers[{{ $index }}][name]" value="{{ $worker['name'] ?? '' }}" class="w-full px-2 py-1.5 border border-gray-100 rounded-lg outline-none focus:ring-1 focus:ring-cyan-300"></td>
                                            <td class="p-2"><input type="text" name="workers[{{ $index }}][job]" value="{{ $worker['job'] ?? '' }}" class="w-full px-2 py-1.5 border border-gray-100 rounded-lg outline-none focus:ring-1 focus:ring-cyan-300"></td>
                                            <td class="p-2"><input type="text" name="workers[{{ $index }}][organization]" value="{{ $worker['organization'] ?? '' }}" class="w-full px-2 py-1.5 border border-gray-100 rounded-lg outline-none focus:ring-1 focus:ring-cyan-300"></td>
                                            <td class="p-2"><input type="text" name="workers[{{ $index }}][phone]" value="{{ $worker['phone'] ?? '' }}" class="w-full px-2 py-1.5 border border-gray-100 rounded-lg outline-none focus:ring-1 focus:ring-cyan-300"></td>
                                            <td class="p-2 text-center"><button type="button" onclick="removeWorkerRow(this)" class="text-red-400 hover:text-red-600"><i class="fas fa-trash"></i></button></td>
                                        </tr>
                                    @empty
                                        <tr class="placeholder-row"><td colspan="6" class="p-4 text-center text-gray-400 italic">لا يوجد أفراد مسجلون حالياً</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <p class="text-[10px] text-gray-400">اترك الحقول فارغة إذا لم يكن هناك أفراد عاملون</p>
                    </div>
                </div>

                <!-- Section 6: Emergency Information -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                    <h3 class="text-xl font-bold font-cairo text-gray-800 mb-6 flex items-center gap-3 border-b border-gray-100 pb-4">
                        <i class="fas fa-phone-alt text-red-500"></i> بيانات الطوارئ
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 font-almarai text-sm">
                        <div class="md:col-span-2">
                            <label class="block text-gray-700 font-bold mb-2">اسم شخص للتواصل في الطوارئ</label>
                            <input type="text" name="emergency_name" value="{{ old('emergency_name', $student->emergency_name) }}"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-300 outline-none">
                        </div>

                        <div>
                            <label class="block text-gray-700 font-bold mb-2">صلة القرابة</label>
                            <input type="text" name="emergency_relation" value="{{ old('emergency_relation', $student->emergency_relation) }}"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-300 outline-none">
                        </div>

                        <div>
                            <label class="block text-gray-700 font-bold mb-2">رقم هاتف الطوارئ</label>
                            <input type="text" name="emergency_phone" value="{{ old('emergency_phone', $student->emergency_phone) }}" dir="ltr"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-300 outline-none   text-left">
                        </div>
                    </div>
                </div>

                <!-- Section 7: System and Other Info -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                    <h3 class="text-xl font-bold font-cairo text-gray-800 mb-6 flex items-center gap-3 border-b border-gray-100 pb-4">
                        <i class="fas fa-cog text-gray-500"></i> إعدادات النظام ومهارات إضافية
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 font-almarai text-sm">
                        @if(auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('center-manager'))
                        <div>
                            <label class="block text-gray-700 font-bold mb-2">حالة الطالب بالمركز (للمسؤولين)</label>
                            <select name="status" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-gray-300 outline-none bg-white">
                                <option value="registered" {{ old('status', $student->status) == 'registered' ? 'selected' : '' }}>مسجل (مبدئي)</option>
                                <option value="residing" {{ old('status', $student->status) == 'residing' ? 'selected' : '' }}>مقيم</option>
                                <option value="left" {{ old('status', $student->status) == 'left' ? 'selected' : '' }}>غادر / إخلاء طرف</option>
                                <option value="graduated" {{ old('status', $student->status) == 'graduated' ? 'selected' : '' }}>متخرج</option>
                                <option value="suspended" {{ old('status', $student->status) == 'suspended' ? 'selected' : '' }}>موقوف</option>
                            </select>
                        </div>
                        @else
                           <input type="hidden" name="status" value="{{ $student->status }}">
                        @endif

                        <div>
                            <label class="block text-gray-700 font-bold mb-2">اسم المستخدم (للدخول)</label>
                            <input type="text" name="username" value="{{ old('username', $student->user->username) }}"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary outline-none   bg-gray-50" readonly>
                            <p class="text-xs text-gray-400 mt-1">اسم المستخدم لا يمكن تعديله حالياً</p>
                        </div>

                        <div>
                            <label class="block text-gray-700 font-bold mb-2">كلمة مرور جديدة (اختياري)</label>
                            <input type="password" name="password"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary outline-none" placeholder="اتركه فارغاً للحفاظ على الحالية">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-gray-700 font-bold mb-2 font-bold">المهارات والهوايات ومؤهلات أخرى</label>
                            <textarea name="skills" rows="3" placeholder="اذكر مهاراتك (مثلا: برمجة، خط، رسم، رياضة...)"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary outline-none">{{ old('skills', $student->skills) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-4 sticky bottom-6 z-10">
                    <button type="submit"
                        class="flex-1 md:flex-none px-12 py-4 bg-primary text-white rounded-2xl shadow-xl hover:bg-primary-dark font-bold font-cairo transition-all transform hover:-translate-y-1">
                        <i class="fas fa-save ml-2"></i> حفظ التحديثات النهائية
                    </button>
                    <a href="{{ route('students.show', $student) }}"
                        class="px-12 py-4 bg-white text-gray-500 border border-gray-100 rounded-2xl shadow-md hover:bg-gray-50 font-bold font-cairo transition-all">
                        إلغاء التعديل
                    </a>
                </div>

            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    let workerIndex = {{ count(old('workers', $student->family_workers ?? [])) }};
    
    function addWorkerRow() {
        const tbody = document.getElementById('workersBody');
        const placeholder = tbody.querySelector('.placeholder-row');
        if (placeholder) placeholder.remove();

        const row = document.createElement('tr');
        row.className = 'border-b border-gray-50 hover:bg-gray-50/50';
        row.innerHTML = `
            <td class="p-2 text-center text-gray-400   worker-num"></td>
            <td class="p-2"><input type="text" name="workers[${workerIndex}][name]" class="w-full px-2 py-1.5 border border-gray-100 rounded-lg outline-none focus:ring-1 focus:ring-cyan-300"></td>
            <td class="p-2"><input type="text" name="workers[${workerIndex}][job]" class="w-full px-2 py-1.5 border border-gray-100 rounded-lg outline-none focus:ring-1 focus:ring-cyan-300"></td>
            <td class="p-2"><input type="text" name="workers[${workerIndex}][organization]" class="w-full px-2 py-1.5 border border-gray-100 rounded-lg outline-none focus:ring-1 focus:ring-cyan-300"></td>
            <td class="p-2"><input type="text" name="workers[${workerIndex}][phone]" class="w-full px-2 py-1.5 border border-gray-100 rounded-lg outline-none focus:ring-1 focus:ring-cyan-300"></td>
            <td class="p-2 text-center"><button type="button" onclick="removeWorkerRow(this)" class="text-red-400 hover:text-red-600"><i class="fas fa-trash"></i></button></td>
        `;
        tbody.appendChild(row);
        workerIndex++;
        updateWorkerNumbers();
    }

    function removeWorkerRow(btn) {
        btn.closest('tr').remove();
        updateWorkerNumbers();
        
        const tbody = document.getElementById('workersBody');
        if (tbody.children.length === 0) {
            tbody.innerHTML = '<tr class="placeholder-row"><td colspan="6" class="p-4 text-center text-gray-400 italic">لا يوجد أفراد مسجلون حالياً</td></tr>';
        }
    }

    function updateWorkerNumbers() {
        document.querySelectorAll('.worker-num').forEach((td, i) => {
            td.textContent = i + 1;
        });
    }
</script>
@endpush
