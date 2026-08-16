@extends('pdf.layouts.master')

@section('content')
    @php
        $moduleLabels = [
            'administrative' => ['الإجراءات الإدارية', 'badge-warning'],
            'activities' => ['الأنشطة والأخبار', 'badge-info'],
            'financial' => ['النظام المالي', 'badge-success'],
            'nutrition' => ['نظام التغذية', 'badge-primary'],
            'quran' => ['الحلقات القرآنية', 'badge-success'],
            'academic' => ['الأكاديمي والدرجات', 'badge-primary'],
            'rooms' => ['تسكين الغرف', 'badge-danger'],
            'vehicles' => ['مركبات الطلاب', 'badge-warning'],
            'complaints' => ['الشكاوى والإشعارات', 'badge-info'],
            'graduates' => ['الطلاب الخريجون', 'badge-info'],
            'funds' => ['الصناديق المالية', 'badge-success'],
            'clubs' => ['الأندية الطلابية', 'badge-info'],
        ];
    @endphp

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;" class="text-center">#</th>
                <th style="width: 8%;" class="text-center">السنة</th>
                <th style="width: 14%;">القسم والقطاع</th>
                <th style="width: 32%;">عنوان وتفاصيل السجل المؤرشف</th>
                <th style="width: 18%;">الطالب المعني</th>
                <th style="width: 11%;" class="text-center">المبلغ (ريال)</th>
                <th style="width: 12%;" class="text-center">التاريخ الأصلي</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $item)
                @php
                    $lbl = $moduleLabels[$item->module] ?? [$item->module, 'badge-secondary'];
                @endphp
                <tr class="{{ $index % 2 == 0 ? 'even' : 'odd' }}">
                    <td class="text-center font-bold text-muted">{{ $index + 1 }}</td>
                    <td class="text-center font-bold text-navy">{{ $item->year }}</td>
                    <td>
                        <span class="badge {{ $lbl[1] }}">{{ $lbl[0] }}</span>
                    </td>
                    <td class="font-bold">{{ $item->title }}</td>
                    <td>{{ $item->student_name ?: ($item->student ? $item->student->name_ar : '-') }}</td>
                    <td class="text-center font-bold {{ $item->amount > 0 ? 'text-success' : 'text-muted' }}">
                        {{ $item->amount > 0 ? number_format($item->amount, 2) : '-' }}
                    </td>
                    <td class="text-center text-sm">
                        {{ $item->record_date ? $item->record_date->format('Y/m/d') : $item->created_at->format('Y/m/d') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted" style="padding: 20px;">
                        لا توجد سجلات مؤرشفة تنطبق على معايير البحث والتصفية
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if (isset($stats['إجمالي المبالغ المالية']))
        <table style="width: 100%; margin-top: 10px; border: none; border-collapse: collapse;">
            <tr>
                <td style="border: none; padding: 6px;">
                    <div
                        style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 8px 12px; text-align: center;">
                        <span style="font-size: 10px; color: #64748b; font-weight: bold;">إجمالي المبالغ المالية للسجلات
                            المعروضة: </span>
                        <span
                            style="font-size: 13px; font-weight: bold; color: #15803d;">{{ $stats['إجمالي المبالغ المالية'] }}</span>
                    </div>
                </td>
            </tr>
        </table>
    @endif

    <table class="signatures-table">
        <tr>
            <td>
                <div class="sign-line"></div>
                <div class="sign-title">مدير المركز</div>
                @php
                    $center_manager = \App\Models\User::role('center-manager')
                        ->where('center_id', $item->center_id)
                        ->first();
                @endphp
                <div class="sign-name">{{ $center_manager ? $center_manager->name : '' }}</div>
            </td>
            <td>
                <div class="sign-line"></div>
                <div class="sign-title">مدير قسم المراكز الطلابية</div>
                @php
                    $super_admin = \App\Models\User::role('super-admin')->first();
                @endphp
                <div class="sign-name">{{ $super_admin ? $super_admin->name : '' }}</div>
            </td>
        </tr>
    </table>
@endsection
