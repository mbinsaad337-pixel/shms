@extends('layouts.app')

@section('title', 'إضافة مركز جديد')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('centers.index') }}" class="text-primary hover:underline text-sm flex items-center">
                <svg class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                </svg>
                العودة للقائمة
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            <h2 class="text-xl font-bold text-gray-800 mb-8">بيانات المركز الجديد</h2>

            <form action="{{ route('centers.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">اسم المركز *</label>
                        <input type="text" name="name" required
                            class="block w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-secondary focus:border-secondary">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">البريد الإلكتروني</label>
                        <input type="email" name="email"
                            class="block w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-secondary focus:border-secondary">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">رقم الهاتف</label>
                        <input type="text" name="phone"
                            class="block w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-secondary focus:border-secondary">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">شعار المركز</label>
                        <input type="file" name="logo"
                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">العنوان بالتفصيل *</label>
                    <textarea name="address" rows="3" required
                        class="block w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-secondary focus:border-secondary"></textarea>
                </div>

                <div class="mt-4">
                    <label class="inline-flex items-center cursor-pointer group">
                        <input type="hidden" name="has_rent" value="0">
                        <input type="checkbox" name="has_rent" value="1" {{ old('has_rent', true) ? 'checked' : '' }}
                               class="w-5 h-5 text-gold rounded border-gray-300 focus:ring-gold accent-gold cursor-pointer">
                        <span class="ml-2 mr-2 text-sm font-bold text-gray-700 font-almarai group-hover:text-gold transition-colors">مبنى مستأجر (يستلزم دفع إيجار)</span>
                    </label>
                </div>

                <div class="pt-4 border-t border-gray-100 flex justify-end">
                    <button type="submit" class="btn-secondary px-10 py-3 font-bold">
                        حفظ البيانات
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
