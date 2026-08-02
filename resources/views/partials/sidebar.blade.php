<!-- Mobile Sidebar Backdrop -->
<div x-show="sidebarOpen" class="fixed inset-0 z-40 bg-black/50 md:hidden" style="display: none;"
    @click="sidebarOpen = false" x-transition.opacity></div>

<!-- Sidebar -->
<aside :class="sidebarOpen ? 'translate-x-0' : 'translate-x-full md:translate-x-0'"
    class="w-64 bg-navy text-white flex-shrink-0 flex flex-col shadow-2xl z-50 fixed inset-y-0 right-0 md:relative transition-transform duration-300 transform">
    <div
        class="h-20 flex flex-col items-center justify-center px-6 bg-[#083358] border-b border-gold/20 relative overflow-hidden group">
        <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform">
            <i class="fas fa-university text-7xl text-gold"></i>
        </div>
        <span class="text-lg font-black text-gold relative z-10">منصة السكن الطلابي</span>
        <span class="text-[9px] text-gray-400 font-almarai relative z-10">جمعية رعاية طالب العلم</span>
    </div>

    @php
        $isGraduateStudent = auth()->user()->student && auth()->user()->student->is_graduate;
    @endphp

    <nav class="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto custom-scrollbar">
        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}"
            class="flex items-center px-4 py-3 text-sm font-medium rounded-2xl transition-all {{ request()->routeIs('dashboard') ? 'bg-gold text-navy font-black shadow-lg shadow-gold/20' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
            <i class="fas fa-th-large h-5 w-5 ml-3"></i>
            لوحة التحكم
        </a>

        @if (!$isGraduateStudent)
            @can ('view-centers')
                <!-- Centers & Managers -->
                <div class="pt-4 pb-2">
                    <p class="px-4 text-[10px] font-bold text-gold/60 uppercase tracking-widest font-cairo mb-2">إدارة النظام
                        المركزية</p>
                    <a href="{{ route('centers.index') }}"
                        class="flex items-center px-4 py-2.5 text-sm font-medium rounded-2xl {{ request()->routeIs('centers.*') ? 'bg-white/10 text-gold font-bold' : 'text-gray-300 hover:bg-white/5' }} transition-all">
                        <i class="fas fa-building h-5 w-5 ml-3"></i>
                        المراكز الطلابية
                    </a>
                    <a href="{{ route('managers.index') }}"
                        class="flex items-center px-4 py-2.5 text-sm font-medium rounded-2xl {{ request()->routeIs('managers.*') ? 'bg-white/10 text-gold font-bold' : 'text-gray-300 hover:bg-white/5' }} transition-all">
                        <i class="fas fa-user-shield h-5 w-5 ml-3"></i>
                        إدارة مدراء المراكز
                    </a>
                    @if(auth()->user()->hasAnyRole(['super-admin', 'executive-manager']))
                        <a href="{{ route('media-officers.index') }}"
                            class="flex items-center px-4 py-2.5 text-sm font-medium rounded-2xl {{ request()->routeIs('media-officers.*') ? 'bg-white/10 text-gold font-bold' : 'text-gray-300 hover:bg-white/5' }} transition-all">
                            <i class="fas fa-bullhorn h-5 w-5 ml-3"></i>
                            إدارة مسؤولي الإعلام
                        </a>
                    @endif
                </div>
            @endcan

            @can ('view-students')
                <a href="{{ route('students.index') }}"
                    class="flex items-center px-4 py-3 text-sm font-medium rounded-2xl {{ request()->routeIs('students.index') || (request()->routeIs('students.*') && !request()->routeIs('students.alumni')) ? 'bg-white/10 text-gold font-bold shadow-sm' : 'text-gray-300 hover:bg-white/5' }} transition-all">
                    <i class="fas fa-user-graduate h-5 w-5 ml-3"></i>
                    إدارة الطلاب
                </a>
                
                @if(auth()->user()->hasRole('center-manager') || auth()->user()->hasRole('super-admin'))
                <a href="{{ route('students.alumni') }}"
                    class="flex items-center px-4 py-3 text-sm font-medium rounded-2xl {{ request()->routeIs('students.alumni') ? 'bg-white/10 text-gold font-bold shadow-sm' : 'text-gray-300 hover:bg-white/5' }} transition-all">
                    <i class="fas fa-user-tag h-5 w-5 ml-3"></i>
                    قائمة الخريجين
                </a>
                @endif

                @if(!auth()->user()->hasRole('super-admin'))
                    <a href="{{ route('student-grades.index') }}"
                        class="flex items-center px-4 py-3 text-sm font-medium rounded-2xl {{ request()->routeIs('student-grades.*') ? 'bg-white/10 text-gold font-bold shadow-sm' : 'text-gray-300 hover:bg-white/5' }} transition-all">
                        <i class="fas fa-file-invoice h-5 w-5 ml-3"></i>
                        بيانات الدرجات
                    </a>
                    <a href="{{ route('student-achievements.index') }}"
                        class="flex items-center px-4 py-3 text-sm font-medium rounded-2xl {{ request()->routeIs('student-achievements.*') ? 'bg-white/10 text-gold font-bold shadow-sm' : 'text-gray-300 hover:bg-white/5' }} transition-all">
                        <i class="fas fa-award h-5 w-5 ml-3"></i>
                        إنجازات الطلاب
                    </a>
                    {{-- Unified Administrative Actions Hub --}}
                    <a href="{{ route('administrative.index') }}"
                        class="flex items-center px-4 py-3 text-sm font-medium rounded-2xl {{ request()->routeIs('administrative.*') || request()->routeIs('violations.*') || request()->routeIs('penalties.*') || request()->routeIs('commitments.*') || request()->routeIs('absences.*') || request()->routeIs('leaves.*') ? 'bg-white/10 text-gold font-bold shadow-sm' : 'text-gray-300 hover:bg-white/5' }} transition-all">
                        <i class="fas fa-clipboard-list h-5 w-5 ml-3 text-amber-400/70"></i>
                        <span class="flex-1">الإجراءات الإدارية</span>
                        @php
                            $pendingLeaves = \App\Models\Leave::whereHas('student', fn($q) => $q->when(auth()->user()->center_id, fn($sq) => $sq->where('center_id', auth()->user()->center_id)))->where('status', 'pending')->count();
                        @endphp
                        @if($pendingLeaves > 0)
                        <span class="bg-amber-500 text-white text-[10px] font-black px-1.5 py-0.5 rounded-full min-w-[20px] text-center">
                            {{ $pendingLeaves > 9 ? '9+' : $pendingLeaves }}
                        </span>
                        @endif
                    </a>
                @endif
            @endcan

            @can ('view-rooms')
                @if(!auth()->user()->hasRole('super-admin'))
                    <a href="{{ route('rooms.index') }}"
                        class="flex items-center px-4 py-3 text-sm font-medium rounded-2xl {{ request()->routeIs('rooms.*') ? 'bg-white/10 text-gold font-bold' : 'text-gray-300 hover:bg-white/5' }} transition-all">
                        <i class="fas fa-door-open h-5 w-5 ml-3"></i>
                        السكن والغرف
                    </a>
                @endif
            @endcan

            @can ('view-meals')
                @if(!auth()->user()->hasRole('super-admin'))
                    <div class="pt-4 pb-2">
                        <p class="px-4 text-[10px] font-bold text-gold/60 uppercase tracking-widest font-cairo mb-2">نظام التغذية
                            والوجبات</p>
                        <a href="{{ route('nutrition.distributions.scan') }}"
                            class="flex items-center px-4 py-2 text-sm font-medium rounded-2xl {{ request()->routeIs('nutrition.distributions.scan') ? 'bg-white/10 text-gold font-bold' : 'text-gray-300 hover:bg-white/5' }} transition-all">
                            <i class="fas fa-qrcode h-5 w-5 ml-3"></i>
                            مسح وتوزيع الوجبات
                        </a>
                        <a href="{{ route('nutrition.attendance-reports') }}"
                            class="flex items-center px-4 py-2 text-sm font-medium rounded-2xl {{ request()->routeIs('nutrition.attendance-reports') ? 'bg-white/10 text-gold font-bold' : 'text-gray-300 hover:bg-white/5' }} transition-all">
                            <i class="fas fa-clipboard-list h-5 w-5 ml-3"></i>
                            تقارير الحضور اليومي
                        </a>
                        <a href="{{ route('nutrition.schedules.index') }}"
                            class="flex items-center px-4 py-2 text-sm font-medium rounded-2xl {{ request()->routeIs('nutrition.schedules.index') ? 'bg-white/10 text-gold font-bold' : 'text-gray-300 hover:bg-white/5' }} transition-all">
                            <i class="fas fa-clock h-5 w-5 ml-3"></i>
                            ضبط مواعيد الوجبات
                        </a>
                        <a href="{{ route('nutrition.budgets.index') }}"
                            class="flex items-center px-4 py-2 text-sm font-medium rounded-2xl {{ request()->routeIs('nutrition.budgets.*') ? 'bg-white/10 text-gold font-bold' : 'text-gray-300 hover:bg-white/5' }} transition-all">
                            <i class="fas fa-file-invoice-dollar h-5 w-5 ml-3"></i>
                            ميزانية التغذية (العهدة)
                        </a>
                        <a href="{{ route('nutrition.subscriptions.index') }}"
                            class="flex items-center px-4 py-2 text-sm font-medium rounded-2xl {{ request()->routeIs('nutrition.subscriptions.*') ? 'bg-white/10 text-gold font-bold' : 'text-gray-300 hover:bg-white/5' }} transition-all">
                            <i class="fas fa-user-check h-5 w-5 ml-3"></i>
                            اشتراكات الطلاب
                        </a>
                        <a href="{{ route('nutrition.dashboard') }}"
                            class="flex items-center px-4 py-2 text-sm font-medium rounded-2xl {{ request()->routeIs('nutrition.dashboard') ? 'bg-white/10 text-gold font-bold' : 'text-gray-300 hover:bg-white/5' }} transition-all">
                            <i class="fas fa-chart-pie h-5 w-5 ml-3"></i>
                            إحصائيات التغذية العامة
                        </a>
                    </div>
                @endif
            @endcan

            @if (auth()->user()->can('view-activities') || auth()->user()->can('view-quran-circles') || auth()->user()->hasRole('circle-teacher') || auth()->user()->can('manage-news') || auth()->user()->hasRole('media-officer'))
                <div class="pt-4 pb-2">
                    <p class="px-4 text-[10px] font-bold text-gold/60 uppercase tracking-widest font-cairo mb-2">قسم الأنشطة </p>

                    @can ('view-activities')
                        <a href="{{ route('activities.index') }}"
                            class="flex items-center px-4 py-2.5 text-sm font-medium rounded-2xl {{ request()->routeIs('activities.*') ? 'bg-white/10 text-gold font-bold' : 'text-gray-300 hover:bg-white/5' }} transition-all">
                            <i class="fas fa-calendar-day h-5 w-5 ml-3"></i>
                            إدارة الأنشطة والفعاليات
                        </a>
                        @if(!auth()->user()->hasRole('activity-assistant'))
                            <a href="{{ route('clubs.index') }}"
                                class="flex items-center px-4 py-2.5 text-sm font-medium rounded-2xl {{ request()->routeIs('clubs.*') ? 'bg-white/10 text-gold font-bold' : 'text-gray-300 hover:bg-white/5' }} transition-all">
                                <i class="fas fa-users-rectangle h-5 w-5 ml-3"></i>
                                إدارة الأندية والقيادات
                            </a>
                        @endif
                    @endcan

                    @can ('manage-news')
                        <a href="{{ route('news.index') }}"
                            class="flex items-center px-4 py-2.5 text-sm font-medium rounded-2xl {{ request()->routeIs('news.index') || request()->routeIs('news.show') || request()->routeIs('news.create') || request()->routeIs('news.edit') ? 'bg-white/10 text-gold font-bold' : 'text-gray-300 hover:bg-white/5' }} transition-all">
                            <i class="fas fa-newspaper h-5 w-5 ml-3"></i>
                            إدارة الأخبار والإعلانات
                        </a>

                        @if(auth()->user()->hasRole(['super-admin', 'media-officer']))
                            @php
                                if (\Illuminate\Support\Facades\Schema::hasColumn('news', 'status')) {
                                    $sidebarPendingNewsCount = \App\Models\News::where('status', 'pending')->orWhere(function($q){ $q->where('is_published', false)->where('status', '!=', 'rejected'); })->count();
                                } else {
                                    $sidebarPendingNewsCount = \App\Models\News::where('is_published', false)->count();
                                }
                            @endphp
                            <a href="{{ route('news.pending') }}"
                                class="flex items-center px-4 py-2.5 text-sm font-medium rounded-2xl {{ request()->routeIs('news.pending') ? 'bg-white/10 text-gold font-bold' : 'text-gray-300 hover:bg-white/5' }} transition-all">
                                <i class="fas fa-bullhorn h-5 w-5 ml-3 text-amber-400"></i>
                                <span class="flex-1">اعتمادات مسؤول الإعلام</span>
                                @if($sidebarPendingNewsCount > 0)
                                    <span class="bg-amber-500 text-white text-[10px] font-black px-1.5 py-0.5 rounded-full min-w-[20px] text-center">
                                        {{ $sidebarPendingNewsCount }}
                                    </span>
                                @endif
                            </a>
                        @endif
                    @endcan

                    @can ('view-quran-circles')
                        @if(!auth()->user()->hasRole('super-admin'))
                            <a href="{{ route('quran-circles.index') }}"
                                class="flex items-center px-4 py-2.5 text-sm font-medium rounded-2xl {{ request()->routeIs('quran-circles.*') || request()->routeIs('circle-sessions.*') ? 'bg-white/10 text-gold font-bold' : 'text-gray-300 hover:bg-white/5' }} transition-all">
                                <i class="fas fa-quran h-5 w-5 ml-3"></i>
                                الحلقات القرآنية
                            </a>
                        @endif
                    @endcan
                </div>
            @endif

            @if (auth()->user()->can('manage-users') || auth()->user()->can('view-assets') || auth()->user()->can('view-vehicles'))
                <div class="pt-4 pb-2 border-t border-white/5 mt-4">
                    <p class="px-4 text-[10px] font-bold text-gold/60 uppercase tracking-widest font-cairo mb-2">إدارة الموقع
                    </p>

                    @if(auth()->user()->can('manage-users') || auth()->user()->hasRole('super-admin'))
                        <a href="{{ route('admin.users.index') }}"
                            class="flex items-center px-4 py-2.5 text-sm font-medium rounded-2xl {{ request()->routeIs('admin.users.*') ? 'bg-white/10 text-gold font-bold' : 'text-gray-300 hover:bg-white/5 transition' }}">
                            <i class="fas fa-users-cog h-5 w-5 ml-3"></i>
                            طاقم العمل
                        </a>
                    @endif
                    @if(auth()->user()->can('view-assets') || auth()->user()->hasRole('super-admin'))
                        <a href="{{ route('assets.index') }}"
                            class="flex items-center px-4 py-2.5 text-sm font-medium rounded-2xl {{ request()->routeIs('assets.*') ? 'bg-white/10 text-gold font-bold' : 'text-gray-300 hover:bg-white/5 transition' }}">
                            <i class="fas fa-boxes h-5 w-5 ml-3"></i>
                            الأصول والعهدة العينية
                        </a>
                    @endif

                    @can ('view-vehicles')
                        @if(!auth()->user()->hasRole('super-admin'))
                            <a href="{{ route('vehicles.index') }}"
                                class="flex items-center px-4 py-2.5 text-sm font-medium rounded-2xl {{ request()->routeIs('vehicles.*') ? 'bg-white/10 text-gold font-bold' : 'text-gray-300 hover:bg-white/5 transition' }}">
                                <i class="fas fa-car h-5 w-5 ml-3"></i>
                                مركبات الطلاب
                            </a>
                        @endif
                    @endcan
                </div>
            @endif

            {{-- Complaints & Internal Notifications --}}
            @if (auth()->user()->hasAnyRole(['super-admin', 'center-manager', 'executive-manager']))
                @php
                    $unreadComplaintsCount = \App\Models\Complaint::where('receiver_id', auth()->id())
                        ->where('status', 'unread')->count();
                @endphp
                <div class="pt-4 pb-2 border-t border-white/5 mt-4">
                    <p class="px-4 text-[10px] font-bold text-gold/60 uppercase tracking-widest font-cairo mb-2">
                        الشكاوى والإشعارات
                    </p>
                    <a href="{{ route('complaints.inbox') }}"
                        class="flex items-center px-4 py-2.5 text-sm font-medium rounded-2xl {{ request()->routeIs('complaints.*') ? 'bg-white/10 text-gold font-bold' : 'text-gray-300 hover:bg-white/5' }} transition-all">
                        <i class="fas fa-inbox h-5 w-5 ml-3"></i>
                        <span class="flex-1">صندوق الوارد</span>
                        @if($unreadComplaintsCount > 0)
                            <span class="bg-red-500 text-white text-[10px] font-black px-1.5 py-0.5 rounded-full min-w-[20px] text-center">
                                {{ $unreadComplaintsCount > 9 ? '9+' : $unreadComplaintsCount }}
                            </span>
                        @endif
                    </a>
                    <a href="{{ route('complaints.sent') }}"
                        class="flex items-center px-4 py-2.5 text-sm font-medium rounded-2xl text-gray-300 hover:bg-white/5 transition-all">
                        <i class="fas fa-paper-plane h-5 w-5 ml-3 text-gold/50"></i>
                        المرسلة
                    </a>
                    <a href="{{ route('complaints.create') }}"
                        class="flex items-center px-4 py-2.5 text-sm font-medium rounded-2xl text-gray-300 hover:bg-white/5 transition-all">
                        <i class="fas fa-plus-circle h-5 w-5 ml-3 text-gold/50"></i>
                        إشعار جديد
                    </a>
                </div>
            @endif

            @if (auth()->user()->can('view-funds') || auth()->user()->can('view-vouchers') || auth()->user()->can('view-budgets') || auth()->user()->can('view-settlements') || auth()->user()->hasRole('super-admin'))
                <div class="pt-4 pb-2 border-t border-white/5 mt-4">
                    <p class="px-4 text-[10px] font-bold text-gold/60 uppercase tracking-widest font-cairo mb-2">النظام المالي
                    </p>

                    @if(auth()->user()->can('view-vouchers') || auth()->user()->hasRole('super-admin'))
                        <a href="{{ route('vouchers.index') }}"
                            class="flex items-center px-4 py-2.5 text-sm font-medium rounded-2xl {{ request()->routeIs('vouchers.*') || request()->routeIs('funds.*') ? 'bg-white/10 text-gold font-bold' : 'text-gray-300 hover:bg-white/5 transition' }}">
                            <i class="fas fa-wallet h-5 w-5 ml-3"></i>
                            الصناديق والسندات
                        </a>
                    @endif

                    {{-- Center Expenses for GM --}}
                    @if(auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('executive-manager'))
                        <a href="{{ route('center-expenses.index') }}"
                            class="flex items-center px-4 py-2.5 text-sm font-medium rounded-2xl {{ request()->routeIs('center-expenses.*') ? 'bg-white/10 text-gold font-bold' : 'text-gray-300 hover:bg-white/5 transition' }}">
                            <i class="fas fa-file-invoice-dollar h-5 w-5 ml-3"></i>
                            مصروفات المراكز
                        </a>
                    @endif

                    @if(auth()->user()->can('view-budgets') || auth()->user()->hasRole('super-admin'))
                        <a href="{{ route('budgets.index') }}"
                            class="flex items-center px-4 py-2.5 text-sm font-medium rounded-2xl {{ request()->routeIs('budgets.*') ? 'bg-white/10 text-gold font-bold' : 'text-gray-300 hover:bg-white/5 transition' }}">
                            <i class="fas fa-calculator h-5 w-5 ml-3"></i>
                            إدارة الموازنات
                        </a>
                    @endif

                    @if(auth()->user()->can('view-settlements') || auth()->user()->hasRole('super-admin'))
                        <a href="{{ route('settlements.index') }}"
                            class="flex items-center px-4 py-2.5 text-sm font-medium rounded-2xl {{ request()->routeIs('settlements.*') ? 'bg-white/10 text-gold font-bold' : 'text-gray-300 hover:bg-white/5 transition' }}">
                            <i class="fas fa-balance-scale h-5 w-5 ml-3"></i>
                            التصفيات الشهرية
                        </a>
                    @endif

                    <a href="{{ route('reports.funds.view') }}"
                        class="flex items-center px-4 py-2.5 text-sm font-medium rounded-2xl {{ request()->routeIs('reports.funds.*') ? 'bg-white/10 text-gold font-bold' : 'text-gray-300 hover:bg-white/5 transition' }}">
                        <i class="fas fa-file-invoice-dollar h-5 w-5 ml-3"></i>
                        تقرير أرصدة الصناديق
                    </a>
                </div>
            @endif

            @if (auth()->user()->student)
                <div class="pt-4 border-t border-white/5">
                    <p class="px-4 text-[10px] font-bold text-gold/60 uppercase tracking-widest font-cairo mb-2">خدمات الطلاب
                    </p>
                    <a href="{{ route('student.food-subscriptions.index') }}"
                        class="flex items-center px-4 py-2.5 text-sm font-medium rounded-2xl {{ request()->routeIs('student.food-subscriptions.*') ? 'bg-white/10 text-gold font-bold' : 'text-gray-300 hover:bg-white/5 transition' }}">
                        <i class="fas fa-utensils h-5 w-5 ml-3"></i>
                        اشتراكات التغذية الخاصة بي
                    </a>
                    <a href="{{ route('student-qr-groups.index') }}"
                        class="flex items-center px-4 py-2.5 text-sm font-medium rounded-2xl {{ request()->routeIs('student-qr-groups.*') ? 'bg-white/10 text-gold font-bold' : 'text-gray-300 hover:bg-white/5 transition' }}">
                        <i class="fas fa-qrcode h-5 w-5 ml-3"></i>
                        رموز QR المجمعة
                    </a>
                    <a href="{{ route('student.quran-circles.index') }}"
                        class="flex items-center px-4 py-2.5 text-sm font-medium rounded-2xl {{ request()->routeIs('student.quran-circles.index') ? 'bg-white/10 text-gold font-bold' : 'text-gray-300 hover:bg-white/5 transition' }}">
                        <i class="fas fa-quran h-5 w-5 ml-3"></i>
                        حلقاتي القرآنية
                    </a>
                    <a href="{{ route('student.leave-requests.index') }}"
                        class="flex items-center px-4 py-2.5 text-sm font-medium rounded-2xl {{ request()->routeIs('student.leave-requests.*') ? 'bg-white/10 text-gold font-bold' : 'text-gray-300 hover:bg-white/5 transition' }}">
                        <i class="fas fa-door-open h-5 w-5 ml-3 text-blue-400/70"></i>
                        <span class="flex-1">طلبات الاستئذان</span>
                        @php
                            $myPendingLeaves = auth()->user()->student
                                ? \App\Models\Leave::where('student_id', auth()->user()->student->id)->where('status', 'pending')->count()
                                : 0;
                        @endphp
                        @if($myPendingLeaves > 0)
                        <span class="bg-amber-500 text-white text-[10px] font-black px-1.5 py-0.5 rounded-full">{{ $myPendingLeaves }}</span>
                        @endif
                    </a>
                    <a href="{{ route('student-grades.index') }}"
                        class="flex items-center px-4 py-2.5 text-sm font-medium rounded-2xl {{ request()->routeIs('student-grades.*') ? 'bg-white/10 text-gold font-bold' : 'text-gray-300 hover:bg-white/5 transition' }}">
                        <i class="fas fa-file-invoice h-5 w-5 ml-3"></i>
                        بيانات درجاتي
                    </a>
                    <a href="{{ route('student-achievements.index') }}"
                        class="flex items-center px-4 py-2.5 text-sm font-medium rounded-2xl {{ request()->routeIs('student-achievements.*') ? 'bg-white/10 text-gold font-bold' : 'text-gray-300 hover:bg-white/5 transition' }}">
                        <i class="fas fa-trophy h-5 w-5 ml-3"></i>
                        إنجازاتي الشخصية
                    </a>
                </div>
            @endif
        @else
            <div class="p-6 text-center bg-white/5 rounded-2xl mx-2 border border-white/10">
                <i class="fas fa-user-graduate text-gold text-3xl mb-3 opacity-50"></i>
                <p class="text-xs text-gray-400 font-almarai leading-relaxed">عزيزي الطالب، بموجب تخرجك تم تجميد صلاحيات النظام النشطة. نتمنى لك دوام التوفيق.</p>
            </div>
        @endif
    </nav>


    <!-- Logout -->
    <div class="p-4 bg-[#083358] border-t border-gold/10">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="w-full flex items-center px-4 py-3 text-sm font-bold rounded-2xl text-red-200 hover:bg-red-500 hover:text-white transition-all group">
                <i class="fas fa-power-off h-5 w-5 ml-3 group-hover:rotate-12 transition-transform"></i>
                تسجيل الخروج
            </button>
        </form>
    </div>
</aside>
