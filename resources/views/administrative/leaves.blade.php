@extends('layouts.app')

@php
    $preview = $preview ?? false;
    $previewArchive = $previewArchive ?? null;
@endphp

@section('title', 'تفاصيل الاستئذان')

@section('content')
    <div class="container mx-auto px-6 py-8" dir="rtl">
        <div class="mb-8 flex flex-col gap-4 rounded-3xl border-r-8 border-navy bg-white p-8 shadow-sm md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="font-cairo text-3xl font-black text-navy">تفاصيل الاستئذان</h1>
                <p class="mt-2 font-almarai text-sm text-gray-400">{{ $preview ? 'معاينة السجل المؤرشف' : 'بيانات الاستئذان المسجل' }}</p>
            </div>
            <div class="flex gap-3">
                @if ($preview && $previewArchive)
                    <a href="{{ route('annual-rollover.export-archive-pdf', $previewArchive) }}" target="_blank" class="rounded-2xl border border-red-100 bg-red-50 px-5 py-3 font-cairo font-bold text-red-600"><i class="fas fa-file-pdf ml-2"></i>تصدير PDF</a>
                @endif
                <a href="{{ $preview ? route('annual-rollover.index') : route('administrative.index', ['tab' => 'leaves']) }}" class="rounded-2xl border border-gray-100 bg-gray-50 px-5 py-3 font-cairo font-bold text-navy"><i class="fas fa-arrow-right ml-2"></i>رجوع</a>
            </div>
        </div>

        <div class="mx-auto max-w-4xl rounded-[2rem] border border-gray-100 bg-white p-8 shadow-sm md:p-10">
            <div class="mb-8 border-b border-gray-100 pb-8"><p class="mb-2 font-cairo text-sm font-bold text-gray-400">الطالب</p><h2 class="font-cairo text-2xl font-black text-navy">{{ $leave->student?->name_ar ?? 'غير محدد' }}</h2></div>
            <dl class="grid grid-cols-1 gap-6 font-cairo sm:grid-cols-2">
                <div class="rounded-2xl bg-gray-50 p-5"><dt class="mb-2 text-sm font-bold text-gray-400">النوع</dt><dd class="font-black text-navy">{{ $leave->getTypeLabel() }}</dd></div>
                <div class="rounded-2xl bg-gray-50 p-5"><dt class="mb-2 text-sm font-bold text-gray-400">الحالة</dt><dd class="font-black text-navy">{{ $leave->getStatusLabel() }}</dd></div>
                <div class="rounded-2xl bg-gray-50 p-5"><dt class="mb-2 text-sm font-bold text-gray-400">المغادرة</dt><dd class="font-black text-navy">{{ $leave->departure_date?->format('Y-m-d') ?? 'غير محدد' }} {{ $leave->departure_time }}</dd></div>
                <div class="rounded-2xl bg-gray-50 p-5"><dt class="mb-2 text-sm font-bold text-gray-400">العودة المتوقعة</dt><dd class="font-black text-navy">{{ $leave->expected_return_date?->format('Y-m-d') ?? 'غير محدد' }} {{ $leave->expected_return_time }}</dd></div>
                <div class="rounded-2xl bg-gray-50 p-5"><dt class="mb-2 text-sm font-bold text-gray-400">اعتمده</dt><dd class="font-black text-navy">{{ $leave->approvedBy?->name ?? 'غير محدد' }}</dd></div>
                <div class="rounded-2xl bg-gray-50 p-5"><dt class="mb-2 text-sm font-bold text-gray-400">العودة الفعلية</dt><dd class="font-black text-navy">{{ $leave->actual_return_date?->format('Y-m-d') ?? 'لم تسجل' }}</dd></div>
            </dl>
            <div class="mt-6 rounded-2xl border border-gray-100 p-6"><p class="mb-3 font-cairo text-sm font-bold text-gray-400">السبب</p><p class="whitespace-pre-line font-almarai leading-8 text-navy">{{ $leave->reason ?: 'لا يوجد سبب مسجل.' }}</p></div>
            @if ($leave->rejection_reason)<div class="mt-6 rounded-2xl border border-red-100 bg-red-50 p-6"><p class="mb-3 font-cairo text-sm font-bold text-red-500">سبب الرفض</p><p class="font-almarai leading-8 text-navy">{{ $leave->rejection_reason }}</p></div>@endif
        </div>
    </div>
@endsection
