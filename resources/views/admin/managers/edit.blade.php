@extends ('layouts.app')

@section ('title', 'تعديل بيانات مدير مركز')

@section ('content')
    <div class="container mx-auto px-6 py-8">
        <div class="max-w-2xl mx-auto bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">
            <div class="bg-primary px-10 py-8 text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16"></div>
                <h2 class="text-2xl font-bold font-cairo relative z-10">تعديل بيانات مدير المركز</h2>
                <p class="text-white/80 font-almarai mt-2 relative z-10">تعديل معلومات الحساب أو تغيير المركز المعين.</p>
            </div>

            <form action="{{ route('managers.update', $manager) }}" method="POST" class="p-10 space-y-6">
                @csrf
                @method ('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-gray-700 font-cairo font-bold mb-2">الاسم الكامل</label>
                        <input type="text" name="name" value="{{ old('name', $manager->name) }}" required
                            class="w-full px-5 py-3 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-primary focus:border-transparent outline-none font-almarai transition-all">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-cairo font-bold mb-2">رقم الجوال</label>
                        <input type="text" name="phone" value="{{ old('phone', $manager->phone) }}" required
                            class="w-full px-5 py-3 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-primary focus:border-transparent outline-none font-almarai transition-all">
                    </div>
                </div>

                <div>
                    <label class="block text-gray-700 font-cairo font-bold mb-2">البريد الإلكتروني</label>
                    <input type="email" name="email" value="{{ old('email', $manager->email) }}" required
                        class="w-full px-5 py-3 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-primary focus:border-transparent outline-none font-almarai transition-all">
                </div>

                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label class="block text-gray-700 font-cairo font-bold mb-2">المركز المعين</label>
                        <select name="center_id" required
                            class="w-full px-5 py-3 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-primary focus:border-transparent outline-none font-almarai transition-all bg-white">
                            @foreach ($centers as $center)
                                <option value="{{ $center->id }}" {{ old('center_id', $manager->center_id) == $center->id ? 'selected' : '' }}>{{ $center->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-gray-700 font-cairo font-bold mb-2">كلمة المرور الجديدة (اختياري)</label>
                    <input type="password" name="password"
                        class="w-full px-5 py-3 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-primary focus:border-transparent outline-none font-almarai transition-all">
                    <p class="text-xs text-gray-400 mt-2 font-almarai">اتركه فارغاً إذا كنت لا ترغب في تغيير كلمة المرور.</p>
                </div>

                <div class="flex justify-end gap-4 pt-6">
                    <a href="{{ route('managers.index') }}"
                        class="px-8 py-3 border border-gray-200 text-gray-600 rounded-2xl hover:bg-gray-50 font-cairo font-bold transition-all">إلغاء</a>
                    <button type="submit"
                        class="px-10 py-3 bg-secondary text-white rounded-2xl hover:bg-orange-600 shadow-xl font-cairo font-bold transition-all transform hover:-translate-y-1">تحديث البيانات</button>
                </div>
            </form>
        </div>
    </div>
@endsection
