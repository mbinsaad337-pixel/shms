@extends('pdf.layouts.master')

@section('styles')
<style>
    .voucher-card {
        border: 2px solid #004274;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        background: #fff;
    }
    .voucher-amount-box {
        background: #fafbfc;
        border: 1px solid #e2e8f0;
        padding: 15px;
        text-align: center;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    .amount-value {
        font-size: 24px;
        font-weight: 900;
        color: #004274;
        font-family: 'dejavusans', monospace;
    }
    .info-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }
    .info-table td {
        padding: 10px;
        border: 1px solid #e2e8f0;
    }
    .info-label {
        font-weight: bold;
        color: #64748b;
        background: #f8fafc;
        width: 25%;
    }
    .info-value {
        font-weight: bold;
        color: #0f172a;
    }
</style>
@endsection

@section('content')

@php
    $typeLabel = $voucher->type === 'receipt' ? 'سند قبض' : 'سند صرف';
    $isIncome = $voucher->type === 'receipt';
@endphp

<div class="voucher-card">
    <div class="voucher-amount-box">
        <div style="font-size: 14px; font-weight: bold; margin-bottom: 5px;">{{ $typeLabel }} (تغذية)</div>
        <div class="amount-value {{ $isIncome ? 'text-success' : 'text-danger' }}">
            {{ number_format($voucher->amount, 2) }} <span style="font-size: 12px; color: #64748b;">{{ currency_symbol() }}</span>
        </div>
        <div style="font-size: 11px; margin-top: 5px; color: #94a3b8;">
            @if($voucher->type === 'receipt')
                استلمنا من الطالب: <span style="color: #0f172a; font-weight: bold;">{{ $voucher->student->name_ar ?? '-' }}</span>
            @else
                صرفنا للمورد: <span style="color: #0f172a; font-weight: bold;">{{ $voucher->supplier->name ?? '-' }}</span>
            @endif
        </div>
    </div>

    <table class="info-table">
        <tr>
            <td class="info-label">رقم السند</td>
            <td class="info-value  ">{{ $voucher->voucher_number }}</td>
            <td class="info-label">تاريخ السند</td>
            <td class="info-value  ">{{ $voucher->voucher_date instanceof \Carbon\Carbon ? $voucher->voucher_date->format('Y-m-d') : $voucher->voucher_date }}</td>
        </tr>
        @if($voucher->student)
        <tr>
            <td class="info-label">اسم الطالب</td>
            <td class="info-value">{{ $voucher->student->name_ar }}</td>
            <td class="info-label">الرقم الجامعي</td>
            <td class="info-value  ">{{ $voucher->student->university_id }}</td>
        </tr>
        @endif
        @if($voucher->supplier)
        <tr>
            <td class="info-label">اسم المورد</td>
            <td class="info-value text-info" colspan="3">{{ $voucher->supplier->name }}</td>
        </tr>
        @endif
        <tr>
            <td class="info-label">البيان</td>
            <td class="info-value" colspan="3" style="line-height: 1.8;">{{ $voucher->description }}</td>
        </tr>
    </table>

    <table class="signatures-table" style="margin-top: 60px;">
        <tr>
            <td>
                <div class="sign-line"></div>
                <div class="sign-title">توقيع المستلم / المستفيد</div>
            </td>
            <td>
                <div class="sign-line"></div>
                <div class="sign-title">أعده / مشرف التغذية</div>
                <div class="sign-name">{{ $voucher->creator->name ?? '-' }}</div>
            </td>
            <td>
                <div class="sign-line"></div>
                <div class="sign-title">اعتماد مدير المركز</div>
                <div class="sign-name"></div>
            </td>
        </tr>
    </table>
</div>

@endsection
