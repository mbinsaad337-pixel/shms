@extends('pdf.layouts.master')

@section('content')
<table class="data-table">
    <thead>
        <tr>
            <th style="width: 5%;">#</th>
            <th>الطالب</th>
            <th>المركز</th>
            <th class="text-center">التاريخ</th>
            <th class="text-center">النوع</th>
            <th class="text-center">العذر</th>
            <th>ملاحظات</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $index => $absence)
        <tr>
            <td class="text-center">{{ $index + 1 }}</td>
            <td class="font-bold">{{ $absence->student->name_ar ?? '---' }}</td>
            <td>{{ $absence->student->center->name ?? '---' }}</td>
            <td class="text-center   text-sm">{{ $absence->date->format('Y/m/d') }}</td>
            <td class="text-center">
                @php $typeLabels = ['housing'=>'سكن','quran'=>'حلقة قرآنية','activity'=>'نشاط','other'=>'آخر']; @endphp
                {{ $typeLabels[$absence->absence_type] ?? ($absence->absence_type ?? 'عام') }}
            </td>
            <td class="text-center">
                @if($absence->has_excuse)
                <span class="badge badge-success text-xs">معذور</span>
                @else
                <span class="badge badge-danger text-xs">غير معذور</span>
                @endif
            </td>
            <td>{{ Str::limit($absence->notes, 40) }}</td>
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
