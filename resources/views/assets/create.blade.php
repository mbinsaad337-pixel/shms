@extends('layouts.app')

@section('title', 'تسجيل أصل جديد')

@section('content')
    <div class="container mx-auto px-6 py-8">
        <div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
            <div class="bg-primary px-10 py-8 text-white relative">
                <h2 class="text-2xl font-bold font-cairo">تسجيل أصل جديد في العهدة</h2>
                <p class="text-white/80 font-almarai mt-2">إضافة عهدة عينية جديدة إلى عهدة المركز</p>
            </div>

            <form action="{{ route('assets.store') }}" method="POST" enctype="multipart/form-data" class="p-10 space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-gray-700 font-cairo font-bold mb-2">اسم الأصل</label>
                        <input type="text" name="name" required placeholder="مثال: مكيف سبليت 24 وحدة"
                            class="w-full px-5 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent outline-none font-almarai transition-all">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-cairo font-bold mb-2">كود الأصل (Barcode/Tag)</label>
                        <input type="text" name="code" required placeholder="CN-ACC-001"
                            class="w-full px-5 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent outline-none font-almarai transition-all">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-gray-700 font-cairo font-bold mb-2">نوع الأصل</label>
                        <input type="text" name="type" required placeholder="أجهزة كهربائية"
                            class="w-full px-5 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent outline-none font-almarai transition-all">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-cairo font-bold mb-2">الفئة</label>
                        <select name="category" required
                            class="w-full px-5 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent outline-none font-almarai bg-white transition-all">
                            <option value="furniture">أثاث ومفروشات</option>
                            <option value="electronics">أجهزة إلكترونية</option>
                            <option value="kitchen">أدوات مطبخ</option>
                            <option value="office">معدات مكتبية</option>
                            <option value="other">أخرى</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-cairo font-bold mb-2">القيمة التقديرية</label>
                        <input type="number" name="value" step="0.01" placeholder="0.00"
                            class="w-full px-5 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent outline-none font-almarai transition-all">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-gray-700 font-cairo font-bold mb-2">حالة الأصل</label>
                        <select name="status" required
                            class="w-full px-5 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent outline-none font-almarai bg-white transition-all text-sm">
                            <option value="good">حالة ممتازة / جديد</option>
                            <option value="needs_maintenance">يحتاج صيانة</option>
                            <option value="damaged">تالف / غير قابل للاستخدام</option>
                            <option value="disposed">تم التخلص منه</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-cairo font-bold mb-2">صورة الأصل</label>
                        <input type="file" name="photo" accept="image/*"
                            class="w-full px-5 py-2 border border-dashed border-gray-300 rounded-xl hover:border-primary transition-all">
                    </div>
                </div>

                <div>
                    <label class="block text-gray-700 font-cairo font-bold mb-2">ملاحظات إضافية</label>
                    <textarea name="notes" rows="3" placeholder="وصف حالة الأصل أو الصيانة المطلوبة..."
                        class="w-full px-5 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent outline-none font-almarai transition-all"></textarea>
                </div>

                <div class="flex justify-end gap-4 pt-6">
                    <a href="{{ route('assets.index') }}"
                        class="px-8 py-3 border border-gray-200 text-gray-600 rounded-xl hover:bg-gray-50 font-cairo font-bold transition-all text-sm">إلغاء</a>
                    <button type="submit"
                        class="px-10 py-3 bg-secondary text-white rounded-xl hover:bg-orange-600 shadow-lg font-cairo font-bold transition-all transform hover:-translate-y-1 text-sm">إكمال
                        التسجيل</button>
                </div>
            </form>
        </div>
    </div>
@endsection
