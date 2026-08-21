@extends ('layouts.app')
@php
    /** @var \App\Models\StudentAchievement[] $achievements */
@endphp
@section ('title', 'إنجازاتي الشخصية')

@section ('content')
<div class="py-12 bg-gray-50/50 min-h-screen" dir="rtl">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="mb-10">
            <div class="flex items-center gap-4 mb-4">
                <a href="{{ route('student.dashboard') }}" class="w-10 h-10 bg-white rounded-full shadow-sm flex items-center justify-center text-gray-400 hover:text-navy transition-all border border-gray-100">
                    <i class="fas fa-arrow-right"></i>
                </a>
                <h1 class="text-3xl font-black text-gray-900 font-cairo">إنجازاتي الشخصية</h1>
            </div>
            <p class="mt-2 text-sm text-gray-500 font-almarai mr-14">قائمة بالإنجازات والجوائز التي تم تسجيلها لك من قبل إدارة المركز.</p>
        </div>

        @if ($achievements->isEmpty())
            <div class="bg-white rounded-3xl p-16 text-center border border-gray-100 shadow-sm">
                <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6 text-gray-300">
                    <i class="fas fa-trophy text-5xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 font-cairo">لا توجد إنجازات مسجلة بعد</h3>
                <p class="text-gray-500 font-almarai mt-2">تواصل مع إدارة المركز لتسجيل إنجازاتك الجديدة هنا.</p>
            </div>
        @else
            <div class="space-y-6">
                @foreach ($achievements as $achievement)
                    <div class="bg-white rounded-3xl overflow-hidden border border-gray-100 shadow-sm flex flex-col md:flex-row gap-6 p-6 hover:shadow-md transition-shadow">
                        @if ($achievement->certificate_file)
                            <div class="w-full md:w-48 h-48 rounded-2xl overflow-hidden border border-gray-50 flex-shrink-0 relative group">
                                <img src="{{ asset('storage/' . $achievement->certificate_file) }}" 
                                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" 
                                    alt="شهادة الإنجاز">
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                    <a href="{{ asset('storage/' . $achievement->certificate_file) }}" target="_blank"
                                        class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-primary shadow-sm hover:bg-primary hover:text-white transition-all">
                                        <i class="fas fa-search-plus"></i>
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="w-full md:w-48 h-48 rounded-2xl bg-gray-50 flex items-center justify-center text-gray-200 flex-shrink-0">
                                <i class="fas fa-award text-6xl"></i>
                            </div>
                        @endif
                        
                        <div class="flex-1 flex flex-col justify-between py-2">
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="text-xl font-bold text-gray-800 font-cairo">{{ $achievement->title }}</h4>
                                    <span class="px-3 py-1 bg-amber-50 text-amber-600 rounded-lg text-[10px] font-bold font-cairo">
                                        {{ $achievement->achievement_date?->format('Y/m/d') ?? 'تاريخ غير محدد' }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600 font-almarai leading-relaxed whitespace-pre-line">
                                    {{ $achievement->description }}
                                </p>
                            </div>
                            
                            <div class="mt-4 pt-4 border-t border-gray-50 flex items-center gap-2 text-[10px] text-gray-400 font-almarai">
                                <i class="fas fa-lock"></i>
                                <span>هذا السجل للعرض فقط - الإضافة والحذف متم بموافقة إدارة المركز.</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

    </div>
</div>
@endsection
