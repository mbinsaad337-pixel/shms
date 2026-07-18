<!-- Official Print Footer -->
<div class="print-only print-footer-official">
    <div class="footer-master-grid">
        <!-- Prepared By -->
        <div class="footer-sign-block">
            <p class="sign-label">أعده / مسؤول التغذية</p>
            <div class="sign-line"></div>
            <p class="sign-name">{{ auth()->user()->name }}</p>
        </div>

        <!-- Reviewed By -->
        <div class="footer-sign-block">
            <p class="sign-label">راجعه / المسؤول المالي</p>
            <div class="sign-line"></div>
        </div>

        <!-- Approved By -->
        <div class="footer-sign-block">
            <p class="sign-label">اعتمده / مدير المركز</p>
            <div class="sign-line"></div>
        </div>
    </div>

    <div class="footer-system-stamp">
        <p>صدر من منصة إدارة السكنات الطلابية - جمعية رعاية طالب العلم - {{ date('Y-m-d H:i:s') }}</p>
        <p class="page-numbering text-left">صفحة <span class="page-count"></span></p>
    </div>
</div>

<style>
    @media print {
        .print-footer-official {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            padding-top: 20px;
            border-top: 1px solid var(--print-border);
            background: white;
        }

        .footer-master-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 40px;
            text-align: center;
        }

        .footer-sign-block {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .sign-label {
            font-size: 11px;
            font-weight: 700;
            color: var(--print-navy);
            margin-bottom: 30px;
        }

        .sign-line {
            width: 80%;
            border-bottom: 1px dashed #cbd5e1;
            margin-bottom: 5px;
        }

        .sign-name {
            font-size: 10px;
            color: #64748b;
        }

        .footer-system-stamp {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
            font-size: 8px;
            color: #94a3b8;
            font-family: monospace;
        }
    }
</style>
