@extends ('layouts.app')
@php
    /** @var \Illuminate\Pagination\LengthAwarePaginator|\App\Models\Student[] $students */
@endphp

@section ('title', 'خريجو السكن')

@section ('content')
    <div class="mb-8 flex justify-between items-center bg-white p-6 rounded-2xl border-l-8 border-navy shadow-sm">
        <div>
            <h1 class="text-3xl font-black text-navy font-cairo">سجل الخريجين</h1>
            <p class="text-gray-400 font-almarai text-sm mt-1">تصفح وفلترة بيانات الطلاب الخريجين</p>
        </div>
        <div class="flex gap-3 flex-wrap">
            @if(auth()->user()->hasRole('center-manager') || auth()->user()->hasRole('super-admin'))
                <a href="{{ route('graduation.pending') }}"
                   class="px-5 py-3 bg-amber-500 text-white rounded-xl hover:bg-amber-600 shadow-lg font-cairo font-bold transition-all flex items-center gap-2">
                    <i class="fas fa-hourglass-half"></i>
                    <span>طلبات التخرج المعلقة</span>
                    @php
                        $pendingCount = \App\Models\Student::where('graduation_request_status', 'pending')
                            ->where('is_graduate', false)
                            ->when(auth()->user()->center_id, fn($q) => $q->where('center_id', auth()->user()->center_id))
                            ->count();
                    @endphp
                    @if($pendingCount > 0)
                        <span class="bg-white text-amber-600 text-xs font-black px-2 py-0.5 rounded-full">{{ $pendingCount }}</span>
                    @endif
                </a>
            @endif
            <a href="{{ route('students.export-list-pdf', array_merge(request()->all(), ['is_graduate' => 1])) }}"
                class="px-6 py-3 bg-white text-red-600 border-2 border-red-50 rounded-xl hover:bg-red-50 shadow-sm font-cairo font-bold transition-all flex items-center gap-2">
                <i class="fas fa-file-pdf"></i>
                <span>تصدير القائمة PDF</span>
            </a>
        </div>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow-sm mb-6 border border-gray-100 font-almarai">
        <form action="{{ route('students.alumni') }}" method="GET" class="space-y-4 text-right">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                @if (isset($centers))
                    <div>
                        <label for="center_id" class="block text-sm font-bold text-gray-700 mb-2 font-cairo">المركز / السكن</label>
                        <select name="center_id" id="center_id"
                            class="w-full rounded-xl border-gray-200 focus:border-primary focus:ring-primary shadow-sm bg-gray-50/50">
                            <option value="">جميع المراكز</option>
                            @foreach ($centers as $center)
                                <option value="{{ $center->id }}" {{ request('center_id') == $center->id ? 'selected' : '' }}>
                                    {{ $center->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-1">
                @else
                    <div class="md:col-span-2">
                @endif
                    <label for="search" class="block text-sm font-bold text-gray-700 mb-2 font-cairo">بحث شامل</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                            placeholder="الاسم، الرقم الجامعي، الهوية الوطنية..."
                            class="w-full pr-10 rounded-xl border-gray-200 focus:border-primary focus:ring-primary shadow-sm bg-gray-50/50">
                    </div>
                </div>

                <div>
                    <label for="university" class="block text-sm font-bold text-gray-700 mb-2 font-cairo">الجامعة</label>
                    <select name="university" id="university"
                        class="w-full rounded-xl border-gray-200 focus:border-primary focus:ring-primary shadow-sm bg-gray-50/50">
                        <option value="">جميع الجامعات</option>
                        @foreach ($universitys as $v)
                            <option value="{{ $v }}" {{ request('university') == $v ? 'selected' : '' }}>{{ $v }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="major" class="block text-sm font-bold text-gray-700 mb-2 font-cairo">التخصص</label>
                    <select name="major" id="major"
                        class="w-full rounded-xl border-gray-200 focus:border-primary focus:ring-primary shadow-sm bg-gray-50/50">
                        <option value="">جميع التخصصات</option>
                        @foreach ($majors as $v)
                            <option value="{{ $v }}" {{ request('major') == $v ? 'selected' : '' }}>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                <div>
                    <label for="college" class="block text-sm font-bold text-gray-700 mb-2 font-cairo">الكلية</label>
                    <select name="college" id="college"
                        class="w-full rounded-xl border-gray-200 focus:border-primary focus:ring-primary shadow-sm bg-gray-50/50">
                        <option value="">جميع الكليات</option>
                        @foreach ($colleges as $v)
                            <option value="{{ $v }}" {{ request('college') == $v ? 'selected' : '' }}>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>

              

                <div>
                    <label for="graduation_year" class="block text-sm font-bold text-gray-700 mb-2 font-cairo">عام التخرج</label>
                    <select name="graduation_year" id="graduation_year"
                        class="w-full rounded-xl border-gray-200 focus:border-primary focus:ring-primary shadow-sm bg-gray-50/50">
                        <option value="">جميع الأعوام</option>
                        @foreach ($graduation_years as $v)
                            <option value="{{ $v }}" {{ request('graduation_year') == $v ? 'selected' : '' }}>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="job_title" class="block text-sm font-bold text-gray-700 mb-2 font-cairo">الوظيفة</label>
                    <select name="job_title" id="job_title"
                        class="w-full rounded-xl border-gray-200 focus:border-primary focus:ring-primary shadow-sm bg-gray-50/50">
                        <option value="">جميع الوظائف</option>
                        @foreach ($job_titles ?? [] as $v)
                            <option value="{{ $v }}" {{ request('job_title') == $v ? 'selected' : '' }}>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-2">
                    <button type="submit"
                        class="flex-1 bg-navy text-white py-2.5 rounded-xl font-bold font-cairo shadow-md hover:bg-navy/90 transition-all flex items-center justify-center gap-2">
                        <i class="fas fa-filter text-xs text-gold"></i>
                        <span>تطبيق الفلترة</span>
                    </button>
                    @if (request()->anyFilled(['search', 'major', 'university', 'college', 'academic_level', 'nationality', 'graduation_year', 'job_title']))
                        <a href="{{ route('students.alumni') }}"
                            class="bg-gray-100 text-gray-700 px-4 py-2.5 rounded-xl font-bold font-cairo hover:bg-gray-200 transition-all text-sm flex items-center">
                            إعادة تعيين
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100">
        <table class="min-w-full divide-y divide-gray-200 text-right">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">الطالب</th>
                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">الرقم الجامعي</th>
                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">الجامعة</th>
                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">التخصص</th>
                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">السكن</th>
                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">البرنامج</th>
                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">عام التخرج</th>
                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">الوظيفة</th>
                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach ($students as $student)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="h-10 w-10 flex-shrink-0">
                                    <img class="h-10 w-10 rounded-full object-cover border opacity-75 grayscale"
                                        src="{{ $student->photo ? asset('storage/' . $student->photo) : 'https://ui-avatars.com/api/?name=' . urlencode($student->name_ar) . '&background=0f172a&color=fff' }}"
                                        alt="">
                                </div>
                                <div class="mr-4">
                                    <div class="text-sm font-bold text-gray-900">{{ $student->name_ar }}</div>
                                    <div class="text-[10px] text-gray-500">خريج</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500  ">
                            {{ $student->student_number }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                            {{ $student->university }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-medium">
                            {{ $student->major }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-bold text-navy font-cairo">
                                {{ $student->center->name ?? 'غير محدد' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-medium">
                            {{ $student->program->name ?? '---' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap   text-sm font-bold text-gray-700">
                            {{ $student->graduation_year ?? '---' }}
                        </td>
                        {{-- Job Title --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($student->job_title)
                                <span class="inline-flex items-center gap-1 px-2 py-1 bg-navy/10 text-navy rounded-full text-xs font-bold font-cairo">
                                    <i class="fas fa-briefcase text-[9px]"></i>
                                    {{ $student->job_title }}
                                </span>
                            @else
                                <span class="text-gray-300 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('students.show', $student) }}"
                                class="text-navy hover:text-gold font-bold ml-3 flex items-center gap-1 transition-colors">
                                <i class="fas fa-id-card text-xs"></i> عرض الملف
                            </a>
                            @if (auth()->user()->hasRole('center-manager'))
                            <form action="{{ route('students.restore-graduate', $student) }}" method="POST" class="inline"
                                data-confirm="هل تريد إعادة هذا الطالب إلى القائمة النشطة؟">
                                @csrf
                                <button type="submit" class="text-green-600 hover:text-green-800 ml-3 flex items-center gap-1 transition-colors font-bold">
                                    <i class="fas fa-user-check text-xs"></i> إعادة للطالب
                                </button>
                            </form>
                            @endif
                            <a href="{{ route('students.export-pdf', $student) }}"
                                class="text-red-600 hover:text-red-800 font-bold ml-3 flex items-center gap-1 transition-colors">
                                <i class="fas fa-file-pdf text-xs"></i> تصدير PDF
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-6 py-4 bg-gray-50 border-t">
            {{ $students->links() }}
        </div>
    </div>
@endsection
