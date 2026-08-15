@extends('pdf.layouts.master')

@section('content')
<div style="padding: 20px;">

    <h2 style="text-align: center; color: #002244; margin-bottom: 30px;">إقرار وتعهد</h2>

    <table style="width: 100%; border: none; margin-bottom: 20px;">
        <tr>
            <td style="width: 50%;"><strong>اسم الطالب:</strong> {{ $commitment->student->name_ar }}</td>
            <td style="width: 50%;"><strong>رقم الطالب:</strong> <span class=" ">{{ $commitment->student->student_number }}</span></td>
        </tr>
        <tr>
            <td><strong>المركز:</strong> {{ $commitment->student->center->name ?? '' }}</td>
            <td><strong>التاريخ:</strong> <span class=" ">{{ $commitment->date->format('Y/m/d') }}</span></td>
        </tr>
    </table>

    <div style="border: 1px solid #ccc; padding: 20px; border-radius: 8px; margin-top: 30px; line-height: 1.8; text-align: justify; background-color: #fcfcfc;">
        <h4 style="margin-top: 0;">{{ $commitment->title ?? 'نص التعهد' }}</h4>
        <p style="font-size: 16px;">
            {!! nl2br(e($commitment->text)) !!}
        </p>
    </div>

    @if($commitment->violation)
    <div style="margin-top: 20px; padding: 15px; border: 1px solid #ffcccc; background-color: #fff5f5; border-radius: 8px;">
        <strong>مخالفة مرتبطة:</strong> {{ $commitment->violation->type }} - (مستوى: {{ $commitment->violation->severity }})
    </div>
    @endif

    <table style="width: 100%; margin-top: 60px; text-align: center; border: none;">
        <tr>
            <td style="width: {{ $commitment->requires_guardian_signature ? '33%' : '50%' }};">
                <div style="margin-bottom: 40px;"><strong>توقيع الطالب</strong></div>
                <div style="border-bottom: 1px solid #000; width: 70%; margin: 0 auto;"></div>
            </td>
            
            @if($commitment->requires_guardian_signature)
            <td style="width: 33%;">
                <div style="margin-bottom: 40px;"><strong>توقيع ولي الأمر</strong></div>
                <div style="border-bottom: 1px solid #000; width: 70%; margin: 0 auto;"></div>
            </td>
            @endif

            <td style="width: {{ $commitment->requires_guardian_signature ? '33%' : '50%' }};">
                @php
                    $isAcademic = $commitment->student?->program?->code === 'academic';
                    $supervisorRole = $isAcademic ? 'academic-supervisor' : 'cooperative-supervisor';
                    $supervisorTitle = $isAcademic ? 'مشرف الطلاب الأكاديمي' : 'مشرف الطلاب التعاوني';
                    $supervisor = \App\Models\User::role($supervisorRole)->where('center_id', $commitment->student?->center_id)->get();
                @endphp
                <div style="margin-bottom: 40px;"><strong> {{ $supervisorTitle }}  </strong></div>
                <div style="border-bottom: 1px solid #000; width: 70%; margin: 0 auto;"></div>
                {{ $supervisor->count() === 1 ? $supervisor->first()->name : '' }}                
            </td>
        </tr>
    </table>

</div>
@endsection
