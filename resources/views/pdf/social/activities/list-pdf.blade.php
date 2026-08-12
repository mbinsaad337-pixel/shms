@extends('pdf.layouts.master')

@section('content')
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 4%;">#</th>
                <th>اسم الفعالية</th>
                <th>الفئة</th>
                <th>النادي المنظم</th>
                <th>المكان</th>
                <th class="text-center">تاريخ البداية</th>
                <th class="text-center">تاريخ النهاية</th>
                <th class="text-center">المستهدفون</th>
                <th class="text-center">الحالة</th>
                <th class="text-center">التكلفة (ر.ي)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $index => $activity)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="font-bold">{{ $activity->name }}</td>
                    <td>{{ $activity->category ?? '---' }}</td>
                    <td>{{ $activity->club->name ?? '---' }}</td>
                    <td>{{ $activity->location }}</td>
                    <td class="text-center   text-sm">
                        {{ $activity->start_date instanceof \Carbon\Carbon ? $activity->start_date->format('Y/m/d') : $activity->start_date }}
                        @if ($activity->start_time)
                            <br><span class="text-xs text-gray-500">{{ $activity->start_time }}</span>
                        @endif
                    </td>
                    <td class="text-center   text-sm">
                        {{ $activity->end_date ? ($activity->end_date instanceof \Carbon\Carbon ? $activity->end_date->format('Y/m/d') : $activity->end_date) : '---' }}
                        @if ($activity->end_time)
                            <br><span class="text-xs text-gray-500">{{ $activity->end_time }}</span>
                        @endif
                    </td>
                    <td class="text-center   text-sm">{{ $activity->target_audience ?? '---' }}</td>
                    <td class="text-center   text-sm">
                        {{ $activity->status == 'planned' ? 'مجدولة' : ($activity->status == 'published' ? 'مستمرة' : ($activity->status == 'cancelled' ? 'ملغاة' : 'منتهية')) }}
                    </td>
                    <td class="text-center text-sm" style="color:#15803d; font-weight:bold;">
                        {{ $activity->total_cost ? number_format($activity->total_cost, 2) : '—' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
        @if (isset($totalCost) && $totalCost > 0)
            <tfoot>
                <tr style="background-color:#f0fdf4; font-weight:bold;">
                    <td colspan="9" style="text-align:right; padding: 8px 12px; color:#166534;">
                        إجمالي التكلفة الكلية للفعاليات المعروضة
                    </td>
                    <td class="text-center" style="color:#15803d; font-size:14px; font-weight:900;">
                        {{ number_format($totalCost, 2) }} ر.ي
                    </td>
                </tr>
            </tfoot>
        @endif
    </table>

    <table class="signatures-table">
        <tr>
            <td>
                <div class="sign-line"></div>
                <div class="sign-title"> مسؤول الأنشطة</div>
                <div class="sign-name">
                    @php
                        $Activty_manager = \App\Models\User::role('social-manager')
                            ->where('center_id', $data->first()->center_id ?? null)
                            ->get();
                    @endphp
                    {{ $Activty_manager->count() === 1 ? $Activty_manager->first()->name : '' }}
                </div>
            </td>

            <td>
                <div class="sign-line"></div>
                <div class="sign-title"> مدير المركز</div>
                <div class="sign-name">
                    @php
                        $center_manager = \App\Models\User::role('center-manager')
                            ->where('center_id', $data->first()->center_id ?? null)
                            ->get();
                    @endphp
                    {{ $center_manager->count() === 1 ? $center_manager->first()->name : '' }}
                </div>
            </td>
        </tr>
    </table>
@endsection
