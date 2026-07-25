@extends('pdf.layouts.master')

@section('content')

    {{-- Room Info --}}
    <div class="detail-card">
        <div class="detail-card-header">بيانات المرفق</div>
        <div class="detail-card-body">
            <table class="two-col-table">
                <tr>
                    <td class="col-right">
                        <div class="detail-row">
                            <div class="detail-label">رقم الغرفة</div>
                            <div class="detail-value large">{{ $room->room_number }}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">المبنى</div>
                            <div class="detail-value">{{ $room->building ?? '—' }}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">الشقة</div>
                            <div class="detail-value">{{ $room->apartment ?? '—' }}</div>
                        </div>
                    </td>
                    <td class="col-spacer"></td>
                    <td class="col-left">
                        <div class="detail-row">
                            <div class="detail-label">الطابق</div>
                            <div class="detail-value">{{ $room->floor ?? '—' }}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">السعة</div>
                            <div class="detail-value">{{ $room->capacity }} طالب</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">الحالة</div>
                            <div class="detail-value">
                                @if($room->status === 'available')
                                    <span class="badge badge-success">متاحة</span>
                                @elseif($room->status === 'maintenance')
                                    <span class="badge badge-warning">صيانة</span>
                                @else
                                    <span class="badge badge-danger">مغلقة</span>
                                @endif
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    {{-- Current Residents --}}
    @if($room->assignments && $room->assignments->count() > 0)
    <table class="data-table">
        <thead>
            <tr>
                <th class="text-center">#</th>
                <th>اسم الطالب</th>
                <th class="text-center">الرقم الجامعي</th>
                <th class="text-center">تاريخ التسكين</th>
            </tr>
        </thead>
        <tbody>
            @foreach($room->assignments as $i => $assignment)
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td class="font-bold">{{ $assignment->student->name_ar ?? '—' }}</td>
                    <td class="text-center font-mono">{{ $assignment->student->student_number ?? '—' }}</td>
                    <td class="text-center font-mono">{{ $assignment->assigned_at ? \Carbon\Carbon::parse($assignment->assigned_at)->format('Y-m-d') : '—' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @else
        <div style="text-align: center; padding: 20px; color: #94a3b8;">
            لا يوجد طلاب مسكنين حالياً في هذه الغرفة
        </div>
    @endif

@endsection
