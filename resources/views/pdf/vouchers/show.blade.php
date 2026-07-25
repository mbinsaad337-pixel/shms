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
    $typeLabel = [
        'receipt' => 'سند قبض',
        'payment' => 'سند صرف',
        'transfer' => 'سند تحويل',
        'salary' => 'مسير رواتب',
    ][$voucher->type] ?? 'سند مالي';

    $isIncome = in_array($voucher->type, ['receipt']);
@endphp

<div class="voucher-card">
    <div class="voucher-amount-box">
        <div style="font-size: 14px; font-weight: bold; margin-bottom: 5px;">{{ $typeLabel }} {{ $voucher->sub_type == 'housing' ? '(تسكين)' : ($voucher->sub_type == 'deposit' ? '(إيداع)' : '') }}</div>
        <div class="amount-value {{ $isIncome ? 'text-success' : 'text-danger' }}">
            {{ number_format($voucher->amount, 2) }} <span style="font-size: 12px; color: #64748b;">ر.ي</span>
        </div>
        <div style="font-size: 11px; margin-top: 5px; color: #94a3b8;">
            استلمنا من / صرفنا لـ: <span style="color: #0f172a; font-weight: bold;">{{ $voucher->payee_or_payer }}</span>
        </div>
    </div>

    <table class="info-table">
        <tr>
            <td class="info-label">الصندوق</td>
            <td class="info-value">{{ $voucher->fund->name ?? '-' }}</td>
            <td class="info-label">تاريخ السند</td>
            <td class="info-value font-mono">{{ $voucher->date instanceof \Carbon\Carbon ? $voucher->date->format('Y-m-d') : $voucher->date }}</td>
        </tr>
        @if($voucher->student)
        <tr>
            <td class="info-label">الطالب المرتبط</td>
            <td class="info-value">{{ $voucher->student->name_ar }}</td>
            <td class="info-label">الرقم الجامعي</td>
            <td class="info-value font-mono">{{ $voucher->student->university_id }}</td>
        </tr>
        @endif
        @if($voucher->type === 'transfer' && $voucher->targetFund)
        <tr>
            <td class="info-label">وجهة التحويل</td>
            <td class="info-value text-info" colspan="3">{{ $voucher->targetFund->name }}</td>
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
                <div class="sign-title">توقيع المحاسب</div>
                <div class="sign-name">{{ $voucher->creator->name ?? '-' }}</div>
            </td>
            <td>
                <div class="sign-line"></div>
                <div class="sign-title">اعتماد مدير المركز</div>
                <div class="sign-name">{{ $voucher->approver->name ?? '-' }}</div>
            </td>
        </tr>
    </table>
</div>

@endsection
