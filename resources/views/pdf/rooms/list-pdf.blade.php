@extends('pdf.layouts.master')

@section('content')

    @if(isset($stats))
    <table class="stats-row">
        <tr>
            @foreach($stats as $label => $value)
                <td class="stat-card">
                    <div class="stat-label">{{ $label }}</div>
                    <div class="stat-value">{{ $value }}</div>
                </td>
            @endforeach
        </tr>
    </table>
    @endif

    <table class="data-table">
        <thead>
            <tr>
                <th class="text-center">#</th>
                <th>رقم الغرفة</th>
                <th>المبنى</th>
                <th>الشقة</th>
                <th class="text-center">الطابق</th>
                <th class="text-center">السعة</th>
                <th class="text-center">السكان الحاليون</th>
                <th class="text-center">الشواغر</th>
                <th class="text-center">الحالة</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $i => $room)
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td class="font-bold">{{ $room->room_number }}</td>
                    <td>{{ $room->building ?? '—' }}</td>
                    <td>{{ $room->apartment ?? '—' }}</td>
                    <td class="text-center">{{ $room->floor ?? '—' }}</td>
                    <td class="text-center font-bold">{{ $room->capacity }}</td>
                    <td class="text-center  ">{{ $room->students_count ?? 0 }}</td>
                    <td class="text-center   text-success">{{ $room->capacity - ($room->students_count ?? 0) }}</td>
                    <td class="text-center">
                        @if($room->status === 'available')
                            <span class="badge badge-success">متاحة</span>
                        @elseif($room->status === 'maintenance')
                            <span class="badge badge-warning">صيانة</span>
                        @else
                            <span class="badge badge-danger">مغلقة</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="9" class="text-center text-muted">لا توجد غرف</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background: #f8fafc; font-weight: bold;">
                <td colspan="5" class="text-center">الإجمالي</td>
                <td class="text-center">{{ $data->sum('capacity') }}</td>
                <td class="text-center">{{ $data->sum('students_count') }}</td>
                <td class="text-center text-success">{{ $data->sum('capacity') - $data->sum('students_count') }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

@endsection
