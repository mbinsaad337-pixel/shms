@extends('layouts.app')
@section('title', 'تعديل الخبر')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="flex items-center gap-3 mb-8">
            <a href="{{ route('news.show', $news) }}" class="w-10 h-10 bg-white rounded-full shadow-sm flex items-center justify-center text-gray-400 hover:text-navy transition-all">
                <i class="fas fa-arrow-right"></i>
            </a>
            <h2 class="text-2xl font-bold text-navy font-cairo">تعديل الخبر</h2>
        </div>

        @php $errorList = isset($errors) ? $errors->all() : []; @endphp
        @if(count($errorList) > 0)
            <div class="mb-6 bg-rose-50 border border-rose-200 p-4 rounded-2xl text-rose-700 font-cairo text-sm">
                <ul class="space-y-1">
                    @foreach($errorList as $e)
                        <li>• {{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('news.update', $news) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf @method('PUT')

            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 space-y-6">
                <h3 class="font-black text-navy font-cairo text-lg border-b border-gray-100 pb-4 flex items-center gap-2">
                    <i class="fas fa-pen-to-square text-gold"></i> تفاصيل الخبر
                </h3>

                <div>
                    <label class="block text-sm font-black text-navy mb-2 font-cairo">عنوان الخبر <span class="text-rose-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title', $news->title) }}" required
                        class="w-full px-5 py-4 rounded-2xl border border-gray-100 bg-gray-50/50 focus:bg-white focus:ring-4 focus:ring-navy/5 focus:border-navy outline-none font-almarai transition-all">
                </div>

                <div>
                    <label class="block text-sm font-black text-navy mb-3 font-cairo">التصنيف <span class="text-rose-500">*</span></label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        @foreach(['general' => ['label' => 'عام', 'icon' => 'fa-newspaper'],
                                  'sports'  => ['label' => 'رياضي', 'icon' => 'fa-futbol'],
                                  'culture' => ['label' => 'ثقافي', 'icon' => 'fa-book-open'],
                                  'achievement' => ['label' => 'إنجاز', 'icon' => 'fa-trophy']] as $val => $cat)
                            <label class="category-btn cursor-pointer p-4 rounded-2xl border-2 text-center hover:border-navy transition-all {{ old('category', $news->category) === $val ? 'border-navy bg-navy/5' : 'border-gray-100' }}">
                                <input type="radio" name="category" value="{{ $val }}" class="sr-only" {{ old('category', $news->category) === $val ? 'checked' : '' }}>
                                <i class="fas {{ $cat['icon'] }} text-2xl text-navy mb-2 block"></i>
                                <span class="font-bold font-cairo text-sm text-navy">{{ $cat['label'] }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-black text-navy mb-2 font-cairo">محتوى الخبر <span class="text-rose-500">*</span></label>
                    <textarea name="body" rows="10" required
                        class="w-full px-5 py-4 rounded-2xl border border-gray-100 bg-gray-50/50 focus:bg-white focus:ring-4 focus:ring-navy/5 focus:border-navy outline-none font-almarai text-sm leading-relaxed transition-all">{{ old('body', $news->body) }}</textarea>
                </div>
            </div>

            {{-- Images & Video Media --}}
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 space-y-8">
                <h3 class="font-black text-navy font-cairo text-lg border-b border-gray-100 pb-4 flex items-center gap-2">
                    <i class="fas fa-photo-film text-gold"></i> الوسائط (الصور والفيديو)
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    {{-- Cover --}}
                    <div class="space-y-4">
                        <label class="block text-sm font-black text-navy font-cairo">صورة الغلاف (الرئيسية)</label>
                        @if($news->cover_image)
                            <div class="relative h-40 rounded-2xl overflow-hidden group">
                                <img src="{{ asset('storage/'.$news->cover_image) }}" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-navy/60 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                    <span class="text-white text-xs font-bold font-cairo">صورة الغلاف الحالية</span>
                                </div>
                            </div>
                        @endif
                        <input type="file" name="cover_image" accept="image/*"
                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-navy/5 file:text-navy hover:file:bg-navy/10 transition-all">
                    </div>

                    {{-- Gallery --}}
                    <div class="space-y-4">
                        <label class="block text-sm font-black text-navy font-cairo">معرض الصور (إضافة صور جديدة)</label>
                        @if($news->gallery && count($news->gallery))
                            <div class="grid grid-cols-4 gap-2 h-40 overflow-y-auto custom-scrollbar p-2 bg-gray-50 rounded-2xl">
                                @foreach($news->gallery as $img)
                                    <img src="{{ asset('storage/'.$img) }}" class="w-full aspect-square object-cover rounded-lg border border-white">
                                @endforeach
                            </div>
                        @endif
                        <input type="file" name="gallery[]" accept="image/*" multiple
                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-navy/5 file:text-navy hover:file:bg-navy/10 transition-all">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-8 border-t border-gray-50">
                    {{-- Video URL --}}
                    <div class="space-y-4">
                        <label class="block text-sm font-black text-navy font-cairo">رابط فيديو (YouTube)</label>
                        <div class="relative">
                            <i class="fab fa-youtube absolute right-5 top-1/2 -translate-y-1/2 text-rose-600 text-xl"></i>
                            <input type="url" name="video_url" value="{{ old('video_url', $news->video_url) }}"
                                placeholder="https://youtube.com/watch?v=..."
                                class="w-full pr-12 pl-5 py-4 rounded-xl border border-gray-100 bg-gray-50/50 focus:bg-white focus:ring-4 focus:ring-navy/5 outline-none font- mono text-xs transition-all ltr">
                        </div>
                    </div>

                    {{-- Video File --}}
                    <div class="space-y-4">
                        <label class="block text-sm font-black text-navy font-cairo">رفع فيديو مباشر</label>
                        @if($news->video_path)
                            <div class="flex items-center gap-3 p-3 bg-emerald-50 rounded-xl border border-emerald-100">
                                <i class="fas fa-video text-emerald-600"></i>
                                <span class="text-[10px] font-bold text-emerald-800 font-cairo">يوجد ملف فيديو مرفوع حالياً</span>
                            </div>
                        @endif
                        <input type="file" name="video_file" accept="video/*"
                            class="w-full px-5 py-3 rounded-xl border border-gray-100 bg-gray-50/50 outline-none transition-all font-almarai text-sm">
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                <label class="flex items-center gap-4 cursor-pointer p-4 bg-emerald-50 rounded-2xl border border-emerald-100 hover:bg-emerald-100 transition-all">
                    <input type="checkbox" name="is_published" value="1" {{ old('is_published', $news->is_published) ? 'checked' : '' }}
                        class="w-5 h-5 rounded text-emerald-600 focus:ring-emerald-500">
                    <div>
                        <p class="font-bold font-cairo text-emerald-800">نشر ومشاركة الخبر</p>
                        <p class="text-sm font-almarai text-emerald-600">ستظهر هذه المادة في شريط الأخبار أسفل صفحة تسجيل الدخول</p>
                    </div>
                </label>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="flex-1 bg-navy text-white py-4 rounded-2xl font-black font-cairo text-lg shadow-xl shadow-navy/20 hover:-translate-y-0.5 transition-all flex items-center justify-center gap-3">
                    <i class="fas fa-save text-gold"></i> حفظ التعديلات
                </button>
                <a href="{{ route('news.show', $news) }}" class="px-8 py-4 bg-gray-100 text-gray-500 rounded-2xl font-bold font-cairo hover:bg-gray-200 transition-all">إلغاء</a>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.category-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.category-btn').forEach(b => b.classList.remove('border-navy', 'bg-navy/5'));
            btn.classList.add('border-navy', 'bg-navy/5');
        });
    });
</script>
@endpush
