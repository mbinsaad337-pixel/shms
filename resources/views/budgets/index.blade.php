@extends('layouts.app')

@section('title', 'إدارة العهد والموازنات')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="mb-8 flex justify-between items-center bg-white p-6 rounded-2xl border-l-8 border-gold shadow-sm">
            <div>
                <h1 class="text-3xl font-black text-navy font-cairo">العهد والموازنات الشهرية</h1>
                <p class="text-gray-400 font-almarai text-sm mt-1">إدارة طلبات الموازنة والتصفيات الشهرية للمركز</p>
            </div>
            <div class="flex items-center gap-4">
                @if(auth()->user()->hasRole('super-admin'))
                    <form action="{{ route('budgets.index') }}" method="GET" class="flex items-center gap-2">
                        <select name="center_id" onchange="this.form.submit()" 
                            class="px-4 py-2 rounded-xl border border-gray-100 bg-gray-50 focus:bg-white focus:border-gold outline-none text-sm font-cairo">
                            <option value="">كل المراكز</option>
                            @foreach($centers as $center)
                                <option value="{{ $center->id }}" {{ request('center_id') == $center->id ? 'selected' : '' }}>
                                    {{ $center->name }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                @endif

                @if(!auth()->user()->hasRole('super-admin'))
                <a href="{{ route('budgets.create') }}"
                    class="px-6 py-3 bg-navy text-white rounded-xl hover:bg-navy/90 shadow-lg font-cairo font-bold transition-all transform hover:-translate-y-1 flex items-center gap-2">
                    <i class="fas fa-file-invoice-dollar text-gold"></i>
                    <span>طلب عهدة جديدة</span>
                </a>
                @endif
            </div>
        </div>

        <!-- Budgets Table -->
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-right">
                    <thead>
                        <tr class="bg-gray-50 text-gray-600">
                            <th class="px-6 py-4 font-cairo text-sm">الفترة</th>
                            <th class="px-6 py-4 font-cairo text-sm">المبلغ الإجمالي</th>
                            <th class="px-6 py-4 font-cairo text-sm">بواسطة</th>
                            <th class="px-6 py-4 font-cairo text-sm">الحالة</th>
                            <th class="px-6 py-4 font-cairo text-sm">تاريخ الطلب</th>
                            <th class="px-6 py-4 font-cairo text-sm">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @if(is_countable($budgets) ? count($budgets) > 0 : (method_exists($budgets, 'count') ? $budgets->count() > 0 : !empty($budgets)))
                            @foreach($budgets as $budget)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 font-bold text-gray-800">
                                    {{ $budget->month }} / {{ $budget->year }}
                                </td>
                                <td class="px-6 py-4 font-bold text-primary">
                                    {{ number_format($budget->total_amount, 2) }} ر.ي
                                </td>
                                <td class="px-6 py-4 text-sm font-almarai">
                                    {{ $budget->submitter->name ?? 'غير محدد' }}
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $statuses = [
                                            'submitted' => ['label' => 'قيد مراجعة المدير', 'color' => 'bg-yellow-100 text-yellow-700'],
                                            'confirmed' => ['label' => 'بانتظار مدير قسم المراكز الطلابية', 'color' => 'bg-blue-100 text-blue-700'],
                                            'approved' => ['label' => 'تم الاعتماد النهائي', 'color' => 'bg-green-100 text-green-700'],
                                            'rejected' => ['label' => 'مرفوض', 'color' => 'bg-red-100 text-red-700'],
                                        ];
                                        $statusInfo = $statuses[$budget->status] ?? ['label' => $budget->status, 'color' => 'bg-gray-100'];
                                    @endphp
                                    <span
                                        class="{{ $statusInfo['color'] }} px-3 py-1 rounded-full text-xs font-bold font-almarai">
                                        {{ $statusInfo['label'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $budget->created_at->format('Y-m-d') }}
                                </td>
                                <td class="px-6 py-4 text-left flex items-center justify-end gap-2">
                                    <a href="{{ route('budgets.show', $budget) }}"
                                        class="bg-blue-600 text-white hover:bg-blue-700 px-4 py-2 rounded-xl flex items-center gap-2 text-xs font-bold font-cairo shadow-sm transition-all whitespace-nowrap">
                                        <i class="fas fa-eye"></i>
                                        <span>عرض التفاصيل</span>
                                    </a>

                                    <a href="{{ route('budgets.export-pdf', $budget) }}" title="تحميل PDF"
                                        class="bg-red-600 text-white hover:bg-red-700 px-3 py-2 rounded-xl flex items-center gap-1 text-xs font-bold font-cairo shadow-sm transition-all whitespace-nowrap">
                                        <i class="fas fa-file-pdf"></i>
                                        <span>PDF</span>
                                    </a>

                                    @if($budget->status === 'submitted' && auth()->user()->can('confirm-budgets'))
                                        <form action="{{ route('budgets.confirm', $budget) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit"
                                                class="bg-primary text-white hover:opacity-90 px-3 py-2 rounded-xl text-xs font-bold font-cairo shadow-sm flex items-center gap-1">
                                                <i class="fas fa-check"></i> تأكيد
                                            </button>
                                        </form>
                                        <form action="{{ route('budgets.reject', $budget) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit"
                                                class="bg-red-600 text-white hover:bg-red-700 px-3 py-2 rounded-xl text-xs font-bold font-cairo shadow-sm flex items-center gap-1">
                                                <i class="fas fa-times"></i> رفض
                                            </button>
                                        </form>
                                    @endif

                                    @if($budget->status === 'confirmed' && auth()->user()->can('approve-budgets'))
                                        <form action="{{ route('budgets.approve', $budget) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit"
                                                class="bg-green-600 text-white hover:bg-green-700 px-3 py-2 rounded-xl text-xs font-bold font-cairo shadow-sm flex items-center gap-1">
                                                <i class="fas fa-check-double"></i> اعتماد
                                            </button>
                                        </form>
                                        <form action="{{ route('budgets.reject', $budget) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit"
                                                class="bg-red-600 text-white hover:bg-red-700 px-3 py-2 rounded-xl text-xs font-bold font-cairo shadow-sm flex items-center gap-1">
                                                <i class="fas fa-times"></i> رفض
                                            </button>
                                        </form>
                                    @endif

                                    @if(auth()->user()->can('manage-budgets') || auth()->user()->hasRole('super-admin'))
                                        <form action="{{ route('budgets.destroy', $budget) }}" method="POST" class="inline"
                                            data-confirm="هل أنت متأكد من رغبتك في حذف هذه الموازنة نهائياً؟ تنبيه: إذا كانت الموازنة معتمدة فسيتم خصم المبالغ من أرصدة الصناديق.">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="bg-gray-100 text-red-600 hover:bg-red-50 px-3 py-2 rounded-xl text-xs font-bold font-cairo shadow-sm flex items-center gap-1 transition-colors">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-400 font-almarai">لا توجد طلبات موازنة
                                    حالياً</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                {{ $budgets->links() }}
            </div>
        </div>
    </div>
@endsection
