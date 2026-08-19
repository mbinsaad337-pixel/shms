<!DOCTYPE html>
<html lang="ar" dir="rtl" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $center->name }} - تفاصيل المركز</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&family=Almarai:wght@300;400;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --navy: #004274;
            --gold: #D4A044;
        }

        body {
            font-family: 'Cairo', sans-serif;
            background: #f8fafc;
            color: #1e293b;
        }

        .font-almarai {
            font-family: 'Almarai', sans-serif;
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
    </style>
</head>

<body>

    <!-- Navbar -->
    <nav class="bg-white/80 backdrop-blur-md border-b border-gray-100 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between h-16">
            <a href="{{ route('welcome') }}" class="flex items-center gap-3">
                <img src="{{ asset('images/logos/scs_logo.png') }}" class="h-10" alt="SCS">
                <span class="text-navy font-black text-lg font-cairo hidden sm:block">جمعية رعاية طالب العلم</span>
            </a>
            <a href="{{ route('welcome') }}"
                class="px-5 py-2 bg-navy text-gold text-sm font-bold rounded-xl hover:bg-gold hover:text-navy transition-all flex items-center gap-2">
                <i class="fas fa-arrow-right"></i> العودة للرئيسية
            </a>
        </div>
    </nav>

    <!-- Hero Header -->
    <div class="bg-gradient-to-br from-navy via-[#00335e] to-[#00264d] text-white relative overflow-hidden">
        <div class="absolute -left-20 -bottom-20 opacity-5 pointer-events-none">
            <i class="fas fa-building text-[20rem]"></i>
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-20 relative z-10">
            <div class="flex flex-col md:flex-row items-center gap-8">
                <div
                    class="w-28 h-28 md:w-36 md:h-36 bg-white rounded-3xl flex items-center justify-center p-4 shrink-0 shadow-xl">
                    @if ($center->logo)
                        <img src="{{ asset('storage/' . $center->logo) }}" class="max-h-full max-w-full object-contain"
                            alt="{{ $center->name }}">
                    @else
                        <i class="fas fa-building text-6xl text-navy/20"></i>
                    @endif
                </div>
                <div class="text-center md:text-right">
                    <div class="flex items-center justify-center md:justify-start gap-3 mb-3">
                        <span
                            class="bg-gold/20 text-gold text-xs font-black px-3 py-1 rounded-full border border-gold/30">
                            <i class="fas fa-building ml-1"></i> مركز طلابي
                        </span>
                        <span
                            class="text-xs font-bold px-3 py-1 rounded-full {{ $center->is_active ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-400/30' : 'bg-red-500/20 text-red-300 border border-red-400/30' }}">
                            <i
                                class="fas {{ $center->is_active ? 'fa-check-circle' : 'fa-times-circle' }} ml-1"></i>{{ $center->is_active ? 'نشط' : 'مغلق' }}
                        </span>
                    </div>
                    <h1 class="text-3xl md:text-4xl font-black text-gold font-cairo mb-2">{{ $center->name }}</h1>
                    <p class="text-gray-400 font-almarai flex items-center justify-center md:justify-start gap-2">
                        <i class="fas fa-map-marker-alt text-gold"></i> {{ $center->address }}
                    </p>
                    <div class="flex items-center justify-center md:justify-start gap-4 mt-4 text-sm">
                        <span class="flex items-center gap-2 bg-white/10 px-4 py-2 rounded-xl">
                            <i class="fas fa-user-graduate text-gold"></i>
                            <span class="font-bold">{{ $center->students_count }}</span> طالب نشط
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-16 space-y-10">

        <!-- Vision & Message -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-blue-50 rounded-3xl p-6 md:p-8 border border-blue-100">
                <h3 class="font-bold text-navy font-cairo mb-3 flex items-center gap-2 text-lg">
                    <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center"><i
                            class="fas fa-eye text-blue-500"></i></div>
                    رؤية المركز
                </h3>
                <p class="text-gray-600 text-sm leading-relaxed font-almarai">
                    {{ $center->vision ?: 'الريادة في توفير بيئة سكنية وتربوية جاذبة تُخرّج كفاءات علمية وقيادية تساهم في نهضة المجتمع.' }}
                </p>
            </div>
            <div class="bg-emerald-50 rounded-3xl p-6 md:p-8 border border-emerald-100">
                <h3 class="font-bold text-navy font-cairo mb-3 flex items-center gap-2 text-lg">
                    <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center"><i
                            class="fas fa-bullseye text-emerald-500"></i></div>
                    رسالة المركز
                </h3>
                <p class="text-gray-600 text-sm leading-relaxed font-almarai">
                    {{ $center->message ?: 'رعاية الطلاب الجامعيين وتقديم برامج نوعية لتطوير قدراتهم في ظل سكن مهيأ وخدمات متكاملة.' }}
                </p>
            </div>
        </div>

        <!-- Goals -->
        <div class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-gray-100">
            <h3 class="font-bold text-navy font-cairo mb-4 flex items-center gap-2 text-lg">
                <div class="w-10 h-10 bg-gold/10 rounded-xl flex items-center justify-center"><i
                        class="fas fa-list-check text-gold"></i></div>
                أهداف المركز
            </h3>
            @if ($center->goals)
                <ul class="list-disc list-inside text-gray-600 text-sm leading-relaxed space-y-2 pr-2 font-almarai">
                    @foreach (explode("\n", $center->goals) as $line)
                        @if (trim($line) !== '')
                            <li>{{ ltrim(trim($line), '-*• ') }}</li>
                        @endif
                    @endforeach
                </ul>
            @else
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div
                        class="bg-gray-50 rounded-2xl p-4 text-center border border-gray-100 hover:border-gold transition">
                        <i class="fas fa-graduation-cap text-navy text-2xl mb-2 block"></i><span
                            class="text-sm font-bold">التفوق العلمي</span>
                    </div>
                    <div
                        class="bg-gray-50 rounded-2xl p-4 text-center border border-gray-100 hover:border-gold transition">
                        <i class="fas fa-users text-navy text-2xl mb-2 block"></i><span class="text-sm font-bold">بناء
                            العلاقات</span>
                    </div>
                    <div
                        class="bg-gray-50 rounded-2xl p-4 text-center border border-gray-100 hover:border-gold transition">
                        <i class="fas fa-lightbulb text-navy text-2xl mb-2 block"></i><span
                            class="text-sm font-bold">تنمية المهارات</span>
                    </div>
                    <div
                        class="bg-gray-50 rounded-2xl p-4 text-center border border-gray-100 hover:border-gold transition">
                        <i class="fas fa-hand-holding-heart text-navy text-2xl mb-2 block"></i><span
                            class="text-sm font-bold">خدمة المجتمع</span>
                    </div>
                </div>
            @endif
        </div>

        <!-- Values -->
        <div class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-gray-100">
            <h3 class="font-bold text-navy font-cairo mb-4 flex items-center gap-2 text-lg">
                <div class="w-10 h-10 bg-gold/10 rounded-xl flex items-center justify-center"><i
                        class="fas fa-gem text-gold"></i></div>
                قيم المركز
            </h3>
            @if ($center->values)
                <ul class="list-disc list-inside text-gray-600 text-sm leading-relaxed space-y-2 pr-2 font-almarai">
                    @foreach (explode("\n", $center->values) as $line)
                        @if (trim($line) !== '')
                            <li>{{ ltrim(trim($line), '-*• ') }}</li>
                        @endif
                    @endforeach
                </ul>
            @else
                <div class="flex flex-wrap gap-3">
                    <span class="px-5 py-2 bg-navy/5 text-navy rounded-full text-sm font-bold">الأمانة</span>
                    <span class="px-5 py-2 bg-navy/5 text-navy rounded-full text-sm font-bold">التميز</span>
                    <span class="px-5 py-2 bg-navy/5 text-navy rounded-full text-sm font-bold">التعاون</span>
                    <span class="px-5 py-2 bg-navy/5 text-navy rounded-full text-sm font-bold">الاحترام</span>
                </div>
            @endif
        </div>

        <!-- Annual Reports -->
        @if ($reports->isNotEmpty())
            <div class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-gray-100">
                <h3 class="font-bold text-navy font-cairo mb-4 flex items-center gap-2 text-lg">
                    <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center"><i
                            class="fas fa-file-alt text-emerald-500"></i></div>
                    التقارير السنوية
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($reports as $report)
                        <div
                            class="flex items-center gap-4 p-4 bg-gray-50 rounded-2xl border border-gray-100 hover:border-emerald-300 transition group">
                            <div
                                class="w-12 h-12 bg-emerald-100 rounded-2xl flex items-center justify-center text-emerald-600 shrink-0 group-hover:bg-emerald-500 group-hover:text-gold transition">
                                <i class="fas fa-file-pdf text-lg"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-navy font-cairo truncate">{{ $report->title }}</p>
                                <p class="text-xs text-gray-400 font-almarai">{{ $report->year }} —
                                    {{ $report->file_size_formatted }}</p>
                            </div>
                            <a href="{{ asset('storage/' . $report->file_path) }}" target="_blank"
                                class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center hover:bg-emerald-500 hover:text-gold transition shrink-0"
                                title="تحميل التقرير">
                                <i class="fas fa-download text-sm"></i>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Contact Info -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @if ($center->phone)
                <div
                    class="flex items-center gap-4 p-5 bg-white rounded-2xl border border-gray-100 shadow-sm hover:border-gold transition">
                    <div class="w-12 h-12 bg-navy/10 rounded-2xl flex items-center justify-center text-navy"><i
                            class="fas fa-phone text-lg"></i></div>
                    <div>
                        <p class="text-xs text-gray-400 font-almarai">الهاتف</p>
                        <p class="text-sm font-bold font-almarai" dir="ltr">{{ $center->phone }}</p>
                    </div>
                </div>
            @endif
            @if ($center->email)
                <div
                    class="flex items-center gap-4 p-5 bg-white rounded-2xl border border-gray-100 shadow-sm hover:border-gold transition">
                    <div class="w-12 h-12 bg-gold/10 rounded-2xl flex items-center justify-center text-gold"><i
                            class="fas fa-envelope text-lg"></i></div>
                    <div>
                        <p class="text-xs text-gray-400 font-almarai">البريد الإلكتروني</p>
                        <p class="text-sm font-bold font-almarai">{{ $center->email }}</p>
                    </div>
                </div>
            @endif
            @if ($center->facebook_link)
                <a href="{{ $center->facebook_link }}" target="_blank"
                    class="flex items-center gap-4 p-5 bg-white rounded-2xl border border-gray-100 shadow-sm hover:border-blue-400 transition">
                    <div class="w-12 h-12 bg-blue-100 rounded-2xl flex items-center justify-center text-blue-600"><i
                            class="fab fa-facebook-f text-lg"></i></div>
                    <div>
                        <p class="text-xs text-gray-400 font-almarai">فيسبوك</p>
                        <p class="text-sm font-bold text-blue-600 font-almarai">صفحتنا على فيسبوك</p>
                    </div>
                </a>
            @endif
            @if ($center->whatsapp_link)
                @php
                    $waLink = str_starts_with($center->whatsapp_link, 'http')
                        ? $center->whatsapp_link
                        : 'https://wa.me/' . preg_replace('/\D/', '', $center->whatsapp_link);
                @endphp
                <a href="{{ $waLink }}" target="_blank"
                    class="flex items-center gap-4 p-5 bg-white rounded-2xl border border-gray-100 shadow-sm hover:border-green-400 transition">
                    <div class="w-12 h-12 bg-green-100 rounded-2xl flex items-center justify-center text-green-600"><i
                            class="fab fa-whatsapp text-lg"></i></div>
                    <div>
                        <p class="text-xs text-gray-400 font-almarai">واتساب</p>
                        <p class="text-sm font-bold text-green-600 font-almarai">تواصل عبر واتساب</p>
                    </div>
                </a>
            @endif
            @if ($center->instagram_link)
                <a href="{{ $center->instagram_link }}" target="_blank"
                    class="flex items-center gap-4 p-5 bg-white rounded-2xl border border-gray-100 shadow-sm hover:border-red-400 transition">
                    <div class="w-12 h-12 bg-red-100 rounded-2xl flex items-center justify-center text-red-600"><i
                            class="fab fa-instagram text-lg"></i></div>
                    <div>
                        <p class="text-xs text-gray-400 font-almarai">إنستغرام</p>
                        <p class="text-sm font-bold text-red-600 font-almarai">تابعنا على إنستغرام</p>
                    </div>
                </a>
            @endif
        </div>

        <!-- Map Link -->
        <div class="text-center">
            <a href="{{ $center->location_link ?: 'https://maps.google.com/?q=' . urlencode($center->address) }}"
                target="_blank"
                class="inline-flex items-center gap-3 px-8 py-4 bg-gradient-to-r from-navy to-[#0a3d62] text-gold hover:text-white font-black text-sm rounded-2xl shadow-lg hover:shadow-xl transition-all">
                <i class="fas fa-map-marked-alt"></i> فتح على الخريطة
            </a>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-8 border-t-4 border-gold">
        <div
            class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center text-sm font-almarai text-gray-500">
            <p>&copy; {{ date('Y') }} جميع الحقوق محفوظة — جمعية رعاية طالب العلم</p>
            <a href="{{ route('welcome') }}" class="mt-3 md:mt-0 text-gold hover:text-white transition">العودة
                للرئيسية</a>
        </div>
    </footer>

</body>

</html>
