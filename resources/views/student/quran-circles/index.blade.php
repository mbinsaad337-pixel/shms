@extends('layouts.app')

@section('title', 'حلقاتي القرآنية')

@section('content')
    <div
        class="mb-8 flex flex-col md:flex-row justify-between items-center bg-white p-6 rounded-2xl border-l-8 border-gold shadow-sm">
        <div class="flex items-center gap-4">
            <a href="{{ route('student.dashboard') }}" class="w-10 h-10 bg-white rounded-full shadow-sm flex items-center justify-center text-gray-400 hover:text-navy transition-all border border-gray-100">
                <i class="fas fa-arrow-right"></i>
            </a>
            <div>
                <h1 class="text-3xl font-black text-navy font-cairo lowercase">حلقاتي القرآنية</h1>
                <p class="text-gray-400 font-almarai text-sm mt-1">متابعة إحصائيات الحضور والتقدم في حفظ القرآن</p>
            </div>
        </div>
        <div class="mt-4 md:mt-0">
            <div class="h-10 w-10 flex items-center justify-center bg-navy/5 text-navy rounded-full shadow-inner">
                <i class="fas fa-quran text-gold"></i>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @if(is_countable($circles) ? count($circles) > 0 : (method_exists($circles, 'count') ? $circles->count() > 0 : !empty($circles)))
    @foreach($circles as $circle)
            <div
                class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden relative group hover:shadow-xl transition-all duration-300">
                <div
                    class="absolute -right-6 -top-6 w-32 h-32 bg-gold/5 rounded-full group-hover:scale-110 transition-transform">
                </div>

                <div class="p-8">
                    <div class="flex justify-between items-start mb-6">
                        <div class="bg-navy p-3 rounded-2xl shadow-md text-gold">
                            <i class="fas fa-book-open text-2xl"></i>
                        </div>
                        <div class="flex gap-2">
                            <span class="px-3 py-1 bg-navy/5 text-navy text-[10px] font-bold rounded-lg font-cairo uppercase">
                                طالب
                            </span>
                            <span class="px-3 py-1 bg-gold/10 text-gold text-[10px] font-bold rounded-lg font-cairo uppercase">
                                {{ $circle->type == 'memorization' ? 'تحفيظ' : 'تلاوة' }}
                            </span>
                        </div>
                    </div>

                    <h3 class="text-2xl font-black text-navy font-cairo mb-2">{{ $circle->name }}</h3>
                    <p class="text-sm text-gray-500 font-almarai mb-6 flex items-center gap-2">
                        <i class="fas fa-user-circle text-navy opacity-30"></i>
                        المدرس: <span class="font-bold text-navy opacity-80">{{ $circle->teacher->name }}</span>
                    </p>

                    <div class="grid grid-cols-2 gap-4 mb-8">
                        <div class="bg-navy p-4 rounded-2xl text-center relative shadow-sm hover:shadow-md transition-shadow">
                            <div class="text-2xl font-black text-gold font-cairo mb-1">{{ $circle->attendanceCount }}</div>
                            <div class="text-[10px] text-white/60 font-almarai uppercase tracking-widest">إجمالي الحضور</div>
                        </div>
                        <div
                            class="bg-gray-50 p-4 rounded-2xl text-center relative shadow-sm group-hover:bg-red-50 transition-colors">
                            <div
                                class="text-2xl font-black text-gray-400 font-cairo mb-1 group-hover:text-red-400 transition-colors">
                                {{ $circle->absenceCount }}</div>
                            <div
                                class="text-[10px] text-gray-400 font-almarai uppercase tracking-widest group-hover:text-red-300 transition-colors">
                                مرات الغياب</div>
                        </div>
                    </div>

                    <div class="bg-gray-50/50 p-6 rounded-2xl border border-gray-50">
                        <h4
                            class="text-xs font-bold text-gray-400 font-cairo uppercase tracking-widest mb-4 flex items-center gap-2">
                            <i class="fas fa-star text-gold text-[10px]"></i>
                            آخر تقدم مسجل
                        </h4>
                        <div class="flex items-center gap-4">
                            <div class="h-12 w-12 bg-white rounded-xl shadow-sm flex items-center justify-center text-navy">
                                <i class="fas fa-bookmark"></i>
                            </div>
                            <div>
                                @if($circle->pivot->last_sura)
                                    <div class="text-sm font-bold text-navy font-cairo">سورة {{ $circle->pivot->last_sura }}</div>
                                    <div class="text-[10px] text-gray-400 font-almarai">الآية رقم: <span
                                            class="text-gold font-bold">{{ $circle->pivot->last_verse }}</span></div>
                                @else
                                    <div class="text-sm font-bold text-gray-300 font-cairo italic">لم يبدأ بعد</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="px-8 py-4 bg-navy flex items-center justify-between group-hover:bg-[#0c1f3d] transition-colors">
                    <span class="text-[10px] text-white/50 font-almarai italic">تاريخ الانضمام:
                        {{ $circle->pivot->created_at->format('Y-m-d') }}</span>
                    <i class="fas fa-chevron-left text-gold/50 group-hover:translate-x-[-4px] transition-transform"></i>
                </div>
            </div>
            @endforeach
@else
            <div class="col-span-full py-20 text-center bg-white rounded-3xl border-2 border-dashed border-gray-200">
                <i class="fas fa-search text-gray-100 text-6xl mb-4"></i>
                <h3 class="text-xl font-bold text-gray-400 font-cairo">لم يتم تسجيلك في أي حلقة قرآنية بعد</h3>
                <p class="text-gray-400 text-sm font-almarai mt-2">يرجى مراجعة مشرف المركز للانضمام إلى المجموعات المتاحة</p>
            </div>
        @endif
    </div>
@endsection
