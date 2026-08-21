@extends('layouts.app')

@section('title', 'إدارة الصناديق المالية')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 font-cairo">الصناديق المالية</h1>
                <p class="text-gray-600 font-almarai mt-2">إدارة الحسابات والصناديق التابعة للمركز</p>
            </div>
            @if(auth()->user()->hasRole('super-admin'))
            <a href="{{ route('funds.create') }}"
                class="btn-primary flex items-center gap-2 px-6 py-3 rounded-2xl shadow-lg transform hover:-translate-y-1 transition-all">
                <i class="fas fa-plus"></i>
                <span>إضافة صندوق جديد</span>
            </a>
            @endif
        </div>

        @if(auth()->user()->hasRole('super-admin'))
        <form action="{{ route('funds.index') }}" method="GET" class="mb-8 flex flex-wrap items-end gap-3 rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
            <div>
                <label class="mb-1 block text-xs font-bold text-gray-500 font-cairo text-right">اسم الصندوق</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث بالاسم..."
                    class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm font-cairo outline-none focus:border-gold focus:bg-white text-right">
            </div>
            <div>
                <label class="mb-1 block text-xs font-bold text-gray-500 font-cairo text-right">المركز</label>
                <select name="center_id" class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm font-cairo outline-none focus:border-gold focus:bg-white min-w-[150px]">
                    <option value="">كل المراكز</option>
                    @foreach($centers as $center)
                        <option value="{{ $center->id }}" {{ request('center_id') == $center->id ? 'selected' : '' }}>{{ $center->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="rounded-xl bg-navy px-5 py-2.5 text-sm font-bold text-white transition-colors hover:bg-navy/90 font-cairo mr-auto">
                <i class="fas fa-filter ml-1"></i> تصفية
            </button>
            @if(request()->filled('search') || request()->filled('center_id'))
                <a href="{{ route('funds.index') }}" class="px-3 py-2.5 text-sm font-bold text-gray-500 hover:text-navy font-cairo">إلغاء الفلترة</a>
            @endif
        </form>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @if(is_countable($funds) ? count($funds) > 0 : (method_exists($funds, 'count') ? $funds->count() > 0 : !empty($funds)))
    @foreach($funds as $fund)
                <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8 card-hover relative group">
                    <div class="flex justify-between items-start mb-6">
                        <div class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center text-primary shadow-sm">
                            <i class="fas fa-vault text-2xl"></i>
                        </div>
                        <div class="flex gap-3">
                            @if(auth()->user()->hasRole('super-admin'))
                            <a href="{{ route('funds.edit', $fund) }}"
                                class="flex items-center gap-2 px-4 py-2 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white rounded-xl font-bold font-cairo transition-all text-sm">
                                <i class="fas fa-edit"></i>
                                <span>تعديل</span>
                            </a>
                            @if(!$fund->is_system)
                                <form action="{{ route('funds.destroy', $fund) }}" method="POST"
                                    data-confirm="هل أنت متأكد من حذف هذا الصندوق؟ لا يمكن التراجع عن هذا الإجراء.">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="flex items-center gap-2 px-4 py-2 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white rounded-xl font-bold font-cairo transition-all text-sm">
                                        <i class="fas fa-trash"></i>
                                        <span>حذف</span>
                                    </button>
                                </form>
                            @endif
                            @endif
                        </div>
                    </div>

                    <div class="mb-6">
                        <h3 class="text-2xl font-bold text-gray-800 font-cairo mb-2">{{ $fund->name }}</h3>
                        <p class="text-gray-500 text-sm font-almarai leading-relaxed">
                            {{ $fund->description ?? 'لا يوجد وصف مضاف لهذا الصندوق' }}</p>
                        <span
                            class="inline-block mt-3 px-3 py-1 rounded-full text-xs font-bold font-cairo bg-emerald-50 text-emerald-700 border border-emerald-100">
                            {{ $fund->currency_label }}
                        </span>
                    </div>

                    <div class="mt-auto pt-6 border-t border-gray-50 flex justify-between items-end">
                        <div>
                            <p class="text-gray-400 text-xs font-almarai mb-1">الرصيد المتوفر</p>
                            <p class="text-3xl font-black text-primary tracking-tight">
                                {{ number_format($fund->balance, 2) }}
                                <span class="text-sm font-normal text-gray-400 mr-1">{{ $fund->currency_symbol }}</span>
                            </p>
                        </div>
                        @if(auth()->user()->hasRole('super-admin'))
                            <div class="flex items-center gap-2 bg-blue-50 px-3 py-1.5 rounded-xl">
                                <i class="fas fa-building text-blue-400 text-xs"></i>
                                <span class="text-xs text-blue-700 font-bold">{{ $fund->center->name ?? 'غير محدد' }}</span>
                            </div>
                        @endif
                        @if($fund->is_system)
                            <span
                                class="bg-gray-100 text-gray-500 text-[10px] px-3 py-1.5 rounded-full font-bold uppercase tracking-wider">نظام</span>
                        @endif
                    </div>
                </div>
                @endforeach
@else
                <div class="col-span-full bg-white rounded-3xl p-16 text-center border-2 border-dashed border-gray-100">
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-wallet text-3xl text-gray-200"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-400 font-cairo">لا توجد صناديق مضافة</h3>
                    <p class="text-gray-300 font-almarai mt-2">ابدأ بإضافة أول صندوق مالي للمراكز الطلابية</p>
                </div>
            @endif
        </div>
    </div>
@endsection
