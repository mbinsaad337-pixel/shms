@extends('pdf.layouts.master')

@section('content')
<table class="data-table">
    <thead>
        <tr>
            <th style="width: 5%;">#</th>
            <th>اسم الطالب</th>
            <th>الرقم الجامعي</th>
            <th>المركز</th>
            @if(isset($isGraduate) && $isGraduate)
                <th>التخصص</th>
                <th>الجامعة</th>
                <th>البرنامج</th>
                <th class="text-center">عام التخرج</th>
            @else
                <th>الغرفة</th>
                <th>نظام التسكن</th>
                <th>المستوى الدراسي</th>
                <th class="text-center">الحالة</th>
            @endif
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
            @if(isset($isGraduate) && $isGraduate)
                <td>{{ $student->major ?? '---' }}</td>
                <td>{{ $student->university ?? '---' }}</td>
                <td>{{ $student->program->name ?? '---' }}</td>
                <td class="text-center font-mono font-bold">{{ $student->graduation_year ?? '---' }}</td>
            @else
                <td>{{ $student->room->room_number ?? '---' }}</td>
                <td>{{ $student->program->name ?? '---' }}</td>
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
            @endif
        </tr>
        @endforeach
    </tbody>
</table>

<table class="signatures-table">
    <tr>
        <td>
            <div class="sign-line"></div>
            <div class="sign-title">مشرف الطلاب</div>
            <div class="sign-name">
                @php
                      $housing_manager = \App\Models\User::role('housing-manager')->where('center_id',$student->center_id)->get();
                @endphp
                {{ $housing_manager->count() ===1 ? $housing_manager->first()->name : '' }}
            </div>
        </td>
        
        <td>
            <div class="sign-line"></div>
            <div class="sign-title"> مدير المركز</div>
            <div class="sign-name">
                @php
                      $center_manager = \App\Models\User::role('center-manager')->where('center_id',$student->center_id)->get();
                @endphp
                {{ $center_manager->count() ===1 ? $center_manager->first()->name : '' }}
            </div>
        </td>
    </tr>
</table>
@endsection
