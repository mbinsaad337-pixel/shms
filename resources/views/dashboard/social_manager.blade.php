@extends('layouts.app')
@section('title', 'لوحة تحكم الشؤون الاجتماعية')

@section('content')
    {{-- Header --}}
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-primary font-cairo">الشؤون الاجتماعية والأنشطة</h1>
            <p class="text-gray-500 font-almarai mt-1">إدارة الأندية، الفعاليات، والأخبار لمركز {{ auth()->user()->center->name ?? '' }}</p>
        </div>
        <span class="bg-gold/10 text-gold border border-gold/20 px-5 py-2 rounded-xl text-sm font-bold font-almarai">مسؤول الأنشطة</span>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 card-hover flex items-center gap-4">
            <div class="bg-navy/10 p-4 rounded-2xl text-navy shrink-0"><i class="fas fa-users-rectangle text-2xl"></i></div>
            <div>
                <p class="text-xs text-gray-400 font-bold">الأندية النشطة</p>
                <h3 class="text-3xl font-black text-navy">{{ $stats['clubs_count'] }}</h3>
            </div>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 card-hover flex items-center gap-4">
            <div class="bg-gold/10 p-4 rounded-2xl text-gold shrink-0"><i class="fas fa-calendar-check text-2xl"></i></div>
            <div>
                <p class="text-xs text-gray-400 font-bold">فعاليات قادمة</p>
                <h3 class="text-3xl font-black text-navy">{{ $stats['activities_count'] }}</h3>
            </div>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 card-hover flex items-center gap-4">
            <div class="bg-emerald-100 p-4 rounded-2xl text-emerald-600 shrink-0"><i class="fas fa-newspaper text-2xl"></i></div>
            <div>
                <p class="text-xs text-gray-400 font-bold">أخبار منشورة</p>
                <h3 class="text-3xl font-black text-navy">{{ $stats['published_news'] }}</h3>
            </div>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 card-hover flex items-center gap-4">
            <div class="bg-gray-100 p-4 rounded-2xl text-gray-500 shrink-0"><i class="fas fa-file-alt text-2xl"></i></div>
            <div>
                <p class="text-xs text-gray-400 font-bold">إجمالي الأخبار</p>
                <h3 class="text-3xl font-black text-navy">{{ $stats['news_count'] }}</h3>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8 mb-8">
        {{-- Left: Upcoming Activities + Recent News --}}
        <div class="xl:col-span-2 space-y-8">
            {{-- Upcoming Activities --}}
            <div class="card-premium overflow-hidden">
                <div class="px-8 py-5 border-b border-gray-50 bg-navy/5 flex justify-between items-center">
                    <h2 class="text-lg font-bold text-navy font-cairo flex items-center gap-2">
                        <i class="fas fa-calendar-days text-gold"></i> الفعاليات القادمة
                    </h2>
                    <a href="{{ route('activities.index') }}" class="text-gold text-sm font-bold font-cairo hover:underline">عرض الكل</a>
                </div>
                <div class="divide-y divide-gray-50">
                    @if($upcoming_activities->count() > 0)
                        @foreach($upcoming_activities as $activity)
                            @php /** @var \App\Models\Activity $activity */ @endphp
                            <div class="flex items-center gap-4 px-8 py-4 hover:bg-gray-50/50 transition-colors">
                                <div class="w-12 h-12 bg-navy/5 rounded-xl flex items-center justify-center text-navy shrink-0">
                                    <i class="fas fa-flag"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-bold text-gray-800 font-cairo truncate">{{ $activity->name }}</p>
                                    <p class="text-xs text-gray-400 font-almarai">{{ $activity->club->name ?? 'نادي عام' }} • {{ $activity->location }}</p>
                                </div>
                                <div class="text-left shrink-0">
                                    <p class="text-xs   text-navy font-bold">{{ $activity->start_date?->format('d/m') ?? '--' }}</p>
                                    <p class="text-[10px] text-gray-400">{{ $activity->participants->count() }} مشارك</p>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="px-8 py-12 text-center text-gray-400 font-almarai">
                            <i class="fas fa-calendar-xmark text-4xl mb-3 opacity-30"></i>
                            <p>لا توجد فعاليات قادمة</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Recent News --}}
            <div class="card-premium overflow-hidden">
                <div class="px-8 py-5 border-b border-gray-50 bg-emerald-50/50 flex justify-between items-center">
                    <h2 class="text-lg font-bold text-navy font-cairo flex items-center gap-2">
                        <i class="fas fa-newspaper text-emerald-500"></i> آخر الأخبار المنشورة
                    </h2>
                    @can('manage-news')
                        <a href="{{ route('news.index') }}" class="text-emerald-600 text-sm font-bold font-cairo hover:underline">إدارة الأخبار</a>
                    @endcan
                </div>
                <div class="divide-y divide-gray-50">
                    @if($recent_news->count() > 0)
                        @foreach($recent_news as $item)
                            @php /** @var \App\Models\News $item */ @endphp
                            <div class="flex items-center gap-4 px-8 py-4 hover:bg-gray-50/50 transition-colors">
                                @if($item->cover_image)
                                    <img src="{{ asset('storage/'.$item->cover_image) }}" class="w-14 h-14 rounded-xl object-cover shrink-0">
                                @else
                                    <div class="w-14 h-14 bg-emerald-100 rounded-xl flex items-center justify-center text-emerald-500 shrink-0">
                                        <i class="fas fa-image text-xl"></i>
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <p class="font-bold text-gray-800 font-cairo truncate">{{ $item->title }}</p>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-[10px] px-2 py-0.5 rounded-full font-bold {{ $item->is_published ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                                            {{ $item->is_published ? 'منشور' : 'مسودة' }}
                                        </span>
                                        <span class="text-[10px] text-gray-400">{{ $item->getCategoryLabel() }}</span>
                                    </div>
                                </div>
                                <a href="{{ route('news.show', $item) }}" class="text-gray-300 hover:text-navy transition-colors">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </div>
                        @endforeach
                    @else
                        <div class="px-8 py-12 text-center text-gray-400 font-almarai">
                            <i class="fas fa-newspaper text-4xl mb-3 opacity-30"></i>
                            <p>لا توجد أخبار حتى الآن</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right: Quick Actions --}}
        <div class="space-y-6">
            <div class="card-premium p-7">
                <h2 class="text-lg font-bold text-navy mb-5 font-cairo">إجراءات سريعة</h2>
                <div class="space-y-3">
                    @can('manage-news')
                        <a href="{{ route('news.create') }}"
                            class="flex items-center p-4 bg-emerald-600 text-white rounded-2xl hover:bg-emerald-700 transition-all group shadow-lg shadow-emerald-600/20">
                            <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center ml-4">
                                <i class="fas fa-plus text-xl"></i>
                            </div>
                            <span class="font-bold font-cairo">نشر خبر جديد</span>
                        </a>
                    @endcan

                    <a href="{{ route('activities.index') }}"
                        class="flex items-center p-4 bg-navy/5 rounded-2xl hover:bg-navy hover:text-white transition-all group">
                        <div class="w-10 h-10 bg-white shadow-sm rounded-xl flex items-center justify-center text-navy ml-4 group-hover:scale-110 transition-transform">
                            <i class="fas fa-calendar-plus text-xl"></i>
                        </div>
                        <span class="font-bold font-cairo">جدولة فعالية</span>
                    </a>

                    <a href="{{ route('clubs.index') }}"
                        class="flex items-center p-4 bg-navy/5 rounded-2xl hover:bg-navy hover:text-white transition-all group">
                        <div class="w-10 h-10 bg-white shadow-sm rounded-xl flex items-center justify-center text-navy ml-4 group-hover:scale-110 transition-transform">
                            <i class="fas fa-users-rectangle text-xl"></i>
                        </div>
                        <span class="font-bold font-cairo">إدارة الأندية</span>
                    </a>

                    @can('manage-news')
                        <a href="{{ route('news.index') }}"
                            class="flex items-center p-4 bg-gold/5 rounded-2xl hover:bg-gold hover:text-navy transition-all group">
                            <div class="w-10 h-10 bg-white shadow-sm rounded-xl flex items-center justify-center text-gold ml-4 group-hover:scale-110 transition-transform">
                                <i class="fas fa-newspaper text-xl"></i>
                            </div>
                            <span class="font-bold font-cairo">إدارة الأخبار والإعلانات</span>
                        </a>
                    @endcan
                </div>
            </div>

            {{-- Tips --}}
          
        </div> 
         <div class="bg-gradient-to-br from-navy to-blue-900 rounded-2xl p-6 text-black/90 shadow-lg shadow-navy/20">
                <i class="fas fa-lightbulb text-gold text-2xl mb-3"></i>
                <h3 class="font-bold font-cairo mb-2 text-gold">تلميح</h3>
                <p class=" text-sm font-bold leading-relaxed">
                    الأخبار التي تنشرها ستظهر تلقائياً في شريط الأخبار أسفل صفحة تسجيل الدخول ليطلع عليها جميع المستخدمين.
                </p>
            </div>
    </div>
@endsection
