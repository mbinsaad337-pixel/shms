@extends('layouts.app')

@section('title', 'الترحيل السنوي وأرشيف السنوات')

@section('content')
    <div x-data="{
        activeTab: '{{ request()->hasAny(['archived_year', 'module', 'date_from', 'date_to', 'search', 'page']) ? 'archives' : 'rollover' }}',
        selectAll: true,
        toggleAllModules(val) {
            let checkboxes = document.querySelectorAll('.module-checkbox');
            checkboxes.forEach(cb => cb.checked = val);
        }
    }" class="space-y-6 pb-12">

        <!-- Header Section -->
        <div
            class="bg-gradient-to-r from-navy via-[#00335e] to-navy rounded-3xl p-6 md:p-8 text-white shadow-xl relative overflow-hidden">
            <div class="absolute -left-10 -bottom-10 opacity-10 pointer-events-none">
                <i class="fas fa-archive text-9xl text-gold"></i>
            </div>
            <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <span class="bg-gold/20 text-gold text-xs font-black px-3 py-1 rounded-full border border-gold/30">
                            <i class="fas fa-calendar-alt ml-1"></i> إدارة السنوات والترحيل
                        </span>
                        <span class="text-xs text-gray-600 font-almarai">
                            {{ auth()->user()->center ? auth()->user()->center->name : 'جميع المراكز الطلابية' }}
                        </span>
                    </div>
                    <h1 class="text-2xl md:text-3xl font-black text-gold font-cairo">
                        الترحيل السنوي وأرشيف البيانات
                    </h1>
                    <p class="text-sm text-gray-400 font-almarai mt-1 max-w-2xl leading-relaxed">
                        نظام أرشفة وتصفية البيانات التشغيلية بنهاية السنة، مع الاحتفاظ بجميع السجلات التاريخية وتصفية
                        السجلات النشطة لاستقبال السنة الدراسية الجديدة.
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <button @click="activeTab = 'archives'"
                        class="px-4 py-2.5 bg-white/10 hover:bg-white/20 text-gold rounded-2xl text-xs font-bold font-cairo backdrop-blur transition-all flex items-center gap-2">
                        <i class="fas fa-search"></i>
                        <span>تصفح الأرشيف</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Alert Status -->
        @if (session('success'))
            <div
                class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl p-4 flex items-center gap-3 shadow-sm font-cairo">
                <i class="fas fa-check-circle text-emerald-500 text-xl"></i>
                <div class="flex-1 font-semibold text-sm">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if (session('error'))
            <div
                class="bg-red-50 border border-red-200 text-red-800 rounded-2xl p-4 flex items-center gap-3 shadow-sm font-cairo">
                <i class="fas fa-exclamation-circle text-red-500 text-xl"></i>
                <div class="flex-1 font-semibold text-sm">
                    {{ session('error') }}
                </div>
            </div>
        @endif

        <!-- Protected Excluded Categories Banner -->
        <div
            class="bg-gradient-to-r from-amber-500/10 via-amber-500/5 to-transparent border-r-4 border-amber-500 bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-start gap-4">
                <div
                    class="w-10 h-10 rounded-2xl bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-md shadow-amber-500/20">
                    <i class="fas fa-shield-alt text-lg"></i>
                </div>
                <div>
                    <h3 class="text-sm font-black text-navy font-cairo flex items-center gap-2">
                        <span>الفئات المستثناة دائماً من الترحيل والتصفية</span>
                        <span class="bg-amber-100 text-amber-800 text-[10px] px-2 py-0.5 rounded-full font-bold">محمية
                            بالنظام</span>
                    </h3>
                    <p class="text-xs text-gray-600 font-almarai mt-1 leading-relaxed">
                        حفاظاً على سلامة واستمرارية البيانات، يمنع الترحيل السنوي حظر أو مسح القطاعات الأساسية التالية بشكل
                        دائم:
                    </p>
                    <div class="flex flex-wrap gap-2 mt-3">
                        <span
                            class="inline-flex items-center gap-1.5 px-3 py-1 bg-gray-100 text-navy text-xs font-bold rounded-xl border border-gray-200">
                            <i class="fas fa-user-graduate text-gold"></i>
                            <span>الطلاب المستمرون والنشطون (Active Students) - محميون ومستثنون دائماً</span>
                        </span>
                        <span
                            class="inline-flex items-center gap-1.5 px-3 py-1 bg-gray-100 text-navy text-xs font-bold rounded-xl border border-gray-200">
                            <i class="fas fa-users-cog text-blue-600"></i>
                            <span>حسابات المستخدمين والموظفين (Users)</span>
                        </span>
                        <span
                            class="inline-flex items-center gap-1.5 px-3 py-1 bg-gray-100 text-navy text-xs font-bold rounded-xl border border-gray-200">
                            <i class="fas fa-boxes text-emerald-600"></i>
                            <span>الأصول والعهدة العينية (Assets)</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="flex items-center gap-2 border-b border-gray-200 pb-1 font-cairo">
            <button @click="activeTab = 'rollover'"
                :class="activeTab === 'rollover' ? 'bg-navy text-white font-black shadow-md' :
                    'bg-white text-gray-600 hover:bg-gray-50 font-bold border border-gray-200'"
                class="px-5 py-3 rounded-2xl text-xs transition-all flex items-center gap-2">
                <i class="fas fa-cogs text-gold"></i>
                <span>تنفيذ الترحيل السنوي للسنة الحالية</span>
            </button>

            <button @click="activeTab = 'archives'"
                :class="activeTab === 'archives' ? 'bg-navy text-white font-black shadow-md' :
                    'bg-white text-gray-600 hover:bg-gray-50 font-bold border border-gray-200'"
                class="px-5 py-3 rounded-2xl text-xs transition-all flex items-center gap-2">
                <i class="fas fa-archive text-gold"></i>
                <span>أرشيف السنوات السابقة</span>
                @if ($archives->total() > 0)
                    <span class="bg-gold text-navy text-[10px] font-black px-2 py-0.5 rounded-full">
                        {{ $archives->total() }}
                    </span>
                @endif
            </button>

            <button @click="activeTab = 'history'"
                :class="activeTab === 'history' ? 'bg-navy text-white font-black shadow-md' :
                    'bg-white text-gray-600 hover:bg-gray-50 font-bold border border-gray-200'"
                class="px-5 py-3 rounded-2xl text-xs transition-all flex items-center gap-2">
                <i class="fas fa-history text-gold"></i>
                <span>سجل عمليات الترحيل التاريخية</span>
                <span class="bg-gray-100 text-navy text-[10px] font-bold px-2 py-0.5 rounded-full">
                    {{ count($rollovers) }}
                </span>
            </button>
        </div>

        <!-- Tab 1: Perform Annual Rollover -->
        <div x-show="activeTab === 'rollover'" x-transition:enter="transition ease-out duration-300" class="space-y-6">
            <form action="{{ route('annual-rollover.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Form Configuration Box -->
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 space-y-6">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                        <div>
                            <h2 class="text-lg font-black text-navy font-cairo">إعداد الترحيل السنوي</h2>
                            <p class="text-xs text-gray-400 font-almarai">حدد السنة الدراسية والقطاعات المراد نقلها للأرشيف
                                وتصفيتها</p>
                        </div>

                        @if (auth()->user()->hasRole('super-admin'))
                            <div class="w-64">
                                <label class="block text-xs font-bold text-navy font-cairo mb-1">المركز المستهدف</label>
                                <select name="center_id"
                                    class="w-full text-xs font-cairo border border-gray-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-navy">
                                    <option value="">جميع المراكز الطلابية</option>
                                    @foreach ($centers as $c)
                                        <option value="{{ $c->id }}" {{ $centerId == $c->id ? 'selected' : '' }}>
                                            {{ $c->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-navy font-cairo mb-2">
                                السنة المراد ترحيل بياناتها <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <select name="year" required
                                    class="w-full text-xs font-bold font-cairo border border-gray-200 rounded-2xl px-4 py-3 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-navy">
                                    @php
                                        $currentYearNum = date('Y');
                                        $prevYearNum = $currentYearNum - 1;
                                        $nextYearNum = $currentYearNum + 1;
                                    @endphp
                                    <option value="{{ $prevYearNum }}-{{ $currentYearNum }}">العام الدراسي الماضي
                                        ({{ $prevYearNum }}-{{ $currentYearNum }})</option>
                                    <option value="{{ $currentYearNum }}" selected>السنة الحالية ({{ $currentYearNum }})
                                    </option>
                                    <option value="{{ $currentYearNum }}-{{ $nextYearNum }}">العام الدراسي الحادي
                                        ({{ $currentYearNum }}-{{ $nextYearNum }})</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-navy font-cairo mb-2">
                                تاريخ السقف لإيقاف الترحيل (اختياري)
                            </label>
                            <input type="date" name="cutoff_date" value="{{ date('Y-m-d') }}"
                                class="w-full text-xs font-cairo border border-gray-200 rounded-2xl px-4 py-3 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-navy">
                            <p class="text-[10px] text-gray-400 mt-1">سيتم أرشفة البيانات المنشأة في أو قبل هذا التاريخ فقط
                            </p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-navy font-cairo mb-2">ملاحظات الترحيل السنوي
                            (اختياري)</label>
                        <textarea name="notes" rows="2" placeholder="أدخل أي ملاحظات توثيقية لعملية الترحيل هذه..."
                            class="w-full text-xs font-almarai border border-gray-200 rounded-2xl p-3 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-navy"></textarea>
                    </div>
                </div>

                <!-- Module Selection Grid -->
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 space-y-6">
                    <div
                        class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-gray-100 pb-4">
                        <div>
                            <h2 class="text-lg font-black text-navy font-cairo">تحديد القطاعات المشمولة بالترحيل والتصفية
                            </h2>
                            <p class="text-xs text-gray-400 font-almarai">يمكنك تحديد الأقسام التي ترغب بترحيلها أو إلغاء
                                تحديد أي قسم</p>
                        </div>

                        <div class="flex items-center gap-2">
                            <button type="button" @click="toggleAllModules(true)"
                                class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-navy text-xs font-bold rounded-xl font-cairo transition-all">
                                <i class="fas fa-check-double text-gold ml-1"></i> تحديد الكل
                            </button>
                            <button type="button" @click="toggleAllModules(false)"
                                class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-navy text-xs font-bold rounded-xl font-cairo transition-all">
                                <i class="fas fa-times ml-1 text-red-400"></i> إلغاء الجميع
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">

                        <!-- 1. Administrative -->
                        <label
                            class="relative block p-4 rounded-2xl border-2 border-gray-100 hover:border-navy/30 bg-gray-50/30 hover:bg-white transition-all cursor-pointer group">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center font-bold">
                                        <i class="fas fa-clipboard-list text-lg"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-black text-navy font-cairo">الإجراءات الإدارية</h4>
                                        <p class="text-[10px] text-gray-400 font-almarai">المخالفات، الجزاءات، التعهدات،
                                            الغياب، الاستئذانات</p>
                                    </div>
                                </div>
                                <input type="checkbox" name="modules[]" value="administrative" checked
                                    class="module-checkbox w-5 h-5 text-navy rounded border-gray-300 focus:ring-navy mt-1">
                            </div>
                            <div class="mt-3 pt-3 border-t border-gray-100 flex items-center justify-between text-[11px]">
                                <span class="text-gray-500">سجلات جاهزة للترحيل:</span>
                                <span
                                    class="font-black text-navy font-cairo bg-amber-100 text-amber-900 px-2 py-0.5 rounded-full">
                                    {{ array_sum($currentCounts['administrative']) }} سجل
                                </span>
                            </div>
                        </label>

                        <!-- 2. Activities & News -->
                        <label
                            class="relative block p-4 rounded-2xl border-2 border-gray-100 hover:border-navy/30 bg-gray-50/30 hover:bg-white transition-all cursor-pointer group">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-600 flex items-center justify-center font-bold">
                                        <i class="fas fa-calendar-day text-lg"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-black text-navy font-cairo">الأنشطة والفعاليات والأخبار
                                        </h4>
                                        <p class="text-[10px] text-gray-400 font-almarai">الأنشطة المكتملة والمشاركين،
                                            الأخبار والإعلانات</p>
                                    </div>
                                </div>
                                <input type="checkbox" name="modules[]" value="activities" checked
                                    class="module-checkbox w-5 h-5 text-navy rounded border-gray-300 focus:ring-navy mt-1">
                            </div>
                            <div class="mt-3 pt-3 border-t border-gray-100 flex items-center justify-between text-[11px]">
                                <span class="text-gray-500">سجلات جاهزة للترحيل:</span>
                                <span
                                    class="font-black text-navy font-cairo bg-blue-100 text-blue-900 px-2 py-0.5 rounded-full">
                                    {{ array_sum($currentCounts['activities']) }} سجل
                                </span>
                            </div>
                        </label>

                        <!-- 3. Financial System -->
                        <label
                            class="relative block p-4 rounded-2xl border-2 border-gray-100 hover:border-navy/30 bg-gray-50/30 hover:bg-white transition-all cursor-pointer group">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center font-bold">
                                        <i class="fas fa-wallet text-lg"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-black text-navy font-cairo">النظام المالي والموازنات</h4>
                                        <p class="text-[10px] text-gray-400 font-almarai">سندات القبض والصرف، الموازنات،
                                            التصفيات، المصروفات</p>
                                    </div>
                                </div>
                                <input type="checkbox" name="modules[]" value="financial" checked
                                    class="module-checkbox w-5 h-5 text-navy rounded border-gray-300 focus:ring-navy mt-1">
                            </div>
                            <div class="mt-3 pt-3 border-t border-gray-100 flex items-center justify-between text-[11px]">
                                <span class="text-gray-500">سجلات جاهزة للترحيل:</span>
                                <span
                                    class="font-black text-navy font-cairo bg-emerald-100 text-emerald-900 px-2 py-0.5 rounded-full">
                                    {{ array_sum($currentCounts['financial']) }} سجل
                                </span>
                            </div>
                        </label>

                        <!-- 4. Nutrition Module -->
                        <label
                            class="relative block p-4 rounded-2xl border-2 border-gray-100 hover:border-navy/30 bg-gray-50/30 hover:bg-white transition-all cursor-pointer group">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 rounded-xl bg-purple-500/10 text-purple-600 flex items-center justify-center font-bold">
                                        <i class="fas fa-utensils text-lg"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-black text-navy font-cairo">نظام التغذية والوجبات</h4>
                                        <p class="text-[10px] text-gray-400 font-almarai">توزيع الوجبات، الاشتراكات، فواتير
                                            الشراء، التصفيات</p>
                                    </div>
                                </div>
                                <input type="checkbox" name="modules[]" value="nutrition" checked
                                    class="module-checkbox w-5 h-5 text-navy rounded border-gray-300 focus:ring-navy mt-1">
                            </div>
                            <div class="mt-3 pt-3 border-t border-gray-100 flex items-center justify-between text-[11px]">
                                <span class="text-gray-500">سجلات جاهزة للترحيل:</span>
                                <span
                                    class="font-black text-navy font-cairo bg-purple-100 text-purple-900 px-2 py-0.5 rounded-full">
                                    {{ array_sum($currentCounts['nutrition']) }} سجل
                                </span>
                            </div>
                        </label>

                        <!-- 5. Quran Circles -->
                        <label
                            class="relative block p-4 rounded-2xl border-2 border-gray-100 hover:border-navy/30 bg-gray-50/30 hover:bg-white transition-all cursor-pointer group">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 rounded-xl bg-teal-500/10 text-teal-600 flex items-center justify-center font-bold">
                                        <i class="fas fa-quran text-lg"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-black text-navy font-cairo">الحلقات القرآنية والحضور</h4>
                                        <p class="text-[10px] text-gray-400 font-almarai">جلسات التحفيظ وسجلات الحضور
                                            والغياب اليومية</p>
                                    </div>
                                </div>
                                <input type="checkbox" name="modules[]" value="quran" checked
                                    class="module-checkbox w-5 h-5 text-navy rounded border-gray-300 focus:ring-navy mt-1">
                            </div>
                            <div class="mt-3 pt-3 border-t border-gray-100 flex items-center justify-between text-[11px]">
                                <span class="text-gray-500">سجلات جاهزة للترحيل:</span>
                                <span
                                    class="font-black text-navy font-cairo bg-teal-100 text-teal-900 px-2 py-0.5 rounded-full">
                                    {{ array_sum($currentCounts['quran']) }} جلسة
                                </span>
                            </div>
                        </label>

                        <!-- 6. Academic Grades -->
                        <label
                            class="relative block p-4 rounded-2xl border-2 border-gray-100 hover:border-navy/30 bg-gray-50/30 hover:bg-white transition-all cursor-pointer group">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 rounded-xl bg-indigo-500/10 text-indigo-600 flex items-center justify-center font-bold">
                                        <i class="fas fa-graduation-cap text-lg"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-black text-navy font-cairo">الدرجات والإنجازات الأكاديمية
                                        </h4>
                                        <p class="text-[10px] text-gray-400 font-almarai">بيانات الدرجات السنوية وإنجازات
                                            الطلاب</p>
                                    </div>
                                </div>
                                <input type="checkbox" name="modules[]" value="academic" checked
                                    class="module-checkbox w-5 h-5 text-navy rounded border-gray-300 focus:ring-navy mt-1">
                            </div>
                            <div class="mt-3 pt-3 border-t border-gray-100 flex items-center justify-between text-[11px]">
                                <span class="text-gray-500">سجلات جاهزة للترحيل:</span>
                                <span
                                    class="font-black text-navy font-cairo bg-indigo-100 text-indigo-900 px-2 py-0.5 rounded-full">
                                    {{ array_sum($currentCounts['academic']) }} سجل
                                </span>
                            </div>
                        </label>

                        <!-- 7. Housing Assignments Reset -->
                        <label
                            class="relative block p-4 rounded-2xl border-2 border-gray-100 hover:border-navy/30 bg-gray-50/30 hover:bg-white transition-all cursor-pointer group">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 rounded-xl bg-rose-500/10 text-rose-600 flex items-center justify-center font-bold">
                                        <i class="fas fa-door-open text-lg"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-black text-navy font-cairo">تسكين السكن والغرف</h4>
                                        <p class="text-[10px] text-gray-400 font-almarai">إعادة ضبط تسكين الغرف للسنة
                                            الدراسية الجديدة</p>
                                    </div>
                                </div>
                                <input type="checkbox" name="modules[]" value="rooms" checked
                                    class="module-checkbox w-5 h-5 text-navy rounded border-gray-300 focus:ring-navy mt-1">
                            </div>
                            <div class="mt-3 pt-3 border-t border-gray-100 flex items-center justify-between text-[11px]">
                                <span class="text-gray-500">إشغالات نشطة حالياً:</span>
                                <span
                                    class="font-black text-navy font-cairo bg-rose-100 text-rose-900 px-2 py-0.5 rounded-full">
                                    {{ array_sum($currentCounts['rooms']) }} غرف
                                </span>
                            </div>
                        </label>

                        <!-- 8. Vehicle Violations -->
                        <label
                            class="relative block p-4 rounded-2xl border-2 border-gray-100 hover:border-navy/30 bg-gray-50/30 hover:bg-white transition-all cursor-pointer group">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 rounded-xl bg-orange-500/10 text-orange-600 flex items-center justify-center font-bold">
                                        <i class="fas fa-car text-lg"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-black text-navy font-cairo"> مركبات الطلاب</h4>
                                        <p class="text-[10px] text-gray-400 font-almarai">أرشفة وتصفية المخالفات المرورية
                                            السابقة</p>
                                    </div>
                                </div>
                                <input type="checkbox" name="modules[]" value="vehicles" checked
                                    class="module-checkbox w-5 h-5 text-navy rounded border-gray-300 focus:ring-navy mt-1">
                            </div>
                            <div class="mt-3 pt-3 border-t border-gray-100 flex items-center justify-between text-[11px]">
                                <span class="text-gray-500">سجلات جاهزة للترحيل:</span>
                                <span
                                    class="font-black text-navy font-cairo bg-orange-100 text-orange-900 px-2 py-0.5 rounded-full">
                                    {{ array_sum($currentCounts['vehicles']) }} مخالفة
                                </span>
                            </div>
                        </label>

                        <!-- 9. Complaints & Notifications -->
                        <label
                            class="relative block p-4 rounded-2xl border-2 border-gray-100 hover:border-navy/30 bg-gray-50/30 hover:bg-white transition-all cursor-pointer group">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 rounded-xl bg-sky-500/10 text-sky-600 flex items-center justify-center font-bold">
                                        <i class="fas fa-inbox text-lg"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-black text-navy font-cairo">الشكاوى والإشعارات</h4>
                                        <p class="text-[10px] text-gray-400 font-almarai">أرشفة المراسلات والإشعارات
                                            الداخلية للمركز</p>
                                    </div>
                                </div>
                                <input type="checkbox" name="modules[]" value="complaints" checked
                                    class="module-checkbox w-5 h-5 text-navy rounded border-gray-300 focus:ring-navy mt-1">
                            </div>
                            <div class="mt-3 pt-3 border-t border-gray-100 flex items-center justify-between text-[11px]">
                                <span class="text-gray-500">سجلات جاهزة للترحيل:</span>
                                <span
                                    class="font-black text-navy font-cairo bg-sky-100 text-sky-900 px-2 py-0.5 rounded-full">
                                    {{ array_sum($currentCounts['complaints']) }} إشعار
                                </span>
                            </div>
                        </label>

                        <!-- 10. Graduated Students Only -->
                        <label
                            class="relative block p-4 rounded-2xl border-2 border-gray-100 hover:border-navy/30 bg-gray-50/30 hover:bg-white transition-all cursor-pointer group">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 rounded-xl bg-teal-500/10 text-teal-600 flex items-center justify-center font-bold">
                                        <i class="fas fa-user-graduate text-lg"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-black text-navy font-cairo">الطلاب الخريجون (الخريجون فقط)
                                        </h4>
                                        <p class="text-[10px] text-gray-400 font-almarai">أرشفة سجلات الطلاب المتخرجين فقط
                                            ونقلهم للأرشيف السنوي</p>
                                    </div>
                                </div>
                                <input type="checkbox" name="modules[]" value="graduates" checked
                                    class="module-checkbox w-5 h-5 text-navy rounded border-gray-300 focus:ring-navy mt-1">
                            </div>
                            <div class="mt-3 pt-3 border-t border-gray-100 flex items-center justify-between text-[11px]">
                                <span class="text-gray-500">خريجون مؤهلون للترحيل:</span>
                                <span
                                    class="font-black text-navy font-cairo bg-teal-100 text-teal-900 px-2 py-0.5 rounded-full">
                                    {{ array_sum($currentCounts['graduates'] ?? [0]) }} طالب خريج
                                </span>
                            </div>
                        </label>

                        <!-- 11. Funds -->
                        <label
                            class="relative block p-4 rounded-2xl border-2 border-gray-100 hover:border-navy/30 bg-gray-50/30 hover:bg-white transition-all cursor-pointer group">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 rounded-xl bg-lime-500/10 text-lime-600 flex items-center justify-center font-bold">
                                        <i class="fas fa-piggy-bank text-lg"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-black text-navy font-cairo">الصناديق المالية</h4>
                                        <p class="text-[10px] text-gray-400 font-almarai">أرشفة الصناديق المالية وأرصدتها
                                            (يستثنى الصناديق النظامية)</p>
                                    </div>
                                </div>
                                <input type="checkbox" name="modules[]" value="funds" checked
                                    class="module-checkbox w-5 h-5 text-navy rounded border-gray-300 focus:ring-navy mt-1">
                            </div>
                            <div class="mt-3 pt-3 border-t border-gray-100 flex items-center justify-between text-[11px]">
                                <span class="text-gray-500">سجلات جاهزة للترحيل:</span>
                                <span
                                    class="font-black text-navy font-cairo bg-lime-100 text-lime-900 px-2 py-0.5 rounded-full">
                                    {{ array_sum($currentCounts['funds']) }} صندوق
                                </span>
                            </div>
                        </label>

                        <!-- 12. Clubs -->
                        <label
                            class="relative block p-4 rounded-2xl border-2 border-gray-100 hover:border-navy/30 bg-gray-50/30 hover:bg-white transition-all cursor-pointer group">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 rounded-xl bg-pink-500/10 text-pink-600 flex items-center justify-center font-bold">
                                        <i class="fas fa-chess text-lg"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-black text-navy font-cairo">الأندية الطلابية</h4>
                                        <p class="text-[10px] text-gray-400 font-almarai">أرشفة الأندية وأعضائها
                                            المسجلين</p>
                                    </div>
                                </div>
                                <input type="checkbox" name="modules[]" value="clubs" checked
                                    class="module-checkbox w-5 h-5 text-navy rounded border-gray-300 focus:ring-navy mt-1">
                            </div>
                            <div class="mt-3 pt-3 border-t border-gray-100 flex items-center justify-between text-[11px]">
                                <span class="text-gray-500">سجلات جاهزة للترحيل:</span>
                                <span
                                    class="font-black text-navy font-cairo bg-pink-100 text-pink-900 px-2 py-0.5 rounded-full">
                                    {{ array_sum($currentCounts['clubs']) }} ناد
                                </span>
                            </div>
                        </label>

                    </div>

                    <div class="pt-4 border-t border-gray-100 flex items-center justify-end">
                        <button type="submit"
                            data-confirm="هل أنت تأكد من إجراء الترحيل السنوي للقطاعات المحددة؟ سيتم نقل البيانات إلى الأرشيف السنوي وتصفية السجلات النشطة للعام الجديد."
                            class="px-8 py-3.5 bg-gradient-to-r from-navy to-[#083358] text-gold hover:text-gold font-black text-sm rounded-2xl shadow-lg hover:shadow-xl transition-all flex items-center gap-3">
                            <i class="fas fa-paper-plane"></i>
                            <span>بدء تنفيذ الترحيل السنوي الآن</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Tab 2: Archives Explorer -->
        <div x-show="activeTab === 'archives'" x-transition:enter="transition ease-out duration-300" class="space-y-6">

            <!-- Filter Form Box -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
                <form action="{{ route('annual-rollover.index') }}" method="GET" class="space-y-4">
                    <input type="hidden" name="tab" value="archives">

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-navy font-cairo mb-1">السنة المؤرخة</label>
                            <select name="archived_year"
                                class="w-full text-xs font-cairo border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50/50">
                                <option value="">جميع السنوات المؤرخة</option>
                                @foreach ($availableYears as $yr)
                                    <option value="{{ $yr }}"
                                        {{ request('archived_year') == $yr ? 'selected' : '' }}>{{ $yr }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-navy font-cairo mb-1">القطاع / القسم</label>
                            <select name="module"
                                class="w-full text-xs font-cairo border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50/50">
                                <option value="">جميع الأقسام</option>
                                <option value="administrative"
                                    {{ request('module') == 'administrative' ? 'selected' : '' }}>الإجراءات الإدارية
                                </option>
                                <option value="activities" {{ request('module') == 'activities' ? 'selected' : '' }}>
                                    الأنشطة والفعاليات والأخبار</option>
                                <option value="financial" {{ request('module') == 'financial' ? 'selected' : '' }}>النظام
                                    المالي</option>
                                <option value="nutrition" {{ request('module') == 'nutrition' ? 'selected' : '' }}>نظام
                                    التغذية والوجبات</option>
                                <option value="quran" {{ request('module') == 'quran' ? 'selected' : '' }}>الحلقات
                                    القرآنية</option>
                                <option value="academic" {{ request('module') == 'academic' ? 'selected' : '' }}>الدرجات
                                    والأكاديمي</option>
                                <option value="rooms" {{ request('module') == 'rooms' ? 'selected' : '' }}>تسكين الغرف
                                </option>
                                <option value="vehicles" {{ request('module') == 'vehicles' ? 'selected' : '' }}>مركبات
                                    الطلاب</option>
                                <option value="complaints" {{ request('module') == 'complaints' ? 'selected' : '' }}>
                                    الشكاوى والإشعارات</option>
                                <option value="graduates" {{ request('module') == 'graduates' ? 'selected' : '' }}>الطلاب
                                    الخريجون</option>
                                <option value="funds" {{ request('module') == 'funds' ? 'selected' : '' }}>الصناديق
                                    المالية</option>
                                <option value="clubs" {{ request('module') == 'clubs' ? 'selected' : '' }}>الأندية
                                    الطلابية</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-navy font-cairo mb-1">من تاريخ</label>
                            <input type="date" name="date_from" value="{{ request('date_from') }}"
                                class="w-full text-xs font-cairo border border-gray-200 rounded-xl px-3 py-2 bg-gray-50/50">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-navy font-cairo mb-1">إلى تاريخ</label>
                            <input type="date" name="date_to" value="{{ request('date_to') }}"
                                class="w-full text-xs font-cairo border border-gray-200 rounded-xl px-3 py-2 bg-gray-50/50">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-navy font-cairo mb-1">بحث بالاسم / العنوان</label>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="ابحث في الأرشيف..."
                                class="w-full text-xs font-cairo border border-gray-200 rounded-xl px-3 py-2 bg-gray-50/50">
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                        <div class="flex items-center gap-2">
                            <button type="submit"
                                class="px-5 py-2.5 bg-navy text-gold font-bold text-xs rounded-xl font-cairo hover:bg-gold hover:text-navy transition-all flex items-center gap-2">
                                <i class="fas fa-filter"></i> تطبيق تصفية الأرشيف
                            </button>
                            <a href="{{ route('annual-rollover.index') }}"
                                class="px-4 py-2.5 bg-gray-100 text-gray-600 font-bold text-xs rounded-xl font-cairo hover:bg-gray-200 transition-all">
                                إلغاء التصفية
                            </a>
                        </div>

                        <div>
                            <a href="{{ route('annual-rollover.export-pdf', request()->query()) }}" target="_blank"
                                class="px-5 py-2.5 bg-red-600 text-white font-bold text-xs rounded-xl font-cairo hover:bg-red-700 transition-all flex items-center gap-2 shadow-sm">
                                <i class="fas fa-file-pdf"></i> تصدير تقرير PDF للأرشيف
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Archives Table -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-4 bg-gray-50/70 border-b border-gray-100 flex items-center justify-between">
                    <span class="text-xs font-black text-navy font-cairo">سجلات الأرشيف المعروضة ({{ $archives->total() }}
                        سجل)</span>
                    <span class="text-[10px] text-gray-400 font-almarai">صفحة {{ $archives->currentPage() }} من
                        {{ $archives->lastPage() }}</span>
                </div>

                @if ($archives->isEmpty())
                    <div class="text-center py-16">
                        <i class="fas fa-folder-open text-5xl text-gray-200 mb-3 block"></i>
                        <p class="text-sm font-bold text-gray-400 font-cairo">لا توجد سجلات مؤرشفة تنطبق عليها شروط البحث
                            والتصفية</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-right border-collapse">
                            <thead>
                                <tr
                                    class="bg-gray-50 text-[11px] font-bold text-gray-500 font-cairo border-b border-gray-100">
                                    <th class="p-4">السنة</th>
                                    <th class="p-4">القسم والنوع</th>
                                    <th class="p-4">عنوان السجل المؤرشف</th>
                                    <th class="p-4">الطالب المعني</th>
                                    <th class="p-4">المبلغ المالي</th>
                                    <th class="p-4">تاريخ السجل الأصلي</th>
                                    <th class="p-4 text-center">التفاصيل</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-xs font-almarai">
                                @foreach ($archives as $arc)
                                    <tr class="hover:bg-blue-50/30 transition-colors">
                                        <td class="p-4 font-bold text-navy font-cairo">
                                            <span class="bg-navy/10 text-navy px-2.5 py-1 rounded-lg text-[11px]">
                                                {{ $arc->year }}
                                            </span>
                                        </td>
                                        <td class="p-4">
                                            @php
                                                $moduleLabels = [
                                                    'administrative' => [
                                                        'الإجراءات الإدارية',
                                                        'bg-amber-100 text-amber-800',
                                                    ],
                                                    'activities' => ['الأنشطة والأخبار', 'bg-blue-100 text-blue-800'],
                                                    'financial' => ['النظام المالي', 'bg-emerald-100 text-emerald-800'],
                                                    'nutrition' => ['نظام التغذية', 'bg-purple-100 text-purple-800'],
                                                    'quran' => ['الحلقات القرآنية', 'bg-teal-100 text-teal-800'],
                                                    'academic' => [
                                                        'الأكاديمي والدرجات',
                                                        'bg-indigo-100 text-indigo-800',
                                                    ],
                                                    'rooms' => ['تسكين الغرف', 'bg-rose-100 text-rose-800'],
                                                    'vehicles' => ['مركبات الطلاب', 'bg-orange-100 text-orange-800'],
                                                    'complaints' => ['الشكاوى والإشعارات', 'bg-sky-100 text-sky-800'],
                                                    'graduates' => ['الطلاب الخريجون', 'bg-teal-100 text-teal-800'],
                                                    'funds' => ['الصناديق المالية', 'bg-lime-100 text-lime-800'],
                                                    'clubs' => ['الأندية الطلابية', 'bg-pink-100 text-pink-800'],
                                                ];
                                                $lbl = $moduleLabels[$arc->module] ?? [
                                                    $arc->module,
                                                    'bg-gray-100 text-gray-800',
                                                ];
                                            @endphp
                                            <span
                                                class="px-2.5 py-1 rounded-full text-[10px] font-bold font-cairo {{ $lbl[1] }}">
                                                {{ $lbl[0] }}
                                            </span>
                                        </td>
                                        <td class="p-4 font-bold text-gray-800 font-cairo">
                                            {{ $arc->title }}
                                        </td>
                                        <td class="p-4 text-gray-600">
                                            {{ $arc->student_name ?: ($arc->student ? $arc->student->name_ar : '-') }}
                                        </td>
                                        <td class="p-4 font-bold text-emerald-700">
                                            {{ $arc->amount > 0 ? number_format($arc->amount, 2) . ' ريال' : '-' }}
                                        </td>
                                        <td class="p-4 text-gray-400 text-[11px]">
                                            {{ $arc->record_date ? $arc->record_date->format('Y/m/d H:i') : $arc->created_at->format('Y/m/d') }}
                                        </td>
                                        <td class="p-4 text-center">
                                            <div class="flex items-center justify-center gap-1.5">
                                                @if ($arc->module === 'graduates')
                                                    <a href="{{ route('annual-rollover.preview-graduate', $arc->id) }}"
                                                        target="_blank"
                                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-navy/10 hover:bg-navy text-navy hover:text-navy rounded-xl text-xs font-bold font-cairo transition-all shadow-sm">
                                                        <i class="fas fa-external-link-alt text-[10px]"></i>
                                                        <span>عرض الملف الشخصي</span>
                                                    </a>
                                                @else
                                                    <a href="{{ route('annual-rollover.show-archive', $arc->id) }}"
                                                        target="_blank"
                                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-navy/10 hover:bg-navy text-navy hover:text-navy rounded-xl text-xs font-bold font-cairo transition-all shadow-sm">
                                                        <i class="fas fa-external-link-alt text-[10px]"></i>
                                                        <span>عرض التفاصيل</span>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="p-4 border-t border-gray-100">
                        {{ $archives->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Tab 3: Rollover History Log -->
        <div x-show="activeTab === 'history'" x-transition:enter="transition ease-out duration-300" class="space-y-6">
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
                <h3 class="text-base font-black text-navy font-cairo mb-4">سجل عمليات الترحيل المنفذة سابقاً</h3>

                @if ($rollovers->isEmpty())
                    <div class="text-center py-12">
                        <i class="fas fa-history text-4xl text-gray-200 mb-2 block"></i>
                        <p class="text-xs text-gray-400 font-almarai">لم يتم تنفيذ أي عملية ترحيل سنوي حتى الآن</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-right border-collapse">
                            <thead>
                                <tr
                                    class="bg-gray-50 text-[11px] font-bold text-gray-500 font-cairo border-b border-gray-100">
                                    <th class="p-3">السنة ترحيلها</th>
                                    <th class="p-3">تاريخ التنفيذ</th>
                                    <th class="p-3">المنفذ بواسطة</th>
                                    <th class="p-3">المركز</th>
                                    <th class="p-3">القطاعات المشمولة</th>
                                    <th class="p-3">ملخص السجلات المؤرشفة</th>
                                    <th class="p-3 text-center">إجراءات</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-xs font-almarai">
                                @foreach ($rollovers as $r)
                                    <tr class="hover:bg-gray-50">
                                        <td class="p-3 font-black text-navy font-cairo">{{ $r->year }}</td>
                                        <td class="p-3 text-gray-500">{{ $r->created_at->format('Y/m/d H:i') }}</td>
                                        <td class="p-3 font-bold text-gray-800">{{ optional($r->user)->name }}</td>
                                        <td class="p-3 text-gray-600">{{ optional($r->center)->name ?? 'جميع المراكز' }}
                                        </td>
                                        <td class="p-3">
                                            <div class="flex flex-wrap gap-1">
                                                @foreach ((array) $r->modules as $m)
                                                    <span
                                                        class="px-2 py-0.5 bg-gray-100 text-navy text-[10px] font-bold rounded-md font-cairo">
                                                        {{ $m }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="p-3">
                                            @if (is_array($r->summary))
                                                <div class="flex flex-wrap gap-1">
                                                    @foreach ($r->summary as $mk => $cnt)
                                                        <span
                                                            class="px-2 py-0.5 bg-blue-50 text-blue-700 text-[10px] font-bold rounded-md">
                                                            {{ $mk }}: {{ $cnt }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="p-3">
                                            <div class="flex items-center justify-center gap-2">
                                                <form action="{{ route('annual-rollover.undo', $r->id) }}" method="POST"
                                                    onsubmit="return confirm('هل أنت متأكد من إلغاء ترحيل هذه السنة؟ سيتم استعادة جميع السجلات والملفات المؤرشفة إلى حالتها الأصلية.');">
                                                    @csrf
                                                    <button type="submit"
                                                        class="px-3 py-1.5 bg-red-50 hover:bg-red-600 hover:text-white text-red-600 text-[10px] font-bold rounded-xl font-cairo transition-all flex items-center gap-1 shadow-sm">
                                                        <i class="fas fa-undo"></i>
                                                        <span>إلغاء الترحيل</span>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

    </div>
@endsection
