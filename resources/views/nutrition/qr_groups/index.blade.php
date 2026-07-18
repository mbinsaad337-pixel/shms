@extends(auth()->user()->hasRole('nutrition-manager') ? 'layouts.nutrition' : 'layouts.app')
@section('title', 'مجموعات QR المجمعة')

@section('content')
    <div class="p-6 max-w-5xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 font-cairo">مجموعات QR المجمعة</h2>
                <p class="text-gray-400 text-sm font-almarai">أنشئ QR مجمع لصديقك ويمكن مسحه بدلاً عنكم</p>
            </div>
            <a href="{{ route('nutrition.qr-groups.create') }}"
                class="inline-flex items-center gap-2 bg-teal-600 hover:bg-teal-700 text-white px-5 py-2.5 rounded-xl font-bold font-cairo text-sm shadow-lg shadow-teal-200">
                <i class="fas fa-plus"></i> مجموعة جديدة
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            @if(is_countable($groups) ? count($groups) > 0 : (method_exists($groups, 'count') ? $groups->count() > 0 : !empty($groups)))
    @foreach($groups as $group)
                <div
                    class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
                    <div
                        class="bg-gradient-to-l {{ $group->is_used ? 'from-gray-400 to-gray-500' : ($group->valid_date->isToday() ? 'from-teal-500 to-emerald-600' : 'from-gray-400 to-gray-500') }} p-5 flex items-center justify-between">
                        <div>
                            <p class="text-white/70 text-xs font-cairo">مجموعة</p>
                            <p class="text-white font-bold text-lg font-cairo">{{ $group->members_count }} أشخاص</p>
                        </div>
                        <div class="text-right">
                            @if($group->is_used)
                                <span class="bg-white/20 text-white px-3 py-1 rounded-lg text-xs font-bold font-cairo">مستخدم</span>
                            @elseif(!$group->valid_date->isToday())
                                <span class="bg-white/20 text-white px-3 py-1 rounded-lg text-xs font-bold font-cairo">منتهي</span>
                            @else
                                <span class="bg-white text-teal-700 px-3 py-1 rounded-lg text-xs font-bold font-cairo">فعال
                                    اليوم</span>
                            @endif
                        </div>
                    </div>
                    <div class="p-5">
                        <p class="text-xs text-gray-400 font-cairo">تاريخ الصلاحية: <span
                                class="font-mono font-bold text-gray-700">{{ $group->valid_date->format('Y-m-d') }}</span></p>
                        <div class="mt-3 flex flex-wrap gap-2">
                            @foreach($group->members->take(6) as $member)
                                <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-lg font-cairo">
                                    {{ $member->student?->name_ar ?? 'طالب غير موجود' }}
                                </span>
                            @endforeach
                            @if($group->members->count() > 6)
                                <span class="text-xs bg-gray-100 text-gray-500 px-2 py-1 rounded-lg font-cairo">
                                    +{{ $group->members->count() - 6 }} آخرين
                                </span>
                            @endif
                        </div>
                        <a href="{{ route('nutrition.qr-groups.show', $group) }}"
                            class="mt-4 w-full block text-center py-2.5 bg-teal-50 hover:bg-teal-100 text-teal-700 rounded-xl font-bold font-cairo text-sm transition-all">
                            <i class="fas fa-qrcode ml-1"></i> عرض QR
                        </a>
                    </div>
                </div>
                @endforeach
@else
                <div class="col-span-2 text-center py-20 text-gray-300">
                    <i class="fas fa-layer-group text-5xl mb-3 block"></i>
                    <p class="font-cairo">لا توجد مجموعات بعد. أنشئ مجموعة للتوزيع الجماعي.</p>
                </div>
            @endif
        </div>
        <div class="mt-4">{{ $groups->links() }}</div>
    </div>
@endsection
