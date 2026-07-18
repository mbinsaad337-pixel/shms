@extends('layouts.app')

@section('title', 'لوحة تحكم مشرف الطلاب')

@section('content')
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-primary font-cairo">مركز: {{ auth()->user()->center->name ?? 'غير محدد' }}
            </h1>
            <p class="text-gray-500 font-almarai mt-1">متابعة شؤون الطلاب، السكن، والسلوك الانضباطي.</p>
        </div>
        <div class="flex gap-4">
            <span
                class="bg-indigo-50 text-indigo-700 border border-indigo-200 px-4 py-2 rounded-xl text-sm font-bold font-almarai shadow-sm">
                <i class="fas fa-user-shield ml-1"></i> مشرف الطلاب والسكن
            </span>
        </div>
    </div>

    <!-- Quick Stats for Student/Housing Manager -->
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-6 mb-8">
        <!-- Students Count -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 card-hover">
            <div class="flex items-center justify-between font-cairo">
                <div>
                    <p class="text-xs text-gray-400 font-bold mb-1">الطلاب المسجلين</p>
                    <h3 class="text-3xl font-bold text-gray-800">{{ number_format($stats['total_students']) }}</h3>
                    <div class="mt-2 text-xs">
                        <span class="text-red-500 font-bold">{{ $stats['suspended_students'] }}</span>
                        <span class="text-gray-400">موقوفين</span>
                    </div>
                </div>
                <div class="bg-blue-50 p-4 rounded-2xl text-blue-600">
                    <i class="fas fa-users text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Pending Approval -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 card-hover">
            <div class="flex items-center justify-between font-cairo">
                <div>
                    <p class="text-xs text-gray-400 font-bold mb-1">بانتظار الاعتماد</p>
                    <h3 class="text-3xl font-bold text-orange-600">{{ number_format($stats['pending_approval']) }}</h3>
                    <p class="text-xs text-gray-400 mt-1">بروفايلات جديدة مستكملة</p>
                </div>
                <div class="bg-orange-50 p-4 rounded-2xl text-orange-600">
                    <i class="fas fa-user-check text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Occupancy -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 card-hover">
            <div class="flex items-center justify-between font-cairo">
                <div>
                    <p class="text-xs text-gray-400 font-bold mb-1">المقاعد المتبقية</p>
                    <h3 class="text-3xl font-bold text-green-600">{{ $stats['remaining_seats'] }}</h3>
                    <p class="text-xs text-gray-400 mt-1">من إجمالي {{ $stats['total_capacity'] }} سعة</p>
                </div>
                <div class="bg-green-50 p-4 rounded-2xl text-green-600">
                    <i class="fas fa-bed text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- On Leave -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 card-hover">
            <div class="flex items-center justify-between font-cairo">
                <div>
                    <p class="text-xs text-gray-400 font-bold mb-1">طلاب خارج السكن</p>
                    <h3 class="text-3xl font-bold text-indigo-600">{{ $stats['on_leave_count'] }}</h3>
                    <p class="text-xs text-gray-400 mt-1">طلاب في إجازة حالياً</p>
                </div>
                <div class="bg-indigo-50 p-4 rounded-2xl text-indigo-600">
                    <i class="fas fa-walking text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Quran Circles -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 card-hover">
            <div class="flex items-center justify-between font-cairo">
                <div>
                    <p class="text-xs text-gray-400 font-bold mb-1">الحلقات القرآنية</p>
                    <h3 class="text-3xl font-bold text-gold">{{ $stats['quran_circles_count'] ?? 0 }}</h3>
                    <div class="mt-2 text-xs">
                        <span
                            class="text-red-500 font-bold blur-sm hover:blur-none transition-all">{{ count($circle_absences) }}</span>
                        <span class="text-gray-400">غيابات غير معالجة</span>
                    </div>
                </div>
                <div class="bg-gold/10 p-4 rounded-2xl text-gold-dark">
                    <i class="fas fa-quran text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <!-- Behavioral & Attendance History -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Recent Violations -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-50 flex justify-between items-center bg-red-50/30">
                    <h2 class="text-lg font-bold text-gray-800 font-cairo">آخر المخالفات المسجلة</h2>
                    <a href="{{ route('students.index') }}"
                        class="text-red-600 text-sm font-bold font-cairo hover:underline">متابعة السلوك</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-right">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-8 py-4 text-xs font-bold text-gray-400 uppercase font-cairo">الطالب</th>
                                <th class="px-8 py-4 text-xs font-bold text-gray-400 uppercase font-cairo">نوع المخالفة</th>
                                <th class="px-8 py-4 text-xs font-bold text-gray-400 uppercase font-cairo">الدرجة</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @if($recent_violations->count() > 0)
                                @foreach($recent_violations as $violation)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-8 py-5 font-bold text-gray-800 font-almarai">
                                            {{ $violation->student->name_ar }}
                                        </td>
                                        <td class="px-8 py-5 text-sm font-almarai text-gray-600">{{ $violation->type }}</td>
                                        <td class="px-8 py-5">
                                            <span
                                                class="px-3 py-1 rounded-full text-[10px] font-bold font-almarai {{ $violation->severity == 'severe' ? 'bg-red-50 text-red-600' : 'bg-orange-50 text-orange-600' }}">
                                                {{ $violation->severity == 'severe' ? 'جسيمة' : 'متوسطة' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="3" class="px-8 py-10 text-center text-gray-400 font-almarai">لا توجد مخالفات
                                        مسجلة مؤخراً</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Absences -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-50 flex justify-between items-center bg-blue-50/30">
                    <h2 class="text-lg font-bold text-gray-800 font-cairo">سجل الغياب الأخير</h2>
                    <a href="{{ route('students.index') }}"
                        class="text-primary text-sm font-bold font-cairo hover:underline">عرض الكل</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-right">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-8 py-4 text-xs font-bold text-gray-400 uppercase font-cairo">الطالب</th>
                                <th class="px-8 py-4 text-xs font-bold text-gray-400 uppercase font-cairo">تاريخ الغياب</th>
                                <th class="px-8 py-4 text-xs font-bold text-gray-400 uppercase font-cairo">العذر</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @if($recent_absences->count() > 0)
                                @foreach($recent_absences as $absence)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-8 py-5 font-bold text-gray-800 font-almarai">{{ $absence->student->name_ar }}
                                        </td>
                                        <td class="px-8 py-5 text-sm font-mono text-gray-500">
                                            {{ $absence->date->format('Y-m-d') }}
                                        </td>
                                        <td class="px-8 py-5">
                                            <span
                                                class="px-3 py-1 rounded-full text-xs font-bold font-almarai {{ $absence->has_excuse ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600' }}">
                                                {{ $absence->has_excuse ? 'بعذر' : 'بدون عذر' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="3" class="px-8 py-10 text-center text-gray-400 font-almarai">لا توجد حالات غياب
                                        مسجلة مؤخراً</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Quran Circles Absences -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-50 flex justify-between items-center bg-gold/10">
                    <h2 class="text-lg font-bold text-gray-800 font-cairo">الطلاب الغائبين عن الحلقات القرآنية</h2>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('quran-circles.absent-report') }}" class="text-xs font-bold text-navy hover:underline font-cairo">التقرير التفصيلي</a>
                        <span class="bg-gold/20 text-gold-dark px-3 py-1 rounded-full text-xs font-bold font-cairo">{{ count($circle_absences) }} سجل جديد</span>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-right">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-8 py-4 text-xs font-bold text-gray-400 uppercase font-cairo">الطالب</th>
                                <th class="px-8 py-4 text-xs font-bold text-gray-400 uppercase font-cairo">الحلقة والتاريخ
                                </th>
                                <th class="px-8 py-4 text-xs font-bold text-gray-400 uppercase font-cairo">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @if($circle_absences->count() > 0)
                                @foreach($circle_absences as $absence)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-8 py-5">
                                            <div class="font-bold text-gray-800 font-almarai">{{ $absence->student->name_ar }}</div>
                                            <div class="text-[10px] text-gray-400 font-mono">{{ $absence->student->barcode }}</div>
                                        </td>
                                        <td class="px-8 py-5">
                                            <div class="text-sm font-bold text-navy font-cairo">
                                                {{ $absence->session->circle->name }}
                                            </div>
                                            <div class="text-xs text-gray-500 font-mono mt-1">
                                                {{ optional($absence->session->session_date)->format('Y-m-d') }}
                                            </div>
                                        </td>
                                        <td class="px-8 py-5 flex gap-2">
                                            <button type="button"
                                                onclick="openViolationForStudent({{ $absence->student->id }}, {{ $absence->id }})"
                                                class="w-8 h-8 rounded-lg bg-red-50 text-red-600 flex items-center justify-center hover:bg-red-600 hover:text-white transition-all">
                                                <i class="fas fa-exclamation-triangle text-xs"></i>
                                            </button>
                                            <form action="{{ route('circle-absences.destroy', $absence) }}" method="POST"
                                                data-confirm="هل أنت متأكد من مسح سجل الغياب (اعتباره بعذر)؟">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" title="إعفاء / مسح السجل"
                                                    class="w-8 h-8 rounded-lg bg-gray-100 text-gray-500 flex items-center justify-center hover:bg-green-500 hover:text-white transition-all">
                                                    <i class="fas fa-check text-xs"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="3" class="px-8 py-10 text-center text-gray-400 font-almarai text-sm">
                                        <i class="fas fa-check-circle text-2xl text-green-300 mb-2 block"></i>
                                        لا يوجد طلاب غائبين في الجلسات الأخيرة
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Actions Panel -->
        <div class="space-y-6">
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                <h2 class="text-lg font-bold text-gray-800 mb-6 font-cairo">إجراءات سريعة</h2>
                <div class="space-y-4">
                    <!-- Administrative Actions Dropdown Replacement -->
                    <div class="relative" id="adminActionsDropdown">
                        <button onclick="toggleAdminActions()" id="adminActionsBtn"
                            class="w-full flex items-center p-4 bg-red-600 text-white rounded-2xl hover:bg-red-700 transition-all font-bold font-cairo shadow-lg shadow-red-900/20">
                            <div
                                class="w-10 h-10 bg-white shadow-sm rounded-xl flex items-center justify-center text-red-600 ml-4">
                                <i class="fas fa-gavel text-lg"></i>
                            </div>
                            <span class="flex-1 text-right">الإجراءات الإدارية</span>
                            <i class="fas fa-chevron-down text-xs mr-2 transition-transform duration-200"
                                id="adminChevron"></i>
                        </button>
                        <div id="adminMenu" class="absolute right-0 left-0 top-full mt-2 hidden z-50 animate-fade-in-down">
                            <div
                                class="bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden font-cairo ring-1 ring-black ring-opacity-5">
                                <button onclick="openViolationModal()"
                                    class="w-full text-right px-6 py-4 hover:bg-red-50 text-gray-700 font-bold border-b border-gray-50 flex items-center gap-3 transition-colors">
                                    <i class="fas fa-exclamation-triangle text-red-500 w-5"></i> تسجيل مخالفة
                                </button>
                                <button onclick="openCommitmentModal()"
                                    class="w-full text-right px-6 py-4 hover:bg-orange-50 text-gray-700 font-bold border-b border-gray-50 flex items-center gap-3 transition-colors">
                                    <i class="fas fa-file-contract text-orange-500 w-5"></i> تسجيل تعهد
                                </button>
                                <button onclick="openPenaltyModal()"
                                    class="w-full text-right px-6 py-4 hover:bg-red-50 text-gray-700 font-bold border-b border-gray-50 flex items-center gap-3 transition-colors">
                                    <i class="fas fa-ban text-red-700 w-5"></i> تطبيق عقوبة
                                </button>
                                <button onclick="openAbsenceModal()"
                                    class="w-full text-right px-6 py-4 hover:bg-blue-50 text-gray-700 font-bold border-b border-gray-50 flex items-center gap-3 transition-colors">
                                    <i class="fas fa-calendar-times text-blue-600 w-5"></i> تسجيل غياب
                                </button>
                                <button onclick="openLeaveModal()"
                                    class="w-full text-right px-6 py-4 hover:bg-blue-50 text-gray-700 font-bold flex items-center gap-3 transition-colors">
                                    <i class="fas fa-plane-departure text-indigo-600 w-5"></i> تسجيل استئذان
                                </button>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('rooms.index', ['vacant' => 1]) }}"
                        class="flex items-center p-4 bg-indigo-50 rounded-2xl hover:bg-indigo-600 hover:text-white transition-all group">
                        <div
                            class="w-10 h-10 bg-white shadow-sm rounded-xl flex items-center justify-center text-indigo-600 ml-4 group-hover:scale-110 transition-transform">
                            <i class="fas fa-bed text-lg"></i>
                        </div>
                        <span class="font-bold font-cairo">تسكين وتوزيع الطلاب</span>
                    </a>

                    <a href="{{ route('students.index') }}"
                        class="flex items-center p-4 bg-gray-50 rounded-2xl hover:bg-gray-800 hover:text-white transition-all group">
                        <div
                            class="w-10 h-10 bg-white shadow-sm rounded-xl flex items-center justify-center text-gray-800 ml-4 group-hover:scale-110 transition-transform">
                            <i class="fas fa-user-check text-lg"></i>
                        </div>
                        <span class="font-bold font-cairo">اعتماد بروفايلات الطلاب</span>
                    </a>

                    <a href="{{ route('students.create') }}"
                        class="flex items-center p-4 bg-gray-50 rounded-2xl hover:bg-secondary hover:text-white transition-all group">
                        <div
                            class="w-10 h-10 bg-white shadow-sm rounded-xl flex items-center justify-center text-secondary ml-4 group-hover:scale-110 transition-transform">
                            <i class="fas fa-user-plus text-lg"></i>
                        </div>
                        <span class="font-bold font-cairo">تسجيل طالب جديد</span>
                    </a>

                    @can('manage-rooms')
                        <a href="{{ route('rooms.index') }}"
                            class="flex items-center p-4 bg-gray-50 rounded-2xl hover:bg-indigo-600 hover:text-white transition-all group">
                            <div
                                class="w-10 h-10 bg-white shadow-sm rounded-xl flex items-center justify-center text-indigo-600 ml-4 group-hover:scale-110 transition-transform">
                                <i class="fas fa-hotel text-lg"></i>
                            </div>
                            <span class="font-bold font-cairo">إدارة المرافق والغرف</span>
                        </a>
                    @endcan

                    <a href="{{ route('students.index') }}"
                        class="flex items-center p-4 bg-gray-50 rounded-2xl hover:bg-blue-600 hover:text-white transition-all group">
                        <div
                            class="w-10 h-10 bg-white shadow-sm rounded-xl flex items-center justify-center text-blue-600 ml-4 group-hover:scale-110 transition-transform">
                            <i class="fas fa-id-card text-lg"></i>
                        </div>
                        <span class="font-bold font-cairo">البحث في ملفات الطلاب</span>
                    </a>
                </div>
            </div>

            <!-- Important Reminders -->
            <div class="bg-gray-800 rounded-3xl p-8 text-white shadow-xl shadow-gray-900/20">
                <h2 class="text-lg font-bold font-cairo mb-6 flex items-center gap-2">
                    <i class="fas fa-bell text-yellow-400"></i> تذكير بالمهام
                </h2>
                <div class="space-y-4 text-sm font-almarai">
                    <div class="flex gap-3 opacity-90">
                        <span class="w-2 h-2 rounded-full bg-yellow-400 mt-1.5 shrink-0"></span>
                        <p>تأكد من مراجعة طلبات السجيل الجديدة يومياً.</p>
                    </div>
                    <div class="flex gap-3 opacity-90">
                        <span class="w-2 h-2 rounded-full bg-green-400 mt-1.5 shrink-0"></span>
                        <p>تحديث قاعدة بيانات الغرف الشاغرة قبل فترة الاختبارات.</p>
                    </div>
                    <div class="flex gap-3 opacity-90">
                        <span class="w-2 h-2 rounded-full bg-blue-400 mt-1.5 shrink-0"></span>
                        <p>متابعة التعهدات التي تحتاج توقيع ولي الأمر.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <style>
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in-down {
            animation: fadeInDown 0.2s ease-out;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 10px;
        }
    </style>

    <script>
        function toggleAdminActions() {
            const menu = document.getElementById('adminMenu');
            if (!menu) return;
            const chevron = document.getElementById('adminChevron');
            menu.classList.toggle('hidden');
            if (chevron) chevron.classList.toggle('rotate-180');
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            const dropdown = document.getElementById('adminActionsDropdown');
            const menu = document.getElementById('adminMenu');
            const chevron = document.getElementById('adminChevron');
            if (dropdown && !dropdown.contains(e.target) && menu) {
                menu.classList.add('hidden');
                if (chevron) chevron.classList.remove('rotate-180');
            }
        });

        function filterSmartStudents(input) {
            const term = input.value.toLowerCase();
            const container = input.closest('.student-selector-container');
            const items = container.querySelectorAll('.student-item');
            items.forEach(item => {
                const name = item.querySelector('.student-name').innerText.toLowerCase();
                if (name.includes(term)) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        function selectAllVisible(btn) {
            const container = btn.closest('.student-selector-container');
            const checkboxes = container.querySelectorAll('.student-item:not([style*="display: none"]) input[type="checkbox"]');
            checkboxes.forEach(cb => {
                cb.checked = true;
                updateSelectedCount(cb);
            });
        }

        function clearSelection(btn) {
            const container = btn.closest('.student-selector-container');
            const checkboxes = container.querySelectorAll('input[type="checkbox"]');
            checkboxes.forEach(cb => {
                cb.checked = false;
                updateSelectedCount(cb);
            });
        }

        function updateSelectedCount(cb) {
            const modal = cb.closest('[id$="Modal"]');
            if (!modal) return;
            const checkboxes = modal.querySelectorAll('input[name="student_id[]"]:checked');
            const badge = modal.querySelector('.selected-badge');
            if (badge) {
                badge.innerText = checkboxes.length + ' مختار';
            }
        }

        function openViolationForStudent(studentId, attendanceId = null) {
            // Uncheck all first
            const modal = document.getElementById('violationModal');
            const checkboxes = modal.querySelectorAll('input[type="checkbox"]');
            checkboxes.forEach(cb => cb.checked = false);

            // Check the target student
            const targetCb = modal.querySelector(`input[value="${studentId}"]`);
            if (targetCb) {
                targetCb.checked = true;
                updateSelectedCount(targetCb);
            }

            // Append attendanceId if exists
            const form = modal.querySelector('form');
            form.querySelectorAll('.temp-attendance-id').forEach(el => el.remove());
            if (attendanceId) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'circle_attendance_id[]';
                input.value = attendanceId;
                input.className = 'temp-attendance-id';
                form.appendChild(input);
            }

            // Open modal without toggling dropdown
            modal.classList.remove('hidden');
        }

        function openViolationModal() { document.getElementById('violationModal').classList.remove('hidden'); toggleAdminActions(); }
        function closeViolationModal() { document.getElementById('violationModal').classList.add('hidden'); }

        function openCommitmentModal() { document.getElementById('commitmentModal').classList.remove('hidden'); toggleAdminActions(); }
        function closeCommitmentModal() { document.getElementById('commitmentModal').classList.add('hidden'); }

        function openPenaltyModal() { document.getElementById('penaltyModal').classList.remove('hidden'); toggleAdminActions(); }
        function closePenaltyModal() { document.getElementById('penaltyModal').classList.add('hidden'); }

        function openAbsenceModal() { document.getElementById('absenceModal').classList.remove('hidden'); toggleAdminActions(); }
        function closeAbsenceModal() { document.getElementById('absenceModal').classList.add('hidden'); }

        function openLeaveModal() { document.getElementById('leaveModal').classList.remove('hidden'); toggleAdminActions(); }
        function closeLeaveModal() { document.getElementById('leaveModal').classList.add('hidden'); }

        // Close modals on escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                document.querySelectorAll('[id$="Modal"]').forEach(m => m.classList.add('hidden'));
            }
        });
    </script>

    <!-- Modals with Multi-Student Support -->

    <!-- Violation Modal -->
    <div id="violationModal" class="fixed inset-0 z-[100] hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-75" onclick="closeViolationModal()">
            </div>
            <div
                class="inline-block overflow-hidden text-right align-bottom transition-all transform bg-white rounded-3xl shadow-xl sm:my-8 sm:align-middle sm:max-w-xl sm:w-full">
                <div class="px-8 pt-8 pb-6 bg-white">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-2xl font-bold text-gray-800 font-cairo">تسجيل مخالفة انضباطية</h3>
                        <button onclick="closeViolationModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <form action="{{ route('violations.store') }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label
                                    class="block text-sm font-bold text-gray-700 mb-2 font-cairo text-right flex justify-between items-center">
                                    <span
                                        class="selected-badge bg-red-100 text-red-700 px-2 py-0.5 rounded-lg text-[10px]">0
                                        مختار</span>
                                    <span>اختر الطالب / الطلاب</span>
                                </label>
                                <div class="student-selector-container bg-gray-50 rounded-2xl p-3 border border-gray-100">
                                    <div class="relative mb-3">
                                        <i
                                            class="fas fa-search absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                                        <input type="text" onkeyup="filterSmartStudents(this)"
                                            placeholder="بحث بالاسم أو الرقم..."
                                            class="w-full pr-10 pl-4 py-2 border-gray-200 rounded-xl text-xs focus:ring-red-500 font-almarai">
                                    </div>
                                    <div class="flex items-center justify-between mb-2 px-1">
                                        <button type="button" onclick="selectAllVisible(this)"
                                            class="text-[10px] text-red-600 font-bold font-cairo hover:underline">تحديد
                                            الكل</button>
                                        <button type="button" onclick="clearSelection(this)"
                                            class="text-[10px] text-gray-400 font-bold font-cairo hover:underline">مسح</button>
                                    </div>
                                    <div class="max-h-40 overflow-y-auto custom-scrollbar space-y-1 pr-1">
                                        @foreach($students as $s)
                                            <label
                                                class="flex items-center p-2 hover:bg-white rounded-lg cursor-pointer transition-colors border border-transparent hover:border-red-100 student-item">
                                                <input type="checkbox" name="student_id[]" value="{{ $s->id }}"
                                                    onchange="updateSelectedCount(this)"
                                                    class="rounded text-red-600 focus:ring-red-500 h-4 w-4">
                                                <div class="mr-3 text-right">
                                                    <p class="text-xs font-bold text-gray-800 font-cairo student-name">
                                                        {{ $s->name_ar }}
                                                    </p>
                                                    <p class="text-[10px] text-gray-400 font-almarai">{{ $s->barcode }}</p>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1 font-cairo text-right">نوع
                                        المخالفة</label>
                                    <input type="text" name="type" required
                                        class="w-full border-gray-200 rounded-xl p-3 focus:ring-red-500 font-almarai"
                                        placeholder="مثلاً: تأخير">
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-bold text-gray-700 mb-1 font-cairo text-right">الدرجة</label>
                                    <select name="severity" required
                                        class="w-full border-gray-200 rounded-xl p-3 focus:ring-red-500 font-almarai">
                                        <option value="minor">بسيطة</option>
                                        <option value="moderate">متوسطة</option>
                                        <option value="severe">جسيمة</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1 font-cairo text-right">تاريخ
                                    المخالفة</label>
                                <input type="date" name="violation_date" value="{{ date('Y-m-d') }}" required
                                    class="w-full border-gray-200 rounded-xl p-3 focus:ring-red-500 font-almarai">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1 font-cairo text-right">الوصف
                                    والتفاصيل</label>
                                <textarea name="description" rows="3" required
                                    class="w-full border-gray-200 rounded-xl p-3 focus:ring-red-500 font-almarai"></textarea>
                            </div>
                        </div>
                        <div class="mt-8 flex gap-3">
                            <button type="submit"
                                class="flex-1 bg-red-600 text-white font-bold font-cairo py-4 rounded-2xl hover:bg-red-700 transition-all shadow-lg shadow-red-900/20">حفظ
                                المخالفة</button>
                            <button type="button" onclick="closeViolationModal()"
                                class="flex-1 bg-gray-100 text-gray-600 font-bold font-cairo py-4 rounded-2xl hover:bg-gray-200 transition-all">إلغاء</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Commitment Modal -->
    <div id="commitmentModal" class="fixed inset-0 z-[100] hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-75" onclick="closeCommitmentModal()">
            </div>
            <div
                class="inline-block overflow-hidden text-right align-bottom transition-all transform bg-white rounded-3xl shadow-xl sm:my-8 sm:align-middle sm:max-w-xl sm:w-full">
                <div class="px-8 pt-8 pb-6 bg-white">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-2xl font-bold text-gray-800 font-cairo">تسجيل تعهد خطي</h3>
                        <button onclick="closeCommitmentModal()"
                            class="text-gray-400 hover:text-gray-600 transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <form action="{{ route('commitments.store') }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label
                                    class="block text-sm font-bold text-gray-700 mb-2 font-cairo text-right flex justify-between items-center">
                                    <span
                                        class="selected-badge bg-orange-100 text-orange-700 px-2 py-0.5 rounded-lg text-[10px]">0
                                        مختار</span>
                                    <span>اختر الطالب / الطلاب</span>
                                </label>
                                <div class="student-selector-container bg-gray-50 rounded-2xl p-3 border border-gray-100">
                                    <div class="relative mb-3">
                                        <i
                                            class="fas fa-search absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                                        <input type="text" onkeyup="filterSmartStudents(this)" placeholder="بحث بالاسم..."
                                            class="w-full pr-10 pl-4 py-2 border-gray-200 rounded-xl text-xs focus:ring-orange-500 font-almarai">
                                    </div>
                                    <div class="flex items-center justify-between mb-2 px-1">
                                        <button type="button" onclick="selectAllVisible(this)"
                                            class="text-[10px] text-orange-600 font-bold font-cairo hover:underline">تحديد
                                            الكل</button>
                                        <button type="button" onclick="clearSelection(this)"
                                            class="text-[10px] text-gray-400 font-bold font-cairo hover:underline">مسح</button>
                                    </div>
                                    <div class="max-h-40 overflow-y-auto custom-scrollbar space-y-1 pr-1">
                                        @foreach($students as $s)
                                            <label
                                                class="flex items-center p-2 hover:bg-white rounded-lg cursor-pointer transition-colors border border-transparent hover:border-orange-100 student-item">
                                                <input type="checkbox" name="student_id[]" value="{{ $s->id }}"
                                                    onchange="updateSelectedCount(this)"
                                                    class="rounded text-orange-600 focus:ring-orange-500 h-4 w-4">
                                                <div class="mr-3 text-right">
                                                    <p class="text-xs font-bold text-gray-800 font-cairo student-name">
                                                        {{ $s->name_ar }}
                                                    </p>
                                                    <p class="text-[10px] text-gray-400 font-almarai">{{ $s->barcode }}</p>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1 font-cairo text-right">نص
                                    التعهد</label>
                                <textarea name="text" rows="4" required
                                    class="w-full border-gray-200 rounded-xl p-3 focus:ring-orange-500 font-almarai"
                                    placeholder="مثلاً: أتعهد بالالتزام بموعد إغلاق البوابات..."></textarea>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label
                                        class="block text-sm font-bold text-gray-700 mb-1 font-cairo text-right">التاريخ</label>
                                    <input type="date" name="date" value="{{ date('Y-m-d') }}" required
                                        class="w-full border-gray-200 rounded-xl p-3 focus:ring-orange-500 font-almarai">
                                </div>
                                <div class="flex items-center gap-3 pt-8">
                                    <label class="flex items-center cursor-pointer">
                                        <input type="checkbox" name="requires_guardian_signature" value="1"
                                            class="rounded text-orange-600">
                                        <span class="mr-2 text-sm font-bold font-cairo">توقيع ولي الأمر</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="mt-8 flex gap-3">
                            <button type="submit"
                                class="flex-1 bg-orange-600 text-white font-bold font-cairo py-4 rounded-2xl hover:bg-orange-700 shadow-lg shadow-orange-900/20">حفظ
                                التعهد</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Penalty Modal -->
    <div id="penaltyModal" class="fixed inset-0 z-[100] hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-75" onclick="closePenaltyModal()"></div>
            <div
                class="inline-block overflow-hidden text-right align-bottom transition-all transform bg-white rounded-3xl shadow-xl sm:my-8 sm:align-middle sm:max-w-xl sm:w-full">
                <div class="px-8 pt-8 pb-6 bg-white">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-2xl font-bold text-gray-800 font-cairo text-right">تطبيق عقوبة إدارية</h3>
                        <button onclick="closePenaltyModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <form action="{{ route('penalties.store') }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label
                                    class="block text-sm font-bold text-gray-700 mb-2 font-cairo text-right flex justify-between items-center">
                                    <span
                                        class="selected-badge bg-red-100 text-red-900 px-2 py-0.5 rounded-lg text-[10px]">0
                                        مختار</span>
                                    <span>اختر الطالب / الطلاب</span>
                                </label>
                                <div class="student-selector-container bg-gray-50 rounded-2xl p-3 border border-gray-100">
                                    <div class="relative mb-3">
                                        <i
                                            class="fas fa-search absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                                        <input type="text" onkeyup="filterSmartStudents(this)" placeholder="بحث بالاسم..."
                                            class="w-full pr-10 pl-4 py-2 border-gray-200 rounded-xl text-xs focus:ring-red-800 font-almarai">
                                    </div>
                                    <div class="flex items-center justify-between mb-2 px-1">
                                        <button type="button" onclick="selectAllVisible(this)"
                                            class="text-[10px] text-red-800 font-bold font-cairo hover:underline">تحديد
                                            الكل</button>
                                        <button type="button" onclick="clearSelection(this)"
                                            class="text-[10px] text-gray-400 font-bold font-cairo hover:underline">مسح</button>
                                    </div>
                                    <div class="max-h-40 overflow-y-auto custom-scrollbar space-y-1 pr-1">
                                        @foreach($students as $s)
                                            <label
                                                class="flex items-center p-2 hover:bg-white rounded-lg cursor-pointer transition-colors border border-transparent hover:border-red-200 student-item">
                                                <input type="checkbox" name="student_id[]" value="{{ $s->id }}"
                                                    onchange="updateSelectedCount(this)"
                                                    class="rounded text-red-800 focus:ring-red-900 h-4 w-4">
                                                <div class="mr-3 text-right">
                                                    <p class="text-xs font-bold text-gray-800 font-cairo student-name">
                                                        {{ $s->name_ar }}
                                                    </p>
                                                    <p class="text-[10px] text-gray-400 font-almarai">{{ $s->barcode }}</p>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4 text-right">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1 font-cairo">نوع
                                        العقوبة</label>
                                    <select name="type" required
                                        class="w-full border-gray-200 rounded-xl p-3 focus:ring-red-700 font-almarai text-right">
                                        <option value="verbal_warning">تنبيه شفهي</option>
                                        <option value="written_warning">إنذار خطي</option>
                                        <option value="service_suspension">إيقاف خدمات</option>
                                        <option value="temporary_suspension">إيقاف مؤقت</option>
                                        <option value="expulsion">فصل نهائي</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1 font-cairo">تاريخ
                                        البدء</label>
                                    <input type="date" name="start_date" value="{{ date('Y-m-d') }}" required
                                        class="w-full border-gray-200 rounded-xl p-3 focus:ring-red-700 font-almarai">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1 font-cairo text-right">السبب
                                    والتفاصيل</label>
                                <textarea name="description" rows="3" required
                                    class="w-full border-gray-200 rounded-xl p-3 focus:ring-red-700 font-almarai"></textarea>
                            </div>
                        </div>
                        <div class="mt-8">
                            <button type="submit"
                                class="w-full bg-red-700 text-white font-bold font-cairo py-4 rounded-2xl hover:bg-red-800 shadow-xl shadow-red-900/10">تطبيق
                                العقوبة</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Absence Modal -->
    <div id="absenceModal" class="fixed inset-0 z-[100] hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-75" onclick="closeAbsenceModal()"></div>
            <div
                class="inline-block overflow-hidden text-right align-bottom transition-all transform bg-white rounded-3xl shadow-xl sm:my-8 sm:align-middle sm:max-w-xl sm:w-full">
                <div class="px-8 pt-8 pb-6 bg-white">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-2xl font-bold text-gray-800 font-cairo">تسجيل غياب</h3>
                        <button onclick="closeAbsenceModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <form action="{{ route('absences.store') }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label
                                    class="block text-sm font-bold text-gray-700 mb-2 font-cairo text-right flex justify-between items-center">
                                    <span
                                        class="selected-badge bg-blue-100 text-blue-700 px-2 py-0.5 rounded-lg text-[10px]">0
                                        مختار</span>
                                    <span>اختر الطالب / الطلاب</span>
                                </label>
                                <div class="student-selector-container bg-gray-50 rounded-2xl p-3 border border-gray-100">
                                    <div class="relative mb-3">
                                        <i
                                            class="fas fa-search absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                                        <input type="text" onkeyup="filterSmartStudents(this)" placeholder="بحث بالاسم..."
                                            class="w-full pr-10 pl-4 py-2 border-gray-200 rounded-xl text-xs focus:ring-blue-600 font-almarai">
                                    </div>
                                    <div class="flex items-center justify-between mb-2 px-1">
                                        <button type="button" onclick="selectAllVisible(this)"
                                            class="text-[10px] text-blue-600 font-bold font-cairo hover:underline">تحديد
                                            الكل</button>
                                        <button type="button" onclick="clearSelection(this)"
                                            class="text-[10px] text-gray-400 font-bold font-cairo hover:underline">مسح</button>
                                    </div>
                                    <div class="max-h-40 overflow-y-auto custom-scrollbar space-y-1 pr-1">
                                        @foreach($students as $s)
                                            <label
                                                class="flex items-center p-2 hover:bg-white rounded-lg cursor-pointer transition-colors border border-transparent hover:border-blue-100 student-item">
                                                <input type="checkbox" name="student_id[]" value="{{ $s->id }}"
                                                    onchange="updateSelectedCount(this)"
                                                    class="rounded text-blue-600 focus:ring-blue-500 h-4 w-4">
                                                <div class="mr-3 text-right">
                                                    <p class="text-xs font-bold text-gray-800 font-cairo student-name">
                                                        {{ $s->name_ar }}
                                                    </p>
                                                    <p class="text-[10px] text-gray-400 font-almarai">{{ $s->barcode }}</p>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1 font-cairo text-right">تاريخ
                                        الغياب</label>
                                    <input type="date" name="date" value="{{ date('Y-m-d') }}" required
                                        class="w-full border-gray-200 rounded-xl p-3 focus:ring-blue-600 font-almarai">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1 font-cairo text-right">هل
                                        بعذر؟</label>
                                    <select name="has_excuse" required
                                        class="w-full border-gray-200 rounded-xl p-3 focus:ring-blue-600 font-almarai">
                                        <option value="0">بدون عذر</option>
                                        <option value="1">بعذر مقبول</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-bold text-gray-700 mb-1 font-cairo text-right">ملاحظات</label>
                                <textarea name="notes" rows="2"
                                    class="w-full border-gray-200 rounded-xl p-3 focus:ring-blue-600 font-almarai"></textarea>
                            </div>
                        </div>
                        <div class="mt-8">
                            <button type="submit"
                                class="w-full bg-blue-600 text-white font-bold font-cairo py-4 rounded-2xl hover:bg-blue-700 shadow-xl shadow-blue-900/10">حفظ
                                الغياب</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Leave Modal -->
    <div id="leaveModal" class="fixed inset-0 z-[100] hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-75" onclick="closeLeaveModal()"></div>
            <div
                class="inline-block overflow-hidden text-right align-bottom transition-all transform bg-white rounded-3xl shadow-xl sm:my-8 sm:align-middle sm:max-w-xl sm:w-full">
                <div class="px-8 pt-8 pb-6 bg-white">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-2xl font-bold text-gray-800 font-cairo">تسجيل استئذان / إجازة</h3>
                        <button onclick="closeLeaveModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <form action="{{ route('leaves.store') }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label
                                    class="block text-sm font-bold text-gray-700 mb-2 font-cairo text-right flex justify-between items-center">
                                    <span
                                        class="selected-badge bg-green-100 text-green-700 px-2 py-0.5 rounded-lg text-[10px]">0
                                        مختار</span>
                                    <span>اختر الطالب / الطلاب</span>
                                </label>
                                <div class="student-selector-container bg-gray-50 rounded-2xl p-3 border border-gray-100">
                                    <div class="relative mb-3">
                                        <i
                                            class="fas fa-search absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                                        <input type="text" onkeyup="filterSmartStudents(this)" placeholder="بحث بالاسم..."
                                            class="w-full pr-10 pl-4 py-2 border-gray-200 rounded-xl text-xs focus:ring-green-600 font-almarai">
                                    </div>
                                    <div class="flex items-center justify-between mb-2 px-1">
                                        <button type="button" onclick="selectAllVisible(this)"
                                            class="text-[10px] text-green-600 font-bold font-cairo hover:underline">تحديد
                                            الكل</button>
                                        <button type="button" onclick="clearSelection(this)"
                                            class="text-[10px] text-gray-400 font-bold font-cairo hover:underline">مسح</button>
                                    </div>
                                    <div class="max-h-40 overflow-y-auto custom-scrollbar space-y-1 pr-1">
                                        @foreach($students as $s)
                                            <label
                                                class="flex items-center p-2 hover:bg-white rounded-lg cursor-pointer transition-colors border border-transparent hover:border-green-100 student-item">
                                                <input type="checkbox" name="student_id[]" value="{{ $s->id }}"
                                                    onchange="updateSelectedCount(this)"
                                                    class="rounded text-green-600 focus:ring-green-500 h-4 w-4">
                                                <div class="mr-3 text-right">
                                                    <p class="text-xs font-bold text-gray-800 font-cairo student-name">
                                                        {{ $s->name_ar }}
                                                    </p>
                                                    <p class="text-[10px] text-gray-400 font-almarai">{{ $s->barcode }}</p>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1 font-cairo text-right">نوع
                                        الإجازة</label>
                                    <select name="type" required
                                        class="w-full border-gray-200 rounded-xl p-3 focus:ring-green-600 font-almarai">
                                        <option value="temporary">استئذان مؤقت</option>
                                        <option value="vacation">إجازة اعتيادية</option>
                                        <option value="medical">إجازة مرضية</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1 font-cairo text-right">تاريخ
                                        الخروج</label>
                                    <input type="datetime-local" name="departure_date" required
                                        class="w-full border-gray-200 rounded-xl p-3 focus:ring-green-600 font-almarai">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1 font-cairo text-right">العودة
                                        المتوقعة</label>
                                    <input type="datetime-local" name="expected_return_date" required
                                        class="w-full border-gray-200 rounded-xl p-3 focus:ring-green-600 font-almarai">
                                </div>
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-bold text-gray-700 mb-1 font-cairo text-right">السبب</label>
                                <textarea name="reason" rows="2" required
                                    class="w-full border-gray-200 rounded-xl p-3 focus:ring-green-600 font-almarai"></textarea>
                            </div>
                        </div>
                        <div class="mt-8">
                            <button type="submit"
                                class="w-full bg-green-600 text-white font-bold font-cairo py-4 rounded-2xl hover:bg-green-700 shadow-xl shadow-green-900/10">حفظ
                                طلب الإجازة</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endpush
