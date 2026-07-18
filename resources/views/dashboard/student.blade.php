@extends ('layouts.app')
@php
    /** @var \App\Models\Student $student */
@endphp

@section ('title', 'ملفي الشخصي')

@section ('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-6xl mx-auto">

            @if (!$student->is_profile_approved)
                <div
                    class="bg-yellow-50 border-r-4 border-yellow-400 p-6 rounded-2xl mb-8 flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-4">
                        <div class="bg-yellow-100 p-3 rounded-full text-yellow-600">
                            <i class="fas fa-clock text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-yellow-800 font-cairo text-lg">بانتظار موافقة الإدارة</h3>
                            <p class="text-yellow-700 font-almarai text-sm mt-1">لقد قمت بإكمال بياناتك بنجاح. يرجى الانتظار حتى
                                تقوم إدارة المركز بمراجعة البيانات واعتمادها رسمياً.</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Header Profile Card -->
            <div
                class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8 mb-8 flex flex-col md:flex-row items-center gap-8 relative overflow-hidden">
                <!-- Decorative Background -->
                <div
                    class="absolute top-0 right-0 w-64 h-64 bg-primary/5 rounded-full blur-3xl -translate-y-1/2 translate-x-1/3">
                </div>

                <div
                    class="relative z-10 hidden md:block w-32 h-32 bg-gray-50 rounded-full border-4 border-white shadow-lg overflow-hidden shrink-0">
                    <img src="{{ auth()->user()->avatar ? asset('storage/' . auth()->user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($student->name_ar) . '&background=1e3a8a&color=fff&size=128' }}"
                        alt="Profile" class="w-full h-full object-cover">
                </div>

                <div class="relative z-10 flex-1 text-center md:text-right">
                    <div
                        class="inline-block bg-primary/10 text-primary px-3 py-1 rounded-full text-xs font-bold font-cairo mb-3">
                        طالب سكن | {{ $student->center->name }}
                    </div>
                    <h1 class="text-3xl font-bold text-gray-800 font-cairo mb-2">{{ $student->name_ar }}</h1>
                    <div
                        class="flex flex-wrap justify-center md:justify-start gap-4 text-gray-500 font-almarai text-sm mt-4">
                        <span class="flex items-center gap-2"><i class="fas fa-id-card"></i>
                            {{ $student->national_id }}</span>
                        <span class="flex items-center gap-2"><i class="fas fa-graduation-cap"></i>
                            {{ $student->university }}</span>
                        <span class="flex items-center gap-2"><i class="fas fa-phone"></i> <span
                                dir="ltr">{{ $student->phone ?? 'غير متوفر' }}</span></span>
                    </div>

                    <div class="mt-6 flex flex-wrap justify-center md:justify-start gap-4 no-print">
                        @if ($student->can_edit_profile)
                            <a href="{{ route('profile.complete.view') }}"
                                class="inline-flex items-center gap-3 bg-gradient-to-r from-orange-500 to-orange-600 text-white px-8 py-3 rounded-2xl hover:from-orange-600 hover:to-orange-700 transition-all font-bold font-cairo shadow-lg shadow-orange-200 transform hover:-translate-y-1 active:scale-95">
                                <i class="fas fa-edit text-lg"></i>
                                <span>تعديل بيانات ملفي الشخصي</span>
                            </a>
                        @endif
                        <button onclick="window.print()"
                            class="inline-flex items-center gap-3 bg-gray-800 text-white px-8 py-3 rounded-2xl hover:bg-gray-900 transition-all font-bold font-cairo shadow-lg transform hover:-translate-y-1 active:scale-95">
                            <i class="fas fa-print text-lg"></i>
                            <span>طباعة كرت الباركود</span>
                        </button>
                    </div>
                </div>

                <div class="relative z-10 w-full md:w-auto mt-6 md:mt-0 flex flex-col items-center gap-4">
                    <!-- Student Barcode Card -->
                    <div class="bg-white p-4 rounded-2xl shadow border border-primary/20 flex flex-col items-center gap-2">
                        <p class="text-xs text-gray-500 font-cairo font-bold">باركود الطالب (للتغذية/الحضور)</p>
                        {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(120)->generate($student->barcode) !!}
                        <div class="flex items-center gap-2 mt-2 w-full justify-center">
                            <code id="studentBarcodeVal"
                                class="text-sm text-primary font-mono bg-blue-50 px-3 py-1 rounded-lg border border-blue-100">{{ $student->barcode }}</code>
                            <button onclick="copyBarcode('studentBarcodeVal')" title="نسخ الباركود"
                                class="bg-primary hover:bg-primary-dark text-white px-3 py-1 rounded-lg transition-colors text-xs font-cairo shadow-sm flex items-center gap-1">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>

                    @php
                        $statusColors = [
                            'registered' => 'bg-blue-100 text-blue-700 border-blue-200',
                            'residing' => 'bg-green-100 text-green-700 border-green-200',
                            'left' => 'bg-gray-100 text-gray-700 border-gray-200',
                            'graduated' => 'bg-purple-100 text-purple-700 border-purple-200',
                            'suspended' => 'bg-red-100 text-red-700 border-red-200',
                        ];
                        $statusLabels = [
                            'registered' => 'حجز مبدئي',
                            'residing' => 'مقيم بالمركز',
                            'left' => 'مخلي طرفه',
                            'graduated' => 'متخرج',
                            'suspended' => 'موقوف',
                        ];
                        $color = $statusColors[$student->status] ?? 'bg-gray-100 text-gray-700';
                        $label = $statusLabels[$student->status] ?? $student->status;
                    @endphp
                    <div class="px-6 py-2.5 rounded-2xl border-2 {{ $color }} text-center w-full">
                        <p class="text-lg font-bold font-cairo flex items-center justify-center gap-2">
                            <i class="fas fa-info-circle opacity-50"></i> {{ $label }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Right Column -->
                <div class="md:col-span-2 space-y-8">

                    <!-- Housing Details -->
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                        <h3 class="text-xl font-bold font-cairo text-gray-800 mb-6 flex items-center gap-3">
                            <i class="fas fa-bed text-primary"></i> بيانات السكن الحالي
                        </h3>

                        @php
                            $currentRoom = $student->roomAssignments->where('released_at', null)->first();
                        @endphp

                        @if ($currentRoom)
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div class="bg-gray-50 p-4 rounded-xl">
                                    <p class="text-xs text-gray-500 font-bold mb-1">المبنى</p>
                                    <p class="font-bold text-gray-800">{{ $currentRoom->room->building }}</p>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-xl">
                                    <p class="text-xs text-gray-500 font-bold mb-1">الطابق</p>
                                    <p class="font-bold text-gray-800">{{ $currentRoom->room->floor }}</p>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-xl">
                                    <p class="text-xs text-gray-500 font-bold mb-1">الشقة</p>
                                    <p class="font-bold text-gray-800">{{ $currentRoom->room->apartment ?? '-' }}</p>
                                </div>
                                <div class="bg-primary/10 p-4 rounded-xl border border-primary/20">
                                    <p class="text-xs text-primary font-bold mb-1">رقم الغرفة</p>
                                    <p class="font-bold text-primary font-mono text-xl">{{ $currentRoom->room->room_number }}
                                    </p>
                                </div>
                            </div>
                        @else
                            <div class="bg-yellow-50 text-yellow-700 p-6 rounded-2xl border border-yellow-200 text-center">
                                <i class="fas fa-info-circle text-2xl mb-2"></i>
                                <p class="font-almarai">لم يتم تسكينك في أي غرفة حتى الآن.</p>
                            </div>
                        @endif
                    </div>

                    <!-- Latest Records (Mixed) -->
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                        <h3 class="text-xl font-bold font-cairo text-gray-800 mb-6 flex items-center gap-3">
                            <i class="fas fa-history text-secondary"></i> السجل السلوكي والإداري
                        </h3>

                        <div class="space-y-4">
                            @php
                                $records = collect();
                                foreach ($student->violations as $v)
                                    $records->push(['type' => 'violation', 'date' => $v->violation_date, 'item' => $v]);
                                foreach ($student->absences as $v)
                                    $records->push(['type' => 'absence', 'date' => $v->date, 'item' => $v]);
                                foreach ($student->leaves as $v)
                                    $records->push(['type' => 'leave', 'date' => $v->start_date, 'item' => $v]);
                                $sorted = $records->sortByDesc('date')->take(5);
                            @endphp

                            @if ($sorted->count() > 0)
                                @foreach ($sorted as $record)
                                    <div
                                        class="flex items-start gap-4 p-4 bg-gray-50 rounded-2xl hover:bg-gray-100 transition-colors">
                                        @if ($record['type'] == 'violation')
                                            <div
                                                class="w-10 h-10 rounded-full bg-red-100 text-red-600 flex items-center justify-center shrink-0">
                                                <i class="fas fa-exclamation-triangle"></i>
                                            </div>
                                            <div class="flex-1">
                                                <h4 class="font-bold text-gray-800 font-cairo">مخالفة سلوكية</h4>
                                                <p class="text-sm text-gray-500 font-almarai mt-1">
                                                    {{ \Illuminate\Support\Str::limit($record['item']->description, 50) }}
                                                </p>
                                            </div>
                                        @elseif ($record['type'] == 'absence')
                                            <div
                                                class="w-10 h-10 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center shrink-0">
                                                <i class="fas fa-calendar-times"></i>
                                            </div>
                                            <div class="flex-1">
                                                <h4 class="font-bold text-gray-800 font-cairo">غياب مسجل</h4>
                                                <p class="text-sm text-gray-500 font-almarai mt-1">
                                                    {{ $record['item']->notes ?? 'غياب بدون عذر' }}
                                                </p>
                                            </div>
                                        @elseif ($record['type'] == 'leave')
                                            <div
                                                class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                                                <i class="fas fa-plane-departure"></i>
                                            </div>
                                            <div class="flex-1">
                                                <h4 class="font-bold text-gray-800 font-cairo">استئذان / إجازة</h4>
                                                <p class="text-sm text-gray-500 font-almarai mt-1">{{ $record['item']->reason }}</p>
                                            </div>
                                        @endif
                                        <div class="text-xs font-mono text-gray-400">
                                            {{ \Carbon\Carbon::parse($record['date'])->format('Y-m-d') }}
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-center text-gray-400 font-almarai py-4">سجلك نظيف، لا توجد أي مدخلات سابقة.</p>
                            @endif
                        </div>
                    </div>

                </div>

                <!-- Left Column -->
                <div class="space-y-8">
                    <!-- Academic Info -->
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                        <h3 class="text-lg font-bold font-cairo text-gray-800 mb-6 border-b border-gray-50 pb-4">
                            <i class="fas fa-book-open text-primary ml-2"></i> البيانات الأكاديمية
                        </h3>
                        <ul class="space-y-4 font-almarai">
                            <li class="flex justify-between items-center">
                                <span class="text-gray-400 text-sm">الرقم الجامعي</span>
                                <span class="font-bold text-gray-800 font-mono">{{ $student->student_number }}</span>
                            </li>
                            <li class="flex justify-between items-center">
                                <span class="text-gray-400 text-sm">الكلية</span>
                                <span class="font-bold text-gray-800">{{ $student->college ?? '-' }}</span>
                            </li>
                            <li class="flex justify-between items-center">
                                <span class="text-gray-400 text-sm">التخصص</span>
                                <span class="font-bold text-gray-800">{{ $student->major ?? '-' }}</span>
                            </li>
                            <li class="flex justify-between items-center">
                                <span class="text-gray-400 text-sm">المستوى</span>
                                <span
                                    class="bg-primary/10 text-primary px-2 py-1 rounded text-xs font-bold">{{ $student->academic_level ?? '-' }}</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Emergency Info -->
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                        <h3 class="text-lg font-bold font-cairo text-gray-800 mb-6 border-b border-gray-50 pb-4">
                            <i class="fas fa-phone-alt text-red-500 ml-2"></i> بيانات الطوارئ
                        </h3>
                        <ul class="space-y-4 font-almarai">
                            <li class="flex justify-between items-center">
                                <span class="text-gray-400 text-sm">الاسم</span>
                                <span class="font-bold text-gray-800">{{ $student->emergency_name ?? '-' }}</span>
                            </li>
                            <li class="flex justify-between items-center">
                                <span class="text-gray-400 text-sm">الصلة</span>
                                <span class="font-bold text-gray-800">{{ $student->emergency_relation ?? '-' }}</span>
                            </li>
                            <li class="flex flex-col gap-2 mt-2 pt-2 border-t border-gray-50">
                                <span class="text-gray-400 text-sm">رقم التواصل</span>
                                <span class="font-bold text-red-600 font-mono text-left w-full block"
                                    dir="ltr">{{ $student->emergency_phone ?? '-' }}</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Meals Attendance Management -->
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                        <h3 class="text-lg font-bold font-cairo text-gray-800 mb-6 border-b border-gray-50 pb-4">
                            <i class="fas fa-calendar-check text-indigo-600 ml-2"></i> إدارة حضور الوجبات
                        </h3>
                        <p class="text-sm text-gray-500 font-almarai mb-4">أخبرنا بحالتك لوجبات اليوم (حاضر، متأخر، أو
                            غائب).</p>
                        <a href="{{ route('student.meals.attendance') }}"
                            class="w-full flex items-center justify-center gap-2 bg-indigo-50 text-indigo-700 py-3 rounded-2xl font-bold font-cairo hover:bg-indigo-100 transition-all border border-indigo-100">
                            <i class="fas fa-user-clock"></i> إدارة الحضور اليومي
                        </a>
                    </div>

                    <!-- Meals Info -->
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                        <h3 class="text-lg font-bold font-cairo text-gray-800 mb-6 border-b border-gray-50 pb-4">
                            <i class="fas fa-utensils text-green-600 ml-2"></i> التغذية
                        </h3>
                        @php
                            $subscription = $student->mealSubscription;
                        @endphp
                        @if ($subscription && $subscription->status == 'active')
                            <div
                                class="bg-gradient-to-l from-green-500 to-emerald-600 text-white p-6 rounded-3xl border border-green-400/30 shadow-lg shadow-green-100 flex items-center justify-between">
                                <div>
                                    <p class="font-bold font-cairo mb-1 text-lg"><i class="fas fa-check-circle ml-1"></i> مشترك
                                        بالوجبات</p>
                                    <p class="text-xs text-white/80 font-almarai italic">صالح حتى: {{ $subscription->end_date }}
                                    </p>
                                </div>
                                <a href="{{ route('nutrition.qr-groups.index') }}"
                                    class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-xl text-xs font-bold font-cairo transition-all backdrop-blur-md border border-white/30">
                                    <i class="fas fa-qrcode ml-1"></i> QR المجمع
                                </a>
                            </div>

                            <!-- Meal Subscription QR -->
                            <div class="mt-4 p-4 border-2 border-dashed border-gray-100 rounded-2xl text-center">
                                <p class="text-[10px] text-gray-400 font-bold uppercase mb-2 font-cairo">كود اشتراك التغذية
                                    الخاص بك</p>
                                <div class="bg-white p-3 inline-block rounded-xl border border-gray-50 shadow-inner">
                                    {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(120)->generate($subscription->qr_code) !!}
                                </div>
                                <div class="mt-3 flex items-center justify-center gap-2">
                                    <code id="subQrCodeVal"
                                        class="text-xs text-green-700 font-mono bg-green-50 px-3 py-1.5 rounded">{{ $subscription->qr_code }}</code>
                                    <button onclick="copyBarcode('subQrCodeVal')"
                                        class="text-green-600 hover:text-green-800 p-1.5 rounded-lg bg-green-50/50 transition-colors"
                                        title="نسخ الكود">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                            </div>

                        @else
                            <div
                                class="bg-gray-50 text-gray-400 p-6 rounded-2xl text-center text-sm font-almarai border border-gray-100 border-dashed">
                                <i class="fas fa-utensils text-2xl mb-2 opacity-20 block"></i>
                                أنت غير مشترك في الوجبات حالياً
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Barcode Print Section (Visible only in Print) -->
    <div id="barcode-print-section">
        <div class="print-card">
            <div class="card-header-logos">
                <img src="{{ asset('images/logos/scs_logo.png') }}" class="print-logo">
                <img src="{{ asset('images/logos/alawayil_logo.png') }}" class="print-logo">
            </div>
            <div class="student-name-print">{{ $student->name_ar }}</div>
            <div class="barcode-wrapper">
                {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(200)->generate($student->barcode) !!}
            </div>
            <div class="print-footer-info">
                <div>المركز: {{ $student->center->name }}</div>
                <div>الرقم الجامعي: {{ $student->university_id }}</div>
            </div>
        </div>
    </div>

    <style>
        #barcode-print-section {
            display: none;
        }

        @media print {

            /* Full page reset */
            body * {
                visibility: hidden;
            }

            #barcode-print-section,
            #barcode-print-section * {
                visibility: visible;
            }

            #barcode-print-section {
                display: flex !important;
                position: fixed;
                left: 0;
                top: 0;
                width: 100vw;
                height: 100vh;
                justify-content: center;
                align-items: center;
                background: white !important;
                z-index: 9999;
            }

            .print-card {
                border: 3px solid #1e293b;
                padding: 3rem;
                border-radius: 2rem;
                text-align: center;
                width: 450px;
                background: white !important;
                box-shadow: none !important;
            }

            .card-header-logos {
                display: flex;
                justify-content: center;
                gap: 1rem;
                margin-bottom: 2rem;
            }

            .print-logo {
                height: 50px;
            }

            .student-name-print {
                font-size: 28px;
                font-weight: 900;
                margin-bottom: 1.5rem;
                color: #1e293b;
                font-family: 'Cairo', sans-serif;
            }

            .barcode-wrapper {
                background: white !important;
                padding: 1rem;
                display: inline-block;
                border: 1px solid #e2e8f0;
                border-radius: 1rem;
                margin-bottom: 1.5rem;
            }

            .print-footer-info {
                font-size: 14px;
                color: #475569;
                font-family: 'Almarai', sans-serif;
                display: flex;
                flex-direction: column;
                gap: 0.25rem;
            }

            /* Hide everything else definitively */
            nav,
            aside,
            footer,
            .container,
            .no-print {
                display: none !important;
            }
        }
    </style>
    @push ('scripts')
        <script>
            function copyBarcode(elementId) {
                const token = document.getElementById(elementId).textContent.trim();
                navigator.clipboard.writeText(token).then(() => {
                    if (window.Swal) {
                        Swal.fire({
                            icon: 'success',
                            title: 'تم النسخ!',
                            text: 'يمكنك الآن لصق الرمز عندما يُطلب منك.',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3500,
                            timerProgressBar: true,
                        });
                    } else {
                        alert('تم نسخ الرمز: ' + token);
                    }
                }).catch(() => {
                    prompt('انسخ هذا الرمز يدوياً:', token);
                });
            }
        </script>
    @endpush
@endsection
