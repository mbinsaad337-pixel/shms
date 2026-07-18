<header class="bg-white border-b border-gray-100 shadow-sm sticky top-0 z-30">
    <div class="px-6 py-3 flex items-center justify-between relative">
        <div class="flex items-center gap-6">
            <!-- Mobile Menu Toggle -->
            <button @click="sidebarOpen = !sidebarOpen"
                class="md:hidden text-gray-400 hover:text-navy transition-colors p-2 focus:outline-none">
                <i class="fas fa-bars text-xl"></i>
            </button>
            <h1 class="text-xs font-black text-navy font-cairo mr-2 md:mr-0 hidden sm:block uppercase tracking-tight">
                @yield ('title')</h1>
        </div>

        <!-- Centered Logos Group -->
        <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 flex items-center gap-10 py-1">
            @if (!auth()->user()->hasRole('super-admin'))
                <img src="{{ auth()->user()->center ? auth()->user()->center->logo_url : asset('images/logos/alawayil_logo.png') }}" 
                    alt="Center Logo" class="h-14 w-auto drop-shadow-md transition-all hover:scale-105">
                <div class="h-10 w-px bg-gray-100"></div>
            @endif
            <img src="{{ asset('images/logos/scs_logo.png') }}" alt="SCS Logo"
                class="h-14 w-auto drop-shadow-md transition-all hover:scale-105">
        </div>

        <div class="flex items-center gap-4">
            <!-- Notifications -->
            <button
                class="relative w-10 h-10 flex items-center justify-center text-gray-400 hover:text-navy hover:bg-gray-50 rounded-xl transition-all">
                <i class="fas fa-bell"></i>
                <span class="absolute top-2.5 right-2.5 h-2 w-2 bg-red-500 rounded-full border-2 border-white"></span>
            </button>

            <!-- User Profile -->
            <div class="flex items-center gap-3 pl-2 border-r border-gray-100">
                <div class="text-right hidden sm:block">
                    <p class="text-xs font-black text-navy font-cairo leading-none mb-1">{{ auth()->user()->name }}</p>
                    <p class="text-[10px] text-gold font-bold font-almarai uppercase tracking-tighter">
                        @php
                            $user = auth()->user();
                            $roleName = 'student';
                            
                            if ($user && method_exists($user, 'getRoleNames')) {
                                $firstRole = $user->getRoleNames()->first();
                                if (is_string($firstRole)) {
                                    $roleName = $firstRole;
                                }
                            }

                            $roleTranslations = [
                                'super-admin' => 'المدير العام',
                                'executive-manager' => 'المدير التنفيذي',
                                'center-manager' => 'مدير المركز',
                                'housing-manager' => 'مشرف الطلاب',
                                'financial-manager' => 'المسؤول المالي',
                                'inventory-manager' => 'مسؤول العهدة',
                                'nutrition-manager' => 'مسؤول التغذية',
                                'social-manager' => 'المسؤول الاجتماعي',
                                'transport-manager' => 'مسؤول النقل',
                                'student' => 'طالب',
                                'circle-teacher' => 'مدرس حلقة',
                            ];
                            
                            $displayRole = $roleTranslations[$roleName] ?? $roleName;
                        @endphp
                        {{ $displayRole }}
                    </p>
                </div>
                <div class="relative group" x-data="{ open: false }" @click.away="open = false">
                    <button @click="open = !open" class="focus:outline-none flex items-center">
                        <img src="{{ auth()->user()->avatar ? asset('storage/' . auth()->user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=004274&color=D4A044&bold=true' }}"
                            alt="User"
                            class="h-10 w-10 rounded-xl border-2 border-gray-50 shadow-sm hover:border-gold transition-colors object-cover">
                    </button>
                    <!-- Dropdown menu -->
                    <div x-show="open" x-transition.opacity style="display: none;"
                        class="absolute left-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-2 z-50 font-cairo">
                        @if (auth()->user()->hasRole('student'))
                            <a href="{{ route('students.show', auth()->user()->student) }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-navy transition-colors text-right">
                                <i class="fas fa-user-circle ml-2 w-4 text-center"></i> ملف البيانات
                            </a>
                        @else
                            <a href="{{ route('profile.edit') }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-navy transition-colors text-right">
                                <i class="fas fa-user-edit ml-2 w-4 text-center"></i> الملف الشخصي
                            </a>
                        @endif
                        <a href="{{ route('profile.change_password.view') }}"
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-navy transition-colors text-right">
                            <i class="fas fa-key ml-2 w-4 text-center"></i> كلمة المرور
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
