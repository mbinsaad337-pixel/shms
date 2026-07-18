@extends('layouts.app')

@section('title', 'تعديل بيانات الأصل')

@section('content')
    <div class="container mx-auto px-6 py-8">
        <div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
            <div class="bg-primary px-10 py-8 text-white relative">
                <h2 class="text-2xl font-bold font-cairo">تعديل بيانات الأصل</h2>
                <p class="text-white/80 font-almarai mt-2">تحديث حالة وبيانات العهدة المركزية</p>
            </div>

            <form action="{{ route('assets.update', $asset) }}" method="POST" enctype="multipart/form-data"
                class="p-10 space-y-6">
                @csrf
                @method('PUT')

                @php $errorList = isset($errors) ? $errors->all() : []; @endphp
                @if(count($errorList) > 0)
                    <div class="bg-red-50 text-red-600 p-4 rounded-xl border border-red-200 font-almarai text-sm">
                        <ul class="list-disc list-inside">
                            @foreach($errorList as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-gray-700 font-cairo font-bold mb-2">اسم الأصل</label>
                        <input type="text" name="name" value="{{ old('name', $asset->name) }}" required
                            placeholder="مثال: مكيف سبليت 24 وحدة"
                            class="w-full px-5 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent outline-none font-almarai transition-all">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-cairo font-bold mb-2">كود الأصل (Barcode/Tag)</label>
                        <input type="text" name="code" value="{{ old('code', $asset->code) }}" required
                            placeholder="CN-ACC-001"
                            class="w-full px-5 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent outline-none font-almarai transition-all">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-gray-700 font-cairo font-bold mb-2">نوع الأصل</label>
                        <input type="text" name="type" value="{{ old('type', $asset->type) }}" required
                            placeholder="أجهزة كهربائية"
                            class="w-full px-5 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent outline-none font-almarai transition-all">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-cairo font-bold mb-2">الفئة</label>
                        <select name="category" required
                            class="w-full px-5 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent outline-none font-almarai bg-white transition-all">
                            <option value="furniture" {{ old('category', $asset->category) == 'furniture' ? 'selected' : '' }}>أثاث ومفروشات</option>
                            <option value="electronics" {{ old('category', $asset->category) == 'electronics' ? 'selected' : '' }}>أجهزة إلكترونية</option>
                            <option value="kitchen" {{ old('category', $asset->category) == 'kitchen' ? 'selected' : '' }}>
                                أدوات مطبخ</option>
                            <option value="office" {{ old('category', $asset->category) == 'office' ? 'selected' : '' }}>معدات
                                مكتبية</option>
                            <option value="other" {{ old('category', $asset->category) == 'other' ? 'selected' : '' }}>أخرى
                            </option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-cairo font-bold mb-2">القيمة التقديرية</label>
                        <input type="number" name="value" value="{{ old('value', $asset->value) }}" step="0.01"
                            placeholder="0.00"
                            class="w-full px-5 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent outline-none font-almarai transition-all">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-gray-700 font-cairo font-bold mb-2">حالة الأصل</label>
                        <select name="status" required
                            class="w-full px-5 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent outline-none font-almarai bg-white transition-all text-sm">
                            <option value="good" {{ old('status', $asset->status) == 'good' ? 'selected' : '' }}>حالة ممتازة /
                                جديد</option>
                            <option value="needs_maintenance" {{ old('status', $asset->status) == 'needs_maintenance' ? 'selected' : '' }}>يحتاج صيانة</option>
                            <option value="damaged" {{ old('status', $asset->status) == 'damaged' ? 'selected' : '' }}>تالف /
                                غير قابل للاستخدام</option>
                            <option value="disposed" {{ old('status', $asset->status) == 'disposed' ? 'selected' : '' }}>تم
                                التخلص منه</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-cairo font-bold mb-2">صورة الأصل (اختر لتحديث الصورة)</label>
                        <input type="file" name="photo" accept="image/*"
                            class="w-full px-5 py-2 border border-dashed border-gray-300 rounded-xl hover:border-primary transition-all">
                        @if($asset->photo)
                            <div class="mt-2 text-sm text-gray-500 font-almarai">
                                صورة حالية متوفرة.
                            </div>
                        @endif
                    </div>
                </div>

                <div>
                    <label class="block text-gray-700 font-cairo font-bold mb-2">ملاحظات إضافية</label>
                    <textarea name="notes" rows="3" placeholder="وصف حالة الأصل أو الصيانة المطلوبة..."
                        class="w-full px-5 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent outline-none font-almarai transition-all">{{ old('notes', $asset->notes) }}</textarea>
                </div>

                <div class="flex justify-end gap-4 pt-6">
                    <a href="{{ route('assets.index') }}"
                        class="px-8 py-3 border border-gray-200 text-gray-600 rounded-xl hover:bg-gray-50 font-cairo font-bold transition-all text-sm">إلغاء</a>
                    <button type="submit"
                        class="px-10 py-3 bg-secondary text-white rounded-xl hover:bg-orange-600 shadow-lg font-cairo font-bold transition-all transform hover:-translate-y-1 text-sm">حفظ
                        التعديلات</button>
                </div>
            </form>
        </div>
    </div>
@endsection
