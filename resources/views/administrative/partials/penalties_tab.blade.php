{{-- PENALTIES TAB --}}
<div class="space-y-4">
   

    {{-- Filters --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
        <form method="GET" action="{{ route('administrative.index') }}" class="grid grid-cols-2 md:grid-cols-4 gap-3 items-end">
            <input type="hidden" name="tab" value="penalties">
            <div>
                <label class="block text-xs font-bold text-gray-400 mb-1 font-cairo">الطالب</label>
                <select name="p_student_id" class="w-full px-3 py-2 rounded-xl border border-gray-100 bg-gray-50 text-sm font-almarai outline-none">
                    <option value="">الكل</option>
                    @foreach($students as $s)
                    <option value="{{ $s->id }}" {{ request('p_student_id') == $s->id ? 'selected' : '' }}>{{ $s->name_ar }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-400 mb-1 font-cairo">من تاريخ</label>
                <input type="date" name="p_date_from" value="{{ request('p_date_from') }}" class="w-full px-3 py-2 rounded-xl border border-gray-100 bg-gray-50 text-sm outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-400 mb-1 font-cairo">إلى تاريخ</label>
                <input type="date" name="p_date_to" value="{{ request('p_date_to') }}" class="w-full px-3 py-2 rounded-xl border border-gray-100 bg-gray-50 text-sm outline-none">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-navy text-white py-2 rounded-xl font-bold font-cairo text-sm hover:bg-navy/90 transition-all flex items-center justify-center gap-1">
                    <i class="fas fa-filter text-xs"></i> فلترة
                </button>
                <a href="{{ route('administrative.index', ['tab' => 'penalties']) }}" class="w-10 h-10 bg-gray-100 text-gray-400 rounded-xl flex items-center justify-center hover:bg-red-50 hover:text-red-500 transition-all">
                    <i class="fas fa-times text-xs"></i>
                </a>
            </div>
        </form>
    </div>
     {{-- Header / Actions --}}
    <div class="flex flex-wrap justify-between items-center px-1 gap-3">
        <span class="text-sm text-gray-400 font-cairo">إجمالي: <strong class="text-navy">{{ $penalties->total() }}</strong></span>
        <div class="flex gap-2">
            <a href="{{ route('penalties.export-list', ['student_id' => request('p_student_id'), 'date_from' => request('p_date_from'), 'date_to' => request('p_date_to')]) }}" target="_blank"
               class="px-4 py-2 bg-gray-100 text-gray-600 rounded-xl text-sm font-bold font-cairo hover:bg-navy hover:text-white transition-all flex items-center gap-2">
                <i class="fas fa-print"></i> طباعة القائمة
            </a>
            <a href="{{ route('penalties.create') }}"
               class="px-4 py-2 bg-orange-500 text-white rounded-xl text-sm font-bold font-cairo hover:bg-orange-600 transition-all flex items-center gap-2 shadow-sm shadow-orange-500/20">
                <i class="fas fa-plus-circle"></i> تسجيل عقوبة
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
                        <th class="px-5 py-4 text-xs font-black text-gray-400 font-cairo text-center">نوع العقوبة</th>
                        <th class="px-5 py-4 text-xs font-black text-gray-400 font-cairo text-center">المخالفة المرتبطة</th>
                        <th class="px-5 py-4 text-xs font-black text-gray-400 font-cairo text-center">تاريخ البدء</th>
                        <th class="px-5 py-4 text-xs font-black text-gray-400 font-cairo text-center">الحالة</th>
                        <th class="px-5 py-4 text-xs font-black text-gray-400 font-cairo text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($penalties as $p)
                    <tr class="hover:bg-gray-50/60 transition-colors">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-orange-50 rounded-lg flex items-center justify-center text-orange-500 font-bold text-xs">{{ mb_substr($p->student->name_ar ?? '?', 0, 1) }}</div>
                                <div>
                                    <p class="font-bold text-navy font-almarai text-sm">{{ $p->student->name_ar ?? '---' }}</p>
                                    <p class="text-[10px] text-gray-400  ">{{ $p->student->student_number ?? '' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-center">
                            @php
                                $ptypes = ['verbal_warning'=>'إنذار شفهي','written_warning'=>'إنذار كتابي','service_suspension'=>'إيقاف الخدمات','temporary_suspension'=>'إيقاف مؤقت','expulsion'=>'فصل'];
                                $pcolors = ['verbal_warning'=>'bg-blue-50 text-blue-600','written_warning'=>'bg-yellow-50 text-yellow-700','service_suspension'=>'bg-orange-50 text-orange-600','temporary_suspension'=>'bg-red-50 text-red-600','expulsion'=>'bg-red-100 text-red-800'];
                            @endphp
                            <span class="px-2.5 py-1 rounded-lg text-xs font-black {{ $pcolors[$p->type] ?? 'bg-gray-100 text-gray-600' }}">{{ $ptypes[$p->type] ?? $p->type }}</span>
                        </td>
                        <td class="px-5 py-4 text-center">
                            @if($p->violation)
                                <a href="{{ route('violations.show', $p->violation->id) }}" class="text-xs text-navy underline font-cairo hover:text-gold">{{ Str::limit($p->violation->type, 25) }}</a>
                            @else
                                <span class="text-xs text-gray-400">---</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-center">
                            <span class="text-xs   text-gray-500">{{ $p->start_date ? \Carbon\Carbon::parse($p->start_date)->format('Y-m-d') : '---' }}</span>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <span class="px-2.5 py-1 rounded-lg text-xs font-black {{ $p->is_active ? 'bg-orange-50 text-orange-600' : 'bg-gray-100 text-gray-500' }}">
                                {{ $p->is_active ? 'نشطة' : 'منتهية' }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <form action="{{ route('penalties.destroy', $p->id) }}" method="POST"
                                  onsubmit="return confirm('حذف هذه العقوبة؟')">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-8 h-8 bg-red-50 text-red-500 rounded-lg flex items-center justify-center hover:bg-red-500 hover:text-white transition-all mx-auto">
                                    <i class="fas fa-trash-alt text-xs"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-16 text-center">
                            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3 opacity-30">
                                <i class="fas fa-calendar-check text-3xl text-gray-400"></i>
                            </div>
                            <p class="text-navy font-black font-cairo">لا توجد عقوبات مسجلة</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($penalties->hasPages())
        <div class="px-5 py-4 border-t border-gray-50">{{ $penalties->appends(request()->except('p_page'))->links() }}</div>
        @endif
    </div>
</div>
