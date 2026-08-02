@extends('layouts.nutrition')
@section('title', 'فواتير المشتريات')

@section('content')
    <div class="p-6 max-w-7xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 font-cairo">فواتير المشتريات</h2>
                <p class="text-gray-400 text-sm font-almarai">فواتير المواد الغذائية من الموردين</p>
            </div>
            <a href="{{ route('nutrition.invoices.create') }}"
                class="inline-flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white px-5 py-2.5 rounded-xl font-bold font-cairo text-sm shadow-lg shadow-orange-200 transition-all">
                <i class="fas fa-plus"></i> فاتورة جديدة
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-right px-5 py-3 text-xs font-bold text-gray-500 font-cairo">رقم الفاتورة</th>
                        <th class="text-right px-5 py-3 text-xs font-bold text-gray-500 font-cairo">المورد</th>
                        <th class="text-center px-5 py-3 text-xs font-bold text-gray-500 font-cairo">التاريخ</th>
                        <th class="text-center px-5 py-3 text-xs font-bold text-gray-500 font-cairo">طريقة الدفع</th>
                        <th class="text-center px-5 py-3 text-xs font-bold text-gray-500 font-cairo">الإجمالي</th>
                        <th class="text-center px-5 py-3 text-xs font-bold text-gray-500 font-cairo">الحالة</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @if ($invoices->count() > 0)
                        @foreach ($invoices as $invoice)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-5 py-4   font-bold text-gray-700 text-sm">{{ $invoice->invoice_number }}</td>
                                    <td class="px-5 py-4 font-cairo font-bold text-gray-800 text-sm">{{ $invoice->supplier->name }}</td>
                                    <td class="px-5 py-4 text-center   text-gray-600 text-sm">
                                        {{ $invoice->invoice_date->format('Y-m-d') }}</td>
                                    <td class="px-5 py-4 text-center">
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold {{ $invoice->payment_type === 'cash' ? 'bg-green-50 text-green-600 border border-green-200' : 'bg-orange-50 text-orange-600 border border-orange-200' }}">
                                            {{ $invoice->payment_type_label }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 text-center font-bold text-orange-600   text-sm">
                                        {{ number_format($invoice->total_amount, 2) }} ر.ي</td>
                                    <td class="px-5 py-4 text-center">
                                        <span
                                            class="px-2 py-1 rounded-lg text-xs font-bold
                                            {{ $invoice->status === 'approved' ? 'bg-green-100 text-green-700' :
                        ($invoice->status === 'cancelled' ? 'bg-red-100 text-red-500' : 'bg-gray-100 text-gray-500') }}">
                                            {{ $invoice->status === 'approved' ? 'مُعتمدة' : ($invoice->status === 'cancelled' ? 'ملغاة' : 'مسودة') }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 flex items-center gap-2">
                                        <a href="{{ route('nutrition.invoices.show', $invoice) }}"
                                            class="w-8 h-8 bg-orange-50 hover:bg-orange-100 text-orange-600 rounded-lg flex items-center justify-center text-xs">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if ($invoice->status === 'approved')
                                            <form action="{{ route('nutrition.invoices.cancel', $invoice) }}" method="POST"
                                                onsubmit="return confirm('هل تريد إلغاء هذه الفاتورة؟')">
                                                @csrf
                                                <button type="submit" title="إلغاء"
                                                    class="w-8 h-8 bg-amber-50 hover:bg-amber-100 text-amber-500 rounded-lg flex items-center justify-center text-xs">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            </form>
                                        @endif
                                        <form action="{{ route('nutrition.invoices.destroy', $invoice) }}" method="POST"
                                            onsubmit="return confirm('هل أنت متأكد من حذف هذه الفاتورة نهائياً؟')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="حذف"
                                                class="w-8 h-8 bg-red-50 hover:bg-red-100 text-red-500 rounded-lg flex items-center justify-center text-xs">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7" class="py-16 text-center text-gray-300">
                                <i class="fas fa-receipt text-5xl mb-3 block"></i>
                                <p class="font-cairo">لا توجد فواتير حتى الآن</p>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $invoices->links() }}</div>
    </div>
@endsection
