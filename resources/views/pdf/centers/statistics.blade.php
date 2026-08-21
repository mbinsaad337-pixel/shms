@extends('pdf.layouts.master')

@section('content')
    <div class="detail-card">
        <div class="detail-card-header">بيانات المركز</div>
        <div class="detail-card-body">
            <table class="two-col-table">
                <tr>
                    <td class="col-right">
                        <div class="detail-row">
                            <div class="detail-label">اسم المركز</div>
                            <div class="detail-value large">{{ $center->name }}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">العنوان</div>
                            <div class="detail-value">{{ $center->address }}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">حالة المركز</div>
                            <div class="detail-value">
                                <span class="badge {{ $center->is_active ? 'badge-success' : 'badge-danger' }}">
                                    {{ $center->is_active ? 'نشط' : 'غير نشط' }}
                                </span>
                            </div>
                        </div>
                    </td>
                    <td class="col-spacer"></td>
                    <td class="col-left">
                        <div class="detail-row">
                            <div class="detail-label">البريد الإلكتروني</div>
                            <div class="detail-value">{{ $center->email ?: '-' }}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">رقم الهاتف</div>
                            <div class="detail-value  ">{{ $center->phone ?: '-' }}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">حالة المبنى</div>
                            <div class="detail-value">{{ $center->has_rent ? 'مبنى مستأجر' : 'مبنى غير مستأجر' }}</div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <h3 style="font-size: 14px; color: #004274; margin: 20px 0 10px; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px;">الإحصائيات العامة</h3>
    <table class="stats-row">
        <tr>
            <td class="stat-card" style="border-bottom: 3px solid #004274;">
                <div class="stat-label">إجمالي الساكنين</div>
                <div class="stat-value text-navy">{{ number_format($center->residents_count) }}</div>
            </td>
            <td class="stat-card" style="border-bottom: 3px solid #D4A044;">
                <div class="stat-label">عدد الغرف</div>
                <div class="stat-value text-gold">{{ number_format($center->rooms_count) }}</div>
            </td>
            <td class="stat-card" style="border-bottom: 3px solid #15803d;">
                <div class="stat-label">عدد الموظفين</div>
                <div class="stat-value text-success">{{ number_format($center->staff_count) }}</div>
            </td>
        </tr>
    </table>

    <table class="signatures-table avoid-break">
        <tr>
            <td>
                <div class="sign-line"></div>
                <div class="sign-title">مُصدّر التقرير</div>
                <div class="sign-name">{{ $exportUser ?? '-' }}</div>
            </td>
            <td>
                <div class="sign-line"></div>
                <div class="sign-title">إدارة المركز</div>
            </td>
            <td>
                <div class="sign-line"></div>
                <div class="sign-title">الإدارة المركزية</div>
            </td>
        </tr>
    </table>
@endsection
