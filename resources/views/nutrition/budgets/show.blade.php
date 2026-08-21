@extends (auth()->user()->hasRole('nutrition-manager') ? 'layouts.nutrition' : 'layouts.app')
@section ('title', 'تفاصيل الميزانية')

@section ('content')
    @php $preview = $preview ?? false; $previewArchive = $previewArchive ?? null; @endphp
    <div class="p-6 max-w-5xl mx-auto">

        @include ('partials.print_header', (array)['title' => 'ميزانية التغذية - ' . $budget->month_name . ' ' . $budget->year, 'number' => 'BDG-' . $budget->id])

        <div class="flex items-center justify-between mb-6 no-print">
            <div class="flex items-center gap-4">
              @if(!$preview)
                <a href="{{ route('nutrition.budgets.index') }}" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-arrow-right text-xl"></i>
                </a>
                @endif
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 font-cairo">
                        ميزانية {{ $budget->month_name }} {{ $budget->year }}
                    </h2>
                    <div class="flex items-center gap-3 mt-1">
                        <span class="px-3 py-1 rounded-full text-xs font-bold
                                    {{ $budget->status === 'approved' ? 'bg-green-100 text-green-700' :
        ($budget->status === 'submitted' ? 'bg-yellow-100 text-yellow-700' :
            ($budget->status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-600')) }}">
                            {{ $budget->status_label }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-2 no-print">
                @if(!$preview)
                    <a href="{{ route('nutrition.budgets.export-pdf', $budget) }}"
                        class="inline-flex items-center gap-2 bg-gray-800 text-white px-4 py-2.5 rounded-xl font-bold font-cairo text-sm">
                        <i class="fas fa-file-pdf"></i> تصدير PDF
                    </a>
                    @if ($budget->status === 'draft')
                        <form action="{{ route('nutrition.budgets.submit', $budget) }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="inline-flex items-center gap-2 bg-yellow-500 hover:bg-yellow-600 text-white px-5 py-2.5 rounded-xl font-bold font-cairo text-sm shadow-lg shadow-yellow-200 transition-all">
                                <i class="fas fa-paper-plane"></i> إرسال للاعتماد
                            </button>
                        </form>
                    @endif
                    @if ($budget->status === 'submitted' && (auth()->user()->hasRole('center-manager') || auth()->user()->hasRole('super-admin')))
                        <form action="{{ route('nutrition.budgets.approve', $budget) }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-xl font-bold font-cairo text-sm shadow-lg shadow-green-200">
                                <i class="fas fa-check"></i> اعتماد
                            </button>
                        </form>
                        <button onclick="document.getElementById('rejectModal').classList.remove('hidden')"
                            class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-5 py-2.5 rounded-xl font-bold font-cairo text-sm">
                            <i class="fas fa-times"></i> رفض
                        </button>
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

        <!-- Summary Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-blue-50 border border-blue-100 rounded-2xl p-4 text-center">
                <p class="text-[10px] text-blue-500 font-bold uppercase font-cairo mb-1">إجمالي الميزانية</p>
                <p class="text-2xl font-black text-blue-700  ">{{ number_format($budget->total_amount, 2) }}</p>
                <p class="text-xs text-blue-400">{{ currency_symbol() }}</p>
            </div>
            <div class="bg-purple-50 border border-purple-100 rounded-2xl p-4 text-center">
                <p class="text-[10px] text-purple-500 font-bold uppercase font-cairo mb-1">عدد المشتركين</p>
                <p class="text-2xl font-black text-purple-700 font-cairo">{{ $budget->subscribers_count }}</p>
                <p class="text-xs text-purple-400">طالب</p>
            </div>
            <div class="bg-green-50 border border-green-100 rounded-2xl p-4 text-center">
                <p class="text-[10px] text-green-500 font-bold uppercase font-cairo mb-1">تكلفة الطالب</p>
                <p class="text-2xl font-black text-green-700  ">
                    {{ number_format($budget->cost_per_student ?? 0, 2) }}
                </p>
                <p class="text-xs text-green-400">{{ currency_symbol() }}</p>
            </div>
            <div class="bg-orange-50 border border-orange-100 rounded-2xl p-4 text-center">
                <p class="text-[10px] text-orange-500 font-bold uppercase font-cairo mb-1">الاشتراك اليومي</p>
                <p class="text-2xl font-black text-orange-700  ">{{ number_format($budget->daily_rate ?? 0, 2) }}
                </p>
                <p class="text-xs text-orange-400">{{ currency_symbol() }}/يوم</p>
            </div>
        </div>

        <!-- Supplier Totals Card -->
        @php
            $supplierTotals = collect($budget->lines)->groupBy('supplier_name')->map(function ($lines) {
                return $lines->sum('total');
            });
        @endphp
        @if($supplierTotals->count() > 0)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-100 bg-slate-50 flex items-center justify-between">
                <h3 class="font-bold text-gray-800 font-cairo"><i class="fas fa-truck text-primary ml-2"></i>إجمالي المبالغ حسب المورد</h3>
            </div>
            <div class="p-4 grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($supplierTotals as $supplierName => $total)
                    @if(!empty($supplierName))
                    <div class="border border-gray-100 rounded-xl p-3 flex flex-col items-center justify-center bg-gray-50/50">
                        <span class="text-xs font-bold text-gray-500 font-cairo text-center mb-1">{{ $supplierName }}</span>
                        <span class="font-black text-navy  ">{{ number_format($total, 2) }} <span class="text-[10px] text-gray-400 font-cairo">{{ currency_symbol() }}</span></span>
                    </div>
                    @endif
                @endforeach
            </div>
        </div>
        @endif

        <!-- Rejection Reason -->
        @if ($budget->status === 'rejected' && $budget->rejection_reason)
            <div class="bg-red-50 border border-red-200 rounded-2xl p-4 mb-6">
                <p class="font-bold text-red-700 font-cairo flex items-center gap-2">
                    <i class="fas fa-exclamation-circle"></i> سبب الرفض:
                </p>
                <p class="text-red-600 font-almarai mt-1 text-sm">{{ $budget->rejection_reason }}</p>
            </div>
        @endif

        <!-- Budget Lines -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-bold text-gray-800 font-cairo">بنود الميزانية</h3>
                <span class="text-sm text-gray-400 font-almarai">{{ $budget->lines->count() }} بند</span>
            </div>
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-right px-4 py-3 text-xs font-bold text-gray-500 font-cairo">#</th>
                        <th class="text-right px-4 py-3 text-xs font-bold text-gray-500 font-cairo">البيان</th>
                        <th class="text-center px-4 py-3 text-xs font-bold text-gray-500 font-cairo">الأيام</th>
                        <th class="text-center px-4 py-3 text-xs font-bold text-gray-500 font-cairo">الكمية</th>
                        <th class="text-center px-4 py-3 text-xs font-bold text-gray-500 font-cairo">سعر الوحدة</th>
                        <th class="text-center px-4 py-3 text-xs font-bold text-gray-500 font-cairo">الإجمالي</th>
                        <th class="text-right px-4 py-3 text-xs font-bold text-gray-500 font-cairo">المورد</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach ($budget->lines->sortBy('sort_order') as $i => $line)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-4 py-3 text-gray-400   text-sm">{{ $i + 1 }}</td>
                            <td class="px-4 py-3 font-bold text-gray-800 font-cairo">{{ $line->item_name }}</td>
                            <td class="px-4 py-3 text-center   text-gray-600 text-sm">{{ $line->days ?? '—' }}</td>
                            <td class="px-4 py-3 text-center   text-gray-600 text-sm">{{ $line->quantity ?? '—' }}</td>
                            <td class="px-4 py-3 text-center   text-gray-600 text-sm">
                                {{ $line->unit_price ? number_format($line->unit_price, 2) : '—' }}
                            </td>
                            <td class="px-4 py-3 text-center font-bold text-green-700  ">
                                {{ number_format($line->total, 2) }} {{ currency_symbol() }}
                            </td>
                            <td class="px-4 py-3 text-gray-500 text-sm font-cairo">{{ $line->supplier_name ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-blue-50 border-t-2 border-blue-200">
                    <tr>
                        <td colspan="5" class="px-4 py-3 text-right font-black text-blue-800 font-cairo">الإجمالي الكلي:
                        </td>
                        <td class="px-4 py-3 text-center font-black text-blue-800   text-lg">
                            {{ number_format($budget->total_amount, 2) }} {{ currency_symbol() }}
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Meta -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm font-cairo">
                <div>
                    <p class="text-gray-400 text-xs font-bold">أُنشئت بواسطة</p>
                    <p class="text-gray-800 font-bold mt-1">{{ $budget->creator?->name ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-gray-400 text-xs font-bold">تاريخ الإنشاء</p>
                    <p class="text-gray-800   mt-1">{{ $budget->created_at->format('Y-m-d H:i') }}</p>
                </div>
                @if ($budget->approved_by)
                    <div>
                        <p class="text-gray-400 text-xs font-bold">اعتُمدت بواسطة</p>
                        <p class="text-gray-800 font-bold mt-1">{{ $budget->approver?->name ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-xs font-bold">تاريخ الاعتماد</p>
                        <p class="text-gray-800   mt-1">{{ $budget->approved_at?->format('Y-m-d H:i') ?? '—' }}</p>
                    </div>
                @endif
            </div>
        </div>

        @include('partials.print_footer')
    </div>

@if (!$preview)
    <!-- Reject Modal -->
    <div id="rejectModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
            <h3 class="font-bold text-gray-800 font-cairo mb-4">رفض الميزانية</h3>
            <form action="{{ route('nutrition.budgets.reject', $budget) }}" method="POST">
                @csrf
                <textarea name="rejection_reason" rows="4" required placeholder="اكتب سبب الرفض..."
                    class="w-full border border-gray-200 rounded-xl px-4 py-3 font-almarai text-sm focus:ring-2 focus:ring-red-400 mb-4"></textarea>
                <div class="flex gap-3 justify-end">
                    <button type="button" onclick="document.getElementById('rejectModal').classList.add('hidden')"
                        class="px-6 py-2.5 border border-gray-200 rounded-xl font-cairo font-bold text-gray-600">إلغاء</button>
                    <button type="submit"
                        class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold font-cairo">رفض
                        الميزانية</button>
                </div>
            </form>
        </div>
    </div>
@endif
@endsection
