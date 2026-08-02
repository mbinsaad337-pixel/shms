{{-- ABSENCES TAB --}}
<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
    {{-- Form --}}
    <div class="xl:col-span-1">
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-black text-navy font-cairo mb-5 flex items-center gap-2">
                <i class="fas fa-user-times text-navy/60"></i> تسجيل غياب
            </h2>
            <form action="{{ route('absences.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5 font-cairo">الطالب (يمكن اختيار أكثر من طالب) <span class="text-red-500">*</span></label>
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
                    <label class="block text-xs font-bold text-gray-500 mb-1.5 font-cairo">تاريخ الغياب <span class="text-red-500">*</span></label>
                    <input type="date" name="date" value="{{ date('Y-m-d') }}" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-sm   outline-none focus:ring-2 focus:ring-navy/10">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5 font-cairo">نوع الغياب</label>
                    <select name="absence_type" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-navy text-sm font-almarai outline-none focus:ring-2 focus:ring-navy/10">
                        <option value="">-- اختر النوع --</option>
                        <option value="housing">غياب سكن</option>
                        <option value="quran">غياب حلقة قرآنية</option>
                        <option value="activity">غياب نشاط</option>
                        <option value="other">غياب آخر</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5 font-cairo">هل لديه عذر؟ <span class="text-red-500">*</span></label>
                    <div class="flex gap-4">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="has_excuse" value="0" checked class="accent-navy">
                            <span class="text-sm font-cairo text-gray-600">لا</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="has_excuse" value="1" class="accent-green-500">
                            <span class="text-sm font-cairo text-gray-600">نعم</span>
                        </label>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5 font-cairo">نوع العذر (إن وجد)</label>
                    <input type="text" name="excuse_type" placeholder="مثال: مرض، ظرف طارئ..." class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-navy text-sm font-almarai outline-none focus:ring-2 focus:ring-navy/10">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5 font-cairo">ملاحظات</label>
                    <textarea name="notes" rows="3" placeholder="ملاحظات إضافية..." class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-navy text-sm font-almarai outline-none focus:ring-2 focus:ring-navy/10 resize-none"></textarea>
                </div>
                <button type="submit" class="w-full bg-navy text-white py-3 rounded-xl font-black font-cairo hover:bg-[#083358] transition-all flex items-center justify-center gap-2 shadow-lg shadow-navy/15">
                    <i class="fas fa-save"></i> تسجيل الغياب
                </button>
            </form>
        </div>
    </div>

    {{-- List --}}
    <div class="xl:col-span-2 space-y-4">
        {{-- Filters --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
            <form method="GET" action="{{ route('administrative.index') }}" class="grid grid-cols-2 md:grid-cols-4 gap-3 items-end">
                <input type="hidden" name="tab" value="absences">
                <div>
                    <label class="block text-xs font-bold text-gray-400 mb-1 font-cairo">الطالب</label>
                    <select name="a_student_id" class="w-full px-3 py-2.5 rounded-xl border border-gray-100 bg-gray-50 text-sm font-almarai outline-none">
                        <option value="">الكل</option>
                        @foreach($students as $s)
                        <option value="{{ $s->id }}" {{ request('a_student_id') == $s->id ? 'selected' : '' }}>{{ $s->name_ar }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 mb-1 font-cairo">من تاريخ</label>
                    <input type="date" name="a_date_from" value="{{ request('a_date_from') }}" class="w-full px-3 py-2.5 rounded-xl border border-gray-100 bg-gray-50 text-sm outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 mb-1 font-cairo">إلى تاريخ</label>
                    <input type="date" name="a_date_to" value="{{ request('a_date_to') }}" class="w-full px-3 py-2.5 rounded-xl border border-gray-100 bg-gray-50 text-sm outline-none">
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-navy text-white py-2.5 rounded-xl font-bold font-cairo text-sm hover:bg-navy/90 transition-all flex items-center justify-center gap-1">
                        <i class="fas fa-filter text-xs"></i> فلترة
                    </button>
                    <a href="{{ route('administrative.index', ['tab' => 'absences']) }}" class="w-10 h-10 bg-gray-100 text-gray-400 rounded-xl flex items-center justify-center hover:bg-red-50 hover:text-red-500 transition-all">
                        <i class="fas fa-times text-xs"></i>
                    </a>
                </div>
            </form>
        </div>
        {{-- Actions Bar --}}
        <div class="flex justify-between items-center px-1">
            <span class="text-sm text-gray-400 font-cairo">إجمالي: <strong class="text-navy">{{ $absences->total() }}</strong></span>
            <a href="{{ route('absences.export-list', ['student_id' => request('a_student_id'), 'date_from' => request('a_date_from'), 'date_to' => request('a_date_to'), 'has_excuse' => request('a_has_excuse'), 'type' => request('a_type')]) }}" target="_blank"
               class="px-4 py-2 bg-gray-100 text-gray-600 rounded-xl text-sm font-bold font-cairo hover:bg-navy hover:text-white transition-all flex items-center gap-2">
                <i class="fas fa-print"></i> طباعة القائمة
            </a>
        </div>
        {{-- Table --}}
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-right text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="px-5 py-4 text-xs font-black text-gray-400 font-cairo">الطالب</th>
                            <th class="px-5 py-4 text-xs font-black text-gray-400 font-cairo text-center">التاريخ</th>
                            <th class="px-5 py-4 text-xs font-black text-gray-400 font-cairo text-center">النوع</th>
                            <th class="px-5 py-4 text-xs font-black text-gray-400 font-cairo text-center">العذر</th>
                            <th class="px-5 py-4 text-xs font-black text-gray-400 font-cairo">الملاحظات</th>
                            <th class="px-5 py-4 text-xs font-black text-gray-400 font-cairo text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($absences as $a)
                        <tr class="hover:bg-gray-50/60 transition-colors">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-navy/5 rounded-lg flex items-center justify-center text-navy font-bold text-xs">{{ mb_substr($a->student->name_ar ?? '?', 0, 1) }}</div>
                                    <div>
                                        <p class="font-bold text-navy font-almarai text-sm">{{ $a->student->name_ar ?? '---' }}</p>
                                        <p class="text-[10px] text-gray-400  ">{{ $a->student->student_number ?? '' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-center"><span class="text-xs   text-gray-500">{{ $a->date->format('Y-m-d') }}</span></td>
                            <td class="px-5 py-4 text-center">
                                @php $typeLabels = ['housing'=>'سكن','quran'=>'حلقة قرآنية','activity'=>'نشاط','other'=>'آخر']; @endphp
                                <span class="px-2.5 py-1 rounded-lg text-xs font-black bg-gray-100 text-gray-600">{{ $typeLabels[$a->absence_type] ?? ($a->absence_type ?? 'عام') }}</span>
                            </td>
                            <td class="px-5 py-4 text-center">
                                @if($a->has_excuse)
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-black bg-green-50 text-green-600">معذور</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-black bg-red-50 text-red-600">غير معذور</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-xs text-gray-400 max-w-xs">
                                <span class="line-clamp-1">{{ $a->notes ?? '---' }}</span>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('absences.edit', $a->id) }}"
                                       class="w-8 h-8 bg-blue-50 text-blue-500 rounded-lg flex items-center justify-center hover:bg-blue-500 hover:text-white transition-all" title="تعديل">
                                        <i class="fas fa-edit text-xs"></i>
                                    </a>
                                    <form action="{{ route('absences.destroy', $a->id) }}" method="POST"
                                          onsubmit="return confirm('هل أنت متأكد من حذف هذا السجل؟')">
                                        @csrf
                                        <button type="submit" class="w-8 h-8 bg-red-50 text-red-500 rounded-lg flex items-center justify-center hover:bg-red-500 hover:text-white transition-all">
                                            <i class="fas fa-trash-alt text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-5 py-16 text-center">
                                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3 opacity-30">
                                    <i class="fas fa-user-check text-3xl text-gray-400"></i>
                                </div>
                                <p class="text-navy font-black font-cairo">لا توجد سجلات غياب</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($absences->hasPages())
            <div class="px-5 py-4 border-t border-gray-50">{{ $absences->appends(request()->except('a_page'))->links() }}</div>
            @endif
        </div>
    </div>
</div>
