@extends(auth()->user()->hasRole('nutrition-manager') ? 'layouts.nutrition' : 'layouts.app')
@section('title', 'إنشاء فاتورة مشتريات')

@section('content')
    <div class="p-6 max-w-4xl mx-auto">
        <div class="flex items-center gap-4 mb-6">
            <a href="{{ route('nutrition.invoices.index') }}" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-arrow-right text-xl"></i>
            </a>
            <h2 class="text-2xl font-bold text-gray-800 font-cairo">إنشاء فاتورة مشتريات جديدة</h2>
        </div>

        <form action="{{ route('nutrition.invoices.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <!-- Header -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                <h3 class="font-bold text-gray-800 font-cairo mb-4 flex items-center gap-2">
                    <i class="fas fa-info-circle text-orange-500"></i> بيانات الفاتورة
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-600 font-cairo mb-1.5">رقم الفاتورة <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="invoice_number" value="{{ $nextNumber }}" required
                            class="w-full border border-gray-200 rounded-xl px-3 py-2.5   text-sm focus:ring-2 focus:ring-orange-400">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-600 font-cairo mb-1.5">المورد <span
                                class="text-red-500">*</span></label>
                        <select name="supplier_id" required
                            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 font-cairo text-sm focus:ring-2 focus:ring-orange-400">
                            <option value="">— اختر المورد —</option>
                            @foreach($suppliers as $s)
                                <option value="{{ $s->id }}">{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-600 font-cairo mb-1.5">تاريخ الفاتورة <span
                                class="text-red-500">*</span></label>
                        <input type="date" name="invoice_date" value="{{ date('Y-m-d') }}" required
                            class="w-full border border-gray-200 rounded-xl px-3 py-2.5   text-sm focus:ring-2 focus:ring-orange-400">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-600 font-cairo mb-1.5">طريقة الدفع <span
                                class="text-red-500">*</span></label>
                        <select name="payment_type" required
                            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 font-cairo text-sm focus:ring-2 focus:ring-orange-400 bg-orange-50/30">
                            <option value="credit">آجل (ذمم موردين)</option>
                            <option value="cash">نقدي (سند صرف تلقائي)</option>
                        </select>
                    </div>
                    <div class="md:col-span-1">
                        <label class="block text-sm font-bold text-gray-600 font-cairo mb-1.5">مرفق (اختياري)</label>
                        <input type="file" name="attachment" accept="image/*,application/pdf"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm">
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-sm font-bold text-gray-600 font-cairo mb-1.5">ملاحظات</label>
                        <input type="text" name="notes" placeholder="ملاحظات إضافية..."
                            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 font-cairo text-sm focus:ring-2 focus:ring-orange-400">
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-gray-800 font-cairo flex items-center gap-2">
                        <i class="fas fa-list text-orange-500"></i> بنود الفاتورة
                    </h3>
                    <button type="button" onclick="addItem()"
                        class="inline-flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-xl font-bold font-cairo text-sm">
                        <i class="fas fa-plus"></i> إضافة مادة
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full" id="itemsTable">
                        <thead class="bg-gray-50 rounded-xl">
                            <tr>
                                <th class="px-3 py-3 text-right text-xs font-bold text-gray-600 font-cairo">المادة</th>
                                <th class="px-3 py-3 text-center text-xs font-bold text-gray-600 font-cairo w-24">الكمية
                                </th>
                                <th class="px-3 py-3 text-center text-xs font-bold text-gray-600 font-cairo w-20">الوحدة
                                </th>
                                <th class="px-3 py-3 text-center text-xs font-bold text-gray-600 font-cairo w-28">سعر الوحدة
                                </th>
                                <th class="px-3 py-3 text-center text-xs font-bold text-gray-600 font-cairo w-28">الإجمالي
                                </th>
                                <th class="w-10"></th>
                            </tr>
                        </thead>
                        <tbody id="itemsBody" class="divide-y divide-gray-50"></tbody>
                        <tfoot class="bg-orange-50 border-t-2 border-orange-200">
                            <tr>
                                <td colspan="4" class="px-4 py-3 text-right font-black text-orange-800 font-cairo">الإجمالي
                                    الكلي للفاتورة:</td>
                                <td class="px-4 py-3 text-center">
                                    <span id="grandTotal" class="font-black text-orange-800   text-xl">0.00</span>
                                    <span class="text-xs text-orange-500 font-cairo">{{ currency_symbol() }}</span>
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('nutrition.invoices.index') }}"
                    class="px-6 py-3 border border-gray-200 rounded-xl font-cairo font-bold text-gray-600">إلغاء</a>
                <button type="submit"
                    class="px-8 py-3 bg-orange-500 hover:bg-orange-600 text-white rounded-xl font-bold font-cairo shadow-lg shadow-orange-200 transition-all">
                    <i class="fas fa-save ml-2"></i> حفظ الفاتورة
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const budgetItems = @json($budgetItems);
        let idx = 0;

        function addItem() {
            if (budgetItems.length === 0) {
                Swal.fire({ icon: 'error', title: 'خطأ', text: 'لا توجد ميزانية غذائية معتمدة لهذا الشهر لدق قيود المواد.' });
                return;
            }

            const i = idx++;
            const tr = document.createElement('tr');
            tr.id = `item-${i}`;
            tr.className = 'hover:bg-gray-50/50';
            let options = '<option value="">— المادة —</option>';
            budgetItems.forEach(item => {
                options += `<option value="${item.name}">${item.name}</option>`;
            });

            tr.innerHTML = `
                <td class="px-2 py-2">
                    <select name="items[${i}][item_name]" onchange="checkBudget(${i})" required
                        class="w-full border border-gray-200 rounded-lg px-2 py-1.5 text-sm font-cairo focus:ring-2 focus:ring-orange-400">
                        ${options}
                    </select>
                </td>
                <td class="px-2 py-2">
                    <input type="number" name="items[${i}][quantity]" min="0.001" step="0.001" required
                        oninput="calcItem(${i})"
                        class="w-full border border-gray-200 rounded-lg px-2 py-1.5 text-sm   text-center focus:ring-2 focus:ring-orange-400">
                </td>
                <td class="px-2 py-2">
                    <input type="text" name="items[${i}][unit]" placeholder="كجم"
                        class="w-full border border-gray-200 rounded-lg px-2 py-1.5 text-sm font-cairo text-center focus:ring-2 focus:ring-orange-400">
                </td>
                <td class="px-2 py-2">
                    <input type="number" name="items[${i}][unit_price]" min="0" step="0.01" required
                        oninput="calcItem(${i})"
                        class="w-full border border-gray-200 rounded-lg px-2 py-1.5 text-sm   text-center focus:ring-2 focus:ring-orange-400">
                </td>
                <td class="px-2 py-2">
                    <div id="itotal-${i}" class="font-bold text-orange-700   text-sm text-center bg-orange-50 rounded-lg px-2 py-1.5">0.00</div>
                    <input type="hidden" id="override-${i}" value="0">
                </td>
                <td class="px-2 py-2 text-center">
                    <button type="button" onclick="this.closest('tr').remove(); updateGrand();" class="w-7 h-7 bg-red-50 hover:bg-red-100 text-red-400 rounded-lg text-xs"><i class="fas fa-trash"></i></button>
                </td>
            `;
            document.getElementById('itemsBody').appendChild(tr);
        }

        function checkBudget(i) {
            const name = document.querySelector(`select[name="items[${i}][item_name]"]`).value;
            const item = budgetItems.find(b => b.name === name);
            if (!item) return;

            const qty = parseFloat(document.querySelector(`input[name="items[${i}][quantity]"]`).value) || 0;
            const price = parseFloat(document.querySelector(`input[name="items[${i}][unit_price]"]`).value) || 0;
            const currentTotal = qty * price;
            
            const override = document.getElementById(`override-${i}`).value === "1";

            if (currentTotal > item.remaining && !override) {
                 Swal.fire({
                    title: 'تنبيه: تجاوز الميزانية!',
                    html: `<div class="text-right font-cairo text-sm">
                        <p class="mb-2">المادة: <span class="font-bold">${item.name}</span></p>
                        <hr class="my-2 opacity-10">
                        <p class="flex justify-between mb-1"><span>إجمالي الميزانية:</span> <span class=" ">${item.total_budget.toLocaleString()}</span></p>
                        <p class="flex justify-between mb-1"><span>المتبقي المتاح:</span> <span class="  text-emerald-600">${item.remaining.toLocaleString()}</span></p>
                        <p class="flex justify-between text-red-600 font-bold border-t pt-2 mt-2"><span>مبلغ الفاتورة الحالية:</span> <span>${currentTotal.toLocaleString()}</span></p>
                        <p class="mt-4 text-xs text-gray-500 italic">* المبلغ يتجاوز المتبقي في الميزانية المعتمدة.</p>
                    </div>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#f97316',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'سماح بالتجاوز',
                    cancelButtonText: 'تعديل المبلغ'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById(`override-${i}`).value = "1";
                    } else {
                        document.querySelector(`input[name="items[${i}][unit_price]"]`).value = "";
                        calcItem(i);
                    }
                });
            }
        }

        function calcItem(i) {
            const qty = parseFloat(document.querySelector(`[name="items[${i}][quantity]"]`)?.value) || 0;
            const price = parseFloat(document.querySelector(`[name="items[${i}][unit_price]"]`)?.value) || 0;
            const total = (qty * price).toFixed(2);
            document.getElementById(`itotal-${i}`).textContent = total;
            updateGrand();
            
            // Re-check budget logic
            const name = document.querySelector(`select[name="items[${i}][item_name]"]`).value;
            if (name) checkBudget(i);
        }

        function updateGrand() {
            let g = 0;
            document.querySelectorAll('[id^="itotal-"]').forEach(el => {
                g += parseFloat(el.textContent) || 0;
            });
            document.getElementById('grandTotal').textContent = g.toFixed(2);
        }

        addItem();
    </script>
@endpush
