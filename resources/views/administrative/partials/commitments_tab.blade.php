{{-- COMMITMENTS TAB --}}
<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
    {{-- Form --}}
    <div class="xl:col-span-1">
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-black text-navy font-cairo mb-5 flex items-center gap-2">
                <i class="fas fa-plus-circle text-navy/60"></i> تسجيل تعهد جديد
            </h2>
            <form action="{{ route('commitments.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5 font-cairo">الطالب <span class="text-red-500">*</span></label>
                    <select name="student_id" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-navy text-sm font-almarai outline-none focus:ring-2 focus:ring-navy/10">
                        <option value="">-- اختر الطالب --</option>
                        @foreach($students as $s)
                        <option value="{{ $s->id }}">{{ $s->name_ar }} ({{ $s->student_number }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5 font-cairo">عنوان التعهد</label>
                    <input type="text" name="title" placeholder="مثال: تعهد بالتزام النظام" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-navy text-sm font-almarai outline-none focus:ring-2 focus:ring-navy/10">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5 font-cairo">نص التعهد <span class="text-red-500">*</span></label>
                    <textarea name="text" rows="4" placeholder="أتعهد أنا الطالب..." class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-navy text-sm font-almarai outline-none focus:ring-2 focus:ring-navy/10 resize-none"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5 font-cairo">التاريخ <span class="text-red-500">*</span></label>
                    <input type="date" name="date" value="{{ date('Y-m-d') }}" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-sm   outline-none focus:ring-2 focus:ring-navy/10">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5 font-cairo">مخالفة مرتبطة (اختياري)</label>
                    <select name="violation_id" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-navy text-sm font-almarai outline-none focus:ring-2 focus:ring-navy/10">
                        <option value="">-- لا توجد مخالفة مرتبطة --</option>
                        @foreach($violationsForForm as $v)
                        <option value="{{ $v->id }}">{{ $v->type }} – {{ $v->student->name_ar ?? '' }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-center gap-3 bg-amber-50 rounded-xl p-3 border border-amber-100">
                    <input type="checkbox" name="requires_guardian_signature" id="guardian_sig" value="1" class="w-4 h-4 accent-amber-500">
                    <label for="guardian_sig" class="text-sm font-bold text-amber-700 font-cairo">يتطلب توقيع ولي الأمر</label>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5 font-cairo">
                        <i class="fas fa-paperclip ml-1"></i> إرفاق صورة (اختياري)
                    </label>
                    <input type="file" name="image" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-navy/5 file:text-navy file:font-bold hover:file:bg-navy/10 cursor-pointer">
                </div>
                <button type="submit" class="w-full bg-navy text-white py-3 rounded-xl font-black font-cairo hover:bg-[#083358] transition-all flex items-center justify-center gap-2 shadow-lg shadow-navy/15">
                    <i class="fas fa-save"></i> حفظ التعهد
                </button>
            </form>
        </div>
    </div>

    {{-- List --}}
    <div class="xl:col-span-2 space-y-4">
        {{-- Filters --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
            <form method="GET" action="{{ route('administrative.index') }}" class="grid grid-cols-2 md:grid-cols-4 gap-3 items-end">
                <input type="hidden" name="tab" value="commitments">
                <div>
                    <label class="block text-xs font-bold text-gray-400 mb-1 font-cairo">الطالب</label>
                    <select name="c_student_id" class="w-full px-3 py-2.5 rounded-xl border border-gray-100 bg-gray-50 text-sm font-almarai outline-none">
                        <option value="">الكل</option>
                        @foreach($students as $s)
                        <option value="{{ $s->id }}" {{ request('c_student_id') == $s->id ? 'selected' : '' }}>{{ $s->name_ar }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 mb-1 font-cairo">من تاريخ</label>
                    <input type="date" name="c_date_from" value="{{ request('c_date_from') }}" class="w-full px-3 py-2.5 rounded-xl border border-gray-100 bg-gray-50 text-sm outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 mb-1 font-cairo">إلى تاريخ</label>
                    <input type="date" name="c_date_to" value="{{ request('c_date_to') }}" class="w-full px-3 py-2.5 rounded-xl border border-gray-100 bg-gray-50 text-sm outline-none">
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-navy text-white py-2.5 rounded-xl font-bold font-cairo text-sm hover:bg-navy/90 transition-all flex items-center justify-center gap-1">
                        <i class="fas fa-filter text-xs"></i> فلترة
                    </button>
                    <a href="{{ route('administrative.index', ['tab' => 'commitments']) }}" class="w-10 h-10 bg-gray-100 text-gray-400 rounded-xl flex items-center justify-center hover:bg-red-50 hover:text-red-500 transition-all">
                        <i class="fas fa-times text-xs"></i>
                    </a>
                </div>
            </form>
        </div>
        {{-- Actions Bar --}}
        <div class="flex justify-between items-center px-1">
            <span class="text-sm text-gray-400 font-cairo">إجمالي: <strong class="text-navy">{{ $commitments->total() }}</strong></span>
            <a href="{{ route('commitments.export-list', ['student_id' => request('c_student_id'), 'date_from' => request('c_date_from'), 'date_to' => request('c_date_to'), 'status' => request('c_status')]) }}" target="_blank"
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
                            <th class="px-5 py-4 text-xs font-black text-gray-400 font-cairo">العنوان / النص</th>
                            <th class="px-5 py-4 text-xs font-black text-gray-400 font-cairo text-center">التاريخ</th>
                            <th class="px-5 py-4 text-xs font-black text-gray-400 font-cairo text-center">الحالة</th>
                            <th class="px-5 py-4 text-xs font-black text-gray-400 font-cairo text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($commitments as $c)
                        <tr class="hover:bg-gray-50/60 transition-colors">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-navy/5 rounded-lg flex items-center justify-center text-navy font-bold text-xs">{{ mb_substr($c->student->name_ar ?? '?', 0, 1) }}</div>
                                    <div>
                                        <p class="font-bold text-navy font-almarai text-sm">{{ $c->student->name_ar ?? '---' }}</p>
                                        <p class="text-[10px] text-gray-400  ">{{ $c->student->student_number ?? '' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <p class="font-bold text-gray-700 font-cairo text-sm">{{ $c->title ?? 'تعهد' }}</p>
                                <p class="text-xs text-gray-400 mt-0.5 line-clamp-1">{{ Str::limit($c->text, 60) }}</p>
                                @if($c->image_path)
                                <span class="inline-flex items-center gap-1 text-[10px] text-blue-500 mt-1"><i class="fas fa-image"></i> يحتوي على صورة</span>
                                @endif
                                @if($c->requires_guardian_signature)
                                <span class="inline-flex items-center gap-1 text-[10px] text-amber-600 mt-1 mr-2"><i class="fas fa-signature"></i> يتطلب توقيع ولي أمر</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-center"><span class="text-xs   text-gray-500">{{ $c->date->format('Y-m-d') }}</span></td>
                            <td class="px-5 py-4 text-center">
                                <span class="px-2.5 py-1 rounded-lg text-xs font-black {{ $c->status === 'active' ? 'bg-green-50 text-green-600' : 'bg-gray-100 text-gray-500' }}">
                                    {{ $c->status === 'active' ? 'نشط' : 'منتهي' }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('commitments.export', $c->id) }}" target="_blank"
                                       class="w-8 h-8 bg-gray-50 text-gray-400 rounded-lg flex items-center justify-center hover:bg-navy hover:text-white transition-all" title="طباعة">
                                        <i class="fas fa-print text-xs"></i>
                                    </a>
                                    <a href="{{ route('commitments.edit', $c->id) }}"
                                       class="w-8 h-8 bg-blue-50 text-blue-500 rounded-lg flex items-center justify-center hover:bg-blue-500 hover:text-white transition-all" title="تعديل">
                                        <i class="fas fa-edit text-xs"></i>
                                    </a>
                                    <form action="{{ route('commitments.destroy', $c->id) }}" method="POST"
                                          onsubmit="return confirm('هل أنت متأكد من حذف هذا التعهد؟')">
                                        @csrf
                                        <button type="submit" class="w-8 h-8 bg-red-50 text-red-500 rounded-lg flex items-center justify-center hover:bg-red-500 hover:text-white transition-all" title="حذف">
                                            <i class="fas fa-trash-alt text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-5 py-16 text-center">
                                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3 opacity-30">
                                    <i class="fas fa-file-signature text-3xl text-gray-400"></i>
                                </div>
                                <p class="text-navy font-black font-cairo">لا توجد تعهدات مسجلة</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($commitments->hasPages())
            <div class="px-5 py-4 border-t border-gray-50">{{ $commitments->appends(request()->except('c_page'))->links() }}</div>
            @endif
        </div>
    </div>
</div>
