@extends('pdf.layouts.master')

@section('content')

    <table class="data-table">
        <thead>
            <tr>
                <th class="text-center">#</th>
                <th>اسم الطالب</th>
                <th>الحلقة</th>
                <th class="text-center">تاريخ الجلسة</th>
                <th>ملاحظات</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $i => $absence)
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td class="font-bold">{{ $absence->student->name_ar ?? '—' }}</td>
                    <td>{{ $absence->session->circle->name ?? '—' }}</td>
                    <td class="text-center font-mono">{{ $absence->session->session_date ?? '—' }}</td>
                    <td class="text-sm">{{ $absence->notes ?? '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted">لا توجد حالات غياب مسجلة</td></tr>
            @endforelse
        </tbody>
    </table>

@endsection
