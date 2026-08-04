{{-- VIOLATIONS TAB --}}
<div class="space-y-4">
    {{-- Filters --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
        <form method="GET" action="{{ route('administrative.index') }}" class="grid grid-cols-2 md:grid-cols-5 gap-3 items-end">
            <input type="hidden" name="tab" value="violations">
            <div>
                <label class="block text-xs font-bold text-gray-400 mb-1 font-cairo">الطالب</label>
                <select name="v_student_id" class="w-full px-3 py-2 rounded-xl border border-gray-100 bg-gray-50 text-sm font-almarai outline-none">
                    <option value="">الكل</option>
                    @foreach($students as $s)
                    <option value="{{ $s->id }}" {{ request('v_student_id') == $s->id ? 'selected' : '' }}>{{ $s->name_ar }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-400 mb-1 font-cairo">نوع المخالفة</label>
                <select name="v_type" class="w-full px-3 py-2 rounded-xl border border-gray-100 bg-gray-50 text-sm font-almarai outline-none">
                    <option value="">الكل</option>
                    @foreach($violationTypes as $t)
                    <option value="{{ $t }}" {{ request('v_type') === $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-400 mb-1 font-cairo">من تاريخ</label>
                <input type="date" name="v_date_from" value="{{ request('v_date_from') }}" class="w-full px-3 py-2 rounded-xl border border-gray-100 bg-gray-50 text-sm outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-400 mb-1 font-cairo">إلى تاريخ</label>
                <input type="date" name="v_date_to" value="{{ request('v_date_to') }}" class="w-full px-3 py-2 rounded-xl border border-gray-100 bg-gray-50 text-sm outline-none">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-navy text-white py-2 rounded-xl font-bold font-cairo text-sm hover:bg-navy/90 transition-all flex items-center justify-center gap-1">
                    <i class="fas fa-filter text-xs"></i> فلترة
                </button>
                <a href="{{ route('administrative.index', ['tab' => 'violations']) }}" class="w-10 h-10 bg-gray-100 text-gray-400 rounded-xl flex items-center justify-center hover:bg-red-50 hover:text-red-500 transition-all">
                    <i class="fas fa-times text-xs"></i>
                </a>
            </div>
        </form>
    </div>

    {{-- Header / Actions --}}
    <div class="flex flex-wrap justify-between items-center px-1 gap-3">
        <span class="text-sm text-gray-400 font-cairo">إجمالي: <strong class="text-navy">{{ $violations->total() }}</strong></span>
        <div class="flex gap-2">
            <a href="{{ route('violations.export-list', ['student_id' => request('v_student_id'), 'date_from' => request('v_date_from'), 'date_to' => request('v_date_to'), 'type' => request('v_type')]) }}" target="_blank"
               class="px-4 py-2 bg-gray-100 text-gray-600 rounded-xl text-sm font-bold font-cairo hover:bg-navy hover:text-white transition-all flex items-center gap-2">
                <i class="fas fa-print"></i> طباعة القائمة
            </a>
            <a href="{{ route('violations.create') }}"
               class="px-4 py-2 bg-red-600 text-white rounded-xl text-sm font-bold font-cairo hover:bg-red-700 transition-all flex items-center gap-2 shadow-sm shadow-red-600/20">
                <i class="fas fa-plus-circle"></i> تسجيل مخالفة
            </a>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-5 py-4 text-xs font-black text-gray-400 font-cairo">الطالب</th>
                        <th class="px-5 py-4 text-xs font-black text-gray-400 font-cairo">نوع المخالفة</th>
                        <th class="px-5 py-4 text-xs font-black text-gray-400 font-cairo text-center">المستوى</th>
                        <th class="px-5 py-4 text-xs font-black text-gray-400 font-cairo text-center">التاريخ</th>
                        <th class="px-5 py-4 text-xs font-black text-gray-400 font-cairo text-center">بواسطة</th>
                        <th class="px-5 py-4 text-xs font-black text-gray-400 font-cairo text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($violations as $v)
                    <tr class="hover:bg-gray-50/60 transition-colors">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-red-50 rounded-lg flex items-center justify-center text-red-600 font-bold text-xs">{{ mb_substr($v->student->name_ar ?? '?', 0, 1) }}</div>
                                <div>
                                    <p class="font-bold text-navy font-almarai text-sm">{{ $v->student->name_ar ?? '---' }}</p>
                                    <p class="text-[10px] text-gray-400  ">{{ $v->student->student_number ?? '' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <span class="font-bold text-gray-600 font-cairo text-sm">{{ $v->type }}</span>
                            <p class="text-[10px] text-gray-400 mt-0.5 line-clamp-1">{{ Str::limit($v->description, 60) }}</p>
                        </td>
                        <td class="px-5 py-4 text-center">
                            @php
                                $sev = ['minor'=>['bg-blue-50 text-blue-600','بسيطة'],'moderate'=>['bg-orange-50 text-orange-600','متوسطة'],'severe'=>['bg-red-50 text-red-600','جسيمة']];
                            @endphp
                            <span class="px-2.5 py-1 rounded-lg text-xs font-black {{ $sev[$v->severity][0] ?? 'bg-gray-100 text-gray-600' }}">{{ $sev[$v->severity][1] ?? $v->severity }}</span>
                        </td>
                        <td class="px-5 py-4 text-center"><span class="text-xs   text-gray-500">{{ $v->violation_date->format('Y-m-d') }}</span></td>
                        <td class="px-5 py-4 text-center"><span class="text-xs font-bold text-navy font-cairo">{{ $v->recordedBy->name ?? '---' }}</span></td>
                        <td class="px-5 py-4 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="{{ route('violations.export', $v->id) }}" target="_blank"
                                   class="w-8 h-8 bg-gray-50 text-gray-400 rounded-lg flex items-center justify-center hover:bg-navy hover:text-white transition-all" title="طباعة">
                                    <i class="fas fa-print text-xs"></i>
                                </a>
                                <a href="{{ route('violations.show', $v->id) }}"
                                   class="w-8 h-8 bg-navy/5 text-navy rounded-lg flex items-center justify-center hover:bg-navy hover:text-white transition-all">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                                @can('manage-violations')
                                <form action="{{ route('violations.destroy', $v->id) }}" method="POST"
                                      data-confirm="حذف هذه المخالفة؟">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-8 h-8 bg-red-50 text-red-500 rounded-lg flex items-center justify-center hover:bg-red-500 hover:text-white transition-all">
                                        <i class="fas fa-trash-alt text-xs"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-16 text-center">
                            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3 opacity-30">
                                <i class="fas fa-shield-check text-3xl text-gray-400"></i>
                            </div>
                            <p class="text-navy font-black font-cairo">لا توجد مخالفات مسجلة</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($violations->hasPages())
        <div class="px-5 py-4 border-t border-gray-50">{{ $violations->appends(request()->except('v_page'))->links() }}</div>
        @endif
    </div>
</div>
