@extends('layouts.app')

@section('title', 'طلبات الاستئذان')

@section('content')
<div class="container mx-auto px-4 py-6" x-data="{ showForm: false }">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between items-center bg-white p-6 rounded-3xl border-r-8 border-navy shadow-sm gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-black text-navy font-cairo">طلبات الاستئذان الخاصة بي</h1>
            <p class="text-gray-400 font-almarai text-sm mt-1">تقديم طلبات الاستئذان وانتظار موافقة المشرف</p>
        </div>
        <button @click="showForm = !showForm"
            class="px-6 py-3 bg-navy text-white rounded-2xl font-bold font-cairo hover:bg-[#083358] transition-all flex items-center gap-2 shadow-lg shadow-navy/15">
            <i class="fas fa-plus-circle"></i>
            <span x-text="showForm ? 'إخفاء النموذج' : 'إرسال طلب استئذان'"></span>
        </button>
    </div>

    {{-- Pending Circle Absences Alert --}}
    @if($pendingAbsences->count() > 0)
    <div class="bg-red-50 border border-red-200 rounded-2xl p-5 mb-6">
        <h3 class="font-black text-red-700 font-cairo mb-3 flex items-center gap-2">
            <i class="fas fa-exclamation-triangle"></i>
            تنبيهات الحلقات القرآنية ({{ $pendingAbsences->count() }} غياب غير معالج)
        </h3>
        <div class="space-y-2">
            @foreach($pendingAbsences as $abs)
            <div class="flex items-center justify-between bg-white rounded-xl p-3 border border-red-100">
                <div>
                    <p class="font-bold text-navy font-cairo text-sm">{{ $abs->session->circle->name ?? 'حلقة قرآنية' }}</p>
                    <p class="text-xs text-gray-400 font-almarai">{{ $abs->session->date ?? '' }}</p>
                </div>
                <span class="px-3 py-1 bg-red-100 text-red-600 rounded-lg text-xs font-black font-cairo">غائب</span>
            </div>
            @endforeach
        </div>
        <p class="text-xs text-red-500 mt-3 font-almarai">
            <i class="fas fa-info-circle ml-1"></i>
            في حال كان غيابك بعذر، تواصل مع المشرف لمراجعة السجل.
        </p>
    </div>
    @endif

    {{-- New Request Form --}}
    <div x-show="showForm" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 mb-6">
        <h2 class="text-lg font-black text-navy font-cairo mb-5 flex items-center gap-2">
            <i class="fas fa-paper-plane text-navy/60"></i> تقديم طلب استئذان جديد
        </h2>
        <form action="{{ route('student.leave-requests.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-5">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1.5 font-cairo">نوع الاستئذان <span class="text-red-500">*</span></label>
                <select name="type" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-navy text-sm font-almarai outline-none focus:ring-2 focus:ring-navy/10">
                    <option value="temporary">استئذان مؤقت</option>
                    <option value="vacation">إجازة</option>
                    <option value="medical">إجازة طبية</option>
                    <option value="lateness">تأخير</option>
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5 font-cairo">تاريخ المغادرة <span class="text-red-500">*</span></label>
                    <input type="date" name="departure_date" value="{{ date('Y-m-d') }}" class="w-full px-3 py-2.5 rounded-xl border border-gray-200 bg-gray-50 text-sm font-mono outline-none focus:ring-2 focus:ring-navy/10">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5 font-cairo">وقت المغادرة</label>
                    <input type="time" name="departure_time" class="w-full px-3 py-2.5 rounded-xl border border-gray-200 bg-gray-50 text-sm font-mono outline-none focus:ring-2 focus:ring-navy/10">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5 font-cairo">تاريخ العودة المتوقع</label>
                    <input type="date" name="expected_return_date" class="w-full px-3 py-2.5 rounded-xl border border-gray-200 bg-gray-50 text-sm font-mono outline-none focus:ring-2 focus:ring-navy/10">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5 font-cairo">وقت العودة المتوقع</label>
                    <input type="time" name="expected_return_time" class="w-full px-3 py-2.5 rounded-xl border border-gray-200 bg-gray-50 text-sm font-mono outline-none focus:ring-2 focus:ring-navy/10">
                </div>
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-gray-500 mb-1.5 font-cairo">سبب الاستئذان <span class="text-red-500">*</span></label>
                <textarea name="reason" rows="3" placeholder="اذكر سبب طلب الاستئذان..." class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-navy text-sm font-almarai outline-none focus:ring-2 focus:ring-navy/10 resize-none"></textarea>
            </div>
            <div class="md:col-span-2">
                <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 mb-4">
                    <p class="text-xs text-blue-600 font-almarai">
                        <i class="fas fa-info-circle ml-1"></i>
                        سيتم إرسال طلبك إلى المشرف للموافقة عليه. لن تُعتبر مستأذناً حتى يوافق المشرف على الطلب.
                        في حال الرفض، قد يُحوَّل الطلب إلى مخالفة.
                    </p>
                </div>
                <button type="submit" class="px-8 py-3 bg-navy text-white rounded-xl font-black font-cairo hover:bg-[#083358] transition-all flex items-center gap-2 shadow-lg shadow-navy/15">
                    <i class="fas fa-paper-plane"></i> إرسال الطلب للمشرف
                </button>
            </div>
        </form>
    </div>

    {{-- My Requests List --}}
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-base font-black text-navy font-cairo">طلباتي السابقة</h2>
            <span class="text-xs text-gray-400 font-cairo">{{ $leaves->total() }} طلب</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-right text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-5 py-3 text-xs font-black text-gray-400 font-cairo">النوع</th>
                        <th class="px-5 py-3 text-xs font-black text-gray-400 font-cairo text-center">تاريخ / وقت المغادرة</th>
                        <th class="px-5 py-3 text-xs font-black text-gray-400 font-cairo text-center">العودة المتوقعة</th>
                        <th class="px-5 py-3 text-xs font-black text-gray-400 font-cairo">السبب</th>
                        <th class="px-5 py-3 text-xs font-black text-gray-400 font-cairo text-center">الحالة</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($leaves as $l)
                    <tr class="hover:bg-gray-50/60 transition-colors">
                        <td class="px-5 py-4">
                            @php
                                $typeColors = ['temporary'=>'bg-blue-50 text-blue-600','vacation'=>'bg-purple-50 text-purple-600','medical'=>'bg-green-50 text-green-600','lateness'=>'bg-orange-50 text-orange-600'];
                                $typeLabels = ['temporary'=>'مؤقت','vacation'=>'إجازة','medical'=>'طبي','lateness'=>'تأخير'];
                            @endphp
                            <span class="px-2.5 py-1 rounded-lg text-xs font-black {{ $typeColors[$l->type] ?? 'bg-gray-100 text-gray-600' }}">{{ $typeLabels[$l->type] ?? $l->type }}</span>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <span class="text-xs font-mono text-gray-600">{{ $l->departure_date->format('Y-m-d') }}</span>
                            @if($l->departure_time)<span class="block text-[10px] text-gray-400">{{ $l->departure_time }}</span>@endif
                        </td>
                        <td class="px-5 py-4 text-center">
                            @if($l->expected_return_date)
                            <span class="text-xs font-mono text-gray-600">{{ $l->expected_return_date->format('Y-m-d') }}</span>
                            @if($l->expected_return_time)<span class="block text-[10px] text-gray-400">{{ $l->expected_return_time }}</span>@endif
                            @else
                            <span class="text-xs text-gray-400">---</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <p class="text-xs text-gray-500 font-almarai line-clamp-2 max-w-xs">{{ $l->reason }}</p>
                        </td>
                        <td class="px-5 py-4 text-center">
                            @php
                                $stColors = ['pending'=>'bg-amber-50 text-amber-600','approved'=>'bg-green-50 text-green-600','rejected'=>'bg-red-50 text-red-600','returned'=>'bg-blue-50 text-blue-600'];
                                $stLabels = ['pending'=>'قيد الانتظار','approved'=>'موافق عليه','rejected'=>'مرفوض','returned'=>'تم العودة'];
                            @endphp
                            <span class="px-2.5 py-1.5 rounded-lg text-xs font-black {{ $stColors[$l->status] ?? 'bg-gray-100 text-gray-600' }}">{{ $stLabels[$l->status] ?? $l->status }}</span>
                            @if($l->status === 'rejected' && $l->rejection_reason)
                            <p class="text-[10px] text-red-500 mt-1 font-almarai">{{ $l->rejection_reason }}</p>
                            @endif
                            @if($l->converted_to_violation)
                            <span class="block text-[10px] text-red-600 font-cairo mt-1"><i class="fas fa-gavel"></i> تحولت لمخالفة</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-16 text-center">
                            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3 opacity-30">
                                <i class="fas fa-door-open text-3xl text-gray-400"></i>
                            </div>
                            <p class="text-navy font-black font-cairo">لا توجد طلبات استئذان سابقة</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($leaves->hasPages())
        <div class="px-5 py-4 border-t border-gray-50">{{ $leaves->links() }}</div>
        @endif
    </div>
</div>
@endsection
