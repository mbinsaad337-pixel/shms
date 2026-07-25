@extends('pdf.layouts.master')

@section('content')
<div class="header" style="margin-bottom: 20px;">
    <h2 style="color: #004274; text-align: center; margin-bottom: 5px;">تقرير حضور وغياب الوجبات</h2>
    <p style="text-align: center; color: #666; font-size: 12px; margin-top: 0;">تاريخ التقرير: {{ \Carbon\Carbon::parse($date)->format('Y-m-d') }}</p>
    @if($meal_type)
        @php $mealNames = ['breakfast' => 'الفطور', 'lunch' => 'الغداء', 'dinner' => 'العشاء']; @endphp
        <p style="text-align: center; color: #666; font-size: 12px; margin-top: 0;">الوجبة المحددة: {{ $mealNames[$meal_type] ?? $meal_type }}</p>
    @endif
</div>

@if(is_countable($reports) ? count($reports) > 0 : (method_exists($reports, 'count') ? $reports->count() > 0 : !empty($reports)))
    <table class="table">
        <thead>
            <tr>
                <th>م</th>
                <th>اسم الطالب</th>
                <th>الرقم الجامعي</th>
                <th>الوجبة</th>
                <th>الحالة</th>
                <th>توقيت الإشعار</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reports as $index => $report)
                @php $mealNames = ['breakfast' => 'الفطور', 'lunch' => 'الغداء', 'dinner' => 'العشاء']; @endphp
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ $report->student?->name_ar ?? 'طالب غير موجود' }}</td>
                    <td style="text-align: center;">{{ $report->student?->university_id }}</td>
                    <td style="text-align: center;">{{ $mealNames[$report->meal_type] ?? $report->meal_type }}</td>
                    <td style="text-align: center;">
                        @if($report->status == 'late')
                            <span style="color: #ca8a04; font-weight: bold;">سيتأخر</span>
                        @else
                            <span style="color: #dc2626; font-weight: bold;">غائب</span>
                        @endif
                    </td>
                    <td style="text-align: center; direction: ltr;">
                        {{ $report->updated_at->format('H:i:s') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 20px; font-size: 12px;">
        <p><strong>إجمالي المتأخرين:</strong> {{ $reports->where('status', 'late')->count() }}</p>
        <p><strong>إجمالي الغائبين:</strong> {{ $reports->where('status', 'absent')->count() }}</p>
    </div>
@else
    <div style="text-align: center; margin-top: 50px; padding: 20px; border: 1px dashed #ccc;">
        <p style="color: #666; font-size: 14px;">لا توجد إشعارات مسجلة لهذه المعايير.</p>
    </div>
@endif

<div class="footer-signatures" style="margin-top: 50px;">
    <table style="width: 100%; border: none;">
        <tr>
            <td style="border: none; text-align: center; width: 33%;">
                <p style="font-weight: bold; margin-bottom: 30px;">مشرف التغذية</p>
                <p>........................</p>
            </td>
            <td style="border: none; text-align: center; width: 33%;">
                <p style="font-weight: bold; margin-bottom: 30px;">الختم</p>
                <p></p>
            </td>
            <td style="border: none; text-align: center; width: 33%;">
                <p style="font-weight: bold; margin-bottom: 30px;">مدير المركز</p>
                <p>........................</p>
            </td>
        </tr>
    </table>
</div>
@endsection
