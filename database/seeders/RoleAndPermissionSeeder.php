<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define permissions
        $permissions = [
            // User & Center Management
            'manage-users',
            'view-users',
            'manage-centers',
            'view-centers',

            // Student & Housing
            'manage-students',
            'view-students',
            'approve-student-profiles',
            'manage-rooms',
            'view-rooms',
            'manage-assignments',
            'view-assignments',

            // Discipline & Administrative
            'manage-violations',
            'view-violations',
            'manage-penalties',
            'view-penalties',
            'manage-leaves',
            'view-leaves',
            'approve-leaves',
            'manage-absences',
            'view-absences',
            'manage-commitments',
            'view-commitments',

            // Financial (General)
            'manage-funds',
            'view-funds',
            'manage-vouchers',
            'view-vouchers',
            'approve-vouchers',
            'manage-budgets',
            'view-budgets',
            'approve-budgets',
            'confirm-budgets',
            'manage-settlements',
            'view-settlements',
            'confirm-settlements',
            'approve-settlements',

            // Social & Activities
            'manage-clubs',
            'view-clubs',
            'manage-activities',
            'view-activities',
            'register-activities',
            'manage-news',
            'publish-news',
            'approve-news',
            'delete-news',

            // Nutrition (Global & Basic)
            'manage-meals',
            'view-meals',
            'manage-menus',
            'view-menus',

            // Nutrition (New Advanced Module)
            'view-nutrition', // Dashboard & General
            'manage-nutrition-budgets',
            'view-nutrition-budgets',
            'approve-nutrition-budgets',
            'manage-nutrition-suppliers',
            'view-nutrition-suppliers',
            'manage-nutrition-subscriptions',
            'view-nutrition-subscriptions',
            'manage-nutrition-invoices',
            'view-nutrition-invoices',
            'manage-nutrition-vouchers',
            'view-nutrition-vouchers',
            'manage-nutrition-settlements',
            'view-nutrition-settlements',
            'approve-nutrition-settlements',
            'manage-nutrition-distributions',
            'view-nutrition-distributions',
            'scan-nutrition-qr',

            // Assets & Vehicles
            'manage-assets',
            'view-assets',
            'manage-vehicles',
            'view-vehicles',

            // Reports
            'view-reports',

            // Quran Circles
            'manage-quran-circles',
            'view-quran-circles',
            'mark-circle-attendance',
            'view-circle-reports',
        ];

        foreach ($permissions as $permission) {
            \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create Roles
        $superAdmin = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        // Super Admin gets all permissions via Gate::before in AppServiceProvider

        $executiveManager = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'executive-manager', 'guard_name' => 'web']);
        $executiveManager->syncPermissions([
            'view-centers',
            'manage-centers',
            'view-users',
            'manage-users',
            'view-students',
            'view-reports',
            'approve-budgets',
            'approve-settlements',
            'approve-nutrition-budgets',
            'approve-nutrition-settlements',
            'view-vouchers',
            'view-funds',
            'view-budgets',
            'view-settlements',
            'view-nutrition',
            'view-nutrition-budgets',
            'view-nutrition-settlements',
            'view-rooms',
            'view-violations',
            'view-penalties',
            'view-leaves',
            'view-absences',
            'view-assets',
            'view-vehicles',
            'view-activities',
        ]);

        // مدير الإسكان (housing-manager)
        $housingManager = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'housing-manager', 'guard_name' => 'web']);
        $housingManager->syncPermissions([
            'manage-students',
            'view-students',
            'view-rooms',
            'manage-assignments',
            'view-assignments',
            'manage-violations',
            'view-violations',
            'view-penalties',
            'manage-leaves',
            'view-leaves',
            'manage-absences',
            'view-absences',
            'view-commitments',
            'manage-commitments',
            'manage-quran-circles',
            'view-quran-circles',
            'mark-circle-attendance',
            'view-circle-reports',
        ]);

        // مدير المخزون: الأصول فقط
        $inventoryManager = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'inventory-manager', 'guard_name' => 'web']);
        $inventoryManager->syncPermissions([
            'manage-assets',
            'view-assets',
        ]);

        // مدير التغذية (nutrition-manager): الوحدة الجديدة بالكامل
        $nutritionManager = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'nutrition-manager', 'guard_name' => 'web']);
        $nutritionManager->syncPermissions([
            'view-nutrition',
            'manage-nutrition-budgets',
            'view-nutrition-budgets',
            'manage-nutrition-suppliers',
            'view-nutrition-suppliers',
            'manage-nutrition-subscriptions',
            'view-nutrition-subscriptions',
            'manage-nutrition-invoices',
            'view-nutrition-invoices',
            'manage-nutrition-vouchers',
            'view-nutrition-vouchers',
            'manage-nutrition-settlements',
            'view-nutrition-settlements',
            'manage-nutrition-distributions',
            'view-nutrition-distributions',
            'scan-nutrition-qr',
            'view-meals',
            'manage-meals',
            'view-menus',
            'manage-menus',
        ]);

        // المدير المالي: الصناديق والسندات العامة + موازنات المركز
        $financialManager = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'financial-manager', 'guard_name' => 'web']);
        $financialManager->syncPermissions([
            'manage-funds',
            'view-funds',
            'manage-vouchers',
            'view-vouchers',
            'manage-budgets',
            'view-budgets',
            'manage-settlements',
            'view-settlements',
            'view-nutrition-budgets', // لمراقبة موازنات التغذية مالياً
            'view-nutrition-settlements',
            'view-nutrition-invoices',
        ]);

        // مدير الشؤون الاجتماعية: النوادي والأنشطة
        $socialManager = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'social-manager', 'guard_name' => 'web']);
        $socialManager->syncPermissions([
            'manage-clubs',
            'view-clubs',
            'manage-activities',
            'view-activities',
            'register-activities',
            'manage-news',
            'publish-news',
            'delete-news',
        ]);

        // مسؤول الإعلام: مراجعة واعتماد الأخبار والإعلانات من المراكز
        $mediaOfficer = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'media-officer', 'guard_name' => 'web']);
        $mediaOfficer->syncPermissions([
            'manage-news',
            'publish-news',
            'approve-news',
            'delete-news',
        ]);

        // مدير النقل: المركبات فقط
        $transportManager = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'transport-manager', 'guard_name' => 'web']);
        $transportManager->syncPermissions([
            'manage-vehicles',
            'view-vehicles',
        ]);

        // مدير المركز: إدارة شاملة للمركز
        $centerManager = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'center-manager', 'guard_name' => 'web']);
        $centerManager->syncPermissions([
            'view-users',
            'manage-users',
            'view-students',
            'manage-students',
            'approve-student-profiles',
            'manage-rooms',
            'view-rooms',
            'manage-assignments',
            'view-assignments',
            'manage-violations',
            'view-violations',
            'manage-penalties',
            'view-penalties',
            'manage-leaves',
            'view-leaves',
            'approve-leaves',
            'manage-absences',
            'view-absences',
            'view-funds',
            'manage-funds',
            'view-vouchers',
            'manage-vouchers',
            'view-budgets',
            'confirm-budgets',
            'view-settlements',
            'confirm-settlements',
            'manage-assets',
            'view-assets',
            'manage-vehicles',
            'view-vehicles',
            'view-activities',
            'manage-news',
            'publish-news',
            'delete-news',
            'view-reports',
            'view-nutrition-budgets',
            'view-nutrition-settlements',
            'approve-nutrition-budgets',
            'approve-nutrition-settlements',
            'manage-quran-circles',
            'view-quran-circles',
            'mark-circle-attendance',
            'view-circle-reports',
        ]);

        // المشرف (supervisor)
        $supervisor = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'supervisor', 'guard_name' => 'web']);
        $supervisor->syncPermissions([
            'view-students',
            'manage-students',
            'view-rooms',
            'manage-rooms',
            'manage-assignments',
            'view-assignments',
            'manage-violations',
            'view-violations',
            'manage-leaves',
            'view-leaves',
            'manage-absences',
            'view-absences',
            'manage-quran-circles',
            'view-quran-circles',
            'mark-circle-attendance',
            'view-circle-reports',
        ]);

        $circleTeacher = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'circle-teacher', 'guard_name' => 'web']);
        $circleTeacher->syncPermissions([
            'view-quran-circles',
            'mark-circle-attendance',
        ]);

        $student = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);
        // Students might have limited permissions like 'view-own-profile' or 'request-leave'
    }
}
