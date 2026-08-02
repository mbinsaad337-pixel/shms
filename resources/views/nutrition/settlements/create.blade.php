@extends('layouts.nutrition')
@section('title', 'إنشاء تصفية شهرية')

@section('content')
    <div class="p-6 max-w-3xl mx-auto">
        <div class="flex items-center gap-4 mb-6">
            <a href="{{ route('nutrition.settlements.index') }}" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-arrow-right text-xl"></i>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-gray-800 font-cairo">تصفية شهرية جديدة</h2>
                <p class="text-gray-400 text-sm font-almarai">تم حساب القيم تلقائياً من البيانات الفعلية</p>
            </div>
        </div>

        <!-- Auto-calculated Preview -->
        <div class="grid grid-cols-4 gap-4 mb-6">
            <div class="bg-indigo-50 border border-indigo-100 rounded-2xl p-4 text-center">
                <p class="text-[10px] font-bold text-indigo-500 uppercase font-cairo mb-2">إجمالي مديونية الموردين</p>
                <p class="text-2xl font-black text-indigo-700  ">{{ number_format($totalDebt, 2) }}</p>
                <p class="text-xs text-indigo-400 font-cairo">ر.ي</p>
            </div>
            <div class="bg-green-50 border border-green-100 rounded-2xl p-5 text-center">
                <p class="text-[10px] font-bold text-green-500 uppercase font-cairo mb-2">إجمالي الإيرادات</p>
                <p class="text-3xl font-black text-green-700  ">{{ number_format($totalRevenue, 2) }}</p>
                <p class="text-xs text-green-400 font-cairo">ر.ي</p>
            </div>
            <div class="bg-red-50 border border-red-100 rounded-2xl p-5 text-center">
                <p class="text-[10px] font-bold text-red-500 uppercase font-cairo mb-2">إجمالي المصاريف</p>
                <p class="text-3xl font-black text-red-700  ">{{ number_format($totalExpenses, 2) }}</p>
                <p class="text-xs text-red-400 font-cairo">ر.ي</p>
            </div>
            <div
                class="bg-{{ $netResult >= 0 ? 'teal' : 'orange' }}-50 border border-{{ $netResult >= 0 ? 'teal' : 'orange' }}-100 rounded-2xl p-5 text-center">
                <p
                    class="text-[10px] font-bold text-{{ $netResult >= 0 ? 'teal' : 'orange' }}-500 uppercase font-cairo mb-2">
                    صافي النتيجة ({{ $netResult >= 0 ? 'فائض' : 'عجز' }})
                </p>
                <p class="text-3xl font-black text-{{ $netResult >= 0 ? 'teal' : 'orange' }}-700  ">
                    {{ number_format(abs($netResult), 2) }}
                </p>
                <p class="text-xs text-{{ $netResult >= 0 ? 'teal' : 'orange' }}-400 font-cairo">ر.ي</p>
            </div>
        </div>

        <form action="{{ route('nutrition.settlements.store') }}" method="POST">
            @csrf
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="grid grid-cols-2 gap-4 mb-5">
                    <div>
                        <label class="block text-sm font-bold text-gray-600 font-cairo mb-1.5">الشهر</label>
                        <select name="month" required
                            onchange="window.location.href='{{ route('nutrition.settlements.create') }}?month=' + this.value + '&year={{ $currentYear }}'"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 font-cairo text-sm focus:ring-2 focus:ring-pink-400">
                            @foreach([1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل', 5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس', 9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر'] as $n => $name)
                                <option value="{{ $n }}" {{ $currentMonth == $n ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-600 font-cairo mb-1.5">السنة</label>
                        <input type="number" name="year" value="{{ $currentYear }}" required
                            onchange="window.location.href='{{ route('nutrition.settlements.create') }}?month={{ $currentMonth }}&year=' + this.value"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2.5   text-sm focus:ring-2 focus:ring-pink-400">
                    </div>
                </div>

                <!-- Hidden auto-calculated values -->
                <input type="hidden" name="total_revenue" value="{{ $totalRevenue }}">
                <input type="hidden" name="total_expenses" value="{{ $totalExpenses }}">
                <input type="hidden" name="total_debt" value="{{ $totalDebt }}">
                <input type="hidden" name="net_result" value="{{ $netResult }}">
                <input type="hidden" name="result_type" value="{{ $resultType }}">
                @if($budget)
                    <input type="hidden" name="budget_id" value="{{ $budget->id }}">
                @endif

                @if($budget)
                    <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 mb-4">
                        <p class="text-sm text-blue-600 font-cairo font-bold">
                            <i class="fas fa-link ml-1"></i>
                            مرتبطة بميزانية: {{ $budget->title ?? ($budget->month_name . ' ' . $budget->year) }}
                        </p>
                    </div>
                @endif

                <div>
                    <label class="block text-sm font-bold text-gray-600 font-cairo mb-1.5">ملاحظات</label>
                    <textarea name="notes" rows="4" placeholder="أي ملاحظات حول التصفية الشهرية..."
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 font-almarai text-sm focus:ring-2 focus:ring-pink-400"></textarea>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <a href="{{ route('nutrition.settlements.index') }}"
                        class="px-6 py-3 border border-gray-200 rounded-xl font-cairo font-bold text-gray-600">إلغاء</a>
                    <button type="submit"
                        class="px-8 py-3 bg-pink-600 hover:bg-pink-700 text-white rounded-xl font-bold font-cairo shadow-lg shadow-pink-200">
                        <i class="fas fa-paper-plane ml-2"></i> إرسال للاعتماد
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection
