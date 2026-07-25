@extends('pdf.layouts.master')

@section('content')
<table class="data-table">
    <thead>
        <tr>
            <th style="width: 5%;">#</th>
            <th style="width: 25%;">الطالب</th>
            <th style="width: 20%;">نوع المخالفة</th>
            <th style="width: 20%;">العقوبة المطبقة</th>
            <th class="text-center" style="width: 15%;">تاريخ التطبيق</th>
            <th class="text-center" style="width: 15%;">مُطبّق العقوبة</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $index => $penalty)
        <tr>
            <td class="text-center">{{ $index + 1 }}</td>
            <td class="font-bold">{{ $penalty->student->name_ar }}</td>
            <td class="text-danger">{{ $penalty->violation->type ?? 'غير محدد' }}</td>
            <td class="text-warning font-bold">
                @php
                    $penaltyTypes = [
                        'verbal_warning' => 'إنذار شفهي',
                        'written_warning' => 'إنذار كتابي',
                        'service_suspension' => 'إيقاف خدمات',
                        'temporary_suspension' => 'إيقاف مؤقت',
                        'expulsion' => 'فصل نهائي',
                    ];
                @endphp
                {{ $penaltyTypes[$penalty->type] ?? $penalty->type }}
            </td>
            <td class="text-center font-mono text-sm">{{ $penalty->created_at->format('Y/m/d') }}</td>
            <td class="text-center text-sm">{{ $penalty->appliedBy->name ?? 'النظام' }}</td>
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
