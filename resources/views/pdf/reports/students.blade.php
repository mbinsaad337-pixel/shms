@extends('pdf.layouts.master')

@section('content')
<table class="data-table">
    <thead>
        <tr>
            <th style="width: 5%;">#</th>
            <th>اسم الطالب</th>
            <th>الرقم الجامعي</th>
            <th>المركز</th>
            <th>الغرفة</th>
            <th>المستوى الدراسي</th>
            <th class="text-center">الحالة</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $index => $student)
        <tr>
            <td class="text-center">{{ $index + 1 }}</td>
            <td class="font-bold">{{ $student->name_ar }}</td>
            <td class="font-mono">{{ $student->university_id }}</td>
            <td>
                <span class="badge badge-primary">{{ $student->center->name }}</span>
            </td>
            <td>{{ $student->room->room_number ?? '---' }}</td>
            <td>{{ $student->academic_level ?? '---' }}</td>
            <td class="text-center">
                @php
                    $statusMap = [
                        'registered' => ['حجز مبدئي', 'badge-info'],
                        'residing' => ['مقيم', 'badge-success'],
                        'left' => ['مخلي طرفه', 'badge-secondary'],
                        'graduated' => ['متخرج', 'badge-warning'],
                        'suspended' => ['موقوف', 'badge-danger'],
                    ];
                    $status = $statusMap[$student->status] ?? [$student->status, 'badge-secondary'];
                @endphp
                <span class="badge {{ $status[1] }}">{{ $status[0] }}</span>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<table class="signatures-table">
    <tr>
        <td>
            <div class="sign-line"></div>
            <div class="sign-title">أعده / مسؤول البيانات</div>
            <div class="sign-name">{{ $exportUser ?? '' }}</div>
        </td>
        <td>
            <div class="sign-line"></div>
            <div class="sign-title">راجعه / المسؤول الإداري</div>
        </td>
        <td>
            <div class="sign-line"></div>
            <div class="sign-title">اعتمده / مدير المركز</div>
        </td>
    </tr>
</table>
@endsection
