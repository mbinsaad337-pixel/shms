@extends(auth()->user()->hasRole('nutrition-manager') ? 'layouts.nutrition' : 'layouts.app')
@section('title', 'إنشاء مجموعة QR')

@section('content')
    <div class="p-6 max-w-2xl mx-auto">
        <div class="flex items-center gap-4 mb-6">
            <a href="{{ route('nutrition.qr-groups.index') }}" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-arrow-right text-xl"></i>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-gray-800 font-cairo">إنشاء مجموعة QR</h2>
                <p class="text-gray-400 text-sm font-almarai">اختر الأصدقاء ليُستخدم QR واحد للجميع</p>
            </div>
        </div>

        <form action="{{ route('nutrition.qr-groups.store') }}" method="POST">
            @csrf
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <!-- Current user always included -->
                <div class="flex items-center gap-3 p-4 bg-teal-50 border border-teal-100 rounded-2xl mb-5">
                    <div class="w-10 h-10 bg-teal-600 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-white text-sm"></i>
                    </div>
                    <div>
                        <p class="font-bold text-teal-800 font-cairo">{{ $student->name_ar }}</p>
                        <p class="text-xs text-teal-500 font-cairo">أنت (مُضاف تلقائياً)</p>
                    </div>
                </div>

                <h3 class="font-bold text-gray-700 font-cairo mb-3">اختر من تريد إضافتهم:</h3>

                @if($students->isEmpty())
                    <div class="text-center py-8 text-gray-300">
                        <i class="fas fa-user-friends text-3xl mb-2 block"></i>
                        <p class="font-cairo text-sm">لا يوجد مشتركون آخرون في نفس المركز</p>
                    </div>
                @else
                    <div class="space-y-2 max-h-80 overflow-y-auto">
                        @foreach($students as $s)
                            <label
                                class="flex items-center gap-3 p-3 border border-gray-100 rounded-xl hover:bg-teal-50 cursor-pointer transition-all has-[:checked]:border-teal-300 has-[:checked]:bg-teal-50">
                                <input type="checkbox" name="student_ids[]" value="{{ $s->id }}"
                                    class="w-4 h-4 text-teal-600 rounded">
                                <div>
                                    <p class="font-bold text-gray-800 font-cairo text-sm">{{ $s->name_ar }}</p>
                                    <p class="text-xs text-gray-400  ">{{ $s->university_id }}</p>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    <div class="flex gap-3 mt-3">
                        <button type="button"
                            onclick="document.querySelectorAll('[name=\'student_ids[]\']').forEach(c=>c.checked=true)"
                            class="text-xs px-3 py-1.5 bg-teal-100 text-teal-700 rounded-lg font-cairo font-bold">تحديد
                            الكل</button>
                        <button type="button"
                            onclick="document.querySelectorAll('[name=\'student_ids[]\']').forEach(c=>c.checked=false)"
                            class="text-xs px-3 py-1.5 bg-gray-100 text-gray-600 rounded-lg font-cairo font-bold">إلغاء
                            التحديد</button>
                    </div>
                @endif

                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-3 mt-5 text-sm font-cairo text-yellow-700">
                    <i class="fas fa-info-circle ml-1"></i>
                    QR المجمع صالح ليوم واحد فقط (حتى نهاية اليوم) ويُستخدم مرة واحدة فقط.
                </div>

                <div class="flex justify-end gap-3 mt-5">
                    <a href="{{ route('nutrition.qr-groups.index') }}"
                        class="px-6 py-3 border border-gray-200 rounded-xl font-cairo font-bold text-gray-600">إلغاء</a>
                    <button type="submit"
                        class="px-8 py-3 bg-teal-600 hover:bg-teal-700 text-white rounded-xl font-bold font-cairo shadow-lg shadow-teal-200">
                        <i class="fas fa-qrcode ml-2"></i> إنشاء QR المجمع
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection
