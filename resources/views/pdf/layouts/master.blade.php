<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $reportTitle ?? 'تقرير' }} - {{ $systemName ?? 'نظام إدارة المراكز الطلابية' }}</title>
    <style>
        body {
            font-family: 'sans-serif';
            direction: rtl;
            text-align: right;
            color: #1e293b;
            font-size: 12px;
            line-height: 1.6;
            background: #ffffff;
        }

        /* ── PAGE HEADER ── */
        .page-header {
            padding-bottom: 12px;
            margin-bottom: 18px;
            border-bottom: 3px solid #004274;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table td {
            border: none;
            vertical-align: top;
            padding: 0;
        }

        .header-right {
            width: 35%;
            text-align: right;
        }

        .header-center {
            width: 30%;
            text-align: center;
            vertical-align: middle;
        }

        .header-left {
            width: 35%;
            text-align: left;
        }

        .system-name {
            font-size: 13px;
            font-weight: bold;
            color: #004274;
            margin-bottom: 5px;
        }

        .report-title-small {
            font-size: 11px;
            font-weight: bold;
            color: #D4A044;
        }

        .meta-item {
            font-size: 9px;
            color: #64748b;
            margin-bottom: 3px;
            text-align: right;
            direction: rtl;
        }

        .meta-value {
            font-weight: bold;
            color: #0f172a;
        }

        .header-logo {
            height: 75px;
            vertical-align: middle;
        }

        /* ── REPORT TITLE ── */
        .report-title-section {
            text-align: center;
            margin-bottom: 18px;
            padding: 10px 0;
        }

        .report-title-section h1 {
            font-size: 17px;
            font-weight: bold;
            color: #004274;
            margin-bottom: 4px;
        }

        .report-title-section .subtitle {
            font-size: 10px;
            color: #64748b;
        }

        .title-underline {
            width: 80px;
            height: 3px;
            background: #D4A044;
            margin: 6px auto 0;
        }

        /* ── STATS CARDS ── */
        .stats-row {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }

        .stat-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 10px 12px;
            text-align: center;
            vertical-align: top;
        }

        .stat-label {
            font-size: 9px;
            color: #94a3b8;
            margin-bottom: 4px;
            font-weight: bold;
        }

        .stat-value {
            font-size: 16px;
            font-weight: bold;
            color: #004274;
        }

        /* ── FILTERS SECTION ── */
        .filters-section {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 10px 15px;
            margin-bottom: 18px;
        }

        .filters-title {
            font-size: 10px;
            font-weight: bold;
            color: #004274;
            margin-bottom: 6px;
        }

        .filters-grid {
            width: 100%;
            border-collapse: collapse;
        }

        .filters-grid td {
            border: none;
            padding: 3px 8px;
            font-size: 9px;
            vertical-align: top;
        }

        .filter-key {
            color: #64748b;
            font-weight: bold;
            width: 25%;
        }

        .filter-val {
            color: #0f172a;
            font-weight: bold;
        }

        /* ── DATA TABLES ── */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
            font-size: 10px;
        }

        .data-table thead tr {
            background: #004274;
        }

        .data-table thead th {
            color: #ffffff;
            padding: 9px 8px;
            font-weight: bold;
            font-size: 10px;
            text-align: right;
            border: 1px solid #003560;
        }

        .data-table thead th.text-center {
            text-align: center;
        }

        .data-table tbody td {
            padding: 8px;
            border: 1px solid #e2e8f0;
            color: #334155;
            font-size: 10px;
        }

        .data-table tbody tr.even {
            background: #fafbfc;
        }

        .data-table tbody tr.odd {
            background: #ffffff;
        }

        /* ── UTILITY CLASSES ── */
        .text-center { text-align: center; }
        .text-left   { text-align: left; }
        .text-right  { text-align: right; }
        .font-bold   { font-weight: bold; }

        .text-navy    { color: #004274; }
        .text-gold    { color: #D4A044; }
        .text-success { color: #15803d; }
        .text-danger  { color: #b91c1c; }
        .text-info    { color: #0369a1; }
        .text-warning { color: #854d0e; }
        .text-muted   { color: #94a3b8; }
        .text-light-c { color: #64748b; }

        .text-sm  { font-size: 9px; }
        .text-xs  { font-size: 8px; }
        .text-lg  { font-size: 14px; }
        .text-xl  { font-size: 16px; }
        .text-2xl { font-size: 18px; }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            font-size: 9px;
            font-weight: bold;
        }

        .badge-success { background: #dcfce7; color: #15803d; }
        .badge-danger  { background: #fee2e2; color: #b91c1c; }
        .badge-info    { background: #dbeafe; color: #0369a1; }
        .badge-warning { background: #fef3c7; color: #854d0e; }
        .badge-primary { background: #e0f2fe; color: #004274; }
        .badge-secondary { background: #f1f5f9; color: #475569; }

        /* ── DETAIL CARDS ── */
        .detail-card {
            border: 1px solid #e2e8f0;
            margin-bottom: 14px;
        }

        .detail-card-header {
            background: #f8fafc;
            padding: 8px 12px;
            font-weight: bold;
            font-size: 11px;
            color: #004274;
            border-bottom: 1px solid #e2e8f0;
        }

        .detail-card-body {
            padding: 12px;
        }

        .detail-label {
            font-size: 9px;
            color: #64748b;
            margin-bottom: 2px;
            font-weight: bold;
        }

        .detail-value {
            font-size: 12px;
            color: #0f172a;
            font-weight: bold;
        }

        /* ── TWO-COLUMN LAYOUT ── */
        .two-col-table {
            width: 100%;
            border-collapse: collapse;
        }

        .two-col-table td {
            border: none;
            vertical-align: top;
            padding: 0;
        }

        .col-right   { width: 48%; }
        .col-spacer  { width: 4%; }
        .col-left    { width: 48%; }

        /* ── SIGNATURES ── */
        .signatures-table {
            width: 100%;
            margin-top: 40px;
            border-collapse: collapse;
        }

        .signatures-table td {
            border: none;
            width: 33%;
            text-align: center;
            padding: 0 10px;
            vertical-align: top;
        }

        .sign-title {
            font-size: 18px;
            font-weight: bold;
            color: #000000;
        }

        .sign-line {
            border-top: 1px dashed #e2e8f0;
            margin: 30px auto 8px;
            width: 80%;
        }

        .sign-name {
            margin-top: 20px;
            font-size: 15px;
            color: #45413f;
            margin-top: 3px;
        }

        /* ── PAGE FOOTER ── */
        .page-footer {
            position: fixed;
            bottom: -10px;
            left: 0;
            right: 0;
            height: 40px;
            border-top: 2px solid #004274;
            padding-top: 5px;
        }

        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }

        .footer-table td {
            border: none;
            font-size: 8px;
            color: #94a3b8;
            padding: 0 5px;
            vertical-align: middle;
        }

        .footer-right  { text-align: right;  width: 33%; }
        .footer-center { text-align: center; width: 34%; }
        .footer-left   { text-align: left;   width: 33%; }

        /* ── PAGE BREAKS ── */
        .page-break  { page-break-after: always; }
        .avoid-break { page-break-inside: avoid; }

        .content { margin-bottom: 50px; }

        /* ── EXTRA STYLES FROM CHILD VIEWS ── */
        @yield('styles')
    </style>
</head>
<body>

    {{-- ════ FIXED FOOTER ════ --}}
    <div class="page-footer">
        <table class="footer-table">
            <tr>
                <td class="footer-right">
                    {{ $systemName ?? 'نظام إدارة المراكز الطلابية' }} | {{ $systemVersion ?? 'Version 1.0' }}
                </td>
                <td class="footer-center">
                    صفحة <span class="mpdf_pagenumber"></span> من <span class="mpdf_nbpages"></span>
                </td>
                <td class="footer-left" style="direction: rtl; text-align: right;">
                    {{ $exportDate ?? date('Y-m-d') }} | &copy; جميع الحقوق محفوظة
                </td>
            </tr>
        </table>
    </div>

    {{-- ════ PAGE HEADER ════ --}}
    <div class="page-header">
        <table class="header-table">
            <tr>
                 {{-- CENTER: System Name + Report Title --}}
                <td class="header-right">
                    <div class="system-name">{{ $systemName ?? 'نظام إدارة المراكز الطلابية' }}</div>
                    <div class="report-title-small">{{ $reportTitle ?? 'تقرير' }}</div>
                </td>
                {{-- RIGHT: Logos --}}
                <td class="header-center">
                    @php
                        $scsLogoPath    = public_path('images/logos/scs_logo.png');
                        $scsLogoData    = file_exists($scsLogoPath) ? base64_encode(file_get_contents($scsLogoPath)) : '';
                        $center         = auth()->user()->center ?? null;
                        
                        $centerLogoPath = ($center && $center->logo && file_exists(storage_path('app/public/' . $center->logo)))
                            ? storage_path('app/public/' . $center->logo)
                            :null;
                        $centerLogoData = file_exists($centerLogoPath) ? base64_encode(file_get_contents($centerLogoPath)) : '';
                        $centerLogoExt  = pathinfo($centerLogoPath, PATHINFO_EXTENSION);
                    @endphp
                    <table style="width: 100%; border: none; padding: 0; margin: 0; border-collapse: collapse;">
                        <tr>
                            <td style="text-align: left; vertical-align: middle; border: none; padding: 0; width: 50%;">
                                @if($scsLogoData)
                                    <img src="data:image/png;base64,{{ $scsLogoData }}" class="header-logo" style="margin-left: 10px;">
                                @endif
                            </td>
                            <td style="text-align: right; vertical-align: middle; border: none; padding: 0; width: 50%;">
                                @if($centerLogoData)
                                    <img src="data:image/{{ $centerLogoExt }};base64,{{ $centerLogoData }}" class="header-logo" style="height: 90px; margin-right: 10px;">
                                @endif
                            </td>
                        </tr>
                    </table>
                </td>

               

                {{-- LEFT: Meta --}}
                <td class="header-left" style="direction: rtl;">
                    <div class="meta-item">
                        تاريخ الإنشاء: <span class="meta-value">{{ $exportDate ?? date('Y-m-d') }}</span>
                    </div>
                    <div class="meta-item">
                        وقت الإنشاء: <span class="meta-value">{{ $exportTime ?? date('H:i:s') }}</span>
                    </div>
                    <div class="meta-item">
                        بواسطة: <span class="meta-value">{{ $exportUser ?? 'النظام' }}</span>
                    </div>
                    @if(!empty($exportCenter))
                    <div class="meta-item">
                        المركز: <span class="meta-value">{{ $exportCenter }}</span>
                    </div>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    {{-- ════ REPORT TITLE ════ --}}
    <div class="report-title-section">
        <h1>{{ $reportTitle ?? 'تقرير' }}</h1>
        @if(!empty($reportSubtitle))
            <div class="subtitle">{{ $reportSubtitle }}</div>
        @endif
        <div class="title-underline"></div>
    </div>

    {{-- ════ STATS CARDS ════ --}}
    @if(!empty($stats) && count($stats) > 0)
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

    {{-- ════ FILTERS ════ --}}
    @if(!empty($filters) && count($filters) > 0)
    <div class="filters-section">
        <div class="filters-title">معلومات التقرير </div>
        <table class="filters-grid">
            @foreach(array_chunk($filters, 2, true) as $row)
            <tr>
                @foreach($row as $key => $val)
                    <td class="filter-key">{{ $key }}:</td>
                    <td class="filter-val">{{ $val }}</td>
                @endforeach
                @if(count($row) < 2)
                    <td></td><td></td>
                @endif
            </tr>
            @endforeach
        </table>
    </div>
    @endif

    {{-- ════ MAIN CONTENT ════ --}}
    <div class="content">
        @yield('content')
    </div>

</body>
</html>
