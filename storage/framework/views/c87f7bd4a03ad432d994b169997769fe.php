<div style="width: 100%; height: 4px; background-color: #004274;"></div>
<div style="width: 100%; height: 2px; background-color: #D4A044; margin-top: 2px; margin-bottom: 25px;"></div>

<table style="width: 100%; font-family: 'dejavusans', sans-serif; margin-bottom: 20px;" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <!-- Right: Official Info -->
        <td style="width: 35%; text-align: right; vertical-align: top;">
            <h1 style="margin: 0 0 5px 0; font-size: 24px; font-weight: 900; color: #004274;">جمعية رعاية طالب العلم</h1>
            <h2 style="margin: 0 0 10px 0; font-size: 16px; font-weight: bold; color: #D4A044;">
                <?php echo e(auth()->user()->center?->name ?? 'مركز الأوائل الجامعي'); ?>

            </h2>
            <div style="width: 45px; height: 2px; background-color: #D4A044; margin-bottom: 10px; float: right;"></div>
            <div style="clear: both;"></div>
            <p style="margin: 0; font-size: 12px; color: #64748b; font-weight: bold;">
                <?php echo e($department ?? 'قسم الإسكان وشؤون الطلاب'); ?>

            </p>
        </td>

        <!-- Center: Logos -->
        <td style="width: 30%; text-align: center; vertical-align: top;">
            <div style="display: inline-block; direction: ltr;">
                <?php
                    $center = auth()->user()->center;
                    $centerLogoPath = ($center && $center->logo && file_exists(storage_path('app/public/' . $center->logo))) 
                                  ? storage_path('app/public/' . $center->logo) 
                                  : public_path('images/logos/alawayil_logo.png');
                    $scsLogoPath = public_path('images/logos/scs_logo.png');
                    
                    $centerLogoData = file_exists($centerLogoPath) ? base64_encode(file_get_contents($centerLogoPath)) : '';
                    $centerLogoExt = pathinfo($centerLogoPath, PATHINFO_EXTENSION);
                    
                    $scsLogoData = file_exists($scsLogoPath) ? base64_encode(file_get_contents($scsLogoPath)) : '';
                    $scsLogoExt = pathinfo($scsLogoPath, PATHINFO_EXTENSION);
                ?>
                <img src="data:image/<?php echo e($scsLogoExt); ?>;base64,<?php echo e($scsLogoData); ?>" style="height: 60px; vertical-align: middle;">
                <span style="display: inline-block; width: 1px; height: 40px; background-color: #cbd5e1; margin: 0 15px; vertical-align: middle;"></span>
                <img src="data:image/<?php echo e($centerLogoExt); ?>;base64,<?php echo e($centerLogoData); ?>" style="height: 60px; vertical-align: middle;">
            </div>
        </td>

        <!-- Left: Context Info -->
        <td style="width: 35%; text-align: left; vertical-align: bottom; direction: rtl;">
            <div style="display: inline-block; text-align: right;">
                <div style="margin-bottom: 8px; font-size: 11px;">
                    <span style="color: #64748b; font-weight: bold; margin-left: 10px;">الرقم المرجعي:</span>
                    <span style="color: #004274; font-weight: bold; font-family: 'Courier New', Courier, monospace;"><?php echo e($number ?? 'SCS-' . date('Ymd')); ?></span>
                </div>
                <div style="margin-bottom: 8px; font-size: 11px;">
                    <span style="color: #64748b; font-weight: bold; margin-left: 10px;">تاريخ المستند:</span>
                    <span style="color: #004274; font-weight: bold;"><?php echo e(date('Y/m/d')); ?></span>
                </div>
                <div style="font-size: 11px;">
                    <span style="color: #64748b; font-weight: bold; margin-left: 10px;">نطاق التقرير:</span>
                    <span style="color: #000000; font-weight: bold;">سري وللاستخدام الداخلي</span>
                </div>
            </div>
        </td>
    </tr>
</table>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($title)): ?>
    <div style="text-align: center; margin-bottom: 20px;">
        <h2 style="margin: 0; font-size: 18px; font-weight: 800; color: #004274;"><?php echo e($title); ?></h2>
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH C:\xampp\htdocs\Student_Housing_Management_System_PRD\shms\resources\views/partials/pdf_header.blade.php ENDPATH**/ ?>