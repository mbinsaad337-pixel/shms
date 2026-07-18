@extends('layouts.app')

@section('title', 'تقرير أرصدة الصناديق')

@section('content')
    <div class="container mx-auto px-6 py-8">
        @include('partials.print_header', [
            'title' => 'تقرير السيولة النقدية وأرصدة الصناديق', 
            'number' => 'REP-F-' . date('Ymd'),
            'department' => 'الرقابة المالية - إدارة الصناديق'
        ])

        <div class="mb-8 flex flex-col lg:flex-row justify-between items-center gap-6 bg-white p-6 rounded-2xl border-l-8 border-gold shadow-sm no-print">
            <div class="w-full lg:w-auto text-center lg:text-right">
                <h1 class="text-3xl font-black text-navy font-cairo">تقرير السيولة وأرصدة الصناديق</h1>
                <p class="text-gray-400 font-almarai text-sm mt-1">عرض حالة السيولة النقدية والأرصدة الجارية في كافة الصناديق والمراكز</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-4 items-center w-full lg:w-auto">
                <form action="{{ route('reports.show', 'funds') }}" method="GET" class="flex gap-2">
                    @if($isExecutive)
                    <select name="center_id" onchange="this.form.submit()" class="px-4 py-2 rounded-xl border border-gray-100 bg-gray-50 focus:bg-white text-sm font-cairo">
                        <option value="">كل المراكز</option>
                        @foreach($centers as $c)
                            <option value="{{ $c->id }}" {{ request('center_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                    @endif
                    <select name="month" onchange="this.form.submit()" class="px-4 py-2 rounded-xl border border-gray-100 bg-gray-50 focus:bg-white text-sm font-cairo">
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" {{ request('month', $month) == $m ? 'selected' : '' }}>شهر {{ $m }}</option>
                        @endforeach
                    </select>
                    <select name="year" onchange="this.form.submit()" class="px-4 py-2 rounded-xl border border-gray-100 bg-gray-50 focus:bg-white text-sm font-cairo">
                        @foreach(range(now()->year - 2, now()->year + 1) as $y)
                            <option value="{{ $y }}" {{ request('year', $year) == $y ? 'selected' : '' }}>سنة {{ $y }}</option>
                        @endforeach
                    </select>
                </form>
                <button onclick="window.print()"
                    class="bg-navy text-white px-8 py-3 rounded-xl shadow-lg font-cairo font-bold hover:bg-navy/90 transition-all flex items-center gap-2 group">
                    <i class="fas fa-print text-gold group-hover:scale-110 transition-transform"></i>
                    <span class="hidden md:inline">طباعة التقرير</span>
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-8">
            @php $totalGlobal = 0; @endphp
            @foreach($data->groupBy('center_id') as $centerId => $funds)
                @php $centerName = $funds->first()->center->name; @endphp
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden mb-6">
                    <div class="bg-navy px-8 py-4 border-b border-gold/30">
                        <h3 class="text-xl font-bold text-white font-cairo">{{ $centerName }}</h3>
                    </div>
                    <table class="w-full text-right">
                        <thead>
                            <tr class="bg-gray-50 text-navy text-sm border-b font-bold font-cairo">
                                <th class="px-8 py-4">اسم الصندوق</th>
                                <th class="px-8 py-4 text-center border-r border-gray-200">الرصيد المعتمد</th>
                                <th class="px-8 py-4 text-center border-r border-gray-200">الرصيد الحالي</th>
                                <th class="px-8 py-4 text-center border-r border-gray-200">رصيد التصفية</th>
                                <th class="px-8 py-4 text-center border-r border-gray-200">الفترة (شهر/سنة)</th>
                                <th class="px-8 py-4 text-center border-r border-gray-200 no-print">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($funds as $fund)
                                @php
                                    $approvedBalance      = $fund->budgetItems->sum('approved_amount');
                                    $settlementDetail     = $settlementBalances[$fund->id] ?? null;
                                    $settlementClosing    = $settlementDetail ? $settlementDetail->closing_balance : null;
                                @endphp
                                <tr class="border-b hover:bg-orange-50/20 transition-colors">
                                    <td class="px-8 py-4 font-almarai border-l border-gray-100">{{ $fund->name }}</td>
                                    <td class="px-8 py-4 text-center font-bold text-navy font-almarai text-lg border-l border-gray-100">
                                        {{ number_format($approvedBalance, 2) }}
                                    </td>
                                    <td class="px-8 py-4 text-center font-bold text-green-600 font-almarai text-lg border-l border-gray-100">
                                        {{ number_format($fund->balance, 2) }}
                                    </td>
                                    <td class="px-8 py-4 text-center font-almarai text-sm border-l border-gray-100">
                                        @if($settlementClosing !== null)
                                            <span class="font-bold {{ $settlementClosing > 0 ? 'text-orange-600' : 'text-gray-500' }} text-base">
                                                {{ number_format($settlementClosing, 2) }}
                                            </span>
                                        @else
                                            <span class="text-xs text-gray-300 bg-gray-50 px-2 py-1 rounded-full">لا تصفية</span>
                                        @endif
                                    </td>
                                    <td class="px-8 py-4 text-center font-almarai text-sm text-gray-500 border-l border-gray-100">
                                        <div class="px-3 py-1 bg-gray-50 rounded-full inline-block">{{ $month }} / {{ $year }}</div>
                                    </td>
                                    <td class="px-8 py-4 text-center no-print">
                                        <a href="{{ route('vouchers.index', ['fund_id' => $fund->id, 'month' => $month, 'year' => $year, 'center_id' => $fund->center_id]) }}" target="_blank"
                                           class="px-4 py-2 bg-navy/5 text-navy rounded-lg text-xs font-bold hover:bg-navy hover:text-white transition-colors flex items-center justify-center gap-2 w-max mx-auto">
                                            <i class="fas fa-file-invoice-dollar"></i>
                                            عرض السندات
                                        </a>
                                    </td>
                                </tr>
                                @php $totalGlobal += $fund->balance; @endphp
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endforeach

            <div class="bg-navy p-10 rounded-3xl text-white shadow-2xl border-r-8 border-gold flex justify-between items-center relative overflow-hidden">
                <div class="absolute -left-10 -top-10 text-white/5 text-9xl">
                    <i class="fas fa-vault"></i>
                </div>
                <h2 class="text-2xl font-black font-cairo relative z-10">إجمالي السيولة النقدية بالنظام</h2>
                <div class="text-4xl font-black font-almarai relative z-10 text-gold">{{ number_format($totalGlobal, 2) }} <span
                        class="text-xl font-bold text-white/80">ريال يمني</span></div>
            </div>
        </div>

        @include('partials.print_footer')
    </div>
@endsection
