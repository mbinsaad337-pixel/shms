@extends ('layouts.app')
@php
    /** @var \App\Models\Room[] $rooms */
@endphp

@section ('title', 'إدارة المرافق والغرف')

@section ('content')
    <div class="container mx-auto px-6 py-8">
        <div class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6 bg-white p-6 rounded-2xl border-r-8 border-gold shadow-sm" dir="rtl">
            <div>
                <h1 class="text-2xl md:text-3xl font-black text-navy font-cairo">المرافق والغرف</h1>
                <p class="text-gray-400 font-almarai text-xs md:text-sm mt-1">إدارة السكن والقاعات والمرافق التابعة للمركز</p>
            </div>
            <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
                <a href="{{ route('rooms.export-list', request()->all()) }}"
                    class="flex-1 sm:flex-none px-4 py-3 bg-white text-red-600 border border-red-100 rounded-xl hover:bg-red-50 shadow-sm font-cairo font-bold transition-all flex items-center justify-center gap-2 text-sm">
                    <i class="fas fa-file-pdf"></i>
                    <span>تصدير عام</span>
                </a>
                <a href="{{ route('rooms.create') }}"
                    class="flex-1 sm:flex-none px-4 py-3 bg-navy text-white rounded-xl hover:bg-navy/90 shadow-lg font-cairo font-bold transition-all transform active:scale-95 flex items-center justify-center gap-2 text-sm">
                    <i class="fas fa-plus-circle text-gold"></i>
                    <span>إضافة مرفق جديد</span>
                </a>
            </div>
        </div>

        <div class="bg-white p-4 md:p-6 rounded-2xl shadow-sm mb-6 border border-gray-50 font-almarai">
            <form action="{{ route('rooms.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 items-end">
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 mb-1.5 uppercase">الطابق</label>
                    <select name="floor" onchange="this.form.submit()" class="w-full bg-gray-50 border-0 rounded-xl p-3 text-sm focus:ring-2 focus:ring-gold/20">
                        <option value="">كل الطوابق</option>
                        @foreach($floors as $floor)
                            <option value="{{ $floor }}" {{ request('floor') == $floor ? 'selected' : '' }}>طابق {{ $floor }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 mb-1.5 uppercase">الشقة</label>
                    <select name="apartment" onchange="this.form.submit()" class="w-full bg-gray-50 border-0 rounded-xl p-3 text-sm focus:ring-2 focus:ring-gold/20">
                        <option value="">كل الشقق</option>
                        @foreach($apartments as $apt)
                            <option value="{{ $apt }}" {{ request('apartment') == $apt ? 'selected' : '' }}>شقة {{ $apt }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 mb-1.5 uppercase">تصفية سريعة</label>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('rooms.index') }}"
                            class="flex-1 text-center py-2.5 rounded-xl text-xs font-bold font-cairo transition-all {{ !request('vacant') ? 'bg-navy text-white shadow-md' : 'bg-gray-50 text-gray-400 border border-gray-100' }}">الكل</a>
                        <a href="{{ route('rooms.index', array_merge(request()->all(), ['vacant' => 1])) }}"
                            class="flex-1 text-center py-2.5 rounded-xl text-xs font-bold font-cairo transition-all {{ request('vacant') ? 'bg-navy text-white shadow-md' : 'bg-gray-50 text-gray-400 border border-gray-100' }}">الشواغر</a>
                    </div>
                </div>
                <div class="flex md:justify-end pb-1">
                     @if(request()->anyFilled(['floor', 'apartment', 'vacant']))
                        <a href="{{ route('rooms.index') }}" class="text-red-500 font-bold text-[10px] hover:underline flex items-center gap-1">
                            <i class="fas fa-undo-alt"></i> إعادة تعيين
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($rooms as $room)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden card-hover">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div class="bg-navy/5 text-navy px-3 py-1 rounded-lg text-xs font-bold font-cairo">
                                {{ $room->type == 'residential' ? 'غرفة سكنية' : ($room->type == 'study_hall' ? 'قاعة دراسية' : 'قاعة أنشطة') }}
                            </div>
                            <span
                                class="px-2 py-1 rounded text-[10px] font-bold {{ $room->status == 'available' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $room->status == 'available' ? 'متاح' : ($room->status == 'maintenance' ? 'صيانة' : 'مغلق') }}
                            </span>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 font-almarai mb-1">غرفة رقم: {{ $room->room_number }}</h3>
                        <p class="text-sm text-gray-500 font-almarai mb-4">
                            مبنى {{ $room->building }} - الطابق {{ $room->floor }}
                            @if ($room->apartment)
                                - شقة {{ $room->apartment }}
                            @endif
                        </p>

                        <div class="space-y-3">
                            <div class="flex justify-between text-xs font-almarai">
                                <span class="text-gray-400 font-bold">نسبة الإشغال:</span>
                                <span class="font-black text-navy">{{ $room->students_count }} / {{ $room->capacity }}</span>
                            </div>
                            <div class="w-full bg-gray-100 h-2.5 rounded-full overflow-hidden shadow-inner">
                                @php $percent = $room->capacity > 0 ? ($room->students_count / $room->capacity) * 100 : 0; @endphp
                                <div class="bg-gold h-full transition-all duration-700 ease-out rounded-full shadow-sm"
                                    style="width: {{ $percent }}%"></div>
                            </div>

                            @if ($room->students_count > 0)
                                <div class="mt-4">
                                    <p class="text-[10px] font-bold text-gray-400 mb-2 uppercase tracking-wider">الطلاب الحاليين:
                                    </p>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach ($room->students()->get() as $student)
                                            <a href="{{ route('students.show', $student) }}"
                                                class="bg-gray-50 hover:bg-gray-100 px-2 py-1 rounded text-xs font-bold text-gray-700 border border-gray-100 transition-colors">
                                                {{ $student->name_ar }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="mt-8 flex gap-3">
                            @if ($room->type == 'residential' && $room->students_count < $room->capacity && $room->status == 'available')
                                <button onclick="openAssignModal({{ $room->id }}, '{{ $room->room_number }}')"
                                    class="flex-1 bg-navy text-white px-4 py-2.5 rounded-xl text-sm font-cairo font-bold hover:bg-navy/90 transition-all shadow-md group">
                                    <i class="fas fa-user-plus text-gold ml-1 group-hover:scale-110 transition-transform"></i> توزيع
                                </button>
                            @endif
                            <a href="{{ route('rooms.export', $room) }}" title="تصدير كشف الغرفة"
                                class="w-10 h-10 flex items-center justify-center bg-white border border-red-100 rounded-xl text-red-500 hover:bg-red-50 transition-all shadow-sm">
                                <i class="fas fa-file-pdf"></i>
                            </a>
                            <a href="{{ route('rooms.edit', $room) }}"
                                class="w-10 h-10 flex items-center justify-center bg-gray-50 border border-gray-100 rounded-xl text-navy hover:bg-gold hover:text-navy transition-all shadow-sm">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('rooms.destroy', $room) }}" method="POST" class="inline"
                                data-confirm="هل أنت متأكد من حذف هذا المرفق؟ لا يمكن التراجع عن هذا الإجراء.">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-10 h-10 flex items-center justify-center text-gray-300 hover:text-red-500 transition-colors">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    <!-- Assign Student Modal -->
    <div id="assignModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-3xl w-full max-w-lg overflow-hidden shadow-2xl">
            <div class="p-8 border-b border-gray-50 flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-800 font-cairo">توزيع طالب على غرفة: <span
                        id="modalRoomNumber"></span></h3>
                <button onclick="closeAssignModal()" class="text-gray-400 hover:text-gray-600"><i
                        class="fas fa-times"></i></button>
            </div>
            <form action="{{ route('assignments.store') }}" method="POST" class="p-8 space-y-6">
                @csrf
                <input type="hidden" name="room_id" id="modalRoomId">

                @php
                    $availableStudents = \App\Models\Student::where('center_id', auth()->user()->center_id)
                        ->where('status', 'registered')
                        ->get(['id', 'name_ar', 'national_id'])
                        ->toArray();
                @endphp

                <div x-data="{ 
                    search: '', 
                    students: {{ json_encode($availableStudents) }},
                    get filteredStudents() {
                        const term = this.search.trim().toLowerCase();
                        if (term === '') return this.students;
                        return this.students.filter(s => 
                            (s.name_ar && s.name_ar.toLowerCase().includes(term)) || 
                            (s.national_id && s.national_id.toLowerCase().includes(term))
                        );
                    }
                }">
                    <label class="block text-sm font-bold text-gray-700 font-cairo mb-2">اختر الطالب</label>
                    <div class="mb-3 relative">
                        <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400">
                            <i class="fas fa-search text-xs"></i>
                        </span>
                        <input type="text" x-model="search" placeholder="ابحث بالاسم أو الهوية..." 
                            class="w-full bg-gray-50 border-0 rounded-xl pr-10 p-3 text-sm focus:ring-2 focus:ring-primary/20 font-almarai">
                    </div>
                    
                    <select name="student_id" required
                        class="w-full bg-gray-50 border-0 rounded-2xl p-4 font-almarai focus:ring-2 focus:ring-primary/20">
                        <option value="">-- اختر من القائمة --</option>
                        <template x-for="student in filteredStudents" :key="student.id">
                            <option :value="student.id" x-text="student.name_ar + ' - ' + student.national_id"></option>
                        </template>
                    </select>
                    
                    <div class="mt-2 flex justify-between items-center px-1">
                        <p class="text-[10px] text-gray-400 font-almarai">عرض <span x-text="filteredStudents.length"></span> من أصل {{ count($availableStudents) }} طالب</p>
                        <template x-if="filteredStudents.length === 0">
                            <p class="text-[10px] text-red-500 font-bold">عذراً، لا توجد نتائج مطابقة</p>
                        </template>
                    </div>
                </div>

                <div class="flex gap-4">
                    <button type="submit"
                        class="flex-1 bg-primary text-white py-4 rounded-2xl font-bold font-cairo shadow-lg hover:bg-blue-900 transition-all">تأكيد
                        التوزيع</button>
                    <button type="button" onclick="closeAssignModal()"
                        class="flex-1 bg-gray-100 text-gray-700 py-4 rounded-2xl font-bold font-cairo hover:bg-gray-200 transition-all">إلغاء</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openAssignModal(roomId, roomNumber) {
            document.getElementById('modalRoomId').value = roomId;
            document.getElementById('modalRoomNumber').innerText = roomNumber;
            document.getElementById('assignModal').classList.remove('hidden');
            document.getElementById('assignModal').classList.add('flex');
        }

        function closeAssignModal() {
            document.getElementById('assignModal').classList.add('hidden');
            document.getElementById('assignModal').classList.remove('flex');
        }
    </script>
@endsection
