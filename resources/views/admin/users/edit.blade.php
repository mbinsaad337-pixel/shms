@extends('layouts.app')

@section('title', 'تعديل موظف')

@section('content')
@php
    /** @var \Illuminate\Support\ViewErrorBag $errors */
@endphp
<div class="container mx-auto px-6 py-8">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-3xl font-bold text-primary font-cairo">تعديل بيانات الموظف</h2>
            <p class="text-gray-500 font-almarai mt-1">تحديث معلومات الموظف: {{ $user->name }}</p>
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

    <form action="{{ route('admin.users.update', $user) }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
            <!-- Name -->
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2 font-cairo">الاسم الكامل</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all font-almarai">
            </div>

            <!-- Email -->
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2 font-cairo">البريد الإلكتروني</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all font-almarai text-left">
            </div>

            <!-- Phone -->
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2 font-cairo">رقم الجوال</label>
                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" required
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all font-almarai text-left">
            </div>

            <!-- Bank Account Number -->
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2 font-cairo">رقم الحساب البنكي</label>
                <input type="text" name="bank_account_number" value="{{ old('bank_account_number', $user->bank_account_number) }}"
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all font-almarai text-left" dir="ltr">
            </div>

            <!-- Salary -->
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2 font-cairo">الراتب</label>
                <input type="number" step="0.01" name="salary" value="{{ old('salary', $user->salary) }}"
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all font-almarai text-left" dir="ltr">
            </div>

            <!-- Role -->
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2 font-cairo">الدور الوظيفي</label>
                <select name="role" required
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all font-almarai">
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                            {{ \App\Helpers\PermissionTranslationHelper::translateRole($role->name) }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mb-8 p-6 bg-orange-50 rounded-2xl border border-orange-100">
            <h3 class="text-md font-bold text-orange-800 mb-4 font-cairo">تغيير كلمة المرور</h3>
            <p class="text-xs text-orange-600 font-almarai mb-4">اترك الحقول التالية فارغة إذا كنت لا ترغب في تغيير كلمة المرور.</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-2 font-cairo">كلمة المرور الجديدة</label>
                    <input type="password" name="password"
                        class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-primary outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-2 font-cairo">تأكيد كلمة المرور</label>
                    <input type="password" name="password_confirmation"
                        class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-primary outline-none">
                </div>
            </div>
        </div>

        <!-- Permissions Section -->
        <div class="mb-8">
            <h3 class="text-lg font-bold text-gray-800 mb-4 font-cairo border-b pb-2">الصلاحيات المباشرة</h3>
            <p class="text-sm text-gray-500 font-almarai mb-6">يمكنك منح صلاحيات إضافية خارج نطاق الدور الوظيفي المختار.</p>
            
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($permissions as $group => $groupPermissions)
                    @php
                        $translatedGroup = \App\Helpers\PermissionTranslationHelper::translateGroup($group);
                    @endphp
                    <div class="space-y-3">
                        <h4 class="font-bold text-primary font-cairo text-sm uppercase bg-blue-50 px-2 py-1 rounded">قسم: {{ $translatedGroup }}</h4>
                        @foreach($groupPermissions as $permission)
                            @php
                                $translatedPermission = \App\Helpers\PermissionTranslationHelper::translatePermission($permission->name);
                            @endphp
                            <label class="flex items-center space-x-3 space-x-reverse cursor-pointer group">
                                <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                    {{ $user->hasDirectPermission($permission->name) ? 'checked' : '' }}
                                    class="w-5 h-5 rounded border-gray-300 text-primary focus:ring-primary">
                                <span class="text-sm text-gray-600 group-hover:text-primary transition-colors font-almarai">{{ $translatedPermission }}</span>
                            </label>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex justify-end pt-6 border-t font-cairo gap-4">
            <a href="{{ route('admin.users.index') }}" class="px-8 py-3 rounded-xl font-bold text-gray-500 hover:bg-gray-50 transition-all">إلغاء</a>
            <button type="submit"
                class="bg-secondary hover:bg-orange-600 text-white px-8 py-3 rounded-xl font-bold transition-all shadow-lg shadow-orange-200">
                حفظ التغييرات
            </button>
        </div>
    </form>
</div>
@endsection
