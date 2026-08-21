@extends(auth()->user()->hasRole('nutrition-manager') ? 'layouts.nutrition' : 'layouts.app')
@section('title', 'تفاصيل الفاتورة')

@section('content')
    @php $preview = $preview ?? false; $previewArchive = $previewArchive ?? null; @endphp
    <div class="p-6 max-w-4xl mx-auto">

        @include('partials.print_header', ['title' => 'فاتورة مشتريات - ' . $invoice->invoice_number, 'number' => $invoice->invoice_number])

        <div class="flex items-center justify-between mb-6 no-print">
            <div class="flex items-center gap-4">
              @if(!$preview)
                <a href="{{ route('nutrition.invoices.index') }}" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-arrow-right text-xl"></i>
                </a>
                @endif
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 font-cairo">{{ $invoice->invoice_number }}</h2>
                    <p class="text-gray-400 font-cairo text-sm">{{ $archivedSupplierName ?? $invoice->supplier?->name ?? '—' }}</p>
                </div>
            </div>
            <div class="flex gap-2 no-print">
                @if(!$preview)
                    <a href="{{ route('nutrition.invoices.export-pdf', $invoice) }}"
                        class="inline-flex items-center gap-2 bg-gray-800 text-white px-4 py-2.5 rounded-xl font-bold font-cairo text-sm">
                        <i class="fas fa-file-pdf"></i> تصدير PDF
                    </a>
                    @if($invoice->status === 'approved')
                        <form action="{{ route('nutrition.invoices.cancel', $invoice) }}" method="POST"
                            data-confirm="هل تريد إلغاء هذه الفاتورة؟">
                            @csrf
                            <button type="submit"
                                class="inline-flex items-center gap-2 bg-red-600 text-white px-4 py-2.5 rounded-xl font-bold font-cairo text-sm">
                                <i class="fas fa-ban"></i> إلغاء
                            </button>
                        </form>
                    @endif
                @elseif($preview && $previewArchive)
                    <a href="{{ route('annual-rollover.export-archive-pdf', $previewArchive) }}" target="_blank"
                        class="inline-flex items-center gap-2 bg-gray-800 text-white px-4 py-2.5 rounded-xl font-bold font-cairo text-sm">
                        <i class="fas fa-file-pdf"></i> تصدير PDF
                    </a>
                    <a href="{{ route('annual-rollover.index') }}"
                        class="inline-flex items-center gap-2 bg-gray-100 text-gray-700 px-4 py-2.5 rounded-xl font-bold font-cairo text-sm">
                        <i class="fas fa-arrow-right"></i> رجوع للقائمة
                    </a>
                @endif
            </div>
        </div>

        <!-- Info Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
            <div class="bg-orange-50 border border-orange-100 rounded-2xl p-4">
                <p class="text-[10px] font-bold text-orange-400 font-cairo uppercase mb-1">المورد</p>
                <p class="font-bold text-orange-800 font-cairo truncate">{{ $archivedSupplierName ?? $invoice->supplier?->name ?? '—' }}</p>
            </div>
            <div class="bg-gray-50 border border-gray-100 rounded-2xl p-4">
                <p class="text-[10px] font-bold text-gray-400 font-cairo uppercase mb-1">تاريخ الفاتورة</p>
                <p class="font-bold text-gray-800  ">{{ $invoice->invoice_date->format('Y-m-d') }}</p>
            </div>
            <div class="bg-blue-50 border border-blue-100 rounded-2xl p-4">
                <p class="text-[10px] font-bold text-blue-400 font-cairo uppercase mb-1">طريقة الدفع</p>
                <p
                    class="font-bold font-cairo text-sm {{ $invoice->payment_type === 'cash' ? 'text-green-600' : 'text-orange-600' }}">
                    <i class="fas {{ $invoice->payment_type === 'cash' ? 'fa-money-bill-wave' : 'fa-clock' }} ml-1"></i>
                    {{ $invoice->payment_type_label }}
                </p>
            </div>
            <div class="bg-gray-50 border border-gray-100 rounded-2xl p-4">
                <p class="text-[10px] font-bold text-gray-400 font-cairo uppercase mb-1">الحالة</p>
                <span
                    class="px-2 py-0.5 rounded text-[10px] font-bold inline-block mt-0.5
                            {{ $invoice->status === 'approved' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                    {{ $invoice->status === 'approved' ? 'مُعتمدة' : 'ملغاة' }}
                </span>
            </div>
            <div class="bg-orange-50 border border-orange-200 rounded-2xl p-4">
                <p class="text-[10px] font-bold text-orange-400 font-cairo uppercase mb-1">الإجمالي</p>
                <p class="font-black text-orange-700   text-lg">{{ number_format($invoice->total_amount, 2) }} {{ currency_symbol() }}
                </p>
            </div>
        </div>

        <!-- Items Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                <h3 class="font-bold text-gray-800 font-cairo">بنود الفاتورة</h3>
            </div>
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="text-right px-5 py-3 text-xs font-bold text-gray-500 font-cairo">#</th>
                        <th class="text-right px-5 py-3 text-xs font-bold text-gray-500 font-cairo">المادة</th>
                        <th class="text-center px-5 py-3 text-xs font-bold text-gray-500 font-cairo">الكمية</th>
                        <th class="text-center px-5 py-3 text-xs font-bold text-gray-500 font-cairo">الوحدة</th>
                        <th class="text-center px-5 py-3 text-xs font-bold text-gray-500 font-cairo">السعر</th>
                        <th class="text-center px-5 py-3 text-xs font-bold text-gray-500 font-cairo">الإجمالي</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($invoice->items as $i => $item)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-5 py-3 text-gray-400   text-sm">{{ $i + 1 }}</td>
                            <td class="px-5 py-3 font-bold text-gray-800 font-cairo">{{ $item->item_name }}</td>
                            <td class="px-5 py-3 text-center   text-gray-700">{{ number_format($item->quantity, 3) }}
                            </td>
                            <td class="px-5 py-3 text-center font-cairo text-gray-500 text-sm">{{ $item->unit ?? '—' }}</td>
                            <td class="px-5 py-3 text-center   text-gray-700">{{ number_format($item->unit_price, 2) }}
                            </td>
                            <td class="px-5 py-3 text-center font-bold text-orange-700  ">
                                {{ number_format($item->total, 2) }} {{ currency_symbol() }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-orange-50 border-t-2 border-orange-200">
                    <tr>
                        <td colspan="5" class="px-5 py-3 text-right font-black text-orange-800 font-cairo">الإجمالي الكلي:
                        </td>
                        <td class="px-5 py-3 text-center font-black text-orange-800   text-xl">
                            {{ number_format($invoice->total_amount, 2) }} {{ currency_symbol() }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Notes & Attachment -->
        @if($invoice->notes || $invoice->attachment)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                @if($invoice->notes)
                    <div class="mb-3">
                        <p class="text-xs font-bold text-gray-400 font-cairo mb-1">ملاحظات</p>
                        <p class="text-gray-600 font-almarai text-sm">{{ $invoice->notes }}</p>
                    </div>
                @endif
                @if($invoice->attachment)
                    <a href="{{ \Illuminate\Support\Facades\Storage::url($invoice->attachment) }}" target="_blank"
                        class="inline-flex items-center gap-2 text-blue-600 hover:underline font-cairo text-sm">
                        <i class="fas fa-paperclip"></i> عرض مرفق الفاتورة
                    </a>
                @endif
            </div>
        @endif

        @include('partials.print_footer')
    </div>
@endsection
