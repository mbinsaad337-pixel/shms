@extends('layouts.app')

@php
    $preview = $preview ?? false;
    $previewArchive = $previewArchive ?? null;
    $receiptUrl = $receiptUrl ?? $expense->receipt_url;
@endphp

@section('title', $preview ? 'معاينة مصروف مركز مؤرشف' : 'تفاصيل مصروف المركز')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    @include('partials.print_header', ['title' => 'تفاصيل مصروف مركز', 'number' => 'EXP-' . str_pad($expense->id, 6, '0', STR_PAD_LEFT)])

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl {{ $expense->type_color }} flex items-center justify-center">
                <i class="fas {{ $expense->type_icon }} text-2xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-black text-navy font-cairo">{{ $expense->type_label }}</h1>
                <p class="text-sm text-gray-400 font-almarai mt-1">{{ $expense->center?->name ?? '—' }} · {{ str_pad($expense->month, 2, '0', STR_PAD_LEFT) }}/{{ $expense->year }}</p>
            </div>
        </div>

        <div class="flex flex-wrap gap-2">
            @if ($preview && $previewArchive)
                <a href="{{ route('annual-rollover.export-archive-pdf', $previewArchive) }}" target="_blank" class="px-4 py-2.5 rounded-xl bg-red-50 text-red-700 border border-red-200 font-bold font-cairo text-sm hover:bg-red-100 transition-all">
                    <i class="fas fa-file-pdf ml-1"></i> تصدير PDF
                </a>
                <a href="{{ route('annual-rollover.index') }}" class="px-4 py-2.5 rounded-xl bg-white text-navy border border-gray-200 font-bold font-cairo text-sm hover:bg-gray-50 transition-all">
                    <i class="fas fa-arrow-right ml-1"></i> العودة للأرشيف
                </a>
            @else
                <a href="{{ route('center-expenses.index') }}" class="px-4 py-2.5 rounded-xl bg-white text-navy border border-gray-200 font-bold font-cairo text-sm hover:bg-gray-50 transition-all">
                    <i class="fas fa-arrow-right ml-1"></i> العودة للمصروفات
                </a>
                <a href="{{ route('center-expenses.export-pdf', $expense) }}" target="_blank" class="px-4 py-2.5 rounded-xl bg-red-50 text-red-700 border border-red-200 font-bold font-cairo text-sm hover:bg-red-100 transition-all">
                    <i class="fas fa-file-pdf ml-1"></i> تصدير PDF
                </a>
            @endif
        </div>
    </div>

    @if ($preview && $previewArchive)
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 flex items-center gap-3 text-amber-900 font-cairo">
            <i class="fas fa-archive text-amber-600 text-xl"></i>
            <span>معاينة سجل مؤرشف من سنة {{ $previewArchive->year }}.</span>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div class="card-premium p-6"><p class="text-xs text-gray-400 font-almarai mb-2">المبلغ</p><p class="text-2xl font-black text-navy">{{ number_format($expense->amount, 2) }} <span class="text-sm text-gray-400">{{ $expense->currency_symbol }}</span></p><p class="text-xs text-gray-400 font-almarai mt-1">{{ $expense->currency_label }}</p></div>
        <div class="card-premium p-6"><p class="text-xs text-gray-400 font-almarai mb-2">الحالة</p><span class="inline-flex px-3 py-1.5 rounded-full text-sm font-bold font-cairo {{ $expense->status_color }}">{{ $expense->status_label }}</span></div>
        <div class="card-premium p-6"><p class="text-xs text-gray-400 font-almarai mb-2">تاريخ الاستحقاق</p><p class="text-lg font-black text-navy">{{ optional($expense->due_date)->format('Y-m-d') ?? '—' }}</p></div>
    </div>

    <div class="card-premium overflow-hidden">
        <div class="p-6 border-b border-gray-100"><h2 class="font-black text-navy font-cairo">بيانات المصروف</h2></div>
        <dl class="grid grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-gray-100">
            <div class="p-5"><dt class="text-xs text-gray-400 font-almarai mb-1">المركز</dt><dd class="font-bold text-navy font-cairo">{{ $expense->center?->name ?? '—' }}</dd></div>
            <div class="p-5"><dt class="text-xs text-gray-400 font-almarai mb-1">الفترة</dt><dd class="font-bold text-navy">{{ str_pad($expense->month, 2, '0', STR_PAD_LEFT) }} / {{ $expense->year }}</dd></div>
            <div class="p-5"><dt class="text-xs text-gray-400 font-almarai mb-1">تاريخ الدفع</dt><dd class="font-bold text-navy">{{ optional($expense->payment_date)->format('Y-m-d') ?? 'لم يتم الدفع' }}</dd></div>
            <div class="p-5"><dt class="text-xs text-gray-400 font-almarai mb-1">تاريخ التسجيل</dt><dd class="font-bold text-navy">{{ optional($expense->created_at)->format('Y-m-d') ?? '—' }}</dd></div>
        </dl>
    </div>

    @if ($expense->notes)
        <div class="card-premium p-6"><h2 class="font-black text-navy font-cairo mb-3">ملاحظات</h2><p class="text-gray-600 font-almarai whitespace-pre-line">{{ $expense->notes }}</p></div>
    @endif

    @if ($expense->receipt && $receiptUrl)
        <div class="card-premium p-6">
            <div class="flex items-center justify-between gap-3 mb-4"><h2 class="font-black text-navy font-cairo">الإيصال المرفق</h2><a href="{{ $receiptUrl }}" target="_blank" class="text-sm text-blue-600 hover:underline font-bold">فتح الملف الأصلي <i class="fas fa-external-link-alt mr-1"></i></a></div>
            @if ($expense->receipt_type === 'pdf')
                <iframe src="{{ $receiptUrl }}" class="w-full h-[600px] rounded-xl border border-gray-100" title="إيصال المصروف"></iframe>
            @else
                <a href="{{ $receiptUrl }}" target="_blank"><img src="{{ $receiptUrl }}" alt="إيصال المصروف" class="max-h-[650px] mx-auto rounded-xl border border-gray-100"></a>
            @endif
        </div>
    @endif
</div>
@endsection
