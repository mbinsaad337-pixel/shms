<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>منصة إدارة السكنات الطلابية - جمعية رعاية طالب العلم</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&family=Almarai:wght@300;400;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        :root {
            --primary-navy: #004274;
            --primary-gold: #D4A044;
            --soft-white: #f8fafc;
        }

        body {
            font-family: 'Cairo', sans-serif;
            background-color: var(--soft-white);
            color: #1e293b;
            scroll-behavior: smooth;
        }

        .hero-section {
            background: linear-gradient(rgba(0, 66, 116, 0.85), rgba(0, 66, 116, 0.95)), url('{{ asset('images/hero.png') }}');
            background-size: cover;
            background-position: center;
            height: 90vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
        }

        .gold-underline {
            width: 80px;
            height: 4px;
            background-color: var(--primary-gold);
            margin: 20px 0;
            border-radius: 2px;
        }

        .feature-card {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border-color: var(--primary-gold);
        }

        .glass-nav {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
        }

        .btn-premium {
            background: linear-gradient(135deg, var(--primary-navy), #0a3d62);
            color: white;
            padding: 12px 30px;
            border-radius: 15px;
            font-weight: 700;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .btn-premium:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 20px rgba(0, 66, 116, 0.2);
            color: var(--primary-gold);
        }

        .gold-border {
            border: 2px solid var(--primary-gold);
        }

        .text-gradient {
            background: linear-gradient(to left, var(--primary-gold), #f4be5e);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>

<body class="antialiased">

    <!-- Navigation -->
    <nav class="glass-nav fixed w-full z-50 py-4 shadow-sm">
        <div class="max-w-7xl mx-auto px-6 flex justify-between items-center">
            <div class="flex items-center gap-4">
                <img src="{{ asset('images/logos/alawayil_logo.png') }}" class="h-12 w-auto" alt="Logo">
                <div class="h-8 w-px bg-gray-200"></div>
                <div class="font-black text-navy uppercase leading-tight text-sm">
                    نظام إدارة<br><span class="text-xs">السكن الطلابي</span>
                </div>
            </div>

            <div class="hidden md:flex items-center gap-8 font-bold text-sm text-gray-600">
                <a href="#features" class="hover:text-navy">المميزات</a>
                <a href="#about" class="hover:text-navy">عن النظام</a>
                <div class="h-6 w-px bg-gray-200"></div>
                @if (\Illuminate\Support\Facades\Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn-premium">لوحة التحكم</a>
                    @else
                        <a href="{{ route('login') }}" class="hover:text-navy">تسجيل الدخول</a>
                        <a href="{{ route('login') }}" class="btn-premium">ابدأ الآن</a>
                    @endauth
                @endif
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section text-white pt-20">
        <div class="max-w-7xl mx-auto px-6 grid md:grid-cols-2 items-center gap-12">
            <div data-aos="fade-left">
                <h1 class="text-5xl md:text-7xl font-black leading-tight">
                    مستقبل إدارة <br> <span class="text-gradient">السكن الجامعي</span>
                </h1>
                <div class="gold-underline"></div>
                <p class="text-lg text-gray-200 font-almarai leading-relaxed mb-8 max-w-lg">
                    نظام متكامل يجمع بين الدقة الإدارية والرفاهية الطلابية، صُمم خصيصاً لمركز الأوائل الجامعي لتقديم
                    تجربة سكنية نموذجية ومؤتمتة بالكامل.
                </p>
                <div class="flex gap-4">
                    <a href="{{ route('login') }}" class="btn-premium text-lg">
                        <i class="fas fa-rocket"></i> دخول النظام
                    </a>
                    <a href="#features"
                        class="px-8 py-3 border-2 border-white/30 rounded-2xl font-bold hover:bg-white/10 transition-all">
                        اكتشف المزيد
                    </a>
                </div>
            </div>

            <div class="hidden md:block relative animate-bounce-slow">
                <div class="absolute -inset-4 bg-gold/20 blur-3xl rounded-full"></div>
                <img src="{{ asset('images/logos/scs_logo.png') }}"
                    class="relative w-full max-w-md mx-auto drop-shadow-2xl" alt="SCS Logo">
            </div>
        </div>

        <!-- Decoration -->
        <div class="absolute bottom-0 left-0 right-0 h-32 bg-gradient-to-t from-soft-white to-transparent"></div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-24 max-w-7xl mx-auto px-6">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-black text-navy">التحول الرقمي المتكامل</h2>
            <div class="gold-underline mx-auto"></div>
            <p class="text-gray-500 font-almarai">أدوات ذكية لإدارة كل تفاصيل الحياة السكنية</p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            <!-- Feature 1 -->
            <div class="feature-card bg-white p-8 rounded-3xl">
                <div class="w-16 h-16 bg-navy/5 text-navy rounded-2xl flex items-center justify-center text-3xl mb-6">
                    <i class="fas fa-utensils"></i>
                </div>
                <h3 class="text-xl font-black mb-3">وحدة التغذية الذكية</h3>
                <p class="text-gray-500 font-almarai text-sm leading-relaxed">
                    إدارة وجبات الطلاب عبر نظام QR-Code، مع تتبع الميزانيات والموردين والتصفيات الشهرية بدقة متناهية.
                </p>
            </div>

            <!-- Feature 2 -->
            <div class="feature-card bg-white p-8 rounded-3xl">
                <div class="w-16 h-16 bg-gold/10 text-gold rounded-2xl flex items-center justify-center text-3xl mb-6">
                    <i class="fas fa-shield-halved"></i>
                </div>
                <h3 class="text-xl font-black mb-3">الانضباط السلوكي</h3>
                <p class="text-gray-500 font-almarai text-sm leading-relaxed">
                    نظام متطور لرصد المخالفات والتعهدات والعقوبات، مع أرشفة رقمية كاملة لملف الطالب السلوكي.
                </p>
            </div>

            <!-- Feature 3 -->
            <div class="feature-card bg-white p-8 rounded-3xl">
                <div class="w-16 h-16 bg-navy/5 text-navy rounded-2xl flex items-center justify-center text-3xl mb-6">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <h3 class="text-xl font-black mb-3">الفوترة المركزية</h3>
                <p class="text-gray-500 font-almarai text-sm leading-relaxed">
                    إدارة السندات المالية (صرف وقبض) مع اعتماد المدير وتصدير تقارير PDF رسمية بضغطة زر.
                </p>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="bg-navy py-20 text-white overflow-hidden relative">
        <div class="max-w-7xl mx-auto px-6 grid md:grid-cols-4 gap-8 text-center relative z-10">
            <div>
                <div class="text-4xl font-black text-gold mb-2">+500</div>
                <p class="text-xs uppercase tracking-widest opacity-60">طالب جامعي</p>
            </div>
            <div>
                <div class="text-4xl font-black text-gold mb-2">100%</div>
                <p class="text-xs uppercase tracking-widest opacity-60">أتمتة العمليات</p>
            </div>
            <div>
                <div class="text-4xl font-black text-gold mb-2">+10</div>
                <p class="text-xs uppercase tracking-widest opacity-60">وحدة إدارية</p>
            </div>
            <div>
                <div class="text-4xl font-black text-gold mb-2">24/7</div>
                <p class="text-xs uppercase tracking-widest opacity-60">دعم فني</p>
            </div>
        </div>
        <!-- Decoration -->
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -mr-32 -mt-32"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-gold/5 rounded-full -ml-24 -mb-24"></div>
    </section>

    <!-- CTA Section -->
    <section id="about" class="py-24 text-center">
        <div class="max-w-3xl mx-auto px-6">
            <img src="{{ asset('images/logos/alawayil_logo.png') }}"
                class="h-24 mx-auto mb-8 opacity-20 filter grayscale" alt="">
            <h2 class="text-3xl font-black text-navy mb-6">هل أنت مستعد لتنظيم سكنك؟</h2>
            <p class="text-gray-500 font-almarai mb-10 text-lg">
                انضم الآن لمئات الطلاب والمسؤولين الذين يعتمدون على منصة إدارة السكنات الطلابية - جمعية رعاية طالب العلم
                لإدارة شؤونهم السكنية والمالية بكل سهولة
                واحترافية.
            </p>
            <div class="flex justify-center gap-4">
                <a href="{{ route('login') }}" class="btn-premium px-12 py-4 text-xl">
                    <i class="fas fa-sign-in-alt"></i> ابدأ بتسجيل الدخول
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-100 py-12 border-t border-gray-200">
        <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="flex items-center gap-3 opacity-50">
                <img src="{{ asset('images/logos/alawayil_logo.png') }}" class="h-8" alt="">
                <img src="{{ asset('images/logos/scs_logo.png') }}" class="h-8" alt="">
                <span class="text-xs font-bold text-navy mr-4">جميع الحقوق محفوظة &copy; {{ date('Y') }} لمنصة إدارة
                    السكنات الطلابية - جمعية رعاية طالب العلم</span>
            </div>
            <div class="flex gap-6 text-gray-400">
                <a href="#" class="hover:text-navy"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="hover:text-navy"><i class="fab fa-twitter"></i></a>
                <a href="#" class="hover:text-navy"><i class="fab fa-instagram"></i></a>
                <a href="#" class="hover:text-gold"><i class="fab fa-whatsapp"></i></a>
            </div>
        </div>
    </footer>

</body>

</html>
