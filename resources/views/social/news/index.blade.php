@extends('layouts.app')
@section('title', auth()->user()->can('manage-news') ? 'إدارة الأخبار والإعلانات' : 'آخر الأخبار والإعلانات')

@section('content')
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-primary font-cairo">
                {{ auth()->user()->can('manage-news') ? 'إدارة الأخبار والإعلانات' : 'آخر الأخبار والإعلانات' }}
            </h1>
            <p class="text-gray-500 font-almarai mt-1">
                {{ auth()->user()->can('manage-news') ? 'نشر وإدارة أخبار المركز والفعاليات الاجتماعية' : 'تابع أحدث أنشطة وأخبار المركز' }}
            </p>
        </div>
        @can('manage-news')
            <a href="{{ route('news.create') }}"
                class="flex items-center gap-3 bg-navy text-white px-8 py-3.5 rounded-2xl font-bold font-cairo shadow-lg shadow-navy/20 hover:-translate-y-0.5 transition-all">
                <i class="fas fa-plus"></i> خبر جديد
            </a>
        @endcan
    </div>

    @if(session('success'))
        <div
            class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl font-cairo font-bold flex items-center gap-3">
            <i class="fas fa-check-circle text-xl"></i> {{ session('success') }}
        </div>
    @endif

    @if(is_countable($news) ? count($news) > 0 : (method_exists($news, 'count') ? $news->count() > 0 : !empty($news)))
    @foreach($news as $item)
        @php /* @var \App\Models\News $item */ @endphp
        <div
            class="bg-white rounded-3xl shadow-sm border border-gray-100 mb-5 overflow-hidden flex flex-col md:flex-row group hover:shadow-md transition-all">
            {{-- Cover --}}
            <div class="md:w-52 h-44 md:h-auto bg-gray-50 shrink-0 overflow-hidden">
                @if($item->cover_image)
                    <img src="{{ asset('storage/' . $item->cover_image) }}"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                @else
                    <div class="w-full h-full flex items-center justify-center text-gray-200">
                        <i class="fas fa-image text-5xl"></i>
                    </div>
                @endif
            </div>

            {{-- Content --}}
            <div class="p-6 flex-1 flex flex-col">
                <div class="flex items-start justify-between gap-4 mb-2">
                    <div>
                        <span class="text-xs px-2.5 py-1 rounded-full font-bold font-cairo mr-1
                                                            {{ match ($item->category) {
                'sports' => 'bg-blue-100 text-blue-700',
                'culture' => 'bg-purple-100 text-purple-700',
                'achievement' => 'bg-amber-100 text-amber-700',
                default => 'bg-gray-100 text-gray-600'
            } }}">
                            {{ $item->getCategoryLabel() }}
                        </span>
                        <span
                            class="text-[10px] px-2.5 py-1 rounded-full font-bold font-cairo {{ $item->is_published ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-600' }}">
                            {{ $item->is_published ? 'منشور' : 'مسودة' }}
                        </span>
                    </div>
                    <p class="text-xs text-gray-400 font-mono shrink-0">{{ $item->created_at->format('Y-m-d') }}</p>
                </div>
                <h2 class="text-xl font-black text-navy font-cairo mb-2 leading-snug">{{ $item->title }}</h2>
                <p class="text-gray-500 font-almarai text-sm line-clamp-2 flex-1">{{ strip_tags($item->body) }}</p>

                <div class="flex items-center gap-3 mt-4 pt-4 border-t border-gray-50">
                    <a href="{{ route('news.show', $item) }}"
                        class="px-5 py-2 bg-navy/5 text-navy rounded-xl font-bold font-cairo text-sm hover:bg-navy hover:text-white transition-all">
                        <i class="fas fa-eye ml-1"></i> عرض
                    </a>
                    @can('manage-news')
                        <a href="{{ route('news.edit', $item) }}"
                            class="px-5 py-2 bg-gold/10 text-gold rounded-xl font-bold font-cairo text-sm hover:bg-gold hover:text-white transition-all">
                            <i class="fas fa-pencil ml-1"></i> تعديل
                        </a>
                    @endcan
                    @can('publish-news')
                        <form action="{{ route('news.toggle-publish', $item) }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="px-5 py-2 {{ $item->is_published ? 'bg-rose-50 text-rose-600 hover:bg-rose-600' : 'bg-emerald-50 text-emerald-600 hover:bg-emerald-600' }} rounded-xl font-bold font-cairo text-sm hover:text-white transition-all">
                                <i class="fas {{ $item->is_published ? 'fa-eye-slash' : 'fa-paper-plane' }} ml-1"></i>
                                {{ $item->is_published ? 'إلغاء النشر' : 'نشر الآن' }}
                            </button>
                        </form>
                    @endcan
                    @can('delete-news')
                        <form action="{{ route('news.destroy', $item) }}" method="POST" class="mr-auto"
                            onsubmit="return confirm('هل أنت متأكد؟')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                class="px-5 py-2 bg-gray-50 text-gray-400 rounded-xl font-bold font-cairo text-sm hover:bg-rose-500 hover:text-white transition-all">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    @endcan
                </div>
            </div>
        </div>
        @endforeach
@else
        <div class="bg-white rounded-3xl p-20 text-center border-2 border-dashed border-gray-100 shadow-sm">
            <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-newspaper text-4xl text-gray-200"></i>
            </div>
            <h3 class="text-2xl font-black text-navy font-cairo">لا توجد أخبار مُضافة بعد</h3>
            <p class="text-gray-400 font-almarai mt-2 mb-8">ابدأ بنشر أول خبر أو إعلان للطلاب في مركزك</p>
            <a href="{{ route('news.create') }}"
                class="inline-flex items-center gap-2 bg-navy text-white px-8 py-4 rounded-2xl font-bold font-cairo shadow-lg hover:-translate-y-0.5 transition-all">
                <i class="fas fa-plus"></i> نشر خبر الآن
            </a>
        </div>
    @endif

    <div class="mt-8">{{ $news->links() }}</div>
@endsection
