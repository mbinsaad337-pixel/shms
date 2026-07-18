
<?php
    /** @var \Illuminate\Pagination\LengthAwarePaginator|\App\Models\Student[] $students */
    /** @var array $universitys */
    /** @var array $majors */
    /** @var array $colleges */
    /** @var array $academic_levels */
    /** @var array $statuss */
?>

<?php $__env->startSection('title', 'إدارة الطلاب'); ?>

<?php $__env->startSection('content'); ?>
    <div class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6 bg-white p-6 rounded-2xl border-r-8 border-gold shadow-sm" dir="rtl">
        <div>
            <h1 class="text-2xl md:text-3xl font-black text-navy font-cairo">سجل الطلاب</h1>
            <p class="text-gray-400 font-almarai text-xs md:text-sm mt-1">إدارة واعتمادات بيانات الطلاب المقيمين</p>
        </div>
        <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
            <a href="<?php echo e(route('students.export-list-pdf', request()->all())); ?>"
                class="flex-1 sm:flex-none px-4 py-3 bg-white text-red-600 border border-red-100 rounded-xl hover:bg-red-50 shadow-sm font-cairo font-bold transition-all flex items-center justify-center gap-2 text-sm">
                <i class="fas fa-file-pdf"></i>
                <span class="whitespace-nowrap">تصدير PDF</span>
            </a>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->can('manage-students') && !auth()->user()->hasRole('super-admin')): ?>
                <a href="<?php echo e(route('students.create')); ?>"
                    class="flex-1 sm:flex-none px-4 py-3 bg-navy text-white rounded-xl hover:bg-navy/90 shadow-lg font-cairo font-bold transition-all flex items-center justify-center gap-2 text-sm">
                    <i class="fas fa-plus-circle text-gold"></i>
                    <span class="whitespace-nowrap">تسجيل جديد</span>
                </a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('center-manager') || auth()->user()->can('manage-students')): ?>
                <button type="button" onclick="openAnnualFeesModal()"
                    class="flex-1 sm:flex-none px-4 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 shadow-lg font-cairo font-bold transition-all flex items-center justify-center gap-2 text-sm">
                    <i class="fas fa-money-bill-wave text-gold"></i>
                    <span class="whitespace-nowrap">تعميم الرسوم</span>
                </button>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    <div class="bg-white p-4 md:p-6 rounded-2xl shadow-sm mb-6 border border-gray-100 font-almarai" dir="rtl">
        <form action="<?php echo e(route('students.index')); ?>" method="GET" class="space-y-4 text-right">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div class="md:col-span-2">
                    <label for="search" class="block text-xs font-bold text-gray-700 mb-2 font-cairo">بحث شامل</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" name="search" id="search" value="<?php echo e(request('search')); ?>"
                            placeholder="الاسم، الرقم الجامعي، الهوية الوطنية..."
                            class="w-full pr-10 rounded-xl border-gray-100 focus:border-gold focus:ring-gold transition-all text-xs md:text-sm bg-gray-50/50">
                    </div>
                </div>

                <div>
                    <label for="university" class="block text-xs font-bold text-gray-700 mb-2 font-cairo">الجامعة</label>
                    <select name="university" id="university"
                        class="w-full rounded-xl border-gray-100 focus:border-gold focus:ring-gold transition-all text-xs md:text-sm bg-gray-50/50">
                        <option value="">جميع الجامعات</option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $universitys; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                            <option value="<?php echo e($v); ?>" <?php echo e(request('university') == $v ? 'selected' : ''); ?>><?php echo e($v); ?>

                            </option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </select>
                </div>

                <div>
                    <label for="major" class="block text-xs font-bold text-gray-700 mb-2 font-cairo">التخصص</label>
                    <select name="major" id="major"
                        class="w-full rounded-xl border-gray-100 focus:border-gold focus:ring-gold transition-all text-xs md:text-sm bg-gray-50/50">
                        <option value="">جميع التخصصات</option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $majors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                            <option value="<?php echo e($v); ?>" <?php echo e(request('major') == $v ? 'selected' : ''); ?>><?php echo e($v); ?></option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 items-end">
                <div>
                    <label for="college" class="block text-xs font-bold text-gray-700 mb-2 font-cairo">الكلية</label>
                    <select name="college" id="college"
                        class="w-full rounded-xl border-gray-100 focus:border-gold focus:ring-gold transition-all text-xs md:text-sm bg-gray-50/50">
                        <option value="">جميع الكليات</option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $colleges; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                            <option value="<?php echo e($v); ?>" <?php echo e(request('college') == $v ? 'selected' : ''); ?>><?php echo e($v); ?></option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </select>
                </div>

                <div>
                    <label for="academic_level"
                        class="block text-xs font-bold text-gray-700 mb-2 font-cairo">المستوى</label>
                    <select name="academic_level" id="academic_level"
                        class="w-full rounded-xl border-gray-100 focus:border-gold focus:ring-gold transition-all text-xs md:text-sm bg-gray-50/50">
                        <option value="">جميع المستويات</option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $academic_levels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                            <option value="<?php echo e($v); ?>" <?php echo e(request('academic_level') == $v ? 'selected' : ''); ?>><?php echo e($v); ?>

                            </option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </select>
                </div>

                <div>
                    <label for="status" class="block text-xs font-bold text-gray-700 mb-2 font-cairo">الحالة</label>
                    <select name="status" id="status"
                        class="w-full rounded-xl border-gray-100 focus:border-gold focus:ring-gold transition-all text-xs md:text-sm bg-gray-50/50">
                        <option value="">جميع الحالات</option>
                        <?php
                            $statusLabels = [
                                'registered' => 'محجوز / مسجل',
                                'residing' => 'مقيم',
                                'left' => 'غادر',
                                'graduated' => 'خريج',
                                'suspended' => 'موقوف',
                            ];
                        ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $statuss; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                            <option value="<?php echo e($v); ?>" <?php echo e(request('status') == $v ? 'selected' : ''); ?>>
                                <?php echo e($statusLabels[(string)$v] ?? $v); ?>

                            </option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </select>
                </div>

                <div class="flex gap-2">
                    <button type="submit"
                        class="flex-1 bg-gold text-navy py-2.5 rounded-xl font-bold font-cairo shadow-md hover:bg-gold/90 transition-all flex items-center justify-center gap-2 text-sm">
                        <i class="fas fa-filter text-xs"></i>
                        <span>بحث</span>
                    </button>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(request()->hasAny(['search', 'major', 'university', 'college', 'academic_level', 'status'])): ?>
                        <a href="<?php echo e(route('students.index')); ?>"
                            class="bg-gray-100 text-gray-700 px-4 py-2.5 rounded-xl font-bold font-cairo hover:bg-gray-200 transition-all text-xs flex items-center justify-center">
                            إعادة تعيين
                        </a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-sm overflow-x-auto border border-gray-100">
        <table class="min-w-full divide-y divide-gray-200 text-right">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">الطالب</th>
                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">الرقم الجامعي</th>
                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">الهوية الوطنية</th>
                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">الجامعة</th>
                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">التخصص</th>
                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <?php echo e(auth()->user()->hasRole('super-admin') ? 'السكن' : 'الحالة'); ?>

                    </th>
                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="h-10 w-10 flex-shrink-0">
                                    <img class="h-10 w-10 rounded-full object-cover border"
                                        src="<?php echo e($student->photo ? asset('storage/' . $student->photo) : 'https://ui-avatars.com/api/?name=' . urlencode($student->name_ar) . '&background=0f172a&color=fff'); ?>"
                                        alt="">
                                </div>
                                <div class="mr-4">
                                    <div class="text-sm font-bold text-gray-900"><?php echo e($student->name_ar); ?></div>
                                    <div class="text-sm text-gray-500"><?php echo e($student->phone); ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php echo e($student->student_number); ?>

                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php echo e($student->national_id); ?>

                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                            <?php echo e($student->university); ?>

                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-medium">
                            <?php echo e($student->major); ?>

                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->hasRole('super-admin')): ?>
                                <span class="text-sm font-bold text-navy font-cairo">
                                    <?php echo e($student->center->name ?? 'غير محدد'); ?>

                                </span>
                            <?php else: ?>
                                <div class="flex flex-col gap-2">
                                    <span
                                        class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full w-fit
                                                                                                <?php if($student->status == 'residing'): ?> bg-navy/10 text-navy 
                                                                                                <?php elseif($student->status == 'registered'): ?> bg-gold/10 text-gold 
                                                                                                <?php else: ?> bg-gray-100 text-gray-800 <?php endif; ?>">
                                        <?php echo e($student->status == 'residing' ? 'مقيم' : ($student->status == 'registered' ? ($student->is_profile_approved ? 'تم الحجز' : 'حجز مبدئي') : 'غادر')); ?>

                                    </span>

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($student->user->profile_completed && !$student->is_profile_approved): ?>
                                        <span
                                            class="px-2 py-1 bg-yellow-100 text-yellow-800 text-[10px] rounded border border-yellow-200 w-fit">
                                            يوجد تحديث بانتظار الاعتماد
                                        </span>
                                    <?php elseif(!$student->user->profile_completed): ?>
                                        <span class="px-2 py-1 bg-gray-100 text-gray-500 text-[10px] rounded w-fit">
                                            لم يكمل بياناته
                                        </span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->can('manage-students') && !auth()->user()->hasRole('super-admin')): ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($student->user->profile_completed && !$student->is_profile_approved): ?>
                                    <form action="<?php echo e(route('students.approve-profile', $student)); ?>" method="POST" class="inline">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit"
                                            class="text-green-600 hover:text-green-800 ml-3 bg-green-50 px-2 py-1 rounded text-xs font-bold font-cairo"
                                            title="اعتماد البيانات">اعتماد</button>
                                    </form>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <a href="<?php echo e(route('students.edit', $student)); ?>"
                                    class="text-gray-400 hover:text-primary ml-3">تعديل</a>
                                <form action="<?php echo e(route('students.mark-graduate', $student)); ?>" method="POST" class="inline"
                                    onsubmit="return confirm('هل تريد نقل هذا الطالب لقائمة الخريجين؟ سيتم إخفاؤه من هذه القائمة وتجميد حسابه.')">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="text-navy hover:text-gold ml-3" title="نقل للخريجين">
                                        <i class="fas fa-user-graduate"></i>
                                    </button>
                                </form>
                                <form action="<?php echo e(route('students.destroy', $student)); ?>" method="POST" class="inline"
                                    data-confirm="هل أنت متأكد من حذف سجل هذا الطالب؟">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="text-gray-400 hover:text-red-700 transition-colors">حذف</button>
                                </form>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <a href="<?php echo e(route('students.show', $student)); ?>"
                                class="text-navy hover:text-gold font-bold ml-3 flex items-center gap-1 transition-colors">
                                <i class="fas fa-eye text-xs"></i> عرض
                            </a>
                        </td>
                    </tr>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </tbody>
        </table>
        <div class="px-6 py-4 bg-gray-50 border-t">
            <?php echo e($students->links()); ?>

        </div>
    </div>

    <!-- Apply Annual Fees Modal -->
    <div id="annualFeesModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-3xl w-full max-w-lg overflow-hidden shadow-2xl">
            <div class="p-8 border-b border-gray-50 flex justify-between items-center bg-gray-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 font-cairo">تعميم الرسوم السنوية</h3>
                </div>
                <button onclick="closeAnnualFeesModal()" class="text-gray-400 hover:text-red-500 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form action="<?php echo e(route('students.apply-annual-fees')); ?>" method="POST" class="p-8 space-y-6">
                <?php echo csrf_field(); ?>
                
                <div>
                    <label class="block text-sm font-bold text-gray-700 font-cairo mb-2">مبلغ الرسوم السنوية</label>
                    <div class="relative">
                        <input type="number" name="amount" min="0" step="0.01" required
                            placeholder="أدخل مبلغ الرسوم السنوية لتعميمه على جميع الطلاب (غير الخريجين والمغادرين)"
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl pr-12 p-4 font-almarai focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-left" dir="ltr">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                            <i class="fas fa-coins text-gray-400"></i>
                        </div>
                    </div>
                    <p class="text-xs text-gray-400 mt-2 font-almarai leading-relaxed"><i class="fas fa-info-circle ml-1"></i> سيتم تطبيق هذا المبلغ على جميع الطلاب المسجلين والمقيمين الحاليين.</p>
                </div>

                <div class="flex gap-4 pt-4 border-t border-gray-50">
                    <button type="submit"
                        class="flex-1 bg-indigo-600 text-white py-4 rounded-xl font-bold font-cairo shadow-lg shadow-indigo-600/30 hover:bg-indigo-700 transform hover:-translate-y-1 transition-all flex justify-center items-center gap-2">
                        <i class="fas fa-check-circle"></i> تأكيد التحديث
                    </button>
                    <button type="button" onclick="closeAnnualFeesModal()"
                        class="flex-1 bg-gray-100 text-gray-700 py-4 rounded-xl font-bold font-cairo hover:bg-gray-200 transition-all">إلغاء</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openAnnualFeesModal() {
            document.getElementById('annualFeesModal').classList.remove('hidden');
            document.getElementById('annualFeesModal').classList.add('flex');
        }

        function closeAnnualFeesModal() {
            document.getElementById('annualFeesModal').classList.add('hidden');
            document.getElementById('annualFeesModal').classList.remove('flex');
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Student_Housing_Management_System_PRD\shms\resources\views/students/index.blade.php ENDPATH**/ ?>