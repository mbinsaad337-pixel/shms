@extends('pdf.layouts.master')

@section('content')

    <h3 style="color: #004274; margin-bottom: 10px; font-size: 13px;">إحصائيات الحلقات</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th class="text-center" style="width: 5%;">#</th>
                <th>اسم الحلقة</th>
                <th>المعلم</th>
                <th class="text-center">النوع</th>
                <th class="text-center">عدد الطلاب</th>
                <th class="text-center">عدد الجلسات</th>
                <th class="text-center">الحضور</th>
                <th class="text-center">الغياب</th>
                <th class="text-center">نسبة الالتزام</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $i => $item)
                <tr class="{{ $i % 2 == 0 ? 'even' : 'odd' }}">
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td class="font-bold">{{ $item['name'] }}</td>
                    <td>{{ $item['teacher'] }}</td>
                    <td class="text-center">{{ $item['type'] }}</td>
                    <td class="text-center">{{ $item['students_count'] }}</td>
                    <td class="text-center">{{ $item['sessions_count'] }}</td>
                    <td class="text-center text-success font-bold">{{ $item['present_count'] }}</td>
                    <td class="text-center text-danger font-bold">{{ $item['absent_count'] }}</td>
                    <td class="text-center font-bold" style="color: {{ $item['rate'] >= 80 ? '#15803d' : ($item['rate'] >= 60 ? '#854d0e' : '#b91c1c') }}">
                        {{ $item['rate'] }}%
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center text-muted">لا توجد بيانات حلقات متاحة للفترة المحددة</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="two-col-table" style="margin-top: 20px;">
        <tr>
            {{-- Most Committed --}}
            <td class="col-right">
                <div class="detail-card">
                    <div class="detail-card-header">
                        أكثر الطلاب التزاماً بالحضور
                    </div>
                    <div class="detail-card-body" style="padding: 0;">
                        <table class="data-table" style="margin-bottom: 0;">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 10%;">#</th>
                                    <th>الطالب</th>
                                    <th class="text-center">عدد مرات الحضور</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($mostCommitted as $idx => $st)
                                    <tr>
                                        <td class="text-center">{{ $idx + 1 }}</td>
                                        <td class="font-bold">{{ $st->student->name_ar ?? '—' }}</td>
                                        <td class="text-center text-success font-bold">{{ $st->attendance_count }} جلسة</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-center text-muted">لا توجد بيانات حضور</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </td>

            <td class="col-spacer"></td>

            {{-- Most Absent --}}
            <td class="col-left">
                <div class="detail-card">
                    <div class="detail-card-header" style="color: #b91c1c;">
                        الطلاب الأكثر غياباً
                    </div>
                    <div class="detail-card-body" style="padding: 0;">
                        <table class="data-table" style="margin-bottom: 0;">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 10%;">#</th>
                                    <th>الطالب</th>
                                    <th class="text-center">عدد مرات الغياب</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($mostAbsent as $idx => $st)
                                    <tr>
                                        <td class="text-center">{{ $idx + 1 }}</td>
                                        <td class="font-bold">{{ $st->student->name_ar ?? '—' }}</td>
                                        <td class="text-center text-danger font-bold">{{ $st->absence_count }} غياب</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-center text-muted">لا توجد سجلات غياب</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </td>
        </tr>
    </table>

@endsection
