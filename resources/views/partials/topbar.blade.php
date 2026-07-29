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
            {{-- Notifications Bell --}}
            <div class="relative" x-data="notifBell()" x-init="init()" @click.away="open = false">
                <button @click="open = !open; if(open) fetchBell()"
                        class="relative w-10 h-10 flex items-center justify-center text-gray-400 hover:text-navy hover:bg-gray-50 rounded-xl transition-all">
                    <i class="fas fa-bell text-lg"></i>
                    <span x-show="count > 0"
                          x-text="count > 9 ? '9+' : count"
                          class="absolute -top-1 -right-1 min-w-[18px] h-[18px] bg-red-500 text-white text-[10px] font-black rounded-full flex items-center justify-center px-1 border-2 border-white shadow"
                          style="display:none;"></span>
                    <span x-show="count === 0"
                          class="absolute top-2.5 right-2.5 h-2 w-2 bg-gray-200 rounded-full border-2 border-white"
                          style="display:none;"></span>
                </button>

                {{-- Dropdown --}}
                <div x-show="open" x-transition.opacity style="display:none;"
                     class="absolute left-auto right-0 mt-2 bg-white rounded-2xl shadow-xl border border-gray-100 z-50 overflow-hidden"
                     style="min-width: 300px; max-width: 340px;">
                    <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 bg-gray-50/70 gap-4">
                        <span class="text-xs font-black text-navy font-cairo whitespace-nowrap">الإشعارات والشكاوى</span>
                        <a href="{{ route('complaints.inbox') }}"
                           class="text-[10px] text-gold font-bold font-cairo hover:underline whitespace-nowrap shrink-0">عرض الكل</a>
                    </div>
                    <div class="divide-y divide-gray-50 max-h-72 overflow-y-auto">
                        <template x-if="notices.length === 0">
                            <div class="text-center py-8">
                                <i class="fas fa-check-circle text-2xl text-gray-200 mb-2 block"></i>
                                <p class="text-xs text-gray-300 font-almarai">لا توجد إشعارات جديدة</p>
                            </div>
                        </template>
                        <template x-for="n in notices" :key="n.id">
                            <a :href="'/complaints/' + n.id"
                               class="flex items-start gap-3 px-4 py-3 hover:bg-blue-50/40 transition-all block">
                                <div :class="n.urgent ? 'bg-red-100 text-red-500' : 'bg-navy/10 text-navy'"
                                     class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 mt-0.5">
                                    <i class="fas" :class="n.urgent ? 'fa-exclamation-circle' : 'fa-envelope'"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-bold text-navy font-cairo truncate" x-text="n.subject"></p>
                                    <p class="text-[10px] text-gray-400 font-almarai" x-text="'من: ' + n.from"></p>
                                    <p class="text-[10px] text-gray-300 font-mono" x-text="n.time"></p>
                                </div>
                                <span class="w-2 h-2 bg-blue-500 rounded-full shrink-0 mt-2"></span>
                            </a>
                        </template>
                    </div>
                    <div class="px-4 py-3 bg-gray-50/50 border-t border-gray-100">
                        <a href="{{ route('complaints.create') }}"
                           class="flex items-center justify-center gap-2 w-full py-2.5 bg-navy text-white rounded-xl text-xs font-bold font-cairo hover:bg-gold hover:text-navy transition-all whitespace-nowrap">
                            <i class="fas fa-plus-circle shrink-0"></i>
                            <span>إشعار / شكوى جديدة</span>
                        </a>
                    </div>
                </div>
            </div>

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
                                'super-admin' => 'مدير قسم المراكز الطلابية',
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
