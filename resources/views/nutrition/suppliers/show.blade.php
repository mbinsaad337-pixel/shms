@extends(auth()->user()->hasRole('nutrition-manager') ? 'layouts.nutrition' : 'layouts.app')
@section('title', 'كشف حساب: ' . $supplier->name)

@section('content')
    @php $preview = $preview ?? false; $previewArchive = $previewArchive ?? null; @endphp
    <div class="p-6 max-w-5xl mx-auto">

        @include('partials.print_header', (array)['title' => 'كشف حساب مورد: ' . $supplier->name, 'number' => 'SUP-' . $supplier->id])

        <div class="flex items-center justify-between mb-6 no-print">
            <div class="flex items-center gap-4">
              @if(!$preview)
                <a href="{{ route('nutrition.suppliers.index') }}" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-arrow-right text-xl"></i>
                </a>
                @endif
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 font-cairo">كشف حساب: {{ $supplier->name }}</h2>
                    @if($supplier->phone)
                        <p class="text-gray-400 text-sm  " dir="ltr">{{ $supplier->phone }}</p>
                    @endif
                </div>
            </div>
            <div class="flex gap-2 no-print">
                @if(!$preview)
                    <a href="{{ route('nutrition.suppliers.export-pdf', $supplier) }}"
                        class="inline-flex items-center gap-2 bg-gray-800 text-white px-4 py-2.5 rounded-xl font-bold font-cairo text-sm">
                        <i class="fas fa-file-pdf"></i> تصدير PDF
                    </a>
                    <a href="{{ route('nutrition.invoices.create') }}?supplier={{ $supplier->id }}"
                        class="inline-flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white px-4 py-2.5 rounded-xl font-bold font-cairo text-sm">
                        <i class="fas fa-receipt"></i> فاتورة جديدة
                    </a>
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

        <!-- Summary Cards -->
        <div class="grid grid-cols-3 gap-4 mb-6">
            @php $net = $supplier->balance_debit - $supplier->balance_credit; @endphp
            <div class="bg-red-50 border border-red-100 rounded-2xl p-5 text-center">
                <p class="text-[10px] font-bold text-red-400 uppercase font-cairo mb-1">مدين (يستحقه)</p>
                <p class="text-3xl font-black text-red-700  ">{{ number_format($supplier->balance_debit, 2) }}</p>
                <p class="text-xs text-red-400 font-cairo">ر.ي</p>
            </div>
            <div class="bg-green-50 border border-green-100 rounded-2xl p-5 text-center">
                <p class="text-[10px] font-bold text-green-500 uppercase font-cairo mb-1">دائن (مدفوع له)</p>
                <p class="text-3xl font-black text-green-700  ">{{ number_format($supplier->balance_credit, 2) }}
                </p>
                <p class="text-xs text-green-400 font-cairo">ر.ي</p>
            </div>
            <div
                class="{{ $net > 0 ? 'bg-orange-50 border-orange-100' : ($net < 0 ? 'bg-blue-50 border-blue-100' : 'bg-gray-50 border-gray-100') }} border rounded-2xl p-5 text-center">
                <p
                    class="text-[10px] font-bold uppercase font-cairo mb-1 {{ $net > 0 ? 'text-orange-500' : ($net < 0 ? 'text-blue-500' : 'text-gray-400') }}">
                    {{ $net > 0 ? 'مستحق عليك' : ($net < 0 ? 'زيادة مدفوعة' : 'متوازن') }}
                </p>
                <p
                    class="text-3xl font-black   {{ $net > 0 ? 'text-orange-700' : ($net < 0 ? 'text-blue-700' : 'text-gray-500') }}">
                    {{ number_format(abs($net), 2) }}</p>
                <p class="text-xs font-cairo {{ $net > 0 ? 'text-orange-400' : 'text-blue-400' }}">ر.ي</p>
            </div>
        </div>

        <!-- Ledger Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                <h3 class="font-bold text-gray-800 font-cairo">كشف الحساب التفصيلي</h3>
                <span class="text-sm text-gray-400 font-almarai">{{ $ledger->count() }} حركة</span>
            </div>
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-right px-4 py-3 text-xs font-bold text-gray-500 font-cairo">التاريخ</th>
                        <th class="text-right px-4 py-3 text-xs font-bold text-gray-500 font-cairo">المرجع</th>
                        <th class="text-right px-4 py-3 text-xs font-bold text-gray-500 font-cairo">البيان</th>
                        <th class="text-center px-4 py-3 text-xs font-bold text-gray-500 font-cairo">مدين</th>
                        <th class="text-center px-4 py-3 text-xs font-bold text-gray-500 font-cairo">دائن</th>
                        <th class="text-center px-4 py-3 text-xs font-bold text-gray-500 font-cairo">الرصيد</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @if(is_countable($ledger) ? count($ledger) > 0 : (method_exists($ledger, 'count') ? $ledger->count() > 0 : !empty($ledger)))
    @foreach($ledger as $row)
                        <tr class="hover:bg-gray-50/50 {{ $row['type'] === 'invoice' ? '' : 'bg-green-50/30' }}">
                            <td class="px-4 py-3   text-gray-600 text-sm">
                                {{ \Carbon\Carbon::parse($row['date'])->format('Y-m-d') }}</td>
                            <td class="px-4 py-3   text-gray-700 text-sm font-bold">{{ $row['reference'] }}</td>
                            <td class="px-4 py-3 font-cairo text-gray-600 text-sm flex items-center gap-2">
                                @if($row['type'] === 'invoice')
                                    <span class="w-2 h-2 rounded-full bg-red-400 inline-block"></span>
                                @else
                                    <span class="w-2 h-2 rounded-full bg-green-400 inline-block"></span>
                                @endif
                                {{ $row['description'] }}
                            </td>
                            <td class="px-4 py-3 text-center   font-bold text-red-600">
                                {{ $row['debit'] > 0 ? number_format($row['debit'], 2) : '—' }}</td>
                            <td class="px-4 py-3 text-center   font-bold text-green-600">
                                {{ $row['credit'] > 0 ? number_format($row['credit'], 2) : '—' }}</td>
                            <td
                                class="px-4 py-3 text-center   font-bold {{ $row['running_balance'] > 0 ? 'text-orange-600' : ($row['running_balance'] < 0 ? 'text-blue-600' : 'text-gray-400') }}">
                                {{ number_format(abs($row['running_balance']), 2) }}
                                @if($row['running_balance'] != 0)
                                    <span class="text-[9px] font-cairo">{{ $row['running_balance'] > 0 ? 'د' : 'د' }}</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
@else
                        <tr>
                            <td colspan="6" class="py-10 text-center text-gray-300 font-cairo">لا توجد حركات مالية لهذا المورد
                            </td>
                        </tr>
                    @endif
                </tbody>
                @if($ledger->count() > 0)
                    <tfoot class="bg-gray-50 border-t-2 border-gray-200">
                        <tr>
                            <td colspan="3" class="px-4 py-3 text-right font-black text-gray-700 font-cairo">الإجمالي</td>
                            <td class="px-4 py-3 text-center font-black text-red-700  ">
                                {{ number_format($ledger->sum('debit'), 2) }}</td>
                            <td class="px-4 py-3 text-center font-black text-green-700  ">
                                {{ number_format($ledger->sum('credit'), 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
@endsection
