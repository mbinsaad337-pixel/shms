@extends('layouts.app')

@section('title', 'إضافة مرفق جديد')

@section('content')
    <div class="container mx-auto px-6 py-8">
        <div class="max-w-2xl mx-auto bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
            <div class="bg-primary px-10 py-8 text-white relative">
                <h2 class="text-2xl font-bold font-cairo">إضافة مرفق جديد</h2>
                <p class="text-white/80 font-almarai mt-2">إضافة غرفة سكنية أو قاعة دراسية/أنشطة</p>
            </div>

            <form action="{{ route('rooms.store') }}" method="POST" class="p-10 space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-gray-700 font-cairo font-bold mb-2">نوع المرفق</label>
                        <select name="type" required
                            class="w-full px-5 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent outline-none font-almarai bg-white transition-all">
                            <option value="residential">غرفة سكنية</option>
                            <option value="study_hall">قاعة دراسية (مذاكرة)</option>
                            <option value="activity_hall">قاعة أنشطة / تدريب</option>
                            <option value="other">أخرى</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-cairo font-bold mb-2">رقم الغرفة/المرفق</label>
                        <input type="text" name="room_number" required placeholder="مثال: 101"
                            class="w-full px-5 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent outline-none font-almarai transition-all">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-cairo font-bold mb-2">الشقة (اختياري)</label>
                        <input type="text" name="apartment" placeholder="مثال: أ أو 1"
                            class="w-full px-5 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent outline-none font-almarai transition-all">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-gray-700 font-cairo font-bold mb-2">المبنى</label>
                        <input type="text" name="building" required placeholder="أ"
                            class="w-full px-5 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent outline-none font-almarai transition-all">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-cairo font-bold mb-2">الطابق</label>
                        <input type="text" name="floor" required placeholder="الأول"
                            class="w-full px-5 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent outline-none font-almarai transition-all">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-cairo font-bold mb-2">السعة الاستيعابية</label>
                        <input type="number" name="capacity" required min="1" placeholder="4"
                            class="w-full px-5 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent outline-none font-almarai transition-all">
                    </div>
                </div>

                <div class="flex justify-end gap-4 pt-6">
                    <a href="{{ route('rooms.index') }}"
                        class="px-8 py-3 border border-gray-200 text-gray-600 rounded-xl hover:bg-gray-50 font-cairo font-bold transition-all text-sm">إلغاء</a>
                    <button type="submit"
                        class="px-10 py-3 bg-secondary text-white rounded-xl hover:bg-orange-600 shadow-lg font-cairo font-bold transition-all transform hover:-translate-y-1 text-sm">حفظ
                        المرفق</button>
                </div>
            </form>
        </div>
    </div>
@endsection
