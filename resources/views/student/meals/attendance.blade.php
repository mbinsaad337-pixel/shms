@extends('layouts.app')
@section('title', 'إدارة الحضور للوجبات')

@section('content')
    <div class="p-6 max-w-4xl mx-auto">
        <div class="mb-8">
            <h2 class="text-3xl font-bold text-gray-800 font-cairo">إدارة الحضور للوجبات</h2>
            <p class="text-gray-500 font-almarai mt-2">حدد حالتك لوجبات اليوم لتحسين كفاءة التوزيع</p>
        <div class="mt-4 inline-flex items-center gap-2 bg-gray-50 px-4 py-2 rounded-xl border border-gray-100 shadow-inner">
            <i class="fas fa-clock text-primary"></i>
            <span id="student-live-clock" class="  text-sm font-bold text-gray-600">
                {{ now()->translatedFormat('h:i:s A') }}
            </span>
        </div>
        <script>
            function updateStudentClock() {
                const now = new Date();
                const options = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true };
                document.getElementById('student-live-clock').textContent = now.toLocaleTimeString('ar-SA', options);
            }
            setInterval(updateStudentClock, 1000);
        </script>
        </div>

        @if(session('success'))
            <div
                class="mb-6 p-4 bg-emerald-50 border border-emerald-100 rounded-2xl flex items-center gap-3 animate-in fade-in slide-in-from-top-2">
                <div
                    class="w-10 h-10 bg-emerald-500 rounded-full flex items-center justify-center text-white shadow-lg shadow-emerald-200">
                    <i class="fas fa-check"></i>
                </div>
                <p class="text-emerald-800 font-bold font-cairo">{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div
                class="mb-6 p-4 bg-red-50 border border-red-100 rounded-2xl flex items-center gap-3 animate-in fade-in slide-in-from-top-2">
                <div
                    class="w-10 h-10 bg-red-500 rounded-full flex items-center justify-center text-white shadow-lg shadow-red-200">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <p class="text-red-800 font-bold font-cairo">{{ session('error') }}</p>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($todayMeals as $meal)
                <div
                    class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden transition-all hover:shadow-xl hover:-translate-y-1">
                    <!-- Header -->
                    <div
                        class="p-6 bg-gradient-to-br {{ $meal->type === 'breakfast' ? 'from-amber-400 to-orange-500' : ($meal->type === 'lunch' ? 'from-teal-500 to-emerald-600' : 'from-indigo-500 to-blue-700') }} text-white">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold font-almarai opacity-80">الوقت: {{ $meal->time }}</span>
                            <i
                                class="fas {{ $meal->type === 'breakfast' ? 'fa-sun' : ($meal->type === 'lunch' ? 'fa-utensils' : 'fa-moon') }} text-xl opacity-50"></i>
                        </div>
                        <h3 class="text-2xl font-black font-cairo">{{ $meal->label }}</h3>
                        <p class="text-xs opacity-90 mt-1 font-almarai">{{ date('Y-m-d') }}</p>
                    </div>

                    <!-- Status Badge -->
                    <div class="px-6 pt-6 pb-2 text-center">
                        @php
                            $statusCls = [
                                'normal' => 'bg-green-50 text-green-700 border-green-100',
                                'late' => 'bg-yellow-50 text-yellow-700 border-yellow-100',
                                'absent' => 'bg-red-50 text-red-700 border-red-100',
                            ][$meal->status];
                            $statusLbl = [
                                'normal' => 'حاضر (افتراضي)',
                                'late' => 'سأتأخر',
                                'absent' => 'لن أحضر',
                            ][$meal->status];
                        @endphp
                        <span
                            class="inline-block px-4 py-1.5 rounded-full text-sm font-bold font-cairo border {{ $statusCls }}">
                            الحالة الحالية: {{ $statusLbl }}
                        </span>
                    </div>

                    <!-- Form -->
                    <div class="p-6">
                        @if($meal->can_edit)
                            <form action="{{ route('student.meals.attendance.update') }}" method="POST" class="space-y-3">
                                @csrf
                                <input type="hidden" name="meal_type" value="{{ $meal->type }}">

                                <div class="flex flex-col gap-2">
                                    <button type="submit" name="status" value="normal"
                                        class="w-full py-2.5 rounded-xl font-bold font-cairo text-sm transition-all {{ $meal->status === 'normal' ? 'bg-green-600 text-white shadow-lg shadow-green-200' : 'bg-gray-50 text-gray-500 hover:bg-gray-100' }}">
                                        حضور
                                    </button>

                                    @if(!$meal->is_late_expired || $meal->status === 'late')
                                        <button type="submit" name="status" value="late"
                                            {{ $meal->is_late_expired ? 'disabled' : '' }}
                                            class="w-full py-2.5 rounded-xl font-bold font-cairo text-sm transition-all {{ $meal->status === 'late' ? 'bg-yellow-500 text-white shadow-lg shadow-yellow-200' : ($meal->is_late_expired ? 'bg-gray-100 text-gray-300 cursor-not-allowed' : 'bg-gray-50 text-gray-500 hover:bg-gray-100') }}">
                                            سأتأخر
                                            @if($meal->is_late_expired) (انتهى الوقت) @endif
                                        </button>
                                    @endif

                                    @if(!$meal->is_absent_expired || $meal->status === 'absent')
                                        <button type="submit" name="status" value="absent"
                                            {{ $meal->is_absent_expired ? 'disabled' : '' }}
                                            class="w-full py-2.5 rounded-xl font-bold font-cairo text-sm transition-all {{ $meal->status === 'absent' ? 'bg-red-500 text-white shadow-lg shadow-red-200' : ($meal->is_absent_expired ? 'bg-gray-100 text-gray-300 cursor-not-allowed' : 'bg-gray-50 text-gray-500 hover:bg-gray-100') }}">
                                            لن أحضر
                                            @if($meal->is_absent_expired) (انتهى الوقت) @endif
                                        </button>
                                    @endif

                                    @if($meal->is_late_expired && $meal->status !== 'late')
                                        <p class="text-[10px] text-yellow-600 font-bold font-cairo text-center italic mt-1">تجاوزت الموعد النهائي للإشعار بالتأخر</p>
                                    @endif
                                    @if($meal->is_absent_expired && $meal->status !== 'absent')
                                        <p class="text-[10px] text-red-500 font-bold font-cairo text-center italic mt-1">تجاوزت الموعد النهائي للإشعار بالغياب</p>
                                    @endif
                                </div>
                            </form>
                        @else
                            <div class="bg-gray-50 rounded-2xl p-4 text-center border border-gray-100">
                                <i class="fas fa-lock text-gray-300 mb-2 block"></i>
                                <p class="text-sm text-gray-400 font-cairo">لا يمكن التعديل الآن</p>
                                <p class="text-xs text-red-400 font-bold font-cairo mt-1">({{ $meal->lock_reason }})</p>
                            </div>
                        @endif
                    </div>

                    @if($meal->status === 'late')
                        <div class="px-6 pb-6 pt-0">
                            <div class="p-3 bg-yellow-50 border border-yellow-100 rounded-xl flex items-start gap-2">
                                <i class="fas fa-info-circle text-yellow-500 mt-0.5"></i>
                                <p class="text-xs text-yellow-700 font-almarai leading-relaxed">
                                    سيتم السماح لك باستلام الوجبة حتى في حال تأخرك، مع إصدار تنبيه للمسؤول.
                                </p>
                            </div>
                        </div>
                    @elseif($meal->status === 'absent')
                        <div class="px-6 pb-6 pt-0">
                            <div class="p-3 bg-red-50 border border-red-100 rounded-xl flex items-start gap-2">
                                <i class="fas fa-times-circle text-red-400 mt-0.5"></i>
                                <p class="text-xs text-red-600 font-almarai leading-relaxed">
                                    تم إلغاء حصتك لهذه الوجبة. لا يمكنك استلامها بعد تسجيل الغياب.
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <!-- Instructions -->
        <div class="mt-10 bg-white rounded-3xl p-8 border border-gray-50 shadow-sm">
            <h4 class="text-lg font-bold text-gray-800 font-cairo mb-4 border-b pb-2">شروط وأحكام النظام</h4>
            <ul class="space-y-3">
                <li class="flex items-start gap-3">
                    <div
                        class="w-6 h-6 bg-teal-100 rounded-full flex items-center justify-center text-teal-600 flex-shrink-0 text-xs mt-0.5">
                        1</div>
                    <p class="text-sm text-gray-500 font-almarai">لا يمكن تغيير الحالة بعد مرور 15 دقيقة من الوقت المحدد
                        لبدء الوجبة.</p>
                </li>
                <li class="flex items-start gap-3">
                    <div
                        class="w-6 h-6 bg-teal-100 rounded-full flex items-center justify-center text-teal-600 flex-shrink-0 text-xs mt-0.5">
                        2</div>
                    <p class="text-sm text-gray-500 font-almarai">يغلق النظام إمكانية التعديل فور بدء عملية التوزيع الفعلية
                        في المركز.</p>
                </li>
                <li class="flex items-start gap-3">
                    <div
                        class="w-6 h-6 bg-teal-100 rounded-full flex items-center justify-center text-teal-600 flex-shrink-0 text-xs mt-0.5">
                        3</div>
                    <p class="text-sm text-gray-500 font-almarai">في حال عدم تحديد أي حالة، يعتبر الطالب "حاضراً" بشكل
                        تلقائي.</p>
                </li>
            </ul>
        </div>
    </div>
@endsection
