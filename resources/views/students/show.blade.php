@extends ('layouts.app')
@php
    /** @var \App\Models\Student $student */
@endphp

@section('title', 'ملف الطالب: ' . $student->name_ar)

@section('content')
    <div class="container mx-auto px-4 py-8">
        @include('partials.print_header', [
            'title' => 'ملف بيانات الطالب السكنية',
            'number' => $student->university_id,
        ])
        <div class="max-w-6xl mx-auto">
            @php
                $currentRoom = $student->roomAssignments->where('released_at', null)->first();
            @endphp

            <!-- Admin Actions & Alerts -->
            @if (!auth()->user()->hasRole('student') && !auth()->user()->hasRole('super-admin'))
                <div class="mb-6 flex flex-col gap-6">
                    @if ($student->user->profile_completed && !$student->is_profile_approved)
                        <div
                            class="bg-gold/10 border-r-4 border-gold p-6 rounded-2xl flex items-center justify-between gap-3 shadow-sm">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-exclamation-circle text-navy text-xl"></i>
                                <p class="text-navy font-bold font-cairo">هذا الطالب قام باستكمال بياناته وبانتظار الاعتماد
                                    المبدئي.</p>
                            </div>
                            <form action="{{ route('students.approve-profile', $student) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit"
                                    class="bg-navy text-white px-6 py-2.5 rounded-xl hover:bg-navy/90 font-bold font-cairo shadow-lg flex items-center gap-2">
                                    <i class="fas fa-check-double text-gold"></i> اعتماد البيانات
                                </button>
                            </form>
                        </div>
                    @elseif(!$student->user->profile_completed)
                        <div
                            class="bg-gray-50 border-r-4 border-gray-400 p-6 rounded-2xl flex items-center gap-3 shadow-sm">
                            <i class="fas fa-user-clock text-gray-400 text-xl"></i>
                            <p class="text-gray-600 font-bold font-cairo">هذا الطالب لم يقم باستكمال بياناته حتى الآن.</p>
                        </div>
                    @endif

                    <div class="flex flex-wrap gap-3 items-center justify-between whitespace-nowrap">
                        <div class="flex flex-wrap gap-2">
                            <!-- Behavioral Actions -->
                            @if ($student->allows('violations'))
                                <div class="relative group">
                                    <button
                                        class="bg-navy text-white px-6 py-3 rounded-xl hover:bg-navy/90 font-bold font-cairo shadow-lg flex items-center gap-3 transition-all">
                                        <i class="fas fa-gavel text-gold"></i>
                                        <span>إجراء إداري</span>
                                        <i class="fas fa-chevron-down text-[10px] opacity-50"></i>
                                    </button>
                                    <div
                                        class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl border border-gray-100 hidden group-hover:block z-50">
                                        <button onclick="openViolationModal()"
                                            class="w-full text-right px-4 py-3 text-sm font-bold font-cairo text-gray-700 hover:bg-gray-50 flex items-center gap-2 border-b border-gray-50">
                                            <i class="fas fa-exclamation-triangle text-red-500 w-5"></i> تسجيل مخالفة
                                        </button>
                                        <button onclick="openCommitmentModal()"
                                            class="w-full text-right px-4 py-3 text-sm font-bold font-cairo text-gray-700 hover:bg-gray-50 flex items-center gap-2 border-b border-gray-50">
                                            <i class="fas fa-file-contract text-orange-500 w-5"></i> تسجيل تعهد
                                        </button>
                                        <button onclick="openPenaltyModal()"
                                            class="w-full text-right px-4 py-3 text-sm font-bold font-cairo text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                            <i class="fas fa-ban text-red-700 w-5"></i> تطبيق عقوبة
                                        </button>
                                    </div>
                                </div>
                            @endif

                            <!-- Presence Actions -->
                            @if ($student->allows('attendance'))
                                <div class="relative group">
                                    <button
                                        class="bg-navy text-white px-6 py-3 rounded-xl hover:bg-navy/90 font-bold font-cairo shadow-lg flex items-center gap-3 transition-all">
                                        <i class="fas fa-user-clock text-gold"></i>
                                        <span>الحضور والغياب</span>
                                        <i class="fas fa-chevron-down text-[10px] opacity-50"></i>
                                    </button>
                                    <div
                                        class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl border border-gray-100 hidden group-hover:block z-50">
                                        <button onclick="openAbsenceModal()"
                                            class="w-full text-right px-4 py-3 text-sm font-bold font-cairo text-gray-700 hover:bg-gray-50 flex items-center gap-2 border-b border-gray-50">
                                            <i class="fas fa-calendar-times text-orange-600 w-5"></i> تسجيل غياب
                                        </button>
                                        <button onclick="openLeaveModal()"
                                            class="w-full text-right px-4 py-3 text-sm font-bold font-cairo text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                            <i class="fas fa-plane-departure text-blue-600 w-5"></i> تسجيل استئذان
                                        </button>
                                    </div>
                                </div>
                            @endif

                            @if ($currentRoom)
                                <button onclick="openTransferModal()"
                                    class="bg-gold text-navy px-6 py-3 rounded-xl hover:bg-gold/90 font-bold font-cairo shadow-lg flex items-center gap-2 transition-all">
                                    <i class="fas fa-exchange-alt"></i> نقل غرفة
                                </button>
                            @endif
                        </div>

                        <div class="flex flex-wrap gap-2">
                            <form action="{{ route('students.toggle-edit', $student) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit"
                                    class="{{ $student->can_edit_profile ? 'bg-orange-500' : 'bg-blue-600' }} text-white px-4 py-2 rounded-lg font-bold font-cairo shadow-sm transition-all">
                                    <i
                                        class="fas {{ $student->can_edit_profile ? 'fa-user-lock' : 'fa-user-edit' }} ml-1"></i>
                                    {{ $student->can_edit_profile ? 'إغلاق التعديل' : 'فتح التعديل' }}
                                </button>
                            </form>
                            <form action="{{ route('students.toggle-circle-teacher', $student) }}" method="POST"
                                class="inline">
                                @csrf
                                <button type="submit"
                                    class="{{ $student->user->hasRole('circle-teacher') ? 'bg-red-600' : 'bg-navy' }} text-white px-4 py-2 rounded-lg font-bold font-cairo shadow-sm transition-all whitespace-nowrap">
                                    <i
                                        class="fas {{ $student->user->hasRole('circle-teacher') ? 'fa-user-minus' : 'fa-user-plus' }} ml-1"></i>
                                    {{ $student->user->hasRole('circle-teacher') ? 'إلغاء كمدرس' : 'تعيين كمدرس حلقة' }}
                                </button>
                            </form>
                            <form action="{{ route('students.toggle-activity-assistant', $student) }}" method="POST"
                                class="inline">
                                @csrf
                                <button type="submit"
                                    class="{{ $student->user->hasRole('activity-assistant') ? 'bg-indigo-600' : 'bg-navy' }} text-white px-4 py-2 rounded-lg font-bold font-cairo shadow-sm transition-all whitespace-nowrap">
                                    <i
                                        class="fas {{ $student->user->hasRole('activity-assistant') ? 'fa-user-shield' : 'fa-user-plus' }} ml-1"></i>
                                    {{ $student->user->hasRole('activity-assistant') ? 'إلغاء كمساعد أنشطة' : 'تعيين كمساعد أنشطة' }}
                                </button>
                            </form>
                            <a href="{{ route('students.edit', $student) }}"
                                class="bg-navy text-white px-6 py-3 rounded-xl hover:bg-navy/90 font-bold font-cairo shadow-lg flex items-center gap-2 transition-all">
                                <i class="fas fa-edit text-gold"></i> تعديل المشرف
                            </a>
                            <a href="{{ route('students.export-pdf', $student) }}"
                                class="bg-white text-navy border-2 border-navy px-6 py-3 rounded-xl hover:bg-navy/5 font-bold font-cairo shadow-md flex items-center gap-2">
                                <i class="fas fa-file-pdf"></i> تصدير PDF
                            </a>
                        </div>
                    </div>
                </div>
            @elseif(auth()->user()->hasRole('super-admin'))
                <div class="mb-6 flex justify-end">
                    <a href="{{ route('students.export-pdf', $student) }}"
                        class="bg-white text-navy border-2 border-navy px-6 py-3 rounded-xl hover:bg-navy/5 font-bold font-cairo shadow-md flex items-center gap-2">
                        <i class="fas fa-file-pdf"></i> تصدير PDF
                    </a>
                </div>
            @else
                <!-- Student Self-Service Actions -->
                <div class="mb-8 flex flex-wrap gap-4 no-print">
                    @if ($student->can_edit_profile)
                        <a href="{{ route('profile.complete.view') }}"
                            class="bg-orange-600 text-white px-8 py-3 rounded-2xl font-bold font-cairo shadow-lg flex items-center gap-3">
                            <i class="fas fa-user-edit"></i> تعديل بيانات ملفي
                        </a>
                    @endif
                    <a href="{{ route('students.export-pdf', $student) }}"
                        class="bg-navy text-white px-8 py-3 rounded-2xl font-bold font-cairo shadow-lg flex items-center gap-3">
                        <i class="fas fa-file-pdf"></i> تصدير ملفي PDF
                    </a>
                    @if ($student->activeFoodSubscription)
                        <a href="{{ route('student.meals.attendance') }}"
                            class="bg-emerald-600 text-white px-8 py-3 rounded-2xl font-bold font-cairo shadow-lg flex items-center gap-3">
                            <i class="fas fa-utensils"></i> إدارة وجباتي
                        </a>
                    @endif
                </div>
            @endif

            <!-- Header Profile Card -->
            <div
                class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8 mb-8 flex flex-col md:flex-row items-center gap-8 relative overflow-hidden group">
                <div class="relative z-10 flex flex-col items-center gap-3">
                    <div class="w-32 h-32 bg-gray-50 rounded-full border-4 border-gold shadow-lg overflow-hidden shrink-0">
                        <img src="{{ $student->photo ? asset('storage/' . $student->photo) : ($student->user->avatar ? asset('storage/' . $student->user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($student->name_ar) . '&background=004274&color=fff&size=128') }}"
                            alt="Profile" class="w-full h-full object-cover">
                    </div>
                </div>

                <div class="relative z-10 flex-1 text-center md:text-right">
                    <div
                        class="inline-block bg-navy/10 text-navy px-4 py-1.5 rounded-full text-[10px] font-black font-cairo mb-3">
                        طالب سكن | {{ $student->center->name }}</div>
                    @if ($student->program)
                        <div class="inline-block mb-3 ml-2"><x-program-badge :program="$student->program" /></div>
                    @endif
                    <div
                        class="inline-block bg-gold/10 text-gold px-4 py-1.5 rounded-full text-[10px] font-black font-mono mb-3 mr-2">
                        {{ $student->barcode }}</div>
                    <h1 class="text-4xl font-black text-navy font-cairo mb-2">{{ $student->name_ar }}</h1>
                    <div
                        class="flex flex-wrap justify-center md:justify-start gap-5 text-gray-500 font-almarai text-sm mt-4">
                        <span class="flex items-center gap-2 bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-100"><i
                                class="fas fa-id-card text-gold"></i> {{ $student->national_id }}</span>
                        <span class="flex items-center gap-2 bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-100"><i
                                class="fas fa-graduation-cap text-gold"></i> {{ $student->university }}</span>
                        <span class="flex items-center gap-2 bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-100"><i
                                class="fas fa-phone text-gold"></i> <span
                                dir="ltr">{{ $student->phone ?? 'غير متوفر' }}</span></span>
                    </div>
                </div>

                <div class="relative z-10 w-full md:w-auto mt-6 md:mt-0 flex flex-col items-center gap-4">
                    <div class="bg-white p-3 rounded-2xl shadow border border-gold/20 flex flex-col items-center gap-2">
                        <p class="text-[10px] text-gray-400 font-cairo font-bold">باركود الطالب</p>
                        {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(110)->generate($student->barcode) !!}
                        <div class="flex items-center gap-2 mt-1">
                            <code id="studentBarcodeVal"
                                class="text-[9px] text-navy font-mono bg-blue-50 px-2 py-0.5 rounded">{{ $student->barcode }}</code>
                            <button onclick="copyBarcode('studentBarcodeVal')"
                                class="text-navy hover:text-gold transition-colors text-xs"><i
                                    class="fas fa-copy"></i></button>
                        </div>
                    </div>
                    @php
                        $statusColors = [
                            'registered' => 'bg-gold/10 text-gold border-gold/20',
                            'residing' => 'bg-navy/10 text-navy border-navy/20',
                            'left' => 'bg-gray-100 text-gray-700 border-gray-200',
                            'graduated' => 'bg-purple-100 text-purple-700 border-purple-200',
                            'suspended' => 'bg-red-100 text-red-700 border-red-200',
                        ];
                        $statusLabels = [
                            'registered' => $student->is_profile_approved ? 'مقيم' : 'حجز مبدئي',
                            'residing' => 'مقيم بالمركز',
                            'left' => 'مخلي طرفه',
                            'graduated' => 'متخرج',
                            'suspended' => 'موقوف',
                        ];
                        $color = $statusColors[$student->status] ?? 'bg-gray-100 text-gray-700';
                        $label = $statusLabels[$student->status] ?? $student->status;
                    @endphp
                    <div class="px-8 py-2.5 rounded-2xl border-2 {{ $color }} text-center w-full shadow-sm">
                        <p class="text-xl font-black font-cairo">{{ $label }}</p>
                    </div>
                </div>
            </div>

            <!-- Content Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Right Column (Main Info) -->
                <div class="md:col-span-2 space-y-8">
                    @if (isset($todayMeals) && count($todayMeals) > 0)
                        <!-- Today's Meals Attendance -->
                        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 overflow-hidden">
                            <div class="flex justify-between items-center mb-6 border-b border-gray-50 pb-4">
                                <h3 class="text-xl font-bold font-cairo text-gray-800 flex items-center gap-3">
                                    <i class="fas fa-clock text-amber-500"></i> حضور الوجبات اليوم
                                </h3>
                                <a href="{{ route('student.meals.attendance') }}"
                                    class="text-xs text-primary font-bold hover:underline">إدارة الكل</a>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                @foreach ($todayMeals as $meal)
                                    <div
                                        class="p-4 rounded-2xl border {{ $meal->status === 'absent' ? 'bg-red-50 border-red-100' : ($meal->status === 'late' ? 'bg-amber-50 border-amber-100' : 'bg-green-50 border-green-100') }}">
                                        <div class="flex justify-between items-start mb-2">
                                            <span
                                                class="text-xs font-bold font-cairo {{ $meal->status === 'absent' ? 'text-red-600' : ($meal->status === 'late' ? 'text-amber-600' : 'text-green-600') }}">
                                                {{ $meal->label }}
                                            </span>
                                            <i
                                                class="fas {{ $meal->type === 'breakfast' ? 'fa-sun' : ($meal->type === 'lunch' ? 'fa-utensils' : 'fa-moon') }} opacity-30"></i>
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-lg font-black font-cairo text-gray-800">
                                                @if ($meal->status === 'normal')
                                                    حاضر
                                                @elseif($meal->status === 'late')
                                                    سأتأخر
                                                @else
                                                    غائب
                                                @endif
                                            </span>
                                            <span class="text-[10px] text-gray-400 font-mono">{{ $meal->time }}</span>
                                        </div>
                                        @if (auth()->user()->hasRole('student') && $meal->can_edit)
                                            <div class="mt-3 flex gap-1">
                                                @if ($meal->status !== 'late' && !$meal->is_late_expired)
                                                    <form action="{{ route('student.meals.attendance.update') }}"
                                                        method="POST" class="flex-1">
                                                        @csrf
                                                        <input type="hidden" name="meal_type"
                                                            value="{{ $meal->type }}">
                                                        <button type="submit" name="status" value="late"
                                                            class="w-full py-1 bg-amber-500 text-white rounded-lg text-[10px] font-bold">تأخير</button>
                                                    </form>
                                                @endif
                                                @if ($meal->status !== 'absent' && !$meal->is_absent_expired)
                                                    <form action="{{ route('student.meals.attendance.update') }}"
                                                        method="POST" class="flex-1">
                                                        @csrf
                                                        <input type="hidden" name="meal_type"
                                                            value="{{ $meal->type }}">
                                                        <button type="submit" name="status" value="absent"
                                                            class="w-full py-1 bg-red-500 text-white rounded-lg text-[10px] font-bold">غياب</button>
                                                    </form>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Personal & Legal Details -->
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 overflow-hidden">
                        <h3
                            class="text-xl font-bold font-cairo text-gray-800 mb-6 flex items-center gap-3 border-b border-gray-50 pb-4">
                            <i class="fas fa-id-badge text-primary"></i> البيانات الشخصية والعدلية
                        </h3>
                        <table class="w-full font-almarai text-sm border-collapse">
                            <tr>
                                <th class="text-right text-gray-400 font-normal py-3 border-b border-gray-50">الاسم الكامل:
                                </th>
                                <td class="text-right font-bold text-gray-800 py-3 border-b border-gray-50">
                                    {{ $student->name_ar }} ({{ $student->surname }})</td>
                                <th class="text-right text-gray-400 font-normal py-3 border-b border-gray-50 px-4">الجنسية:
                                </th>
                                <td class="text-right font-bold text-gray-800 py-3 border-b border-gray-50">
                                    {{ $student->nationality }}</td>
                            </tr>
                            <tr>
                                <th class="text-right text-gray-400 font-normal py-3 border-b border-gray-50">تاريخ
                                    الميلاد:</th>
                                <td class="text-right font-bold text-gray-800 py-3 border-b border-gray-50">
                                    {{ $student->date_of_birth?->format('Y-m-d') }}</td>
                                <th class="text-right text-gray-400 font-normal py-3 border-b border-gray-50 px-4">رقم
                                    البطاقة:</th>
                                <td class="text-right font-bold text-gray-800 py-3 border-b border-gray-50 font-mono">
                                    {{ $student->id_card_number }}</td>
                            </tr>
                            <tr>
                                <th class="text-right text-gray-400 font-normal py-3 border-b border-gray-50">الحالة
                                    الصحية:</th>
                                <td class="text-right font-bold text-gray-800 py-3 border-b border-gray-50">
                                    {{ $student->health_status == 'good' ? 'سليم' : 'بحاجة لمتابعة' }}</td>
                                <th class="text-right text-gray-400 font-normal py-3 border-b border-gray-50 px-4">فصيلة
                                    الدم:</th>
                                <td class="text-right font-bold text-amber-600 font-mono py-3 border-b border-gray-50">
                                    {{ $student->blood_type ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="text-right text-gray-400 font-normal py-3">العنوان:</th>
                                <td colspan="3" class="text-right font-bold text-gray-800 py-3">
                                    {{ $student->governorate ?? '-' }} - {{ $student->district ?? '-' }}
                                    @if ($student->address)
                                        ({{ $student->address }})
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>

                    <!-- Housing Details -->
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                        <h3 class="text-xl font-bold font-cairo text-gray-800 mb-6"><i
                                class="fas fa-bed text-primary ml-2"></i> بيانات السكن الحالي</h3>
                        @if ($currentRoom)
                            <table class="w-full font-almarai text-sm border-collapse">
                                <tr class="bg-gray-50">
                                    <th class="border border-gray-100 p-3 text-center">المبنى</th>
                                    <th class="border border-gray-100 p-3 text-center">الطابق</th>
                                    <th class="border border-gray-100 p-3 text-center">الشقة</th>
                                    <th class="border border-gray-100 p-3 text-center bg-primary/5 text-primary">رقم الغرفة
                                    </th>
                                </tr>
                                <tr>
                                    <td class="border border-gray-100 p-4 text-center font-bold">
                                        {{ $currentRoom->room->building }}</td>
                                    <td class="border border-gray-100 p-4 text-center font-bold">
                                        {{ $currentRoom->room->floor }}</td>
                                    <td class="border border-gray-100 p-4 text-center font-bold">
                                        {{ $currentRoom->room->apartment ?? '-' }}</td>
                                    <td class="border border-gray-100 p-4 text-center font-black text-primary text-xl">
                                        {{ $currentRoom->room->room_number }}</td>
                                </tr>
                            </table>
                        @else
                            <div
                                class="bg-yellow-50 text-yellow-700 p-6 rounded-2xl border border-yellow-200 text-center font-almarai">
                                غير مسكن في أي غرفة حالياً.</div>
                        @endif
                    </div>

                    <!-- Violations & Penalties -->
                    @if ($student->allows('violations'))
                        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                            <h3 class="text-xl font-bold font-cairo text-gray-800 mb-6 flex justify-between items-center">
                                <span><i class="fas fa-exclamation-triangle text-red-500 ml-2"></i> سجل المخالفات
                                    والعقوبات</span>
                                <span
                                    class="bg-red-50 text-red-600 px-3 py-1 rounded-full text-xs font-black">{{ $student->violations->count() }}</span>
                            </h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full text-right text-sm">
                                    <thead>
                                        <tr class="bg-gray-50 font-cairo text-gray-600">
                                            <th class="px-4 py-3">التاريخ</th>
                                            <th class="px-4 py-3">نوع المخالفة</th>
                                            <th class="px-4 py-3">الحالة</th>
                                        </tr>
                                    </thead>
                                    <tbody class="font-almarai">
                                        @if ($student->violations->count() > 0)
                                            @foreach ($student->violations as $violation)
                                                <tr class="border-b border-gray-50 hover:bg-gray-50">
                                                    <td class="px-4 py-4 text-gray-500 font-mono">
                                                        {{ $violation->violation_date->format('Y/m/d') }}</td>
                                                    <td class="px-4 py-4 font-bold text-gray-800">{{ $violation->type }}
                                                    </td>
                                                    <td class="px-4 py-4 text-xs">
                                                        {{ $violation->penalty ? 'تمت العقوبة' : 'بانتظار الإجراء' }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="3" class="px-4 py-8 text-center text-gray-400">لا توجد
                                                    مخالفات مسجلة.</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    <!-- Grades & Achievements -->
                    @if ($student->allows('evaluation'))
                        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                            <div class="flex justify-between items-center mb-6">
                                <h3 class="text-xl font-bold font-cairo text-navy"><i
                                        class="fas fa-scroll text-gold ml-2"></i> بيان الدرجات والإنجازات</h3><a
                                    href="{{ route('student-grades.index', ['student_id' => $student->id]) }}"
                                    class="text-xs text-primary font-bold">عرض الكل</a>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @if ($student->grades()->count() > 0)
                                    @foreach ($student->grades()->latest()->take(2)->get() as $grade)
                                        <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100">
                                            <p class="text-xs font-bold text-gray-800">{{ $grade->semester }} -
                                                {{ $grade->academic_year }}</p>
                                            <p class="text-lg font-black text-emerald-600 font-mono">
                                                {{ number_format($grade->gpa_percentage, 2) }}%</p>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="col-span-2 text-center py-4 text-gray-400 text-xs">لا توجد بيانات درجات.
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Left Column (Metadata) -->
                <div class="space-y-8">
                    <!-- Academic Summary -->
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                        <h3 class="text-lg font-bold font-cairo text-gray-800 mb-6 border-b border-gray-50 pb-4"><i
                                class="fas fa-graduation-cap text-primary ml-2"></i> ملخص الأكاديمي</h3>
                        <table class="w-full font-almarai text-sm border-collapse">
                            <tr>
                                <th class="text-right text-gray-400 font-normal py-2 border-b border-gray-50">رقم القيد:
                                </th>
                                <td class="text-left font-bold text-gray-800 py-2 border-b border-gray-50 font-mono">
                                    {{ $student->university_id }}</td>
                            </tr>
                            <tr>
                                <th class="text-right text-gray-400 font-normal py-2 border-b border-gray-50">الكلية:</th>
                                <td class="text-left font-bold text-gray-800 py-2 border-b border-gray-50">
                                    {{ $student->college ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="text-right text-gray-400 font-normal py-2">المستوى:</th>
                                <td class="text-left py-2"><span
                                        class="bg-primary/10 text-primary px-2 py-1 rounded text-xs font-bold">{{ $student->academic_level ?? '-' }}</span>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <!-- Financial Summary -->
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                        <h3 class="text-lg font-bold font-cairo text-gray-800 mb-6 border-b border-gray-50 pb-4">
                            <i class="fas fa-money-bill-wave text-emerald-500 ml-2"></i> الوضع المالي (الرسوم)
                        </h3>
                        <div class="space-y-4 font-almarai">
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-500">إجمالي الرسوم السنوية:</span>
                                <span class="font-bold text-gray-800">{{ number_format($student->annual_fees, 2) }}
                                    ر.ي</span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-500">إجمالي ما تم سداده:</span>
                                <span class="font-bold text-emerald-600">{{ number_format($student->total_paid, 2) }}
                                    ر.ي</span>
                            </div>
                            <div class="pt-3 border-t border-gray-50 flex justify-between items-center">
                                <span class="text-gray-800 font-bold">المتبقي:</span>
                                @if ($student->remaining_fees <= 0)
                                    <span
                                        class="bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full text-xs font-black">خالص
                                        السداد</span>
                                @else
                                    <span
                                        class="text-red-600 font-black text-lg">{{ number_format($student->remaining_fees, 2) }}
                                        <small class="text-[10px]">ر.ي</small></span>
                                @endif
                            </div>

                            <!-- Progress Bar -->
                            @php
                                $percent =
                                    $student->annual_fees > 0
                                        ? min(100, ($student->total_paid / $student->annual_fees) * 100)
                                        : 0;
                            @endphp
                            <div class="w-full bg-gray-100 rounded-full h-2 mt-4">
                                <div class="bg-emerald-500 h-2 rounded-full transition-all duration-500"
                                    style="width: {{ $percent }}%"></div>
                            </div>
                            <div class="text-[10px] text-center text-gray-400">نسبة السداد: {{ round($percent) }}%</div>
                        </div>
                    </div>

                    <!-- Emergency & Family -->
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                        <h3 class="text-lg font-bold font-cairo text-gray-800 mb-6 border-b border-gray-50 pb-4"><i
                                class="fas fa-phone-alt text-red-500 ml-2"></i> بيانات الطوارئ</h3>
                        <table class="w-full font-almarai text-sm border-collapse">
                            <tr>
                                <th class="text-right text-gray-400 font-normal py-2 border-b border-gray-50">جهة الاتصال:
                                </th>
                                <td class="text-left font-bold text-gray-800 py-2 border-b border-gray-50">
                                    {{ $student->emergency_name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="text-right text-gray-400 font-normal py-2 border-b border-gray-50">الصلة:</th>
                                <td class="text-left font-bold text-gray-800 py-2 border-b border-gray-50">
                                    {{ $student->emergency_relation ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="text-right text-gray-400 font-normal py-2">رقم الهاتف:</th>
                                <td class="text-left font-bold text-red-900 font-mono py-2" dir="ltr">
                                    {{ $student->emergency_phone ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>

                    <!-- Legal Documents -->
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                        <h3 class="text-lg font-bold font-cairo text-gray-800 mb-6 border-b border-gray-50 pb-4"><i
                                class="fas fa-folder-open text-amber-500 ml-2"></i> الوثائق المرفوعة</h3>
                        <div class="grid grid-cols-1 gap-3 font-almarai text-xs">
                            @foreach (['id_card_file' => 'البطاقة الشخصية', 'certificate_file' => 'شهادة المؤهل', 'university_card_file' => 'البطاقة الجامعية', 'photo' => 'الصورة الشخصية'] as $key => $title)
                                @if ($student->$key)
                                    <a href="{{ asset('storage/' . $student->$key) }}" target="_blank"
                                        class="flex items-center justify-between p-4 bg-gray-50 hover:bg-gold/10 rounded-2xl transition-all group">
                                        <span class="font-bold text-gray-700">{{ $title }}</span>
                                        <i class="fas fa-external-link-alt text-gray-300 group-hover:text-gold"></i>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Modals -->
    <!-- Violation Modal -->
    <div id="violationModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-3xl w-full max-w-lg overflow-hidden shadow-2xl">
            <div class="p-8 border-b border-gray-50 flex justify-between items-center">
                <h3 class="text-xl font-bold font-cairo">تسجيل مخالفة</h3><button onclick="closeModal('violationModal')"
                    class="text-gray-400"><i class="fas fa-times"></i></button>
            </div>
            <form action="{{ route('violations.store') }}" method="POST" class="p-8 space-y-4">
                @csrf<input type="hidden" name="student_id" value="{{ $student->id }}">
                <div><label class="block text-sm font-bold font-cairo mb-2">نوع المخالفة</label><input type="text"
                        name="type" required class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-almarai"></div>
                <div><label class="block text-sm font-bold font-cairo mb-2">الدرجة</label><select name="severity"
                        class="w-full bg-gray-50 border-0 rounded-2xl p-4">
                        <option value="minor">بسيطة</option>
                        <option value="moderate">متوسطة</option>
                        <option value="severe">جسيمة</option>
                    </select></div>
                <div><label class="block text-sm font-bold font-cairo mb-2">التفاصيل</label>
                    <textarea name="description" required class="w-full bg-gray-50 border-0 rounded-2xl p-4" rows="3"></textarea>
                </div>
                <button type="submit" class="w-full bg-red-600 text-white py-4 rounded-2xl font-bold font-cairo">حفظ
                    المخالفة</button>
            </form>
        </div>
    </div>

    <!-- Commitment Modal -->
    <div id="commitmentModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-3xl w-full max-w-lg overflow-hidden shadow-2xl">
            <div class="p-8 border-b border-gray-50 flex justify-between items-center">
                <h3 class="text-xl font-bold font-cairo">تسجيل تعهد</h3><button onclick="closeModal('commitmentModal')"
                    class="text-gray-400"><i class="fas fa-times"></i></button>
            </div>
            <form action="{{ route('commitments.store') }}" method="POST" class="p-8 space-y-4">
                @csrf<input type="hidden" name="student_id" value="{{ $student->id }}">
                <div><label class="block text-sm font-bold font-cairo mb-2">نص التعهد</label>
                    <textarea name="text" required class="w-full bg-gray-50 border-0 rounded-2xl p-4" rows="4"></textarea>
                </div>
                <button type="submit" class="w-full bg-orange-600 text-white py-4 rounded-2xl font-bold font-cairo">حفظ
                    التعهد</button>
            </form>
        </div>
    </div>

    <!-- Absence Modal -->
    <div id="absenceModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-3xl w-full max-w-lg overflow-hidden shadow-2xl">
            <div class="p-8 border-b border-gray-50 flex justify-between items-center">
                <h3 class="text-xl font-bold font-cairo">تسجيل غياب</h3><button onclick="closeModal('absenceModal')"
                    class="text-gray-400"><i class="fas fa-times"></i></button>
            </div>
            <form action="{{ route('absences.store') }}" method="POST" class="p-8 space-y-4">
                @csrf<input type="hidden" name="student_id" value="{{ $student->id }}">
                <div><label class="block text-sm font-bold font-cairo mb-2">التاريخ</label><input type="date"
                        name="date" value="{{ date('Y-m-d') }}" required
                        class="w-full bg-gray-50 border-0 rounded-2xl p-4"></div>
                <button type="submit" class="w-full bg-blue-600 text-white py-4 rounded-2xl font-bold font-cairo">حفظ
                    الغياب</button>
            </form>
        </div>
    </div>

    <!-- Transfer Modal -->
    <div id="transferModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-3xl w-full max-w-lg overflow-hidden shadow-2xl">
            <div class="p-8 border-b border-gray-50 flex justify-between items-center">
                <h3 class="text-xl font-bold font-cairo">نقل غرفة</h3><button onclick="closeModal('transferModal')"
                    class="text-gray-400"><i class="fas fa-times"></i></button>
            </div>
            <form action="{{ route('students.transfer', $student) }}" method="POST" class="p-8 space-y-4">
                @csrf
                <div><label class="block text-sm font-bold font-cairo mb-2">اختر الغرفة الجديدة</label>
                    <select name="new_room_id" required class="w-full bg-gray-50 border-0 rounded-2xl p-4">
                        @foreach (\App\Models\Room::where('center_id', auth()->user()->center_id)->where('status', 'available')->get() as $room)
                            <option value="{{ $room->id }}">غرفة {{ $room->room_number }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="w-full bg-indigo-600 text-white py-4 rounded-2xl font-bold font-cairo">تأكيد
                    النقل</button>
            </form>
        </div>
    </div>

    <!-- Penalty Modal -->
    <div id="penaltyModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-3xl w-full max-w-lg overflow-hidden shadow-2xl">
            <div class="p-8 border-b border-gray-50 flex justify-between items-center">
                <h3 class="text-xl font-bold font-cairo">تطبيق عقوبة</h3><button onclick="closeModal('penaltyModal')"
                    class="text-gray-400"><i class="fas fa-times"></i></button>
            </div>
            <form action="{{ route('penalties.store') }}" method="POST" class="p-8 space-y-4">
                @csrf<input type="hidden" name="student_id" value="{{ $student->id }}">
                <div><label class="block text-sm font-bold font-cairo mb-2">نوع العقوبة</label>
                    <select name="type" class="w-full bg-gray-50 border-0 rounded-2xl p-4">
                        <option value="verbal_warning">تنبيه شفهي</option>
                        <option value="written_warning">إنذار خطي</option>
                        <option value="temporary_suspension">إيقاف مؤقت</option>
                        <option value="expulsion">فصل نهائي</option>
                    </select>
                </div>
                <div><label class="block text-sm font-bold font-cairo mb-2">السبب</label>
                    <textarea name="description" required class="w-full bg-gray-50 border-0 rounded-2xl p-4" rows="3"></textarea>
                </div>
                <button type="submit" class="w-full bg-red-800 text-white py-4 rounded-2xl font-bold font-cairo">تطبيق
                    العقوبة</button>
            </form>
        </div>
    </div>

    @include('partials.print_footer')

    <style>
        @media print {

            .no-print,
            .mb-6,
            .mb-8,
            .grid,
            .flex,
            .bg-white,
            .rounded-3xl,
            .shadow-sm {
                /* Resetting some values for print */
                box-shadow: none !important;
                border-radius: 0 !important;
            }

            body {
                background: white !important;
                color: black !important;
                font-family: 'Cairo', sans-serif;
            }

            .container {
                width: 100% !important;
                max-width: none !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            /* Profile Header Print */
            .group {
                border: 2px solid #004274 !important;
                padding: 20px !important;
                margin-bottom: 20px !important;
                display: flex !important;
                flex-direction: row !important;
                align-items: center !important;
                gap: 30px !important;
            }

            /* Table Styles for Sections */
            .print-table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 20px;
            }

            .print-table th,
            .print-table td {
                border: 1px solid #ddd;
                padding: 10px;
                text-align: right;
            }

            .print-table th {
                background-color: #f8f9fa !important;
                -webkit-print-color-adjust: exact;
                width: 30%;
                font-weight: bold;
                color: #004274;
            }

            h3 {
                border-bottom: 2px solid #004274;
                padding-bottom: 5px;
                margin-top: 25px;
                color: #004274 !important;
            }

            /* Hide everything that isn't content */
            .no-print,
            button,
            form,
            .modal,
            .fixed {
                display: none !important;
            }

            /* Layout adjustments */
            .grid-cols-1,
            .grid-cols-2,
            .grid-cols-3 {
                display: block !important;
            }

            .md\:col-span-2,
            .space-y-8 {
                width: 100% !important;
            }

            /* Show document links as text or hidden */
            .group-hover\:text-gold {
                display: none !important;
            }
        }
    </style>

    <script>
        function openViolationModal() {
            document.getElementById('violationModal').classList.remove('hidden');
            document.getElementById('violationModal').classList.add('flex');
        }

        function openCommitmentModal() {
            document.getElementById('commitmentModal').classList.remove('hidden');
            document.getElementById('commitmentModal').classList.add('flex');
        }

        function openAbsenceModal() {
            document.getElementById('absenceModal').classList.remove('hidden');
            document.getElementById('absenceModal').classList.add('flex');
        }

        function openTransferModal() {
            document.getElementById('transferModal').classList.remove('hidden');
            document.getElementById('transferModal').classList.add('flex');
        }

        function openLeaveModal() {
            document.getElementById('leaveModal').classList.remove('hidden');
            document.getElementById('leaveModal').classList.add('flex');
        }

        function openPenaltyModal() {
            document.getElementById('penaltyModal').classList.remove('hidden');
            document.getElementById('penaltyModal').classList.add('flex');
        }

        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
            document.getElementById(id).classList.remove('flex');
        }

        function copyBarcode(elementId) {
            const token = document.getElementById(elementId).textContent.trim();
            navigator.clipboard.writeText(token).then(() => {
                if (window.Swal) Swal.fire({
                    icon: 'success',
                    title: 'تم النسخ!',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });
                else alert('تم النسخ');
            });
        }
    </script>
@endsection
