@extends('layouts.app')

@section('title', 'تعديل بيانات النادي')

@section('content')
    <div class="container mx-auto px-6 py-8">
        <div class="max-w-3xl mx-auto">
            <div class="mb-8 flex justify-between items-center bg-white p-8 rounded-3xl border-l-8 border-gold shadow-sm">
                <div>
                    <h1 class="text-3xl font-black text-navy font-cairo">تعديل نادٍ</h1>
                    <p class="text-gray-400 font-almarai text-sm mt-2">تحديث بيانات النادي: {{ $club->name }}</p>
                </div>
                <a href="{{ route('clubs.index') }}"
                    class="px-6 py-3 bg-gray-50 text-navy rounded-2xl hover:bg-gray-100 font-cairo font-bold transition-all flex items-center gap-2 border border-gray-100">
                    <i class="fas fa-arrow-right"></i>
                    <span>رجوع</span>
                </a>
            </div>

            <div class="bg-white rounded-[2.5rem] p-10 shadow-sm border border-gray-100">
                <form action="{{ route('clubs.update', $club->id) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        @if($club->logo)
                        <div class="md:col-span-2 flex items-center gap-6 bg-gray-50 p-6 rounded-3xl border border-gray-100">
                            <div class="w-24 h-24 bg-white rounded-2xl overflow-hidden shadow-sm flex items-center justify-center p-2">
                                <img src="{{ asset('storage/' . $club->logo) }}" class="max-w-full max-h-full object-contain">
                            </div>
                            <div>
                                <h4 class="text-sm font-black text-navy font-cairo">الشعار الحالي</h4>
                                <p class="text-xs text-gray-400 mt-1">يمكنك رفع شعار جديد لاستبداله</p>
                            </div>
                        </div>
                        @endif

                        <div class="md:col-span-2">
                            <label class="block text-sm font-black text-navy mb-3 font-cairo">رفع شعار جديد</label>
                            <input type="file" name="logo" accept="image/*"
                                class="w-full px-5 py-3 rounded-2xl border border-gray-100 bg-gray-50/50 outline-none transition-all font-almarai text-sm">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-black text-navy mb-3 font-cairo">اسم النادي</label>
                            <input type="text" name="name" value="{{ $club->name }}" required
                                class="w-full px-5 py-4 rounded-2xl border border-gray-100 bg-gray-50/50 focus:bg-white focus:ring-4 focus:ring-navy/5 focus:border-navy outline-none transition-all font-almarai">
                        </div>

                        <div>
                            <label class="block text-sm font-black text-navy mb-3 font-cairo">التصنيف</label>
                            <select name="category" required
                                class="w-full px-5 py-4 rounded-2xl border border-gray-100 bg-gray-50/50 focus:bg-white focus:ring-4 focus:ring-navy/5 outline-none transition-all font-almarai">
                                <option value="ثقافي" {{ $club->category == 'ثقافي' ? 'selected' : '' }}>ثقافي</option>
                                <option value="رياضي" {{ $club->category == 'رياضي' ? 'selected' : '' }}>رياضي</option>
                                <option value="تقني" {{ $club->category == 'تقني' ? 'selected' : '' }}>تقني</option>
                                <option value="فني" {{ $club->category == 'فني' ? 'selected' : '' }}>فني</option>
                                <option value="اجتماعي" {{ $club->category == 'اجتماعي' ? 'selected' : '' }}>اجتماعي</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-black text-navy mb-3 font-cairo">الحالة</label>
                            <select name="status" required
                                class="w-full px-5 py-4 rounded-2xl border border-gray-100 bg-gray-50/50 focus:bg-white focus:ring-4 focus:ring-navy/5 outline-none transition-all font-almarai">
                                <option value="active" {{ $club->status == 'active' ? 'selected' : '' }}>نشط</option>
                                <option value="inactive" {{ $club->status == 'inactive' ? 'selected' : '' }}>معطل</option>
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-black text-navy mb-3 font-cairo">وصف النادي</label>
                            <textarea name="description" rows="5" required
                                class="w-full px-5 py-4 rounded-2xl border border-gray-100 bg-gray-50/50 focus:bg-white focus:ring-4 focus:ring-navy/5 outline-none transition-all font-almarai resize-none">{{ $club->description }}</textarea>
                        </div>
                    </div>

                    <div class="flex gap-4 pt-6">
                        <button type="submit"
                            class="flex-[3] bg-navy text-white py-5 rounded-2xl font-black text-lg shadow-xl hover:shadow-navy/40 transition-all flex items-center justify-center gap-3">
                            <i class="fas fa-save text-gold"></i>
                            <span>حفظ التعديلات</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
