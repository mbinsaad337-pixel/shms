<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }} - @yield ('title')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&family=Almarai:wght@300;400;700;800&display=swap"
        rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        :root {
            --primary-navy: #004274;
            --primary-gold: #D4A044;
            --secondary-gold: #f4be5e;
            --bg-light: #f8fafc;
        }

        .font-cairo {
            font-family: 'Cairo', sans-serif;
        }

        .font-almarai {
            font-family: 'Almarai', sans-serif;
        }

        .bg-navy {
            background-color: var(--primary-navy);
        }

        .text-navy {
            color: var(--primary-navy);
        }

        .bg-gold {
            background-color: var(--primary-gold);
        }

        .text-gold {
            color: var(--primary-gold);
        }

        .text-primary {
            color: var(--primary-navy);
        }

        .bg-primary {
            background-color: var(--primary-navy);
        }

        .border-primary {
            border-color: var(--primary-navy);
        }

        .sidebar-active {
            background: linear-gradient(to left, rgba(212, 160, 68, 0.15), rgba(212, 160, 68, 0.05));
            color: var(--primary-gold) !important;
            border-right: 4px solid var(--primary-gold);
        }

        .btn-primary-shms {
            background-color: var(--primary-navy);
            color: white;
            transition: all 0.3s;
            border-radius: 12px;
            font-weight: 700;
        }

        .btn-primary-shms:hover {
            background-color: #083358;
            box-shadow: 0 4px 12px rgba(0, 66, 116, 0.2);
            color: var(--primary-gold);
        }

        .card-premium {
            background: white;
            border: 1px solid #f1f5f9;
            border-radius: 24px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .card-premium:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            border-color: var(--primary-gold);
        }

        /* Mobile Global Enhancements */
        @media (max-width: 768px) {
            .overflow-hidden:has(table) {
                overflow-x: auto !important;
                overflow-y: hidden !important;
                -webkit-overflow-scrolling: touch;
            }
            main.p-6 {
                padding: 1rem !important; /* Overrides p-6 to be like p-4 on mobile */
            }
            .card-premium, .bg-white.rounded-2xl {
                padding: 1rem !important; /* Smaller padding inside cards on mobile */
            }
            .gap-10 {
                gap: 1rem !important; /* Fix large logo gap in topbar */
            }
            img.h-14 {
                height: 2.5rem !important; /* Fix oversized logos on mobile */
            }
        }
    </style>
    @livewireStyles
    @stack ('styles')
</head>

<body class="bg-gray-50 font-sans text-gray-900 antialiased">
    <div class="h-screen flex overflow-hidden" x-data="{ sidebarOpen: false }">
        <!-- Sidebar -->
        @include ('partials.sidebar')

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Topbar -->
            @include ('partials.topbar')

            <!-- Page Content -->
            <main class="flex-1 relative z-0 overflow-y-auto focus:outline-none p-6">
                <!-- Alerts -->
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-4">
                    @if (session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl relative font-almarai whitespace-pre-line"
                            role="alert">
                            <span class="block sm:inline">{!! nl2br(e(session('success'))) !!}</span>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl relative font-almarai"
                            role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    @if (session('warning'))
                        <div class="mb-4 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded-xl relative font-almarai"
                            role="alert">
                            <span class="block sm:inline">{{ session('warning') }}</span>
                        </div>
                    @endif
                </div>

                @yield ('content')
            </main>
        </div>
    </div>

    @include('partials.pdf_preview')

    @livewireScripts

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Find all forms with data-confirm attribute
            const confirmForms = document.querySelectorAll('form[data-confirm]');

            confirmForms.forEach(form => {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();

                    const message = this.getAttribute('data-confirm');

                    Swal.fire({
                        title: 'تأكيد الإجراء',
                        text: message,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#2b304c', // primary color
                        cancelButtonColor: '#f97316',  // secondary color
                        confirmButtonText: 'نعم، متأكد',
                        cancelButtonText: 'إلغاء',
                        customClass: {
                            popup: 'font-cairo',
                            title: 'font-cairo font-bold',
                            content: 'font-almarai'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Let the form submit normally bypassing alpine/JS
                            HTMLFormElement.prototype.submit.call(this);
                        }
                    });
                });
            });
        });
    </script>

    @if(session('whatsapp_url'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                title: 'إرسال بيانات الدخول',
                html: '<p class="font-almarai text-gray-600">هل تريد إرسال بيانات الدخول للطالب عبر واتساب؟</p>',
                icon: 'success',
                showCancelButton: true,
                confirmButtonColor: '#25D366',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '<i class="fab fa-whatsapp ml-2"></i> إرسال عبر واتساب',
                cancelButtonText: 'تخطي',
                customClass: {
                    popup: 'font-cairo',
                    title: 'font-cairo font-bold',
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.open(@json(session('whatsapp_url')), '_blank');
                }
            });
        });
    </script>
    @endif

    {{-- Notification Bell Alpine Component --}}
    <script>
        function notifBell() {
            return {
                open: false,
                count: 0,
                notices: [],
                init() {
                    this.fetchBell();
                    // Poll every 60 seconds
                    setInterval(() => this.fetchBell(), 60000);
                },
                fetchBell() {
                    fetch('{{ route('complaints.bell') }}', {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(r => r.json())
                    .then(data => {
                        this.count   = data.count;
                        this.notices = data.notices;
                    })
                    .catch(() => {});
                }
            }
        }
    </script>

    @stack ('scripts')
</body>

</html>
