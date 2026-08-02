@extends('layouts.app')
@section('title', 'إدارة بيانات الدرجات')

@section('content')
<div class="py-12 bg-gray-50/50 min-h-screen" dir="rtl">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-black text-gray-900 font-cairo">إدارة بيانات الدرجات للطلاب</h1>
                <p class="mt-2 text-sm text-gray-500 font-almarai">عرض، تعديل، وحذف بيانات الدرجات المرفوعة من الطلاب.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-700 font-almarai flex items-center gap-3">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm mb-8">
            <form action="{{ route('student-grades.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end font-almarai">
                <div class="space-y-1.5 col-span-1 md:col-span-1">
                    <label class="text-xs font-bold text-gray-500 mr-2">بحث باسم الطالب أو الرقم</label>
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="بحث..." 
                            class="w-full pl-4 pr-10 py-2.5 bg-gray-50 border border-gray-100 rounded-xl focus:ring-2 focus:ring-primary outline-none text-sm transition-all">
                        <i class="fas fa-search absolute right-3 top-3 text-gray-300"></i>
                    </div>
                </div>
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-gray-500 mr-2">الفصل الدراسي</label>
                    <select name="semester" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-xl focus:ring-2 focus:ring-primary outline-none text-sm transition-all">
                        <option value="">الكل</option>
                        @foreach (['الفصل الدراسي الأول', 'الفصل الدراسي الثاني', 'الفصل الدراسي الثالث', 'الفصل الدراسي الصيفي', 'بيان درجات سنوي'] as $sem)
                            <option value="{{ $sem }}" {{ request('semester') == $sem ? 'selected' : '' }}>{{ $sem }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-gray-500 mr-2">السنة الدراسية</label>
                    <input type="text" name="year" value="{{ request('year') }}" placeholder="مثال: 2024" 
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-xl focus:ring-2 focus:ring-primary outline-none text-sm transition-all">
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 py-2.5 bg-primary text-white rounded-xl font-bold text-sm shadow-lg shadow-primary/20 hover:bg-primary/90 transition-all">
                        <i class="fas fa-filter ml-1"></i> تصفية
                    </button>
                    <a href="{{ route('student-grades.index') }}" class="py-2.5 px-4 bg-gray-100 text-gray-500 rounded-xl font-bold text-sm hover:bg-gray-200 transition-all flex items-center justify-center">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-3xl overflow-hidden border border-gray-100 shadow-xl shadow-gray-200/20">
            <div class="overflow-x-auto">
                <table class="w-full text-right border-collapse">
                    <thead class="bg-gray-50/50 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-sm font-bold text-gray-700 font-cairo">الطالب</th>
                            <th class="px-6 py-4 text-sm font-bold text-gray-700 font-cairo text-center">الفصل</th>
                            <th class="px-6 py-4 text-sm font-bold text-gray-700 font-cairo text-center">النسبة (%)</th>
                            <th class="px-6 py-4 text-sm font-bold text-gray-700 font-cairo text-center">السنة الجامعية</th>
                            <th class="px-6 py-4 text-sm font-bold text-gray-700 font-cairo text-center">البيان</th>
                            <th class="px-6 py-4 text-sm font-bold text-gray-700 font-cairo text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @if(count($grades) > 0)
                            @foreach ($grades as $grade)
                                <tr class="hover:bg-gray-50/30 transition-colors group">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-primary/5 flex items-center justify-center text-primary">
                                                <i class="fas fa-user-graduate"></i>
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-gray-900 font-cairo">{{ $grade->student->name_ar }}</p>
                                                <p class="text-[10px] text-gray-400   italic">#{{ $grade->student->student_number }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                        <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-lg text-xs font-bold font-cairo">
                                            {{ $grade->semester }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center whitespace-nowrap   font-bold text-gray-700">
                                        @if($grade->gpa_percentage)
                                            <span class="text-emerald-600">{{ number_format($grade->gpa_percentage, 2) }}%</span>
                                        @else
                                            <span class="text-gray-300">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                        <span class="text-sm text-gray-600 font-almarai">{{ $grade->academic_year ?? 'غير محدد' }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <a href="{{ asset('storage/' . $grade->file_path) }}" target="_blank"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 hover:bg-primary/10 hover:text-primary rounded-lg text-xs font-bold font-cairo transition-all">
                                            <i class="fas fa-image"></i> عرض الصورة
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="{{ route('student-grades.edit', $grade) }}" 
                                                class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 hover:bg-amber-600 hover:text-white transition-all flex items-center justify-center shadow-sm">
                                                <i class="fas fa-edit text-xs"></i>
                                            </a>
                                            <form action="{{ route('student-grades.destroy', $grade) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا البيان؟ لا يمكن التراجع!')">
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
                                            <i class="fas fa-file-invoice text-3xl"></i>
                                        </div>
                                        <h3 class="text-lg font-bold text-gray-700 font-cairo">لا توجد بيانات مرفوعة حالياً</h3>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            @if($grades instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="px-6 py-4 border-t border-gray-50">
                    {{ $grades->links() }}
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
