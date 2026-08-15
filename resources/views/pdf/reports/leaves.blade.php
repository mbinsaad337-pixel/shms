@extends('pdf.layouts.master')

@section('content')
<table class="data-table">
    <thead>
        <tr>
            <th style="width: 5%;">#</th>
            <th>الطالب</th>
            <th>النوع</th>
            <th class="text-center">تاريخ / وقت المغادرة</th>
            <th class="text-center">تاريخ / وقت العودة</th>
            <th class="text-center">الحالة</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $index => $leave)
        <tr>
            <td class="text-center">{{ $index + 1 }}</td>
            <td class="font-bold">
                {{ $leave->student->name_ar ?? '---' }}
                @if($leave->submitted_by_student) <div style="font-size: 8px; color: #666;">(طلب الطالب)</div> @endif
            </td>
            <td class="text-center">
                @php $typeLabels = ['temporary'=>'مؤقت','vacation'=>'إجازة','medical'=>'طبي','lateness'=>'تأخير']; @endphp
                {{ $typeLabels[$leave->type] ?? $leave->type }}
            </td>
            <td class="text-center   text-sm">
                {{ $leave->departure_date->format('Y/m/d') }}<br>
                {{ $leave->departure_time ?? '' }}
            </td>
            <td class="text-center   text-sm">
                {{ $leave->expected_return_date ? $leave->expected_return_date->format('Y/m/d') : '---' }}<br>
                {{ $leave->expected_return_time ?? '' }}
            </td>
            <td class="text-center">
                @php
                    $stLabels = ['pending'=>'قيد الانتظار','approved'=>'موافق','rejected'=>'مرفوض','returned'=>'عاد','not_returned'=>'لم يعد'];
                @endphp
                {{ $stLabels[$leave->status] ?? $leave->status }}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<table class="signatures-table">
    <tr>
        @php
            $firstStudent = $data->first()?->student;
            $centerId = $firstStudent?->center_id;
            $isAcademic = $firstStudent?->program?->code === 'academic';
            $supervisorRole = $isAcademic ? 'academic-supervisor' : 'cooperative-supervisor';
            $supervisorTitle = $isAcademic ? 'مشرف الطلاب الأكاديمي' : 'مشرف الطلاب التعاوني';
            $supervisor = \App\Models\User::role($supervisorRole)->where('center_id', $centerId)->get();
            $center_manager = \App\Models\User::role('center-manager')->where('center_id', $centerId)->get();
        @endphp
        <td>
            <div class="sign-line"></div>
            <div class="sign-title">{{ $supervisorTitle }}</div>
            <div class="sign-name">{{ $supervisor->count() === 1 ? $supervisor->first()->name : '' }}</div>
        </td>
        <td>
            <div class="sign-line"></div>
            <div class="sign-title">مدير المركز</div>
            <div class="sign-name">{{ $center_manager->count() === 1 ? $center_manager->first()->name : '' }}</div>
        </td>
    </tr>
</table>
@endsection
