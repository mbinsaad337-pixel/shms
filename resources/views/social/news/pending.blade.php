@extends('layouts.app')
@section('title', 'لوحة اعتمادات مسؤول الإعلام')

@section('content')
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-6 rounded-3xl border-l-8 border-gold shadow-sm">
        <div>
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-navy/10 rounded-2xl flex items-center justify-center text-navy shadow-sm">
                    <i class="fas fa-bullhorn text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl md:text-3xl font-black text-navy font-cairo">لوحة اعتمادات مسؤول الإعلام</h1>
                    <p class="text-gray-400 font-almarai text-sm mt-0.5">مراجعة والبت في الإعلانات والأخبار الواردة من المراكز الطلابية قبل نشرها</p>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('news.index') }}" class="px-5 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold font-cairo rounded-2xl transition-all text-sm flex items-center gap-2">
                <i class="fas fa-newspaper"></i> جميع الأخبار المنشورة
            </a>
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
        <div class="bg-amber-500/10 border border-amber-500/20 rounded-3xl p-6 flex items-center gap-5">
            <div class="w-14 h-14 bg-amber-500 text-white rounded-2xl flex items-center justify-center text-2xl shadow-lg shadow-amber-500/20">
                <i class="fas fa-clock animate-pulse"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-amber-700 font-cairo">بانتظار الاعتماد</p>
                <h3 class="text-3xl font-black text-amber-900 font-cairo mt-1">{{ $pendingNews->total() }}</h3>
            </div>
        </div>

        <div class="bg-emerald-500/10 border border-emerald-500/20 rounded-3xl p-6 flex items-center gap-5">
            <div class="w-14 h-14 bg-emerald-500 text-white rounded-2xl flex items-center justify-center text-2xl shadow-lg shadow-emerald-500/20">
                <i class="fas fa-check-circle"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-emerald-700 font-cairo">الإعلانات المعتمدة</p>
                <h3 class="text-3xl font-black text-emerald-900 font-cairo mt-1">{{ $approvedCount }}</h3>
            </div>
        </div>

        <div class="bg-rose-500/10 border border-rose-500/20 rounded-3xl p-6 flex items-center gap-5">
            <div class="w-14 h-14 bg-rose-500 text-white rounded-2xl flex items-center justify-center text-2xl shadow-lg shadow-rose-500/20">
                <i class="fas fa-times-circle"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-rose-700 font-cairo">الإعلانات المرفوضة</p>
                <h3 class="text-3xl font-black text-rose-900 font-cairo mt-1">{{ $rejectedCount }}</h3>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl font-cairo font-bold flex items-center gap-3">
            <i class="fas fa-check-circle text-xl"></i> {{ session('success') }}
        </div>
    @endif

    @if($pendingNews->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($pendingNews as $item)
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden flex flex-col hover:shadow-md transition-all group relative">
                    {{-- Status Badge --}}
                    <div class="absolute top-4 right-4 z-10">
                        <span class="px-3 py-1 rounded-full text-xs font-bold font-cairo shadow-sm bg-amber-500 text-white flex items-center gap-1.5">
                            <i class="fas fa-hourglass-half text-xs"></i> قيد الانتظار
                        </span>
                    </div>

                    {{-- Cover image / placeholder --}}
                    <div class="h-48 bg-gray-100 relative overflow-hidden shrink-0">
                        @if($item->cover_image)
                            <img src="{{ asset('storage/' . $item->cover_image) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @else
                            <div class="w-full h-full flex flex-col items-center justify-center text-gray-300 bg-navy/5">
                                <i class="fas fa-newspaper text-5xl mb-2"></i>
                                <span class="text-xs font-cairo text-gray-400">لا توجد صورة غلاف</span>
                            </div>
                        @endif

                        @if($item->video_url || $item->video_path)
                            <div class="absolute bottom-3 left-3 bg-black/60 backdrop-blur-md text-white text-[11px] font-bold font-cairo px-2.5 py-1 rounded-lg flex items-center gap-1.5">
                                <i class="fas fa-video text-rose-400"></i> يحتوي على فيديو
                            </div>
                        @endif
                    </div>

                    {{-- Card Body --}}
                    <div class="p-6 flex-1 flex flex-col">
                        <div class="flex items-center justify-between text-xs text-gray-400 font-almarai mb-3">
                            <span class="font-bold text-navy bg-navy/5 px-2.5 py-1 rounded-xl">
                                <i class="fas fa-building text-gold ml-1"></i> {{ $item->center ? $item->center->name : 'غير محدد' }}
                            </span>
                            <span>{{ $item->created_at->diffForHumans() }}</span>
                        </div>

                        <h3 class="text-lg font-black text-navy font-cairo mb-2 line-clamp-2 leading-snug">{{ $item->title }}</h3>
                        <p class="text-gray-500 font-almarai text-sm line-clamp-3 mb-4 flex-1">{{ strip_tags($item->body) }}</p>

                        {{-- Submitter Details --}}
                        <div class="bg-gray-50 rounded-2xl p-3.5 mb-5 flex items-center justify-between border border-gray-100">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full bg-navy text-gold flex items-center justify-center font-bold text-xs">
                                    <i class="fas fa-user-pen"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-navy font-cairo">{{ $item->creator ? $item->creator->name : 'مستخدم' }}</p>
                                    <p class="text-[10px] text-gray-400 font-almarai">ناشر الخبر</p>
                                </div>
                            </div>
                            @if($item->creator && $item->creator->phone)
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $item->creator->phone) }}?text={{ urlencode('مرحباً بك، بخصوص إعلانك: ' . $item->title) }}" 
                                   target="_blank" title="تواصل عبر واتساب" class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white transition-all flex items-center justify-center text-sm">
                                    <i class="fab fa-whatsapp"></i>
                                </a>
                            @endif
                        </div>

                        {{-- Action Buttons --}}
                        <div class="space-y-2 pt-2 border-t border-gray-50">
                            <a href="{{ route('news.show', $item) }}" target="_blank"
                               class="w-full py-2.5 bg-navy/5 text-navy hover:bg-navy hover:text-white rounded-xl font-bold font-cairo text-sm transition-all flex items-center justify-center gap-2">
                                <i class="fas fa-eye"></i> معاينة المادة الكاملة
                            </a>

                            <div class="grid grid-cols-2 gap-2">
                                <form action="{{ route('news.approve', $item) }}" method="POST" class="w-full">
                                    @csrf
                                    <button type="submit" onclick="return confirm('هل ترغب في اعتماد ونشر هذا الإعلان للجميع؟')"
                                            class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold font-cairo text-sm shadow-md shadow-emerald-600/20 transition-all flex items-center justify-center gap-1.5">
                                        <i class="fas fa-check"></i> اعتماد ونشر
                                    </button>
                                </form>

                                <button type="button" x-data="" @click="$dispatch('open-reject-modal-{{ $item->id }}')"
                                        class="w-full py-2.5 bg-rose-50 hover:bg-rose-500 text-rose-600 hover:text-white rounded-xl font-bold font-cairo text-sm transition-all flex items-center justify-center gap-1.5">
                                    <i class="fas fa-times"></i> رفض الإعلان
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Reject Modal --}}
                    <div x-data="{ open: false }" @open-reject-modal-{{ $item->id }}.window="open = true" x-show="open" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="open = false"></div>
                        <div class="relative min-h-screen flex items-center justify-center p-4">
                            <div class="relative bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl space-y-4">
                                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                                    <h4 class="font-black text-rose-600 font-cairo text-lg flex items-center gap-2">
                                        <i class="fas fa-times-circle"></i> رفض الإعلان
                                    </h4>
                                    <button @click="open = false" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
                                </div>
                                <form action="{{ route('news.reject', $item) }}" method="POST" class="space-y-4">
                                    @csrf
                                    <div>
                                        <label class="block text-xs font-bold text-navy mb-2 font-cairo">سبب الرفض (اختياري)</label>
                                        <textarea name="reason" rows="4" placeholder="اكتب سبب الرفض هنا ليتضح لمدير المركز..." class="w-full p-3 rounded-2xl border border-gray-200 text-sm font-almarai focus:border-rose-500 outline-none"></textarea>
                                    </div>
                                    <div class="flex gap-3">
                                        <button type="submit" class="flex-1 bg-rose-600 hover:bg-rose-700 text-white py-3 rounded-2xl font-bold font-cairo text-sm shadow-lg shadow-rose-600/20">تأكيد الرفض</button>
                                        <button type="button" @click="open = false" class="px-5 py-3 bg-gray-100 text-gray-600 rounded-2xl font-bold font-cairo text-sm">إلغاء</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            @endforeach
        </div>

        <div class="mt-8">{{ $pendingNews->links() }}</div>
    @else
        <div class="bg-white rounded-3xl p-16 text-center border-2 border-dashed border-gray-100 shadow-sm">
            <div class="w-20 h-20 bg-emerald-50 rounded-full flex items-center justify-center mx-auto mb-4 text-emerald-500">
                <i class="fas fa-check-double text-3xl"></i>
            </div>
            <h3 class="text-2xl font-black text-navy font-cairo">لا توجد إعلانات معلقة في الانتظار حالياً</h3>
            <p class="text-gray-400 font-almarai mt-2">جميع الإعلانات والأخبار الواردة من المراكز تم مراجعتها والبت فيها.</p>
        </div>
    @endif
@endsection
