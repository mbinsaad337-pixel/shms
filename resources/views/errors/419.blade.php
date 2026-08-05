<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>انتهت الجلسة</title>
    <meta http-equiv="refresh" content="0;url={{ route('login') }}">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Cairo', 'Segoe UI', sans-serif;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            direction: rtl;
        }
        .card {
            background: white;
            border-radius: 16px;
            padding: 2.5rem;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            max-width: 380px;
            width: 90%;
        }
        .icon { font-size: 3rem; margin-bottom: 1rem; }
        h2 { color: #1e3a5f; font-size: 1.25rem; margin-bottom: 0.5rem; }
        p { color: #64748b; font-size: 0.875rem; margin-bottom: 1.5rem; }
        a {
            display: inline-block;
            background: #004274;
            color: white;
            text-decoration: none;
            padding: 0.625rem 1.5rem;
            border-radius: 10px;
            font-size: 0.875rem;
            font-weight: 700;
            transition: background 0.2s;
        }
        a:hover { background: #083358; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">⏱️</div>
        <h2>انتهت صلاحية الجلسة</h2>
        <p>يتم إعادة توجيهك إلى صفحة تسجيل الدخول...</p>
        <a href="{{ route('login') }}">تسجيل الدخول</a>
    </div>

    <script>
        // Immediate redirect
        window.location.href = "{{ route('login') }}";
    </script>
</body>
</html>
