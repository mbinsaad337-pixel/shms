@extends('layouts.app')

@section('content')
        <div class="mb-8 flex justify-between items-center bg-white p-6 rounded-2xl border-l-8 border-gold shadow-sm">
            <div>
                <h1 class="text-3xl font-black text-navy font-cairo">طلبات الاعتماد المعلقة</h1>
                <p class="text-gray-400 font-almarai text-sm mt-1">مراجعة والبت في طلبات العهد والتصفيات المالية للمراكز</p>
            </div>
            <div class="flex gap-4">
                <span class="px-4 py-2 bg-navy/5 text-navy rounded-xl text-xs font-black font-cairo border border-navy/10 flex items-center gap-2">
                    <i class="fas fa-clock text-gold animate-pulse"></i>
                    قيد الانتظار
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-8">
            <!-- Budgets -->
            <div class="card-premium p-8 border-t-8 border-t-navy">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-navy/10 rounded-xl flex items-center justify-center text-navy shadow-sm">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                    <h3 class="text-xl font-black text-navy font-cairo">طلبات العهد الشهرية</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="text-right text-gray-400 uppercase text-[10px] font-black tracking-widest font-cairo bg-gray-50">
                                <th class="px-6 py-4 rounded-r-xl">المركز</th>
                                <th class="px-6 py-4">الشهر/السنة</th>
                                <th class="px-6 py-4">المبلغ الإجمالي</th>
                                <th class="px-6 py-4">بواسطة</th>
                                <th class="px-6 py-4 text-center rounded-l-xl">الإجراء</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(is_countable($pendingBudgets) ? count($pendingBudgets) > 0 : (method_exists($pendingBudgets, 'count') ? $pendingBudgets->count() > 0 : !empty($pendingBudgets)))
    @foreach($pendingBudgets as $budget)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-4 font-almarai">{{ $budget->center->name }}</td>
                                    <td class="py-4 font-almarai">{{ $budget->month }}/{{ $budget->year }}</td>
                                    <td class="py-4 font-bold text-primary font-almarai text-lg">
                                        {{ number_format($budget->total_amount, 2) }}</td>
                                    <td class="py-4 font-almarai text-sm">{{ $budget->submitter->name }}</td>
                                    <td class="py-4 flex items-center gap-2">
                                        <a href="{{ route('budgets.show', $budget) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm font-cairo shadow-sm transition-all flex items-center gap-1">
                                            <i class="fas fa-eye"></i> عرض
                                        </a>
                                        <form action="{{ route('executive.budgets.approve', $budget) }}" method="POST" class="inline">
                                            @csrf
                                            <button
                                                class="bg-green-500 hover:bg-green-600 text-white px-4 py-1 rounded text-sm font-cairo shadow-sm transition-all">اعتماد</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
@else
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-gray-400 font-almarai">لا توجد طلبات اعتماد عهد
                                        حالياً</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Settlements -->
            <div class="card-premium p-8 border-t-8 border-t-gold">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-gold/10 rounded-xl flex items-center justify-center text-gold shadow-sm">
                        <i class="fas fa-file-signature"></i>
                    </div>
                    <h3 class="text-xl font-black text-navy font-cairo">طلبات التصفية الشهرية</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="text-right text-gray-500 border-b">
                                <th class="pb-3 font-cairo">المركز</th>
                                <th class="pb-3 font-cairo">الشهر/السنة</th>
                                <th class="pb-3 font-cairo">المصروف الفعلي</th>
                                <th class="pb-3 font-cairo">المتبقي</th>
                                <th class="pb-3 font-cairo">الإجراء</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(is_countable($pendingSettlements) ? count($pendingSettlements) > 0 : (method_exists($pendingSettlements, 'count') ? $pendingSettlements->count() > 0 : !empty($pendingSettlements)))
    @foreach($pendingSettlements as $settlement)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-4 font-almarai">{{ $settlement->center->name }}</td>
                                    <td class="py-4 font-almarai">{{ $settlement->month }}/{{ $settlement->year }}</td>
                                    <td class="py-4 font-bold text-red-600 font-almarai">
                                        {{ number_format($settlement->total_spent, 2) }}</td>
                                    <td class="py-4 font-bold text-green-600 font-almarai">
                                        {{ number_format($settlement->total_remaining, 2) }}</td>
                                    <td class="py-4">
                                        <div class="flex items-center gap-2">
                                            <a href="#" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm font-cairo shadow-sm transition-all flex items-center gap-1">
                                                <i class="fas fa-eye"></i> عرض
                                            </a>
                                            <form action="{{ route('executive.settlements.approve', $settlement) }}" method="POST" class="inline">
                                                @csrf
                                                <button class="bg-green-500 hover:bg-green-600 text-white px-4 py-1 rounded text-sm font-cairo shadow-sm transition-all">اعتماد</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
@else
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-gray-400 font-almarai">لا توجد طلبات تصفية
                                        حالياً</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
