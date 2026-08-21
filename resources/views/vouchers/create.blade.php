@extends('layouts.app')

@section('title', 'إصدار سند مالي')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <div class="flex items-center gap-4 mb-8">
                <a href="{{ route('vouchers.index') }}"
                    class="w-10 h-10 rounded-xl bg-white shadow-sm flex items-center justify-center text-gray-400 hover:text-primary transition-all">
                    <i class="fas fa-chevron-right"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 font-cairo">إصدار سند جديد</h1>
                    <p class="text-gray-500 font-almarai">قم بتعبئة بيانات السند المالي واختيار نوعه</p>
                </div>
            </div>

            <form action="{{ route('vouchers.store') }}" method="POST" enctype="multipart/form-data"
                class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
                @csrf

                <div class="p-8 space-y-8">
                    <!-- Section 1: Basic Info -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-3 font-cairo text-right">نوع
                                السند</label>
                            <select name="type_display" id="voucher_type" required
                                class="w-full px-5 py-4 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-transparent outline-none font-almarai bg-gray-50/50">
                                <option value="receipt_housing">سند قبض (تسكين)</option>
                                <option value="receipt_deposit">سند قبض (إيداع عام)</option>
                                <option value="payment">سند صرف (دفع)</option>
                                {{-- <option value="transfer">سند تحويل (بين الصناديق)</option> --}}
                                <option value="salary">سند رواتب</option>
                            </select>
                            <input type="hidden" name="type" id="hidden_type" value="receipt">
                            <input type="hidden" name="sub_type" id="hidden_sub_type" value="housing">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-3 font-cairo text-right">التاريخ</label>
                            <input type="date" name="date" value="{{ date('Y-m-d') }}" required
                                class="w-full px-5 py-4 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-primary outline-none font-almarai bg-gray-50/50">
                        </div>
                    </div>

                    <!-- Section 2: Fund and Amount -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 pt-6 border-t border-gray-50">
                        <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label id="fund_label"
                                    class="block text-sm font-bold text-gray-700 mb-3 font-cairo text-right">الصندوق
                                    الأساسي</label>
                                <select name="fund_id" required
                                    class="w-full px-5 py-4 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-primary outline-none font-almarai bg-gray-50/50">
                                    @foreach($funds as $fund)
                                        <option value="{{ $fund->id }}">{{ $fund->name }}
                                            ({{ number_format($fund->balance, 2) }} {{ $fund->currency_symbol }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div id="target_fund_container" class="hidden">
                                <label class="block text-sm font-bold text-gray-700 mb-3 font-cairo text-right">الصندوق
                                    المحول إليه</label>
                                <select name="target_fund_id"
                                    class="w-full px-5 py-4 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-primary outline-none font-almarai bg-gray-50/50">
                                    <option value="">اختر الصندوق</option>
                                    @foreach($funds as $fund)
                                        <option value="{{ $fund->id }}">{{ $fund->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-3 font-cairo text-right">المبلغ</label>
                            <input type="number" name="amount" step="0.01" placeholder="0.00" required
                                class="w-full px-5 py-4 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-primary outline-none   text-xl font-bold text-primary bg-gray-50/50 text-center">
                        </div>
                    </div>

                    <!-- Section 3: Payee/Payer -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-6 border-t border-gray-50">
                        <div id="student_selection_container" class="hidden">
                            <label class="block text-sm font-bold text-gray-700 mb-3 font-cairo text-right text-primary">المسدد (الطالب)</label>
                            <select name="student_id" id="student_id"
                                class="w-full px-5 py-4 rounded-2xl border border-primary/20 focus:ring-2 focus:ring-primary outline-none font-almarai bg-blue-50/10">
                                <option value="">--- اختر الطالب ---</option>
                                @foreach($students as $student)
                                    <option value="{{ $student->id }}" data-name="{{ $student->name_ar }}" data-remaining="{{ $student->remaining_fees }}">
                                        {{ $student->name_ar }} (المتبقي: {{ number_format($student->remaining_fees, 2) }} {{ $student->annual_fee_currency_symbol }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label id="payee_label"
                                class="block text-sm font-bold text-gray-700 mb-3 font-cairo text-right">المستفيد /
                                المصدر</label>
                            <input type="text" name="payee_or_payer" id="payee_or_payer" required
                                class="w-full px-5 py-4 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-primary outline-none font-almarai bg-gray-50/50"
                                placeholder="اسم الشخص أو الجهة">
                        </div>
                        <div id="attachment_container">
                            <label class="block text-sm font-bold text-gray-700 mb-3 font-cairo text-right">المرفق
                                (اختياري)</label>
                            <div class="relative">
                                <input type="file" name="attachment" class="hidden" id="voucher_file"
                                    onchange="updateFileName(this)">
                                <label for="voucher_file"
                                    class="flex items-center justify-center gap-3 w-full px-5 py-4 rounded-2xl border-2 border-dashed border-gray-200 hover:border-primary hover:bg-blue-50/30 transition-all cursor-pointer">
                                    <i class="fas fa-cloud-upload-alt text-gray-400"></i>
                                    <span id="file_name" class="text-gray-500 font-almarai">ارفاق صورة السند أو
                                        الفاتورة</span>
                                </label>
                            </div>
                            @error('attachment') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Section 4: Description -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-3 font-cairo text-right">الوصف /
                            الغرض</label>
                        <textarea name="description" required rows="3"
                            class="w-full px-5 py-4 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-primary outline-none font-almarai bg-gray-50/50"
                            placeholder="اكتب تفاصيل إضافية للعملية..."></textarea>
                    </div>
                </div>

                <div class="p-8 bg-gray-50/80 border-t border-gray-100 flex justify-end">
                    <button type="submit"
                        class="w-full md:w-auto px-12 py-4 btn-primary text-lg flex items-center justify-center gap-3">
                        <i class="fas fa-check-circle"></i>
                        <span>تأكيد وحفظ السند</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const typeSelect = document.getElementById('voucher_type');
        const hiddenType = document.getElementById('hidden_type');
        const hiddenSubType = document.getElementById('hidden_sub_type');
        const targetFundContainer = document.getElementById('target_fund_container');
        const studentSelectionContainer = document.getElementById('student_selection_container');
        const fundLabel = document.getElementById('fund_label');
        const payeeLabel = document.getElementById('payee_label');
        const amountInput = document.querySelector('input[name="amount"]');
        const fundSelect = document.querySelector('select[name="fund_id"]');
        const studentSelect = document.getElementById('student_id');
        const payeeInput = document.getElementById('payee_or_payer');
        const form = document.querySelector('form');

        // Store fund balances in JS
        const fundBalances = {
            @foreach($funds as $fund)
                "{{ $fund->id }}": {{ (float)$fund->balance }},
            @endforeach
        };
        const fundCurrencies = {
            @foreach($funds as $fund)
                "{{ $fund->id }}": "{{ $fund->currency_symbol }}",
            @endforeach
        };
        function checkBalance() {
    const type = typeSelect.value;
    const fundId = fundSelect.value;
    const amount = parseFloat(amountInput.value) || 0;
    const balance = parseFloat(fundBalances[fundId]) || 0;
    const currencySymbol = fundCurrencies[fundId] || '{{ currency_symbol() }}';

    // إزالة التنبيه السابق
    amountInput.classList.remove(
        'border-red-500',
        'bg-red-50',
        'border-yellow-500',
        'bg-yellow-50'
    );

    const existingError = document.getElementById('balance-error');
    if (existingError) existingError.remove();

    if (['payment', 'salary', 'transfer'].includes(type) && amount > balance) {

        const deficit = amount - balance;
        const newBalance = balance - amount; // سيكون سالباً

        amountInput.classList.add('border-yellow-500', 'bg-yellow-50');

        const warning = document.createElement('div');
        warning.id = 'balance-error';
        warning.className =
            'text-yellow-700 text-xs mt-2 font-almarai font-bold leading-6';

        warning.innerHTML = `
            ⚠️ تنبيه: هذه العملية ستؤدي إلى عجز في الصندوق.<br>
            الرصيد الحالي: <strong>${balance.toLocaleString()} ${currencySymbol}</strong><br>
            قيمة العجز: <strong style="color:#dc2626;">-${deficit.toLocaleString()} ${currencySymbol}</strong><br>
            الرصيد بعد الحفظ:
            <strong style="color:#dc2626;">${newBalance.toLocaleString()} ${currencySymbol}</strong>
        `;

        amountInput.parentNode.appendChild(warning);

        // السماح بالحفظ
        return true;
    }

    return true;
}

        // function checkBalance() {
        //     const type = typeSelect.value;
        //     const fundId = fundSelect.value;
        //     const amount = parseFloat(amountInput.value) || 0;
        //     const balance = fundBalances[fundId] || 0;

        //     // Remove existing warnings
        //     amountInput.classList.remove('border-red-500', 'bg-red-50');
        //     const existingError = document.getElementById('balance-error');
        //     if (existingError) existingError.remove();

        //     if (['payment', 'salary', 'transfer'].includes(type) && amount > balance) {
        //         amountInput.classList.add('border-red-500', 'bg-red-50');
        //         const errorDiv = document.createElement('div');
        //         errorDiv.id = 'balance-error';
        //         errorDiv.className = 'text-red-500 text-xs mt-2 font-almarai font-bold';
        //     errorDiv.innerText = '⚠️ الرصيد غير كافٍ! الرصيد المتاح: ' + balance.toLocaleString() + ' {{ currency_symbol() }}';
        //         amountInput.parentNode.appendChild(errorDiv);
        //         return false;
        //     }
        //     return true;
        // }

        typeSelect.addEventListener('change', function () {
            const val = this.value;

            // Defaults
            targetFundContainer.classList.add('hidden');
            studentSelectionContainer.classList.add('hidden');
            fundLabel.innerText = 'الصندوق الأساسي';
            payeeLabel.innerText = 'المستفيد / المصدر';
            hiddenSubType.value = '';

            if (val === 'transfer') {
                targetFundContainer.classList.remove('hidden');
                fundLabel.innerText = 'من صندوق';
                payeeLabel.innerText = 'سبب التحويل / المسئول';
                hiddenType.value = 'transfer';
            } else if (val === 'receipt_housing') {
                studentSelectionContainer.classList.remove('hidden');
                payeeLabel.innerText = 'اسم الطالب (المسدد)';
                hiddenType.value = 'receipt';
                hiddenSubType.value = 'housing';
            } else if (val === 'receipt_deposit') {
                payeeLabel.innerText = 'اسم المودع / جهة الإيداع';
                hiddenType.value = 'receipt';
                hiddenSubType.value = 'deposit';
            } else if (val === 'salary') {
                hiddenType.value = 'salary';
            } else if (val === 'payment') {
                hiddenType.value = 'payment';
            }
            
            checkBalance();
        });

        // Initialize values on load
        window.addEventListener('load', () => {
             typeSelect.dispatchEvent(new Event('change'));
        });

        studentSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.value) {
                payeeInput.value = selectedOption.getAttribute('data-name');
            }
        });

        fundSelect.addEventListener('change', checkBalance);
        amountInput.addEventListener('input', checkBalance);

        form.addEventListener('submit', function(e) {
            if (!checkBalance()) {
                e.preventDefault();
                alert('عذراً، لا يمكن إتمام العملية لأن المبلغ المطلوب أكبر من رصيد الصندوق المختار.');
            }
        });

        function updateFileName(input) {
            if (input.files && input.files[0]) {
                document.getElementById('file_name').innerText = input.files[0].name;
                document.getElementById('file_name').classList.remove('text-gray-500');
                document.getElementById('file_name').classList.add('text-primary', 'font-bold');
            }
        }
    </script>
@endsection