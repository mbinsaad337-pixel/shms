@extends('layouts.app')

@section('title', 'تسجيل طالب جديد')

@section('content')
    <div class="container mx-auto px-6 py-8">
        <div class="max-w-2xl mx-auto bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
            <div class="bg-primary px-10 py-8 text-white relative text-center">
                <i class="fas fa-user-plus text-4xl mb-3"></i>
                <h2 class="text-2xl font-bold font-cairo">تسجيل سريع لطالب جديد</h2>
                <p class="text-white/80 font-almarai mt-2">أنشئ حساب الدخول للطالب، وسيقوم الطالب بإكمال بياناته لاحقاً</p>
            </div>

            <form action="{{ route('students.store') }}" method="POST" class="p-10 space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-gray-700 font-cairo font-bold mb-2">اسم الطالب (عربي) <span class="text-red-500">*</span></label>
                        <input type="text" name="name_ar" value="{{ old('name_ar') }}" required
                            class="w-full px-5 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent outline-none font-almarai transition-all"
                            placeholder="الاسم الثلاثي">
                        @error('name_ar') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-gray-700 font-cairo font-bold mb-2">نظام التسكين التابع له <span class="text-red-500">*</span></label>
                        <select name="program_id" required class="w-full px-5 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary outline-none font-almarai bg-white transition-all">
                            <option value="">اختر نظام التسكين...</option>
                            @foreach($programs ?? [] as $program)
                                <option value="{{ $program->id }}" {{ old('program_id') == $program->id ? 'selected' : '' }}>
                                    {{ $program->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('program_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-gray-700 font-cairo font-bold mb-2 text-sm">اسم المستخدم للدخول <span class="text-red-500">*</span></label>
                        <input type="text" name="username" value="{{ old('username') }}" required
                            class="w-full px-5 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent outline-none font-almarai transition-all"
                            placeholder="s442000">
                        @error('username') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-gray-700 font-cairo font-bold mb-2 text-sm">كلمة المرور المؤقتة <span class="text-red-500">*</span></label>
                        <input type="text" name="temp_password" value="{{ old('temp_password') }}" required
                            class="w-full px-5 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent outline-none font-almarai transition-all"
                            placeholder="welcom123">
                        @error('temp_password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-gray-700 font-cairo font-bold mb-2 text-sm">رقم الهاتف <span class="text-red-500">*</span></label>
                        <input type="tel" name="phone" value="{{ old('phone') }}" required
                            class="w-full px-5 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent outline-none font-almarai transition-all"
                            placeholder="77XXXXXXX" dir="ltr">
                        @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- قسم اختياري - يمكن ملؤها لاحقاً من قبل الطالب --}}
                <div class="pt-4 border-t border-dashed border-gray-200">
                    <p class="text-xs text-gray-400 font-almarai mb-4 flex items-center gap-2">
                        <i class="fas fa-info-circle text-blue-400"></i>
                        الحقول التالية اختيارية — سيقوم الطالب بإكمالها عند تسجيل الدخول لأول مرة
                    </p>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div>
                            <label class="block text-gray-500 font-cairo font-bold mb-2 text-sm">البريد الإلكتروني</label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                class="w-full px-5 py-3 border border-gray-100 bg-gray-50/50 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent outline-none font-almarai transition-all"
                                placeholder="اختياري">
                            @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-gray-500 font-cairo font-bold mb-2 text-sm">رقم الهوية</label>
                            <input type="text" name="national_id" value="{{ old('national_id') }}"
                                class="w-full px-5 py-3 border border-gray-100 bg-gray-50/50 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent outline-none font-almarai transition-all"
                                placeholder="اختياري">
                            @error('national_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-gray-500 font-cairo font-bold mb-2 text-sm">الرقم الجامعي</label>
                            <input type="text" name="student_number" value="{{ old('student_number') }}"
                                class="w-full px-5 py-3 border border-gray-100 bg-gray-50/50 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent outline-none font-almarai transition-all"
                                placeholder="اختياري">
                            @error('student_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-gray-500 font-cairo font-bold mb-2 text-sm">الرسوم السنوية (ر.ي)</label>
                            <input type="number" name="annual_fees" value="{{ old('annual_fees', 0) }}" step="0.01"
                                class="w-full px-5 py-3 border border-gray-100 bg-gray-50/50 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none font-bold text-emerald-700 font-almarai transition-all"
                                placeholder="0.00">
                            @error('annual_fees') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-4 pt-8">
                    <a href="{{ route('students.index') }}"
                        class="px-8 py-3 border border-gray-200 text-gray-600 rounded-xl hover:bg-gray-50 font-cairo font-bold transition-all text-sm">إلغاء</a>
                    <button type="submit"
                        class="px-10 py-3 bg-secondary text-white rounded-xl hover:bg-orange-600 shadow-lg font-cairo font-bold transition-all transform hover:-translate-y-1 text-sm">إنشاء
                        الحساب</button>
                </div>
            </form>
        </div>
    </div>
@endsection
