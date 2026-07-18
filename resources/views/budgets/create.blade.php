@extends('layouts.app')

@section('title', 'طلب عهدة دائرية')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <div class="flex items-center gap-4 mb-8">
                <a href="{{ route('budgets.index') }}"
                    class="w-10 h-10 rounded-xl bg-white shadow-sm flex items-center justify-center text-gray-400 hover:text-primary transition-all">
                    <i class="fas fa-chevron-right"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 font-cairo">طلب عهدة جديدة</h1>
                    <p class="text-gray-500 font-almarai">إعداد موازنة المصاريف المتوقعة لهذا الشهر</p>
                </div>
            </div>

            <form action="{{ route('budgets.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-3 font-cairo text-right">الشهر</label>
                            <select name="month" required
                                class="w-full px-5 py-4 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-primary outline-none font-almarai bg-gray-50/50">
                                @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ date('m') == $i ? 'selected' : '' }}>
                                        {{ date('F', mktime(0, 0, 0, $i, 10)) }}</option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-3 font-cairo text-right">السنة</label>
                            <select name="year" required
                                class="w-full px-5 py-4 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-primary outline-none font-almarai bg-gray-50/50">
                                <option value="{{ date('Y') }}" selected>{{ date('Y') }}</option>
                                <option value="{{ date('Y') + 1 }}">{{ date('Y') + 1 }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 pt-8">
                        <h3 class="text-xl font-bold font-cairo text-gray-800 mb-6">تفاصيل الموازنة حسب الصناديق</h3>

                        <div id="budget-items-container" class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 budget-item">
                                <div class="md:col-span-7">
                                    <label class="block text-xs font-bold text-gray-400 mb-2 font-cairo">الصندوق</label>
                                    <select name="items[0][fund_id]" required
                                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary outline-none">
                                        @foreach($funds as $fund)
                                            <option value="{{ $fund->id }}">{{ $fund->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="md:col-span-4">
                                    <label class="block text-xs font-bold text-gray-400 mb-2 font-cairo">المبلغ
                                        المطلوب</label>
                                    <input type="number" name="items[0][amount]" step="0.01" required
                                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary outline-none text-center font-bold">
                                </div>
                                <div class="md:col-span-1 flex items-end">
                                    <button type="button"
                                        class="w-full h-[48px] text-gray-300 hover:text-red-500 transition-colors">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <button type="button" onclick="addBudgetItem()"
                            class="mt-6 flex items-center gap-2 text-secondary font-bold font-cairo hover:text-orange-600 transition-colors">
                            <i class="fas fa-plus-circle"></i>
                            <span>إضافة بند موازنة آخر</span>
                        </button>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                        class="w-full md:w-auto px-12 py-4 btn-primary text-lg flex items-center justify-center gap-3">
                        <i class="fas fa-paper-plane"></i>
                        <span>إرسال طلب العهدة للمراجعة</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let itemIndex = 1;
        function addBudgetItem() {
            const container = document.getElementById('budget-items-container');
            const newItem = document.createElement('div');
            newItem.className = 'grid grid-cols-1 md:grid-cols-12 gap-4 budget-item pt-4 border-t border-gray-50';
            newItem.innerHTML = `
                <div class="md:col-span-7">
                    <select name="items[${itemIndex}][fund_id]" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary outline-none">
                        @foreach($funds as $fund)
                            <option value="{{ $fund->id }}">{{ $fund->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-4">
                    <input type="number" name="items[${itemIndex}][amount]" step="0.01" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary outline-none text-center font-bold">
                </div>
                <div class="md:col-span-1 flex items-center justify-center">
                    <button type="button" onclick="this.closest('.budget-item').remove()" class="text-red-400 hover:text-red-600 transition-colors">
                        <i class="fas fa-minus-circle"></i>
                    </button>
                </div>
            `;
            container.appendChild(newItem);
            itemIndex++;
        }
    </script>
@endsection
