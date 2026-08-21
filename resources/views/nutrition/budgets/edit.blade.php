@extends(auth()->user()->hasRole('nutrition-manager') ? 'layouts.nutrition' : 'layouts.app')
@section('title', 'تعديل الميزانية')

@section('content')
    <div class="p-6 max-w-5xl mx-auto">
        <div class="flex items-center gap-4 mb-6">
            <a href="{{ route('nutrition.budgets.index') }}" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-arrow-right text-xl"></i>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-gray-800 font-cairo">تعديل ميزانية التغذية</h2>
                <p class="text-gray-400 text-sm font-almarai">تحديث بنود الميزانية للشهر المختار</p>
            </div>
            @if($budget->status === 'approved')
                <div class="mr-auto bg-amber-50 text-amber-700 px-4 py-2 rounded-xl border border-amber-200 flex items-center gap-2 text-sm font-bold font-cairo">
                    <i class="fas fa-exclamation-triangle"></i>
                    تنبيه: أنت تقوم بتعديل ميزانية معتمدة مسبقاً.
                </div>
            @endif
        </div>

        <form action="{{ route('nutrition.budgets.update', $budget) }}" method="POST" id="budgetForm">
            @csrf
            @method('PUT')
            
            <!-- Header Info -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                <h3 class="font-bold text-navy font-cairo mb-4 flex items-center gap-2">
                    <i class="fas fa-info-circle text-gold"></i> معلومات الميزانية والاشتراكات
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-600 font-cairo mb-1.5">الشهر <span
                                class="text-red-500">*</span></label>
                        <select name="month" required
                            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-navy font-cairo text-sm">
                            @foreach([1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل', 5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس', 9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر'] as $num => $name)
                                <option value="{{ $num }}" {{ $budget->month == $num ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-600 font-cairo mb-1.5">السنة <span
                                class="text-red-500">*</span></label>
                        <input type="number" name="year" value="{{ $budget->year }}" required
                            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-navy   text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-600 font-cairo mb-1.5">عدد أيام الفترة <span
                                class="text-red-500">*</span></label>
                        <input type="number" name="days_count" id="daysCount" value="{{ $budget->days_count }}" min="1" required
                            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-navy   text-sm">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-gray-600 font-cairo mb-1.5">عنوان الميزانية</label>
                        <input type="text" name="title" value="{{ $budget->title }}" placeholder="مثال: ميزانية التغذية لشهر مارس 2026"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-navy font-cairo text-sm">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-gray-600 font-cairo mb-1.5">عدد المشتركين</label>
                        <input type="number" name="subscribers_count" id="subscribersCount" min="0" value="{{ $budget->subscribers_count }}" readonly
                            class="w-full border border-gray-100 bg-gray-50 rounded-xl px-3 py-2.5   text-sm text-gray-500">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-gold font-cairo mb-1.5">قيمة الاشتراك الشهري (ثابت) <span class="text-red-500">*</span></label>
                        <input type="number" name="cost_per_student" id="costPerStudent" step="0.01" min="0" value="{{ $budget->cost_per_student }}" required
                            class="w-full border border-gold/30 bg-gold/5 rounded-xl px-4 py-3 focus:ring-2 focus:ring-gold   text-lg font-bold shadow-sm"
                            placeholder="0.00">
                        <p class="text-[10px] text-gray-400 mt-1 font-cairo">هذا المبلغ سيحتسب للطلاب المشتركين بالنظام الشهري</p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-navy font-cairo mb-1.5">قيمة اليوم الواحد (للاشتراك اليومي) <span class="text-red-500">*</span></label>
                        <input type="number" name="daily_rate" id="dailyRate" step="0.01" min="0" value="{{ $budget->daily_rate }}" required
                            class="w-full border border-navy/20 bg-navy/5 rounded-xl px-4 py-3 focus:ring-2 focus:ring-navy   text-lg font-bold shadow-sm"
                            placeholder="0.00">
                        <p class="text-[10px] text-gray-400 mt-1 font-cairo">هذا السعر سيضرب في عدد الأيام للطلاب المشتركين بالنظام اليومي</p>
                    </div>

                    <div class="md:col-span-4 mt-2">
                        <label class="block text-sm font-bold text-red-600 font-cairo mb-1.5">آخر تاريخ لدفع الاشتراك</label>
                        <input type="date" name="last_payment_date" value="{{ $budget->last_payment_date ? $budget->last_payment_date->format('Y-m-d') : '' }}"
                            class="w-full border border-red-100 bg-red-50 rounded-xl px-4 py-3 focus:ring-2 focus:ring-red-400   text-lg text-center font-bold">
                    </div>
                </div>
            </div>

            <!-- Budget Lines Table -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-navy font-cairo flex items-center gap-2">
                        <i class="fas fa-table text-gold"></i> بنود الميزانية (المصروفات تقديرية)
                    </h3>
                    <button type="button" onclick="addRow()"
                        class="inline-flex items-center gap-2 bg-navy hover:bg-[#083358] text-white px-4 py-2 rounded-xl font-bold font-cairo text-sm transition-all shadow-md">
                        <i class="fas fa-plus"></i> إضافة بند
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full" id="linesTable">
                        <thead class="bg-gray-50 border border-gray-100 rounded-xl">
                            <tr>
                                <th class="px-3 py-3 text-right text-xs font-bold text-gray-600 font-cairo">البيان</th>
                                <th class="px-3 py-3 text-right text-xs font-bold text-gray-600 font-cairo w-24">عدد الأيام
                                </th>
                                <th class="px-3 py-3 text-right text-xs font-bold text-gray-600 font-cairo w-24">الكمية</th>
                                <th class="px-3 py-3 text-right text-xs font-bold text-gray-600 font-cairo w-28">سعر الوحدة
                                </th>
                                <th class="px-3 py-3 text-right text-xs font-bold text-gray-600 font-cairo w-28">الإجمالي
                                </th>
                                <th class="px-3 py-3 text-right text-xs font-bold text-gray-600 font-cairo">المورد</th>
                                <th class="w-10"></th>
                            </tr>
                        </thead>
                        <tbody id="linesBody" class="divide-y divide-gray-50">
                            @foreach($budget->lines as $i => $line)
                                <tr class="hover:bg-gray-50/50" id="row-{{ $i }}">
                                    <td class="px-2 py-2">
                                        <input type="text" name="lines[{{ $i }}][item_name]" value="{{ $line->item_name }}" placeholder="اسم المادة الغذائية" required
                                            class="w-full border border-gray-200 rounded-lg px-2 py-1.5 text-sm font-cairo focus:ring-2 focus:ring-navy">
                                    </td>
                                    <td class="px-2 py-2">
                                        <input type="number" name="lines[{{ $i }}][days]" value="{{ $line->days }}" placeholder="—" min="0" step="1"
                                            onchange="calcRow({{ $i }})" oninput="calcRow({{ $i }})"
                                            class="w-full border border-gray-200 rounded-lg px-2 py-1.5 text-sm   text-center focus:ring-2 focus:ring-navy">
                                    </td>
                                    <td class="px-2 py-2">
                                        <input type="number" name="lines[{{ $i }}][quantity]" value="{{ $line->quantity }}" placeholder="—" min="0" step="0.001"
                                            onchange="calcRow({{ $i }})" oninput="calcRow({{ $i }})"
                                            class="w-full border border-gray-200 rounded-lg px-2 py-1.5 text-sm   text-center focus:ring-2 focus:ring-navy">
                                    </td>
                                    <td class="px-2 py-2">
                                        <input type="number" name="lines[{{ $i }}][unit_price]" value="{{ $line->unit_price }}" placeholder="0.00" min="0" step="0.01"
                                            onchange="calcRow({{ $i }})" oninput="calcRow({{ $i }})"
                                            class="w-full border border-gray-200 rounded-lg px-2 py-1.5 text-sm   text-center focus:ring-2 focus:ring-navy">
                                    </td>
                                    <td class="px-2 py-2">
                                        <div id="total-{{ $i }}" class="font-bold text-green-700   text-sm text-center bg-green-50 rounded-lg px-2 py-1.5 min-w-[70px]">{{ number_format($line->total, 2, '.', '') }}</div>
                                    </td>
                                    <td class="px-2 py-2">
                                        <select name="lines[{{ $i }}][supplier_name]"
                                            class="w-full border border-gray-200 rounded-lg px-2 py-1.5 text-sm font-cairo focus:ring-2 focus:ring-navy">
                                            <option value="">— اختر المورد —</option>
                                            @foreach($suppliers as $s)
                                                <option value="{{ $s->name }}" {{ $line->supplier_name == $s->name ? 'selected' : '' }}>{{ $s->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-2 py-2 text-center">
                                        <button type="button" onclick="removeRow({{ $i }})" class="w-7 h-7 bg-red-50 hover:bg-red-100 text-red-400 rounded-lg transition-all text-xs">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-blue-50 border-t-2 border-blue-200">
                                <td colspan="4" class="px-4 py-3 text-right font-black text-navy font-cairo text-sm">
                                    <i class="fas fa-sigma ml-1"></i> إجمالي الميزانية (المصروفات المتوقعة):
                                </td>
                                <td class="px-4 py-3">
                                    <span id="grandTotal" class="font-black text-navy   text-lg">{{ number_format($budget->total_amount, 2, '.', '') }}</span>
                                    <span class="text-xs text-navy font-cairo">{{ currency_symbol() }}</span>
                                </td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('nutrition.budgets.index') }}"
                    class="px-6 py-3 border border-gray-200 rounded-xl font-cairo font-bold text-gray-600 hover:bg-gray-50 transition-all">
                    إلغاء
                </a>
                <button type="submit"
                    class="px-8 py-3 bg-navy hover:bg-[#083358] text-white rounded-xl font-bold font-cairo shadow-lg shadow-navy/20 transition-all">
                    <i class="fas fa-check-circle ml-2"></i> حفظ التعديلات
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        const suppliers = @json($suppliers);
        let rowIndex = {{ $budget->lines->count() }};

        function addRow() {
            const tbody = document.getElementById('linesBody');
            const i = rowIndex++;
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-gray-50/50';
            tr.id = `row-${i}`;

            let supplierOptions = '<option value="">— اختر المورد —</option>';
            suppliers.forEach(s => {
                supplierOptions += `<option value="${s.name}">${s.name}</option>`;
            });

            tr.innerHTML = `
                <td class="px-2 py-2">
                    <input type="text" name="lines[${i}][item_name]" placeholder="اسم المادة الغذائية" required
                        class="w-full border border-gray-200 rounded-lg px-2 py-1.5 text-sm font-cairo focus:ring-2 focus:ring-navy">
                </td>
                <td class="px-2 py-2">
                    <input type="number" name="lines[${i}][days]" placeholder="—" min="0" step="1"
                        onchange="calcRow(${i})" oninput="calcRow(${i})"
                        class="w-full border border-gray-200 rounded-lg px-2 py-1.5 text-sm   text-center focus:ring-2 focus:ring-navy">
                </td>
                <td class="px-2 py-2">
                    <input type="number" name="lines[${i}][quantity]" placeholder="—" min="0" step="0.001"
                        onchange="calcRow(${i})" oninput="calcRow(${i})"
                        class="w-full border border-gray-200 rounded-lg px-2 py-1.5 text-sm   text-center focus:ring-2 focus:ring-navy">
                </td>
                <td class="px-2 py-2">
                    <input type="number" name="lines[${i}][unit_price]" placeholder="0.00" min="0" step="0.01"
                        onchange="calcRow(${i})" oninput="calcRow(${i})"
                        class="w-full border border-gray-200 rounded-lg px-2 py-1.5 text-sm   text-center focus:ring-2 focus:ring-navy">
                </td>
                <td class="px-2 py-2">
                    <div id="total-${i}" class="font-bold text-green-700   text-sm text-center bg-green-50 rounded-lg px-2 py-1.5 min-w-[70px]">0.00</div>
                </td>
                <td class="px-2 py-2">
                    <select name="lines[${i}][supplier_name]"
                        class="w-full border border-gray-200 rounded-lg px-2 py-1.5 text-sm font-cairo focus:ring-2 focus:ring-navy">
                        ${supplierOptions}
                    </select>
                </td>
                <td class="px-2 py-2 text-center">
                    <button type="button" onclick="removeRow(${i})" class="w-7 h-7 bg-red-50 hover:bg-red-100 text-red-400 rounded-lg transition-all text-xs">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
        }

        function calcRow(i) {
            const days = parseFloat(document.querySelector(`[name="lines[${i}][days]"]`)?.value) || 1;
            const qty = parseFloat(document.querySelector(`[name="lines[${i}][quantity]"]`)?.value) || 1;
            const price = parseFloat(document.querySelector(`[name="lines[${i}][unit_price]"]`)?.value) || 0;
            const total = days * qty * price;
            document.getElementById(`total-${i}`).textContent = total.toFixed(2);
            updateGrandTotal();
        }

        function updateGrandTotal() {
            let grand = 0;
            document.querySelectorAll('[id^="total-"]').forEach(el => {
                grand += parseFloat(el.textContent) || 0;
            });
            document.getElementById('grandTotal').textContent = grand.toFixed(2);
        }

        function removeRow(i) {
            document.getElementById(`row-${i}`)?.remove();
            updateGrandTotal();
        }
    </script>
@endpush
