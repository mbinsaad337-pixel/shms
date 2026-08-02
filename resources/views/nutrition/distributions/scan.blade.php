@extends('layouts.nutrition')
@section('title', 'توزيع الوجبات - مسح QR')

@section('content')
    <div class="p-3 sm:p-4 max-w-3xl mx-auto">
        <div class="text-center mb-4">
            <div
                class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-teal-500 to-emerald-600 rounded-2xl flex items-center justify-center mx-auto mb-2 shadow-xl">
                <i class="fas fa-qrcode text-white text-xl sm:text-2xl"></i>
            </div>
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800 font-cairo">توزيع الوجبات</h2>
            <p class="text-gray-400 font-almarai text-xs sm:text-sm mt-1" id="mealTypeLabel">جارٍ تحديد نوع الوجبة...</p>
        </div>

        <!-- Scanner Area -->
        <div class="bg-white rounded-2xl sm:rounded-3xl shadow-sm border border-gray-100 p-3 sm:p-6 mb-4">
            <div class="flex items-center gap-2 sm:gap-3 mb-3 sm:mb-4">
                <button onclick="showTab('camera')" id="cameraTab"
                    class="flex-1 py-2 sm:py-2.5 rounded-xl font-bold font-cairo text-sm transition-all bg-teal-600 text-white shadow-lg shadow-teal-200">
                    <i class="fas fa-camera ml-1"></i> كاميرا
                </button>
                <button onclick="showTab('photo')" id="photoTab"
                    class="flex-1 py-2 sm:py-2.5 rounded-xl font-bold font-cairo text-sm transition-all bg-blue-600 text-white shadow-lg shadow-blue-200">
                    <i class="fas fa-image ml-1"></i> تصوير QR
                </button>
                <button onclick="showTab('manual')" id="manualTab"
                    class="flex-1 py-2 sm:py-2.5 rounded-xl font-bold font-cairo text-sm transition-all bg-gray-100 text-gray-600 hover:bg-gray-200">
                    <i class="fas fa-keyboard ml-1"></i> يدوي
                </button>
            </div>

            <!-- Camera Scanner -->
            <div id="cameraPanel">
                <div id="scannerContainer" class="relative bg-gray-900 rounded-2xl overflow-hidden h-64 sm:h-80">
                    <video id="video" class="w-full h-full object-cover" autoplay playsinline></video>
                    <canvas id="canvas" class="hidden absolute inset-0"></canvas>
                    <!-- Scan overlay -->
                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                        <div class="w-44 h-44 sm:w-52 sm:h-52 border-4 border-teal-400 rounded-2xl relative">
                            <div class="absolute top-0 right-0 w-7 h-7 border-t-4 border-r-4 border-teal-300 rounded-tr-xl">
                            </div>
                            <div class="absolute top-0 left-0 w-7 h-7 border-t-4 border-l-4 border-teal-300 rounded-tl-xl">
                            </div>
                            <div
                                class="absolute bottom-0 right-0 w-7 h-7 border-b-4 border-r-4 border-teal-300 rounded-br-xl">
                            </div>
                            <div
                                class="absolute bottom-0 left-0 w-7 h-7 border-b-4 border-l-4 border-teal-300 rounded-bl-xl">
                            </div>
                            <div class="scan-line absolute right-0 left-0 h-0.5 bg-teal-400 opacity-80"
                                style="animation: scan 2s linear infinite;"></div>
                        </div>
                    </div>
                    <!-- Camera init -->
                    <div id="cameraInitOverlay"
                        class="absolute inset-0 bg-gray-900 flex flex-col items-center justify-center gap-3">
                        <i class="fas fa-camera text-gray-500 text-4xl"></i>
                        <select id="cameraSelect"
                            class="bg-gray-800 text-gray-300 border border-gray-600 rounded-xl px-4 py-2 text-sm font-cairo w-56 text-center hidden">
                        </select>
                        <button onclick="startCamera()"
                            class="bg-teal-600 hover:bg-teal-700 text-white px-6 py-3 rounded-xl font-bold font-cairo text-sm transition-all">
                            <i class="fas fa-play ml-2"></i> تشغيل الكاميرا
                        </button>
                    </div>
                    <!-- Switch camera button (shown when running) -->
                    <button id="switchCamBtn" onclick="switchCamera()"
                        class="hidden absolute top-2 left-2 bg-black/50 text-white px-3 py-1.5 rounded-lg text-xs font-cairo">
                        <i class="fas fa-sync ml-1"></i> تبديل الكاميرا
                    </button>
                </div>
            </div>

            <!-- Photo Capture Panel -->
            <div id="photoPanel" class="hidden">
                <div class="text-center py-4">
                    <input type="file" id="qrPhotoInput" accept="image/*" capture="environment" class="hidden"
                        onchange="handlePhotoCapture(this)">
                    <canvas id="photoCanvas" class="hidden w-full max-h-40 object-contain rounded-xl border border-gray-200 mb-3"></canvas>
                    <div id="photoScanStatus" class="hidden mb-3">
                        <div class="flex items-center justify-center gap-2 text-blue-600">
                            <i class="fas fa-spinner fa-spin"></i>
                            <span class="text-sm font-cairo">جارٍ قراءة الباركود...</span>
                        </div>
                    </div>
                    <div id="photoScanError" class="hidden mb-3 bg-red-50 border border-red-200 rounded-xl p-3 text-red-600 text-xs font-cairo"></div>

                    <!-- Editable result field (shown after read) -->
                    <div id="photoResultArea" class="hidden mb-3">
                        <p class="text-xs text-gray-500 font-cairo mb-1 text-right">✅ تم قراءة الباركود — يمكنك التعديل إذا لزم:</p>
                        <div class="flex gap-2">
                            <input type="text" id="photoQrValue" dir="ltr"
                                class="flex-1 border border-green-300 bg-green-50 rounded-xl px-3 py-2   text-xs focus:ring-2 focus:ring-green-400 text-left"
                                placeholder="القيمة المقروءة...">
                            <button onclick="submitPhotoQr()"
                                class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-xl font-bold font-cairo text-sm transition-all">
                                إرسال
                            </button>
                        </div>
                    </div>

                    <button onclick="document.getElementById('qrPhotoInput').click()"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-2xl font-bold font-cairo text-base transition-all shadow-lg shadow-blue-200 w-full active:scale-95">
                        <i class="fas fa-camera text-xl ml-2"></i>
                        افتح كاميرا الجوال وصوّر الباركود
                    </button>
                    <p class="text-gray-400 text-xs font-almarai mt-3">
                        📌 يعمل على جميع الأجهزة
                    </p>
                </div>
            </div>

            <!-- Manual Input -->
            <div id="manualPanel" class="hidden">
                <div class="flex gap-2">
                    <input type="text" id="manualQr" placeholder="أدخل رمز QR يدوياً..."
                        class="flex-1 border border-gray-200 rounded-xl px-3 py-3   text-sm focus:ring-2 focus:ring-teal-400">
                    <button onclick="processManualScan()"
                        class="bg-teal-600 hover:bg-teal-700 text-white px-5 py-3 rounded-xl font-bold font-cairo text-sm transition-all">
                        مسح
                    </button>
                </div>
            </div>
        </div>

        <!-- Result Area -->
        <div id="resultArea" class="hidden">
            <!-- Loading -->
            <div id="resultLoading" class="hidden bg-white rounded-3xl shadow-sm border border-gray-100 p-8 text-center">
                <i class="fas fa-spinner fa-spin text-teal-500 text-3xl mb-3"></i>
                <p class="font-cairo text-gray-500">جارٍ التحقق...</p>
            </div>

            <!-- Error -->
            <div id="resultError" class="hidden bg-red-50 border border-red-200 rounded-3xl p-6 text-center">
                <i class="fas fa-times-circle text-red-500 text-4xl mb-3"></i>
                <p id="errorMsg" class="font-bold text-red-700 font-cairo text-lg"></p>
                <button onclick="resetScanner()"
                    class="mt-4 bg-gray-800 text-white px-6 py-2.5 rounded-xl font-cairo font-bold text-sm">
                    <i class="fas fa-redo ml-2"></i> مسح آخر
                </button>
            </div>

            <!-- Success - Distribution Form -->
            <div id="resultSuccess" class="hidden bg-white rounded-2xl sm:rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <!-- Meal type badge -->
                <div class="bg-gradient-to-l from-teal-500 to-emerald-600 px-4 sm:px-6 py-3 sm:py-4 flex items-center justify-between">
                    <div>
                        <p class="text-teal-100 text-xs sm:text-sm font-cairo">وجبة اليوم</p>
                        <p id="resultMealType" class="text-white text-xl sm:text-2xl font-bold font-cairo"></p>
                    </div>
                    <i class="fas fa-utensils text-white/50 text-3xl sm:text-4xl"></i>
                </div>

                <div class="p-4 sm:p-6">
                    <!-- Students list -->
                    <div id="studentsList" class="space-y-2 sm:space-y-3 mb-4"></div>

                    <!-- Dish number (REQUIRED) -->
                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-700 font-cairo mb-1.5">
                            رقم الصحن <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="dishNumber" placeholder="أدخل رقم الصحن..."
                            min="1" inputmode="numeric"
                            class="w-full border-2 border-gray-200 rounded-xl px-4 py-3   text-xl text-center focus:ring-2 focus:ring-teal-400 focus:border-teal-400 transition-all">
                        <p id="dishNumberError" class="hidden text-red-500 text-xs font-cairo mt-1">⚠️ رقم الصحن مطلوب قبل التوزيع</p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="grid grid-cols-2 gap-2 sm:gap-3">
                        <button id="distributeBtn" onclick="distribute('normal')"
                            class="py-4 sm:py-4 bg-teal-500 hover:bg-teal-600 active:scale-95 disabled:bg-gray-200 disabled:opacity-50 disabled:cursor-not-allowed text-white rounded-2xl font-black font-cairo text-base sm:text-lg shadow-lg shadow-teal-200 transition-all">
                            <i class="fas fa-check-circle ml-1 sm:ml-2 text-lg sm:text-xl"></i> توزيع
                        </button>
                        <button id="extraBtn" onclick="distribute('extra')"
                            class="py-4 sm:py-4 bg-orange-400 hover:bg-orange-500 active:scale-95 disabled:bg-gray-200 disabled:opacity-50 disabled:cursor-not-allowed text-white rounded-2xl font-black font-cairo text-base sm:text-lg shadow-lg shadow-orange-200 transition-all">
                            <i class="fas fa-plus-circle ml-1 sm:ml-2 text-lg sm:text-xl"></i> لاحقة
                        </button>
                    </div>

                    <button onclick="resetScanner()"
                        class="w-full mt-3 py-3 border border-gray-200 rounded-xl text-gray-500 font-cairo text-sm hover:bg-gray-50 active:bg-gray-100 transition-all">
                        <i class="fas fa-redo ml-1"></i> مسح آخر
                    </button>
                </div>
            </div>
        </div>

        <!-- Today's Stats -->
        <div id="todayStats" class="mt-5 grid grid-cols-3 gap-3">
            <div class="bg-white rounded-2xl border border-gray-100 p-4 text-center shadow-sm">
                <p class="text-2xl font-black text-teal-600 font-cairo" id="statsTotal">—</p>
                <p class="text-xs text-gray-400 font-cairo mt-0.5">إجمالي الوجبات</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 p-4 text-center shadow-sm">
                <p class="text-2xl font-black text-orange-500 font-cairo" id="statsExtra">—</p>
                <p class="text-xs text-gray-400 font-cairo mt-0.5">وجبات لاحقة</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 p-4 text-center shadow-sm">
                <a href="{{ route('nutrition.distributions.index') }}" class="text-teal-600 hover:underline">
                    <p class="text-2xl font-black text-gray-400 font-cairo"><i class="fas fa-list-check"></i></p>
                    <p class="text-xs text-gray-400 font-cairo mt-0.5">عرض السجل</p>
                </a>
            </div>
        </div>
    </div>

    <style>
        @keyframes scan {
            0% {
                top: 10%;
            }

            50% {
                top: 85%;
            }

            100% {
                top: 10%;
            }
        }

        .scan-line {
            animation: scan 2s linear infinite;
        }
    </style>
@endsection

@push('scripts')
    <script src="https://unpkg.com/@zxing/library@latest/umd/index.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        let scanData = null;
        let cameraRunning = false;
        let codeReader = null;
        let availableCameras = [];
        let currentCameraIndex = 0;

        // Detect meal type
        const hour = new Date().getHours();
        const mealType = hour < 11 ? 'breakfast' : (hour < 17 ? 'lunch' : 'dinner');
        const mealLabels = { breakfast: 'الفطور', lunch: 'الغداء', dinner: 'العشاء' };
        document.getElementById('mealTypeLabel').textContent = 'وجبة: ' + (mealLabels[mealType] || '');

        function showTab(tab) {
            document.getElementById('cameraPanel').className = tab === 'camera' ? '' : 'hidden';
            document.getElementById('photoPanel').className = tab === 'photo' ? '' : 'hidden';
            document.getElementById('manualPanel').className = tab === 'manual' ? '' : 'hidden';
            document.getElementById('cameraTab').className = `flex-1 py-2.5 rounded-xl font-bold font-cairo text-sm transition-all ${tab === 'camera' ? 'bg-teal-600 text-white shadow-lg shadow-teal-200' : 'bg-gray-100 text-gray-600'}`;
            document.getElementById('photoTab').className = `flex-1 py-2.5 rounded-xl font-bold font-cairo text-sm transition-all ${tab === 'photo' ? 'bg-blue-600 text-white shadow-lg shadow-blue-200' : 'bg-gray-100 text-gray-600'}`;
            document.getElementById('manualTab').className = `flex-1 py-2.5 rounded-xl font-bold font-cairo text-sm transition-all ${tab === 'manual' ? 'bg-teal-600 text-white shadow-lg shadow-teal-200' : 'bg-gray-100 text-gray-600'}`;
            if (tab !== 'camera' && cameraRunning) stopCamera();
        }

        function handlePhotoCapture(input) {
            if (!input.files || !input.files[0]) return;
            const file = input.files[0];

            const statusEl = document.getElementById('photoScanStatus');
            const errorEl = document.getElementById('photoScanError');
            const canvas = document.getElementById('photoCanvas');
            const resultArea = document.getElementById('photoResultArea');
            const qrValueInput = document.getElementById('photoQrValue');

            statusEl.classList.remove('hidden');
            errorEl.classList.add('hidden');
            resultArea.classList.add('hidden');

            const reader = new FileReader();
            reader.onload = function(e) {
                const img = new Image();
                img.onload = function() {
                    canvas.width = img.width;
                    canvas.height = img.height;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0);
                    canvas.classList.remove('hidden');

                    const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);

                    // Try with both normal and inverted
                    let code = jsQR(imageData.data, imageData.width, imageData.height, {
                        inversionAttempts: "attemptBoth"
                    });

                    statusEl.classList.add('hidden');

                    if (code) {
                        // Clean the QR data
                        let qrData = code.data
                            .replace(/^\uFEFF/, '')
                            .replace(/[\u200B-\u200D\uFEFF]/g, '')
                            .replace(/\r\n|\r|\n/g, '')
                            .trim();

                        // Show in editable field — user can verify before submitting
                        qrValueInput.value = qrData;
                        resultArea.classList.remove('hidden');
                        qrValueInput.focus();
                        input.value = '';
                    } else {
                        errorEl.textContent = '❌ لم يتم التعرف على الباركود. تأكد من وضوح الصورة وإضاءتها جيداً وأن الباركود ظاهر بشكل كامل، ثم حاول مرة أخرى.';
                        errorEl.className = 'mb-3 bg-red-50 border border-red-200 rounded-xl p-3 text-red-600 text-xs font-cairo';
                        errorEl.classList.remove('hidden');
                        input.value = '';
                    }
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }

        function submitPhotoQr() {
            const val = document.getElementById('photoQrValue').value.trim();
            if (!val) return;
            document.getElementById('photoResultArea').classList.add('hidden');
            processQr(val);
        }


        async function enumerateCameras() {
            try {
                const devices = await ZXing.BrowserCodeReader.listVideoInputDevices();
                availableCameras = devices;
                const sel = document.getElementById('cameraSelect');
                sel.innerHTML = '';
                devices.forEach((cam, i) => {
                    const opt = document.createElement('option');
                    opt.value = cam.deviceId;
                    opt.textContent = cam.label || `كاميرا ${i + 1}`;
                    const lbl = (cam.label || '').toLowerCase();
                    if (lbl.includes('back') || lbl.includes('environment') || lbl.includes('rear')) {
                        opt.selected = true;
                        currentCameraIndex = i;
                    }
                    sel.appendChild(opt);
                });
                if (devices.length > 1) sel.classList.remove('hidden');
            } catch (e) { console.error('enumerate cameras error:', e); }
        }

        async function startCamera(deviceId) {
            const overlay = document.getElementById('cameraInitOverlay');

            // ── Step 1: Check permission status via Permissions API ──
            if (navigator.permissions) {
                try {
                    const perm = await navigator.permissions.query({ name: 'camera' });
                    if (perm.state === 'denied') {
                        showCameraBlockedUI(overlay, 'denied');
                        return;
                    }
                } catch(e) { /* Permissions API may not support 'camera' query - continue */ }
            }

            // ── Step 2: Check if mediaDevices API exists ──
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                showCameraBlockedUI(overlay, 'unsupported');
                return;
            }

            // ── Step 3: Request permission explicitly (shows the browser dialog) ──
            let testStream = null;
            try {
                testStream = await navigator.mediaDevices.getUserMedia({ video: true });
                testStream.getTracks().forEach(t => t.stop());
            } catch (permErr) {
                showCameraBlockedUI(overlay, permErr.name === 'NotFoundError' ? 'notfound' : 'denied');
                return;
            }

            // ── Step 4: Permission granted - start ZXing scanner ──
            // Try 3 different constraint levels (most specific → simplest)
            const constraintsList = [
                deviceId
                    ? { video: { deviceId: { exact: deviceId } } }
                    : { video: { facingMode: { ideal: 'environment' } } },
                { video: { facingMode: 'environment' } },
                { video: true }
            ];

            let started = false;
            for (const constraints of constraintsList) {
                try {
                    if (codeReader) codeReader.reset();
                    codeReader = new ZXing.BrowserMultiFormatReader();
                    if (availableCameras.length === 0) await enumerateCameras();
                    overlay.style.display = 'none';
                    cameraRunning = true;
                    if (availableCameras.length > 1) document.getElementById('switchCamBtn').classList.remove('hidden');

                    await codeReader.decodeFromConstraints(
                        constraints,
                        'video',
                        (result, err) => {
                            if (result) {
                                stopCamera();
                                processQr(result.getText());
                            }
                        }
                    );
                    started = true;
                    break;
                } catch (e) {
                    console.warn('Camera constraint failed, trying next...', constraints, e.message);
                    if (codeReader) codeReader.reset();
                }
            }

            if (!started) {
                overlay.style.display = 'flex';
                showCameraBlockedUI(overlay, 'error');
            }
        }

        function showCameraBlockedUI(overlay, type) {
            const msgs = {
                denied: {
                    title: '🚫 تم حجب الكاميرا',
                    body: 'رُفض الإذن من قبل. لإعادة تفعيله:',
                    steps: `
                        <div class="bg-gray-800 rounded-xl p-3 text-right mb-3 border border-yellow-600/40 text-xs">
                            <p class="text-yellow-300 font-bold font-cairo mb-1.5">📱 من إعدادات الجوال:</p>
                            <ol class="text-gray-300 font-almarai space-y-1 list-decimal list-inside leading-relaxed">
                                <li>افتح <strong class="text-white">إعدادات الجوال</strong></li>
                                <li>اختر <strong class="text-white">التطبيقات</strong> ← <strong class="text-white">Chrome</strong></li>
                                <li>اضغط <strong class="text-white">الأذونات</strong> ← <strong class="text-white">الكاميرا</strong></li>
                                <li>اختر <strong class="text-green-400">السماح دائماً</strong></li>
                                <li>ارجع وأعد تحميل الصفحة</li>
                            </ol>
                        </div>`
                },
                notfound: {
                    title: '📷 لا توجد كاميرا',
                    body: 'لم يتم العثور على كاميرا في هذا الجهاز.',
                    steps: ''
                },
                unsupported: {
                    title: '⚠️ المتصفح لا يدعم الكاميرا',
                    body: 'جرب استخدام Chrome أو Firefox أحدث إصدار.',
                    steps: ''
                },
                error: {
                    title: '❌ خطأ في تشغيل الكاميرا',
                    body: 'تعذّر تشغيل الكاميرا. جرب "تصوير QR" أو الإدخال اليدوي.',
                    steps: ''
                }
            };
            const m = msgs[type] || msgs.error;
            overlay.style.display = 'flex';
            overlay.innerHTML = `
                <div class="text-center px-4 py-4 max-w-xs mx-auto overflow-y-auto max-h-full">
                    <div class="w-14 h-14 bg-red-900/40 rounded-full flex items-center justify-center mx-auto mb-3 border-2 border-red-500">
                        <i class="fas fa-camera-slash text-red-400 text-xl"></i>
                    </div>
                    <h3 class="text-white font-bold font-cairo text-sm mb-2">${m.title}</h3>
                    <p class="text-gray-400 text-xs font-almarai leading-relaxed mb-3">${m.body}</p>
                    ${m.steps}
                    <div class="flex flex-col gap-2">
                        <button onclick="showTab('photo')"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-xl font-bold font-cairo text-xs transition-all">
                            <i class="fas fa-image ml-1"></i> استخدم "تصوير QR" بدلاً من ذلك
                        </button>
                        <button onclick="location.reload()"
                            class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2.5 rounded-xl font-bold font-cairo text-xs transition-all">
                            <i class="fas fa-redo ml-1"></i> إعادة المحاولة بعد منح الإذن
                        </button>
                        <button onclick="showTab('manual')"
                            class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2.5 rounded-xl font-bold font-cairo text-xs transition-all">
                            <i class="fas fa-keyboard ml-1"></i> إدخال يدوي
                        </button>
                    </div>
                </div>
            `;
        }

        async function switchCamera() {
            if (availableCameras.length < 2) return;
            currentCameraIndex = (currentCameraIndex + 1) % availableCameras.length;
            const nextId = availableCameras[currentCameraIndex].deviceId;
            document.getElementById('cameraSelect').value = nextId;
            await startCamera(nextId);
        }

        function stopCamera() {
            if (codeReader) { codeReader.reset(); codeReader = null; }
            cameraRunning = false;
            document.getElementById('switchCamBtn').classList.add('hidden');
        }

        function processManualScan() {
            const val = document.getElementById('manualQr').value.trim();
            if (!val) { alert('أدخل رمز QR'); return; }
            processQr(val);
        }

        async function processQr(qrCode) {
            document.getElementById('resultArea').classList.remove('hidden');
            document.getElementById('resultLoading').classList.remove('hidden');
            document.getElementById('resultError').classList.add('hidden');
            document.getElementById('resultSuccess').classList.add('hidden');

            try {
                const res = await fetch('{{ route("nutrition.distributions.process-scan") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ qr_code: qrCode })
                });
                const data = await res.json();
                document.getElementById('resultLoading').classList.add('hidden');

                if (!data.success) {
                    document.getElementById('resultError').classList.remove('hidden');
                    document.getElementById('errorMsg').innerHTML =
                        (data.message || 'خطأ غير معروف') +
                        `<br><span class="text-xs text-gray-400   mt-2 block break-all">القيمة: ${qrCode.substring(0, 80)}</span>`;
                    return;
                }

                if (data.type === 'individual' && data.students[0].status === 'suspended') {
                    document.getElementById('resultError').classList.remove('hidden');
                    document.getElementById('errorMsg').innerHTML = `<div class="p-2 border-2 border-red-500 bg-red-50 rounded-xl animate-pulse">
                            <i class="fas fa-exclamation-triangle text-red-600 text-3xl mb-2 block"></i>
                            <span class="text-red-700 font-black text-lg">تحذير: الطالب موقوف!</span><br>
                            الطالب <b>${data.students[0].name}</b> موقوف من خدمات التغذية حالياً.
                        </div>`;
                    document.getElementById('distributeBtn').disabled = true;
                    document.getElementById('extraBtn').disabled = true;
                    return;
                }

                scanData = data;

                // If individual and already received, show error instead of success
                if (data.type === 'individual' && data.students[0].status === 'already_received') {
                    document.getElementById('resultError').classList.remove('hidden');
                    document.getElementById('errorMsg').innerHTML = `الطالب <b>${data.students[0].name}</b> استلم وجبة <b>${data.meal_type}</b> مسبقاً اليوم. <br><span class="text-xs text-red-400 mt-2 block">يرجى استخدام خيار "اللاحقة" إذا كان مسموحاً.</span>`;
                    return;
                }

                document.getElementById('resultSuccess').classList.remove('hidden');
                document.getElementById('resultMealType').textContent = data.meal_type;

                // Build students list
                const list = document.getElementById('studentsList');
                list.innerHTML = '';

                let anyReady = false;
                let anySuspended = false;

                data.students.forEach(s => {
                    const statusMap = {
                        'ready': ['bg-green-100 text-green-700', 'جاهز'],
                        'already_received': ['bg-gray-100 text-gray-500', 'استلم مسبقاً'],
                        'suspended': ['bg-red-100 text-red-600', 'موقوف'],
                        'expired': ['bg-orange-100 text-orange-600', 'منتهي الاشتراك'],
                        'absent': ['bg-red-200 text-red-800', 'غائب (بإشعار)'],
                        'no_subscription': ['bg-gray-200 text-gray-400', 'بدون اشتراك']
                    };
                    const [cls, lbl] = statusMap[s.status] || statusMap['already_received'];
                    if (s.status === 'ready') anyReady = true;
                    if (s.status === 'suspended') anySuspended = true;

                    // Attendance sub-badge
                    let attendanceBadge = '';
                    if (s.attendance_status === 'late') {
                        attendanceBadge = '<span class="mr-2 px-1.5 py-0.5 rounded bg-yellow-100 text-yellow-800 text-[10px] font-bold border border-yellow-200 animate-pulse">متأخر</span>';
                    }

                    list.innerHTML += `
                            <div class="flex items-center justify-between p-4 rounded-2xl border ${s.status === 'ready' ? 'border-green-100 bg-green-50' : (s.status === 'suspended' ? 'border-red-200 bg-red-50' : 'border-gray-100 bg-gray-50')}">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full ${s.status === 'ready' ? 'bg-green-600' : (s.status === 'suspended' ? 'bg-red-600' : 'bg-gray-300')} flex items-center justify-center">
                                        <i class="fas fa-user text-white text-sm"></i>
                                    </div>
                                    <div>
                                        <span class="font-bold text-gray-800 font-cairo block">${s.name} ${attendanceBadge}</span>
                                        ${s.attendance_status === 'late' ? '<span class="text-[10px] text-yellow-600 font-bold">⚠️ تنبيه: الطالب سيصل متأخراً</span>' : ''}
                                    </div>
                                </div>
                                <span class="px-3 py-1 rounded-lg text-xs font-bold ${cls}">${lbl}</span>
                            </div>`;
                });

                // Show alert if anyone in group is suspended
                if (anySuspended && data.type === 'group') {
                    const alertDiv = document.createElement('div');
                    alertDiv.className = "mt-4 p-4 bg-red-600 text-white rounded-2xl font-bold font-cairo flex items-center gap-3 animate-bounce";
                    alertDiv.innerHTML = '<i class="fas fa-hand-paper text-2xl"></i> <span>تنبيه: يوجد طلاب موقوفين في هذه المجموعة!</span>';
                    list.prepend(alertDiv);
                }

                // Disable regular distribution if no one is ready
                document.getElementById('distributeBtn').disabled = !anyReady;
            } catch (e) {
                document.getElementById('resultLoading').classList.add('hidden');
                document.getElementById('resultError').classList.remove('hidden');
                document.getElementById('errorMsg').textContent = 'حدث خطأ في الاتصال. حاول مجدداً.';
            }
        }

        async function distribute(mode) {
            if (!scanData) return;
            const btn = document.getElementById(mode === 'extra' ? 'extraBtn' : 'distributeBtn');
            const otherBtn = document.getElementById(mode === 'extra' ? 'distributeBtn' : 'extraBtn');

            // ✅ Validate dish number is required
            const dishInput = document.getElementById('dishNumber');
            const dishError = document.getElementById('dishNumberError');
            if (!dishInput.value.trim()) {
                dishInput.classList.add('border-red-400', 'bg-red-50');
                dishError.classList.remove('hidden');
                dishInput.focus();
                return;
            }
            dishInput.classList.remove('border-red-400', 'bg-red-50');
            dishError.classList.add('hidden');

            btn.disabled = true;
            otherBtn.disabled = true;

            const students = scanData.students.filter(s => mode === 'extra' || s.status === 'ready');
            if (!students.length) {
                alert('لا يوجد طلاب جاهزين للتوزيع بالوضع الحالي.');
                btn.disabled = false;
                otherBtn.disabled = false;
                return;
            }

            const payload = {
                students: students.map(s => ({ student_id: s.id, subscription_id: s.subscription_id })),
                meal_type: scanData.meal_key,
                dish_number: dishInput.value.trim(),
                type: mode === 'extra' ? 'extra' : (scanData.type === 'group' || scanData.type === 'student_group' ? 'group' : 'individual'),
                group_id: scanData.type === 'group' ? scanData.group_id : null,
                student_qr_group_id: scanData.type === 'student_group' ? scanData.group_id : null,
            };


            let res;
            try {
                res = await fetch('{{ route("nutrition.distributions.distribute") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(payload),
                });

                if (res.status === 419) {
                    alert('انتهت صلاحية الصفحة (419). يرجى تحديث الصفحة وحاول مجدداً.');
                    btn.disabled = false;
                    otherBtn.disabled = false;
                    return;
                }

                const data = await res.json();

                if (data.success) {
                    if (mode === 'extra') {
                        showSuccess(`✓ تم تسجيل وتوزيع الوجبة اللاحقة لـ ${data.distributed} طالب`);
                    } else {
                        showSuccess(`✓ تم التوزيع بنجاح لـ ${data.distributed} طالب`);
                    }
                } else {
                    alert(data.message || 'حدث خطأ أثناء التوزيع');
                    btn.disabled = false;
                    otherBtn.disabled = false;
                }
            } catch (e) {
                console.error('Distribute error:', e);
                const statusInfo = res ? ` (Status: ${res.status})` : '';
                alert('حدث خطأ في الاتصال بالسيرفر' + statusInfo + '. يرجى مراجعة سجلات النظام.');
                btn.disabled = false;
                otherBtn.disabled = false;
            }
        }

        function showSuccess(msg) {
            document.getElementById('resultSuccess').innerHTML = `
                                        <div class="p-10 text-center">
                                            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                                <i class="fas fa-check-circle text-green-500 text-4xl"></i>
                                            </div>
                                            <p class="text-2xl font-black text-green-700 font-cairo mb-6">${msg}</p>
                                            <button onclick="resetScanner()" class="bg-gray-800 text-white px-8 py-3 rounded-2xl font-bold font-cairo text-lg">الطالب التالي</button>
                                        </div>`;
        }

        function resetScanner() {
            scanData = null;
            document.getElementById('resultArea').classList.add('hidden');
            document.getElementById('manualQr') && (document.getElementById('manualQr').value = '');
            if (document.getElementById('cameraPanel').getBoundingClientRect().height > 0) startCamera();
        }
    </script>
@endpush
