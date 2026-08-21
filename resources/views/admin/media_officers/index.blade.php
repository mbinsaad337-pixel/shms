@extends('layouts.app')
@section('title', 'إدارة مسؤول الإعلام - قسم المراكز الطلابية')

@section('content')
    <div
        class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-6 rounded-3xl border-l-8 border-gold shadow-sm">
        <div class="flex items-center gap-4">
            <div
                class="w-14 h-14 bg-navy text-gold rounded-2xl flex items-center justify-center text-2xl shadow-lg shadow-navy/20">
                <i class="fas fa-bullhorn"></i>
            </div>
            <div>
                <h1 class="text-2xl md:text-3xl font-black text-navy font-cairo">إدارة مسؤولي الإعلام المركزية</h1>
                <p class="text-gray-400 font-almarai text-sm mt-0.5">تعيين وإدارة مسؤولي الإعلام المعتمدين لمراجعة إعلانات
                    المراكز الطلابية</p>
            </div>
        </div>
        <div>
            <a href="{{ route('media-officers.create') }}"
                class="px-6 py-3.5 bg-gold text-navy hover:bg-yellow-500 font-black font-cairo rounded-2xl shadow-lg shadow-gold/20 transition-all flex items-center gap-2 text-sm">
                <i class="fas fa-user-plus"></i> إضافة مسؤول إعلام جديد
            </a>
        </div>
    </div>

    @if (session('success'))
        <div
            class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl font-cairo font-bold flex items-center gap-3">
            <i class="fas fa-check-circle text-xl"></i> {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-black text-navy font-cairo text-lg flex items-center gap-2">
                <i class="fas fa-users-cog text-gold"></i> قائمة مسؤولي الإعلام المسجلين بالنظام
            </h3>
            <span class="text-xs font-bold text-gray-400 font-almarai">الإجمالي: {{ $mediaOfficers->count() }} مسؤول</span>
        </div>

        @if ($mediaOfficers->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-right font-almarai">
                    <thead class="bg-gray-50 text-gray-500 font-cairo text-xs font-bold uppercase">
                        <tr>
                            <th class="px-6 py-4">الاسم الكامل</th>
                            <th class="px-6 py-4">البريد الإلكتروني</th>
                            <th class="px-6 py-4">رقم الجوال (واتساب)</th>
                            <th class="px-6 py-4">المركز التابع له</th>
                            <th class="px-6 py-4">الحالة</th>
                            <th class="px-6 py-4 text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @foreach ($mediaOfficers as $officer)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 font-bold text-navy font-cairo">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 bg-navy/10 text-navy rounded-full flex items-center justify-center font-black">
                                            <i class="fas fa-user-shield"></i>
                                        </div>
                                        <div>
                                            <p class="font-bold text-navy font-cairo">{{ $officer->name }}</p>
                                            <span class="text-[10px] text-gray-400">مسؤول إعلام معتمد</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-600 font-mono text-xs">{{ $officer->email }}</td>
                                <td class="px-6 py-4 text-gray-700">
                                    @if ($officer->phone)
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $officer->phone) }}"
                                            target="_blank"
                                            class="inline-flex items-center gap-1.5 text-emerald-600 hover:text-emerald-700 font-bold bg-emerald-50 px-3 py-1 rounded-xl">
                                            <i class="fab fa-whatsapp"></i> {{ $officer->phone }}
                                        </a>
                                    @else
                                        <span class="text-gray-300 text-xs">غير محدد</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if ($officer->center)
                                        <span class="px-3 py-1 bg-navy/5 text-navy rounded-xl text-xs font-bold font-cairo">
                                            <i class="fas fa-building text-gold ml-1"></i> {{ $officer->center->name }}
                                        </span>
                                    @else
                                        <span
                                            class="px-3 py-1 bg-purple-50 text-purple-700 rounded-xl text-xs font-bold font-cairo">
                                            <i class="fas fa-globe ml-1"></i> عام (جميع المراكز)
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="px-3 py-1 rounded-full text-xs font-bold font-cairo {{ $officer->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                        {{ $officer->is_active ? 'نشط' : 'معطل' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('media-officers.edit', $officer) }}"
                                            class="w-9 h-9 bg-gold/10 text-gold hover:bg-amber-600 hover:text-white rounded-xl transition-all flex items-center justify-center text-sm"
                                            title="تعديل">
                                            <i class="fas fa-pencil"></i>
                                        </a>

                                        <form action="{{ route('media-officers.toggle', $officer) }}" method="POST"
                                            class="inline">
                                            @csrf
                                            <button type="submit"
                                                class="w-9 h-9 {{ $officer->is_active ? 'bg-amber-50 text-amber-600 hover:bg-amber-600' : 'bg-emerald-50 text-emerald-600 hover:bg-emerald-600' }} hover:text-white rounded-xl transition-all flex items-center justify-center text-sm"
                                                title="{{ $officer->is_active ? 'تعطيل' : 'تفعيل' }}">
                                                <i class="fas {{ $officer->is_active ? 'fa-ban' : 'fa-check' }}"></i>
                                            </button>
                                        </form>

                                        <form action="{{ route('media-officers.destroy', $officer) }}" method="POST"
                                            class="inline" data-confirm='هل أنت متأكد من حذف حساب مسؤول الإعلام؟'>
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="w-9 h-9 bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white rounded-xl transition-all flex items-center justify-center text-sm"
                                                title="حذف">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-16 text-center">
                <div
                    class="w-20 h-20 bg-amber-50 rounded-full flex items-center justify-center mx-auto mb-4 text-amber-500">
                    <i class="fas fa-user-shield text-3xl"></i>
                </div>
                <h3 class="text-xl font-black text-navy font-cairo">لا يوجد مسؤول إعلام مسجل حالياً</h3>
                <p class="text-gray-400 font-almarai mt-1 mb-6 text-sm">قم بإنشاء أول حساب مسؤول إعلام ليتلقى الإعلانات
                    الواردة من المراكز للبت فيها.</p>
                <a href="{{ route('media-officers.create') }}"
                    class="px-6 py-3.5 bg-navy text-white font-bold font-cairo rounded-2xl shadow-lg hover:bg-navy/90 transition-all text-sm">
                    إضافة مسؤول إعلام
                </a>
            </div>
        @endif
    </div>
@endsection
