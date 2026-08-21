@extends('layouts.nutrition')
@section('title', 'إنشاء سند')

@section('content')
    <div class="p-6 max-w-2xl mx-auto">
        <div class="flex items-center gap-4 mb-6">
            <a href="{{ route('nutrition.vouchers.index') }}" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-arrow-right text-xl"></i>
            </a>
            <h2 class="text-2xl font-bold text-gray-800 font-cairo">إنشاء سند جديد</h2>
        </div>

        <form action="{{ route('nutrition.vouchers.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">

                <!-- Type Selector -->
                <div class="flex gap-3 mb-6" id="typeSelector">
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="type" value="payment" class="sr-only peer" checked
                            onchange="onTypeChange()">
                        <div
                            class="text-center p-4 border-2 border-gray-100 peer-checked:border-red-400 peer-checked:bg-red-50 rounded-2xl transition-all">
                            <i class="fas fa-arrow-up text-2xl peer-checked:text-red-500 mb-2 block text-gray-300"></i>
                            <p class="font-bold font-cairo peer-checked:text-red-700 text-gray-400 text-sm">سند صرف</p>
                            <p class="text-[10px] text-gray-400 mt-0.5 font-almarai">دفع للمورد</p>
                        </div>
                    </label>
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="type" value="receipt" class="sr-only peer" onchange="onTypeChange()">
                        <div
                            class="text-center p-4 border-2 border-gray-100 peer-checked:border-green-400 peer-checked:bg-green-50 rounded-2xl transition-all">
                            <i class="fas fa-arrow-down text-2xl peer-checked:text-green-500 mb-2 block text-gray-300"></i>
                            <p class="font-bold font-cairo peer-checked:text-green-700 text-gray-400 text-sm">سند قبض</p>
                            <p class="text-[10px] text-gray-400 mt-0.5 font-almarai">تحصيل من الطالب</p>
                        </div>
                    </label>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-600 font-cairo mb-1.5">التاريخ <span
                                class="text-red-500">*</span></label>
                        <input type="date" name="voucher_date" value="{{ date('Y-m-d') }}" required
                            class="w-full border border-gray-200 rounded-xl px-4 py-3   text-sm focus:ring-2 focus:ring-yellow-400">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-600 font-cairo mb-1.5">المبلغ ({{ currency_symbol() }}) <span
                                class="text-red-500">*</span></label>
                        <input type="number" name="amount" step="0.01" min="0.01" required
                            class="w-full border border-gray-200 rounded-xl px-4 py-3   text-sm focus:ring-2 focus:ring-yellow-400"
                            placeholder="0.00">
                    </div>

                    <!-- Supplier (for payment) -->
                    <div id="supplierField">
                        <label class="block text-sm font-bold text-gray-600 font-cairo mb-1.5">المورد</label>
                        <select name="supplier_id"
                            class="w-full border border-gray-200 rounded-xl px-4 py-3 font-cairo text-sm focus:ring-2 focus:ring-yellow-400">
                            <option value="">— اختر المورد —</option>
                            @foreach($suppliers as $s)
                                <option value="{{ $s->id }}">{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Student (for receipt) -->
                    <div id="studentField" class="hidden">
                        <label class="block text-sm font-bold text-gray-600 font-cairo mb-1.5">الطالب</label>
                        <select name="student_id"
                            class="w-full border border-gray-200 rounded-xl px-4 py-3 font-cairo text-sm focus:ring-2 focus:ring-yellow-400">
                            <option value="">— اختر الطالب —</option>
                            @foreach($students as $st)
                                <option value="{{ $st->id }}">{{ $st->name_ar }} - {{ $st->university_id }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-span-2">
                        <label class="block text-sm font-bold text-gray-600 font-cairo mb-1.5">البيان <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="description" required placeholder="وصف العملية المالية..."
                            class="w-full border border-gray-200 rounded-xl px-4 py-3 font-cairo text-sm focus:ring-2 focus:ring-yellow-400">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-bold text-gray-600 font-cairo mb-1.5">مرفق (اختياري)</label>
                        <input type="file" name="attachment" accept="image/*,application/pdf"
                            class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm">
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <a href="{{ route('nutrition.vouchers.index') }}"
                        class="px-6 py-3 border border-gray-200 rounded-xl font-cairo font-bold text-gray-600">إلغاء</a>
                    <button type="submit"
                        class="px-8 py-3 bg-yellow-500 hover:bg-yellow-600 text-white rounded-xl font-bold font-cairo shadow-lg shadow-yellow-200 transition-all">
                        <i class="fas fa-save ml-2"></i> حفظ السند
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        function onTypeChange() {
            const type = document.querySelector('input[name="type"]:checked')?.value;
            document.getElementById('supplierField').className = type === 'payment' ? '' : 'hidden';
            document.getElementById('studentField').className = type === 'receipt' ? '' : 'hidden';
        }
    </script>
@endpush
