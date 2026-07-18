@extends('layouts.app')

@section('title', 'سجل السندات والعمليات')

@section('content')
    <div class="container mx-auto px-6 py-8">
        @include('partials.print_header', [
            'title' => 'تقرير السندات والعمليات المالية', 
            'number' => 'REP-' . date('Ymd'),
            'department' => 'الإدارة المالية المركزية'
        ])

        <div class="mb-8 flex justify-between items-center bg-white p-6 rounded-2xl border-l-8 border-gold shadow-sm no-print">
            <div>
                <h1 class="text-3xl font-black text-navy font-cairo">سجل السندات المالية المركزية</h1>
                <p class="text-gray-400 font-almarai text-sm mt-1">كشف بجميع السندات والعمليات المالية المسجلة عبر النظام</p>
            </div>
            <button onclick="window.print()"
                class="bg-navy text-white px-8 py-3 rounded-xl shadow-lg font-cairo font-bold hover:bg-navy/90 transition-all flex items-center gap-2 group">
                <i class="fas fa-print text-gold group-hover:scale-110 transition-transform"></i>
                <span>طباعة التقرير</span>
            </button>
        </div>

        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-right">
                <thead>
                    <tr class="bg-navy text-white font-cairo">
                        <th class="px-6 py-4 font-bold">رقم السند</th>
                        <th class="px-6 py-4 font-bold">المركز</th>
                        <th class="px-6 py-4 font-bold text-center">النوع</th>
                        <th class="px-6 py-4 font-bold text-center">المبلغ</th>
                        <th class="px-6 py-4 font-bold">البيان</th>
                        <th class="px-6 py-4 font-bold text-center">التاريخ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($data as $voucher)
                        <tr class="hover:bg-blue-50/30 transition-colors">
                            <td class="px-6 py-4 font-mono font-bold text-primary">{{ $voucher->voucher_number }}</td>
                            <td class="px-6 py-4 font-almarai">{{ $voucher->center->name }}</td>
                            <td class="px-6 py-4">
                                <span
                                    class="px-2 py-1 rounded text-xs font-bold {{ $voucher->type == 'payment' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }} font-almarai">
                                    {{ $voucher->type == 'payment' ? 'صرف' : 'قبض' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-bold font-almarai">{{ number_format($voucher->amount, 2) }}</td>
                            <td class="px-6 py-4 font-almarai text-sm text-gray-500 max-w-xs truncate">
                                {{ $voucher->description }}
                            </td>
                            <td class="px-6 py-4 font-almarai text-xs text-gray-400">{{ $voucher->date }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @include('partials.print_footer')
    </div>
@endsection
