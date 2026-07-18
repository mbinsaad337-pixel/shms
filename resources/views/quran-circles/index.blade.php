@extends('layouts.app')

@section('title', 'إدارة الحلقات القرآنية')

@section('content')
    <div class="mb-8 flex justify-between items-center bg-white p-6 rounded-2xl border-l-8 border-gold shadow-sm">
        <div>
            <h1 class="text-3xl font-black text-navy font-cairo">إدارة الحلقات القرآنية</h1>
            <p class="text-gray-400 font-almarai text-sm mt-1">حلقات تلاوة وتحفيظ القرآن الكريم بالمركز</p>
        </div>
        <div class="flex gap-3">
            @can('view-circle-reports')
                <a href="{{ route('quran-circles.stats') }}"
                    class="px-6 py-3 bg-white text-navy border-2 border-navy/10 rounded-xl hover:bg-navy hover:text-white shadow-sm font-cairo font-bold transition-all flex items-center gap-2">
                    <i class="fas fa-chart-line"></i>
                    <span>الإحصائيات والتقارير</span>
                </a>
            @endcan

            @can('manage-quran-circles')
                <a href="{{ route('quran-circles.create') }}"
                    class="px-6 py-3 bg-navy text-white rounded-xl hover:bg-navy/90 shadow-lg font-cairo font-bold transition-all transform hover:-translate-y-1 flex items-center gap-2">
                    <i class="fas fa-plus-circle text-gold"></i>
                    <span>إنشاء حلقة جديدة</span>
                </a>
            @endcan
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-100 border-r-4 border-green-500 text-green-700 font-cairo rounded-xl shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @if(is_countable($circles) ? count($circles) > 0 : (method_exists($circles, 'count') ? $circles->count() > 0 : !empty($circles)))
            @foreach ($circles as $circle)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div class="bg-gold/10 p-3 rounded-xl">
                                <i class="fas fa-quran text-2xl text-gold"></i>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-3 py-1 bg-navy/5 text-navy text-xs font-bold rounded-full font-cairo">
                                    {{ $circle->type == 'memorization' ? 'تحفيظ' : 'تلاوة' }}
                                </span>
                                @can('manage-quran-circles')
                                    <div class="flex items-center gap-1">
                                        <a href="{{ route('quran-circles.edit', $circle) }}" 
                                            class="text-blue-400 hover:text-blue-600 transition-colors p-1" title="تعديل الحلقة">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('quran-circles.destroy', $circle) }}" method="POST"
                                            onsubmit="return confirm('هل أنت متأكد من حذف هذه الحلقة؟ سيتم حذف جميع الجلسات والبيانات المرتبطة بها.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-400 hover:text-red-600 transition-colors p-1"
                                                title="حذف الحلقة">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                @endcan
                            </div>
                        </div>

                        <h3 class="text-xl font-bold text-navy font-cairo mb-2">{{ $circle->name }}</h3>
                        <p class="text-gray-500 text-sm font-almarai mb-4 line-clamp-2">
                            {{ $circle->description ?? 'لا يوجد وصف مضاف لهذه الحلقة.' }}
                        </p>

                        <div class="space-y-3 pt-4 border-t border-gray-50">
                            <div class="flex items-center text-sm text-gray-600 font-almarai">
                                <i class="fas fa-user-tie w-6 text-gold/60 text-center ml-2"></i>
                                <span>المدرس: <span class="font-bold text-navy">{{ $circle->teacher->name }}</span></span>
                            </div>
                            <div class="flex items-center text-sm text-gray-600 font-almarai">
                                <i class="fas fa-users w-6 text-gold/60 text-center ml-2"></i>
                                <span>الطلاب: <span class="font-bold text-navy">{{ $circle->students()->count() }} طالب</span></span>
                            </div>
                            <div class="flex items-center text-sm text-gray-600 font-almarai">
                                <i class="fas fa-calendar-check w-6 text-gold/60 text-center ml-2"></i>
                                <span>آخر جلسة: <span class="font-bold text-navy">{{ $circle->sessions()->latest()->first()?->session_date?->format('Y-m-d') ?? 'لا يوجد' }}</span></span>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-gray-50 flex justify-between gap-3">
                        <a href="{{ route('quran-circles.show', $circle) }}"
                            class="flex-1 bg-navy text-white py-2 rounded-xl text-center font-bold font-cairo text-sm hover:bg-navy/90 transition-all">
                            عرض التفاصيل
                        </a>
                        @if(auth()->id() == $circle->teacher_id || auth()->user()->can('manage-quran-circles'))
                            <a href="{{ route('circle-sessions.create', $circle) }}"
                                class="flex-1 bg-gold text-navy py-2 rounded-xl text-center font-bold font-cairo text-sm hover:bg-gold/90 transition-all">
                                تسجيل جلسة
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        @else
            <div class="col-span-full bg-white p-12 text-center rounded-2xl border-2 border-dashed border-gray-200">
                <i class="fas fa-book-open text-5xl text-gray-200 mb-4"></i>
                <h3 class="text-xl font-bold text-gray-400 font-cairo">لا توجد حلقات قرآنية حالياً</h3>
                @can('manage-quran-circles')
                    <p class="text-gray-400 mt-2 font-almarai">يمكنك البدء بإنشاء أول حلقة تعليمية الآن</p>
                @endcan
            </div>
        @endif
    </div>

    <div class="mt-8">
        {{ $circles->links() }}
    </div>
@endsection
