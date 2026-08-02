@extends('layouts.nutrition')
@section('title', 'سندات الصرف والقبض')

@section('content')
    <div class="p-6 max-w-7xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 font-cairo">سندات الصرف والقبض</h2>
                <p class="text-gray-400 text-sm font-almarai">المعاملات المالية لقسم التغذية</p>
            </div>
            <a href="{{ route('nutrition.vouchers.create') }}"
                class="inline-flex items-center gap-2 bg-yellow-500 hover:bg-yellow-600 text-white px-5 py-2.5 rounded-xl font-bold font-cairo text-sm shadow-lg shadow-yellow-200">
                <i class="fas fa-plus"></i> سند جديد
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-right px-5 py-3 text-xs font-bold text-gray-500 font-cairo">رقم السند</th>
                        <th class="text-center px-5 py-3 text-xs font-bold text-gray-500 font-cairo">النوع</th>
                        <th class="text-right px-5 py-3 text-xs font-bold text-gray-500 font-cairo">الطرف الآخر</th>
                        <th class="text-right px-5 py-3 text-xs font-bold text-gray-500 font-cairo">البيان</th>
                        <th class="text-center px-5 py-3 text-xs font-bold text-gray-500 font-cairo">المبلغ</th>
                        <th class="text-center px-5 py-3 text-xs font-bold text-gray-500 font-cairo">التاريخ</th>
                        <th class="text-center px-5 py-3 text-xs font-bold text-gray-500 font-cairo">الحالة</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @if ($vouchers->count() > 0)
                        @foreach ($vouchers as $voucher)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-5 py-4   font-bold text-gray-700">{{ $voucher->voucher_number }}</td>
                            <td class="px-5 py-4 text-center">
                                <span
                                    class="px-3 py-1.5 rounded-lg text-xs font-bold
                                    {{ $voucher->type === 'payment' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                                    <i
                                        class="fas {{ $voucher->type === 'payment' ? 'fa-arrow-up' : 'fa-arrow-down' }} ml-1"></i>
                                    {{ $voucher->getTypeLabel() }}
                                </span>
                            </td>
                            <td class="px-5 py-4 font-cairo font-bold text-gray-800 text-sm">
                                {{ $voucher->supplier?->name ?? $voucher->student?->name_ar ?? '—' }}
                            </td>
                            <td class="px-5 py-4 text-gray-500 font-almarai text-sm truncate max-w-xs">
                                {{ $voucher->description }}</td>
                            <td class="px-5 py-4 text-center font-black   text-lg
                                {{ $voucher->type === 'payment' ? 'text-red-600' : 'text-green-600' }}">
                                {{ number_format($voucher->amount, 2) }} <span
                                    class="text-xs font-cairo font-normal text-gray-400">ر.ي</span>
                            </td>
                            <td class="px-5 py-4 text-center   text-gray-600 text-sm">
                                {{ $voucher->voucher_date->format('Y-m-d') }}</td>
                            <td class="px-5 py-4 text-center">
                                <span
                                    class="px-2 py-1 rounded-lg text-xs font-bold {{ $voucher->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-500' }}">
                                    {{ $voucher->status === 'active' ? 'فعال' : 'ملغي' }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('nutrition.vouchers.show', $voucher) }}"
                                        class="w-8 h-8 bg-yellow-50 hover:bg-yellow-100 text-yellow-600 rounded-lg flex items-center justify-center text-xs">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if ($voucher->status === 'active')
                                        <form action="{{ route('nutrition.vouchers.cancel', $voucher) }}" method="POST"
                                            onsubmit="return confirm('هل تريد إلغاء هذا السند؟')">
                                            @csrf
                                            <button type="submit" title="إلغاء"
                                                class="w-8 h-8 bg-amber-50 hover:bg-amber-100 text-amber-500 rounded-lg flex items-center justify-center text-xs">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('nutrition.vouchers.destroy', $voucher) }}" method="POST"
                                        onsubmit="return confirm('هل أنت متأكد من حذف هذا السند نهائياً؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="حذف"
                                            class="w-8 h-8 bg-red-50 hover:bg-red-100 text-red-500 rounded-lg flex items-center justify-center text-xs">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="8" class="py-16 text-center text-gray-300">
                                <i class="fas fa-money-bill-wave text-5xl mb-3 block"></i>
                                <p class="font-cairo">لا توجد سندات حتى الآن</p>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $vouchers->links() }}</div>
    </div>
@endsection
