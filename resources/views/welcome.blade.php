<!DOCTYPE html>
<html lang="ar" dir="rtl" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>منصة إدارة المراكز الطلابية - جمعية رعاية طالب العلم</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&family=Almarai:wght@300;400;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        :root {
            --navy: #004274;
            --gold: #D4A044;
        }

        .hero-dynamic {
            height: 100vh;
            height: 100dvh;
        }

        .hero-dynamic img {
            object-position: center 40%;
        }

        @media (max-width: 767px) {
            .hero-dynamic img {
                object-position: center 30%;
            }
        }

        body {
            font-family: 'Cairo', sans-serif;
            background: #f8fafc;
            color: #1e293b;
        }

        .font-almarai {
            font-family: 'Almarai', sans-serif;
        }

        .glass {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }

        .btn-navy {
            background: linear-gradient(135deg, #004274, #0a3d62);
            color: white;
            padding: 10px 24px;
            border-radius: 12px;
            font-weight: 700;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-navy:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 66, 116, 0.25);
            color: #D4A044;
        }

        .text-navy {
            color: #004274;
        }

        .text-gold {
            color: #D4A044;
        }

        .bg-navy {
            background: #004274;
        }

        .bg-gold {
            background: #D4A044;
        }

        .border-gold {
            border-color: #D4A044;
        }

        .hover-lift {
            transition: all 0.35s cubic-bezier(.4, 0, .2, 1);
        }

        .hover-lift:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 30px -8px rgba(0, 66, 116, 0.15);
        }

        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        /* News cards */
        .news-card {
            background: white;
            border-radius: 1.5rem;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0, 66, 116, 0.06);
            border: 1px solid #eef2f7;
            transition: transform 0.3s, box-shadow 0.3s;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .news-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 16px 40px rgba(0, 66, 116, 0.14);
        }

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
            transition: transform 0.5s;
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

        .center-details-button {
            background: rgba(0, 66, 116, 0.05);
            color: #004274;
        }

        .center-details-button:hover {
            background: #004274;
            color: #D4A044 !important;
        }

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

        .badge-jobs {
            background: #d1fae5;
            color: #065f46;
        }

        /* Ticker */
        @keyframes ticker {
            0% {
                transform: translateX(0)
            }

            100% {
                transform: translateX(-50%)
            }
        }

        .ticker-scroll {
            animation: ticker 30s linear infinite;
            display: inline-flex;
            align-items: center;
        }

        .ticker-scroll:hover {
            animation-play-state: paused;
        }

        /* Fade up */
        @keyframes fadeUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-up {
            animation: fadeUp 0.8s ease forwards;
            opacity: 0;
            transform: translateY(20px);
        }

        .delay-1 {
            animation-delay: 100ms;
        }

        .delay-2 {
            animation-delay: 300ms;
        }

        .delay-3 {
            animation-delay: 500ms;
        }
    </style>
</head>

<body class="antialiased" x-data="{ mobileOpen: false, scrolled: false }" @scroll.window="scrolled=(window.pageYOffset>20)">

    <!-- HEADER -->
    <header :class="scrolled ? 'glass shadow-md py-3' : 'bg-transparent py-5'"
        class="fixed w-full z-50 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/logos/scs_logo.png') }}" class="h-12 w-auto" alt="شعار">
                <div class="hidden sm:block w-px h-8 bg-gray-300 mx-1"></div>
                <div class="hidden sm:block font-black text-navy text-sm leading-tight "> جمعية رعاية طالب
                    العلم<br><span class="text-xs font-bold text-gold-500"
                        style="color:goldenrod;font-size:17.3px">Student Care Society</span></div>
            </div>
            <nav class="hidden md:flex items-center gap-6 font-bold text-gray-700 text-sm">
                <a href="#hero" class="hover:text-gold transition-colors">الرئيسية</a>
                <a href="#centers" class="hover:text-gold transition-colors">المراكز</a>
                <a href="#about" class="hover:text-gold transition-colors">من نحن</a>
                <a href="#news" class="hover:text-gold transition-colors">الأخبار</a>
                <a href="#contact" class="hover:text-gold transition-colors">تواصل معنا</a>
            </nav>
            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn-navy hidden sm:inline-flex text-sm"><i
                            class="fas fa-desktop"></i> لوحة التحكم</a>
                @else
                    <a href="{{ route('login') }}" class="btn-navy hidden sm:inline-flex text-sm"><i
                            class="fas fa-lock"></i> تسجيل الدخول</a>
                @endauth
                <button @click="mobileOpen=!mobileOpen" class="md:hidden text-gray-700 text-2xl"><i
                        class="fas fa-bars"></i></button>
            </div>
        </div>
        <!-- Mobile Menu -->
        <div x-show="mobileOpen" x-collapse class="md:hidden glass border-t border-gray-200">
            <div class="flex flex-col items-center gap-1 py-4 font-bold text-gray-700 text-sm">
                <a href="#hero" @click="mobileOpen=false"
                    class="w-full text-center py-3 border-b border-gray-100 hover:text-navy">الرئيسية</a>
                <a href="#centers" @click="mobileOpen=false"
                    class="w-full text-center py-3 border-b border-gray-100 hover:text-navy">المراكز</a>
                <a href="#about" @click="mobileOpen=false"
                    class="w-full text-center py-3 border-b border-gray-100 hover:text-navy">من نحن</a>
                <a href="#news" @click="mobileOpen=false"
                    class="w-full text-center py-3 border-b border-gray-100 hover:text-navy">الأخبار</a>
                <a href="#contact" @click="mobileOpen=false" class="w-full text-center py-3 mb-2 hover:text-navy">تواصل
                    معنا</a>
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn-navy w-3/4 justify-center"><i class="fas fa-desktop"></i>
                        لوحة التحكم</a>
                @else
                    <a href="{{ route('login') }}" class="btn-navy w-3/4 justify-center"><i class="fas fa-lock"></i> تسجيل
                        الدخول</a>
                @endauth
            </div>
        </div>
    </header>

    <!-- HERO SLIDER -->
    @php
        $slides = [
            ['img' => asset('images/awael.jpg')],
            ['img' => asset('images/Almaali.jpg')],
            ['img' => asset('images/altafoq.jpg')],
            ['img' => asset('images/ertiqa.jpg')],
            ['img' => asset('images/nader.jpg')],
            ['img' => asset('images/raed.jpg')],
            ['img' => asset('images/saer.jpg')],
            ['img' => asset('images/um ahmed.jpg')],
            ['img' => asset('images/awael9.jpg')],
            ['img' => asset('images/awael10.jpg')],
            // ['img'=>'https://www.scs-ye.org/Uploads/caaf446e-3afc-4432-ad41-cd4887b8d88a.jpg','title'=>'دعم لا محدود لنجاحك الأكاديمي','desc'=>'نهتم بأدق التفاصيل لنوفر لك السكن المريح والأنشطة الإثرائية.','cta'=>'تعرف علينا','link'=>'#about'],
            // ['img'=>'https://www.scs-ye.org/Uploads/ee2c3eb5-b9eb-4c50-a381-9fc0054f09da.jpg','title'=>'فرص وتطوير مستمر للطلاب','desc'=>'برامج وأنشطة ثقافية ورياضية لتنمية مهاراتك وبناء شخصيتك.','cta'=>'آخر الأخبار','link'=>'#news'],
        ];
    @endphp
    <section id="hero" class="relative hero-dynamic flex items-center justify-center overflow-hidden bg-gray-900"
        x-data='{
        cur:0,
        slides: @json($slides),
        init(){ setInterval(()=>{ this.cur=(this.cur===this.slides.length-1)?0:this.cur+1 },5000) },
        prev(){ this.cur=(this.cur===0)?this.slides.length-1:this.cur-1 },
        next(){ this.cur=(this.cur===this.slides.length-1)?0:this.cur+1 }
    }'>
        <template x-for="(s,i) in slides" :key="i">
            <div x-show="cur===i" x-transition:enter="transition-opacity duration-1000"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity duration-700" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0" class="absolute inset-0">
                <div class="absolute inset-0 bg-black/55 z-10"></div>
                <img :src="s.img" class="absolute inset-0 w-full h-full object-cover" alt="">
                <div class="absolute inset-0 z-20 flex items-center justify-center text-center px-4">
                    {{-- <div class="max-w-3xl">
                    <h1 class="text-4xl md:text-6xl font-black text-white mb-6 leading-tight drop-shadow-lg" x-text="s.title"></h1>
                    <p class="text-lg md:text-xl text-gray-200 font-almarai mb-10 leading-relaxed" x-text="s.desc"></p>
                    <a :href="s.link" class="btn-navy px-10 py-4 text-lg rounded-2xl shadow-xl">
                        <span x-text="s.cta"></span> <i class="fas fa-arrow-left mr-2"></i>
                    </a>
                </div> --}}
                </div>
            </div>
        </template>
        {{-- <button @click="prev" class="absolute bottom-8 right-4 md:right-10 z-30 w-12 h-12 rounded-full bg-white/10 hover:bg-white/25 text-white backdrop-blur-sm flex items-center justify-center transition"><i class="fas fa-chevron-right"></i></button>
    <button @click="next" class="absolute bottom-8 left-4 md:left-10 z-30 w-12 h-12 rounded-full bg-white/10 hover:bg-white/25 text-white backdrop-blur-sm flex items-center justify-center transition"><i class="fas fa-chevron-left"></i></button>
    <div class="absolute bottom-8 z-30 flex gap-3">
        <template x-for="(s,i) in slides" :key="i">
            <button @click="cur=i" :class="cur===i ? 'w-8 bg-gold' : 'w-3 bg-white/50'" class="h-3 rounded-full transition-all duration-300"></button>
        </template>
    </div> --}}
        {{-- <div class="absolute bottom-0 w-full z-20 leading-none">
        <svg viewBox="0 0 1440 80" class="w-full fill-[#f8fafc]" preserveAspectRatio="none">
            <path d="M0,80L60,68C120,56,240,32,360,22C480,12,600,16,720,28C840,40,960,56,1080,60C1200,64,1320,56,1380,52L1440,48L1440,80Z"/>
        </svg>
    </div> --}}
    </section>

    <!-- CENTERS SECTION -->
    <section id="centers" class="py-14 md:py-24 bg-[#f8fafc]" x-data="{
        sel: null,
        open(c) {
            this.sel = c;
            document.body.style.overflow = 'hidden'
        },
        close() {
            this.sel = null;
            document.body.style.overflow = ''
        }
    }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-black text-navy mb-3"> مراكزنا الطلابية</h2>
                <div class="w-20 h-1.5 bg-gold rounded-full mx-auto mb-4"></div>
                <p class="text-gray-500 font-almarai max-w-xl mx-auto">بيئة سكنية محفزة تدعم الطالب وتوفر له كافة سبل
                    الراحة والتميز الأكاديمي</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($centers as $center)
                    <div
                        class="hover-lift bg-white rounded-3xl overflow-hidden border border-gray-100 shadow-sm flex flex-col h-full">
                        <div
                            class="h-44 bg-gradient-to-br from-[#004274]/5 to-[#D4A044]/10 flex items-center justify-center p-6 relative">
                            @if ($center->logo)
                                <img src="{{ asset('storage/' . $center->logo) }}"
                                    class="w-full h-full max-w-[80%] max-h-28 object-contain drop-shadow-md"
                                    alt="{{ $center->name }}">
                            @else
                                <i class="fas fa-building text-7xl text-navy/20"></i>
                            @endif
                            <span
                                class="absolute top-4 left-4 text-xs font-bold px-3 py-1 rounded-full {{ $center->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                <i
                                    class="fas {{ $center->is_active ? 'fa-check-circle' : 'fa-times-circle' }} mr-1"></i>{{ $center->is_active ? 'متاح' : 'مغلق' }}
                            </span>
                        </div>
                        <div class="p-6 flex flex-col flex-1">
                            <h3 class="text-lg md:text-xl font-black text-navy mb-2">{{ $center->name }}</h3>
                            <p class="text-gray-400 font-almarai text-sm mb-4 flex items-center gap-1"><i
                                    class="fas fa-map-marker-alt text-gold"></i> {{ $center->address }}</p>
                            <div class="flex items-center gap-3 text-xs font-bold mb-6 text-gray-500">
                                <span><i class="fas fa-user-graduate text-navy/50 mr-1"></i>
                                    {{ $center->students_count }} طالب</span>
                            </div>
                            <button type="button" @click='open(@json($center))'
                                class="center-details-button mt-auto w-full py-3 font-bold rounded-xl transition-colors">
                                عرض تفاصيل المركز <i class="fas fa-arrow-left mr-2 text-sm"></i>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-16 text-gray-400 font-almarai">لا توجد مراكز نشطة حالياً
                    </div>
                @endforelse
            </div>
        </div>

        <!-- MODAL -->
        <div x-show="sel" style="display:none"
            class="fixed inset-0 z-[200] flex items-center justify-center px-4 sm:p-6">
            <div x-show="sel" x-transition.opacity class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"
                @click="close"></div>
            <div x-show="sel" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="relative bg-white rounded-3xl shadow-2xl max-w-3xl w-full max-h-[85vh] sm:max-h-[85vh] flex flex-col overflow-hidden">
                <div class="bg-navy p-4 sm:p-6 text-white flex justify-between items-center shrink-0">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-12 h-12 sm:w-14 sm:h-14 bg-white rounded-xl flex items-center justify-center p-2 shrink-0">
                            <template x-if="sel && sel.logo">
                                <img :src="'{{ asset('storage') }}/' + sel.logo"
                                    class="max-h-full max-w-full object-contain">
                            </template>
                            <template x-if="sel && !sel.logo">
                                <i class="fas fa-building text-3xl text-navy/30"></i>
                            </template>
                        </div>
                        <div>
                            <h3 class="text-lg sm:text-xl font-black" x-text="sel ? sel.name : ''"></h3>
                            <p class="text-gold text-xs sm:text-sm font-almarai flex items-center gap-1 mt-1">
                                <i class="fas fa-map-marker-alt"></i><span x-text="sel ? sel.address : ''"></span>
                            </p>
                        </div>
                    </div>
                    <button @click="close"
                        class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-white/10 hover:bg-red-500 flex items-center justify-center transition shrink-0">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="p-4 sm:p-6 overflow-y-auto flex-1 min-h-0 font-almarai">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5 mb-6">
                        <div class="bg-blue-50 rounded-2xl p-4 sm:p-5 border border-blue-100">
                            <h4 class="font-bold text-navy font-cairo mb-2 flex items-center gap-2"><i
                                    class="fas fa-eye text-blue-500"></i> رؤية المركز </h4>
                            <p class="text-gray-600 text-sm leading-relaxed"
                                x-text="sel?.vision || 'الريادة في توفير بيئة سكنية وتربوية جاذبة تُخرّج كفاءات علمية وقيادية تساهم في نهضة المجتمع.'">
                            </p>
                        </div>
                        <div class="bg-emerald-50 rounded-2xl p-4 sm:p-5 border border-emerald-100">
                            <h4 class="font-bold text-navy font-cairo mb-2 flex items-center gap-2"><i
                                    class="fas fa-bullseye text-emerald-500"></i> رسالة المركز </h4>
                            <p class="text-gray-600 text-sm leading-relaxed"
                                x-text="sel?.message || 'رعاية الطلاب الجامعيين وتقديم برامج نوعية لتطوير قدراتهم في ظل سكن مهيأ وخدمات متكاملة.'">
                            </p>
                        </div>
                    </div>
<<<<<<< HEAD
                    <div>
                        <h3 class="text-lg sm:text-xl font-black" x-text="sel ? sel.name : ''"></h3>
                        <p class="text-gold text-xs sm:text-sm font-almarai flex items-center gap-1 mt-1">
                            <i class="fas fa-map-marker-alt"></i><span x-text="sel ? sel.address : ''"></span>
                        </p>
                    </div>
                </div>
                <button @click="close" class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-white/10 hover:bg-red-500 flex items-center justify-center transition shrink-0">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-4 sm:p-6 overflow-y-auto flex-1 min-h-0 font-almarai">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5 mb-6">
                    <div class="bg-blue-50 rounded-2xl p-4 sm:p-5 border border-blue-100">
                        <h4 class="font-bold text-navy font-cairo mb-2 flex items-center gap-2"><i class="fas fa-eye text-blue-500"></i> رؤية المركز </h4>
                        <p class="text-gray-600 text-sm leading-relaxed" x-text="sel?.vision || 'الريادة في توفير بيئة سكنية وتربوية جاذبة تُخرّج كفاءات علمية وقيادية تساهم في نهضة المجتمع.'"></p>
                    </div>
                    <div class="bg-emerald-50 rounded-2xl p-4 sm:p-5 border border-emerald-100">
                        <h4 class="font-bold text-navy font-cairo mb-2 flex items-center gap-2"><i class="fas fa-bullseye text-emerald-500"></i> رسالة المركز </h4>
                        <p class="text-gray-600 text-sm leading-relaxed" x-text="sel?.message || 'رعاية الطلاب الجامعيين وتقديم برامج نوعية لتطوير قدراتهم في ظل سكن مهيأ وخدمات متكاملة.'"></p>
                    </div>
                </div>
                <div class="mb-6">
                    <h4 class="font-bold text-navy font-cairo mb-3 flex items-center gap-2"><i class="fas fa-list-check text-gold"></i> أهداف المركز</h4>
                    <ul class="list-disc list-inside text-gray-600 text-sm leading-relaxed space-y-1 pr-2" x-show="sel?.goals">
                        <template x-for="line in (sel?.goals || '').split('\n').filter(l => l.trim() !== '')">
                            <li x-text="line.trim().replace(/^[-*•]+/, '').trim()"></li>
                        </template>
                    </ul>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3" x-show="!sel?.goals">
                        <div class="bg-gray-50 rounded-xl p-3 text-center border border-gray-100 hover:border-gold transition"><i class="fas fa-graduation-cap text-navy text-xl mb-1 block"></i><span class="text-xs font-bold">التفوق العلمي</span></div>
                        <div class="bg-gray-50 rounded-xl p-3 text-center border border-gray-100 hover:border-gold transition"><i class="fas fa-users text-navy text-xl mb-1 block"></i><span class="text-xs font-bold">بناء العلاقات</span></div>
                        <div class="bg-gray-50 rounded-xl p-3 text-center border border-gray-100 hover:border-gold transition"><i class="fas fa-lightbulb text-navy text-xl mb-1 block"></i><span class="text-xs font-bold">تنمية المهارات</span></div>
                        <div class="bg-gray-50 rounded-xl p-3 text-center border border-gray-100 hover:border-gold transition"><i class="fas fa-hand-holding-heart text-navy text-xl mb-1 block"></i><span class="text-xs font-bold">المجتمع</span></div>
                    </div>
                </div>
                <div class="mb-4">
                    <h4 class="font-bold text-navy font-cairo mb-3 flex items-center gap-2"><i class="fas fa-gem text-gold"></i> قيمنا </h4>
                    <ul class="list-disc list-inside text-gray-600 text-sm leading-relaxed space-y-1 pr-2" x-show="sel?.values">
                        <template x-for="line in (sel?.values || '').split('\n').filter(l => l.trim() !== '')">
                            <li x-text="line.trim().replace(/^[-*•]+/, '').trim()"></li>
                        </template>
                    </ul>
                    <div class="flex flex-wrap gap-2" x-show="!sel?.values">
                        <span class="px-4 py-1.5 bg-navy/5 text-navy rounded-full text-sm font-bold">الأمانة</span>
                        <span class="px-4 py-1.5 bg-navy/5 text-navy rounded-full text-sm font-bold">التميز</span>
                        <span class="px-4 py-1.5 bg-navy/5 text-navy rounded-full text-sm font-bold">التعاون</span>
                        <span class="px-4 py-1.5 bg-navy/5 text-navy rounded-full text-sm font-bold">الاحترام</span>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-4 border-t border-gray-100">
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                        <div class="w-10 h-10 bg-navy/10 rounded-full flex items-center justify-center text-navy"><i class="fas fa-phone"></i></div>
                        <div><p class="text-xs text-gray-400">الهاتف</p><p class="text-sm font-bold" dir="ltr" x-text="sel?.phone || 'غير متوفر'"></p></div>
                    </div>
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                        <div class="w-10 h-10 bg-gold/10 rounded-full flex items-center justify-center text-gold"><i class="fas fa-envelope"></i></div>
                        <div><p class="text-xs text-gray-400">البريد الإلكتروني</p><p class="text-sm font-bold" x-text="sel?.email || 'غير متوفر'"></p></div>
                    </div>
                </div>
            </div>
            <div class="p-3 sm:p-4 bg-gray-50 border-t border-gray-100 flex justify-between items-center flex-wrap gap-2">
                <div class="flex gap-2">
                    <template x-if="sel?.facebook_link">
                        <a :href="sel.facebook_link" target="_blank" class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-blue-100 text-blue-600 hover:bg-blue-600 hover:text-white transition flex items-center justify-center"><i class="fab fa-facebook-f"></i></a>
                    </template>
                    <template x-if="sel?.whatsapp_link">
                        <a :href="sel.whatsapp_link.startsWith('http') ? sel.whatsapp_link : 'https://wa.me/' + sel.whatsapp_link.replace(/\D/g, '')" target="_blank" class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-green-100 text-green-600 hover:bg-green-600 hover:text-white transition flex items-center justify-center"><i class="fab fa-whatsapp"></i></a>
                    </template>
                    <template x-if="sel?.instagram_link">
                        <a :href="sel.instagram_link" target="_blank" class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-red-100 text-red-600 hover:bg-red-600 hover:text-white transition flex items-center justify-center"><i class="fab fa-instagram"></i></a>
                    </template>
                </div>
                <a :href="sel?.location_link ? sel.location_link : ('https://maps.google.com/?q='+(sel?sel.address:''))" target="_blank" class="btn-navy px-4 py-2 sm:px-5 text-xs sm:text-sm rounded-xl">
                    <i class="fas fa-map-marked-alt"></i> فتح على الخريطة
                </a>
            </div>
        </div>
    </div>
</section>

<!-- ABOUT SECTION -->
<section id="about" class="py-24 bg-white relative overflow-hidden">
    <div class="absolute top-0 right-0 w-96 h-96 bg-navy/3 rounded-full blur-3xl -mr-48 -mt-48 pointer-events-none"></div>
    <div class="absolute bottom-0 left-0 w-72 h-72 bg-gold/5 rounded-full blur-3xl -ml-36 -mb-36 pointer-events-none"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-16 items-stretch">
            <div class="flex flex-col justify-center" style='margin-left:25px'>
                <span class="inline-block bg-navy/5 text-navy text-sm font-bold px-4 py-2 rounded-full mb-4 self-start">من نحن؟</span>
                <h2 class="text-4xl font-black text-navy mb-4 leading-snug">جمعية رعاية طالب العلم</h2>
                <div class="w-16 h-1.5 bg-gold rounded-full mb-6"></div>
                <p class="text-gray-600 font-almarai text-lg leading-relaxed mb-8">
                    جمعية رائدة في تقديم خدمات متكاملة لرعاية الطلاب الجامعيين. نسعى لتوفير بيئة سكنية وتعليمية وتثقيفية تساهم في إعداد جيل متميز علمياً ومهارياً وقيمياً.
                </p>
                <div class="grid grid-cols-2 gap-6 mb-8 font-almarai">
                    <div class="p-4 rounded-2xl border border-navy/10 bg-navy/3 hover:border-gold transition">
                        <div class="w-10 h-10 bg-navy/10 text-navy rounded-lg flex items-center justify-center mb-3"><i class="fas fa-eye"></i></div>
                        <h4 class="font-bold text-gray-800 mb-1">رؤيتنا</h4>
                        <p class="text-xs text-gray-500 " style='font-size: 15px'>فرص تعليمية مستدامة لمخرجات وطنية فاعلة</p>
                    </div>
                    <div class="p-4 rounded-2xl border border-gold/20 bg-gold/5 hover:border-gold transition">
                        <div class="w-10 h-10 bg-gold/15 text-gold rounded-lg flex items-center justify-center mb-3"><i class="fas fa-bullseye"></i></div>
                        <h4 class="font-bold text-gray-800 mb-1">رسالتنا</h4>
                        <p class="text-xs text-gray-500 " style='font-size: 15px'>تمكين الأفراد من مواصلة تعليمهم وتطوير قدراتهم؛ لبناء وطنهم، من خلال بيئة تعليمية مستدامة ومبادرات مبتكرة وشراكات فاعلة وفق أفضل الممارسات.</p>
                    </div>
                    <div class="p-4 rounded-2xl border border-gray-100 bg-gray-50 hover:border-gold transition">
                        <div class="w-10 h-10 bg-emerald-100 text-emerald-600 rounded-lg flex items-center justify-center mb-3"><i class="fas fa-list-check"></i></div>
                        <h4 class="font-bold text-gray-800 mb-1">أهدافنا</h4>
                        <ul class="list-disc list-inside text-[15px] text-gray-500 space-y-1.5 mt-2">
                            <li>توسيع فرص الوصول إلى التعليم المستدام.</li>
                            <li>تطوير المراكز الطلابية.</li>
                            <li>التميز في تحسين المخرجات التعليمية.</li>
                            <li>تعزيز الصورة الذهنية الإيجابية.</li>
                            <li>تطوير الأداء المؤسسي.</li>
                            <li>الإبداع في إعداد وتنفيذ المشاريع التعليمية.</li>
                            <li>تعزيز الشراكات لدعم التعليم.</li>
                            <li>الاستدامة في المشاريع التعليمية.</li>
=======
                    <div class="mb-6">
                        <h4 class="font-bold text-navy font-cairo mb-3 flex items-center gap-2"><i
                                class="fas fa-list-check text-gold"></i> أهداف المركز</h4>
                        <ul class="list-disc list-inside text-gray-600 text-sm leading-relaxed space-y-1 pr-2"
                            x-show="sel?.goals">
                            <template x-for="line in (sel?.goals || '').split('\n').filter(l => l.trim() !== '')">
                                <li x-text="line.trim().replace(/^[-*•]+/, '').trim()"></li>
                            </template>
>>>>>>> 02e58f1abaa77e53728027b8ae5a3c5d86b8f899
                        </ul>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3" x-show="!sel?.goals">
                            <div
                                class="bg-gray-50 rounded-xl p-3 text-center border border-gray-100 hover:border-gold transition">
                                <i class="fas fa-graduation-cap text-navy text-xl mb-1 block"></i><span
                                    class="text-xs font-bold">التفوق العلمي</span>
                            </div>
                            <div
                                class="bg-gray-50 rounded-xl p-3 text-center border border-gray-100 hover:border-gold transition">
                                <i class="fas fa-users text-navy text-xl mb-1 block"></i><span
                                    class="text-xs font-bold">بناء العلاقات</span>
                            </div>
                            <div
                                class="bg-gray-50 rounded-xl p-3 text-center border border-gray-100 hover:border-gold transition">
                                <i class="fas fa-lightbulb text-navy text-xl mb-1 block"></i><span
                                    class="text-xs font-bold">تنمية المهارات</span>
                            </div>
                            <div
                                class="bg-gray-50 rounded-xl p-3 text-center border border-gray-100 hover:border-gold transition">
                                <i class="fas fa-hand-holding-heart text-navy text-xl mb-1 block"></i><span
                                    class="text-xs font-bold">المجتمع</span>
                            </div>
                        </div>
                    </div>
<<<<<<< HEAD
                    <div class="p-4 rounded-2xl border border-gray-100 bg-gray-50 hover:border-gold transition">
                        <div class="w-10 h-10 bg-purple-100 text-purple-600 rounded-lg flex items-center justify-center mb-3"><i class="fas fa-gem"></i></div>
                        <h4 class="font-bold text-gray-800 mb-2">قيمنا</h4>
                        <div class="flex flex-wrap gap-2text-[15px]">
                            <span class="px-3 py-1.5 bg-purple-100/50 text-purple-700 rounded-lg text-xs font-bold border border-purple-100">الاستدامة</span>
                            <span class="px-3 py-1.5 bg-purple-100/50 text-purple-700 rounded-lg text-xs font-bold border border-purple-100">الشراكة</span>
                            <span class="px-3 py-1.5 bg-purple-100/50 text-purple-700 rounded-lg text-xs font-bold border border-purple-100">روح الفريق</span>
                            <span class="px-3 py-1.5 bg-purple-100/50 text-purple-700 rounded-lg text-xs font-bold border border-purple-100">الشفافية</span>
                            <span class="px-3 py-1.5 bg-purple-100/50 text-purple-700 rounded-lg text-xs font-bold border border-purple-100">التمكين</span>
=======
                    <div class="mb-4">
                        <h4 class="font-bold text-navy font-cairo mb-3 flex items-center gap-2"><i
                                class="fas fa-gem text-gold"></i> قيمنا </h4>
                        <ul class="list-disc list-inside text-gray-600 text-sm leading-relaxed space-y-1 pr-2"
                            x-show="sel?.values">
                            <template x-for="line in (sel?.values || '').split('\n').filter(l => l.trim() !== '')">
                                <li x-text="line.trim().replace(/^[-*•]+/, '').trim()"></li>
                            </template>
                        </ul>
                        <div class="flex flex-wrap gap-2" x-show="!sel?.values">
                            <span class="px-4 py-1.5 bg-navy/5 text-navy rounded-full text-sm font-bold">الأمانة</span>
                            <span class="px-4 py-1.5 bg-navy/5 text-navy rounded-full text-sm font-bold">التميز</span>
                            <span class="px-4 py-1.5 bg-navy/5 text-navy rounded-full text-sm font-bold">التعاون</span>
                            <span
                                class="px-4 py-1.5 bg-navy/5 text-navy rounded-full text-sm font-bold">الاحترام</span>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-4 border-t border-gray-100">
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                            <div class="w-10 h-10 bg-navy/10 rounded-full flex items-center justify-center text-navy">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400">الهاتف</p>
                                <p class="text-sm font-bold" dir="ltr" x-text="sel?.phone || 'غير متوفر'"></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                            <div class="w-10 h-10 bg-gold/10 rounded-full flex items-center justify-center text-gold">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400">البريد الإلكتروني</p>
                                <p class="text-sm font-bold" x-text="sel?.email || 'غير متوفر'"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div
                    class="p-3 sm:p-4 bg-gray-50 border-t border-gray-100 flex justify-between items-center flex-wrap gap-2">
                    <div class="flex gap-2">
                        <template x-if="sel?.facebook_link">
                            <a :href="sel.facebook_link" target="_blank"
                                class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-blue-100 text-blue-600 hover:bg-blue-600 hover:text-white transition flex items-center justify-center"><i
                                    class="fab fa-facebook-f"></i></a>
                        </template>
                        <template x-if="sel?.whatsapp_link">
                            <a :href="sel.whatsapp_link.startsWith('http') ? sel.whatsapp_link : 'https://wa.me/' + sel
                                .whatsapp_link.replace(/\D/g, '')"
                                target="_blank"
                                class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-green-100 text-green-600 hover:bg-green-600 hover:text-white transition flex items-center justify-center"><i
                                    class="fab fa-whatsapp"></i></a>
                        </template>
                        <template x-if="sel?.instagram_link">
                            <a :href="sel.instagram_link" target="_blank"
                                class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-red-100 text-red-600 hover:bg-red-600 hover:text-white transition flex items-center justify-center"><i
                                    class="fab fa-instagram"></i></a>
                        </template>
                    </div>
                    <a :href="sel?.location_link ? sel.location_link : ('https://maps.google.com/?q=' + (sel ? sel.address : ''))"
                        target="_blank" class="btn-navy px-4 py-2 sm:px-5 text-xs sm:text-sm rounded-xl">
                        <i class="fas fa-map-marked-alt"></i> فتح على الخريطة
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- ABOUT SECTION -->
    <section id="about" class="py-14 md:py-24 bg-white relative overflow-hidden">
        <div
            class="absolute top-0 right-0 w-48 md:w-96 h-48 md:h-96 bg-navy/3 rounded-full blur-3xl -mr-24 md:-mr-48 -mt-24 md:-mt-48 pointer-events-none">
        </div>
        <div
            class="absolute bottom-0 left-0 w-36 md:w-72 h-36 md:h-72 bg-gold/5 rounded-full blur-3xl -ml-18 md:-ml-36 -mb-18 md:-mb-36 pointer-events-none">
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-16 items-stretch">
                <div class="flex flex-col justify-center">
                    <span
                        class="inline-block bg-navy/5 text-navy text-sm font-bold px-4 py-2 rounded-full mb-4 self-start">من
                        نحن؟</span>
                    <h2 class="text-3xl md:text-4xl font-black text-navy mb-4 leading-snug">جمعية رعاية طالب العلم</h2>
                    <div class="w-16 h-1.5 bg-gold rounded-full mb-6"></div>
                    <p class="text-gray-600 font-almarai text-base md:text-lg leading-relaxed mb-8">
                        جمعية رائدة في تقديم خدمات متكاملة لرعاية الطلاب الجامعيين. نسعى لتوفير بيئة سكنية وتعليمية
                        وتثقيفية تساهم في إعداد جيل متميز علمياً ومهارياً وقيمياً.
                    </p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 md:gap-6 mb-8 font-almarai">
                        <div class="p-4 rounded-2xl border border-navy/10 bg-navy/3 hover:border-gold transition">
                            <div
                                class="w-10 h-10 bg-navy/10 text-navy rounded-lg flex items-center justify-center mb-3">
                                <i class="fas fa-eye"></i>
                            </div>
                            <h4 class="font-bold text-gray-800 mb-1">رؤيتنا</h4>
                            <p class="text-xs text-gray-500">فرص تعليمية مستدامة لمخرجات وطنية فاعلة</p>
                        </div>
                        <div class="p-4 rounded-2xl border border-gold/20 bg-gold/5 hover:border-gold transition">
                            <div
                                class="w-10 h-10 bg-gold/15 text-gold rounded-lg flex items-center justify-center mb-3">
                                <i class="fas fa-bullseye"></i>
                            </div>
                            <h4 class="font-bold text-gray-800 mb-1">رسالتنا</h4>
                            <p class="text-xs text-gray-500">تمكين الأفراد من مواصلة تعليمهم وتطوير قدراتهم؛ لبناء
                                وطنهم، من خلال بيئة تعليمية مستدامة ومبادرات مبتكرة وشراكات فاعلة وفق أفضل الممارسات.
                            </p>
                        </div>
                        <div class="p-4 rounded-2xl border border-gray-100 bg-gray-50 hover:border-gold transition">
                            <div
                                class="w-10 h-10 bg-emerald-100 text-emerald-600 rounded-lg flex items-center justify-center mb-3">
                                <i class="fas fa-list-check"></i>
                            </div>
                            <h4 class="font-bold text-gray-800 mb-1">أهدافنا</h4>
                            <ul class="list-disc list-inside text-[11px] text-gray-500 space-y-1.5 mt-2">
                                <li>توسيع فرص الوصول إلى التعليم المستدام.</li>
                                <li>تطوير المراكز الطلابية.</li>
                                <li>التميز في تحسين المخرجات التعليمية.</li>
                                <li>تعزيز الصورة الذهنية الإيجابية.</li>
                                <li>تطوير الأداء المؤسسي.</li>
                                <li>الإبداع في إعداد وتنفيذ المشاريع التعليمية.</li>
                                <li>تعزيز الشراكات لدعم التعليم.</li>
                                <li>الاستدامة في المشاريع التعليمية.</li>
                            </ul>
                        </div>
                        <div class="p-4 rounded-2xl border border-gray-100 bg-gray-50 hover:border-gold transition">
                            <div
                                class="w-10 h-10 bg-purple-100 text-purple-600 rounded-lg flex items-center justify-center mb-3">
                                <i class="fas fa-gem"></i>
                            </div>
                            <h4 class="font-bold text-gray-800 mb-2">قيمنا</h4>
                            <div class="flex flex-wrap gap-2">
                                <span
                                    class="px-3 py-1.5 bg-purple-100/50 text-purple-700 rounded-lg text-xs font-bold border border-purple-100">الاستدامة</span>
                                <span
                                    class="px-3 py-1.5 bg-purple-100/50 text-purple-700 rounded-lg text-xs font-bold border border-purple-100">الشراكة</span>
                                <span
                                    class="px-3 py-1.5 bg-purple-100/50 text-purple-700 rounded-lg text-xs font-bold border border-purple-100">روح
                                    الفريق</span>
                                <span
                                    class="px-3 py-1.5 bg-purple-100/50 text-purple-700 rounded-lg text-xs font-bold border border-purple-100">الشفافية</span>
                                <span
                                    class="px-3 py-1.5 bg-purple-100/50 text-purple-700 rounded-lg text-xs font-bold border border-purple-100">التمكين</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="relative flex min-h-[300px] md:min-h-[500px] items-end">
                    <div class="relative w-full overflow-hidden">
                        <img src="https://www.scs-ye.org/Uploads/8b517cd4-09e8-4bf5-b12a-c42f592c5cec.jpg"
                            class="relative z-10 h-auto w-full rounded-3xl object-cover shadow-2xl" alt="من نحن">
                        <div class="absolute -bottom-4 -left-4 h-full w-full rounded-3xl border-4 border-gold/40 z-0 hidden md:block">
>>>>>>> 02e58f1abaa77e53728027b8ae5a3c5d86b8f899
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- STATS SECTION -->
    <section class="bg-navy py-10 md:py-16 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div>
                    <div
                        class="w-14 h-14 mx-auto bg-white/10 rounded-2xl flex items-center justify-center mb-3 text-gold text-2xl">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="text-3xl md:text-4xl font-black text-gold mb-1">{{ $stats['centers'] }}</div>
                    <p class="text-sm font-almarai text-gray-300">مراكز طلابية</p>
                </div>
                <div>
                    <div
                        class="w-14 h-14 mx-auto bg-white/10 rounded-2xl flex items-center justify-center mb-3 text-gold text-2xl">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <div class="text-3xl md:text-4xl font-black text-gold mb-1">+{{ $center->students_count }}</div>
                    <p class="text-sm font-almarai text-gray-300">طالب مستفيد</p>
                </div>
                <div>
                    <div
                        class="w-14 h-14 mx-auto bg-white/10 rounded-2xl flex items-center justify-center mb-3 text-gold text-2xl">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <div class="text-3xl md:text-4xl font-black text-gold mb-1">+{{ $stats['graduates'] }}</div>
                    <p class="text-sm font-almarai text-gray-300">طالب خريج</p>
                </div>
                <div>
                    <div
                        class="w-14 h-14 mx-auto bg-white/10 rounded-2xl flex items-center justify-center mb-3 text-gold text-2xl">
                        <i class="fas fa-award"></i>
                    </div>
                    <div class="text-3xl md:text-4xl font-black text-gold mb-1">+30</div>
                    <p class="text-sm font-almarai text-gray-300">تخصص مختلف </p>
                </div>
            </div>
        </div>
    </section>

    <!-- NEWS SECTION — Same design as login page + filters -->
    <section id="news" class="py-14 md:py-24 bg-[#f8fafc]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Section Header -->
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10">
                <div>
                    <div
                        class="inline-flex items-center gap-2 bg-navy/5 text-navy px-4 py-2 rounded-full text-sm font-bold mb-3">
                        <i class="fas fa-newspaper text-gold"></i> أخبار المراكز الطلابية
                    </div>
                    <h2 class="text-3xl md:text-4xl font-black text-navy"> آخر الأخبار والفعاليات</h2>
                    <div class="w-20 h-1.5 bg-gold rounded-full mt-3"></div>
                </div>
                <div>
                    <select id="centerFilter" onchange="filterNews()"
                        class="border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-almarai focus:ring-2 focus:ring-[#004274] shadow-sm bg-white">
                        <option value=""> جميع المراكز</option>
                        @foreach ($allCenters as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Category Filter Tabs -->
            <div class="flex overflow-x-auto hide-scrollbar gap-2 mb-10 pb-2">
                <button onclick="setCategory('all')" id="tab-all"
                    class="tab-btn active px-6 py-2.5 rounded-full font-bold whitespace-nowrap text-sm bg-navy text-white shadow-md">الكل</button>
                <button onclick="setCategory('general')" id="tab-general"
                    class="tab-btn px-6 py-2.5 rounded-full font-bold whitespace-nowrap text-sm bg-white text-gray-600 hover:bg-gray-100 transition-colors">عام</button>
                <button onclick="setCategory('sports')" id="tab-sports"
                    class="tab-btn px-6 py-2.5 rounded-full font-bold whitespace-nowrap text-sm bg-white text-gray-600 hover:bg-gray-100 transition-colors">رياضي
                </button>
                <button onclick="setCategory('culture')" id="tab-culture"
                    class="tab-btn px-6 py-2.5 rounded-full font-bold whitespace-nowrap text-sm bg-white text-gray-600 hover:bg-gray-100 transition-colors">ثقافي
                </button>
                <button onclick="setCategory('achievement')" id="tab-achievement"
                    class="tab-btn px-6 py-2.5 rounded-full font-bold whitespace-nowrap text-sm bg-white text-gray-600 hover:bg-gray-100 transition-colors">إنجاز
                </button>
                <button onclick="setCategory('jobs')" id="tab-jobs"
                    class="tab-btn px-6 py-2.5 rounded-full font-bold whitespace-nowrap text-sm bg-white text-emerald-600 border border-emerald-100 hover:bg-emerald-50 transition-colors flex items-center gap-1">
                    فرص عمل للخريجين <span
                        class="bg-red-500 text-white text-[10px] px-1.5 py-0.5 rounded-full animate-pulse">جديد</span>
                </button>
            </div>

            <!-- Ticker Bar (same as login page) -->
            <div id="tickerBar" class="hidden bg-navy rounded-2xl overflow-hidden mb-10 border border-navy/30">
                <div class="flex items-center gap-0">
                    <div
                        class="shrink-0 flex items-center gap-2 bg-gold text-navy px-5 py-2.5 font-black text-xs rounded-r-xl">
                        <i class="fas fa-satellite-dish animate-pulse"></i><span>عاجل</span>
                    </div>
                    <div class="overflow-hidden flex-1 py-2" style="height:36px">
                        <div id="tickerContent" class="ticker-scroll gap-10 h-full whitespace-nowrap"></div>
                    </div>
                </div>
            </div>

            <!-- Loader -->
            <div id="newsLoader" class="flex justify-center items-center py-20">
                <i class="fas fa-circle-notch fa-spin text-4xl text-gold"></i>
            </div>

            <!-- News Grid (same card style as login page) -->
            <div id="newsGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-7 hidden"></div>

            <!-- Empty State -->
            <div id="newsEmpty"
                class="hidden text-center py-20 bg-white rounded-3xl border border-dashed border-gray-200">
                <i class="fas fa-folder-open text-6xl text-gray-300 mb-4 block"></i>
                <h3 class="text-xl font-bold text-gray-500 font-cairo">لا توجد أخبار في هذا التصنيف</h3>
            </div>

            <!-- Pagination -->
            <div id="newsPagination" class="flex justify-center gap-3 mt-10"></div>

            <!-- View All -->
            {{-- <div class="text-center mt-10">
            <a href="{{ route('news.index') }}" class="btn-navy px-10 py-3 rounded-full text-base shadow-lg">
                عرض جميع الأخبار من هنا <i class="fas fa-arrow-left mr-2"></i>
            </a>
        </div> --}}
        </div>
    </section>

    <!-- CONTACT SECTION -->
    {{-- <section id="contact" class="py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-navy rounded-[3rem] overflow-hidden shadow-2xl flex flex-col lg:flex-row min-h-[400px]">
            <div class="p-10 lg:p-14 lg:w-1/2 text-white flex flex-col justify-center">
                <h2 class="text-4xl font-black mb-3">📞 تواصل معنا</h2>
                <p class="text-gray-300 font-almarai mb-8 text-lg">نحن هنا للإجابة على استفساراتك. لا تتردد في مراسلتنا.</p>
                <div class="space-y-5 font-almarai">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-white/10 flex items-center justify-center text-gold text-xl shrink-0"><i class="fas fa-phone-alt"></i></div>
                        <div><p class="text-xs text-gray-400">رقم الهاتف</p><p class="font-bold text-lg" dir="ltr">+967 770 000 000</p></div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-white/10 flex items-center justify-center text-gold text-xl shrink-0"><i class="fas fa-envelope"></i></div>
                        <div><p class="text-xs text-gray-400">البريد الإلكتروني</p><p class="font-bold text-lg">info@alawayil.com</p></div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-white/10 flex items-center justify-center text-gold text-xl shrink-0"><i class="fas fa-map-marker-alt"></i></div>
                        <div><p class="text-xs text-gray-400">العنوان</p><p class="font-bold text-lg">الجمهورية اليمنية - حضرموت - المكلا</p></div>
                    </div>
                </div>
                <div class="flex gap-3 mt-8">
                    <a href="#" class="w-11 h-11 rounded-full bg-white/10 hover:bg-gold transition flex items-center justify-center"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="w-11 h-11 rounded-full bg-white/10 hover:bg-gold transition flex items-center justify-center"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="w-11 h-11 rounded-full bg-white/10 hover:bg-gold transition flex items-center justify-center"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="w-11 h-11 rounded-full bg-white/10 hover:bg-[#25D366] transition flex items-center justify-center"><i class="fab fa-whatsapp"></i></a>
                </div>
            </div>
            <div class="lg:w-1/2 min-h-[350px] bg-gray-200">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d123282.8596660142!2d49.07172771891963!3d14.542718165780182!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3deb5e62f5f144df%3A0xc3b5ea621c4b72dc!2sAl%20Mukalla%2C%20Yemen!5e0!3m2!1sen!2s!4v1714571256781!5m2!1sen!2s" class="w-full h-full border-0 min-h-[350px]" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>
    </div>
</section> --}}

    <!-- FOOTER -->
    <footer class="bg-gray-900 text-white pt-10 md:pt-16 pb-8 border-t-4 border-gold" id="contact">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 mb-14">
                <div>
                    <img src="{{ asset('images/logos/scs_logo.png') }}" class="h-16 mb-5 filter brightness-0 invert"
                        alt="">
                    <p class="text-gray-400 font-almarai text-sm leading-relaxed mb-5">منصة الإدارة الذكية للمراكز
                        الطلابية التابعة لجمعية رعاية طالب العلم.</p>
                    <div class="flex gap-3">
                        <a href="https://www.facebook.com/scsyeorg/"
                            class="w-10 h-10 rounded-full bg-white/10 hover:bg-gold transition flex items-center justify-center text-sm"><i
                                class="fab fa-facebook-f"></i></a>
                        <a href="https://x.com/scs_ye?lang=ar"
                            class="w-10 h-10 rounded-full bg-white/10 hover:bg-gold transition flex items-center justify-center text-sm"><i
                                class="fab fa-x-twitter"></i></a>
                        <a href="https://www.instagram.com/scs_ye/?hl=ar"
                            class="w-10 h-10 rounded-full bg-white/10 hover:bg-gold transition flex items-center justify-center text-sm"><i
                                class="fab fa-instagram"></i></a>
                        <a href="https://www.youtube.com/@scs-ye"
                            class="w-10 h-10 rounded-full bg-white/10 hover:bg-gold transition flex items-center justify-center text-sm"><i
                                class="fab fa-youtube"></i></a>
                    </div>
                </div>
                <div>
                    <h4 class="text-base md:text-lg font-bold text-gold mb-5 font-cairo">المحتوي</h4>
                    <ul class="space-y-3 font-almarai text-sm text-gray-400">
                        <li><a href="#hero" class="hover:text-white transition block"><i
                                    class="fas fa-angle-left mr-2 text-xs"></i>الرئيسية</a></li>
                        <li><a href="#centers" class="hover:text-white transition block"><i
                                    class="fas fa-angle-left mr-2 text-xs"></i>المراكز الطلابية</a></li>
                        <li><a href="#about" class="hover:text-white transition block"><i
                                    class="fas fa-angle-left mr-2 text-xs"></i>من نحن</a></li>
                        <li><a href="#news" class="hover:text-white transition block"><i
                                    class="fas fa-angle-left mr-2 text-xs"></i>الأخبار والفعاليات</a></li>
                        <li><a href="#contact" class="hover:text-white transition block"><i
                                    class="fas fa-angle-left mr-2 text-xs"></i>تواصل معنا</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-base md:text-lg font-bold text-gold mb-5 font-cairo">الخدمات</h4>
                    <ul class="space-y-3 font-almarai text-sm text-gray-400">
                        <li><a href="{{ route('login') }}" class="hover:text-white transition block"><i
                                    class="fas fa-lock mr-2 text-xs"></i>تسجيل الدخول</a></li>
                        <li><a href="#" class="hover:text-white transition block"><i
                                    class="fas fa-utensils mr-2 text-xs"></i>نظام التغذية</a></li>
                        <li><a href="#" class="hover:text-white transition block"><i
                                    class="fas fa-book-open mr-2 text-xs"></i>الانشطة و الفعاليات</a></li>
                        <li><a href="#news" onclick="setCategory('jobs')"
                                class="hover:text-white transition block"><i
                                    class="fas fa-briefcase mr-2 text-xs"></i>فرص العمل للخريجين</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-base md:text-lg font-bold text-gold mb-5 font-cairo">التواصل</h4>
                    <ul class="space-y-4 font-almarai text-sm text-gray-400">
                        <li class="flex items-start gap-3"><i
                                class="fas fa-map-marker-alt mt-1 text-gold"></i><span>اليمن، حضرموت، المكلا ،
                                باجعمان</span></li>
                        <li class="flex items-center gap-3"><i class="fas fa-phone-alt text-gold"></i><span
                                dir="ltr">+967 770892674</li>
                        <li class="flex items-center gap-3"><i
                                class="fas fa-envelope text-gold"></i><span>info@scs-ye.org</span>
                    </ul>
                </div>
            </div>
            <div
                class="border-t border-white/10 pt-6 flex flex-col md:flex-row justify-between items-center text-sm font-almarai text-gray-500">
                <p>&copy; {{ date('Y') }} جميع الحقوق محفوظة — جمعية رعاية طالب العلم</p>
                <p class="mt-2 md:mt-0">منصة إدارة المراكز الطلابية</p>
            </div>
        </div>
    </footer>

    <!-- Back to top -->
    <button id="backToTop" onclick="window.scrollTo({top:0,behavior:'smooth'})"
        class="hidden fixed bottom-8 left-8 z-50 w-12 h-12 rounded-full bg-navy text-white shadow-xl hover:bg-gold hover:-translate-y-1 transition-all flex items-center justify-center text-xl">
        <i class="fas fa-arrow-up"></i>
    </button>

    <script>
        // ── Vars ──
        let currentCategory = 'all';
        let currentPage = 1;
        const BASE = window.location.origin;
        const categoryInfo = {
            sports: {
                label: 'رياضي',
                cls: 'badge-sports',
                emoji: '⚽'
            },
            culture: {
                label: 'ثقافي',
                cls: 'badge-culture',
                emoji: '📚'
            },
            achievement: {
                label: 'إنجاز',
                cls: 'badge-achievement',
                emoji: '🏆'
            },
            jobs: {
                label: 'فرص عمل',
                cls: 'badge-jobs',
                emoji: '💼'
            },
            general: {
                label: 'عام',
                cls: 'badge-general',
                emoji: '📰'
            },
        };

        function getCatInfo(cat) {
            return categoryInfo[cat] || categoryInfo.general;
        }

        function setCategory(cat) {
            currentCategory = cat;
            currentPage = 1;
            document.querySelectorAll('.tab-btn').forEach(b => {
                b.classList.remove('bg-navy', 'text-white', 'shadow-md', 'bg-emerald-600');
                b.classList.add('bg-white', 'text-gray-600');
            });
            const el = document.getElementById('tab-' + cat);
            if (el) {
                el.classList.remove('bg-white', 'text-gray-600', 'text-emerald-600');
                el.classList.add(cat === 'jobs' ? 'bg-emerald-600' : 'bg-navy', 'text-white', 'shadow-md');
            }
            fetchNews();
        }

        function filterNews() {
            currentPage = 1;
            fetchNews();
        }

        function fetchNews() {
            const loader = document.getElementById('newsLoader');
            const grid = document.getElementById('newsGrid');
            const empty = document.getElementById('newsEmpty');
            const pages = document.getElementById('newsPagination');

            loader.classList.remove('hidden');
            grid.classList.add('hidden');
            empty.classList.add('hidden');
            pages.innerHTML = '';

            const centerId = document.getElementById('centerFilter').value;
            let url = `/api/public-news-filter?page=${currentPage}`;
            if (centerId) url += `&center_id=${centerId}`;
            if (currentCategory && currentCategory !== 'all') url += `&category=${currentCategory}`;

            fetch(url)
                .then(r => r.json())
                .then(data => {
                    loader.classList.add('hidden');
                    const items = data.data || [];

                    if (!items.length) {
                        empty.classList.remove('hidden');
                        return;
                    }

                    // Build ticker only on first load (page 1, no filters)
                    if (currentPage === 1 && !centerId && currentCategory === 'all') buildTicker(items);

                    grid.innerHTML = items.map(item => buildCard(item)).join('');
                    grid.classList.remove('hidden');

                    // Pagination
                    if (data.last_page > 1) buildPagination(data.current_page, data.last_page);
                })
                .catch(e => {
                    console.error(e);
                    loader.classList.add('hidden');
                    empty.classList.remove('hidden');
                });
        }

        function buildCard(item) {
            const info = getCatInfo(item.category);
            const date = item.published_at ?
                new Date(item.published_at).toLocaleDateString('ar-SA', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                }) :
                '';
            const rawText = (item.body || '').replace(/<[^>]*>/g, '');
            const excerpt = rawText.substring(0, 120) + (rawText.length > 120 ? '...' : '');
            const cover = item.cover_image ?
                `<img src="${BASE}/storage/${item.cover_image}" alt="${item.title}" loading="lazy" onerror="this.parentElement.innerHTML='<i class=\\'fas fa-image\\' style=\\'font-size:3rem;color:#c8d9e8\\'></i>'">` :
                `<i class='fas fa-image' style='font-size:3rem;color:#c8d9e8'></i>`;
            const isJob = item.category === 'jobs';

            return `<div class="news-card ${isJob?'border-emerald-200':''} relative">
            ${isJob?'<span class="absolute top-3 left-3 z-10 bg-emerald-500 text-white text-[11px] font-bold px-3 py-1 rounded-full shadow flex items-center gap-1"><i class=\'fas fa-briefcase\'></i> فرصة عمل جديدة</span>':''}
            <div class="news-cover">${cover}</div>
            <div style="padding:1.25rem;display:flex;flex-direction:column;flex:1">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;flex-wrap:wrap">
                    <span class="${info.cls}" style="font-size:0.7rem;font-weight:900;padding:3px 10px;border-radius:999px">${info.emoji} ${info.label}</span>
                    ${item.center?.name?`<span style="font-size:0.72rem;color:#9ca3af;font-family:Almarai">${item.center.name}</span>`:''}
                    <span style="font-size:0.68rem;color:#d1d5db;font-family:monospace;margin-right:auto">${date}</span>
                </div>
                <h3 style="font-size:1.05rem;font-weight:900;color:#004274;line-height:1.4;margin-bottom:8px;font-family:Cairo">${item.title}</h3>
                <p style="color:#6b7280;font-size:0.82rem;font-family:Almarai;line-height:1.7;flex:1">${excerpt}</p>
                <div style="display:flex;align-items:center;justify-content:space-between;margin-top:14px">
                    <div style="display:flex;align-items:center;gap:12px">
                        ${!isJob ? `
                                <span style="display:flex;align-items:center;gap:4px;font-size:0.78rem;color:#ef4444;font-weight:700;background:#fef2f2;padding:4px 10px;border-radius:999px">
                                    <svg width="12" height="12" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                                    ${item.likes_count||0}
                                </span>
                                <span style="display:flex;align-items:center;gap:4px;font-size:0.78rem;color:#6b7280;font-weight:700;background:#f3f4f6;padding:4px 10px;border-radius:999px">
                                    <svg width="12" height="12" fill="currentColor" viewBox="0 0 24 24"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-2 12H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z"/></svg>
                                    ${item.comments_count||0}
                                </span>
                                ` : ''}
                    </div>
                    <a href="${BASE}/news/public/${item.id}" class="read-more-btn" style="margin-top:0">
                        <i class="fas fa-book-open" style="font-size:0.75rem"></i> قراءة المزيد
                    </a>
                </div>
            </div>
        </div>`;
        }

        function buildTicker(items) {
            const bar = document.getElementById('tickerBar');
            const content = document.getElementById('tickerContent');
            let html = '';
            items.forEach(item => {
                const info = getCatInfo(item.category);
                html += `<span class="inline-flex items-center gap-2 text-white/90 font-almarai text-sm font-bold ml-16">
                        <span>${info.emoji}</span>
                        <span style="color:#D4A044">${item.center?.name||''}</span>
                        <span class="text-white/40 mx-1">·</span>
                        <span>${item.title}</span>
                     </span>
                     <span class="text-white/15 mx-6">◆</span>`;
            });
            content.innerHTML = html + html;
            bar.classList.remove('hidden');
        }

        function buildPagination(cur, last) {
            const el = document.getElementById('newsPagination');
            let html = '';
            for (let i = 1; i <= last; i++) {
                html +=
                    `<button onclick="currentPage=${i};fetchNews()" class="w-9 h-9 rounded-full font-bold text-sm transition ${i===cur?'bg-navy text-white':'bg-white text-gray-500 hover:bg-gray-100'}">${i}</button>`;
            }
            el.innerHTML = html;
        }

        // ── Back to top ──
        window.addEventListener('scroll', () => {
            const btn = document.getElementById('backToTop');
            if (window.pageYOffset > 500) btn.classList.remove('hidden');
            else btn.classList.add('hidden');
        });

        // ── Init ──
        document.addEventListener('DOMContentLoaded', () => {
            fetchNews();
        });
    </script>
</body>

</html>
