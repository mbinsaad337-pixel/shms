@extends('layouts.app')

@php
    $preview = $preview ?? false;
    $previewArchive = $previewArchive ?? null;
    $penaltyTypes = [
        'verbal_warning' => 'تنبيه شفوي',
        'written_warning' => 'إنذار كتابي',
        'service_suspension' => 'إيقاف الخدمات',
        'temporary_suspension' => 'إيقاف مؤقت',
        'expulsion' => 'فصل',
    ];
@endphp

@section('title', $preview ? 'معاينة السجل المؤرشف' : 'تفاصيل العقوبة')

@section('content')
    <div class="container mx-auto px-6 py-8" dir="rtl">
        <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-center md:justify-between rounded-3xl border-r-8 border-navy bg-white p-8 shadow-sm">
            <div>
                <h1 class="font-cairo text-3xl font-black text-navy">تفاصيل العقوبة</h1>
                <p class="mt-2 font-almarai text-sm text-gray-400">
                    {{ $preview ? 'معاينة السجل المؤرشف' : 'بيانات العقوبة المسجلة' }}
                </p>
            </div>
            <div class="flex gap-3">
                @if ($preview && $previewArchive)
                <a href="{{ $preview ? route('annual-rollover.index') : route('administrative.index', ['tab' => 'penalties']) }}"
                    class="flex items-center gap-2 rounded-2xl border border-gray-100 bg-gray-50 px-5 py-3 font-cairo font-bold text-navy transition hover:bg-gray-100">
                    <i class="fas fa-arrow-right"></i><span>رجوع</span>
                </a>
                @endif
            </div>
        </div>

        <div class="mx-auto max-w-4xl rounded-[2rem] border border-gray-100 bg-white p-8 shadow-sm md:p-10">
            <div class="mb-8 flex flex-col gap-5 border-b border-gray-100 pb-8 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="mb-2 font-cairo text-sm font-bold text-gray-400">الطالب</p>
                    <h2 class="font-cairo text-2xl font-black text-navy">{{ $penalty->student?->name_ar ?? 'غير محدد' }}</h2>
                </div>
                <span class="w-fit rounded-2xl bg-orange-50 px-5 py-3 font-cairo font-black text-orange-700">
                    {{ $penaltyTypes[$penalty->type] ?? $penalty->type }}
                </span>
            </div>

            <dl class="grid grid-cols-1 gap-6 font-cairo sm:grid-cols-2">
                <div class="rounded-2xl bg-gray-50 p-5">
                    <dt class="mb-2 text-sm font-bold text-gray-400">تاريخ البداية</dt>
                    <dd class="font-black text-navy">{{ $penalty->start_date?->format('Y-m-d') ?? 'غير محدد' }}</dd>
                </div>
                <div class="rounded-2xl bg-gray-50 p-5">
                    <dt class="mb-2 text-sm font-bold text-gray-400">تاريخ النهاية</dt>
                    <dd class="font-black text-navy">{{ $penalty->end_date?->format('Y-m-d') ?? 'غير محدد' }}</dd>
                </div>
                <div class="rounded-2xl bg-gray-50 p-5">
                    <dt class="mb-2 text-sm font-bold text-gray-400">تم التطبيق بواسطة</dt>
                    <dd class="font-black text-navy">{{ $penalty->appliedBy?->name ?? 'غير محدد' }}</dd>
                </div>
                <div class="rounded-2xl bg-gray-50 p-5">
                    <dt class="mb-2 text-sm font-bold text-gray-400">الحالة</dt>
                    <dd class="font-black {{ $penalty->is_active ? 'text-emerald-600' : 'text-gray-500' }}">
                        {{ $penalty->is_active ? 'نشطة' : 'غير نشطة' }}
                    </dd>
                </div>
            </dl>

            <div class="mt-6 rounded-2xl border border-gray-100 p-6">
                <p class="mb-3 font-cairo text-sm font-bold text-gray-400">الوصف</p>
                <p class="whitespace-pre-line font-almarai leading-8 text-navy">{{ $penalty->description ?: 'لا يوجد وصف مسجل.' }}</p>
            </div>
        </div>
    </div>
@endsection
