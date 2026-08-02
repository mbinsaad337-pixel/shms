@extends(auth()->user()->hasRole('nutrition-manager') ? 'layouts.nutrition' : 'layouts.app')
@section('title', 'ضبط مواعيد الوجبات')

@section('content')
    <div class="p-6 max-w-5xl mx-auto">
        <div class="mb-8 flex justify-between items-end">
            <div>
                <h2 class="text-3xl font-bold text-gray-800 font-cairo">ضبط مواعيد الوجبات والإشعارات</h2>
                <p class="text-gray-500 font-almarai mt-2">قم بتحديد أوقات الوجبات والمواعيد النهائية للإشعارات (التأخر
                    والغياب)</p>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 rounded-2xl flex items-center gap-3">
                <div class="w-10 h-10 bg-emerald-500 rounded-full flex items-center justify-center text-white">
                    <i class="fas fa-check"></i>
                </div>
                <p class="text-emerald-800 font-bold font-cairo">{{ session('success') }}</p>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($mealTypes as $type => $label)
                @php $s = $schedules->get($type); @endphp
                <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
                    <div
                        class="p-6 bg-gradient-to-br {{ $type === 'breakfast' ? 'from-amber-400 to-orange-500' : ($type === 'lunch' ? 'from-teal-500 to-emerald-600' : 'from-indigo-500 to-blue-700') }} text-white">
                        <h3 class="text-2xl font-black font-cairo flex items-center gap-3">
                            <i
                                class="fas {{ $type === 'breakfast' ? 'fa-sun' : ($type === 'lunch' ? 'fa-utensils' : 'fa-moon') }}"></i>
                            {{ $label }}
                        </h3>
                    </div>

                    <form action="{{ route('nutrition.schedules.store') }}" method="POST" class="p-8 space-y-6">
                        @csrf
                        <input type="hidden" name="meal_type" value="{{ $type }}">

                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-400 mb-2 font-cairo">وقت بدء الوجبة</label>
                                    <input type="time" name="start_time"
                                        value="{{ $s ? \Carbon\Carbon::parse($s->start_time)->format('H:i') : '' }}" required
                                        class="w-full bg-gray-50 border-gray-100 rounded-xl focus:ring-primary focus:border-primary  ">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-400 mb-2 font-cairo">وقت انتهاء
                                        الوجبة</label>
                                    <input type="time" name="end_time"
                                        value="{{ $s ? \Carbon\Carbon::parse($s->end_time)->format('H:i') : '' }}" required
                                        class="w-full bg-gray-50 border-gray-100 rounded-xl focus:ring-primary focus:border-primary  ">
                                </div>
                            </div>

                            <hr class="border-gray-50">

                            <div>
                                <label class="block text-xs font-bold text-orange-400 mb-2 font-cairo italic">آخر موعد لإشعار
                                    التأخر</label>
                                <input type="time" name="late_deadline"
                                    value="{{ $s ? \Carbon\Carbon::parse($s->late_deadline)->format('H:i') : '' }}" required
                                    class="w-full bg-orange-50 border-orange-100 rounded-xl focus:ring-orange-500 focus:border-orange-500   text-orange-700">
                                <p class="text-[10px] text-gray-400 mt-1 font-almarai italic">* لا يمكن للطالب اختيار "سأتأخر"
                                    بعد هذا الوقت</p>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-red-400 mb-2 font-cairo italic">آخر موعد لإشعار
                                    الغياب</label>
                                <input type="time" name="absent_deadline"
                                    value="{{ $s ? \Carbon\Carbon::parse($s->absent_deadline)->format('H:i') : '' }}" required
                                    class="w-full bg-red-50 border-red-100 rounded-xl focus:ring-red-500 focus:border-red-500   text-red-700">
                                <p class="text-[10px] text-gray-400 mt-1 font-almarai italic">* لا يمكن للطالب اختيار "لن أحضر"
                                    بعد هذا الوقت</p>
                            </div>
                        </div>

                        <button type="submit"
                            class="w-full bg-gray-800 text-white py-3 rounded-2xl font-bold font-cairo hover:bg-gray-900 transition-all shadow-lg hover:shadow-gray-200">
                            حفظ الإعدادات
                        </button>
                    </form>
                </div>
            @endforeach
        </div>

        <div class="mt-8 bg-blue-50 border border-blue-100 p-6 rounded-[2rem] flex items-start gap-4">
            <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-blue-500 shadow-sm shrink-0">
                <i class="fas fa-info-circle text-xl"></i>
            </div>
            <div>
                <h4 class="font-bold text-blue-900 font-cairo mb-1">كيف تعمل هذه المواعيد؟</h4>
                <p class="text-sm text-blue-700 font-almarai leading-relaxed">
                    تستخدم هذه المواعيد لتقييد الطلاب من تسجيل حالاتهم. بمجرد تجاوز "الموعد النهائي"، سيختفي الزر المقابل من
                    واجهة الطالب.
                    <br>
                    <strong>ملاحظة:</strong> إذا لم يتم تحديد مواعيد، سيعتمد النظام على وقت البدء + 15 دقيقة كحد أقصى
                    افتراضي.
                </p>
            </div>
        </div>
    </div>
@endsection
