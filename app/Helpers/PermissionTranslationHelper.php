<?php

namespace App\Helpers;

class PermissionTranslationHelper
{
    public static function translateGroup($groupName)
    {
        $translations = [
            'users' => 'المستخدمين',
            'centers' => 'المراكز',
            'students' => 'الطلاب',
            'rooms' => 'الغرف والمتعلقات',
            'assignments' => 'توزيع السكن',
            'violations' => 'المخالفات',
            'penalties' => 'العقوبات',
            'leaves' => 'الاستئذانات',
            'absences' => 'الغياب',
            'funds' => 'الصناديق المالية',
            'vouchers' => 'السندات المالية',
            'budgets' => 'العهد الشهرية',
            'settlements' => 'التصفية الشهرية',
            'clubs' => 'النوادي',
            'activities' => 'الأنشطة',
            'meals' => 'وجبات التغذية',
            'menus' => 'قوائم الطعام',
            'assets' => 'الأصول والممتلكات',
            'vehicles' => 'المركبات',
            'reports' => 'التقارير',
            'news' => 'الأخبار والإعلانات',
            'quran' => 'الحلقات القرآنية',
            'quran-circles' => 'الحلقات القرآنية',
            'circle' => 'متابعة الحلقات',
            'nutrition' => 'إدارة التغذية',
            'other' => 'أخرى',
        ];

        return $translations[$groupName] ?? $groupName;
    }

    public static function translatePermission($permissionName)
    {
        $translations = [
            'manage-users' => 'إدارة المستخدمين بالكامل',
            'view-users' => 'عرض قائمة المستخدمين',
            'manage-centers' => 'إدارة المراكز بالكامل',
            'view-centers' => 'عرض المراكز',
            'manage-students' => 'إدارة الطلاب بالكامل',
            'view-students' => 'عرض قائمة الطلاب',
            'manage-rooms' => 'إدارة الغرف بالكامل',
            'view-rooms' => 'عرض الغرف',
            'manage-assignments' => 'إدارة التسكين',
            'view-assignments' => 'عرض بيانات التسكين',
            'manage-violations' => 'إدارة المخالفات',
            'view-violations' => 'عرض المخالفات',
            'manage-penalties' => 'إدارة العقوبات',
            'view-penalties' => 'عرض العقوبات',
            'manage-leaves' => 'إدارة الاستئذانات',
            'view-leaves' => 'عرض الاستئذانات',
            'approve-leaves' => 'الموافقة على الاستئذانات',
            'manage-absences' => 'إدارة الغياب',
            'view-absences' => 'عرض الغياب',
            'manage-funds' => 'إدارة الصناديق المالية',
            'view-funds' => 'عرض الصناديق المالية',
            'manage-vouchers' => 'إدارة السندات',
            'view-vouchers' => 'عرض السندات',
            'approve-vouchers' => 'اعتماد السندات',
            'manage-budgets' => 'إدارة العهد المتبقية',
            'view-budgets' => 'عرض العهد',
            'approve-budgets' => 'اعتماد العهد',
            'manage-settlements' => 'إدارة التصفيات',
            'view-settlements' => 'عرض التصفيات',
            'approve-settlements' => 'اعتماد التصفيات',
            'manage-clubs' => 'إدارة النوادي',
            'view-clubs' => 'عرض النوادي',
            'manage-activities' => 'إدارة الأنشطة',
            'view-activities' => 'عرض الأنشطة',
            'manage-meals' => 'إدارة وجبات التغذية',
            'view-meals' => 'عرض وجبات التغذية',
            'manage-menus' => 'إدارة قوائم الطعام',
            'view-menus' => 'عرض قوائم الطعام',
            'manage-assets' => 'إدارة الأصول',
            'view-assets' => 'عرض الأصول',
            'manage-vehicles' => 'إدارة المركبات',
            'view-vehicles' => 'عرض المركبات',
            'view-reports' => 'عرض التقارير الطبية',
            'manage-news' => 'إدارة الأخبار (إضافة وتعديل)',
            'publish-news' => 'نشر الأخبار (تغيير حالة النشر)',
            'approve-news' => 'اعتماد وتدقيق الأخبار والإعلانات',
            'delete-news' => 'حذف الأخبار نهائياً',
            'confirm-budgets' => 'تأكيد الميزانية (للمدير المالي)',
            'confirm-settlements' => 'تأكيد التصفية (للمدير المالي)',
            'register-activities' => 'تسجيل حضور الأنشطة',
            
            // التغذية المتقدمة
            'view-nutrition' => 'عرض وحدة التغذية بالكامل',
            'manage-nutrition-budgets' => 'إدارة ميزانية التغذية',
            'view-nutrition-budgets' => 'عرض ميزانية التغذية',
            'approve-nutrition-budgets' => 'اعتماد ميزانية التغذية',
            'manage-nutrition-suppliers' => 'إدارة موردين التغذية',
            'view-nutrition-suppliers' => 'عرض موردين التغذية',
            'manage-nutrition-subscriptions' => 'إدارة اشتراكات التغذية',
            'view-nutrition-subscriptions' => 'عرض اشتراكات التغذية',
            'manage-nutrition-invoices' => 'إدارة فواتير التغذية',
            'view-nutrition-invoices' => 'عرض فواتير التغذية',
            'manage-nutrition-vouchers' => 'إدارة سندات التغذية',
            'view-nutrition-vouchers' => 'عرض سندات التغذية',
            'manage-nutrition-settlements' => 'إدارة تصفيات التغذية',
            'view-nutrition-settlements' => 'عرض تصفيات التغذية',
            'approve-nutrition-settlements' => 'اعتماد تصفيات التغذية',
            'manage-nutrition-distributions' => 'إدارة توزيع الوجبات',
            'view-nutrition-distributions' => 'عرض سجل توزيع الوجبات',
            'scan-nutrition-qr' => 'مسح رموز QR للوجبات',

            'manage-quran-circles' => 'إدارة الحلقات بالكامل',
            'view-quran-circles' => 'عرض الحلقات القرآنية',
            'mark-circle-attendance' => 'تسجيل حضور الحلقات',
            'view-circle-reports' => 'عرض تقارير الحلقات',
        ];

        return $translations[$permissionName] ?? $permissionName;
    }

    public static function translateRole($roleName)
    {
        $translations = [
            'super-admin' => 'المدير العام',
            'executive-manager' => 'المدير التنفيذي',
            'center-manager' => 'مدير المركز',
            'housing-manager' => 'مشرف سكن',
            'financial-manager' => 'المشرف المالي',
            'social-manager' => 'مشرف أنشطة',
            'nutrition-manager' => 'مشرف تغذية',
            'inventory-manager' => 'أمين مستودع / أصول',
            'transport-manager' => 'مشرف نقل',
            'media-officer' => 'مسؤول الإعلام',
            'circle-teacher' => 'مدرس حلقة',
            'supervisor' => 'المشرف العام (ميداني)',
            'student' => 'طالب',
        ];

        return $translations[$roleName] ?? $roleName;
    }
}
