@extends('layouts.app')

@section('title', 'إضافة موظف جديد')

@section('content')
    @php
        /** @var \Illuminate\Support\ViewErrorBag $errors */
    @endphp
    <div class="container mx-auto px-6 py-8">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-bold text-primary font-cairo">إضافة موظف جديد</h2>
                <p class="text-gray-500 font-almarai mt-1">إنشاء حساب لموظف جديد وتحديد صلاحياته في المركز.</p>
            </div>
            <a href="{{ route('admin.users.index') }}" class="text-primary hover:underline font-cairo">العودة للقائمة</a>
        </div>

        @php $errorList = isset($errors) ? $errors->all() : []; @endphp
        @if(count($errorList) > 0)
            <div class="mb-6 bg-red-50 border-r-4 border-red-500 p-4 rounded-lg">
                <ul class="list-disc list-inside text-red-700 font-almarai">
                    @foreach($errorList as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.users.store') }}" method="POST"
            class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <!-- Name -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2 font-cairo">الاسم الكامل</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all font-almarai">
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2 font-cairo">البريد الإلكتروني</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all font-almarai text-left">
                </div>

                <!-- Phone -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2 font-cairo">رقم الجوال</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" required
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all font-almarai text-left">
                </div>

                <!-- Bank Account Number -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2 font-cairo">رقم الحساب البنكي</label>
                    <input type="text" name="bank_account_number" value="{{ old('bank_account_number') }}"
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all font-almarai text-left" dir="ltr">
                </div>

                <!-- Salary -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2 font-cairo">الراتب</label>
                    <input type="number" step="0.01" name="salary" value="{{ old('salary') }}"
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all font-almarai text-left" dir="ltr">
                </div>

                <!-- Role -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2 font-cairo">الدور الوظيفي</label>
                    <select name="role" required
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all font-almarai">
                        <option value="">اختر الدور</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
                                {{ \App\Helpers\PermissionTranslationHelper::translateRole($role->name) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2 font-cairo">كلمة المرور</label>
                    <input type="password" name="password" required
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all font-almarai">
                </div>

                <!-- Password Confirmation -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2 font-cairo">تأكيد كلمة المرور</label>
                    <input type="password" name="password_confirmation" required
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all font-almarai">
                </div>
            </div>

            <!-- Permissions Section -->
            {{-- <div class="mb-8">
                <h3 class="text-lg font-bold text-gray-800 mb-4 font-cairo border-b pb-2">الصلاحيات المباشرة (اختياري)</h3>
                <p class="text-sm text-gray-500 font-almarai mb-6">سيحصل الموظف تلقائياً على صلاحيات الدور المختار، يمكنك
                    إضافة صلاحيات إضافية هنا.</p>

                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach($permissions as $group => $groupPermissions)
                        @php
                            $translatedGroup = \App\Helpers\PermissionTranslationHelper::translateGroup($group);
                        @endphp
                        <div class="space-y-3">
                            <h4 class="font-bold text-primary font-cairo text-sm uppercase bg-blue-50 px-2 py-1 rounded">قسم:
                                {{ $translatedGroup }}
                            </h4>
                            @foreach($groupPermissions as $permission)
                                @php
                                    $translatedPermission = \App\Helpers\PermissionTranslationHelper::translatePermission($permission->name);
                                @endphp
                                <label class="flex items-center space-x-3 space-x-reverse cursor-pointer group">
                                    <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                        class="w-5 h-5 rounded border-gray-300 text-primary focus:ring-primary">
                                    <span
                                        class="text-sm text-gray-600 group-hover:text-primary transition-colors font-almarai">{{ $translatedPermission }}</span>
                                </label>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div> --}}

            <div class="flex justify-end pt-6 border-t font-cairo">
                <button type="submit"
                    class="bg-primary hover:bg-blue-700 text-white px-8 py-3 rounded-xl font-bold transition-all shadow-lg shadow-blue-200">
                    حفظ بيانات الموظف
                </button>
            </div>
        </form>
    </div>
@endsection
