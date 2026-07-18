

<?php $__env->startSection('content'); ?>
    <div class="text-center mb-10">
        <h2 class="text-3xl font-black text-navy font-cairo">تسجيل الدخول للمنصة</h2>
        <p class="text-gray-400 font-almarai text-sm mt-2">نظام إدارة الإسكان الطلابي - النسخة المطورة</p>
    </div>

    <form method="POST" action="<?php echo e(route('login')); ?>" class="space-y-6">
        <?php echo csrf_field(); ?>

        <div>
            <label for="email" class="block text-sm font-black text-navy mb-2 font-cairo">البريد الإلكتروني أو اسم
                المستخدم</label>
            <div class="relative">
                <i class="fas fa-user absolute right-4 top-1/2 -translate-y-1/2 text-gray-300"></i>
                <input type="text" name="email" id="email" value="<?php echo e(old('email')); ?>" required autofocus
                    placeholder="s442100 أو البريد الجامعي"
                    class="block w-full pr-12 pl-4 py-3.5 rounded-xl border border-gray-100 bg-gray-50 focus:ring-2 focus:ring-navy focus:bg-white transition-all font-almarai text-sm">
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <p class="mt-2 text-xs text-red-600 font-cairo"><?php echo e($message); ?></p>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <div>
            <label for="password" class="block text-sm font-black text-navy mb-2 font-cairo">كلمة المرور</label>
            <div class="relative">
                <i class="fas fa-lock absolute right-4 top-1/2 -translate-y-1/2 text-gray-300"></i>
                <input type="password" name="password" id="password" required placeholder="••••••••"
                    class="block w-full pr-12 pl-4 py-3.5 rounded-xl border border-gray-100 bg-gray-50 focus:ring-2 focus:ring-navy focus:bg-white transition-all">
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <p class="mt-2 text-xs text-red-600 font-cairo"><?php echo e($message); ?></p>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <div class="flex items-center justify-between">
            <label class="inline-flex items-center">
                <input type="checkbox" name="remember" class="rounded border-gray-300 text-secondary focus:ring-secondary">
                <span class="mr-2 text-sm text-gray-600">تذكرني</span>
            </label>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(\Illuminate\Support\Facades\Route::has('password.request')): ?>
                <a href="#" class="text-sm text-secondary hover:underline">نسيت كلمة المرور؟</a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <div>
            <button type="submit"
                class="w-full bg-navy text-white rounded-xl py-4 font-black font-cairo shadow-lg shadow-navy/20 hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center justify-center gap-3">
                <span class="text-gold"><i class="fas fa-sign-in-alt"></i></span>
                <span>تسجيل الدخول</span>
            </button>
        </div>
    </form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.guest', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Student_Housing_Management_System_PRD\shms\resources\views/auth/login.blade.php ENDPATH**/ ?>