@extends('pdf.layouts.master')

@section('content')
<table class="data-table">
    <thead>
        <tr>
            <th style="width: 5%;">#</th>
            <th>الطالب</th>
            <th>المركز</th>
            <th>نوع المخالفة</th>
            <th class="text-center">التاريخ</th>
            <th class="text-center">إجراء العقوبة</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $index => $violation)
        <tr>
            <td class="text-center">{{ $index + 1 }}</td>
            <td class="font-bold">{{ $violation->student->name_ar }}</td>
            <td>{{ $violation->center->name }}</td>
            <td class="text-danger">{{ $violation->type }}</td>
            <td class="text-center font-mono text-sm">{{ $violation->created_at->format('Y/m/d') }}</td>
            <td class="text-center">
                <span class="badge badge-warning">{{ $violation->penalty->name ?? 'قيد المراجعة' }}</span>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<table class="signatures-table">
    <tr>
        <td>
            <div class="sign-line"></div>
            <div class="sign-title">أعده / مسؤول الانضباط</div>
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
