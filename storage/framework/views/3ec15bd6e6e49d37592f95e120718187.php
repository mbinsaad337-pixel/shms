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

    <?php
        $isGraduateStudent = auth()->user()->student && auth()->user()->student->is_graduate;
    ?>

    <nav class="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto custom-scrollbar">
        <!-- Dashboard -->
        <a href="<?php echo e(route('dashboard')); ?>"
            class="flex items-center px-4 py-3 text-sm font-medium rounded-2xl transition-all <?php echo e(request()->routeIs('dashboard') ? 'bg-gold text-navy font-black shadow-lg shadow-gold/20' : 'text-gray-300 hover:bg-white/5 hover:text-white'); ?>">
            <i class="fas fa-th-large h-5 w-5 ml-3"></i>
            لوحة التحكم
        </a>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$isGraduateStudent): ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view-centers')): ?>
                <!-- Centers & Managers -->
                <div class="pt-4 pb-2">
                    <p class="px-4 text-[10px] font-bold text-gold/60 uppercase tracking-widest font-cairo mb-2">إدارة النظام
                        المركزية</p>
                    <a href="<?php echo e(route('centers.index')); ?>"
                        class="flex items-center px-4 py-2.5 text-sm font-medium rounded-2xl <?php echo e(request()->routeIs('centers.*') ? 'bg-white/10 text-gold font-bold' : 'text-gray-300 hover:bg-white/5'); ?> transition-all">
                        <i class="fas fa-building h-5 w-5 ml-3"></i>
                        المراكز الطلابية
                    </a>
                    <a href="<?php echo e(route('managers.index')); ?>"
                        class="flex items-center px-4 py-2.5 text-sm font-medium rounded-2xl <?php echo e(request()->routeIs('managers.*') ? 'bg-white/10 text-gold font-bold' : 'text-gray-300 hover:bg-white/5'); ?> transition-all">
                        <i class="fas fa-user-shield h-5 w-5 ml-3"></i>
                        إدارة مدراء المراكز
                    </a>
                </div>
            <?php endif; ?>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view-students')): ?>
                <a href="<?php echo e(route('students.index')); ?>"
                    class="flex items-center px-4 py-3 text-sm font-medium rounded-2xl <?php echo e(request()->routeIs('students.index') || (request()->routeIs('students.*') && !request()->routeIs('students.alumni')) ? 'bg-white/10 text-gold font-bold shadow-sm' : 'text-gray-300 hover:bg-white/5'); ?> transition-all">
                    <i class="fas fa-user-graduate h-5 w-5 ml-3"></i>
                    إدارة الطلاب
                </a>
                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->hasRole('center-manager') || auth()->user()->hasRole('super-admin')): ?>
                <a href="<?php echo e(route('students.alumni')); ?>"
                    class="flex items-center px-4 py-3 text-sm font-medium rounded-2xl <?php echo e(request()->routeIs('students.alumni') ? 'bg-white/10 text-gold font-bold shadow-sm' : 'text-gray-300 hover:bg-white/5'); ?> transition-all">
                    <i class="fas fa-user-tag h-5 w-5 ml-3"></i>
                    قائمة الخريجين
                </a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!auth()->user()->hasRole('super-admin')): ?>
                    <a href="<?php echo e(route('student-grades.index')); ?>"
                        class="flex items-center px-4 py-3 text-sm font-medium rounded-2xl <?php echo e(request()->routeIs('student-grades.*') ? 'bg-white/10 text-gold font-bold shadow-sm' : 'text-gray-300 hover:bg-white/5'); ?> transition-all">
                        <i class="fas fa-file-invoice h-5 w-5 ml-3"></i>
                        بيانات الدرجات
                    </a>
                    <a href="<?php echo e(route('student-achievements.index')); ?>"
                        class="flex items-center px-4 py-3 text-sm font-medium rounded-2xl <?php echo e(request()->routeIs('student-achievements.*') ? 'bg-white/10 text-gold font-bold shadow-sm' : 'text-gray-300 hover:bg-white/5'); ?> transition-all">
                        <i class="fas fa-award h-5 w-5 ml-3"></i>
                        إنجازات الطلاب
                    </a>
                    <a href="<?php echo e(route('violations.index')); ?>"
                        class="flex items-center px-4 py-3 text-sm font-medium rounded-2xl <?php echo e(request()->routeIs('violations.*') ? 'bg-white/10 text-gold font-bold shadow-sm' : 'text-gray-300 hover:bg-white/5'); ?> transition-all">
                        <i class="fas fa-gavel h-5 w-5 ml-3 text-red-400/60"></i>
                        سجل المخالفات
                    </a>
                    <a href="<?php echo e(route('penalties.index')); ?>"
                        class="flex items-center px-4 py-3 text-sm font-medium rounded-2xl <?php echo e(request()->routeIs('penalties.*') ? 'bg-white/10 text-gold font-bold shadow-sm' : 'text-gray-300 hover:bg-white/5'); ?> transition-all">
                        <i class="fas fa-calendar-minus h-5 w-5 ml-3 text-orange-400/60"></i>
                        سجل العقوبات
                    </a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php endif; ?>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view-rooms')): ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!auth()->user()->hasRole('super-admin')): ?>
                    <a href="<?php echo e(route('rooms.index')); ?>"
                        class="flex items-center px-4 py-3 text-sm font-medium rounded-2xl <?php echo e(request()->routeIs('rooms.*') ? 'bg-white/10 text-gold font-bold' : 'text-gray-300 hover:bg-white/5'); ?> transition-all">
                        <i class="fas fa-door-open h-5 w-5 ml-3"></i>
                        السكن والغرف
                    </a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php endif; ?>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view-meals')): ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!auth()->user()->hasRole('super-admin')): ?>
                    <div class="pt-4 pb-2">
                        <p class="px-4 text-[10px] font-bold text-gold/60 uppercase tracking-widest font-cairo mb-2">نظام التغذية
                            والوجبات</p>
                        <a href="<?php echo e(route('nutrition.distributions.scan')); ?>"
                            class="flex items-center px-4 py-2 text-sm font-medium rounded-2xl <?php echo e(request()->routeIs('nutrition.distributions.scan') ? 'bg-white/10 text-gold font-bold' : 'text-gray-300 hover:bg-white/5'); ?> transition-all">
                            <i class="fas fa-qrcode h-5 w-5 ml-3"></i>
                            مسح وتوزيع الوجبات
                        </a>
                        <a href="<?php echo e(route('nutrition.attendance-reports')); ?>"
                            class="flex items-center px-4 py-2 text-sm font-medium rounded-2xl <?php echo e(request()->routeIs('nutrition.attendance-reports') ? 'bg-white/10 text-gold font-bold' : 'text-gray-300 hover:bg-white/5'); ?> transition-all">
                            <i class="fas fa-clipboard-list h-5 w-5 ml-3"></i>
                            تقارير الحضور اليومي
                        </a>
                        <a href="<?php echo e(route('nutrition.schedules.index')); ?>"
                            class="flex items-center px-4 py-2 text-sm font-medium rounded-2xl <?php echo e(request()->routeIs('nutrition.schedules.index') ? 'bg-white/10 text-gold font-bold' : 'text-gray-300 hover:bg-white/5'); ?> transition-all">
                            <i class="fas fa-clock h-5 w-5 ml-3"></i>
                            ضبط مواعيد الوجبات
                        </a>
                        <a href="<?php echo e(route('nutrition.budgets.index')); ?>"
                            class="flex items-center px-4 py-2 text-sm font-medium rounded-2xl <?php echo e(request()->routeIs('nutrition.budgets.*') ? 'bg-white/10 text-gold font-bold' : 'text-gray-300 hover:bg-white/5'); ?> transition-all">
                            <i class="fas fa-file-invoice-dollar h-5 w-5 ml-3"></i>
                            ميزانية التغذية (العهدة)
                        </a>
                        <a href="<?php echo e(route('nutrition.subscriptions.index')); ?>"
                            class="flex items-center px-4 py-2 text-sm font-medium rounded-2xl <?php echo e(request()->routeIs('nutrition.subscriptions.*') ? 'bg-white/10 text-gold font-bold' : 'text-gray-300 hover:bg-white/5'); ?> transition-all">
                            <i class="fas fa-user-check h-5 w-5 ml-3"></i>
                            اشتراكات الطلاب
                        </a>
                        <a href="<?php echo e(route('nutrition.dashboard')); ?>"
                            class="flex items-center px-4 py-2 text-sm font-medium rounded-2xl <?php echo e(request()->routeIs('nutrition.dashboard') ? 'bg-white/10 text-gold font-bold' : 'text-gray-300 hover:bg-white/5'); ?> transition-all">
                            <i class="fas fa-chart-pie h-5 w-5 ml-3"></i>
                            إحصائيات التغذية العامة
                        </a>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->can('view-activities') || auth()->user()->can('view-quran-circles') || auth()->user()->hasRole('circle-teacher')): ?>
                <div class="pt-4 pb-2">
                    <p class="px-4 text-[10px] font-bold text-gold/60 uppercase tracking-widest font-cairo mb-2">قسم الشؤون الاجتماعية</p>

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view-activities')): ?>
                        <a href="<?php echo e(route('activities.index')); ?>"
                            class="flex items-center px-4 py-2.5 text-sm font-medium rounded-2xl <?php echo e(request()->routeIs('activities.*') ? 'bg-white/10 text-gold font-bold' : 'text-gray-300 hover:bg-white/5'); ?> transition-all">
                            <i class="fas fa-calendar-day h-5 w-5 ml-3"></i>
                            إدارة الأنشطة والفعاليات
                        </a>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!auth()->user()->hasRole('activity-assistant')): ?>
                            <a href="<?php echo e(route('clubs.index')); ?>"
                                class="flex items-center px-4 py-2.5 text-sm font-medium rounded-2xl <?php echo e(request()->routeIs('clubs.*') ? 'bg-white/10 text-gold font-bold' : 'text-gray-300 hover:bg-white/5'); ?> transition-all">
                                <i class="fas fa-users-rectangle h-5 w-5 ml-3"></i>
                                إدارة الأندية والقيادات
                            </a>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php endif; ?>

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage-news')): ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!auth()->user()->hasRole('super-admin')): ?>
                            <a href="<?php echo e(route('news.index')); ?>"
                                class="flex items-center px-4 py-2.5 text-sm font-medium rounded-2xl <?php echo e(request()->routeIs('news.*') ? 'bg-white/10 text-gold font-bold' : 'text-gray-300 hover:bg-white/5'); ?> transition-all">
                                <i class="fas fa-newspaper h-5 w-5 ml-3"></i>
                                إدارة الأخبار والإعلانات
                            </a>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php endif; ?>

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view-quran-circles')): ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!auth()->user()->hasRole('super-admin')): ?>
                            <a href="<?php echo e(route('quran-circles.index')); ?>"
                                class="flex items-center px-4 py-2.5 text-sm font-medium rounded-2xl <?php echo e(request()->routeIs('quran-circles.*') || request()->routeIs('circle-sessions.*') ? 'bg-white/10 text-gold font-bold' : 'text-gray-300 hover:bg-white/5'); ?> transition-all">
                                <i class="fas fa-quran h-5 w-5 ml-3"></i>
                                الحلقات القرآنية
                            </a>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->can('manage-users') || auth()->user()->can('view-assets') || auth()->user()->can('view-vehicles')): ?>
                <div class="pt-4 pb-2 border-t border-white/5 mt-4">
                    <p class="px-4 text-[10px] font-bold text-gold/60 uppercase tracking-widest font-cairo mb-2">إدارة الموقع
                    </p>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->can('manage-users') || auth()->user()->hasRole('super-admin')): ?>
                        <a href="<?php echo e(route('admin.users.index')); ?>"
                            class="flex items-center px-4 py-2.5 text-sm font-medium rounded-2xl <?php echo e(request()->routeIs('admin.users.*') ? 'bg-white/10 text-gold font-bold' : 'text-gray-300 hover:bg-white/5 transition'); ?>">
                            <i class="fas fa-users-cog h-5 w-5 ml-3"></i>
                            طاقم العمل
                        </a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->can('view-assets') || auth()->user()->hasRole('super-admin')): ?>
                        <a href="<?php echo e(route('assets.index')); ?>"
                            class="flex items-center px-4 py-2.5 text-sm font-medium rounded-2xl <?php echo e(request()->routeIs('assets.*') ? 'bg-white/10 text-gold font-bold' : 'text-gray-300 hover:bg-white/5 transition'); ?>">
                            <i class="fas fa-boxes h-5 w-5 ml-3"></i>
                            الأصول والعهدة العينية
                        </a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view-vehicles')): ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!auth()->user()->hasRole('super-admin')): ?>
                            <a href="<?php echo e(route('vehicles.index')); ?>"
                                class="flex items-center px-4 py-2.5 text-sm font-medium rounded-2xl <?php echo e(request()->routeIs('vehicles.*') ? 'bg-white/10 text-gold font-bold' : 'text-gray-300 hover:bg-white/5 transition'); ?>">
                                <i class="fas fa-car h-5 w-5 ml-3"></i>
                                مركبات الطلاب
                            </a>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->can('view-funds') || auth()->user()->can('view-vouchers') || auth()->user()->can('view-budgets') || auth()->user()->can('view-settlements') || auth()->user()->hasRole('super-admin')): ?>
                <div class="pt-4 pb-2 border-t border-white/5 mt-4">
                    <p class="px-4 text-[10px] font-bold text-gold/60 uppercase tracking-widest font-cairo mb-2">النظام المالي
                    </p>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if((auth()->user()->can('view-funds') || auth()->user()->can('view-vouchers')) && !auth()->user()->hasRole('super-admin')): ?>
                        <a href="<?php echo e(route('vouchers.index')); ?>"
                            class="flex items-center px-4 py-2.5 text-sm font-medium rounded-2xl <?php echo e(request()->routeIs('vouchers.*') || request()->routeIs('funds.*') ? 'bg-white/10 text-gold font-bold' : 'text-gray-300 hover:bg-white/5 transition'); ?>">
                            <i class="fas fa-wallet h-5 w-5 ml-3"></i>
                            الصناديق والسندات
                        </a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->can('view-budgets') || auth()->user()->hasRole('super-admin')): ?>
                        <a href="<?php echo e(route('budgets.index')); ?>"
                            class="flex items-center px-4 py-2.5 text-sm font-medium rounded-2xl <?php echo e(request()->routeIs('budgets.*') ? 'bg-white/10 text-gold font-bold' : 'text-gray-300 hover:bg-white/5 transition'); ?>">
                            <i class="fas fa-calculator h-5 w-5 ml-3"></i>
                            إدارة الموازنات
                        </a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->can('view-settlements') || auth()->user()->hasRole('super-admin')): ?>
                        <a href="<?php echo e(route('settlements.index')); ?>"
                            class="flex items-center px-4 py-2.5 text-sm font-medium rounded-2xl <?php echo e(request()->routeIs('settlements.*') ? 'bg-white/10 text-gold font-bold' : 'text-gray-300 hover:bg-white/5 transition'); ?>">
                            <i class="fas fa-balance-scale h-5 w-5 ml-3"></i>
                            التصفيات الشهرية
                        </a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <a href="<?php echo e(route('reports.show', 'funds')); ?>"
                        class="flex items-center px-4 py-2.5 text-sm font-medium rounded-2xl <?php echo e(request()->routeIs('reports.*') ? 'bg-white/10 text-gold font-bold' : 'text-gray-300 hover:bg-white/5 transition'); ?>">
                        <i class="fas fa-file-invoice-dollar h-5 w-5 ml-3"></i>
                        تقرير أرصدة الصناديق
                    </a>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->student): ?>
                <div class="pt-4 border-t border-white/5">
                    <p class="px-4 text-[10px] font-bold text-gold/60 uppercase tracking-widest font-cairo mb-2">خدمات الطلاب
                    </p>
                    <a href="<?php echo e(route('student.food-subscriptions.index')); ?>"
                        class="flex items-center px-4 py-2.5 text-sm font-medium rounded-2xl <?php echo e(request()->routeIs('student.food-subscriptions.*') ? 'bg-white/10 text-gold font-bold' : 'text-gray-300 hover:bg-white/5 transition'); ?>">
                        <i class="fas fa-utensils h-5 w-5 ml-3"></i>
                        اشتراكات التغذية الخاصة بي
                    </a>
                    <a href="<?php echo e(route('student-qr-groups.index')); ?>"
                        class="flex items-center px-4 py-2.5 text-sm font-medium rounded-2xl <?php echo e(request()->routeIs('student-qr-groups.*') ? 'bg-white/10 text-gold font-bold' : 'text-gray-300 hover:bg-white/5 transition'); ?>">
                        <i class="fas fa-qrcode h-5 w-5 ml-3"></i>
                        رموز QR المجمعة
                    </a>
                    <a href="<?php echo e(route('student.quran-circles.index')); ?>"
                        class="flex items-center px-4 py-2.5 text-sm font-medium rounded-2xl <?php echo e(request()->routeIs('student.quran-circles.index') ? 'bg-white/10 text-gold font-bold' : 'text-gray-300 hover:bg-white/5 transition'); ?>">
                        <i class="fas fa-quran h-5 w-5 ml-3"></i>
                        حلقاتي القرآنية
                    </a>
                    <a href="<?php echo e(route('student-grades.index')); ?>"
                        class="flex items-center px-4 py-2.5 text-sm font-medium rounded-2xl <?php echo e(request()->routeIs('student-grades.*') ? 'bg-white/10 text-gold font-bold' : 'text-gray-300 hover:bg-white/5 transition'); ?>">
                        <i class="fas fa-file-invoice h-5 w-5 ml-3"></i>
                        بيانات درجاتي
                    </a>
                    <a href="<?php echo e(route('student-achievements.index')); ?>"
                        class="flex items-center px-4 py-2.5 text-sm font-medium rounded-2xl <?php echo e(request()->routeIs('student-achievements.*') ? 'bg-white/10 text-gold font-bold' : 'text-gray-300 hover:bg-white/5 transition'); ?>">
                        <i class="fas fa-trophy h-5 w-5 ml-3"></i>
                        إنجازاتي الشخصية
                    </a>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php else: ?>
            <div class="p-6 text-center bg-white/5 rounded-2xl mx-2 border border-white/10">
                <i class="fas fa-user-graduate text-gold text-3xl mb-3 opacity-50"></i>
                <p class="text-xs text-gray-400 font-almarai leading-relaxed">عزيزي الطالب، بموجب تخرجك تم تجميد صلاحيات النظام النشطة. نتمنى لك دوام التوفيق.</p>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </nav>


    <!-- Logout -->
    <div class="p-4 bg-[#083358] border-t border-gold/10">
        <form method="POST" action="<?php echo e(route('logout')); ?>">
            <?php echo csrf_field(); ?>
            <button type="submit"
                class="w-full flex items-center px-4 py-3 text-sm font-bold rounded-2xl text-red-200 hover:bg-red-500 hover:text-white transition-all group">
                <i class="fas fa-power-off h-5 w-5 ml-3 group-hover:rotate-12 transition-transform"></i>
                تسجيل الخروج
            </button>
        </form>
    </div>
</aside>
<?php /**PATH C:\xampp\htdocs\Student_Housing_Management_System_PRD\shms\resources\views/partials/sidebar.blade.php ENDPATH**/ ?>