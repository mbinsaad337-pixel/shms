@extends('layouts.app')
@php
    /** @var \App\Models\Student $student */
    $preview = $preview ?? false;
    $previewArchive = $previewArchive ?? null;
@endphp

@section('title',($preview?'معاينة السند المالي':'تفاصيل السند المالي').$voucher->voucher_number)

@section('content')
    <div class="container mx-auto px-4 py-8 max-w-5xl">
        
        @php
            $types = [
                'receipt' => 'سند قبض',
                'payment' => 'سند صرف',
                'transfer' => 'سند تحويل',
                'salary' => 'مسير رواتب',
            ];
            $typeLabel = $types[$voucher->type] ?? 'سند مالي';
        @endphp

        @include('partials.print_header', ['title' => $typeLabel, 'number' => $voucher->voucher_number])

        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-white rounded-2xl shadow-sm border border-gray-100 flex items-center justify-center text-primary">
                    <i class="fas fa-file-invoice-dollar text-xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 font-cairo">
                        {{ $typeLabel }}
                    </h1>
                    <div class="flex items-center gap-3 mt-1">
                        <span class="text-gray-500   text-sm">#{{ $voucher->voucher_number }}</span>
                        <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                        <span class="text-gray-500 text-sm font-almarai">{{ $voucher->date->format('Y-m-d') }}</span>
                    </div>
                </div>
            </div>
            
            
            
            @if ($preview && $previewArchive)
                <div class="mb-6 bg-gradient-to-r from-amber-50 to-orange-50 border-2 border-amber-300 rounded-2xl p-5 flex flex-col sm:flex-row items-center justify-between gap-4 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center">
                            <i class="fas fa-archive text-amber-600 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-amber-800 font-cairo">وضع المعاينة — سجل مؤرشف</h3>
                            <p class="text-xs text-amber-600 font-almarai">
                                بيانات هذا السند مؤرشفة من السنة {{ $previewArchive->year }}
                                &bull; الأرشيف #{{ str_pad($previewArchive->id, 6, '0', STR_PAD_LEFT) }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('annual-rollover.export-archive-pdf', $previewArchive) }}" target="_blank"
                            class="px-4 py-2 bg-red-50 hover:bg-red-100 text-red-700 rounded-xl text-xs font-bold font-cairo flex items-center gap-2 transition-all border border-red-200">
                            <i class="fas fa-file-pdf"></i> تصدير أرشيف PDF
                        </a>
                        <a href="{{ route('annual-rollover.index', $previewArchive) }}"
                            class="px-4 py-2 bg-white hover:bg-gray-50 text-navy rounded-xl text-xs font-bold font-cairo flex items-center gap-2 transition-all border border-gray-200">
                            <i class="fas fa-arrow-right"></i> العودة للأرشيف
                        </a>
                    </div>
                </div>
                @elseif (!$preview)
                <div class="flex gap-3">
                

                <a href="{{ route('vouchers.export-pdf', $voucher) }}" target="_blank"
                    class="bg-red-600 text-white hover:bg-red-700 px-4 py-2 rounded-xl flex items-center gap-2 text-sm font-bold font-cairo shadow-sm transition-all">
                    <i class="fas fa-file-pdf"></i>
                    <span>تصدير كـ PDF</span>
                </a>
            </div>
            @endif
        </div>

        <!-- Main Card -->
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden mb-6">
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 border-b border-gray-50">
                <div class="p-6 border-l border-gray-50 last:border-0">
                    <p class="text-sm text-gray-400 font-almarai mb-1">المبلغ الإجمالي</p>
                    <p class="text-2xl font-bold text-gray-800  ">
                        {{ number_format($voucher->amount, 2) }}
                        <span class="text-sm text-gray-500 font-cairo">{{ $voucher->fund->currency_symbol ?? 'ر.ي' }}</span>
                    </p>
                </div>
                <div class="p-6 border-l border-gray-50 last:border-0">
                    <p class="text-sm text-gray-400 font-almarai mb-1">طبيعة السند</p>
                    <p class="text-lg font-bold text-primary font-cairo">{{ $typeLabel }}</p>
                </div>
                <div class="p-6 border-l border-gray-50 last:border-0">
                    <p class="text-sm text-gray-400 font-almarai mb-1">الصندوق</p>
                    <p class="text-lg font-bold text-gray-800 font-cairo">{{ $voucher->fund->name ?? '-' }}
                        @if($voucher->fund)<span class="text-xs font-bold text-emerald-700">({{ $voucher->fund->currency_label }})</span>@endif</p>
                </div>
                <div class="p-6 border-l border-gray-50 last:border-0">
                    <p class="text-sm text-gray-400 font-almarai mb-1">المُنشئ</p>
                    <p class="text-lg font-bold text-gray-800 font-cairo">{{ $voucher->creator->name ?? '-' }}</p>
                </div>
            </div>

            <div class="p-6 md:p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-12">
                    
                    <div class="space-y-6">
                        <div>
                            <h3 class="text-sm font-bold text-gray-800 font-cairo mb-4 pb-2 border-b border-gray-100">
                                <i class="fas fa-info-circle text-gray-400 ml-2"></i> بيانات المستفيد أو الدافع
                            </h3>
                            <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100">
                                <p class="text-gray-500 text-sm mb-1 font-almarai">
                                    @if(in_array($voucher->type, ['payment', 'salary']))يُصرف للسيد/الجهة:
                                    @elseif($voucher->type == 'receipt')استلمنا من السيد/الجهة:
                                    @else
                                        المُستفيد/الطرف الآخر:
                                    @endif
                                </p>
                                <p class="text-lg font-bold text-gray-800">{{ $voucher->payee_or_payer }}</p>
                                @if($voucher->student)
                                    <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center text-primary text-xs">
                                                <i class="fas fa-user-graduate"></i>
                                            </div>
                                            <div>
                                                <p class="text-[10px] text-gray-400 font-cairo">مرتبط بالطالب:</p>
                                                <a href="{{ route('students.show', $voucher->student) }}" class="text-sm font-bold text-primary hover:underline">{{ $voucher->student->name_ar }}</a>
                                            </div>
                                        </div>
                                        <div class="text-left text-[10px]">
                                            <p class="text-gray-400 font-cairo">المتبقي من الرسوم:</p>
                                            <p class="font-bold text-red-600">{{ number_format($voucher->student->remaining_fees, 2) }} ر.ي</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div>
                            <h3 class="text-sm font-bold text-gray-800 font-cairo mb-4 pb-2 border-b border-gray-100">
                                <i class="fas fa-file-alt text-gray-400 ml-2"></i> البيان / التفاصيل
                            </h3>
                            <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100 min-h-[100px] text-gray-700 font-almarai leading-relaxed">
                                {{ $voucher->description }}
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6">
                        @if($voucher->type === 'transfer' && $voucher->targetFund)
                            <div>
                                <h3 class="text-sm font-bold text-gray-800 font-cairo mb-4 pb-2 border-b border-gray-100">
                                    <i class="fas fa-exchange-alt text-gray-400 ml-2"></i> بيانات التحويل
                                </h3>
                                <div class="bg-blue-50/50 rounded-2xl p-4 border border-blue-100 flex items-center justify-between">
                                    <div>
                                        <p class="text-sm text-gray-500 mb-1">صندوق المصدر</p>
                                        <p class="font-bold text-gray-800">{{ $voucher->fund->name }}
                                            <span class="text-[10px] font-bold text-emerald-700">{{ $voucher->fund->currency_label }}</span>
                                        </p>
                                    </div>
                                    <i class="fas fa-arrow-left text-blue-300 mx-2"></i>
                                    <div>
                                        <p class="text-sm text-gray-500 mb-1">صندوق الوجهة</p>
                                        <p class="font-bold text-blue-700">{{ $voucher->targetFund->name }}
                                            <span class="text-[10px] font-bold text-emerald-700">{{ $voucher->targetFund->currency_label }}</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div>
                            <h3 class="text-sm font-bold text-gray-800 font-cairo mb-4 pb-2 border-b border-gray-100">
                                <i class="fas fa-paperclip text-gray-400 ml-2"></i> المرفقات والنُسخ الأصلية
                            </h3>
                            @if($voucher->attachment)
                                <a href="{{ asset('storage/' . $voucher->attachment) }}" target="_blank"
                                   class="flex items-center gap-3 p-4 bg-gray-50 rounded-2xl border border-gray-100 hover:bg-primary-50 hover:border-primary-100 transition-all group">
                                    <div class="w-10 h-10 rounded-xl bg-white border border-gray-200 flex items-center justify-center text-gray-400 group-hover:text-primary group-hover:border-primary-200 transition-colors">
                                        <i class="fas fa-file-pdf"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-bold text-gray-800 font-cairo group-hover:text-primary transition-colors">عرض المرفق الأصلي</p>
                                        <p class="text-xs text-gray-500 mt-0.5">انقر للاستعراض في نافذة جديدة</p>
                                    </div>
                                    <i class="fas fa-external-link-alt text-gray-300 group-hover:text-primary transition-colors text-sm"></i>
                                </a>
                            @else
                                <div class="text-center p-6 bg-gray-50 rounded-2xl border border-gray-100 border-dashed">
                                    <i class="fas fa-unlink text-gray-300 text-2xl mb-2 block"></i>
                                    <p class="text-sm text-gray-500 font-almarai">لا توجد مرفقات مرتبطة بهذا السند</p>
                                </div>
                            @endif
                        </div>

                        <div>
                            <h3 class="text-sm font-bold text-gray-800 font-cairo mb-4 pb-2 border-b border-gray-100">
                                <i class="fas fa-history text-gray-400 ml-2"></i> تفاصيل إضافية
                            </h3>
                            <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100 space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-500 font-almarai">المركز</span>
                                    <span class="text-sm font-bold text-gray-800">{{ $voucher->center->name }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-500 font-almarai">تاريخ الإنشاء</span>
                                    <span class="text-sm font-bold text-gray-800  ">{{ $voucher->created_at->format('Y-m-d H:i') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
        @if(!$preview)
            <div class="flex justify-center no-print mt-8">
                <a href="{{ route('vouchers.index') }}" class="text-gray-500 hover:text-gray-700 font-bold font-cairo text-sm transition-colors flex items-center gap-2">
                    <i class="fas fa-arrow-right"></i>
                    <span>العودة لقائمة السندات</span>
                </a>
            </div>
        @endif
        @include('partials.print_footer')
    </div>
@endsection
