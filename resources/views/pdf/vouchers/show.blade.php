@extends('pdf.layouts.master')

@section('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap');
    @page { size: A4 portrait; margin: 15mm; }
    body{
        font-family:'Cairo','DejaVu Sans',sans-serif;
        direction:rtl;
        background:#f1f5f9;
        color:#0f172a;
        margin:0;
        padding:0;
    }
    .page-wrap{
        min-height:297mm;
    }
    .doc{
        background:#fff;
        max-width:800px;
        margin:0 auto;
        padding:28px 36px;
        border-radius:20px;
        box-shadow:0 12px 40px rgba(15,23,42,.06);
    }
    /* Header */
    .head{
        display:flex;
        justify-content:space-between;
        align-items:center;
        border-bottom:1px solid #e2e8f0;
        padding-bottom:16px;
        margin-bottom:20px;
    }
    .head-right h1{
        margin:0;
        font-size:20px;
        font-weight:700;
        color:#0f172a;
    }
    .head-right .sub{
        font-size:11px;
        color:#64748b;
        margin-top:3px;
    }
    .head-left .badge{
        background:#eef2ff;
        color:#3730a3;
        font-size:11px;
        font-weight:700;
        padding:6px 12px;
        border-radius:999px;
        display:inline-block;
    }
    /* Meta */
    .meta{
        display:flex;
        gap:20px;
        flex-wrap:wrap;
        font-size:12px;
        color:#475569;
        margin-bottom:5px;
    }
    /* Amount */
    .amount-card{
        border-radius:16px;
        padding:10px 10px;
        margin-bottom:24px;
        position:relative;
        overflow:hidden;
    }
    .amount-card.income{ background:linear-gradient(135deg,#ecfdf5,#f0fdfa); border:1px solid #a7f3d0; }
    .amount-card.expense{ background:linear-gradient(135deg,#fff1f2,#fef2f2); border:1px solid #fecaca; }
    .amount-card.transfer{ background:linear-gradient(135deg,#eff6ff,#f0f9ff); border:1px solid #bfdbfe; }
    .amount-card.salary{ background:linear-gradient(135deg,#faf5ff,#fdf4ff); border:1px solid #e9d5ff; }
    .amount-top{ font-size:13px; color:#475569; margin-bottom:6px; }
    .amount-value{ font-size:20px; font-weight:700; color:#0f172a; line-height:1.1; }
    .amount-curr{ font-size:15px; color:#475569; margin-right:8px; }
    .amount-note{ margin-top:10px; font-size:13px; color:#334155; background-color: #e2e0e02a }
    /* Sections */
    .section-title{
        font-size:13px;
        font-weight:700;
        color:#0f172a;
        background:#f8fafc;
        border-right:4px solid #2563eb;
        padding:8px 12px;
        border-radius:8px;
        margin:22px 0 10px;
    }
    .grid{ width:100%; border-collapse:collapse; }
    .grid td{ padding:10px 12px; font-size:12.5px; border-bottom:1px dashed #e2e8f0; vertical-align:middle; }
    .grid .label{ width:28%; color:#64748b; font-weight:600; }
    .grid .value{ color:#0f172a; font-weight:600; }
    .desc{
        background:#f8fafc;
        border:1px solid #e2e8f0;
        border-radius:12px;
        padding:14px;
        font-size:13px;
        line-height:1.8;
        color:#334155;
        min-height:60px;
    }
    .signs{ margin-top:28px; width:100%; text-align:center; }
    .signs td{ width:33%; padding:16px 8px; vertical-align:top; }
    .sign-line{ height:44px; border-bottom:1px dashed #cbd5e1; margin-bottom:6px; }
    .sign-title{ font-size:11.5px; color:#64748b; font-weight:600; }
    .sign-name{ margin-top:5px; font-size:11.5px; color:#0f172a; font-weight:700; }
    .footer{
        text-align:center;
        font-size:10px;
        color:#94a3b8;
        margin-top:20px;
        padding-top:12px;
        border-top:1px solid #e2e8f0;
    }
    .blank-half{
        height:145mm; /* يترك النصف السفلي من الصفحة فارغ */
    }
</style>
@endsection

@section('content')
@php
$typeLabel = [
    'receipt'  => 'سند قبض',
    'payment'  => 'سند صرف',
    'transfer' => 'سند تحويل',
    'salary'   => 'مسير رواتب'
][$voucher->type] ?? 'سند مالي';
$typeClass = $voucher->type;
$isIncome = $voucher->type == 'receipt';
@endphp

<div class="page-wrap">
    

        <div class="meta">
            <span>📅 تاريخ الإصدار: {{ \Carbon\Carbon::parse($voucher->date)->format('d/m/Y') }}</span>
            <span>🧾 رقم السند: {{ $voucher->id }}</span>
            <span>🏦 الصندوق: {{ $voucher->fund->name ?? '-' }}</span>
        </div>

        <div class="amount-card {{ $typeClass }}">
            <div class="amount-top">قيمة السند</div>
            
            <div class="amount-value">
                {{ number_format($voucher->amount,2) }}
                <span class="amount-curr">ريال يمني</span>
            </div>
            <div class="amount-note">
                {{ $isIncome ? 'استلمنا من' : 'صرفنا إلى' }} : 
                <strong>{{ $voucher->payee_or_payer }}</strong>
            </div>
        </div>

        <table class="grid">
            
            @if($voucher->student)
            <tr>
                <td class="label">الطالب</td>
                <td class="value">{{ $voucher->student->name_ar }}</td>
                <td class="label">الرقم الجامعي</td>
                <td class="value">{{ $voucher->student->university_id }}</td>
            </tr>
            @endif
            @if($voucher->type=='transfer' && $voucher->targetFund)
            <tr>
                <td class="label">الصندوق المستلم</td>
                <td class="value" colspan="3">{{ $voucher->targetFund->name }}</td>
            </tr>
            @endif
        </table>

        <div class="section-title">البيان والملاحظات</div>
        <div class="desc">
            {{ $voucher->description ?: 'لا يوجد بيان' }}
        </div>

        <table class="signs">
            <tr>
                <td>
                    <div class="sign-line"></div>
                    <div class="sign-title">توقيع المستلم / المستفيد</div>
                </td>
                <td>
                    <div class="sign-line"></div>
                    <div class="sign-title">المسؤول المالي</div>
              <div class="sign-name">
              @php
                      $financial_manager = \App\Models\User::role('financial-manager')->where('center_id',$voucher->center_id)->get();
                @endphp
                {{ $financial_manager->count() ===1 ? $financial_manager->first()->name : '' }}
            </div>                </td>
                <td>
                    <div class="sign-line"></div>
                    <div class="sign-title"> مدير المركز</div>
                    <div class="sign-name">
                    @php
                      $center_manager = \App\Models\User::role('center-manager')->where('center_id',$voucher->center_id)->get();
                @endphp
                {{ $center_manager->count() ===1 ? $center_manager->first()->name : '' }}
            </div>
                </td>
            </tr>
        </table>

        <div class="footer">
            تم إنشاء هذا السند إلكترونياً بواسطة نظام إدارة السكن الطلابي • {{ now()->format('Y-m-d') }}
        </div>
    </div>

    
</div>
@endsection