<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }} - تسجيل الدخول</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&family=Almarai:wght@300;400;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Global Loader */
        #global-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(255, 255, 255, 0.7);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 99999;
            visibility: hidden;
            opacity: 0;
            transition: opacity 0.3s ease, visibility 0.3s ease;
            backdrop-filter: blur(2px);
        }

        #global-loader.active {
            visibility: visible;
            opacity: 1;
        }

        .logo-loader {
            height: 80px;
            width: auto;
            margin-bottom: 1rem;
            animation: pulse-logo 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        .logo-blue {
            filter: brightness(0) saturate(100%) invert(16%) sepia(87%) saturate(2523%) hue-rotate(190deg) brightness(91%) contrast(104%);
        }

        .loading-dots {
            display: flex;
            gap: 0.5rem;
            justify-content: center;
            margin-bottom: 1rem;
        }

        .loading-dots .dot {
            width: 10px;
            height: 10px;
            background-color: var(--navy);
            border-radius: 50%;
            animation: bounce-dot 1.4s infinite ease-in-out both;
        }

        .loading-dots .dot:nth-child(1) { animation-delay: -0.32s; }
        .loading-dots .dot:nth-child(2) { animation-delay: -0.16s; }
        
        @keyframes bounce-dot {
            0%, 80%, 100% { transform: scale(0); }
            40% { transform: scale(1); }
        }

        @keyframes pulse-logo {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.05); opacity: 0.8; }
        }
        :root {
            --navy: #004274;
            --gold: #D4A044;
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        .bg-navy {
            background-color: var(--navy);
        }

        .text-navy {
            color: var(--navy);
        }

        .text-gold {
            color: var(--gold);
        }

        .border-gold {
            border-color: var(--gold);
        }

        .bg-gold {
            background-color: var(--gold);
        }

        /* Hero login section */
        .login-hero {
            min-height: 100vh;
            background: linear-gradient(135deg, #002a50 0%, #004274 60%, #00538f 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            padding: 3rem 1rem;
        }

        .login-hero::before {
            content: '';
            position: absolute;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(212, 160, 68, 0.08) 0%, transparent 70%);
            top: -100px;
            right: -100px;
            border-radius: 50%;
        }

        .login-hero::after {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.03) 0%, transparent 70%);
            bottom: -80px;
            left: -80px;
            border-radius: 50%;
        }

        /* News section */
        .news-section {
            background: #f8fafc;
            padding: 5rem 1.5rem;
        }

        /* News cards */
        .news-card {
            background: white;
            border-radius: 1.5rem;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0, 66, 116, 0.06);
            border: 1px solid #eef2f7;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .news-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 16px 40px rgba(0, 66, 116, 0.14);
        }

        /* Cover container — fixed height wrapper */
        .news-cover {
            width: 100%;
            height: 210px;
            overflow: hidden;
            background: #e9f0f6;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .news-cover img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .news-card:hover .news-cover img {
            transform: scale(1.06);
        }

        .read-more-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-top: 14px;
            padding: 8px 18px;
            background: #004274;
            color: white;
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 800;
            font-family: 'Cairo', sans-serif;
            text-decoration: none;
            transition: background 0.2s, transform 0.2s;
        }

        .read-more-btn:hover {
            background: #D4A044;
            color: #004274;
            transform: translateY(-2px);
        }

        .news-card .cover.placeholder {
            font-size: 3rem;
            color: #c8d9e8;
        }

        /* Scroll indicator */
        @keyframes bounce {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(8px);
            }
        }

        .bounce {
            animation: bounce 1.8s ease-in-out infinite;
        }

        /* News ticker */
        @keyframes ticker {
            0% {
                transform: translateX(0);
            }

            100% {
                transform: translateX(-50%);
            }
        }

        .ticker-scroll {
            animation: ticker 28s linear infinite;
            display: inline-flex;
            align-items: center;
        }

        .ticker-scroll:hover {
            animation-play-state: paused;
        }

        /* Badge colors */
        .badge-sports {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .badge-culture {
            background: #ede9fe;
            color: #7c3aed;
        }

        .badge-achievement {
            background: #fef3c7;
            color: #b45309;
        }

        .badge-general {
            background: #e0edf7;
            color: #004274;
        }

        /* Mobile Global Enhancements */
        @media (max-width: 768px) {
            .login-hero {
                padding: 1.5rem 1rem !important;
            }
            .gap-6 {
                gap: 1rem !important;
            }
            img.h-16 {
                height: 3rem !important;
            }
            img.h-14 {
                height: 2.5rem !important;
            }
        }
    </style>
</head>

<body class="font-cairo antialiased">
    <!-- Global Loader -->
    <div id="global-loader">
        <div class="flex flex-col items-center">
            <img src="{{ asset('images/logos/scs_logo.png') }}" alt="SCS Logo" class="logo-loader logo-blue">
            <div class="loading-dots">
                <span class="dot"></span>
                <span class="dot"></span>
                <span class="dot"></span>
            </div>
            <p class="font-cairo text-navy font-bold text-lg animate-pulse">جاري المعالجة...</p>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════ --}}
    {{-- SECTION 1: Login Hero --}}
    {{-- ═══════════════════════════════════════════ --}}
    <section class="login-hero">
          <a href="{{ route('welcome') }}" class="absolute top-6 right-6 md:top-8 md:right-10 bg-white/10 hover:bg-white/20 text-white px-5 py-2.5 rounded-full backdrop-blur-md border border-white/10 transition-all flex items-center gap-2 font-cairo text-sm font-bold z-50">
            <i class="fas fa-arrow-right"></i>
            العودة للرئيسية
        </a>
        <div class="w-full max-w-md relative z-10">
           

            <!-- Logos -->
            <div class="flex flex-col items-center mb-10">
                <div class="flex items-center gap-6 mb-5">
                    <img src="{{ asset('images/logos/scs_logo.png') }}" alt="SCS"
                        class="h-14 w-auto filter brightness-0 invert">
                </div>
                <h1 class="text-xl font-black text-white text-center leading-tight">
                    منصة إدارة المراكز الطلابية<br>
                    <span class="text-gold font-almarai text-xs font-bold uppercase tracking-wider">جمعية رعاية طالب العلم</span>
                </h1>
            </div>

            <!-- Login Card -->
            <div class="bg-white p-10 rounded-3xl shadow-2xl border-t-8 border-gold relative overflow-hidden">
                <div class="absolute -right-4 -top-4 opacity-[0.025]">
                    <i class="fas fa-shield-alt text-9xl text-navy"></i>
                </div>
                @yield('content')
            </div>

            <!-- Copyright -->
            <p class="mt-8 text-center text-white/40 text-xs font-almarai">
                منصة ادارة  المراكز الطلابية&copy; {{ date('Y') }}<br>
                <span class="text-[10px] opacity-50">جميع الحقوق محفوظة - جمعية رعاية طالب العلم</span>
            </p>

            <!-- Scroll Down Hint (shown only when there's news) -->
            {{-- <div id="scrollHint" class="hidden flex-col items-center mt-8 cursor-pointer"
                onclick="document.getElementById('newsSection').scrollIntoView({behavior:'smooth'})">
                <p class="text-white/50 text-xs font-almarai mb-2">آخر الأخبار أدناه</p>
                <div class="bounce text-gold text-xl"><i class="fas fa-chevron-down"></i></div>
            </div>
        </div>

        {{-- Moving particles 
        <div class="absolute inset-0 pointer-events-none overflow-hidden">
            <div class="absolute w-2 h-2 bg-gold/20 rounded-full top-1/4 left-1/4 animate-ping"
                style="animation-duration:3s"></div>
            <div class="absolute w-1 h-1 bg-white/10 rounded-full top-3/4 right-1/3 animate-ping"
                style="animation-duration:4s;animation-delay:1s"></div>
            <div class="absolute w-1.5 h-1.5 bg-gold/10 rounded-full top-1/2 left-1/6 animate-ping"
                style="animation-duration:5s;animation-delay:2s"></div>
        </div> --}}
    </section>

    {{-- ═══════════════════════════════════════════ --}}
    {{-- News Ticker Bar --}}
    {{-- ═══════════════════════════════════════════ --}}
    {{-- <div id="tickerBar" class="hidden bg-navy border-y border-white/10">
        <div class="flex items-center gap-0">
            <div class="shrink-0 flex items-center gap-2 bg-gold text-navy px-5 py-2.5 font-black text-xs">
                <i class="fas fa-satellite-dish animate-pulse"></i>
                <span>عاجل</span>
            </div>
            <div class="overflow-hidden flex-1 py-2" style="height:36px">
                <div id="tickerContent" class="ticker-scroll gap-10 h-full whitespace-nowrap"></div>
            </div>
        </div>
    </div> --}}

    {{-- ═══════════════════════════════════════════ --}}
    {{-- SECTION 2: News Feed --}}
    {{-- ═══════════════════════════════════════════ --}}
    {{-- <section id="newsSection" class="news-section hidden">
        <div class="max-w-6xl mx-auto">

            <!-- Section Header -->
            <div class="text-center mb-12">
                <div
                    class="inline-flex items-center gap-2 bg-navy/5 text-navy px-5 py-2 rounded-full text-sm font-bold font-cairo mb-4">
                    <i class="fas fa-newspaper text-gold"></i>
                    <span>أخبار المراكز السكنية</span>
                </div>
                <h2 class="text-4xl font-black text-navy">آخر الأخبار والفعاليات</h2>
                <p class="text-gray-400 font-almarai mt-3">تابع أحدث أنشطة وأخبار مراكزالطلابية</p>
            </div>

            <!-- News Grid -->
            <div id="newsGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-7">
                <!-- Filled dynamically -->
                <div class="news-card animate-pulse">
                    <div class="cover placeholder bg-gray-100"></div>
                    <div class="p-6 space-y-3">
                        <div class="h-3 bg-gray-100 rounded w-1/3"></div>
                        <div class="h-5 bg-gray-100 rounded w-4/5"></div>
                        <div class="h-3 bg-gray-100 rounded w-full"></div>
                        <div class="h-3 bg-gray-100 rounded w-2/3"></div>
                    </div>
                </div>
                <div class="news-card animate-pulse hidden md:flex flex-col">
                    <div class="cover placeholder bg-gray-100"></div>
                    <div class="p-6 space-y-3">
                        <div class="h-3 bg-gray-100 rounded w-1/3"></div>
                        <div class="h-5 bg-gray-100 rounded w-3/4"></div>
                        <div class="h-3 bg-gray-100 rounded w-full"></div>
                    </div>
                </div>
                <div class="news-card animate-pulse hidden lg:flex flex-col">
                    <div class="cover placeholder bg-gray-100"></div>
                    <div class="p-6 space-y-3">
                        <div class="h-3 bg-gray-100 rounded w-1/4"></div>
                        <div class="h-5 bg-gray-100 rounded w-4/5"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Footer --}}
   {{--footer id="newsFooter" class="hidden bg-navy py-6 text-center">
        <p class="text-white/30 text-xs font-almarai">
            منصة ادارة  المراكز الطلابية&copy; {{ date('Y') }} — جمعية رعاية طالب العلم
        </p>
    </footer> --}}

    <script>
        const categoryIcons = {
            sports: { emoji: '⚽', label: 'رياضي', cls: 'badge-sports' },
            culture: { emoji: '📚', label: 'ثقافي', cls: 'badge-culture' },
            achievement: { emoji: '🏆', label: 'إنجاز', cls: 'badge-achievement' },
            general: { emoji: '📰', label: 'عام', cls: 'badge-general' },
        };

        fetch("{{ route('news.public-feed') }}")
            .then(r => r.json())
            .then(items => {
                if (!items || items.length === 0) return;

                // Show scroll hint
                document.getElementById('scrollHint').classList.remove('hidden');
                document.getElementById('scrollHint').classList.add('flex');

                // Show news section & footer
                document.getElementById('newsSection').classList.remove('hidden');
                document.getElementById('newsFooter').classList.remove('hidden');

                // ── Ticker ──────────────────────────────
                const tickerBar = document.getElementById('tickerBar');
                const tickerContent = document.getElementById('tickerContent');
                let tickHtml = '';
                items.forEach(item => {
                    const info = categoryIcons[item.category] || categoryIcons.general;
                    tickHtml += `
                        <span class="inline-flex items-center gap-2 text-white/90 font-almarai text-sm font-bold ml-16">
                            <span>${info.emoji}</span>
                            <span style="color:var(--gold)">${item.center?.name ?? ''}</span>
                            <span class="text-white/40 mx-1">·</span>
                            <span>${item.title}</span>
                        </span>
                        <span class="text-white/15 mx-6 text-lg">◆</span>
                    `;
                });
                tickerContent.innerHTML = tickHtml + tickHtml; // duplicate for loop
                tickerBar.classList.remove('hidden');

                // ── News Cards ───────────────────────────
                const grid = document.getElementById('newsGrid');
                let cardsHtml = '';
                const baseUrl = "{{ url('/') }}";
                items.forEach(item => {
                    const info = categoryIcons[item.category] || categoryIcons.general;
                    const date = item.published_at
                        ? new Date(item.published_at).toLocaleDateString('ar-SA', { year:'numeric', month:'long', day: 'numeric' }) 
                        : '';
                    const rawText = (item.body || '').replace(/<[^>]*>/g, '');
                    const excerpt = rawText.substring(0, 130);

                    const coverHtml = item.cover_image
                        ? `<div class="news-cover"><img src="${baseUrl}/storage/${item.cover_image}" alt="${item.title}" loading="lazy" onerror="this.parentElement.innerHTML='<i class=\'fas fa-image\' style=\'font-size:3rem;color:#c8d9e8\'></i>'"></div>`
                        : `<div class="news-cover"><i class="fas fa-image" style="font-size:3rem;color:#c8d9e8"></i></div>`;

                    cardsHtml += `
                        <div class="news-card">
                            ${coverHtml}
                            <div style="padding:1.5rem;display:flex;flex-direction:column;flex:1">
                                <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;flex-wrap:wrap">
                                    <span class="${info.cls}" style="font-size:0.7rem;font-weight:900;padding:3px 10px;border-radius:999px">${info.emoji} ${info.label}</span>
                                    ${item.center?.name ? `<span style="font-size:0.72rem;color:#9ca3af;font-family:Almarai">${item.center.name}</span>` : ''}
                                    <span style="font-size:0.68rem;color:#d1d5db;font-family:monospace;margin-right:auto">${date}</span>
                                </div>
                                <h3 style="font-size:1.05rem;font-weight:900;color:#004274;line-height:1.4;margin-bottom:8px;font-family:Cairo">${item.title}</h3>
                                <p style="color:#6b7280;font-size:0.82rem;font-family:Almarai;line-height:1.7;flex:1">${excerpt}${rawText.length > 130 ? '...' : ''}</p>
                                
                                <div style="display:flex;align-items:center;justify-content:space-between;margin-top:14px">
                                    <div style="display:flex;align-items:center;gap:14px">
                                        <span style="display:flex;align-items:center;gap:5px;font-size:0.78rem;color:#ef4444;font-weight:700;background:#fef2f2;padding:4px 10px;border-radius:999px">
                                            <svg width="13" height="13" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                                            ${item.likes_count ?? 0}
                                        </span>
                                        <span style="display:flex;align-items:center;gap:5px;font-size:0.78rem;color:#6b7280;font-weight:700;background:#f3f4f6;padding:4px 10px;border-radius:999px">
                                            <svg width="13" height="13" fill="currentColor" viewBox="0 0 24 24"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-2 12H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z"/></svg>
                                            ${item.comments_count ?? 0}
                                        </span>
                                    </div>
                                    <a href="${baseUrl}/news/public/${item.id}" class="read-more-btn" style="margin-top:0">
                                        <i class="fas fa-book-open" style="font-size:0.75rem"></i>
                                        قراءة المزيد
                                    </a>
                                </div>
                            </div>
                        </div>
                    `;
                });

                grid.innerHTML = cardsHtml;
            })
            .catch(err => console.error("News Feed Error:", err));
    </script>
    <script>
        function showGlobalLoader() {
            document.getElementById('global-loader').classList.add('active');
        }

        function hideGlobalLoader() {
            document.getElementById('global-loader').classList.remove('active');
        }

        window.addEventListener('pageshow', function (event) {
            if (event.persisted) {
                hideGlobalLoader();
            }
        });

        document.addEventListener('DOMContentLoaded', function () {
            const normalForms = document.querySelectorAll('form:not([data-no-loader]):not([target="_blank"])');
            normalForms.forEach(form => {
                form.addEventListener('submit', function (e) {
                    if (this.checkValidity()) {
                        showGlobalLoader();
                    }
                });
            });

            // Show loader on page navigation
            document.addEventListener('click', function(e) {
                const link = e.target.closest('a');
                if (link && link.href && !link.target && !link.hasAttribute('download') && !link.href.includes('javascript:') && !link.href.includes('#')) {
                    if (link.origin === window.location.origin) {
                        showGlobalLoader();
                    }
                }
            });
        });
    </script>
</body>

</html>
