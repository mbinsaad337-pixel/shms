@extends(auth()->user()->hasRole('nutrition-manager') ? 'layouts.nutrition' : 'layouts.app')
@section('title', 'الميزانيات')

@section('content')
    <div class="p-6 max-w-7xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 font-cairo">ميزانيات التغذية</h2>
                <p class="text-gray-400 text-sm font-almarai">إدارة ميزانيات قسم التغذية الشهرية</p>
            </div>
            @hasrole('nutrition-manager')
            <a href="{{ route('nutrition.budgets.create') }}"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-bold font-cairo text-sm transition-all shadow-lg shadow-blue-200">
                <i class="fas fa-plus"></i> ميزانية جديدة
            </a>
            @endhasrole
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th
                            class="text-right px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider font-cairo">
                            الفترة</th>
                        <th
                            class="text-right px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider font-cairo">
                            العنوان</th>
                        <th
                            class="text-right px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider font-cairo">
                            إجمالي الميزانية</th>
                        <th
                            class="text-right px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider font-cairo">
                            المشتركون</th>
                        <th
                            class="text-right px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider font-cairo">
                            اشتراك الطالب (فترة)</th>
                        <th
                            class="text-right px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider font-cairo">
                            اشتراك الطالب (يومي)</th>
                        <th
                            class="text-right px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider font-cairo">
                            الحالة</th>
                        <th class="px-6 py-4"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @if(is_countable($budgets) ? count($budgets) > 0 : (method_exists($budgets, 'count') ? $budgets->count() > 0 : !empty($budgets)))
                        @foreach($budgets as $budget)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <span class="font-bold text-gray-800 font-cairo">{{ $budget->month_name }}
                                            {{ $budget->year }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600 font-cairo text-sm">{{ $budget->title ?? '—' }}</td>
                                    <td class="px-6 py-4">
                                        <span class="font-bold text-blue-700  ">{{ number_format($budget->total_amount, 2) }}
                                            ر.ي</span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-700 font-cairo text-center">{{ $budget->subscribers_count }}</td>
                                    <td class="px-6 py-4">
                                        @if($budget->cost_per_student)
                                            <span class="  text-gray-700 font-bold">{{ number_format($budget->cost_per_student, 2) }}
                                                ر.ي</span>
                                        @else
                                            <span class="text-gray-300">—</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($budget->daily_rate)
                                            <span class="  text-blue-600 font-bold">{{ number_format($budget->daily_rate, 2) }}
                                                ر.ي</span>
                                        @else
                                            <span class="text-gray-300">—</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="px-3 py-1 rounded-full text-xs font-bold
                                                        {{ $budget->status === 'approved' ? 'bg-green-100 text-green-700' :
                                                            ($budget->status === 'submitted' ? 'bg-yellow-100 text-yellow-700' :
                                                            ($budget->status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-600')) }}">
                                            {{ $budget->status_label }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-left">
                                        <div class="flex items-center justify-end gap-3">
                                            <a href="{{ route('nutrition.budgets.show', $budget) }}"
                                                class="text-blue-600 hover:text-blue-800 font-cairo text-sm font-bold flex items-center gap-1">
                                                <i class="fas fa-eye"></i> تفاصيل
                                            </a>
                                            @if(auth()->user()->hasRole('nutrition-manager') && in_array($budget->status, ['draft', 'rejected']))
                                                <a href="{{ route('nutrition.budgets.edit', $budget) }}"
                                                    class="text-amber-600 hover:text-amber-800 font-cairo text-sm font-bold flex items-center gap-1">
                                                    <i class="fas fa-edit"></i> تعديل
                                                </a>
                                            @endif
                                            @if(in_array($budget->status, ['draft', 'rejected']) && auth()->user()->hasRole('nutrition-manager'))
                                                <form action="{{ route('nutrition.budgets.destroy', $budget) }}" method="POST"
                                                    data-confirm="هل أنت متأكد من حذف هذه الميزانية (العهدة)؟">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-500 hover:text-red-700">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7" class="text-center py-16 text-gray-300">
                                <i class="fas fa-file-invoice-dollar text-5xl mb-3 block"></i>
                                <p class="font-cairo">لا توجد ميزانيات حتى الآن</p>
                                <a href="{{ route('nutrition.budgets.create') }}"
                                    class="text-blue-500 hover:underline font-cairo text-sm mt-2 block">إنشاء أول ميزانية</a>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $budgets->links() }}</div>
    </div>
@endsection
