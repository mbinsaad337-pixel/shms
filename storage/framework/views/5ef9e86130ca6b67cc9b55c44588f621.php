<!DOCTYPE html>
<html dir="rtl" lang="ar">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        body {
            font-family: 'sans-serif';
            direction: rtl;
            text-align: right;
            font-size: 11px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 10px;
        }

        .header h1 {
            font-size: 20px;
            margin: 0;
            color: #0f172a;
        }

        .header p {
            margin: 5px 0;
            color: #64748b;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #e2e8f0;
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: #f8fafc;
            color: #0f172a;
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            text-align: left;
            font-size: 10px;
            color: #94a3b8;
        }

        .status-residing {
            color: #16a34a;
            font-weight: bold;
        }

        .status-registered {
            color: #2563eb;
            font-weight: bold;
        }

        .status-left {
            color: #dc2626;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <!-- HEADER -->
    <?php echo $__env->make('partials.pdf_header', [
        'title' => 'سجل الطلاب (جديد)',
        'number' => 'STU-LIST-' . date('Ymd'),
        'department' => 'إدارة الإسكان وشؤون الطلاب'
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>اسم الطالب</th>
                <th>الرقم الجامعي</th>
                <th>الهوية الوطنية</th>
                <th>المركز</th>
                <th>الجامعة</th>
                <th>التخصص</th>
                <th><?php echo e(auth()->user()->hasRole('super-admin') ? 'السكن' : 'الحالة'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                <tr>
                    <td><?php echo e($index + 1); ?></td>
                    <td style="text-align: right;"><?php echo e($student->name_ar); ?></td>
                    <td><?php echo e($student->student_number); ?></td>
                    <td><?php echo e($student->national_id); ?></td>
                    <td><?php echo e($student->center->name ?? '-'); ?></td>
                    <td><?php echo e($student->university); ?></td>
                    <td><?php echo e($student->major); ?></td>
                    <td>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->hasRole('super-admin')): ?>
                            <?php echo e($student->center->name ?? 'غير محدد'); ?>

                        <?php else: ?>
                            <span class="status-<?php echo e($student->status); ?>">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($student->status == 'residing'): ?> 
                                    مقيم
                                <?php elseif($student->status == 'registered'): ?>
                                    <?php echo e($student->is_profile_approved ? 'تم الحجز' : 'حجز مبدئي'); ?>

                                <?php else: ?> 
                                    غادر 
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                </tr>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </tbody>
    </table>

    <div class="footer">
        طبع بواسطة: <?php echo e(auth()->user()->name); ?> | الصفحة {PAGENO} من {nbpg}
    </div>
</body>

</html>
<?php /**PATH C:\xampp\htdocs\Student_Housing_Management_System_PRD\shms\resources\views/students/list-pdf.blade.php ENDPATH**/ ?>