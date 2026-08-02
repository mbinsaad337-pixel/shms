@extends(auth()->user()->hasRole('nutrition-manager') ? 'layouts.nutrition' : 'layouts.app')
@section('title', 'تعديل مورد')

@section('content')
    <div class="p-6 max-w-2xl mx-auto">
        <div class="flex items-center gap-4 mb-6">
            <a href="{{ route('nutrition.suppliers.index') }}" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-arrow-right text-xl"></i>
            </a>
            <h2 class="text-2xl font-bold text-gray-800 font-cairo">تعديل بيانات المورد</h2>
        </div>

        <form action="{{ route('nutrition.suppliers.update', $supplier) }}" method="POST">
            @csrf @method('PUT')
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-gray-600 font-cairo mb-1.5">اسم المورد <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="name" required value="{{ old('name', $supplier->name) }}"
                            class="w-full border border-gray-200 rounded-xl px-4 py-3 font-cairo focus:ring-2 focus:ring-orange-400">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-600 font-cairo mb-1.5">رقم التواصل</label>
                        <input type="text" name="phone" value="{{ old('phone', $supplier->phone) }}" dir="ltr"
                            class="w-full border border-gray-200 rounded-xl px-4 py-3   focus:ring-2 focus:ring-orange-400">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-600 font-cairo mb-1.5">البريد الإلكتروني</label>
                        <input type="email" name="email" value="{{ old('email', $supplier->email) }}"
                            class="w-full border border-gray-200 rounded-xl px-4 py-3   focus:ring-2 focus:ring-orange-400">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-gray-600 font-cairo mb-1.5">العنوان</label>
                        <input type="text" name="address" value="{{ old('address', $supplier->address) }}"
                            class="w-full border border-gray-200 rounded-xl px-4 py-3 font-cairo focus:ring-2 focus:ring-orange-400">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-600 font-cairo mb-1.5">الرقم الضريبي</label>
                        <input type="text" name="tax_number" value="{{ old('tax_number', $supplier->tax_number) }}"
                            class="w-full border border-gray-200 rounded-xl px-4 py-3   focus:ring-2 focus:ring-orange-400">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-gray-600 font-cairo mb-1.5">ملاحظات</label>
                        <textarea name="notes" rows="3"
                            class="w-full border border-gray-200 rounded-xl px-4 py-3 font-almarai text-sm focus:ring-2 focus:ring-orange-400">{{ old('notes', $supplier->notes) }}</textarea>
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <a href="{{ route('nutrition.suppliers.index') }}"
                        class="px-6 py-3 border border-gray-200 rounded-xl font-cairo font-bold text-gray-600">إلغاء</a>
                    <button type="submit"
                        class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold font-cairo shadow-lg shadow-blue-200">
                        <i class="fas fa-save ml-2"></i> حفظ التعديلات
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection
