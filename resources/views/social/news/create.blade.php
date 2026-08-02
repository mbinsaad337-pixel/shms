@extends('layouts.app')
@section('title', 'نشر خبر جديد')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="flex items-center gap-3 mb-8">
            <a href="{{ route('news.index') }}"
                class="w-10 h-10 bg-white rounded-full shadow-sm flex items-center justify-center text-gray-400 hover:text-navy transition-all">
                <i class="fas fa-arrow-right"></i>
            </a>
            <h2 class="text-2xl font-bold text-navy font-cairo">نشر خبر جديد</h2>
        </div>

        @php $errorList = isset($errors) ? $errors->all() : []; @endphp
        @if (count($errorList) > 0)
            <div class="mb-6 bg-rose-50 border border-rose-200 p-4 rounded-2xl text-rose-700 font-cairo text-sm">
                <ul class="space-y-1">
                    @foreach ($errorList as $e)
                        <li>• {{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('news.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf

            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 space-y-6">
                <h3 class="font-black text-navy font-cairo text-lg border-b border-gray-100 pb-4 flex items-center gap-2">
                    <i class="fas fa-pen-to-square text-gold"></i> تفاصيل الخبر
                </h3>

                {{-- Title --}}
                <div>
                    <label class="block text-sm font-black text-navy mb-2 font-cairo">عنوان الخبر <span
                            class="text-rose-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" required
                        placeholder="اكتب عنواناً واضحاً وجذاباً..."
                        class="w-full px-5 py-4 rounded-2xl border border-gray-100 bg-gray-50/50 focus:bg-white focus:ring-4 focus:ring-navy/5 focus:border-navy outline-none font-almarai transition-all">
                </div>

                {{-- Category --}}
                <div>
                    <label class="block text-sm font-black text-navy mb-3 font-cairo">التصنيف <span
                            class="text-rose-500">*</span></label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3" id="categoryGroup">
                        @foreach (['general' => ['label' => 'عام', 'icon' => 'fa-newspaper', 'color' => 'navy'], 'sports' => ['label' => 'رياضي', 'icon' => 'fa-futbol', 'color' => 'blue'], 'culture' => ['label' => 'ثقافي', 'icon' => 'fa-book-open', 'color' => 'purple'], 'achievement' => ['label' => 'إنجاز', 'icon' => 'fa-trophy', 'color' => 'amber']] as $val => $cat)
                            <label
                                class="category-btn cursor-pointer p-4 rounded-2xl border-2 text-center hover:border-navy transition-all {{ old('category', 'general') === $val ? 'border-navy bg-navy/5' : 'border-gray-100' }}">
                                <input type="radio" name="category" value="{{ $val }}" class="sr-only"
                                    {{ old('category', 'general') === $val ? 'checked' : '' }}>
                                <i class="fas {{ $cat['icon'] }} text-2xl text-navy mb-2 block"></i>
                                <span class="font-bold font-cairo text-sm text-navy">{{ $cat['label'] }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Body --}}
                <div>
                    <label class="block text-sm font-black text-navy mb-2 font-cairo">محتوى الخبر <span
                            class="text-rose-500">*</span></label>
                    <textarea name="body" rows="10" required placeholder="اكتب تفاصيل الخبر هنا..."
                        class="w-full px-5 py-4 rounded-2xl border border-gray-100 bg-gray-50/50 focus:bg-white focus:ring-4 focus:ring-navy/5 focus:border-navy outline-none font-almarai text-sm leading-relaxed transition-all">{{ old('body') }}</textarea>
                </div>
            </div>

            {{-- Images & Video --}}
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 space-y-8">
                <h3 class="font-black text-navy font-cairo text-lg border-b border-gray-100 pb-4 flex items-center gap-2">
                    <i class="fas fa-photo-film text-gold"></i> الوسائط (الصور والفيديو)
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    {{-- Cover --}}
                    <div>
                        <label class="block text-sm font-black text-navy mb-3 font-cairo">صورة الغلاف (الرئيسية)</label>
                        <div class="relative border-2 border-dashed border-gray-200 rounded-2xl p-8 text-center hover:border-navy transition-all cursor-pointer h-48 flex flex-col items-center justify-center"
                            onclick="document.getElementById('cover_image').click()">
                            <div id="coverPreview" class="hidden h-full w-full">
                                <img id="coverImg" class="mx-auto h-full w-full rounded-xl object-cover">
                            </div>
                            <div id="coverPlaceholder">
                                <i class="fas fa-image text-4xl text-gray-200 mb-3 block"></i>
                                <p class="text-gray-400 font-almarai text-sm">اضغط لرفع صورة الغلاف</p>
                                <p class="text-gray-300 text-xs mt-1">JPG, PNG حتى 4 ميجابايت</p>
                            </div>
                            <input type="file" name="cover_image" id="cover_image" accept="image/*" class="sr-only"
                                onchange="previewCover(event)">
                        </div>
                    </div>

                    {{-- Gallery --}}
                    <div>
                        <label class="block text-sm font-black text-navy mb-3 font-cairo">معرض الصور (إضافية)</label>
                        <div class="relative border-2 border-dashed border-gray-200 rounded-2xl p-8 text-center hover:border-navy transition-all cursor-pointer h-48 flex flex-col items-center justify-center overflow-hidden"
                            onclick="document.getElementById('gallery').click()">
                            <div id="galleryPreview" class="grid grid-cols-3 gap-1 h-full w-full hidden"></div>
                            <div id="galleryPlaceholder">
                                <i class="fas fa-images text-4xl text-gray-200 mb-3 block"></i>
                                <p class="text-gray-400 font-almarai text-sm">اضغط لرفع صور معرض إضافية</p>
                            </div>
                            <input type="file" name="gallery[]" id="gallery" accept="image/*" multiple class="sr-only"
                                onchange="previewGallery(event)">
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-4 border-t border-gray-50">
                    {{-- Video URL --}}
                    <div>
                        <label class="block text-sm font-black text-navy mb-3 font-cairo">رابط فيديو
                            (YouTube/YouTube)</label>
                        <div class="relative">
                            <i class="fab fa-youtube absolute right-5 top-1/2 -translate-y-1/2 text-rose-600 text-xl"></i>
                            <input type="url" name="video_url" value="{{ old('video_url') }}"
                                placeholder="https://youtube.com/watch?v=..."
                                class="w-full pr-12 pl-5 py-4 rounded-xl border border-gray-100 bg-gray-50/50 focus:bg-white focus:ring-4 focus:ring-navy/5 outline-none font- mono text-xs transition-all ltr">
                        </div>
                    </div>

                    {{-- Video File --}}
                    <div>
                        <label class="block text-sm font-black text-navy mb-3 font-cairo">أو رفع ملف فيديو مباشر</label>
                        <input type="file" name="video_file" id="video_file" accept="video/*"
                            class="w-full px-5 py-3 rounded-xl border border-gray-100 bg-gray-50/50 outline-none transition-all font-almarai text-sm">
                        <p class="text-[10px] text-gray-400 mt-2 font-almarai mr-2">• يدعم MP4, MOV بحد أقصى 20 ميجابايت</p>
                    </div>
                </div>
            </div>

            {{-- Publish Options --}}
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                <h3
                    class="font-black text-navy font-cairo text-lg border-b border-gray-100 pb-4 mb-6 flex items-center gap-2">
                    <i class="fas fa-paper-plane text-gold"></i> مسار الاعتماد والنشر
                </h3>
                @if (auth()->user()->hasAnyRole(['super-admin', 'media-officer']))
                    <label
                        class="flex items-center gap-4 cursor-pointer p-4 bg-emerald-50 rounded-2xl border border-emerald-100 hover:bg-emerald-100 transition-all">
                        <input type="checkbox" name="is_published" value="1"
                            {{ old('is_published', '1') ? 'checked' : '' }}
                            class="w-5 h-5 rounded text-emerald-600 focus:ring-emerald-500">
                        <div>
                            <p class="font-bold font-cairo text-emerald-800">نشر فوري مباشر</p>
                            <p class="text-sm font-almarai text-emerald-600">بصفتك مسؤول الإعلام/المدير العام، سيتم الاعتماد
                                والنشر فوراً في المنصة والشريط الإخباري</p>
                        </div>
                    </label>
                @else
                    <div class="p-5 bg-amber-50 rounded-2xl border border-amber-200 flex items-start gap-4">
                        <div
                            class="w-10 h-10 bg-amber-500 text-white rounded-xl flex items-center justify-center shrink-0 text-lg shadow-sm">
                            <i class="fas fa-hourglass-half"></i>
                        </div>
                        <div>
                            <p class="font-bold font-cairo text-amber-900 text-base">إشعار اعتماد الإعلان</p>
                            <p class="text-sm font-almarai text-amber-700 mt-1 leading-relaxed">
                                عند إرسال الإعلان، سيوجه تلقائياً إلى <strong>قائمة الانتظار لمسؤول الإعلام</strong>
                                للمعاينة والاعتماد قبل ظهوره في المنصة، وسيمكّنك النظام من إرسال إشعار فوري عبر واتساب
                                لمسؤول الإعلام.
                            </p>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Submit --}}
            <div class="flex gap-4">
                <button type="submit"
                    class="flex-1 bg-navy text-white py-4 rounded-2xl font-black font-cairo text-lg shadow-xl shadow-navy/20 hover:-translate-y-0.5 transition-all flex items-center justify-center gap-3">
                    <i class="fas fa-paper-plane text-gold"></i> إرسال الإعلان للانتظار والاعتماد
                </button>
                <a href="{{ route('news.index') }}"
                    class="px-8 py-4 bg-gray-100 text-gray-500 rounded-2xl font-bold font-cairo hover:bg-gray-200 transition-all">إلغاء</a>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        // Category radio visual selection
        document.querySelectorAll('.category-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.category-btn').forEach(b => {
                    b.classList.remove('border-navy', 'bg-navy/5');
                    b.classList.add('border-gray-100');
                });
                btn.classList.remove('border-gray-100');
                btn.classList.add('border-navy', 'bg-navy/5');
            });
        });

        function previewCover(event) {
            const file = event.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = e => {
                document.getElementById('coverImg').src = e.target.result;
                document.getElementById('coverPreview').classList.remove('hidden');
                document.getElementById('coverPlaceholder').classList.add('hidden');
            };
            reader.readAsDataURL(file);
        }

        function previewGallery(event) {
            const files = Array.from(event.target.files);
            const container = document.getElementById('galleryPreview');
            container.innerHTML = '';
            files.forEach(file => {
                const reader = new FileReader();
                reader.onload = e => {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'w-full aspect-square object-cover rounded-xl';
                    container.appendChild(img);
                };
                reader.readAsDataURL(file);
            });
            container.classList.remove('hidden');
            document.getElementById('galleryPlaceholder').classList.add('hidden');
        }
    </script>
@endpush
