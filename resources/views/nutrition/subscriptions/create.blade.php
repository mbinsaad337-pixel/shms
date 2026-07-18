@extends(auth()->user()->hasRole('nutrition-manager') ? 'layouts.nutrition' : 'layouts.app')
@section('title', 'إضافة اشتراكات')

@section('content')
    <div class="p-6 max-w-4xl mx-auto">
        <div class="flex items-center gap-4 mb-6">
            <a href="{{ route('nutrition.subscriptions.index') }}" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-arrow-right text-xl"></i>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-gray-800 font-cairo">إضافة اشتراك طالب جديد</h2>
                <p class="text-gray-400 text-sm font-almarai"> يمكن اختيار أكثر من طالب في نفس الوقت</p>
            </div>
        </div>

        <form action="{{ route('nutrition.subscriptions.store') }}" method="POST">
            @csrf
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                <h3 class="font-bold text-navy font-cairo mb-5 flex items-center gap-2">
                    <i class="fas fa-cog text-gold"></i> إعدادات الاشتراك
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-600 font-cairo mb-1.5">نوع الاشتراك <span
                                class="text-red-500">*</span></label>
                        <select name="subscription_type" id="subType" onchange="updateType()" required
                            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 font-cairo text-sm focus:ring-2 focus:ring-navy">
                            <option value="monthly">شهري</option>
                            <option value="semi_monthly">نصف شهري</option>
                            <option value="daily">يومي</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-600 font-cairo mb-1.5">من تاريخ <span
                                class="text-red-500">*</span></label>
                        <input type="date" name="start_date" id="startDate" required value="{{ date('Y-m-01') }}"
                            onchange="calcDays()"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 font-mono text-sm focus:ring-2 focus:ring-navy">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-600 font-cairo mb-1.5">إلى تاريخ <span
                                class="text-red-500">*</span></label>
                        <input type="date" name="end_date" id="endDate" required value="{{ date('Y-m-t') }}"
                            onchange="calcDays()"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 font-mono text-sm focus:ring-2 focus:ring-navy">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-600 font-cairo mb-1.5">عدد الأيام <span
                                class="text-red-500">*</span></label>
                        <input type="number" name="days_count" id="daysCount" required min="1" value="{{ date('t') }}"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 font-mono text-sm focus:ring-2 focus:ring-navy">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-600 font-cairo mb-1.5" id="rateLabel">قيمة الاشتراك اليومي (ر.ي)</label>
                        <input type="number" name="daily_rate" id="dailyRate" step="0.01" min="0" required
                            onchange="calcTotal()" oninput="calcTotal()"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 font-mono text-sm focus:ring-2 focus:ring-navy"
                            placeholder="0.00">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-600 font-cairo mb-1.5 font-red-hue">آخر يوم للدفع (من الميزانية)</label>
                        <input type="date" name="last_payment_date" id="lastPaymentDate" readonly
                            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 font-mono text-sm focus:ring-0 bg-gray-50 text-red-600 cursor-not-allowed">
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-sm font-bold text-gray-600 font-cairo mb-1.5">الميزانية المرتبطة</label>
                        <select name="budget_id" id="budgetSelect" onchange="syncBudgetRate()"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 font-cairo text-sm focus:ring-2 focus:ring-navy">
                            <option value="">— بدون ربط ميزانية —</option>
                            @foreach ($budgets as $budget)
                                <option value="{{ $budget->id }}" 
                                    data-rate="{{ $budget->daily_rate }}"
                                    data-cost="{{ $budget->cost_per_student }}"
                                    data-days="{{ $budget->days_count }}"
                                    data-last-payment="{{ $budget->last_payment_date ? $budget->last_payment_date->format('Y-m-d') : '' }}">
                                    {{ $budget->month_name }} {{ $budget->year }} —
                                    معدل اليوم: {{ number_format($budget->daily_rate, 2) }} | شهري: {{ number_format($budget->cost_per_student, 2) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Total Preview -->
                <div id="totalPreview" class="mt-4 bg-navy/5 border border-navy/10 rounded-xl p-4 hidden">
                    <p class="text-sm text-navy font-cairo font-bold">
                        تكلفة الاشتراك للطالب الواحد: <span id="totalText" class="font-black text-lg">0.00 ر.ي</span>
                        <span class="text-xs text-gray-400 mr-2" id="formulaText">(<span id="daysText">0</span> يوم × <span
                                id="rateText">0</span> ر.ي/يوم)</span>
                    </p>
                </div>
            </div>

            <!-- Students Selection -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                <h3 class="font-bold text-navy font-cairo mb-4 flex items-center gap-2">
                    <i class="fas fa-users text-gold"></i> اختر الطلاب
                </h3>
                <div class="mb-3">
                    <input type="text" id="studentSearch" placeholder="بحث سريع باسم الطالب..." oninput="filterStudents()"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 font-cairo text-sm focus:ring-2 focus:ring-navy">
                </div>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3 max-h-80 overflow-y-auto" id="studentsGrid">
                    @if(count($students) > 0)
                        @foreach ($students as $student)
                            <label class="flex items-center gap-3 p-3 border border-gray-100 rounded-xl hover:bg-navy/5 cursor-pointer transition-all has-[:checked]:border-gold has-[:checked]:bg-gold/5">
                                <input type="checkbox" name="student_ids[]" value="{{ $student->id }}"
                                    class="w-4 h-4 text-navy rounded border-gray-300 focus:ring-navy">
                                <div>
                                    <p class="font-bold text-gray-800 font-cairo text-sm leading-tight">{{ $student->name_ar }}</p>
                                    <p class="text-xs text-gray-400 font-mono">{{ $student->university_id }}</p>
                                </div>
                            </label>
                        @endforeach
                    @else
                        <div class="col-span-3 text-center py-8 text-gray-300">
                            <i class="fas fa-user-slash text-3xl mb-2 block"></i>
                            <p class="font-cairo text-sm">لا يوجد طلاب متاحون</p>
                        </div>
                    @endif
                </div>
                <div class="flex gap-3 mt-3">
                    <button type="button" onclick="selectAll()" class="text-xs px-3 py-1.5 bg-navy/5 text-navy rounded-lg font-cairo font-bold">تحديد الكل</button>
                    <button type="button" onclick="clearAll()" class="text-xs px-3 py-1.5 bg-gray-100 text-gray-600 rounded-lg font-cairo font-bold">إلغاء التحديد</button>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('nutrition.subscriptions.index') }}" class="px-6 py-3 border border-gray-200 rounded-xl font-cairo font-bold text-gray-600">إلغاء</a>
                <button type="submit" class="px-8 py-3 bg-navy text-white rounded-xl font-bold font-cairo flex items-center gap-2">
                    <i class="fas fa-save"></i> حفظ الاشتراكات
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            updateType();
        });

        function calcDays() {
            const startStr = document.getElementById('startDate').value;
            const endStr = document.getElementById('endDate').value;
            if (startStr && endStr) {
                const s = new Date(startStr);
                const e = new Date(endStr);
                if (!isNaN(s.getTime()) && !isNaN(e.getTime())) {
                    const days = Math.round((e - s) / (1000 * 60 * 60 * 24)) + 1;
                    document.getElementById('daysCount').value = (days > 0 ? days : 0);
                }
            }
            updateType();
        }

        function updateType() {
            const type = document.getElementById('subType').value;
            const startStr = document.getElementById('startDate').value;
            
            const rateInput = document.getElementById('dailyRate');
            const select = document.getElementById('budgetSelect');
            const budgetOpt = select.options[select.selectedIndex];
            
            rateInput.readOnly = false;
            rateInput.classList.remove('bg-gray-50');

            // Auto-adjust dates if needed
            if (!isNaN(new Date(startStr).getTime())) {
                const start = new Date(startStr);
                if (type === 'monthly') {
                    const lastDay = new Date(start.getFullYear(), start.getMonth() + 1, 0);
                    document.getElementById('endDate').value = lastDay.toISOString().substring(0, 10);
                } else if (type === 'semi_monthly') {
                    if (start.getDate() <= 15) {
                        document.getElementById('endDate').value = new Date(start.getFullYear(), start.getMonth(), 15).toISOString().substring(0, 10);
                    } else {
                        document.getElementById('endDate').value = new Date(start.getFullYear(), start.getMonth() + 1, 0).toISOString().substring(0, 10);
                    }
                }
            }

            // Recalculate days count
            const s = new Date(document.getElementById('startDate').value);
            const e = new Date(document.getElementById('endDate').value);
            let daysCount = 0;
            if (!isNaN(s.getTime()) && !isNaN(e.getTime())) {
                daysCount = Math.round((e - s) / (1000 * 60 * 60 * 24)) + 1;
                document.getElementById('daysCount').value = (daysCount > 0 ? daysCount : 0);
            }

            // Apply budget pricing
            if (budgetOpt && budgetOpt.value && daysCount > 0) {
                const budgetMonthlyCost = parseFloat(budgetOpt.getAttribute('data-cost')) || 0;
                const budgetDailyRate = parseFloat(budgetOpt.getAttribute('data-rate')) || 0;
                const budgetLastPayment = budgetOpt.getAttribute('data-last-payment');

                if (budgetLastPayment) {
                    document.getElementById('lastPaymentDate').value = budgetLastPayment;
                }

                if (type === 'monthly') {
                    rateInput.value = (budgetMonthlyCost).toFixed(2);
                    rateInput.readOnly = true;
                    rateInput.classList.add('bg-gray-50');
                    document.getElementById('rateLabel').textContent = 'قيمة الاشتراك الشهري (ثابت)';
                } else if (type === 'semi_monthly') {
                    rateInput.value = (budgetMonthlyCost / 2).toFixed(2);
                    rateInput.readOnly = true;
                    rateInput.classList.add('bg-gray-50');
                    document.getElementById('rateLabel').textContent = 'قيمة الاشتراك نصف الشهري (ثابت)';
                } else if (type === 'daily') {
                    rateInput.value = budgetDailyRate.toFixed(2);
                    rateInput.readOnly = false;
                    document.getElementById('rateLabel').textContent = 'قيمة اليوم الواحد (ر.ي)';
                }
            } else if (type === 'daily') {
                rateInput.readOnly = false;
                rateInput.classList.remove('bg-gray-50');
                document.getElementById('rateLabel').textContent = 'قيمة اليوم الواحد (ر.ي)';
            } else {
                document.getElementById('rateLabel').textContent = 'قيمة الاشتراك (ر.ي)';
            }

            calcTotal();
        }

        function calcTotal() {
            const type = document.getElementById('subType').value;
            const days = parseFloat(document.getElementById('daysCount').value) || 0;
            const inputVal = parseFloat(document.getElementById('dailyRate').value) || 0;
            
            let total = 0;
            if (type === 'monthly' || type === 'semi_monthly') {
                total = inputVal;
            } else {
                total = days * inputVal;
            }
            
            if (days > 0 && inputVal > 0) {
                document.getElementById('totalText').textContent = total.toFixed(2) + ' ر.ي';
                document.getElementById('daysText').textContent = Math.round(days);
                document.getElementById('rateText').textContent = (type === 'daily' ? inputVal.toFixed(2) : (total / days).toFixed(2));
                document.getElementById('totalPreview').classList.remove('hidden');
                
                if (type === 'monthly') {
                    document.getElementById('formulaText').innerHTML = `(مبلغ ثابت للشهر)`;
                } else if (type === 'semi_monthly') {
                    document.getElementById('formulaText').innerHTML = `(نصف قيمة الاشتراك الشهري)`;
                } else {
                    document.getElementById('formulaText').innerHTML = `(${Math.round(days)} يوم × ${inputVal.toFixed(2)} ر.ي/يوم)`;
                }
            } else {
                document.getElementById('totalPreview').classList.add('hidden');
            }
        }

        function syncBudgetRate() {
            updateType();
        }

        function filterStudents() {
            const q = document.getElementById('studentSearch').value.toLowerCase();
            document.querySelectorAll('#studentsGrid label').forEach(el => {
                el.style.display = el.textContent.toLowerCase().includes(q) ? '' : 'none';
            });
        }

        function selectAll() {
            document.querySelectorAll('#studentsGrid input[type=checkbox]:not([disabled])').forEach(c => {
                if (c.parentElement.style.display !== 'none') c.checked = true;
            });
        }
        function clearAll() {
            document.querySelectorAll('#studentsGrid input[type=checkbox]').forEach(c => c.checked = false);
        }
    </script>
@endpush
