@extends('layouts.app')
@php
    /** @var \App\Models\Student $student */
    $preview = $preview ?? false;
    $previewArchive = $previewArchive ?? null;
@endphp
@section('title', $news->title)

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="flex items-center gap-3 mb-8 no-print">
          @if(!$preview)
            <a href="{{ route('news.index') }}"
                class="w-10 h-10 bg-white rounded-full shadow-sm flex items-center justify-center text-gray-400 hover:text-navy transition-all">
                <i class="fas fa-arrow-right"></i>
            </a>
          @elseif($preview && $previewArchive)
          <a href="{{ route('annual-rollover.index', $previewArchive) }}"
                class="w-10 h-10 bg-white rounded-full shadow-sm flex items-center justify-center text-gray-400 hover:text-navy transition-all">
                <i class="fas fa-arrow-right"></i>
            </a>  @endif
            <h2 class="text-xl font-bold text-navy font-cairo">الأخبار والإعلانات</h2>
        </div>

        <article class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            {{-- Cover Image --}}
            @if ($news->cover_image)
                <div class="h-72 overflow-hidden">
                    <img src="{{ asset('storage/' . $news->cover_image) }}" class="w-full h-full object-cover"
                        alt="{{ $news->title }}">
                </div>
            @endif

            <div class="p-10">
                {{-- Meta --}}
                <div class="flex items-center gap-3 mb-4">
                    <span
                        class="text-xs px-3 py-1 rounded-full font-bold font-cairo bg-navy/10 text-navy">{{ $news->getCategoryLabel() }}</span>
                    <span
                        class="text-xs px-3 py-1 rounded-full font-bold {{ $news->is_published ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-600' }}">{{ $news->is_published ? 'منشور' : 'مسودة' }}</span>
                    <span class="text-xs text-gray-400   mr-auto">{{ $news->created_at->format('Y/m/d') }}</span>
                </div>

                <h1 class="text-3xl font-black text-navy font-cairo leading-snug mb-6">{{ $news->title }}</h1>

                {{-- Video Section --}}
                @if ($news->video_url || $news->video_path)
                    <div class="mb-10 rounded-2xl overflow-hidden bg-black shadow-lg">
                        @if($news->video_url)
                            @php
                                $videoId = null;
                                if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $news->video_url, $matches)) {
                                    $videoId = $matches[1];
                                }
                            @endphp
                            @if($videoId)
                                <div class="aspect-video">
                                    <iframe class="w-full h-full" src="https://www.youtube.com/embed/{{ $videoId }}" frameborder="0" allowfullscreen></iframe>
                                </div>
                            @else
                                <div class="p-6 text-center">
                                    <a href="{{ $news->video_url }}" target="_blank" class="text-white hover:text-gold transition-colors underline   text-sm">
                                        <i class="fas fa-external-link-alt ml-2"></i> شاهد الفيديو من المصدر الخارجي
                                    </a>
                                </div>
                            @endif
                        @else
                            <video controls class="w-full max-h-[500px]">
                                <source src="{{ asset('storage/' . $news->video_path) }}" type="video/mp4">
                                متصفحك لا يدعم تشغيل الفيديو.
                            </video>
                        @endif
                    </div>
                @endif

                <div class="prose prose-lg font-almarai text-gray-700 leading-relaxed max-w-none">
                    {!! nl2br(e($news->body)) !!}
                </div>

                {{-- Gallery --}}
                @if($news->gallery && count($news->gallery))
                    <div class="mt-10">
                        <h3 class="text-lg font-bold text-navy font-cairo mb-4 border-r-4 border-gold pr-3">معرض الصور</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            @foreach ($news->gallery as $img)
                                <a href="{{ asset('storage/' . $img) }}" target="_blank"
                                    class="overflow-hidden rounded-2xl aspect-square block group border border-gray-100">
                                    <img src="{{ asset('storage/' . $img) }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Interactions (Likes & Shares) --}}
                <div class="mt-12 pt-8 border-t border-gray-100 flex items-center justify-between">
                    <div class="flex items-center gap-6">
                        <form action="{{ route('news.like', $news) }}" method="POST">
                            @csrf
                            <button type="submit" class="flex items-center gap-2 group transition-all">
                                <div class="w-12 h-12 rounded-full flex items-center justify-center transition-all {{ $news->isLikedBy(auth()->id()) ? 'bg-rose-50 text-rose-500 shadow-rose-100' : 'bg-gray-50 text-gray-400 hover:bg-rose-50 hover:text-rose-400' }} shadow-sm">
                                    <i class="fa{{ $news->isLikedBy(auth()->id()) ? 's' : 'r' }} fa-heart text-xl group-hover:scale-125 transition-transform"></i>
                                </div>
                                <span class="font-black font-cairo {{ $news->isLikedBy(auth()->id()) ? 'text-rose-600' : 'text-gray-400' }}">{{ $news->likes()->count() }}</span>
                            </button>
                        </form>

                        <div class="flex items-center gap-2">
                            <div class="w-12 h-12 bg-gray-50 text-gray-400 rounded-full flex items-center justify-center shadow-sm">
                                <i class="far fa-comment text-xl"></i>
                            </div>
                            <span class="font-black font-cairo text-gray-400">{{ $news->comments()->count() }}</span>
                        </div>
                    </div>

                    <div class="flex gap-2">
                    </div>
                </div>

                {{-- Comments Section --}}
                <div class="mt-12 bg-gray-50/50 rounded-[2.5rem] p-8 border border-gray-100" id="comments">
                    <h3 class="text-xl font-black text-navy font-cairo mb-8 flex items-center gap-3">
                        <i class="fas fa-comments text-gold"></i> التعليقات والمناقشة
                    </h3>

                    <form action="{{ route('news.comments.add', $news) }}" method="POST" class="mb-10 group">
                        @csrf
                        <div class="relative">
                            <textarea name="content" rows="3" required
                                placeholder="اكتب تعليقك هنا..."
                                class="w-full px-6 py-5 rounded-3xl border border-gray-100 bg-white focus:bg-white focus:ring-8 focus:ring-navy/5 outline-none font-almarai text-sm transition-all shadow-sm"></textarea>
                            <button type="submit"
                                class="absolute left-4 bottom-4 bg-navy text-white px-6 py-3 rounded-2xl font-bold font-cairo text-sm hover:bg-gold hover:text-navy transition-all shadow-lg flex items-center gap-2">
                                <span>تعليق</span>
                                <i class="fas fa-paper-plane ltr:rotate-0 rtl:rotate-180"></i>
                            </button>
                        </div>
                    </form>

                    <div class="space-y-6">
                        @if ($news->comments->count() > 0)
                            @foreach ($news->comments as $comment)
                                <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-50 flex gap-4 group">
                                    <div class="w-12 h-12 bg-navy/5 rounded-2xl flex items-center justify-center text-navy shrink-0 font-black">
                                        {{ mb_substr($comment->user->name, 0, 1) }}
                                    </div>
                                    <div class="flex-grow">
                                        <div class="flex items-center justify-between mb-2">
                                            <div>
                                                <span class="font-bold text-navy font-cairo">{{ $comment->user->name }}</span>
                                                <span class="text-[10px] text-gray-400 mr-2  ">{{ $comment->created_at->diffForHumans() }}</span>
                                            </div>
                                            @if (auth()->id() === $comment->user_id || auth()->user()->can('manage-news'))
                                                <form action="{{ route('news.comments.delete', $comment) }}" method="POST" data-confirm="حذف التعليق؟">
                                                    @csrf
                                                    <button type="submit" class="text-gray-300 hover:text-rose-500 opacity-0 group-hover:opacity-100 transition-all">
                                                        <i class="fas fa-trash-alt text-xs"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                        <p class="text-gray-600 font-almarai text-sm leading-relaxed">{{ $comment->content }}</p>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-10">
                                <i class="fas fa-comment-slash text-4xl text-gray-100 mb-3 block"></i>
                                <p class="text-gray-300 font-almarai text-sm italic">لا توجد تعليقات بعد، كن أول من يعلق!</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Administrative Actions --}}
                <div class="flex items-center gap-3 mt-12 pt-8 border-t border-gray-100 no-print">
                    @can('manage-news')
                        <a href="{{ route('news.edit', $news) }}"
                            class="inline-flex items-center gap-2 bg-gold/10 text-gold px-6 py-3 rounded-xl font-bold font-cairo hover:bg-gold hover:text-white transition-all">
                            <i class="fas fa-pencil"></i> تعديل الخبر
                        </a>
                    @endcan
                    @can('publish-news')
                        <form action="{{ route('news.toggle-publish', $news) }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="inline-flex items-center gap-2 {{ $news->is_published ? 'bg-rose-50 text-rose-600 hover:bg-rose-600' : 'bg-emerald-50 text-emerald-600 hover:bg-emerald-600' }} px-6 py-3 rounded-xl font-bold font-cairo hover:text-white transition-all">
                                <i class="fas {{ $news->is_published ? 'fa-eye-slash' : 'fa-paper-plane' }}"></i>
                                {{ $news->is_published ? 'إلغاء النشر' : 'نشر الآن' }}
                            </button>
                        </form>
                    @endcan
                    @can('delete-news')
                        <form action="{{ route('news.destroy', $news) }}" method="POST" class="mr-auto"
                            data-confirm="هل أنت متأكد من حذف هذا الخبر؟">
                            @csrf
                            <button type="submit"
                                class="inline-flex items-center gap-2 bg-gray-50 text-gray-400 px-6 py-3 rounded-xl font-bold font-cairo hover:bg-rose-500 hover:text-white transition-all">
                                <i class="fas fa-trash-alt"></i> حذف
                            </button>
                        </form>
                    @endcan
                </div>
            </div>
        </article>
    </div>
@endsection
