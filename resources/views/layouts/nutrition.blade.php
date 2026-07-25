<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield ('title', 'وحدة التغذية') - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&family=Almarai:wght@300;400;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --primary-navy: #004274;
            --primary-gold: #D4A044;
            --secondary-gold: #f4be5e;
            --bg-light: #f8fafc;
        }

        .bg-navy { background-color: var(--primary-navy); }
        .text-navy { color: var(--primary-navy); }
        .bg-gold { background-color: var(--primary-gold); }
        .text-gold { color: var(--primary-gold); }

        .nav-active {
            background: linear-gradient(to left, rgba(0, 66, 116, 0.05), rgba(0, 66, 116, 0.02));
            color: var(--primary-navy) !important;
            border-right: 4px solid var(--primary-gold);
        }

        @media print { .no-print { display: none !important; } }
        
        /* Mobile Global Enhancements */
        @media (max-width: 768px) {
            .gap-10 { gap: 1rem !important; }
            img.h-14 { height: 2.5rem !important; }
        }
    </style>
    @stack ('styles')
</head>

<body class="bg-gray-50 font-cairo" style="font-family:'Cairo',sans-serif;">

    <!-- Top Navbar -->
    <nav class="bg-white border-b border-gray-100 shadow-sm sticky top-0 z-50 no-print">
        <div class="max-w-screen-2xl mx-auto px-6 py-3 flex items-center justify-between relative">
            <div class="flex items-center gap-6">
                <!-- Hamburger for mobile -->
                <button onclick="toggleNutritionSidebar()"
                    class="md:hidden text-gray-400 hover:text-navy transition-colors p-2 z-50 focus:outline-none">
                    <i class="fas fa-bars text-xl"></i>
                </button>

                <!-- Module Title -->
                <div class="hidden lg:flex items-center gap-3">
                    <div class="w-8 h-8 bg-navy rounded-lg flex items-center justify-center shadow-md">
                        <i class="fas fa-utensils text-gold text-xs"></i>
                    </div>
                    <div>
                        <div class="font-black text-navy text-xs leading-none uppercase tracking-tight">وحدة التغذية</div>
                        <div class="text-[9px] text-gray-400 font-almarai italic">Nutrition Module</div>
                    </div>
                </div>
            </div>

            <!-- Centered Logos Group -->
            <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 flex items-center gap-10 py-1">
                <img src="{{ asset('images/logos/alawayil_logo.png') }}" alt="Alawayil Logo"
                    class="h-14 w-auto drop-shadow-md">
                <div class="h-10 w-px bg-gray-100"></div>
                <img src="{{ asset('images/logos/scs_logo.png') }}" alt="SCS Logo"
                    class="h-14 w-auto drop-shadow-md">
            </div>

            <!-- Right side -->
            <div class="flex items-center gap-4">
                <a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-navy text-sm font-bold flex items-center gap-1 transition-colors">
                    <i class="fas fa-home"></i> الرئيسية
                </a>
                <div class="h-6 w-px bg-gray-100 mx-2"></div>
                <div class="text-xs font-bold text-navy hidden md:block">{{ auth()->user()->name }}</div>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="w-9 h-9 bg-red-50 hover:bg-red-100 text-red-500 rounded-xl transition-all flex items-center justify-center">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Mobile Sidebar Backdrop -->
    <div id="sidebarBackdrop" onclick="toggleNutritionSidebar()" 
        class="fixed inset-0 bg-black/50 z-40 hidden md:hidden backdrop-blur-sm transition-opacity"></div>

    <div class="flex min-h-[calc(100vh-64px)] relative">
        <!-- Sidebar (White Style) -->
        <aside id="sidebar"
            class="w-64 bg-white border-l border-gray-100 shadow-sm flex-shrink-0 no-print
            md:translate-x-0 transition-transform duration-300 translate-x-full fixed inset-y-0 right-0 md:relative z-50 md:z-auto">
            <div class="p-4 space-y-1 overflow-y-auto md:sticky md:top-16 md:h-[calc(100vh-64px)] pt-16 md:pt-4">
                <!-- Center Info -->
                <div class="px-4 py-4 mb-4 rounded-2xl shadow-inner relative group" style="background: linear-gradient(to bottom right, #004274, #083358);">
                    <p class="text-[9px] font-bold uppercase mb-1" style="color: rgba(212, 160, 68, 0.8);">المركز التعليمي</p>
                    <p class="text-xs text-white font-bold line-clamp-1">{{ auth()->user()->center?->name }}</p>
                </div>

                @php
                    $navItems = [
                        ['route' => 'nutrition.dashboard', 'icon' => 'fa-chart-pie', 'label' => 'لوحة المتابعة'],
                        ['route' => 'nutrition.budgets.index', 'icon' => 'fa-file-invoice-dollar', 'label' => 'الميزانيات'],
                        ['route' => 'nutrition.subscriptions.index', 'icon' => 'fa-users', 'label' => 'المشتركون'],
                        ['route' => 'nutrition.suppliers.index', 'icon' => 'fa-truck', 'label' => 'الموردون'],
                        ['route' => 'nutrition.invoices.index', 'icon' => 'fa-receipt', 'label' => 'فواتير المشتريات'],
                        ['route' => 'nutrition.vouchers.index', 'icon' => 'fa-money-bill-wave', 'label' => 'سندات الصرف'],
                        ['route' => 'nutrition.distributions.scan', 'icon' => 'fa-qrcode', 'label' => 'توزيع الوجبات'],
                        ['route' => 'nutrition.distributions.index', 'icon' => 'fa-list-check', 'label' => 'سجل التوزيع'],
                        ['route' => 'nutrition.settlements.index', 'icon' => 'fa-balance-scale', 'label' => 'التصفية الشهرية'],
                    ];
                @endphp

                @foreach ($navItems as $item)
                    @php $isActive = request()->routeIs(rtrim($item['route'], '.index') . '*'); @endphp
                    <a href="{{ route($item['route']) }}"
                        class="flex items-center gap-3 px-4 py-3.5 rounded-2xl transition-all mb-1
                        {{ $isActive ? 'nav-active shadow-sm' : 'text-gray-500 hover:bg-gray-50' }}">
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center text-sm
                            {{ $isActive ? 'bg-navy text-gold shadow-md shadow-navy/20' : 'bg-gray-100 text-gray-400' }}">
                            <i class="fas {{ $item['icon'] }}"></i>
                        </div>
                        <span class="text-sm font-{{ $isActive ? 'bold' : 'medium' }}">{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 overflow-x-hidden">
            @yield ('content')
        </main>
    </div>

    @include('partials.pdf_preview')

    @stack ('scripts')
    <script>
        function toggleNutritionSidebar() {
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            sidebar.classList.toggle('translate-x-full');
            if (sidebar.classList.contains('translate-x-full')) {
                backdrop.classList.add('hidden');
            } else {
                backdrop.classList.remove('hidden');
            }
        }
    </script>
</body>

</html>
