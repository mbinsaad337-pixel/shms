<!-- Official Executive Print Header -->
<div class="print-only print-header-executive">
    <!-- Sophisticated Watermark -->
    <div class="print-watermark">المنصة</div>

    <!-- Top Pattern Bar -->
    <div class="premium-pattern-bar"></div>

    <div class="header-content-grid">
        <!-- Right: Official Identity -->
        <div class="official-id-section">
            <h2 class="org-main-ar">جمعية رعاية طالب العلم</h2>
            <h3 class="center-sub-ar">{{ auth()->user()->center?->name ?? 'مركز الأوائل الجامعي' }}</h3>
            <div class="h-accent-gold"></div>
            <p class="dept-label">{{ $department ?? 'الإدارة المركزية - منصة إدارة المراكز الطلابية' }}</p>
        </div>

        <!-- Center: Branding Logos -->
        <div class="branding-logos-section">
            <div class="logos-flex-container">
                <img src="{{ asset('images/logos/alawayil_logo.png') }}" alt="Alawayil" class="h-14">
                <div class="vertical-gold-line"></div>
                <img src="{{ asset('images/logos/scs_logo.png') }}" alt="SCS" class="h-14">
            </div>
        </div>

        <!-- Left: Document Context -->
        <div class="document-context-section">
            <div class="official-security-seal">
                <div class="seal-inner">
                    <i class="fas fa-shield-halved"></i>
                    <span>CERTIFIED<br>SYSTEM</span>
                </div>
            </div>
            <div class="ctx-item">
                <span class="ctx-label">الرقم المرجعي:</span>
                <span class="ctx-val font-mono">{{ $number ?? '---' }}</span>
            </div>
            <div class="ctx-item">
                <span class="ctx-label">تاريخ المستند:</span>
                <span class="ctx-val">{{ date('Y/m/d') }}</span>
            </div>
            <div class="ctx-item">
                <span class="ctx-label">نطاق التقرير:</span>
                <span class="ctx-val">سري وللاستخدام الداخلي</span>
            </div>
        </div>
    </div>

    <!-- Title & Abstract -->
    <div class="title-abstract-container">
        <h1 class="document-hero-title">{{ $title ?? 'تقرير رسمي' }}</h1>
        <div class="title-underline"></div>
    </div>
</div>

<style>
    :root {
        --executive-navy: #004274;
        --executive-gold: #D4A044;
        --soft-border: #e2e8f0;
    }

    @media screen {
        .print-only {
            display: none !important;
        }
    }

    @media print {
        @page {
            size: A4;
            margin: 1.2cm;
        }

        body {
            background: white !important;
            font-family: 'Cairo', sans-serif !important;
            color: #0f172a !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .no-print,
        nav,
        aside,
        header,
        button,
        .btn,
        .fixed,
        .sticky,
        .sidebar {
            display: none !important;
        }

        .print-only {
            display: block !important;
        }

        /* Watermark */
        .print-watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 10rem;
            color: rgba(0, 0, 0, 0.03);
            font-weight: 900;
            z-index: -1;
            pointer-events: none;
            letter-spacing: 1rem;
        }

        .premium-pattern-bar {
            height: 6px;
            background: var(--executive-navy);
            border-bottom: 3px solid var(--executive-gold);
            margin-bottom: 30px;
            border-radius: 3px;
        }

        .header-content-grid {
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            gap: 30px;
            align-items: center;
            padding-bottom: 25px;
            border-bottom: 1px solid var(--soft-border);
        }

        /* Identity */
        .official-id-section {
            text-align: right;
        }

        .org-main-ar {
            font-size: 18px;
            font-weight: 900;
            color: var(--executive-navy);
            margin: 0;
        }

        .center-sub-ar {
            font-size: 15px;
            font-weight: 700;
            color: var(--executive-gold);
            margin: 2px 0;
        }

        .h-accent-gold {
            height: 2px;
            width: 45px;
            background: var(--executive-gold);
            margin: 8px 0 8px auto;
            border-radius: 1px;
        }

        .dept-label {
            font-size: 10px;
            color: #64748b;
            font-weight: bold;
            margin: 0;
        }

        /* Logos */
        .branding-logos-section {
            text-align: center;
        }

        .logos-flex-container {
            display: flex;
            align-items: center;
            gap: 20px;
            justify-content: center;
        }

        .logos-flex-container img {
            height: 60px;
            width: auto;
        }

        .official-security-seal {
            position: absolute;
            top: -15px;
            left: -10px;
            width: 70px;
            height: 70px;
            background: rgba(212, 160, 68, 0.03);
            border: 1.5px dashed var(--executive-gold);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transform: rotate(-15deg);
            opacity: 0.8;
            z-index: 10;
        }

        .seal-inner {
            text-align: center;
            color: var(--executive-gold);
            font-size: 7px;
            font-weight: 900;
            line-height: 1.1;
            font-family: 'Almarai', sans-serif;
            text-transform: uppercase;
        }

        .seal-inner i {
            font-size: 16px;
            display: block;
            margin-bottom: 2px;
            color: var(--executive-gold);
        }

        .vertical-gold-line {
            width: 1.5px;
            height: 50px;
            background: #e2e8f0;
        }

        /* Context */
        .document-context-section {
            text-align: left;
        }

        .ctx-item {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            font-size: 10px;
            margin-bottom: 4px;
        }

        .ctx-label {
            color: #64748b;
            font-weight: 700;
        }

        .ctx-val {
            color: #000;
            font-weight: 700;
        }

        /* Title Area */
        .title-abstract-container {
            margin-top: 35px;
            text-align: center;
        }

        .document-hero-title {
            font-size: 26px;
            font-weight: 900;
            color: var(--executive-navy);
            margin: 0;
            text-shadow: 1px 1px 0 rgba(0, 0, 0, 0.05);
        }

        .title-underline {
            width: 120px;
            height: 4px;
            background: var(--executive-gold);
            margin: 12px auto;
            border-radius: 2px;
        }

        /* Standardized Premium Print Styles for Content */
        .bg-white {
            border: 1px solid var(--soft-border) !important;
            border-radius: 15px !important;
            box-shadow: none !important;
        }

        table {
            width: 100% !important;
            margin-top: 25px !important;
            border-collapse: separate !important;
            border-spacing: 0 !important;
        }

        thead th {
            background: #f8fafc !important;
            color: var(--executive-navy) !important;
            border-bottom: 2px solid var(--executive-navy) !important;
            padding: 14px 12px !important;
            font-size: 11px !important;
            font-weight: 800 !important;
            text-align: right !important;
        }

        tbody td {
            padding: 12px !important;
            border-bottom: 1px solid #f1f5f9 !important;
            font-size: 11px !important;
            color: #334155 !important;
        }

        tbody tr:nth-child(even) {
            background-color: #fafbfc !important;
        }

        .font-mono {
            font-family: 'Courier New', Courier, monospace !important;
        }

        .font-black {
            font-weight: 800 !important;
        }

        .text-lg {
            font-size: 1.25rem !important;
        }

        .text-2xl {
            font-size: 1.5rem !important;
        }

        .text-3xl {
            font-size: 1.875rem !important;
        }

        .text-4xl {
            font-size: 2.25rem !important;
        }

        .text-5xl {
            font-size: 3rem !important;
        }
    }
</style>
