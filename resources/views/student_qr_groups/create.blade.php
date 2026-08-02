@extends('layouts.app')

@section('title', 'إنشاء رمز QR مجمع')

@section('content')
    <div class="max-w-4xl mx-auto py-4 sm:py-8 px-3 sm:px-6 lg:px-8">
        <div class="mb-6 sm:mb-8">
            <a href="{{ route('student-qr-groups.index') }}"
                class="text-navy hover:text-blue-700 font-bold mb-2 sm:mb-4 inline-flex items-center font-cairo text-sm sm:text-base">
                <i class="fas fa-arrow-right ml-2"></i> العودة للقائمة
            </a>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-navy font-cairo">إنشاء رمز QR مجمع</h1>
            <p class="mt-1 sm:mt-2 text-gray-600 font-almarai text-sm sm:text-base">اختر الطلاب الذين ترغب في ضم بياناتهم إلى الرمز المجمع الجديد.</p>
        </div>

        <form action="{{ route('student-qr-groups.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="card-premium p-4 sm:p-6">
                <h2 class="text-lg sm:text-xl font-bold text-navy mb-3 sm:mb-4 font-cairo border-b pb-2">الطالب الرئيسي</h2>
                <div
                    class="flex items-center space-x-3 space-x-reverse sm:space-x-4 bg-gray-50 p-3 sm:p-4 rounded-xl border border-dashed border-gray-300">
                    <div class="bg-navy text-white p-3 rounded-full">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <div>
                        <p class="text-lg font-bold text-navy font-cairo">{{ $student->name_ar }}</p>
                        <p class="text-sm text-gray-500 font-almarai">{{ $student->university_id }} | {{ $student->major }}
                        </p>
                    </div>
                </div>
                <p class="mt-2 text-xs text-gray-400 font-almarai">* سيتم إدراج بياناتك تلقائياً كطالب أساسي في الرمز.</p>
            </div>

            <div class="card-premium p-4 sm:p-6" x-data="{ search: '' }">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 border-b pb-2 gap-3 sm:gap-0">
                    <h2 class="text-lg sm:text-xl font-bold text-navy font-cairo">اختر الطلاب المضافين</h2>
                    <button type="button" onclick="startScanner()"
                        class="w-full sm:w-auto bg-navy text-white px-4 py-2.5 sm:py-2 rounded-xl text-sm font-bold font-cairo hover:bg-sky-900 transition-all flex justify-center">
                        <i class="fas fa-camera ml-2"></i> تصوير QR للطالب
                    </button>
                </div>

                <!-- Scanner / Manual Input Modal -->
                <div id="scannerModal"
                    class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/60 backdrop-blur-sm p-3 sm:p-4">
                    <div class="bg-white rounded-2xl sm:rounded-3xl w-full max-w-lg overflow-hidden shadow-2xl">
                        <div class="p-4 border-b flex justify-between items-center bg-navy text-white">
                            <h3 class="font-bold font-cairo">إضافة طالب (كاميرا / يدوي)</h3>
                            <button type="button" onclick="stopScanner()" class="text-white hover:text-gray-200">
                                <i class="fas fa-times text-xl"></i>
                            </button>
                        </div>

                        <!-- Tabs -->
                        <div class="flex border-b border-gray-100 p-2 gap-2 bg-gray-50">
                            <button type="button" onclick="switchScannerTab('camera')" id="camTabBtn"
                                class="flex-1 py-2 bg-navy text-white font-bold rounded-xl text-sm font-cairo transition-all shadow-sm">
                                <i class="fas fa-camera ml-1 text-gold"></i> كاميرا
                            </button>
                            <button type="button" onclick="switchScannerTab('manual')" id="manTabBtn"
                                class="flex-1 py-2 bg-white text-gray-500 font-bold rounded-xl text-sm font-cairo border border-gray-200 transition-all hover:bg-gray-100">
                                <i class="fas fa-keyboard ml-1"></i> إدخال يدوي
                            </button>
                        </div>

                        <!-- Camera View -->
                        <div id="camViewBox" class="relative bg-black h-64 sm:h-80 w-full">
                            <video id="video" class="w-full h-full object-cover"></video>
                            <canvas id="canvas" class="hidden"></canvas>
                            <div
                                class="absolute inset-0 border-2 border-white/30 m-8 sm:m-12 rounded-3xl pointer-events-none flex items-center justify-center">
                                <div
                                    class="absolute w-full h-full border-4 border-gold m-0 rounded-3xl opacity-50 scanner-line">
                                </div>
                            </div>
                        </div>

                        <!-- Manual Input View -->
                        <div id="manViewBox"
                            class="hidden p-4 sm:p-8 flex flex-col items-center justify-center h-64 sm:h-80 bg-gray-50">
                            <div
                                class="w-20 h-20 bg-gray-200 rounded-full flex items-center justify-center mb-6 shadow-inner">
                                <i class="fas fa-qrcode text-4xl text-gray-400"></i>
                            </div>
                            <p class="text-sm text-gray-600 font-bold font-cairo mb-2 text-center">أدخل أو الصق رمز الطالب
                            </p>
                            <p class="text-xs text-gray-400 font-almarai mb-6 text-center">يمكنك استخدام الباركود الخاص به
                                أو كود اشتراك التغذية.</p>

                            <div class="w-full relative">
                                <input type="text" id="manualCodeInput"
                                    class="w-full focus:ring-navy focus:border-navy border-gray-300 rounded-xl   text-center p-4 mb-4 text-sm"
                                    placeholder="مثال: 95RZIKReI1r...">
                                <button type="button" onclick="processManualEntry()"
                                    class="w-full bg-navy hover:bg-sky-900 text-white font-bold py-3.5 rounded-xl font-cairo transition-all shadow-md">
                                    <i class="fas fa-search ml-2 text-gold"></i> بحث وتسجيل
                                </button>
                            </div>
                        </div>

                        <!-- Debug/Info Box for Camera -->
                        <div id="camDebugBox" class="p-4 bg-gray-900 border-t border-gray-700">
                            <p class="text-center text-gray-400 font-almarai text-sm mb-2">وجه الكاميرا نحو باركود الطالب
                                ليتم اختياره تلقائياً</p>
                            <div
                                class="bg-black/60 rounded-xl px-4 py-2 text-center mt-1 min-h-[3rem] flex items-center justify-center">
                                <p class="text-[10px] text-gray-500  ">آخر قراءة: <span id="debugText"
                                        class="text-yellow-400">—</span></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative mb-6">
                    <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" x-model="search" placeholder="ابحث باسم الطالب أو الرقم الجامعي..."
                        class="block w-full pr-10 border-gray-300 rounded-xl focus:ring-navy focus:border-navy font-almarai transition-all">
                </div>

                <div class="max-h-96 overflow-y-auto space-y-2 pr-2" id="studentsContainer">
                    @foreach($students as $s)
                        <label
                            class="flex items-center p-3 rounded-xl hover:bg-gray-100 transition-all cursor-pointer border border-transparent hover:border-gray-200 student-row"
                            x-show="'{{ $s->name_ar }} {{ $s->university_id }}'.toLowerCase().includes(search.toLowerCase())"
                            data-barcode="{{ $s->barcode }}" data-sub-qr="{{ $s->activeFoodSubscription?->qr_code }}">
                            <input type="checkbox" name="student_ids[]" value="{{ $s->id }}"
                                class="student-checkbox w-5 h-5 text-navy border-gray-300 rounded focus:ring-navy ml-4">
                            <div class="flex-1">
                                <p class="font-bold text-navy font-cairo">{{ $s->name_ar }}</p>
                                <p class="text-xs text-gray-500 font-almarai">{{ $s->university_id }} | {{ $s->college }}</p>
                            </div>
                        </label>
                    @endforeach
                </div>

                @error('student_ids')
                    <p class="mt-2 text-sm text-red-600 font-almarai">{{ $message }}</p>
                @enderror
            </div>

            </div>

            <div class="card-premium p-4 sm:p-6">
                <h2 class="text-lg sm:text-xl font-bold text-navy mb-4 font-cairo border-b pb-2">إعدادات الرمز</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 font-cairo mb-2">تاريخ انتهاء الصلاحية
                            (اختياري)</label>
                        <input type="datetime-local" name="expires_at" value="{{ now()->endOfDay()->format('Y-m-d\TH:i') }}"
                            class="block w-full border-gray-300 rounded-xl focus:ring-navy focus:border-navy font-almarai">
                        <p class="mt-1 text-xs text-gray-400 font-almarai">الافتراضي هو نهاية اليوم الحالي.</p>
                    </div>
                </div>
            </div>

            <div class="flex justify-end pb-8">
                <button type="submit"
                    class="w-full sm:w-auto px-8 py-4 bg-navy text-white font-bold rounded-2xl hover:bg-sky-900 transition-all shadow-lg hover:shadow-navy/20 font-cairo text-lg sm:text-base">
                    <i class="fas fa-qrcode ml-2"></i> إنشاء الرمز المجمع
                </button>
            </div>
        </form>
    </div>
@endsection

@push('styles')
    <style>
        @keyframes scan {
            0% {
                top: 0%;
            }

            50% {
                top: 100%;
            }

            100% {
                top: 0%;
            }
        }

        .scanner-line {
            height: 2px;
            background: #D4A044;
            animation: scan 3s linear infinite;
            box-shadow: 0 0 15px #D4A044;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://unpkg.com/@zxing/library@latest/umd/index.min.js"></script>
    <script>
        let videoStream = null;
        let codeReader = null;
        let isScanning = false;

        async function startScanner() {
            const modal = document.getElementById('scannerModal');
            modal.classList.remove('hidden');

            try {
                if (codeReader) codeReader.reset();
                codeReader = new ZXing.BrowserMultiFormatReader();
                isScanning = true;

                // Force back camera on mobile (environment = back camera)
                const constraints = {
                    video: {
                        facingMode: { ideal: 'environment' },
                        width: { ideal: 1280 },
                        height: { ideal: 720 }
                    }
                };

                await codeReader.decodeFromConstraints(
                    constraints,
                    'video',
                    (result, err) => {
                        if (result && isScanning) {
                            const scanned = result.getText().trim();

                            // Show in debug box
                            const debugEl = document.getElementById('debugText');
                            if (debugEl) debugEl.textContent = scanned.substring(0, 60);

                            const found = handleScannedCode(scanned);
                            if (found) {
                                stopScanner();
                                Swal.fire({
                                    icon: 'success',
                                    title: 'تم التعرف على الطالب',
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 3000,
                                    timerProgressBar: true,
                                });
                            }
                        }
                        // Ignore errors - they are normal when no QR in frame
                    }
                );
            } catch (err) {
                // Fallback: try without facingMode constraint
                try {
                    if (codeReader) codeReader.reset();
                    codeReader = new ZXing.BrowserMultiFormatReader();
                    await codeReader.decodeFromConstraints(
                        { video: true },
                        'video',
                        (result, err) => {
                            if (result && isScanning) {
                                const scanned = result.getText().trim();
                                const debugEl = document.getElementById('debugText');
                                if (debugEl) debugEl.textContent = scanned.substring(0, 60);
                                const found = handleScannedCode(scanned);
                                if (found) {
                                    stopScanner();
                                    Swal.fire({ icon: 'success', title: 'تم التعرف على الطالب', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
                                }
                            }
                        }
                    );
                } catch (err2) {
                    const modal = document.getElementById('scannerModal');
                    const videoEl = document.getElementById('video');
                    if (videoEl) videoEl.style.display = 'none';

                    const isPermissionError = err2.name === 'NotAllowedError' || 
                        err2.name === 'PermissionDeniedError' || 
                        (err2.message && err2.message.toLowerCase().includes('permission'));
                    
                    const errorHtml = `
                        <div class="absolute inset-0 bg-gray-900 flex items-center justify-center z-50 rounded-2xl">
                            <div class="text-center px-5 py-6 max-w-xs mx-auto">
                                <div class="w-16 h-16 bg-red-900/50 rounded-full flex items-center justify-center mx-auto mb-3 border-2 border-red-500">
                                    <i class="fas fa-camera-slash text-red-400 text-2xl"></i>
                                </div>
                                <h3 class="text-white font-bold font-cairo text-base mb-2">
                                    ${isPermissionError ? 'تم رفض إذن الكاميرا' : 'لا يمكن الوصول إلى الكاميرا'}
                                </h3>
                                <p class="text-gray-400 text-xs font-almarai leading-relaxed mb-4">
                                    ${isPermissionError ? 'قام المتصفح بحجب الكاميرا. اتبع الخطوات لإصلاحها:' : (err2.message || 'خطأ غير معروف')}
                                </p>
                                ${isPermissionError ? `
                                <div class="bg-gray-800 rounded-xl p-3 text-right mb-4 border border-gray-700">
                                    <p class="text-yellow-400 text-xs font-bold font-cairo mb-2">📱 كيفية السماح بالكاميرا:</p>
                                    <ol class="text-gray-300 text-xs font-almarai space-y-1.5 list-decimal list-inside leading-relaxed">
                                        <li>اضغط على 🔒 في شريط العنوان</li>
                                        <li>اختر "إعدادات الموقع"</li>
                                        <li>اضغط على "الكاميرا"</li>
                                        <li>اختر "السماح"</li>
                                        <li>أعد تحميل الصفحة</li>
                                    </ol>
                                </div>` : ''}
                                <div class="flex flex-col gap-2">
                                    <button onclick="location.reload()" class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2.5 rounded-xl font-bold font-cairo text-sm transition-all">
                                        <i class="fas fa-redo ml-1"></i> إعادة المحاولة
                                    </button>
                                    <button onclick="document.getElementById('scannerModal').classList.add('hidden'); isScanning=false;" class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2.5 rounded-xl font-bold font-cairo text-sm transition-all">
                                        <i class="fas fa-times ml-1"></i> إغلاق
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                    // Inject error UI into the modal's video container
                    const container = document.getElementById('videoContainer') || modal.querySelector('.relative');
                    if (container) {
                        const errDiv = document.createElement('div');
                        errDiv.innerHTML = errorHtml;
                        container.appendChild(errDiv.firstElementChild);
                    }
                    isScanning = false;
                }
            }
        }


        function stopScanner() {
            const modal = document.getElementById('scannerModal');
            modal.classList.add('hidden');

            isScanning = false;
            if (codeReader) {
                codeReader.reset();
                codeReader = null;
            }
        }


        function handleScannedCode(scanned) {
            if (!scanned) return false;

            // 1. Try exact match on data-barcode or data-sub-qr
            let row = document.querySelector(`.student-row[data-barcode="${scanned}"], .student-row[data-sub-qr="${scanned}"]`);

            // 2. Try partial/contains match (scanned contains stored value, or stored value is inside scanned)
            if (!row) {
                const allRows = document.querySelectorAll('.student-row');
                for (const r of allRows) {
                    const bc = r.getAttribute('data-barcode') || '';
                    const sub = r.getAttribute('data-sub-qr') || '';
                    if (
                        (bc && (scanned.includes(bc) || bc.includes(scanned))) ||
                        (sub && (scanned.includes(sub) || sub.includes(scanned)))
                    ) {
                        row = r;
                        break;
                    }
                }
            }

            if (row) {
                const checkbox = row.querySelector('.student-checkbox');
                if (checkbox && !checkbox.checked) {
                    checkbox.checked = true;
                    // Trigger a smooth visual feedback
                    row.classList.add('bg-green-100', 'border-green-300', 'shadow-md');
                    row.scrollIntoView({ behavior: 'smooth', block: 'center' });

                    setTimeout(() => {
                        row.classList.remove('bg-green-100', 'border-green-300', 'shadow-md');
                    }, 2000);
                }
                return true; // found
            }
            return false;
        }

        function processManualEntry() {
            const input = document.getElementById('manualCodeInput');
            const token = input.value.trim();

            if (!token) {
                Swal.fire({
                    icon: 'warning',
                    title: 'تنبيه',
                    text: 'الرجاء إدخال الرمز أولاً',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });
                return;
            }

            const found = handleScannedCode(token);
            if (found) {
                stopScanner();
                Swal.fire({
                    icon: 'success',
                    title: 'تم التعرف على الطالب وتسجيله!',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                });
                input.value = '';
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'عذراً',
                    text: 'لم يتم العثور على طالب يطابق هذا الرمز في المركز أو الطالب ليس مشتركاً.',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 4000
                });
            }
        }

        function switchScannerTab(tab) {
            const camBtn = document.getElementById('camTabBtn');
            const manBtn = document.getElementById('manTabBtn');
            const camView = document.getElementById('camViewBox');
            const manView = document.getElementById('manViewBox');
            const debugBox = document.getElementById('camDebugBox');

            if (tab === 'camera') {
                camBtn.className = "flex-1 py-2 bg-navy text-white font-bold rounded-xl text-sm font-cairo transition-all shadow-sm";
                manBtn.className = "flex-1 py-2 bg-white text-gray-500 font-bold rounded-xl text-sm font-cairo border border-gray-200 transition-all hover:bg-gray-100";
                camView.classList.remove('hidden');
                debugBox.classList.remove('hidden');
                manView.classList.add('hidden');

                // Resume scanning
                isScanning = true;
            } else {
                manBtn.className = "flex-1 py-2 bg-navy text-white font-bold rounded-xl text-sm font-cairo transition-all shadow-sm";
                camBtn.className = "flex-1 py-2 bg-white text-gray-500 font-bold rounded-xl text-sm font-cairo border border-gray-200 transition-all hover:bg-gray-100";
                manView.classList.remove('hidden');
                camView.classList.add('hidden');
                debugBox.classList.add('hidden');

                // Pause camera processing to save battery
                isScanning = false;
                setTimeout(() => document.getElementById('manualCodeInput').focus(), 100);
            }
        }
    </script>
@endpush
