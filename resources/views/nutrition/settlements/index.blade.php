@extends (auth()->user()->hasRole('nutrition-manager') ? 'layouts.nutrition' : 'layouts.app')
@section ('title', 'التصفية الشهرية')

@section ('content')
    <div class="p-6 max-w-7xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 font-cairo">التصفية الشهرية</h2>
                <p class="text-gray-400 text-sm font-almarai">الحسابات الختامية لقسم التغذية</p>
            </div>
            @hasrole ('nutrition-manager')
            <a href="{{ route('nutrition.settlements.create') }}"
                class="inline-flex items-center gap-2 bg-pink-600 hover:bg-pink-700 text-white px-5 py-2.5 rounded-xl font-bold font-cairo text-sm shadow-lg shadow-pink-200">
                <i class="fas fa-plus"></i> تصفية جديدة
            </a>
            @endhasrole
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @if($settlements->isNotEmpty())
                @foreach($settlements as $settlement)
                    @php
                        $resultColors = ['surplus' => 'green', 'deficit' => 'red', 'break_even' => 'gray'];
                        $color = $resultColors[$settlement->result_type] ?? 'gray';
                    @endphp
                    <div
                        class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
                        <!-- Header -->
                        <div
                            class="bg-{{ $color }}-50 border-b border-{{ $color }}-100 px-5 py-4 flex items-center justify-between">
                            <div>
                                <p class="font-black text-gray-800 font-cairo text-lg">{{ $settlement->month_name }} {{ $settlement->year }}</p>
                                <p class="text-xs text-gray-400 font-cairo">{{ $settlement->getResultTypeLabel() }}</p>
                            </div>
                            <span class="px-3 py-1 rounded-lg text-xs font-bold
                                                                {{ $settlement->status === 'approved' ? 'bg-green-100 text-green-700' :
                    ($settlement->status === 'submitted' ? 'bg-yellow-100 text-yellow-700' :
                        ($settlement->status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-500')) }}">
                                {{ $settlement->getStatusLabel() }}
                            </span>
                        </div>
                        <!-- Body -->
                        <div class="p-5 space-y-3">
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-400 font-cairo">الإيرادات:</span>
                                <span class="font-bold text-green-700  ">{{ number_format($settlement->total_revenue, 2) }}
                                    ر.ي</span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-400 font-cairo">المصاريف:</span>
                                <span class="font-bold text-red-600  ">{{ number_format($settlement->total_expenses, 2) }} ر.ي</span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-400 font-cairo">إجمالي مديونية الموردين:</span>
                                <span class="font-bold text-indigo-700  ">{{ number_format($settlement->total_debt, 2) }} ر.ي</span>
                            </div>
                            <div class="border-t border-gray-100 pt-3 flex justify-between items-center">
                                <span class="font-bold text-gray-700 font-cairo">الصافي:</span>
                                <span
                                    class="font-black text-{{ $color }}-700   text-lg">{{ number_format($settlement->net_result, 2) }}
                                    ر.ي</span>
                            </div>
                                <div class="flex items-center gap-2 mt-3">
                                    <a href="{{ route('nutrition.settlements.show', $settlement) }}"
                                        class="flex-1 text-center py-2 bg-{{ $color }}-50 hover:bg-{{ $color }}-100 text-{{ $color }}-700 rounded-xl font-bold font-cairo text-sm transition-all">
                                        عرض التفاصيل
                                    </a>
                                    <form action="{{ route('nutrition.settlements.recalculate', $settlement) }}" method="POST">
                                        @csrf
                                        <button type="submit" title="تحديث الأرقام"
                                            class="w-10 h-10 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center transition-all">
                                            <i class="fas fa-sync-alt"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('nutrition.settlements.destroy', $settlement) }}" method="POST"
                                        data-confirm="هل أنت متأكد من حذف هذه التصفية نهائياً؟ لا يمكن التراجع.">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="حذف التصفية"
                                            class="w-10 h-10 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-xl flex items-center justify-center transition-all">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="col-span-3 text-center py-20 text-gray-300">
                    <i class="fas fa-balance-scale text-5xl mb-3 block"></i>
                    <p class="font-cairo">لا توجد تصفيات شهرية بعد</p>
                </div>
            @endif
        </div>
        <div class="mt-6">{{ $settlements->links() }}</div>
    </div>
@endsection
