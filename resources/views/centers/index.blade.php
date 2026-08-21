@extends('layouts.app')

@section('title', 'إدارة المراكز الطلابية')

@section('content')
    <section class="font-almarai" dir="rtl" aria-labelledby="centers-heading">
        <header class="mb-7 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <div class="mb-2 flex items-center gap-2 text-xs font-bold text-gold font-cairo">
                    <span class="h-px w-7 bg-gold"></span>
                    الإدارة المركزية
                </div>
                <h1 id="centers-heading" class="text-2xl font-black text-navy font-cairo md:text-3xl">المراكز الطلابية</h1>
                <p class="mt-2 text-sm leading-6 text-gray-500">نظرة سريعة على المراكز  الطلابية والموارد التابعة لها.</p>
            </div>

            <a href="{{ route('centers.create') }}"
                class="inline-flex items-center justify-center  rounded-xl bg-navy px-2 py-5 text-sm font-bold text-white shadow-lg shadow-navy/15 transition-all hover:-translate-y-0.5 hover:bg-navy/90 focus:outline-none focus:ring-4 focus:ring-navy/20 font-cairo">
                <i class="fas fa-plus-circle text-gold" aria-hidden="true"></i>
              <p>إضافة مركز جديد</p>
            </a>
        </header>
        <br>

        <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">
            @forelse($centers as $center)
                <article class="group relative flex min-w-0 flex-col overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-gold/35 hover:shadow-xl hover:shadow-navy/10">
                    <div class="h-1 w-full bg-navy"></div>

                    <div class="flex flex-1 flex-col p-5">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex min-w-0 items-center gap-3">
                                <div class="flex h-14 w-14 flex-shrink-0 items-center justify-center overflow-hidden rounded-xl border border-navy/10 bg-gray-50 shadow-sm">
                                    <img class="h-full w-full object-cover"
                                        src="{{ $center->logo ? asset('storage/' . $center->logo) : null }}"
                                        alt="شعار {{ $center->name }}">
                                </div>
                                <div class="min-w-0">
                                    <h2 class="truncate text-base font-black text-navy font-cairo" title="{{ $center->name }}">{{ $center->name }}</h2>
                                    <p class="mt-1 truncate text-xs text-gray-500" dir="ltr" title="{{ $center->email }}">{{ $center->email }}</p>
                                </div>
                            </div>

                            @if($center->is_active)
                                <span class="inline-flex flex-shrink-0 items-center gap-1.5 rounded-lg border border-emerald-100 bg-emerald-50 px-2.5 py-1.5 text-[11px] font-bold text-emerald-700">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500" aria-hidden="true"></span>
                                    نشط
                                </span>
                            @else
                                <span class="inline-flex flex-shrink-0 items-center gap-1.5 rounded-lg border border-rose-100 bg-rose-50 px-2.5 py-1.5 text-[11px] font-bold text-rose-700">
                                    <span class="h-1.5 w-1.5 rounded-full bg-rose-500" aria-hidden="true"></span>
                                    غير نشط
                                </span>
                            @endif
                        </div>

                        <div class="mt-5 flex min-h-[2.75rem] items-start gap-2 border-r-2 border-gold/70 pr-3 text-sm leading-6 text-gray-600">
                            <i class="fas fa-location-dot mt-1 text-xs text-gold" aria-hidden="true"></i>
                            <p class="line-clamp-2">{{ $center->address ?: 'لم يُسجّل عنوان المركز بعد' }}</p>
                        </div>

                        <dl class="mt-5 grid grid-cols-3 divide-x divide-x-reverse divide-gray-100 border-y border-gray-100 bg-gray-50/70 py-3">
                            <div class="px-2 text-center">
                                <dt class="text-[11px] text-gray-500">الساكنين</dt>
                                <dd class="mt-1 text-lg font-black tabular-nums text-navy font-cairo">{{ $center->residents_count }}</dd>
                            </div>
                            <div class="px-2 text-center">
                                <dt class="text-[11px] text-gray-500">الغرف</dt>
                                <dd class="mt-1 text-lg font-black tabular-nums text-navy font-cairo">{{ $center->rooms_count }}</dd>
                            </div>
                            <div class="px-2 text-center">
                                <dt class="text-[11px] text-gray-500">الطاقم الإداري</dt>
                                <dd class="mt-1 text-lg font-black tabular-nums text-navy font-cairo">{{ $center->staff_count }}</dd>
                            </div>
                        </dl>

                        <footer class="mt-5 grid grid-cols-2 gap-3">
                            <a href="{{ route('centers.show', $center) }}"
                                class="inline-flex items-center justify-center gap-2 rounded-xl border border-navy/15 bg-white px-3 py-2.5 text-sm font-bold text-navy transition-colors hover:border-navy hover:bg-navy/5 focus:outline-none focus:ring-4 focus:ring-navy/10 font-cairo">
                                <i class="fas fa-eye text-gold" aria-hidden="true"></i>
                                عرض
                            </a>
                            <a href="{{ route('centers.edit', $center) }}"
                                class="inline-flex items-center justify-center gap-2 rounded-xl bg-gold px-3 py-2.5 text-sm font-bold text-navy shadow-sm transition-colors hover:bg-gold/85 focus:outline-none focus:ring-4 focus:ring-gold/25 font-cairo">
                                <i class="fas fa-pen-to-square" aria-hidden="true"></i>
                                تعديل
                            </a>
                        </footer>
                    </div>
                </article>
            @empty
                <div class="col-span-full rounded-2xl border border-dashed border-navy/20 bg-white px-6 py-16 text-center shadow-sm">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-navy/5 text-2xl text-gold">
                        <i class="fas fa-building-columns" aria-hidden="true"></i>
                    </div>
                    <h2 class="mt-5 text-xl font-black text-navy font-cairo">لا توجد مراكز طلابية حتى الآن</h2>
                    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-gray-500">ابدأ بإضافة أول مركز لتتمكن من متابعة الطلاب والغرف والموظفين من مكان واحد.</p>
                    <a href="{{ route('centers.create') }}"
                        class="mt-6 inline-flex items-center justify-center gap-2 rounded-xl bg-navy px-5 py-3 text-sm font-bold text-white shadow-lg shadow-navy/15 transition-colors hover:bg-navy/90 focus:outline-none focus:ring-4 focus:ring-navy/20 font-cairo">
                        <i class="fas fa-plus-circle text-gold" aria-hidden="true"></i>
                        إضافة مركز جديد
                    </a>
                </div>
            @endforelse
        </div>
    </section>
@endsection
