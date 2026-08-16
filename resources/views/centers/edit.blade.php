@extends('layouts.app')

@section('title', 'تعديل المركز - ' . $center->name)

@section('content')
    <div class="mx-auto max-w-5xl">
        <header class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <a href="{{ route('centers.show', $center) }}" class="inline-flex items-center gap-2 text-sm font-bold text-navy transition-colors hover:text-gold focus:outline-none focus:ring-4 focus:ring-gold/20 rounded-lg font-cairo">
                    <i class="fas fa-arrow-right" aria-hidden="true"></i>
                    العودة إلى تفاصيل المركز
                </a>
                <h1 class="mt-3 text-2xl font-black text-navy font-cairo sm:text-3xl">تعديل بيانات المركز</h1>
                <p class="mt-1 text-sm text-gray-500 font-almarai">حدّث المعلومات الأساسية وحالة تشغيل المركز.</p>
            </div>

            <div class="inline-flex items-center gap-2 self-start rounded-xl border border-gold/25 bg-gold/10 px-4 py-2 text-sm font-bold text-navy font-cairo">
                <i class="fas fa-building-columns text-gold" aria-hidden="true"></i>
                {{ $center->name }}
            </div>
        </header>

        <form action="{{ route('centers.update', $center) }}" method="POST" enctype="multipart/form-data" class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
            @csrf
            @method('PUT')

            <div class="border-b border-gray-100 bg-navy px-6 py-5 sm:px-8">
                <h2 class="flex items-center gap-3 text-lg font-bold text-white font-cairo">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-gold/20 text-gold">
                        <i class="fas fa-pen-to-square" aria-hidden="true"></i>
                    </span>
                    بيانات المركز
                </h2>
            </div>

            <div class="space-y-8 p-6 sm:p-8">
                <section aria-labelledby="basic-information-title">
                    <div class="mb-5 flex items-center gap-3">
                        <span class="h-8 w-1 rounded-full bg-gold"></span>
                        <div>
                            <h3 id="basic-information-title" class="font-bold text-navy font-cairo">المعلومات الأساسية</h3>
                            <p class="mt-0.5 text-xs text-gray-500 font-almarai">الحقول المشار إليها بـ * مطلوبة.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <div>
                            <label for="name" class="mb-2 block text-sm font-bold text-gray-700 font-cairo">اسم المركز <span class="text-red-600">*</span></label>
                            <input id="name" type="text" name="name" value="{{ old('name', $center->name) }}" required autocomplete="organization"
                                class="block w-full rounded-xl border px-4 py-3 text-gray-800 outline-none transition focus:border-gold focus:ring-4 focus:ring-gold/15 @error('name') border-red-400 @else border-gray-300 @enderror">
                            @error('name')
                                <p class="mt-2 flex items-center gap-1 text-xs font-bold text-red-600 font-cairo"><i class="fas fa-circle-exclamation" aria-hidden="true"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="mb-2 block text-sm font-bold text-gray-700 font-cairo">البريد الإلكتروني</label>
                            <input id="email" type="email" name="email" value="{{ old('email', $center->email) }}" autocomplete="email" dir="ltr"
                                class="block w-full rounded-xl border px-4 py-3 text-right text-gray-800 outline-none transition focus:border-gold focus:ring-4 focus:ring-gold/15 @error('email') border-red-400 @else border-gray-300 @enderror">
                            @error('email')
                                <p class="mt-2 flex items-center gap-1 text-xs font-bold text-red-600 font-cairo" dir="rtl"><i class="fas fa-circle-exclamation" aria-hidden="true"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="phone" class="mb-2 block text-sm font-bold text-gray-700 font-cairo">رقم الهاتف</label>
                            <input id="phone" type="text" name="phone" value="{{ old('phone', $center->phone) }}" autocomplete="tel" inputmode="tel" dir="ltr"
                                class="block w-full rounded-xl border px-4 py-3 text-right text-gray-800 outline-none transition focus:border-gold focus:ring-4 focus:ring-gold/15 @error('phone') border-red-400 @else border-gray-300 @enderror">
                            @error('phone')
                                <p class="mt-2 flex items-center gap-1 text-xs font-bold text-red-600 font-cairo" dir="rtl"><i class="fas fa-circle-exclamation" aria-hidden="true"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="is_active" class="mb-2 block text-sm font-bold text-gray-700 font-cairo">حالة النشاط <span class="text-red-600">*</span></label>
                            <select id="is_active" name="is_active" required class="block w-full rounded-xl border px-4 py-3 text-gray-800 outline-none transition focus:border-gold focus:ring-4 focus:ring-gold/15 @error('is_active') border-red-400 @else border-gray-300 @enderror">
                                <option value="1" @selected((string) old('is_active', (int) $center->is_active) === '1')>نشط ومفعّل</option>
                                <option value="0" @selected((string) old('is_active', (int) $center->is_active) === '0')>غير نشط</option>
                            </select>
                            @error('is_active')
                                <p class="mt-2 flex items-center gap-1 text-xs font-bold text-red-600 font-cairo"><i class="fas fa-circle-exclamation" aria-hidden="true"></i>{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-5">
                        <label for="address" class="mb-2 block text-sm font-bold text-gray-700 font-cairo">العنوان بالتفصيل <span class="text-red-600">*</span></label>
                        <textarea id="address" name="address" rows="3" required autocomplete="street-address"
                            class="block w-full resize-y rounded-xl border px-4 py-3 text-gray-800 outline-none transition focus:border-gold focus:ring-4 focus:ring-gold/15 @error('address') border-red-400 @else border-gray-300 @enderror">{{ old('address', $center->address) }}</textarea>
                        @error('address')
                            <p class="mt-2 flex items-center gap-1 text-xs font-bold text-red-600 font-cairo"><i class="fas fa-circle-exclamation" aria-hidden="true"></i>{{ $message }}</p>
                        @enderror
                    </div>
                </section>

                <section class="border-t border-gray-100 pt-8" aria-labelledby="logo-title">
                    <div class="mb-5 flex items-center gap-3">
                        <span class="h-8 w-1 rounded-full bg-gold"></span>
                        <div>
                            <h3 id="logo-title" class="font-bold text-navy font-cairo">شعار المركز</h3>
                            <p class="mt-0.5 text-xs text-gray-500 font-almarai">يمكنك استبدال الشعار الحالي بصورة جديدة.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 items-center gap-5 rounded-2xl border border-dashed border-gray-300 bg-gray-50 p-5 sm:grid-cols-[auto_1fr]">
                        <div class="flex h-24 w-24 shrink-0 items-center justify-center overflow-hidden rounded-2xl border-2 border-white bg-white shadow-sm">
                            @if ($center->logo)
                                <img src="{{ asset('storage/' . $center->logo) }}" alt="الشعار الحالي لـ {{ $center->name }}" class="h-full w-full object-cover">
                            @else
                                <i class="fas fa-building text-3xl text-navy/30" aria-hidden="true"></i>
                            @endif
                        </div>
                        <div>
                            <label for="logo" class="mb-2 block text-sm font-bold text-gray-700 font-cairo">رفع شعار جديد <span class="font-normal text-gray-400">(اختياري)</span></label>
                            <input id="logo" type="file" name="logo" accept="image/*"
                                class="block w-full cursor-pointer rounded-lg border border-gray-300 bg-white text-sm text-gray-600 file:ml-4 file:cursor-pointer file:rounded-lg file:border-0 file:bg-navy file:px-4 file:py-2.5 file:text-sm file:font-bold file:text-white hover:file:bg-navy/90 focus:outline-none focus:ring-4 focus:ring-gold/15">
                            <p class="mt-2 text-xs leading-5 text-gray-500 font-almarai">إذا لم ترفع ملفًا جديدًا، سيبقى الشعار الحالي دون تغيير.</p>
                            @error('logo')
                                <p class="mt-2 flex items-center gap-1 text-xs font-bold text-red-600 font-cairo"><i class="fas fa-circle-exclamation" aria-hidden="true"></i>{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </section>

                <section class="border-t border-gray-100 pt-8" aria-labelledby="settings-title">
                    <div class="mb-5 flex items-center gap-3">
                        <span class="h-8 w-1 rounded-full bg-gold"></span>
                        <div>
                            <h3 id="settings-title" class="font-bold text-navy font-cairo">الإعدادات المالية</h3>
                            <p class="mt-0.5 text-xs text-gray-500 font-almarai">حدد ما إذا كان المركز يحتاج إلى متابعة مدفوعات الإيجار.</p>
                        </div>
                    </div>

                    <label for="has_rent" class="flex cursor-pointer items-start gap-4 rounded-2xl border border-gray-200 bg-gray-50 p-5 transition-colors hover:border-gold/50 hover:bg-gold/5">
                        <input type="hidden" name="has_rent" value="0">
                        <input id="has_rent" type="checkbox" name="has_rent" value="1" @checked(old('has_rent', $center->has_rent) == 1)
                            class="mt-0.5 h-5 w-5 rounded border-gray-300 text-gold focus:ring-gold">
                        <span>
                            <span class="block font-bold text-gray-800 font-cairo">مبنى مستأجر</span>
                            <span class="mt-1 block text-xs leading-5 text-gray-500 font-almarai">فعّل هذا الخيار إذا كان المركز يستلزم دفع إيجار دوري.</span>
                        </span>
                    </label>
                    @error('has_rent')
                        <p class="mt-2 flex items-center gap-1 text-xs font-bold text-red-600 font-cairo"><i class="fas fa-circle-exclamation" aria-hidden="true"></i>{{ $message }}</p>
                    @enderror
                </section>

                <section class="border-t border-gray-100 pt-8" aria-labelledby="details-title">
                    <div class="mb-5 flex items-center gap-3">
                        <span class="h-8 w-1 rounded-full bg-gold"></span>
                        <div>
                            <h3 id="details-title" class="font-bold text-navy font-cairo">تفاصيل المركز</h3>
                            <p class="mt-0.5 text-xs text-gray-500 font-almarai">هذه التفاصيل اختيارية وتظهر في صفحة الترحيب.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <div>
                            <label for="message" class="mb-2 block text-sm font-bold text-gray-700 font-cairo">رسالة المركز</label>
                            <textarea id="message" name="message" rows="3" class="block w-full resize-y rounded-xl border px-4 py-3 text-gray-800 outline-none transition focus:border-gold focus:ring-4 focus:ring-gold/15 border-gray-300">{{ old('message', $center->message) }}</textarea>
                        </div>
                        <div>
                            <label for="vision" class="mb-2 block text-sm font-bold text-gray-700 font-cairo">رؤية المركز</label>
                            <textarea id="vision" name="vision" rows="3" class="block w-full resize-y rounded-xl border px-4 py-3 text-gray-800 outline-none transition focus:border-gold focus:ring-4 focus:ring-gold/15 border-gray-300">{{ old('vision', $center->vision) }}</textarea>
                        </div>
                        <div>
                            <label for="values" class="mb-2 block text-sm font-bold text-gray-700 font-cairo">قيم المركز</label>
                            <textarea id="values" name="values" rows="3" class="block w-full resize-y rounded-xl border px-4 py-3 text-gray-800 outline-none transition focus:border-gold focus:ring-4 focus:ring-gold/15 border-gray-300">{{ old('values', $center->values) }}</textarea>
                        </div>
                        <div>
                            <label for="goals" class="mb-2 block text-sm font-bold text-gray-700 font-cairo">أهداف المركز</label>
                            <textarea id="goals" name="goals" rows="3" class="block w-full resize-y rounded-xl border px-4 py-3 text-gray-800 outline-none transition focus:border-gold focus:ring-4 focus:ring-gold/15 border-gray-300">{{ old('goals', $center->goals) }}</textarea>
                        </div>
                    </div>
                </section>

                <section class="border-t border-gray-100 pt-8" aria-labelledby="links-title">
                    <div class="mb-5 flex items-center gap-3">
                        <span class="h-8 w-1 rounded-full bg-gold"></span>
                        <div>
                            <h3 id="links-title" class="font-bold text-navy font-cairo">روابط التواصل والموقع</h3>
                            <p class="mt-0.5 text-xs text-gray-500 font-almarai">أضف روابط وسائل التواصل والموقع على الخريطة (اختياري).</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <div>
                            <label for="whatsapp_link" class="mb-2 block text-sm font-bold text-gray-700 font-cairo">رقم الواتساب / الرابط</label>
                            <input id="whatsapp_link" type="text" name="whatsapp_link" value="{{ old('whatsapp_link', $center->whatsapp_link) }}" dir="ltr" class="block w-full rounded-xl border px-4 py-3 text-right text-gray-800 outline-none transition focus:border-gold focus:ring-4 focus:ring-gold/15 border-gray-300">
                        </div>
                        <div>
                            <label for="instagram_link" class="mb-2 block text-sm font-bold text-gray-700 font-cairo">رابط انستقرام</label>
                            <input id="instagram_link" type="url" name="instagram_link" value="{{ old('instagram_link', $center->instagram_link) }}" dir="ltr" class="block w-full rounded-xl border px-4 py-3 text-right text-gray-800 outline-none transition focus:border-gold focus:ring-4 focus:ring-gold/15 border-gray-300">
                        </div>
                        <div>
                            <label for="facebook_link" class="mb-2 block text-sm font-bold text-gray-700 font-cairo">رابط فيسبوك</label>
                            <input id="facebook_link" type="url" name="facebook_link" value="{{ old('facebook_link', $center->facebook_link) }}" dir="ltr" class="block w-full rounded-xl border px-4 py-3 text-right text-gray-800 outline-none transition focus:border-gold focus:ring-4 focus:ring-gold/15 border-gray-300">
                        </div>
                        <div>
                            <label for="location_link" class="mb-2 block text-sm font-bold text-gray-700 font-cairo">رابط الموقع (Google Maps)</label>
                            <input id="location_link" type="url" name="location_link" value="{{ old('location_link', $center->location_link) }}" dir="ltr" class="block w-full rounded-xl border px-4 py-3 text-right text-gray-800 outline-none transition focus:border-gold focus:ring-4 focus:ring-gold/15 border-gray-300">
                        </div>
                    </div>
                </section>
            </div>

            <footer class="flex flex-col-reverse gap-3 border-t border-gray-100 bg-gray-50 px-6 py-5 sm:flex-row sm:items-center sm:justify-end sm:px-8">
                <a href="{{ route('centers.show', $center) }}" class="inline-flex items-center justify-center gap-2 rounded-xl border border-gray-300 bg-white px-6 py-3 text-sm font-bold text-gray-700 transition-colors hover:border-navy hover:text-navy focus:outline-none focus:ring-4 focus:ring-navy/10 font-cairo">
                    <i class="fas fa-xmark" aria-hidden="true"></i>
                    إلغاء والعودة للتفاصيل
                </a>
                <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-gold px-7 py-3 text-sm font-black text-navy shadow-sm transition-all hover:-translate-y-0.5 hover:bg-gold/85 focus:outline-none focus:ring-4 focus:ring-gold/25 font-cairo">
                    <i class="fas fa-floppy-disk" aria-hidden="true"></i>
                    حفظ التعديلات
                </button>
            </footer>
        </form>
    </div>
@endsection
