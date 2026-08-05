@extends(auth()->user()->hasRole('nutrition-manager') ? 'layouts.nutrition' : 'layouts.app')
@section('title', 'الموردون')

@section('content')
    <div class="p-6 max-w-7xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 font-cairo">الموردون</h2>
                <p class="text-gray-400 text-sm font-almarai">إدارة موردي قسم التغذية وكشوف حساباتهم</p>
            </div>
            <a href="{{ route('nutrition.suppliers.create') }}"
                class="inline-flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white px-5 py-2.5 rounded-xl font-bold font-cairo text-sm shadow-lg shadow-orange-200 transition-all">
                <i class="fas fa-plus"></i> إضافة مورد
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-right px-5 py-3 text-xs font-bold text-gray-500 font-cairo">المورد</th>
                        <th class="text-right px-5 py-3 text-xs font-bold text-gray-500 font-cairo">رقم التواصل</th>
                        <th class="text-right px-5 py-3 text-xs font-bold text-gray-500 font-cairo">العنوان</th>
                        <th class="text-center px-5 py-3 text-xs font-bold text-gray-500 font-cairo">مدين (يستحق)</th>
                        <th class="text-center px-5 py-3 text-xs font-bold text-gray-500 font-cairo">دائن (مدفوع)</th>
                        <th class="text-center px-5 py-3 text-xs font-bold text-gray-500 font-cairo">الرصيد</th>
                        <th class="text-center px-5 py-3 text-xs font-bold text-gray-500 font-cairo">الفواتير</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @if(is_countable($suppliers) ? count($suppliers) > 0 : (method_exists($suppliers, 'count') ? $suppliers->count() > 0 : !empty($suppliers)))
    @foreach($suppliers as $supplier)
                        @php $net = $supplier->balance_debit - $supplier->balance_credit; @endphp
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-5 py-4">
                                <p class="font-bold text-gray-800 font-cairo">{{ $supplier->name }}</p>
                                @if($supplier->email)
                                    <p class="text-xs text-gray-400">{{ $supplier->email }}</p>
                                @endif
                            </td>
                            <td class="px-5 py-4   text-gray-600 text-sm" dir="ltr">{{ $supplier->phone ?? '—' }}</td>
                            <td class="px-5 py-4 text-gray-500 font-cairo text-sm">{{ $supplier->address ?? '—' }}</td>
                            <td class="px-5 py-4 text-center font-bold text-red-600  ">
                                {{ number_format($supplier->balance_debit, 2) }}</td>
                            <td class="px-5 py-4 text-center font-bold text-green-600  ">
                                {{ number_format($supplier->balance_credit, 2) }}</td>
                            <td class="px-5 py-4 text-center font-bold   text-lg
                                {{ $net > 0 ? 'text-red-600' : ($net < 0 ? 'text-green-600' : 'text-gray-400') }}">
                                {{ number_format(abs($net), 2) }}
                                <span
                                    class="text-xs font-cairo font-normal text-gray-400">{{ $net > 0 ? 'دائن' : ($net < 0 ? 'مدين' : '') }}</span>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <span class="bg-orange-100 text-orange-700 px-2 py-0.5 rounded-lg text-xs font-bold font-cairo">
                                    {{ $supplier->invoices_count }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('nutrition.suppliers.show', $supplier) }}"
                                        class="w-8 h-8 bg-orange-50 hover:bg-orange-100 text-orange-600 rounded-lg flex items-center justify-center text-xs"
                                        title="كشف الحساب">
                                        <i class="fas fa-receipt"></i>
                                    </a>
                                    <a href="{{ route('nutrition.suppliers.edit', $supplier) }}"
                                        class="w-8 h-8 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center text-xs"
                                        title="تعديل">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('nutrition.suppliers.destroy', $supplier) }}" method="POST"
                                        data-confirm="هل تريد حذف هذا المورد؟">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="w-8 h-8 bg-red-50 hover:bg-red-100 text-red-500 rounded-lg flex items-center justify-center text-xs">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
@else
                        <tr>
                            <td colspan="8" class="py-16 text-center text-gray-300">
                                <i class="fas fa-truck text-5xl mb-3 block"></i>
                                <p class="font-cairo">لم يتم إضافة موردين بعد</p>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $suppliers->links() }}</div>
    </div>
@endsection
