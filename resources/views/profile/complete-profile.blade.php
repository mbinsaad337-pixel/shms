@extends('layouts.app')

@section('title', 'تعديل بيانات الملف الشخصي')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('dashboard') }}" class="px-6 py-3 bg-gray-50 text-navy rounded-2xl hover:bg-gray-100 font-cairo font-bold transition-all inline-flex items-center gap-2 border border-gray-100">
                <i class="fas fa-arrow-right"></i>
                <span>العودة للوحة التحكم</span>
            </a>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 border-r-4 border-red-500 p-4 rounded-xl mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-500"></i>
                    </div>
                    <div class="mr-3">
                        <h3 class="text-sm font-bold text-red-800 font-cairo">يرجى تصحيح الأخطاء التالية:</h3>
                        <ul class="mt-2 list-disc list-inside text-sm text-red-700 font-almarai">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <form action="{{ route('profile.complete.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf
            @method('POST')

            {{-- Section 1: Basic Information --}}
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                <h3 class="text-xl font-bold font-cairo text-gray-800 mb-6 flex items-center gap-3 border-b border-gray-100 pb-4">
                    <i class="fas fa-user-circle text-primary"></i> المعلومات الأساسية
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

                    <div class="md:col-span-3">
                        <label class="block text-gray-700 font-bold mb-2">Full Name (English)</label>
                        <input type="text" name="name_en" value="{{ old('name_en', $student->name_en) }}" dir="ltr"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all text-left">
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
                        <label class="block text-gray-700 font-bold mb-2">الحالة الاجتماعية</label>
                        <select name="marital_status" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary outline-none bg-white">
                            <option value="">اختر...</option>
                            <option value="single" {{ old('marital_status', $student->marital_status) == 'single' ? 'selected' : '' }}>أعزب</option>
                            <option value="married" {{ old('marital_status', $student->marital_status) == 'married' ? 'selected' : '' }}>متزوج</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-bold mb-2">الحالة الصحية</label>
                        <select name="health_status" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary outline-none bg-white">
                            <option value="">اختر...</option>
                            <option value="good" {{ old('health_status', $student->health_status) == 'good' ? 'selected' : '' }}>جيد</option>
                            <option value="average" {{ old('health_status', $student->health_status) == 'average' ? 'selected' : '' }}>متوسط</option>
                            <option value="weak" {{ old('health_status', $student->health_status) == 'weak' ? 'selected' : '' }}>ضعيف</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-bold mb-2">رقم الهوية الوطنية</label>
                        <input type="text" name="id_card_number" value="{{ old('id_card_number', $student->id_card_number) }}"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all">
                    </div>
                </div>
            </div>

            {{-- Section 2: Contact Information --}}
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                <h3 class="text-xl font-bold font-cairo text-gray-800 mb-6 flex items-center gap-3 border-b border-gray-100 pb-4">
                    <i class="fas fa-phone-alt text-green-500"></i> بيانات التواصل
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 font-almarai text-sm">
                    <div>
                        <label class="block text-gray-700 font-bold mb-2">رقم الجوال *</label>
                        <input type="text" name="phone" value="{{ old('phone', $student->phone) }}" required dir="ltr"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 outline-none text-left">
                    </div>

                    <div>
                        <label class="block text-gray-700 font-bold mb-2">البريد الإلكتروني *</label>
                        <input type="email" name="email" value="{{ old('email', $student->email ?? ($student->user->email ?? '')) }}" required dir="ltr"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 outline-none text-left">
                    </div>

                    <div>
                        <label class="block text-gray-700 font-bold mb-2">تلفون المنزل</label>
                        <input type="text" name="home_phone" value="{{ old('home_phone', $student->home_phone) }}" dir="ltr"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 outline-none text-left">
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

            {{-- Section 3: Academic Information --}}
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                <h3 class="text-xl font-bold font-cairo text-gray-800 mb-6 flex items-center gap-3 border-b border-gray-100 pb-4">
                    <i class="fas fa-graduation-cap text-blue-500"></i> البيانات الأكاديمية والجامعية
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 font-almarai text-sm">
                    <div>
                        <label class="block text-gray-700 font-bold mb-2">الرقم الجامعي</label>
                        <input type="text" name="student_number" value="{{ old('student_number', $student->student_number) }}"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-gray-700 font-bold mb-2">الجامعة *</label>
                        <input type="text" name="university" value="{{ old('university', $student->university) }}" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-gray-700 font-bold mb-2">الكلية *</label>
                        <input type="text" name="college" value="{{ old('college', $student->college) }}" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-gray-700 font-bold mb-2">التخصص *</label>
                        <input type="text" name="major" value="{{ old('major', $student->major) }}" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-gray-700 font-bold mb-2">المستوى الدراسي الحالي *</label>
                        <select name="academic_level" required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none bg-white font-almarai">
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
                        <label class="block text-gray-700 font-bold mb-2">المهارات والهوايات</label>
                        <input type="text" name="skills" value="{{ old('skills', $student->skills) }}"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none" placeholder="مثلاً: برمجة، خط، رسم">
                    </div>
                </div>

                <div class="mt-8 border-t border-gray-50 pt-6">
                    <h4 class="text-md font-bold text-gray-600 mb-4 font-cairo">بيانات آخر مؤهل دراسي</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 font-almarai">
                        <div>
                            <label class="block text-gray-600 text-xs mb-1 font-bold">آخر مؤهل حاصل عليه</label>
                            <input type="text" name="last_certificate" value="{{ old('last_certificate', $student->last_certificate) }}"
                                class="w-full px-4 py-2 border border-gray-100 rounded-lg outline-none focus:ring-1 focus:ring-blue-300" placeholder="مثلاً: ثانوية عامة">
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

            {{-- Section 4: Emergency Contact --}}
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
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-300 outline-none text-left">
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex gap-4 sticky bottom-6 z-10">
                <button type="submit"
                    class="flex-1 md:flex-none px-12 py-4 bg-primary text-white rounded-2xl shadow-xl hover:bg-primary-dark font-bold font-cairo transition-all transform hover:-translate-y-1">
                    <i class="fas fa-save ml-2"></i> حفظ التحديثات
                </button>
                <a href="{{ route('dashboard') }}"
                    class="px-12 py-4 bg-white text-gray-500 border border-gray-100 rounded-2xl shadow-md hover:bg-gray-50 font-bold font-cairo transition-all">
                    إلغاء
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
