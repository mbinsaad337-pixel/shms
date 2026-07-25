@extends('pdf.layouts.master')

@section('content')

<div class="detail-card">
    <div class="detail-card-header">تفاصيل الفعالية</div>
    <div class="detail-card-body">
        <table class="two-col-table">
            <tr>
                <td class="col-right">
                    <div class="detail-row">
                        <div class="detail-label">اسم الفعالية</div>
                        <div class="detail-value text-navy">{{ $activity->name }}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">النادي المنظم</div>
                        <div class="detail-value">{{ $activity->club->name ?? '---' }}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">تاريخ ووقت البداية</div>
                        <div class="detail-value font-mono">{{ $activity->start_date }} {{ $activity->start_time ? ' - ' . $activity->start_time : '' }}</div>
                    </div>
                </td>
                <td class="col-spacer"></td>
                <td class="col-left">
                    <div class="detail-row">
                        <div class="detail-label">المكان</div>
                        <div class="detail-value">{{ $activity->location }}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">الطلاب المستهدفين</div>
                        <div class="detail-value font-mono">{{ $activity->targetedStudents->count() }} طالب</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">الطلاب الغائبين</div>
                        <div class="detail-value font-mono text-danger">{{ count($absentees) }} طالب</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</div>

<h3 style="font-size: 14px; color: #b91c1c; margin: 20px 0 10px; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px;">قائمة الطلاب الغائبين</h3>

<table class="data-table">
    <thead>
        <tr>
            <th style="width: 5%;">#</th>
            <th>الرقم الجامعي</th>
            <th>اسم الطالب</th>
            <th>المستوى الدراسي</th>
            <th>الغرفة</th>
            <th>رقم الهاتف</th>
        </tr>
    </thead>
    <tbody>
        @foreach($absentees as $index => $student)
        <tr>
            <td class="text-center">{{ $index + 1 }}</td>
            <td class="font-mono">{{ $student->university_id }}</td>
            <td class="font-bold">{{ $student->name_ar }}</td>
            <td>{{ $student->academic_level ?? '---' }}</td>
            <td class="text-center font-mono">{{ $student->room->room_number ?? '---' }}</td>
            <td class="font-mono" dir="ltr">{{ $student->phone }}</td>
        </tr>
        @endforeach
        
        @if(count($absentees) == 0)
        <tr>
            <td colspan="6" class="text-center text-success" style="padding: 20px;">جميع الطلاب المستهدفين حضروا الفعالية</td>
        </tr>
        @endif
    </tbody>
</table>

<table class="signatures-table avoid-break">
    <tr>
        <td>
            <div class="sign-line"></div>
            <div class="sign-title">أعده / مسؤول الأنشطة</div>
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
