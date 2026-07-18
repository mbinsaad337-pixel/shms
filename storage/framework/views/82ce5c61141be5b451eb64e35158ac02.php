
<?php $__env->startSection('title', 'لوحة متابعة التغذية'); ?>

<?php $__env->startSection('content'); ?>
    <div class="p-6 max-w-7xl mx-auto">

        <?php echo $__env->make('partials.print_header', (array)['title' => 'لوحة متابعة قسم التغذية', 'number' => 'FOOD-' . date('Ymd')], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 font-cairo">لوحة متابعة التغذية</h1>
                <p class="text-gray-400 font-almarai text-sm mt-1">نظرة عامة على الأداء المالي لقسم التغذية</p>
            </div>
            <div class="text-sm text-gray-400 font-mono" id="realtime-clock">
                <?php echo e(now()->translatedFormat('l, d F Y | h:i:s A')); ?>

            </div>
            <script>
                function updateClock() {
                    const now = new Date();
                    const options = {
                        weekday: 'long',
                        day: '2-digit',
                        month: 'long',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit',
                        hour12: true
                    };
                    document.getElementById('realtime-clock').textContent = now.toLocaleDateString('ar-SA', options);
                }
                setInterval(updateClock, 1000);
            </script>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-5 mb-8">
            <?php
                $cards = [
                    ['label' => 'إجمالي الميزانيات', 'value' => number_format($stats['total_budgets'], 0), 'unit' => 'ر.ي', 'icon' => 'fa-file-invoice-dollar', 'bg' => 'bg-navy', 'iconColor' => 'text-gold'],
                    ['label' => 'إجمالي الإيرادات', 'value' => number_format($stats['total_collected'], 0), 'unit' => 'ر.ي', 'icon' => 'fa-hand-holding-dollar', 'bg' => 'bg-gold', 'iconColor' => 'text-navy'],
                    ['label' => 'إجمالي المصروفات', 'value' => number_format($stats['total_expenses'], 0), 'unit' => 'ر.ي', 'icon' => 'fa-cart-shopping', 'bg' => 'bg-navy', 'iconColor' => 'text-gold'],
                    ['label' => 'صافي النتيجة', 'value' => number_format($stats['net_result'], 0), 'unit' => 'ر.ي', 'icon' => 'fa-scale-balanced', 'bg' => 'bg-gold', 'iconColor' => 'text-navy'],
                ];
            ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $cards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                <div
                    class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 relative overflow-hidden group hover:shadow-md transition-all">
                    <div
                        class="absolute left-0 top-0 w-32 h-32 bg-gray-50 rounded-full -translate-x-1/2 -translate-y-1/2 opacity-50 group-hover:scale-110 transition-transform">
                    </div>
                    <div class="relative z-10">
                        <div class="w-12 h-12 <?php echo e($card['bg']); ?> rounded-2xl flex items-center justify-center shadow-lg mb-3">
                            <i class="fas <?php echo e($card['icon']); ?> <?php echo e($card['iconColor']); ?> text-lg"></i>
                        </div>
                        <p class="text-[10px] font-bold text-gray-400 font-cairo mb-1 uppercase tracking-wider">
                            <?php echo e($card['label']); ?>

                        </p>
                        <p class="text-2xl font-black text-navy font-mono"><?php echo e($card['value']); ?> <span
                                class="text-xs font-cairo text-gray-400"><?php echo e($card['unit']); ?></span></p>
                    </div>
                </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>

        <!-- Attendance & Subscriber Stats -->
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-5 mb-8">
            <div
                class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 text-center group hover:border-navy/20 transition-all">
                <div
                    class="w-12 h-12 bg-navy/5 rounded-full flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform">
                    <i class="fas fa-user-check text-navy text-xl"></i>
                </div>
                <p class="text-3xl font-black text-navy font-mono"><?php echo e($stats['active_subscribers']); ?></p>
                <p class="text-[10px] font-bold text-gray-400 mt-2 font-cairo uppercase tracking-widest">مشترك فعال</p>
            </div>

            <div
                class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 text-center group hover:border-red-50 transition-all">
                <div
                    class="w-12 h-12 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform">
                    <i class="fas fa-hand-holding-dollar text-red-500 text-xl"></i>
                </div>
                <p class="text-3xl font-black text-red-600 font-mono"><?php echo e($stats['pending_payments']); ?></p>
                <p class="text-[10px] font-bold text-gray-400 mt-2 font-cairo uppercase tracking-widest">تأخر دفع</p>
            </div>

            <div
                class="bg-white rounded-2xl shadow-sm border border-orange-100 p-6 text-center group hover:border-orange-200 transition-all bg-orange-50/10">
                <div
                    class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform">
                    <i class="fas fa-user-clock text-orange-600 text-xl"></i>
                </div>
                <p class="text-3xl font-black text-orange-700 font-mono"><?php echo e($stats['late_today']); ?></p>
                <p class="text-[10px] font-bold text-gray-400 mt-2 font-cairo uppercase tracking-widest">متأخر اليوم</p>
            </div>

            <div
                class="bg-white rounded-2xl shadow-sm border border-rose-100 p-6 text-center group hover:border-rose-200 transition-all bg-rose-50/10">
                <div
                    class="w-12 h-12 bg-rose-100 rounded-full flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform">
                    <i class="fas fa-user-xmark text-rose-600 text-xl"></i>
                </div>
                <p class="text-3xl font-black text-rose-700 font-mono"><?php echo e($stats['absent_today']); ?></p>
                <p class="text-[10px] font-bold text-gray-400 mt-2 font-cairo uppercase tracking-widest">غائب اليوم</p>
            </div>

            <div
                class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 text-center group hover:border-gold/20 transition-all">
                <div
                    class="w-12 h-12 bg-gold/5 rounded-full flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform">
                    <i class="fas fa-user-slash text-gold text-xl"></i>
                </div>
                <p class="text-3xl font-black text-gold font-mono"><?php echo e($stats['suspended_subscribers']); ?></p>
                <p class="text-[10px] font-bold text-gray-400 mt-2 font-cairo uppercase tracking-widest">موقوفين</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Recent Budgets -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-6 bg-navy rounded-full"></div>
                        <h3 class="font-black text-navy font-cairo">آخر الميزانيات</h3>
                    </div>
                    <a href="<?php echo e(route('nutrition.budgets.index')); ?>"
                        class="text-xs text-navy font-bold hover:text-gold transition-colors font-cairo underline">عرض
                        الكل</a>
                </div>
                <div class="space-y-3">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(is_countable($recentBudgets) ? count($recentBudgets) > 0 : (method_exists($recentBudgets, 'count') ? $recentBudgets->count() > 0 : !empty($recentBudgets))): ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $recentBudgets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $budget): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                <?php /** @var \App\Models\FoodMonthlyBudget $budget */ ?>
                                <a href="<?php echo e(route('nutrition.budgets.show', $budget)); ?>"
                                    class="flex items-center justify-between p-4 bg-gray-50 hover:bg-navy/5 border border-transparent hover:border-navy/10 rounded-2xl transition-all">
                                    <div>
                                        <p class="font-bold text-navy text-sm font-cairo"><?php echo e($budget->month_name); ?>

                                            <?php echo e($budget->year); ?>

                                        </p>
                                        <p class="text-xs text-gray-400 font-mono"><?php echo e(number_format($budget->total_amount, 0)); ?> ر.ي</p>
                                    </div>
                                    <span
                                        class="px-3 py-1 rounded-lg text-[10px] font-bold
                                                                                                                                        <?php echo e($budget->status === 'approved' ? 'bg-green-100 text-green-700' :
                        ($budget->status === 'submitted' ? 'bg-yellow-100 text-yellow-700' :
                            ($budget->status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-600'))); ?>">
                                        <?php echo e($budget->status_label); ?>

                                    </span>
                                </a>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
<?php else: ?>
                        <p class="text-center text-gray-300 py-4 text-sm font-cairo">لا توجد ميزانيات حتى الآن</p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            <!-- Recent Invoices -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-6 bg-gold rounded-full"></div>
                        <h3 class="font-black text-navy font-cairo">آخر الفواتير</h3>
                    </div>
                    <a href="<?php echo e(route('nutrition.invoices.index')); ?>"
                        class="text-xs text-navy font-bold hover:text-gold transition-colors font-cairo underline">عرض
                        الكل</a>
                </div>
                <div class="space-y-3">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(is_countable($recentInvoices) ? count($recentInvoices) > 0 : (method_exists($recentInvoices, 'count') ? $recentInvoices->count() > 0 : !empty($recentInvoices))): ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $recentInvoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                        <?php /** @var \App\Models\FoodPurchaseInvoice $invoice */ ?>
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl border border-transparent">
                            <div>
                                <p class="font-bold text-navy text-sm font-cairo"><?php echo e($invoice->supplier->name); ?></p>
                                <p class="text-xs text-gray-400 font-mono"><?php echo e($invoice->invoice_number); ?></p>
                            </div>
                            <p class="font-black text-navy font-mono text-sm"><?php echo e(number_format($invoice->total_amount, 0)); ?>

                                ر.ي</p>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
<?php else: ?>
                        <p class="text-center text-gray-300 py-4 text-sm font-cairo">لا توجد فواتير</p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            <!-- Debtors -->
            <div class="card-premium p-6">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-2">
                        <div class="w-1 h-6 bg-red-500 rounded-full"></div>
                        <h3 class="font-black text-navy font-cairo">أعلى المديونيات</h3>
                    </div>
                    <a href="<?php echo e(route('nutrition.subscriptions.index')); ?>"
                        class="text-xs text-navy font-bold hover:text-gold transition-colors font-cairo underline">عرض
                        الكل</a>
                </div>
                <div class="space-y-3">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(is_countable($debtorStudents) ? count($debtorStudents) > 0 : (method_exists($debtorStudents, 'count') ? $debtorStudents->count() > 0 : !empty($debtorStudents))): ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $debtorStudents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                        <?php /** @var \App\Models\FoodMealSubscription $sub */ ?>
                        <div
                            class="flex items-center justify-between p-4 bg-red-50/20 rounded-2xl border border-red-100 transition-all hover:bg-red-50/40">
                            <p class="font-bold text-navy text-sm font-cairo"><?php echo e($sub->student?->name_ar ?? 'طالب غير موجود'); ?></p>
                            <p class="font-black text-red-600 font-mono text-xs">
                                <?php echo e(number_format($sub->total_due - $sub->total_paid, 0)); ?> ر.ي
                            </p>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
<?php else: ?>
                        <p class="text-center text-gray-300 py-4 text-sm font-cairo">لا توجد مديونيات</p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-4 lg:grid-cols-6 gap-4 no-print">
            <?php
                $actions = [
                    ['route' => 'nutrition.distributions.scan', 'label' => 'توزيع الوجبات', 'icon' => 'fa-qrcode', 'bg' => 'bg-navy', 'iconColor' => 'text-gold'],
                    ['route' => 'nutrition.attendance-reports', 'label' => 'تقارير الحضور', 'icon' => 'fa-clipboard-list', 'bg' => 'bg-gold', 'iconColor' => 'text-navy'],
                    ['route' => 'nutrition.schedules.index', 'label' => 'توقيت الوجبات', 'icon' => 'fa-clock', 'bg' => 'bg-navy', 'iconColor' => 'text-gold'],
                    ['route' => 'nutrition.subscriptions.create', 'label' => 'اشتراك جديد', 'icon' => 'fa-user-plus', 'bg' => 'bg-gold', 'iconColor' => 'text-navy'],
                    ['route' => 'nutrition.budgets.create', 'label' => 'ميزانية جديدة', 'icon' => 'fa-file-invoice-dollar', 'bg' => 'bg-navy', 'iconColor' => 'text-gold'],
                    ['route' => 'nutrition.invoices.create', 'label' => 'فاتورة جديدة', 'icon' => 'fa-cart-shopping', 'bg' => 'bg-gold', 'iconColor' => 'text-navy'],
                ];
            ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $actions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $action): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                <a href="<?php echo e(route($action['route'])); ?>"
                    class="flex flex-col items-center justify-center gap-3 p-6 bg-white border border-gray-100 shadow-sm rounded-[2rem] transition-all group hover:shadow-xl hover:-translate-y-1 hover:border-navy/10">
                    <div
                        class="w-14 h-14 <?php echo e($action['bg']); ?> rounded-2xl flex items-center justify-center shrink-0 shadow-lg group-hover:rotate-6 transition-all">
                        <i class="fas <?php echo e($action['icon']); ?> <?php echo e($action['iconColor']); ?> text-xl"></i>
                    </div>
                    <span class="font-black text-navy font-cairo text-xs text-center"><?php echo e($action['label']); ?></span>
                </a>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make(auth()->user()->hasRole('nutrition-manager') ? 'layouts.nutrition' : 'layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Student_Housing_Management_System_PRD\shms\resources\views/nutrition/dashboard.blade.php ENDPATH**/ ?>