{{-- LEAVES / ISTIZHAN TAB --}}
<div class="space-y-6">

    {{-- Pending requests alert banner --}}
    @if($pendingLeavesCount > 0)
    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 flex items-center gap-4">
        <div class="w-10 h-10 bg-amber-500 rounded-xl flex items-center justify-center text-white">
            <i class="fas fa-bell animate-pulse"></i>
        </div>
        <div>
            <p class="font-black text-amber-700 font-cairo">{{ $pendingLeavesCount }} طلب استئذان بانتظار الموافقة</p>
            <p class="text-xs text-amber-600 font-almarai mt-0.5">يرجى مراجعة الطلبات أدناه وإما الموافقة أو الرفض أو تحويلها إلى مخالفة</p>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        {{-- Form --}}
        <div class="xl:col-span-1">
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-black text-navy font-cairo mb-5 flex items-center gap-2">
                    <i class="fas fa-plus-circle text-navy/60"></i> تسجيل استئذان (المشرف)
                </h2>
                <p class="text-xs text-gray-400 font-almarai mb-4 bg-blue-50 p-3 rounded-xl border border-blue-100">
                    <i class="fas fa-info-circle text-blue-400 ml-1"></i>
                    هذا النموذج للتسجيل المباشر من المشرف. طلبات الطلاب تظهر في القائمة بحالة "قيد الانتظار".
                </p>
                <form action="{{ route('leaves.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1.5 font-cairo">الطالب <span class="text-red-500">*</span></label>
                        <div dir="rtl">
                            <select name="student_id[]" multiple class="tom-select w-full" placeholder="ابحث باسم الطالب أو الرقم...">
                                <option value="">-- اختر الطلاب --</option>
                                @foreach($students as $s)
                                <option value="{{ $s->id }}">{{ $s->name_ar }} ({{ $s->student_number }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1.5 font-cairo">نوع الاستئذان <span class="text-red-500">*</span></label>
                        <select name="type" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-navy text-sm font-almarai outline-none focus:ring-2 focus:ring-navy/10">
                            <option value="temporary">استئذان مؤقت</option>
                            <option value="vacation">إجازة</option>
                            <option value="medical">إجازة طبية</option>
                            <option value="lateness">تأخير</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-1.5 font-cairo">تاريخ المغادرة <span class="text-red-500">*</span></label>
                            <input type="date" name="departure_date" value="{{ date('Y-m-d') }}" class="w-full px-3 py-2.5 rounded-xl border border-gray-200 bg-gray-50 text-sm   outline-none focus:ring-2 focus:ring-navy/10">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-1.5 font-cairo">وقت المغادرة</label>
                            <input type="time" name="departure_time" class="w-full px-3 py-2.5 rounded-xl border border-gray-200 bg-gray-50 text-sm   outline-none focus:ring-2 focus:ring-navy/10">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-1.5 font-cairo">تاريخ العودة المتوقع</label>
                            <input type="date" name="expected_return_date" class="w-full px-3 py-2.5 rounded-xl border border-gray-200 bg-gray-50 text-sm   outline-none focus:ring-2 focus:ring-navy/10">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-1.5 font-cairo">وقت العودة المتوقع</label>
                            <input type="time" name="expected_return_time" class="w-full px-3 py-2.5 rounded-xl border border-gray-200 bg-gray-50 text-sm   outline-none focus:ring-2 focus:ring-navy/10">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1.5 font-cairo">السبب <span class="text-red-500">*</span></label>
                        <textarea name="reason" rows="3" placeholder="سبب الاستئذان..." class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-navy text-sm font-almarai outline-none focus:ring-2 focus:ring-navy/10 resize-none"></textarea>
                    </div>
                    <button type="submit" class="w-full bg-navy text-white py-3 rounded-xl font-black font-cairo hover:bg-[#083358] transition-all flex items-center justify-center gap-2 shadow-lg shadow-navy/15">
                        <i class="fas fa-save"></i> تسجيل الاستئذان
                    </button>
                </form>
            </div>
        </div>

        {{-- List --}}
        <div class="xl:col-span-2 space-y-4">
            {{-- Leave Cutoff Time Setting --}}
            @if(auth()->user()->center_id)
            <div class="bg-indigo-50 border border-indigo-100 rounded-2xl p-4 mb-4 flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center text-indigo-600">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div>
                        <h3 class="font-black text-indigo-900 font-cairo">وقت المنع اليومي للاستئذان</h3>
                        <p class="text-xs text-indigo-700 font-almarai mt-0.5">لن يتمكن الطلاب من إرسال طلب استئذان بعد هذا الوقت المخصص للمركز.</p>
                    </div>
                </div>
                <form action="{{ route('leaves.cutoff') }}" method="POST" class="flex items-center gap-2">
                    @csrf
                    <input type="time" name="leave_cutoff_time" value="{{ ($center && $center->leave_cutoff_time) ? \Carbon\Carbon::parse($center->leave_cutoff_time)->format('H:i') : '' }}" class="px-3 py-2 rounded-xl border border-indigo-200 bg-white text-sm   outline-none focus:ring-2 focus:ring-indigo/20">
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-xl font-bold font-cairo text-sm hover:bg-indigo-700 transition-all shadow-sm">
                        حفظ الوقت
                    </button>
                    @if($center && $center->leave_cutoff_time)
                    <button type="submit" name="clear" value="1" class="px-3 py-2 bg-white text-red-500 border border-red-100 rounded-xl text-sm hover:bg-red-50 transition-all" title="إلغاء المنع">
                        <i class="fas fa-times"></i>
                    </button>
                    @endif
                </form>
            </div>
            @endif

            {{-- Filters --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                <form method="GET" action="{{ route('administrative.index') }}" class="grid grid-cols-2 md:grid-cols-5 gap-3 items-end">
                    <input type="hidden" name="tab" value="leaves">
                    <div>
                        <label class="block text-xs font-bold text-gray-400 mb-1 font-cairo">الطالب</label>
                        <select name="l_student_id" class="w-full px-3 py-2 rounded-xl border border-gray-100 bg-gray-50 text-sm font-almarai outline-none">
                            <option value="">الكل</option>
                            @foreach($students as $s)
                            <option value="{{ $s->id }}" {{ request('l_student_id') == $s->id ? 'selected' : '' }}>{{ $s->name_ar }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-400 mb-1 font-cairo">النوع</label>
                        <select name="l_type" class="w-full px-3 py-2 rounded-xl border border-gray-100 bg-gray-50 text-sm font-almarai outline-none">
                            <option value="">الكل</option>
                            <option value="temporary" {{ request('l_type') === 'temporary' ? 'selected' : '' }}>مؤقت</option>
                            <option value="vacation" {{ request('l_type') === 'vacation' ? 'selected' : '' }}>إجازة</option>
                            <option value="medical" {{ request('l_type') === 'medical' ? 'selected' : '' }}>طبي</option>
                            <option value="lateness" {{ request('l_type') === 'lateness' ? 'selected' : '' }}>تأخير</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-400 mb-1 font-cairo">الحالة</label>
                        <select name="l_status" class="w-full px-3 py-2 rounded-xl border border-gray-100 bg-gray-50 text-sm font-almarai outline-none">
                            <option value="">الكل</option>
                            <option value="pending" {{ request('l_status') === 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                            <option value="approved" {{ request('l_status') === 'approved' ? 'selected' : '' }}>موافق</option>
                            <option value="rejected" {{ request('l_status') === 'rejected' ? 'selected' : '' }}>مرفوض</option>
                            <option value="returned" {{ request('l_status') === 'returned' ? 'selected' : '' }}>عاد</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-400 mb-1 font-cairo">من تاريخ</label>
                        <input type="date" name="l_date_from" value="{{ request('l_date_from') }}" class="w-full px-3 py-2 rounded-xl border border-gray-100 bg-gray-50 text-sm outline-none">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 bg-navy text-white py-2 rounded-xl font-bold font-cairo text-sm hover:bg-navy/90 transition-all flex items-center justify-center gap-1">
                            <i class="fas fa-filter text-xs"></i> فلترة
                        </button>
                        <a href="{{ route('administrative.index', ['tab' => 'leaves']) }}" class="w-10 h-10 bg-gray-100 text-gray-400 rounded-xl flex items-center justify-center hover:bg-red-50 hover:text-red-500 transition-all">
                            <i class="fas fa-times text-xs"></i>
                        </a>
                    </div>
                </form>
            </div>

            {{-- Actions Bar --}}
            <div class="flex justify-between items-center px-1">
                <span class="text-sm text-gray-400 font-cairo">إجمالي: <strong class="text-navy">{{ $leaves->total() }}</strong></span>
                <div class="flex gap-2">
                    <a href="{{ route('leaves.export-list', ['student_id' => request('l_student_id'), 'date_from' => request('l_date_from'), 'date_to' => request('l_date_to'), 'type' => request('l_type'), 'status' => request('l_status')]) }}" target="_blank"
                       class="px-4 py-2 bg-gray-100 text-gray-600 rounded-xl text-sm font-bold font-cairo hover:bg-navy hover:text-white transition-all flex items-center gap-2">
                        <i class="fas fa-print"></i> طباعة القائمة
                    </a>
                </div>
            </div>

            {{-- Table --}}
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-right text-sm">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100">
                                <th class="px-4 py-4 text-xs font-black text-gray-400 font-cairo">الطالب</th>
                                <th class="px-4 py-4 text-xs font-black text-gray-400 font-cairo text-center">النوع</th>
                                <th class="px-4 py-4 text-xs font-black text-gray-400 font-cairo text-center">تاريخ / وقت المغادرة</th>
                                <th class="px-4 py-4 text-xs font-black text-gray-400 font-cairo text-center">الحالة</th>
                                <th class="px-4 py-4 text-xs font-black text-gray-400 font-cairo text-center">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($leaves as $l)
                            <tr class="hover:bg-gray-50/60 transition-colors {{ $l->status === 'pending' ? 'bg-amber-50/30' : '' }}">
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 bg-navy/5 rounded-lg flex items-center justify-center text-navy font-bold text-xs">{{ mb_substr($l->student->name_ar ?? '?', 0, 1) }}</div>
                                        <div>
                                            <p class="font-bold text-navy font-almarai text-sm">{{ $l->student->name_ar ?? '---' }}</p>
                                            @if($l->submitted_by_student)<span class="text-[10px] text-blue-500 font-cairo">طلب الطالب</span>@endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    @php
                                        $typeColors = ['temporary'=>'bg-blue-50 text-blue-600','vacation'=>'bg-purple-50 text-purple-600','medical'=>'bg-green-50 text-green-600','lateness'=>'bg-orange-50 text-orange-600'];
                                        $typeLabels = ['temporary'=>'مؤقت','vacation'=>'إجازة','medical'=>'طبي','lateness'=>'تأخير'];
                                    @endphp
                                    <span class="px-2 py-1 rounded-lg text-xs font-black {{ $typeColors[$l->type] ?? 'bg-gray-100 text-gray-600' }}">{{ $typeLabels[$l->type] ?? $l->type }}</span>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <span class="text-xs   text-gray-600">{{ $l->departure_date->format('Y-m-d') }}</span>
                                    @if($l->departure_time)
                                    <span class="block text-[10px] text-gray-400  ">{{ $l->departure_time }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-center">
                                    @php
                                        $stColors = ['pending'=>'bg-amber-50 text-amber-600','approved'=>'bg-green-50 text-green-600','rejected'=>'bg-red-50 text-red-600','returned'=>'bg-blue-50 text-blue-600','not_returned'=>'bg-gray-100 text-gray-500'];
                                        $stLabels = ['pending'=>'قيد الانتظار','approved'=>'موافق','rejected'=>'مرفوض','returned'=>'عاد','not_returned'=>'لم يعد'];
                                    @endphp
                                    <span class="px-2 py-1 rounded-lg text-xs font-black {{ $stColors[$l->status] ?? 'bg-gray-100 text-gray-600' }}">{{ $stLabels[$l->status] ?? $l->status }}</span>
                                    @if($l->converted_to_violation)<span class="block text-[10px] text-red-500 mt-0.5 font-cairo">← مخالفة</span>@endif
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center justify-center gap-1 flex-wrap">
                                        {{-- Approve (if pending) --}}
                                        @if($l->status === 'pending')
                                        <form action="{{ route('leaves.approve', $l->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="px-2 py-1 bg-green-50 text-green-600 rounded-lg text-xs font-bold font-cairo hover:bg-green-500 hover:text-white transition-all" title="قبول">
                                                <i class="fas fa-check text-xs"></i> قبول
                                            </button>
                                        </form>
                                        {{-- Reject --}}
                                        <form action="{{ route('leaves.reject', $l->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="px-2 py-1 bg-red-50 text-red-500 rounded-lg text-xs font-bold font-cairo hover:bg-red-500 hover:text-white transition-all" title="رفض">
                                                <i class="fas fa-times text-xs"></i> رفض
                                            </button>
                                        </form>
                                        @endif
                                        {{-- Mark returned (if approved) --}}
                                        @if($l->status === 'approved')
                                        <form action="{{ route('leaves.return', $l->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="px-2 py-1 bg-blue-50 text-blue-600 rounded-lg text-xs font-bold font-cairo hover:bg-blue-500 hover:text-white transition-all">
                                                <i class="fas fa-undo text-xs"></i> عاد
                                            </button>
                                        </form>
                                        @endif
                                        {{-- Convert to violation (if rejected/pending) --}}
                                        @if(in_array($l->status, ['pending','rejected']) && !$l->converted_to_violation)
                                        <button onclick="document.getElementById('conv-modal-{{ $l->id }}').classList.remove('hidden')"
                                            class="px-2 py-1 bg-red-100 text-red-700 rounded-lg text-xs font-bold font-cairo hover:bg-red-700 hover:text-white transition-all">
                                            <i class="fas fa-gavel text-xs"></i> مخالفة
                                        </button>
                                        @endif
                                        {{-- Edit / Delete --}}
                                        <a href="{{ route('leaves.edit', $l->id) }}" class="w-7 h-7 bg-gray-50 text-gray-400 rounded-lg flex items-center justify-center hover:bg-blue-50 hover:text-blue-500 transition-all">
                                            <i class="fas fa-edit text-xs"></i>
                                        </a>
                                        <form action="{{ route('leaves.destroy', $l->id) }}" method="POST" onsubmit="return confirm('حذف هذا الاستئذان؟')">
                                            @csrf
                                            <button type="submit" class="w-7 h-7 bg-red-50 text-red-500 rounded-lg flex items-center justify-center hover:bg-red-500 hover:text-white transition-all">
                                                <i class="fas fa-trash-alt text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            {{-- Convert to Violation Modal --}}
                            @if(in_array($l->status, ['pending','rejected']) && !$l->converted_to_violation)
                            <tr id="conv-modal-{{ $l->id }}" class="hidden">
                                <td colspan="5" class="bg-red-50 px-6 py-4 border border-red-100">
                                    <form action="{{ route('leaves.convert-violation', $l->id) }}" method="POST" class="space-y-3">
                                        @csrf
                                        <p class="font-black text-red-700 font-cairo text-sm mb-2">
                                            <i class="fas fa-exclamation-triangle ml-1"></i>
                                            تحويل استئذان "{{ $l->student->name_ar }}" إلى مخالفة
                                        </p>
                                        <div class="grid grid-cols-3 gap-3">
                                            <div>
                                                <label class="text-xs font-bold text-gray-500 mb-1 block font-cairo">نوع المخالفة <span class="text-red-500">*</span></label>
                                                <input type="text" name="type" value="غياب بدون إذن" class="w-full px-3 py-2 rounded-xl border border-gray-200 text-sm outline-none">
                                            </div>
                                            <div>
                                                <label class="text-xs font-bold text-gray-500 mb-1 block font-cairo">المستوى <span class="text-red-500">*</span></label>
                                                <select name="severity" class="w-full px-3 py-2 rounded-xl border border-gray-200 text-sm outline-none">
                                                    <option value="minor">بسيطة</option>
                                                    <option value="moderate">متوسطة</option>
                                                    <option value="severe">جسيمة</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="text-xs font-bold text-gray-500 mb-1 block font-cairo">الوصف <span class="text-red-500">*</span></label>
                                                <input type="text" name="description" value="خروج بدون إذن رسمي" class="w-full px-3 py-2 rounded-xl border border-gray-200 text-sm outline-none">
                                            </div>
                                        </div>
                                        <div class="flex gap-3">
                                            <button type="submit" class="px-5 py-2 bg-red-600 text-white rounded-xl font-bold font-cairo text-sm hover:bg-red-700 transition-all">
                                                <i class="fas fa-gavel ml-1"></i> تحويل وتسجيل مخالفة
                                            </button>
                                            <button type="button" onclick="document.getElementById('conv-modal-{{ $l->id }}').classList.add('hidden')"
                                                class="px-5 py-2 bg-gray-100 text-gray-600 rounded-xl font-bold font-cairo text-sm hover:bg-gray-200 transition-all">
                                                إلغاء
                                            </button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                            @endif

                            @empty
                            <tr>
                                <td colspan="5" class="px-5 py-16 text-center">
                                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3 opacity-30">
                                        <i class="fas fa-door-open text-3xl text-gray-400"></i>
                                    </div>
                                    <p class="text-navy font-black font-cairo">لا توجد سجلات استئذان</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($leaves->hasPages())
                <div class="px-5 py-4 border-t border-gray-50">{{ $leaves->appends(request()->except('l_page'))->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</div>
