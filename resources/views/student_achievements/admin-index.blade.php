@extends ('layouts.app')
@php
    /** @var \Illuminate\Pagination\LengthAwarePaginator|\App\Models\StudentAchievement[] $achievements */
@endphp

@section('content')
<div class="py-12 bg-gray-50/50 min-h-screen" dir="rtl">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-black text-gray-900 font-cairo">إدارة إنجازات الطلاب</h1>
                <p class="mt-2 text-sm text-gray-500 font-almarai">عرض، تعديل، وحذف إنجازات الطلاب الشخصية المسجلة.</p>
            </div>
            <a href="{{ route('student-achievements.create') }}" 
                class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-bold rounded-2xl text-white bg-primary hover:bg-primary/90 shadow-xl shadow-primary/20 transition-all transform hover:-translate-y-0.5">
                <i class="fas fa-plus-circle ml-2"></i>
                إضافة إنجاز جديد
            </a>
        </div>

        @if (session('success'))
            <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-700 font-almarai flex items-center gap-3 shadow-sm">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-3xl overflow-hidden border border-gray-100 shadow-xl shadow-gray-200/20">
            <div class="overflow-x-auto">
                <table class="w-full text-right border-collapse">
                    <thead class="bg-gray-50/50 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-sm font-bold text-gray-700 font-cairo">الطالب</th>
                            <th class="px-6 py-4 text-sm font-bold text-gray-700 font-cairo text-center">الإنجاز</th>
                            <th class="px-6 py-4 text-sm font-bold text-gray-700 font-cairo text-center">التاريخ</th>
                            <th class="px-6 py-4 text-sm font-bold text-gray-700 font-cairo text-center">الشهادة</th>
                            <th class="px-6 py-4 text-sm font-bold text-gray-700 font-cairo text-center">تاريخ الرفع</th>
                            <th class="px-6 py-4 text-sm font-bold text-gray-700 font-cairo text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @if ($achievements->count() > 0)
                            @foreach ($achievements as $achievement)
                            <tr class="hover:bg-gray-50/30 transition-colors group">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-primary/5 flex items-center justify-center text-primary">
                                            <i class="fas fa-user-graduate"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-gray-900 font-cairo">{{ $achievement->student->name_ar }}</p>
                                            <p class="text-[10px] text-gray-400 font-mono italic">#{{ $achievement->student->student_number }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <span class="px-3 py-1 bg-amber-50 text-amber-600 rounded-lg text-xs font-bold font-cairo">
                                        {{ $achievement->title }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <span class="text-sm text-gray-600 font-almarai">{{ $achievement->achievement_date?->format('Y/m/d') ?? 'غير محدد' }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if ($achievement->certificate_file)
                                        <a href="{{ asset('storage/' . $achievement->certificate_file) }}" target="_blank"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 hover:bg-primary/10 hover:text-primary rounded-lg text-xs font-bold font-cairo transition-all">
                                            <i class="fas fa-certificate"></i> عرض الملف
                                        </a>
                                    @else
                                        <span class="text-xs text-gray-300 font-almarai italic">لا يوجد ملف</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <span class="text-[11px] text-gray-400 font-mono">{{ $achievement->created_at->format('Y/m/d') }}</span>
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('student-achievements.edit', $achievement) }}" 
                                            class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 hover:bg-amber-600 hover:text-white transition-all flex items-center justify-center shadow-sm">
                                            <i class="fas fa-pencil-alt text-xs"></i>
                                        </a>
                                        <form action="{{ route('student-achievements.destroy', $achievement) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا الإنجاز؟ لا يمكن التراجع!')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                class="w-9 h-9 rounded-xl bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-all flex items-center justify-center shadow-sm">
                                                <i class="fas fa-trash-alt text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6" class="px-6 py-20 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="w-16 h-16 rounded-3xl bg-gray-50 flex items-center justify-center text-gray-300">
                                            <i class="fas fa-trophy text-3xl"></i>
                                        </div>
                                        <h3 class="text-lg font-bold text-gray-700 font-cairo">لا توجد إنجازات مسجلة حالياً</h3>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            @if ($achievements instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="px-6 py-4 border-t border-gray-50">
                    {{ $achievements->links() }}
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
