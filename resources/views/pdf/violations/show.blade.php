@extends('pdf.layouts.master')

@section('styles')
<style>
    .violation-card {
        border: 2px solid #004274;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        background: #fff;
    }
    .violation-header-box {
        background: #fee2e2;
        border: 1px solid #f87171;
        padding: 15px;
        text-align: center;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    .violation-title {
        font-size: 20px;
        font-weight: 900;
        color: #b91c1c;
    }
    .info-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }
    .info-table td {
        padding: 10px;
        border: 1px solid #e2e8f0;
    }
    .info-label {
        font-weight: bold;
        color: #64748b;
        background: #f8fafc;
        width: 25%;
    }
    .info-value {
        font-weight: bold;
        color: #0f172a;
    }
    .description-box {
        background: #fafbfc;
        border: 1px solid #e2e8f0;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        line-height: 1.8;
    }
</style>
@endsection

@section('content')

<div class="violation-card">
    <div class="violation-header-box">
        <div style="font-size: 14px; font-weight: bold; margin-bottom: 5px; color: #7f1d1d;">تقرير مخالفة مسجلة</div>
        <div class="violation-title">
            {{ $violation->type }}
        </div>
        <div style="font-size: 11px; margin-top: 5px; color: #b91c1c;">
            مستوى المخالفة: 
            @if($violation->severity == 'minor')
                بسيطة
            @elseif($violation->severity == 'moderate')
                متوسطة
            @else
                جسيمة
            @endif
        </div>
    </div>

    <table class="info-table">
        <tr>
            <td class="info-label">اسم الطالب</td>
            <td class="info-value">{{ $violation->student->name_ar ?? 'غير معروف' }}</td>
            <td class="info-label">الرقم الجامعي</td>
            <td class="info-value font-mono">{{ $violation->student->university_id ?? '-' }}</td>
        </tr>
        <tr>
            <td class="info-label">المركز السكني</td>
            <td class="info-value">{{ $violation->center->name ?? '-' }}</td>
            <td class="info-label">تاريخ المخالفة</td>
            <td class="info-value font-mono">{{ $violation->violation_date ? \Carbon\Carbon::parse($violation->violation_date)->format('Y-m-d') : '-' }}</td>
        </tr>
        <tr>
            <td class="info-label">العقوبة المطبقة</td>
            <td class="info-value text-warning font-bold" colspan="3">
                @if($violation->penalty)
                    @php
                        $penaltyTypes = [
                            'verbal_warning' => 'تنبيه شفوي',
                            'written_warning' => 'إنذار كتابي',
                            'service_suspension' => 'إيقاف خدمات',
                            'temporary_suspension' => 'فصل مؤقت من السكن',
                            'expulsion' => 'فصل نهائي'
                        ];
                    @endphp
                    {{ $penaltyTypes[$violation->penalty->type] ?? $violation->penalty->type }} 
                    @if($violation->penalty->description)
                        <span style="color: #64748b; font-size: 10px; font-weight: normal;">({{ $violation->penalty->description }})</span>
                    @endif
                @else
                    قيد المراجعة / لم يتم اتخاذ إجراء بعد
                @endif
            </td>
        </tr>
    </table>

    <div style="font-weight: bold; color: #004274; margin-bottom: 8px;">وصف وتفاصيل المخالفة:</div>
    <div class="description-box">
        {{ $violation->description ?: 'لا يوجد وصف إضافي مسجل لهذه المخالفة.' }}
    </div>

    <table class="signatures-table">
    <tr>
        <td>
            <div class="sign-line"></div>
            <div class="sign-title">مشرف الطلاب</div>
            <div class="sign-name">
                 @php
                      $housing_manager = \App\Models\User::role('housing-manager')->where('center_id',$violation->student->center_id)->get();
                @endphp
                {{ $housing_manager->count() ===1 ? $housing_manager->first()->name : '' }}</div>
        </td>
        <td>
            <div class="sign-line"></div>
            <div class="sign-title">توقيع الطالب</div>
               
                 
        </td>
        <td>
            <div class="sign-line"></div>
            <div class="sign-title">مدير المركز</div>
            <div class="sign-name">
                @php
                      $center_manager = \App\Models\User::role('center-manager')->where('center_id',$violation->student->center_id)->get();
                @endphp
                {{ $center_manager->count() ===1 ? $center_manager->first()->name : '' }}
            </div>
        </td>
    </tr>
</table>
</div>

@endsection
