@extends('layouts.app')

@section('title', 'الأنشطة والفعاليات الاجتماعية')

@section('content')
    <div class="container mx-auto px-6 py-8">
        <!-- Header Section -->
        <div class="mb-8 flex justify-between items-center bg-white p-8 rounded-3xl border-l-8 border-gold shadow-sm">
            <div>
                <h1 class="text-3xl font-black text-navy font-cairo">سجل الأنشطة والفعاليات</h1>
                <p class="text-gray-400 font-almarai text-sm mt-2">إدارة الفعاليات الاجتماعية والرياضية والثقافية بالأندية
                </p>
            </div>
            <div class="flex gap-4">
                <a href="{{ route('activities.export-list', request()->all()) }}" target="_blank"
                    class="px-6 py-4 bg-white text-navy border-2 border-navy/10 rounded-2xl hover:bg-navy/5 font-cairo font-bold transition-all flex items-center gap-2 shadow-sm">
                    <i class="fas fa-print"></i>
                    <span>طباعة</span>
                </a>
                @if(!auth()->user()->hasRole('activity-assistant'))
                <a href="{{ route('clubs.index') }}"
                    class="px-6 py-4 bg-gray-50 text-navy rounded-2xl hover:bg-gray-100 font-cairo font-bold transition-all flex items-center gap-2 border border-gray-100">
                    <i class="fas fa-users-rectangle text-gold"></i>
                    <span>إدارة الأندية</span>
                </a>
                @endif
                @if(!auth()->user()->hasRole('super-admin') && !auth()->user()->hasRole('activity-assistant'))
                <button onclick="openPlanModal()"
                    class="px-8 py-4 bg-navy text-white rounded-2xl hover:bg-navy/90 shadow-xl font-cairo font-bold transition-all transform hover:-translate-y-1 flex items-center gap-3 group">
                    <i class="fas fa-calendar-plus text-gold group-hover:scale-110 transition-transform"></i>
                    <span>جدولة فعالية جديدة</span>
                </button>
                @endif
            </div>
        </div>

        <!-- Filters Section -->
        <div class="bg-white p-6 rounded-3xl shadow-sm mb-8 border border-gray-100">
            <form action="{{ route('activities.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-6 items-end">
                @if (isset($centers) && count($centers) > 0)
                <div>
                    <label class="block text-xs font-bold text-gray-400 mb-2 uppercase font-cairo">المركز / السكن</label>
                    <select name="center_id" onchange="this.form.submit()" 
                        class="w-full bg-gray-50 border-0 rounded-xl p-3 text-sm focus:ring-2 focus:ring-gold/20 font-almarai">
                        <option value="">جميع المراكز</option>
                        @foreach($centers as $center)
                            <option value="{{ $center->id }}" {{ request('center_id') == $center->id ? 'selected' : '' }}>
                                {{ $center->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif
                
                <div>
                    <label class="block text-xs font-bold text-gray-400 mb-2 uppercase font-cairo">الشهر والسنة</label>
                    <input type="month" name="month" value="{{ request('month') }}" onchange="this.form.submit()"
                        class="w-full bg-gray-50 border-0 rounded-xl p-3 text-sm focus:ring-2 focus:ring-gold/20 font-almarai">
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit" class="flex-1 bg-navy text-white p-3 rounded-xl font-bold font-cairo hover:bg-navy/90 transition-all shadow-md">
                        <i class="fas fa-filter text-gold ml-1"></i> عرض
                    </button>
                    @if(request()->anyFilled(['center_id', 'month']))
                        <a href="{{ route('activities.index') }}" class="w-12 h-11 flex items-center justify-center bg-red-50 text-red-500 rounded-xl hover:bg-red-100 transition-all border border-red-100" title="إعادة تعيين">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Activities Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @if(is_countable($activities) ? count($activities) > 0 : (method_exists($activities, 'count') ? $activities->count() > 0 : !empty($activities)))
                @foreach($activities as $activity)
                <div
                    class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden card-premium group h-full flex flex-col">
                    <div class="relative h-48 bg-navy/5 overflow-hidden flex items-center justify-center p-8">
                        @if($activity->club?->logo)
                            <img src="{{ asset('storage/' . $activity->club->logo) }}" alt="Club Logo"
                                class="w-24 h-24 object-contain opacity-80 group-hover:scale-110 transition-transform duration-500">
                        @else
                            <i
                                class="fas fa-users-viewfinder text-6xl text-navy/10 group-hover:scale-110 transition-transform duration-500"></i>
                        @endif

                        <div class="absolute top-6 left-6">
                            <span class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest
                                        @if($activity->status == 'planned') bg-blue-100 text-blue-700 
                                        @elseif($activity->status == 'published') bg-green-100 text-green-700
                                        @elseif($activity->status == 'completed') bg-purple-100 text-purple-700
                                        @else bg-red-100 text-red-500 @endif">
                                @if($activity->status == 'planned') مجدولة
                                @elseif($activity->status == 'published') منشورة
                                @elseif($activity->status == 'completed') مكتملة
                                @else ملغاة @endif
                            </span>
                        </div>
                    </div>

                    <div class="p-8 flex-grow">
                        <div class="mb-4">
                            <span
                                class="text-gold font-bold text-xs uppercase font-cairo block mb-1 tracking-wider">{{ $activity->club->name ?? 'نادي عام' }}</span>
                            <h2
                                class="text-2xl font-black text-navy font-cairo leading-tight group-hover:text-gold transition-colors">
                                {{ $activity->name }}</h2>
                        </div>

                        <div class="space-y-3 mb-8">
                            <div class="flex items-start gap-3 text-gray-500 font-almarai text-sm">
                                <i class="fas fa-calendar-alt text-navy w-4 mt-0.5"></i>
                                <div>
                                    <span class="block">{{ $activity->start_date->format('Y/m/d') }}</span>
                                    @if($activity->end_date && $activity->end_date != $activity->start_date)
                                        <span class="text-xs text-gray-400">إلى {{ $activity->end_date->format('Y/m/d') }}</span>
                                    @endif
                                </div>
                            </div>
                            @if($activity->start_time)
                            <div class="flex items-center gap-3 text-gray-500 font-almarai text-sm">
                                <i class="fas fa-clock text-navy w-4"></i>
                                <span>{{ $activity->start_time }} @if($activity->end_time) - {{ $activity->end_time }} @endif</span>
                            </div>
                            @endif
                            <div class="flex items-center gap-3 text-gray-500 font-almarai text-sm">
                                <i class="fas fa-location-dot text-navy w-4"></i>
                                <span>{{ $activity->location }}</span>
                            </div>
                            <div class="flex items-center gap-3 text-gray-500 font-almarai text-sm">
                                <i class="fas fa-users text-navy w-4"></i>
                                <span>{{ $activity->participants->count() }} / {{ $activity->targetedStudents->count() > 0 ? $activity->targetedStudents->count() : ($activity->max_participants ?? '∞') }}
                                    مشارك</span>
                            </div>
                        </div>

                        <div class="mt-auto border-t border-gray-50 pt-6 flex gap-3">
                            @if(auth()->user()->can('register-activities'))
                                <button onclick="openRegisterModal({{ $activity->id }}, '{{ $activity->name }}')"
                                    class="flex-1 bg-gray-50 text-navy py-3.5 rounded-xl font-bold font-cairo hover:bg-navy hover:text-white transition-all text-sm flex items-center justify-center gap-2">
                                    <i class="fas fa-user-plus text-xs"></i>
                                    <span>تسجيل حضور</span>
                                </button>
                            @endif
                            
                            @if(!auth()->user()->hasRole('super-admin') && !auth()->user()->hasRole('activity-assistant'))
                                <a href="{{ route('activities.edit', $activity->id) }}"
                                    class="w-10 h-10 bg-gold/10 text-gold rounded-xl flex items-center justify-center hover:bg-gold hover:text-white transition-all"
                                    title="تعديل الفعالية">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                            @endif
                                <a href="{{ route('activities.show', $activity->id) }}"
                                    class="w-10 h-10 bg-navy/5 text-navy rounded-xl flex items-center justify-center hover:bg-navy hover:text-white transition-all"
                                    title="عرض التفاصيل">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                        </div>
                    </div>
                </div>
                @endforeach
            @else
                <div
                    class="col-span-full bg-white rounded-[3rem] p-20 text-center border-2 border-dashed border-gray-100 shadow-sm">
                    <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-icons text-4xl text-gray-200"></i>
                    </div>
                    <h3 class="text-2xl font-black text-navy font-cairo">لا توجد فعاليات مجدولة</h3>
                    <p class="text-gray-400 font-almarai mt-2">ابدأ بجدولة أولى فعالياتك الاجتماعية للأندية والمراكز</p>
                </div>
            @endif
        </div>

        <div class="mt-10">
            {{ $activities->links() }}
        </div>
    </div>

@endsection

@push('modals')
    <!-- Plan Activity Modal -->
    <div id="planModal" class="fixed inset-0 bg-navy/60 backdrop-blur-sm hidden z-[1000] overflow-y-auto">
        <div class="min-h-full flex items-start justify-center p-6 py-8">
        <div
            class="bg-white rounded-[2.5rem] p-8 md:p-10 max-w-5xl w-full shadow-2xl transform transition-all border-t-8 border-navy relative">
            <div class="flex items-center justify-between mb-8 border-b border-gray-50 pb-6">
                <div class="flex items-center gap-5">
                    <div class="w-14 h-14 bg-navy/5 rounded-2xl flex items-center justify-center text-navy">
                        <i class="fas fa-calendar-plus text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-black font-cairo text-navy">جدولة فعالية جديدة</h2>
                        <p class="text-gray-400 font-almarai text-sm italic">يرجى تعبئة بيانات الفعالية والطلاب المستهدفين</p>
                    </div>
                </div>
                <button onclick="closePlanModal()" class="text-gray-400 hover:text-red-500 transition-colors">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>

            <form action="{{ route('activities.store') }}" method="POST" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Basic Info -->
                    <div class="lg:col-span-2">
                        <label class="block text-sm font-black text-navy mb-3 font-cairo text-right">عنوان الفعالية</label>
                        <input type="text" name="name" required placeholder="مثال: دوري كرة القدم الشامل"
                            class="w-full px-5 py-4 rounded-xl border border-gray-100 bg-gray-50/50 focus:bg-white focus:ring-4 focus:ring-navy/5 outline-none text-right transition-all font-almarai">
                    </div>

                    <div>
                        <label class="block text-sm font-black text-navy mb-3 font-cairo text-right">النادي المنظم</label>
                        <select id="plan_club_id" name="club_id" required onchange="updateTargetOptions()"
                            class="w-full px-5 py-4 rounded-xl border border-gray-100 bg-gray-50/50 focus:bg-white focus:ring-4 focus:ring-navy/5 outline-none text-right transition-all font-almarai">
                            <option value="">اختر النادي...</option>
                            @foreach($clubs as $club)
                                <option value="{{ $club->id }}">{{ $club->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Date Range -->
                    <div>
                        <label class="block text-sm font-black text-navy mb-3 font-cairo text-right">من تاريخ</label>
                        <input type="date" name="start_date" required
                            class="w-full px-5 py-4 rounded-xl border border-gray-100 bg-gray-50/50 focus:bg-white focus:ring-4 focus:ring-navy/5 outline-none text-right font-mono transition-all">
                    </div>

                    <div>
                        <label class="block text-sm font-black text-navy mb-3 font-cairo text-right">إلى تاريخ (اختياري)</label>
                        <input type="date" name="end_date"
                            class="w-full px-5 py-4 rounded-xl border border-gray-100 bg-gray-50/50 focus:bg-white focus:ring-4 focus:ring-navy/5 outline-none text-right font-mono transition-all">
                    </div>

                    <div>
                        <label class="block text-sm font-black text-navy mb-3 font-cairo text-right">الموقع / القاعة</label>
                        <input type="text" name="location" required placeholder="مثال: الصالة الرياضية"
                            class="w-full px-5 py-4 rounded-xl border border-gray-100 bg-gray-50/50 focus:bg-white focus:ring-4 focus:ring-navy/5 outline-none text-right transition-all font-almarai">
                    </div>

                    <!-- Time Range -->
                    <div>
                        <label class="block text-sm font-black text-navy mb-3 font-cairo text-right">من الساعة</label>
                        <input type="time" name="start_time"
                            class="w-full px-5 py-4 rounded-xl border border-gray-100 bg-gray-50/50 focus:bg-white focus:ring-4 focus:ring-navy/5 outline-none text-center font-mono transition-all">
                    </div>

                    <div>
                        <label class="block text-sm font-black text-navy mb-3 font-cairo text-right">إلى الساعة</label>
                        <input type="time" name="end_time"
                            class="w-full px-5 py-4 rounded-xl border border-gray-100 bg-gray-50/50 focus:bg-white focus:ring-4 focus:ring-navy/5 outline-none text-center font-mono transition-all">
                    </div>



                </div>

                <!-- Target Selection - Moved out of grid for absolute full width -->
                <div class="w-full mt-6">
                    <div class="bg-gray-50 rounded-[2rem] p-6 md:p-8 border border-gray-100 shadow-inner">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="font-black text-navy font-cairo text-lg">تحديد الطلاب المستهدفين</h3>
                            <div class="flex gap-3">
                                <div id="club_member_option" class="hidden items-center gap-3 bg-white px-4 py-2 rounded-xl border border-gold/20 shadow-sm">
                                    <input type="checkbox" name="target_club_members" value="1" id="target_club" class="w-5 h-5 rounded border-gray-300 text-gold focus:ring-gold">
                                    <label for="target_club" class="text-xs font-bold text-navy font-cairo">أعضاء النادي</label>
                                </div>
                                <div class="flex items-center gap-3 bg-navy/5 px-4 py-2 rounded-xl border border-navy/10 shadow-sm">
                                    <input type="checkbox" name="target_all_students" value="1" id="target_all" class="w-5 h-5 rounded border-gray-300 text-navy focus:ring-navy">
                                    <label for="target_all" class="text-xs font-bold text-navy font-cairo">جميع طلاب المركز</label>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <div class="relative">
                                <input type="text" id="student_search" placeholder="ابحث عن الطالب بالاسم..." onkeyup="filterStudents()"
                                    class="w-full px-12 py-4 rounded-2xl border-2 border-transparent focus:border-gold/30 bg-white shadow-sm outline-none text-right font-almarai text-sm transition-all focus:ring-8 focus:ring-gold/5">
                                <i class="fas fa-search absolute right-5 top-1/2 -translate-y-1/2 text-gold/50"></i>
                            </div>

                            <div class="space-y-2 max-h-80 overflow-y-auto px-2 custom-scrollbar-v2" id="student_list">
                                @foreach($students as $student)
                                    <label class="student-item group relative flex items-center justify-between p-4 rounded-2xl border border-white bg-white/50 hover:bg-white hover:border-gold/30 hover:shadow-md cursor-pointer transition-all">
                                        <div class="flex items-center gap-4">
                                            <input type="checkbox" name="target_student_ids[]" value="{{ $student->id }}" class="w-6 h-6 rounded-lg border-gray-300 text-gold focus:ring-gold transition-transform group-hover:scale-110">
                                            <div class="flex flex-col text-right">
                                                <span class="text-sm font-black text-navy group-hover:text-gold transition-colors">{{ $student->name_ar }}</span>
                                                <span class="text-[10px] text-gray-400 font-mono tracking-widest">{{ $student->student_number }}</span>
                                            </div>
                                        </div>
                                        <div class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                            <i class="fas fa-plus text-[10px] text-gold"></i>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <style>
                    .custom-scrollbar-v2::-webkit-scrollbar { width: 6px; }
                    .custom-scrollbar-v2::-webkit-scrollbar-track { background: transparent; }
                    .custom-scrollbar-v2::-webkit-scrollbar-thumb { background: #E5E7EB; border-radius: 10px; border: 2px solid transparent; }
                    .custom-scrollbar-v2::-webkit-scrollbar-thumb:hover { background: #D1D5DB; }
                </style>

                <div class="flex gap-4 pt-4">
                    <button type="submit"
                        class="flex-[3] bg-navy text-white py-5 rounded-2xl font-black text-lg shadow-xl hover:shadow-navy/40 transition-all flex items-center justify-center gap-3">
                        <i class="fas fa-check-circle text-gold"></i>
                        <span>حفظ وجدولة الفعالية</span>
                    </button>
                    <button type="button" onclick="closePlanModal()"
                        class="flex-1 bg-gray-100 text-gray-400 py-5 rounded-2xl font-bold font-cairo hover:bg-gray-200 transition-colors">إلغاء</button>
                </div>
            </form>
        </div>
        </div>
    </div>

    <!-- Register Modal -->
    <div id="registerModal"
        class="fixed inset-0 bg-navy/60 backdrop-blur-sm hidden items-center justify-center z-[100] p-4">
        <div
            class="bg-white rounded-[2.5rem] p-10 max-w-md w-full shadow-2xl transform transition-all border-t-8 border-gold">
            <div class="text-center mb-10">
                <div
                    class="w-20 h-20 bg-gold/10 rounded-3xl flex items-center justify-center text-gold mx-auto mb-6 shadow-sm">
                    <i class="fas fa-qrcode text-3xl"></i>
                </div>
                <h2 class="text-2xl font-black font-cairo text-navy mb-1">تسجيل حضور / مشاركة</h2>
                <p id="reg_activity_name" class="text-gold font-bold font-almarai text-sm italic"></p>
            </div>

            <form id="registerForm" method="POST" class="space-y-6">
                @csrf
                <div id="reader" style="width: 100%; border-radius: 1.5rem; overflow: hidden; display: none;" class="mb-6 border-4 border-gold/20 shadow-inner"></div>
                
                <button type="button" id="toggleScannerBtn" onclick="toggleScanner()" 
                    class="w-full mb-6 py-4 rounded-2xl bg-gold/10 text-gold font-black font-cairo hover:bg-gold/20 transition-all flex items-center justify-center gap-3 border-2 border-dashed border-gold/30">
                    <i class="fas fa-camera"></i>
                    <span>تشغيل الكاميرا للمسح</span>
                </button>

                <div class="relative">
                    <label class="block text-sm font-black text-navy mb-3 font-cairo text-center">أدخل باركود الطالب أو الرقم الجامعي</label>
                    <input type="text" name="barcode" id="barcodeInput" required autofocus placeholder="قم بمسح الباركود..."
                        class="w-full px-5 py-6 rounded-2xl border border-gray-100 bg-gray-50/50 focus:bg-white focus:ring-8 focus:ring-gold/5 focus:border-gold outline-none text-center font-black text-2xl tracking-widest text-navy transition-all font-mono">
                    <div class="absolute left-4 top-[60px] text-gray-300">
                        <i class="fas fa-keyboard"></i>
                    </div>
                </div>

                <div class="flex gap-4 pt-6">
                    <button type="submit"
                        class="flex-[3] bg-gold text-navy py-5 rounded-2xl font-black text-lg shadow-xl hover:shadow-gold/40 transition-all flex items-center justify-center gap-3">
                        <i class="fas fa-plus-circle"></i>
                        <span>تسجيل الطالب</span>
                    </button>
                    <button type="button" onclick="closeRegisterModal()"
                        class="flex-1 bg-gray-100 text-gray-400 py-5 rounded-2xl font-bold font-cairo hover:bg-gray-200 transition-colors">إلغاء</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Scanner Library -->
    <script src="https://unpkg.com/html5-qrcode"></script>

    <script>
        let html5QrCode;

        function openPlanModal() { showModal('planModal'); }
        function closePlanModal() { hideModal('planModal'); }

        function openRegisterModal(id, name) {
            document.getElementById('reg_activity_name').innerText = name;
            let route = "{{ route('activities.register', ':id') }}";
            document.getElementById('registerForm').action = route.replace(':id', id);
            showModal('registerModal');
            setTimeout(() => document.getElementById('barcodeInput').focus(), 300);
        }
        
        async function closeRegisterModal() { 
            await stopScanner();
            hideModal('registerModal'); 
        }

        async function toggleScanner() {
            const reader = document.getElementById('reader');
            const btn = document.getElementById('toggleScannerBtn');
            const input = document.getElementById('barcodeInput');
            
            if (html5QrCode && html5QrCode.isScanning) {
                await stopScanner();
                return;
            }

            if (!window.isSecureContext && window.location.hostname !== 'localhost') {
                alert("الكاميرا تحتاج إلى اتصال آمن (HTTPS) أو تشغيلها عبر 'localhost' لتعمل بشكل صحيح في هذا المتصفح.");
                return;
            }

            reader.style.display = 'block';
            btn.querySelector('span').innerText = 'إيقاف الكاميرا';
            btn.classList.add('bg-red-50', 'text-red-500', 'border-red-200');
            btn.classList.remove('bg-gold/10', 'text-gold', 'border-gold/30');

            html5QrCode = new Html5Qrcode("reader");
            const config = { 
                fps: 25, 
                qrbox: { width: 350, height: 200 }, // Larger width for barcodes
                aspectRatio: 1.0,
                formatsToSupport: [ 
                    Html5QrcodeSupportedFormats.QR_CODE, 
                    Html5QrcodeSupportedFormats.CODE_128,
                    Html5QrcodeSupportedFormats.CODE_39,
                    Html5QrcodeSupportedFormats.CODE_93,
                    Html5QrcodeSupportedFormats.EAN_13
                ]
            };

            // Start scanning with support for multiple formats (QR, 1D Barcodes)
            html5QrCode.start(
                { facingMode: "environment" }, 
                config,
                (decodedText) => {
                    input.value = decodedText;
                    stopScanner();
                    document.getElementById('registerForm').submit();
                },
                (errorMessage) => { /* scanning... */ }
            ).catch(err => {
                console.error(err);
                alert("تعذر تشغيل الكاميرا. يرجى التأكد من منح الإذن لاستخدام الكاميرا.");
                stopScanner();
            });
        }

        async function stopScanner() {
            if (html5QrCode) {
                try {
                    if (html5QrCode.isScanning) {
                        await html5QrCode.stop();
                    }
                } catch (err) {
                    console.error("Scanner stop error:", err);
                }
                html5QrCode = null;
            }
            document.getElementById('reader').style.display = 'none';
            const btn = document.getElementById('toggleScannerBtn');
            if(btn) {
                btn.querySelector('span').innerText = 'تشغيل الكاميرا للمسح';
                btn.classList.remove('bg-red-50', 'text-red-500', 'border-red-200');
                btn.classList.add('bg-gold/10', 'text-gold', 'border-gold/30');
            }
        }

        function filterStudents() {
            const input = document.getElementById('student_search').value.toLowerCase();
            const items = document.getElementsByClassName('student-item');
            
            for (let i = 0; i < items.length; i++) {
                const name = items[i].querySelector('span').innerText.toLowerCase();
                if (name.includes(input)) {
                    items[i].classList.remove('hidden');
                } else {
                    items[i].classList.add('hidden');
                }
            }
        }

        function updateTargetOptions() {
            const clubId = document.getElementById('plan_club_id').value;
            const clubOption = document.getElementById('club_member_option');
            if (clubId) {
                clubOption.classList.remove('hidden');
                clubOption.classList.add('flex');
            } else {
                clubOption.classList.add('hidden');
                clubOption.classList.remove('flex');
                document.getElementById('target_club').checked = false;
            }
        }

        function showModal(id) {
            const m = document.getElementById(id);
            // Teleport modal to body on first use to escape overflow parents
            if (m.parentElement !== document.body) {
                document.body.appendChild(m);
            }
            m.classList.remove('hidden');
            if (id === 'planModal') {
                m.style.display = 'block';
                m.scrollTop = 0;
            } else {
                m.classList.add('flex');
            }
            document.body.style.overflow = 'hidden';
            const main = document.getElementById('mainContent');
            if (main) main.style.overflow = 'hidden';
        }

        function hideModal(id) {
            const m = document.getElementById(id);
            m.classList.add('hidden');
            m.style.display = '';
            m.classList.remove('flex');
            document.body.style.overflow = 'auto';
            const main = document.getElementById('mainContent');
            if (main) main.style.overflow = '';
        }
    </script>
@endpush
