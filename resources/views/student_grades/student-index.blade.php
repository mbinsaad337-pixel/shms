@extends('layouts.app')
@section('title', 'بيانات درجاتي')

@section('content')
<div class="py-12 bg-gray-50/50 min-h-screen" dir="rtl">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('student.dashboard') }}" class="w-10 h-10 bg-white rounded-full shadow-sm flex items-center justify-center text-gray-400 hover:text-navy transition-all border border-gray-100">
                    <i class="fas fa-arrow-right"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-black text-gray-900 font-cairo">بيانات درجاتي الجامعية</h1>
                    <p class="mt-2 text-sm text-gray-500 font-almarai">يمكنك هنا رفع صور بيان الدرجات الخاص بك لكل فصل أو سنة دراسية.</p>
                </div>
            </div>
            <a href="{{ route('student-grades.create') }}" 
                class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-bold rounded-2xl text-white bg-primary hover:bg-primary/90 shadow-xl shadow-primary/20 transition-all transform hover:-translate-y-0.5">
                <i class="fas fa-plus-circle ml-2"></i>
                إضافة بيان جديد
            </a>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-700 font-almarai flex items-center gap-3">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
        @endif

        @if($grades->isEmpty())
            <div class="bg-white rounded-3xl p-12 text-center border border-gray-100 shadow-sm mt-10">
                <div class="w-20 h-20 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-4 text-gray-300">
                    <i class="fas fa-file-invoice-dollar text-4xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 font-cairo">لا توجد بيانات درجات مرفوعة</h3>
                <p class="text-gray-500 font-almarai mt-2">ابدأ برفع أول بيان درجات لك بالنقر على الزر أعلاه.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($grades as $grade)
                    <div class="bg-white rounded-3xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-md transition-shadow group">
                        <div class="aspect-video bg-gray-100 relative overflow-hidden">
                            <img src="{{ asset('storage/' . $grade->file_path) }}" 
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" 
                                alt="بيان درجات">
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-4">
                                <a href="{{ asset('storage/' . $grade->file_path) }}" target="_blank"
                                    class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-primary shadow-sm hover:bg-primary hover:text-white transition-all">
                                    <i class="fas fa-eye"></i>
                                </a>
                                {{-- No delete button for students --}}
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-2">
                                <span class="px-3 py-1 bg-primary/10 text-primary rounded-lg text-xs font-bold font-cairo">
                                     {{ $grade->semester }}
                                 </span>
                                 @if($grade->gpa_percentage)
                                     <span class="text-emerald-500   font-bold">{{ number_format($grade->gpa_percentage, 2) }}%</span>
                                 @endif
                            </div>
                            <h4 class="font-bold text-gray-800 font-cairo">{{ $grade->academic_year ?? 'السنة الدراسية غير محددة' }}</h4>
                            @if($grade->notes)
                                <p class="text-sm text-gray-500 font-almarai mt-2 line-clamp-2 italic">" {{ $grade->notes }} "</p>
                            @endif
                            
                            <div class="mt-4 pt-4 border-t border-gray-50 flex items-center gap-2 text-[10px] text-amber-600 font-almarai">
                                <i class="fas fa-info-circle"></i>
                                <span>لا يمكنك حذف هذا البيان بعد الرفع، تواصل مع الإدارة للتعديل.</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

    </div>
</div>
@endsection
