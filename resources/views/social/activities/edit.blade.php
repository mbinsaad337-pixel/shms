@extends('layouts.app')

@section('title', 'تعديل الفعالية')

@section('content')
    <div class="container mx-auto px-6 py-8">
        <!-- Header Section -->
        <div class="mb-8 flex justify-between items-center bg-white p-8 rounded-3xl border-l-8 border-navy shadow-sm">
            <div>
                <h1 class="text-3xl font-black text-navy font-cairo text-right">تعديل الفعالية</h1>
                <p class="text-gray-400 font-almarai text-sm mt-2 text-right">تحديث بيانات: {{ $activity->name }}</p>
            </div>
            <div class="flex gap-4">
                <a href="{{ route('activities.show', $activity->id) }}"
                    class="px-6 py-3 bg-gray-50 text-navy rounded-2xl hover:bg-gray-100 font-cairo font-bold transition-all flex items-center gap-2 border border-gray-100">
                    <i class="fas fa-eye"></i>
                    <span>عرض التفاصيل</span>
                </a>
                <a href="{{ route('activities.index') }}"
                    class="px-6 py-3 bg-gray-50 text-navy rounded-2xl hover:bg-gray-100 font-cairo font-bold transition-all flex items-center gap-2 border border-gray-100">
                    <i class="fas fa-arrow-right"></i>
                    <span>رجوع للقائمة</span>
                </a>
            </div>
        </div>

        <div class="bg-white rounded-[2.5rem] p-10 shadow-2xl border-t-8 border-navy">
            <form action="{{ route('activities.update', $activity->id) }}" method="POST" class="space-y-8">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <!-- Title & Club -->
                    <div class="lg:col-span-2">
                        <label class="block text-sm font-black text-navy mb-3 font-cairo text-right">عنوان الفعالية</label>
                        <input type="text" name="name" value="{{ $activity->name }}" required
                            class="w-full px-5 py-4 rounded-xl border border-gray-100 bg-gray-50/50 focus:bg-white focus:ring-4 focus:ring-navy/5 outline-none text-right transition-all font-almarai">
                    </div>

                    <div class="lg:col-span-1">
                        <label class="block text-sm font-black text-navy mb-3 font-cairo text-right">النادي المنظم</label>
                        <select id="edit_club_id" name="club_id" required onchange="updateTargetOptions()"
                            class="w-full px-5 py-4 rounded-xl border border-gray-100 bg-gray-50/50 focus:bg-white focus:ring-4 focus:ring-navy/5 outline-none text-right transition-all font-almarai">
                            @foreach($clubs as $club)
                                <option value="{{ $club->id }}" {{ $activity->club_id == $club->id ? 'selected' : '' }}>{{ $club->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="lg:col-span-1">
                        <label class="block text-sm font-black text-navy mb-3 font-cairo text-right">حالة الفعالية</label>
                        <select name="status" required
                            class="w-full px-5 py-4 rounded-xl border border-gray-100 bg-gray-50/50 focus:bg-white focus:ring-4 focus:ring-navy/5 outline-none text-right transition-all font-almarai">
                            <option value="planned" {{ $activity->status == 'planned' ? 'selected' : '' }}>مجدولة (مخطط لها)</option>
                            <option value="published" {{ $activity->status == 'published' ? 'selected' : '' }}>منشورة (نشطة)</option>
                            <option value="completed" {{ $activity->status == 'completed' ? 'selected' : '' }}>مكتملة</option>
                            <option value="cancelled" {{ $activity->status == 'cancelled' ? 'selected' : '' }}>ملغاة</option>
                        </select>
                    </div>

                    <!-- Date Range -->
                    <div class="lg:col-span-1">
                        <label class="block text-sm font-black text-navy mb-3 font-cairo text-right">من تاريخ</label>
                        <input type="date" name="start_date" value="{{ $activity->start_date?->format('Y-m-d') }}" required
                            class="w-full px-5 py-4 rounded-xl border border-gray-100 bg-gray-50/50 focus:bg-white focus:ring-4 focus:ring-navy/5 outline-none text-right font-mono transition-all">
                    </div>

                    <div class="lg:col-span-1">
                        <label class="block text-sm font-black text-navy mb-3 font-cairo text-right">إلى تاريخ</label>
                        <input type="date" name="end_date" value="{{ $activity->end_date?->format('Y-m-d') }}"
                            class="w-full px-5 py-4 rounded-xl border border-gray-100 bg-gray-50/50 focus:bg-white focus:ring-4 focus:ring-navy/5 outline-none text-right font-mono transition-all">
                    </div>

                    <div class="lg:col-span-2">
                        <label class="block text-sm font-black text-navy mb-3 font-cairo text-right">الموقع / القاعة</label>
                        <input type="text" name="location" value="{{ $activity->location }}" required
                            class="w-full px-5 py-4 rounded-xl border border-gray-100 bg-gray-50/50 focus:bg-white focus:ring-4 focus:ring-navy/5 outline-none text-right transition-all font-almarai">
                    </div>

                    <!-- Time Range -->
                    <div class="lg:col-span-2">
                        <label class="block text-sm font-black text-navy mb-3 font-cairo text-right">من الساعة</label>
                        <input type="time" name="start_time" value="{{ $activity->start_time }}"
                            class="w-full px-5 py-4 rounded-xl border border-gray-100 bg-gray-50/50 focus:bg-white focus:ring-4 focus:ring-navy/5 outline-none text-center font-mono transition-all">
                    </div>

                    <div class="lg:col-span-2">
                        <label class="block text-sm font-black text-navy mb-3 font-cairo text-right">إلى الساعة</label>
                        <input type="time" name="end_time" value="{{ $activity->end_time }}"
                            class="w-full px-5 py-4 rounded-xl border border-gray-100 bg-gray-50/50 focus:bg-white focus:ring-4 focus:ring-navy/5 outline-none text-center font-mono transition-all">
                    </div>
                </div>

                <!-- Target Selection Box -->
                <div class="w-full mt-10">
                    <div class="bg-gray-50 rounded-[2.5rem] p-8 border border-gray-100 shadow-inner">
                        <div class="flex items-center justify-between mb-8">
                            <h3 class="font-black text-navy font-cairo text-xl">تعديل الطلاب المستهدفين</h3>
                            <div class="flex gap-3">
                                <div id="edit_club_member_option" class="flex items-center gap-3 bg-white px-5 py-3 rounded-2xl border border-gold/20 shadow-sm">
                                    <input type="checkbox" name="target_club_members" value="1" id="edit_target_club" class="w-6 h-6 rounded border-gray-300 text-gold focus:ring-gold">
                                    <label for="edit_target_club" class="text-sm font-bold text-navy font-cairo">أعضاء النادي</label>
                                </div>
                                <div class="flex items-center gap-3 bg-navy/5 px-5 py-3 rounded-2xl border border-navy/10 shadow-sm">
                                    <input type="checkbox" name="target_all_students" value="1" id="edit_target_all" class="w-6 h-6 rounded border-gray-300 text-navy focus:ring-navy">
                                    <label for="edit_target_all" class="text-sm font-bold text-navy font-cairo">جميع طلاب المركز</label>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <div class="relative">
                                <input type="text" id="edit_student_search" placeholder="ابحث عن الطالب لإضافته أو إزالته..." onkeyup="filterEditStudents()"
                                    class="w-full px-14 py-5 rounded-3xl border-2 border-transparent focus:border-gold/30 bg-white shadow-sm outline-none text-right font-almarai text-lg transition-all">
                                <i class="fas fa-search absolute right-6 top-1/2 -translate-y-1/2 text-gold/50 text-xl"></i>
                            </div>

                            <div class="space-y-3 max-h-96 overflow-y-auto px-4 custom-scrollbar-v2" id="edit_student_list">
                                @php
                                    $targetedIds = $activity->targetedStudents->pluck('id')->toArray();
                                @endphp
                                @foreach($students as $student)
                                    <label class="edit-student-item group relative flex items-center justify-between p-5 rounded-2xl border border-white bg-white/50 hover:bg-white hover:border-gold/30 hover:shadow-md cursor-pointer transition-all">
                                        <div class="flex items-center gap-5">
                                            <input type="checkbox" name="target_student_ids[]" value="{{ $student->id }}" 
                                                {{ in_array($student->id, $targetedIds) ? 'checked' : '' }}
                                                class="w-7 h-7 rounded-lg border-gray-300 text-gold focus:ring-gold transition-transform group-hover:scale-110">
                                            <div class="flex flex-col text-right">
                                                <span class="text-base font-black text-navy group-hover:text-gold transition-colors">{{ $student->name_ar }}</span>
                                                <span class="text-xs text-gray-400 font-mono tracking-widest">{{ $student->student_number }}</span>
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex gap-6 pt-10">
                    <button type="submit"
                        class="flex-[3] bg-navy text-white py-6 rounded-3xl font-black text-xl shadow-2xl hover:shadow-navy/40 transition-all flex items-center justify-center gap-4 group">
                        <i class="fas fa-save text-gold group-hover:scale-125 transition-transform"></i>
                        <span>حفظ التعديلات وتحديث البيانات</span>
                    </button>
                    <a href="{{ route('activities.show', $activity->id) }}"
                        class="flex-1 bg-gray-100 text-gray-400 py-6 rounded-3xl font-bold font-cairo text-center hover:bg-gray-200 transition-colors flex items-center justify-center">إلغاء</a>
                </div>
            </form>
        </div>
    </div>

    <style>
        .custom-scrollbar-v2::-webkit-scrollbar { width: 8px; }
        .custom-scrollbar-v2::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar-v2::-webkit-scrollbar-thumb { background: #E5E7EB; border-radius: 10px; border: 3px solid transparent; }
        .custom-scrollbar-v2::-webkit-scrollbar-thumb:hover { background: #D1D5DB; }
    </style>

    <script>
        function filterEditStudents() {
            const search = document.getElementById('edit_student_search').value.toLowerCase();
            const items = document.querySelectorAll('.edit-student-item');
            
            items.forEach(item => {
                const name = item.innerText.toLowerCase();
                item.style.display = name.includes(search) ? 'flex' : 'none';
            });
        }
    </script>
@endsection
