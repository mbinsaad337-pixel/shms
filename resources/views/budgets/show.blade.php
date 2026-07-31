@extends('layouts.app')

@section('title', 'تفاصيل الموازنة الشهرية')

@push('styles')
    <style>
        @media print {

            /* Hide system chrome */
            nav,
            header,
            aside,
            [class*="sidebar"],
            .no-print {
                display: none !important;
            }

            /* Reset layout for print */
            body {
                background: white !important;
            }

            main {
                padding: 0 !important;
            }

            /* Hide buttons while printing */
            button,
            a.bg-red-600 {
                display: none !important;
            }

            /* Ensure content fills page */
            .container {
                max-width: 100% !important;
                padding: 0 !important;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container mx-auto px-4 py-8">
        @php /** @var \App\Models\MonthlyBudget $budget */ @endphp
        @include('partials.print_header', (array)['title' => 'طلب الموازنة التشغيلية الشهرية', 'number' => "BGT-{$budget->id}"])
        <div class="max-w-5xl mx-auto">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                <div class="flex items-center gap-4">
                    <a href="{{ route('budgets.index') }}"
                        class="w-10 h-10 rounded-xl bg-white shadow-sm flex items-center justify-center text-gray-400 hover:text-primary transition-all">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800 font-cairo">موازنة شهر {{ $budget->month }} /
                            {{ $budget->year }}
                        </h1>
                        <p class="text-gray-500 font-almarai">مركز: {{ $budget->center->name }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    @php
                        $statuses = [
                            'draft' => ['label' => 'مسودة', 'color' => 'bg-gray-100 text-gray-700'],
                            'submitted' => ['label' => 'قيد مراجعة المدير', 'color' => 'bg-yellow-100 text-yellow-700'],
                            'confirmed' => ['label' => 'بانتظار مدير قسم المراكز الطلابي ', 'color' => 'bg-blue-100 text-blue-700'],
                            'approved' => ['label' => 'تم الاعتماد النهائي', 'color' => 'bg-green-100 text-green-700'],
                            'rejected' => ['label' => 'مرفوض', 'color' => 'bg-red-100 text-red-700'],
                        ];
                        $statusInfo = $statuses[$budget->status] ?? ['label' => $budget->status, 'color' => 'bg-gray-100 text-gray-700'];
                    @endphp
                    <span class="{{ $statusInfo['color'] }} px-4 py-2 rounded-xl text-sm font-bold font-cairo shadow-sm">
                        {{ $statusInfo['label'] }}
                    </span>

                    {{-- PDF Export Button --}}
                    <a href="{{ route('budgets.export-pdf', $budget) }}"
                        class="flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-xl text-sm font-bold font-cairo shadow-sm transition-all hover:shadow-md">
                        <i class="fas fa-file-pdf"></i>
                        حفظ PDF
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Info Column -->
                <div class="md:col-span-1 space-y-6">
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
                        <h3
                            class="font-bold text-gray-800 font-cairo mb-4 pb-2 border-b border-gray-50 flex items-center gap-2">
                            <i class="fas fa-info-circle text-primary"></i> ملخص الطلب
                        </h3>
                        <div class="space-y-4 font-almarai">
                            <div class="flex justify-between">
                                <span class="text-gray-400">تاريخ الطلب</span>
                                <span class="font-bold">{{ $budget->created_at->format('Y-m-d') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">بواسطة</span>
                                <span class="font-bold">{{ $budget->submitter->name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">المبلغ الإجمالي</span>
                                <span class="font-bold text-primary text-lg">{{ number_format($budget->total_amount, 2) }}
                                    ر.ي</span>
                            </div>
                        </div>
                    </div>

                    @if($budget->status == 'approved')
                        <div class="bg-green-50 rounded-3xl border border-green-100 p-6">
                            <h3
                                class="font-bold text-green-800 font-cairo mb-4 pb-2 border-b border-green-200 flex items-center gap-2">
                                <i class="fas fa-check-double"></i> تفاصيل الاعتماد
                            </h3>
                            <div class="space-y-4 font-almarai text-sm">
                                <div class="flex justify-between">
                                    <span class="text-green-600">المعتمد</span>
                                    <span class="font-bold">{{ $budget->approver->name ?? '-' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-green-600">تاريخ الاعتماد</span>
                                    <span
                                        class="font-bold">{{ $budget->approved_at ? $budget->approved_at->format('Y-m-d H:i') : '-' }}</span>
                                </div>
                            </div>
                        </div>
                    @endif
                    <!-- Actions -->
                    <div class="flex flex-col gap-3">
                        @if($budget->status === 'submitted' && auth()->user()->can('confirm-budgets'))
                            <form action="{{ route('budgets.confirm', $budget) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="w-full py-4 bg-primary text-white rounded-2xl font-bold font-cairo shadow-lg shadow-blue-100 hover:bg-primary-dark transition-all flex items-center justify-center gap-2">
                                    <i class="fas fa-check"></i> تأكيد وإرسال للمدير العام
                                </button>
                            </form>
                            <form action="{{ route('budgets.reject', $budget) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="w-full py-4 bg-white text-red-600 border-2 border-red-50 rounded-2xl font-bold font-cairo hover:bg-red-50 transition-all flex items-center justify-center gap-2">
                                    <i class="fas fa-times"></i> رفض الموازنة
                                </button>
                            </form>
                        @endif

                        @if($budget->status === 'confirmed' && auth()->user()->can('approve-budgets'))
                            <form action="{{ route('budgets.approve', $budget) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="w-full py-4 bg-green-600 text-white rounded-2xl font-bold font-cairo shadow-lg shadow-green-100 hover:bg-green-700 transition-all flex items-center justify-center gap-2">
                                    <i class="fas fa-check-double"></i> اعتماد نهائي وتحديث الأرصدة
                                </button>
                            </form>
                            <form action="{{ route('budgets.reject', $budget) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="w-full py-4 bg-white text-red-600 border-2 border-red-50 rounded-2xl font-bold font-cairo hover:bg-red-50 transition-all flex items-center justify-center gap-2">
                                    <i class="fas fa-times"></i> رفض الموازنة
                                </button>
                            </form>
                        @endif

                        @if(auth()->user()->can('manage-budgets') || auth()->user()->hasRole('super-admin'))
                            <form action="{{ route('budgets.destroy', $budget) }}" method="POST"
                                onsubmit="return confirm('هل أنت متأكد من رغبتك في حذف هذه الموازنة نهائياً؟ تنبيه: إذا كانت الموازنة معتمدة فسيتم خصم المبالغ من أرصدة الصناديق.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="w-full py-4 bg-white text-red-600 hover:bg-red-50 hover:border-red-600 border border-gray-100 rounded-2xl font-bold font-cairo transition-all flex items-center justify-center gap-2 mt-4 shadow-sm">
                                    <i class="fas fa-trash"></i> حذف الموازنة
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <!-- Items Column -->
                <div class="md:col-span-2 space-y-6">
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-6 border-b border-gray-50">
                            <h3 class="font-bold text-gray-800 font-cairo">بنود الموازنة التفصيلية</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-right">
                                <thead>
                                    <tr class="bg-gray-50 text-gray-500 text-xs font-bold font-cairo h-12">
                                        <th class="px-6">الصندوق</th>
                                        <th class="px-6">المبلغ المطلوب</th>
                                        <th class="px-6">الرصيد الحالي</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @foreach($budget->items as $item)
                                        @php /** @var \App\Models\BudgetItem $item */ @endphp
                                        <tr class="font-almarai h-16 hover:bg-gray-50/50 transition-colors">
                                            <td class="px-6">
                                                <div class="font-bold text-gray-800">{{ $item->fund->name }}</div>
                                            </td>
                                            <td class="px-6">
                                                <div class="font-bold text-primary">
                                                    {{ number_format($item->requested_amount, 2) }} ر.ي
                                                </div>
                                            </td>
                                            <td class="px-6 text-sm text-gray-400">
                                                {{ number_format($item->fund->balance, 2) }} ر.ي
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @if($budget->notes)
                        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
                            <h3 class="font-bold text-gray-800 font-cairo mb-4 flex items-center gap-2">
                                <i class="fas fa-comment-dots text-secondary"></i> ملاحظات
                            </h3>
                            <p class="text-gray-600 font-almarai leading-relaxed">
                                {{ $budget->notes }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @include('partials.print_footer')
    </div>
@endsection
