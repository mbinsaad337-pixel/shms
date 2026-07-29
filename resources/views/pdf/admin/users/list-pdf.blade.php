@extends('pdf.layouts.master')

@section('content')
<table class="data-table">
    <thead>
        <tr>
            <th style="width: 5%;">#</th>
            <th>اسم المستخدم</th>
            <th>البريد الإلكتروني</th>
            <th>المركز التابع له</th>
            <th>الأدوار والصلاحيات</th>
            <th class="text-center">تاريخ الإضافة</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $index => $user)
        <tr>
            <td class="text-center">{{ $index + 1 }}</td>
            <td class="font-bold">{{ $user->name }}</td>
            <td class="font-mono text-sm" dir="ltr" style="text-align: right;">{{ $user->email }}</td>
            <td>{{ $user->center->name ?? 'الإدارة العامة' }}</td>
            <td>
                @foreach($user->roles as $role)
                    <span class="badge badge-info" style="margin-bottom: 2px;">{{ $role->name }}</span>
                @endforeach
                @if($user->roles->count() == 0)
                    <span class="text-muted text-sm">لا توجد أدوار</span>
                @endif
            </td>
            <td class="text-center font-mono text-sm">{{ $user->created_at->format('Y/m/d') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<table class="signatures-table">
    <tr>
        <td>
            <div class="sign-line"></div>
            <div class="sign-title">أعده / مسؤول النظام</div>
            <div class="sign-name">{{ $exportUser ?? '' }}</div>
        </td>
        <td>
            <div class="sign-line"></div>
            <div class="sign-title">راجعه / مدير الموارد البشرية</div>
        </td>
        <td>
            <div class="sign-line"></div>
            <div class="sign-title">اعتمده / مدير قسم المراكز الطلابية</div>
        </td>
    </tr>
</table>
@endsection
