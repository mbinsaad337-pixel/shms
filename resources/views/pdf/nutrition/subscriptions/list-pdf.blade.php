@extends('pdf.layouts.master')

@section('content')

    <table class="data-table">
        <thead>
            <tr>
                <th class="text-center">#</th>
                <th>اسم الطالب</th>
                <th class="text-center">نوع الاشتراك</th>
                <th class="text-center">تاريخ البداية</th>
                <th class="text-center">تاريخ النهاية</th>
                <th class="text-center">عدد الأيام</th>
                <th class="text-center">المبلغ المستحق</th>
                <th class="text-center">المبلغ المسدد</th>
                <th class="text-center">المتبقي</th>
                <th class="text-center">الحالة</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $i => $sub)
                @php
                    $remaining = $sub->total_due - $sub->total_paid;
                    $types = [
                        'daily' => 'يومي',
                        'semi_monthly' => 'نصف شهري',
                        'monthly' => 'شهري',
                    ];
                @endphp
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td class="font-bold">{{ $sub->student->name_ar ?? '—' }}</td>
                    <td class="text-center">{{ $types[$sub->subscription_type] ?? $sub->subscription_type }}</td>
                    <td class="text-center  ">{{ $sub->start_date?->format('Y-m-d') ?? '—' }}</td>
                    <td class="text-center  ">{{ $sub->end_date?->format('Y-m-d') ?? '—' }}</td>
                    <td class="text-center  ">{{ $sub->days_count }}</td>
                    <td class="text-center  ">{{ number_format($sub->total_due, 2) }}</td>
                    <td class="text-center   text-success">{{ number_format($sub->total_paid, 2) }}</td>
                    <td class="text-center   {{ $remaining > 0 ? 'text-danger' : 'text-success' }} font-bold">{{ number_format($remaining, 2) }}</td>
                    <td class="text-center">
                        @if($sub->status === 'active')
                            <span class="badge badge-success">فعال</span>
                        @elseif($sub->status === 'suspended')
                            <span class="badge badge-warning">موقوف</span>
                        @elseif($sub->status === 'expired')
                            <span class="badge badge-secondary">منتهي</span>
                        @else
                            <span class="badge badge-danger">{{ $sub->status }}</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="10" class="text-center text-muted">لا توجد اشتراكات</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background: #f8fafc; font-weight: bold;">
                <td colspan="6" class="text-center">الإجمالي ({{ $data->count() }} اشتراك)</td>
                <td class="text-center  ">{{ number_format($data->sum('total_due'), 2) }}</td>
                <td class="text-center   text-success">{{ number_format($data->sum('total_paid'), 2) }}</td>
                <td class="text-center   text-danger">{{ number_format($data->sum('total_due') - $data->sum('total_paid'), 2) }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

@endsection
