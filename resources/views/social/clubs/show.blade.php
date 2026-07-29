@extends('layouts.app')

@section('title', 'تفاصيل النادي وإدارة الأعضاء')

@section('content')
    <div class="container mx-auto px-6 py-8">
        <!-- Header Section -->
        <div class="mb-8 flex justify-between items-center bg-white p-8 rounded-3xl border-l-8 border-gold shadow-sm">
            <div class="flex items-center gap-6">
                <div class="w-20 h-20 bg-navy/5 rounded-2xl flex items-center justify-center text-navy shadow-sm">
                    <i class="fas fa-users-rectangle text-3xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-black text-navy font-cairo">{{ $club->name }}</h1>
                    <p class="text-gray-400 font-almarai text-sm mt-1">{{ $club->category }} | مركز
                        {{ $club->center->name ?? '---' }}</p>
                </div>
            </div>
            <div class="flex gap-4">
                <a href="{{ route('clubs.index') }}"
                    class="px-6 py-4 bg-gray-50 text-navy rounded-2xl hover:bg-gray-100 font-cairo font-bold transition-all flex items-center gap-2 border border-gray-100">
                    <i class="fas fa-arrow-right text-xs"></i>
                    <span>العودة للأندية</span>
                </a>
                @if(!auth()->user()->hasRole('super-admin'))
                <button onclick="openMemberModal()"
                    class="px-8 py-4 bg-navy text-white rounded-2xl hover:bg-navy/90 shadow-xl font-cairo font-bold transition-all transform hover:-translate-y-1 flex items-center gap-3 group">
                    <i class="fas fa-user-plus text-gold group-hover:scale-110 transition-transform"></i>
                    <span>إضافة عضو جديد</span>
                </button>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Club Info Card -->
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white rounded-[2.5rem] p-10 shadow-sm border border-gray-100 relative overflow-hidden">
                    <div class="absolute -right-8 -bottom-8 opacity-[0.03] rotate-12">
                        <i class="fas fa-university text-9xl"></i>
                    </div>

                    <h3 class="text-xl font-black text-navy font-cairo mb-6 border-b border-gray-50 pb-4">وصف النادي</h3>
                    <p class="text-gray-500 font-almarai leading-relaxed mb-8">
                        {{ $club->description }}
                    </p>

                    <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-50">
                        <div class="bg-gray-50/50 p-4 rounded-2xl border border-gray-100 text-center">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">تاريخ
                                التأسيس</span>
                            <span class="text-navy font-bold text-sm">{{ $club->created_at->format('Y/m/d') }}</span>
                        </div>
                        <div class="bg-gold/5 p-4 rounded-2xl border border-gold/10 text-center">
                            <span class="text-[10px] font-black text-gold uppercase tracking-widest block mb-1">إجمالي
                                الأعضاء</span>
                            <span class="text-navy font-black text-lg">{{ $club->members->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Members Table Card -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-10 py-8 border-b border-gray-50 bg-gray-50/30">
                        <h2 class="text-xl font-black text-navy font-cairo">قائمة أعضاء النادي والقيادات</h2>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-right">
                            <thead class="bg-gray-50/50">
                                <tr>
                                    <th class="px-10 py-5 text-xs font-black text-gray-400 uppercase tracking-widest font-cairo">
                                        الطالب</th>
                                    <th class="px-10 py-5 text-xs font-black text-gray-400 uppercase tracking-widest font-cairo text-center">
                                        الصفة / الدور</th>
                                    <th class="px-10 py-5 text-xs font-black text-gray-400 uppercase tracking-widest font-cairo">
                                        تاريخ الانضمام</th>
                                    <th class="px-10 py-5 text-xs font-black text-gray-400 uppercase tracking-widest font-cairo">
                                        العمليات</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($club->members as $member)
                                    <tr class="hover:bg-gray-50/50 transition-colors group">
                                        <td class="px-10 py-6">
                                            <div class="flex items-center gap-4">
                                                <div class="w-12 h-12 bg-navy/5 rounded-xl flex items-center justify-center text-navy font-black text-sm">
                                                    {{ mb_substr($member->student->name_ar, 0, 1) }}
                                                </div>
                                                <div>
                                                    <p class="font-black text-navy font-cairo group-hover:text-gold transition-colors">
                                                        {{ $member->student->name_ar }}</p>
                                                    <p class="text-xs text-gray-400 font-mono tracking-wider">
                                                        {{ $member->student->barcode }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-10 py-6 text-center">
                                            <span class="px-4 py-1.5 rounded-full text-[11px] font-black font-cairo
                                                        @if($member->role == 'رئيس النادي') bg-gold/10 text-gold border border-gold/20
                                                        @elseif($member->role == 'نائب الرئيس') bg-blue-50 text-blue-600 border border-blue-100
                                                        @else bg-gray-100 text-gray-600 @endif">
                                                {{ $member->role }}
                                            </span>
                                        </td>
                                        <td class="px-10 py-6 font-almarai text-sm text-gray-500">
                                            {{ $member->joined_at->format('Y/m/d') }}
                                        </td>
                                        <td class="px-10 py-6">
                                            @if(!auth()->user()->hasRole('super-admin'))
                                                <form
                                                    action="{{ route('clubs.members.remove', [$club->id, $member->student->id]) }}"
                                                    method="POST"
                                                    data-confirm="هل أنت متأكد من رغبتك في إزالة هذا الطالب من النادي؟">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="w-10 h-10 rounded-xl bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-all flex items-center justify-center shadow-sm">
                                                        <i class="fas fa-user-minus text-xs"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-10 py-16 text-center">
                                            <div class="bg-gray-50 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-300">
                                                <i class="fas fa-users-slash text-2xl"></i>
                                            </div>
                                            <p class="text-gray-400 font-almarai">لم يتم إضافة أي أعضاء للنادي بعد</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Member Modal -->
    <div id="memberModal" class="fixed inset-0 bg-navy/60 backdrop-blur-sm hidden items-center justify-center z-[100] p-4">
        <div class="bg-white rounded-[2.5rem] p-10 max-w-xl w-full shadow-2xl transform transition-all border-t-8 border-gold">
            <div class="flex items-center gap-5 mb-10 border-b border-gray-50 pb-6">
                <div class="w-16 h-16 bg-gold/10 rounded-2xl flex items-center justify-center text-gold shadow-sm">
                    <i class="fas fa-user-plus text-2xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black font-cairo text-navy">إسناد طالب للنادي</h2>
                    <p class="text-gray-400 font-almarai text-sm italic">اختر الطالب وحدد دوره القيادي أو الإداري</p>
                </div>
            </div>

            <form action="{{ route('clubs.members.add', $club->id) }}" method="POST" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-black text-navy mb-3 font-cairo text-right">اختيار الطالب</label>
                        <select name="student_id" required
                            class="w-full px-5 py-4 rounded-2xl border border-gray-100 bg-gray-50/50 focus:bg-white focus:ring-4 focus:ring-navy/5 outline-none text-right transition-all font-almarai">
                            <option value="">-- اختر من قائمة الطلاب --</option>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}">{{ $student->name_ar }} ({{ $student->barcode }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-black text-navy mb-3 font-cairo text-right">الدور / الصفة</label>
                        <select name="role" required
                            class="w-full px-5 py-4 rounded-2xl border border-gray-100 bg-gray-50/50 focus:bg-white focus:ring-4 focus:ring-navy/5 outline-none text-right transition-all font-almarai">
                            <option value="عضو">عضو</option>
                            <option value="رئيس النادي">رئيس النادي</option>
                            <option value="نائب الرئيس">نائب الرئيس</option>
                            <option value="مسؤول ثقافي">مسؤول ثقافي</option>
                            <option value="مسؤول اجتماعي">مسؤول اجتماعي</option>
                            <option value="مسؤول فني">مسؤول فني</option>
                            <option value="مسؤول رياضي">مسؤول رياضي</option>
                        </select>
                    </div>
                </div>

                <div class="flex gap-4 pt-8">
                    <button type="submit"
                        class="flex-[3] bg-navy text-white py-5 rounded-2xl font-black text-lg shadow-xl hover:shadow-navy/40 transition-all flex items-center justify-center gap-3">
                        <i class="fas fa-check-circle text-gold"></i>
                        <span>تأكيد الإسناد</span>
                    </button>
                    <button type="button" onclick="closeMemberModal()"
                        class="flex-1 bg-gray-100 text-gray-400 py-5 rounded-2xl font-bold font-cairo hover:bg-gray-200 transition-colors">إلغاء</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openMemberModal() { showModal('memberModal'); }
        function closeMemberModal() { hideModal('memberModal'); }

        function showModal(id) {
            const m = document.getElementById(id);
            m.classList.remove('hidden');
            m.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function hideModal(id) {
            const m = document.getElementById(id);
            m.classList.add('hidden');
            m.classList.remove('flex');
            document.body.style.overflow = 'auto';
        }
    </script>
@endsection
