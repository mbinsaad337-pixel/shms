<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CenterController;
use App\Http\Controllers\Admin\CenterManagerController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\NewsController;

// Public Landing / Welcome Page
Route::get('/', [NewsController::class, 'welcome'])->name('welcome');

// Public News Feed (for login page marquee & welcome page)
Route::get('api/public-news', [NewsController::class, 'publicFeed'])->name('news.public-feed');
// Public Centers API
Route::get('api/public-centers', [NewsController::class, 'publicCenters'])->name('centers.public-feed');
// Filtered public news (with center & category filters)
Route::get('api/public-news-filter', [NewsController::class, 'publicNewsFilter'])->name('news.public-filter');

// Public News Detail (no login required, read only)
Route::get('news/public/{news}', [NewsController::class, 'publicShow'])->name('news.public-show');

// Public Center Detail (no login required)
Route::get('centers/public/{center}', [NewsController::class, 'publicShowCenter'])->name('centers.public-show');

Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
});

use App\Http\Controllers\Admin\CenterUserController;
use App\Http\Controllers\Admin\ApprovalController;
use App\Http\Controllers\ViolationController;
use App\Http\Controllers\PenaltyController;
use App\Http\Controllers\CommitmentController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\AbsenceController;
use App\Http\Controllers\AdministrativeController;
use App\Http\Controllers\FundController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\MealSubscriptionController;
use App\Http\Controllers\MealDistributionController;
use App\Http\Controllers\WeeklyMenuController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ClubController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\MealGroupController;

use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ComplaintController;


Route::middleware(['auth', 'active', \App\Http\Middleware\EnsurePasswordIsChanged::class])->group(function () {
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
    // Fallback for shared hosting (InfinityFree) that may convert POST to GET
    Route::get('logout', [LoginController::class, 'logout']);

    // Profile & Password Change
    Route::get('profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('profile/change-password', [ProfileController::class, 'showChangePassword'])->name('profile.change_password.view');
    Route::post('profile/change-password', [ProfileController::class, 'updatePassword'])->name('profile.change_password.update');
    Route::get('profile/complete', [ProfileController::class, 'showCompleteProfile'])->name('profile.complete.view');
    Route::post('profile/complete', [ProfileController::class, 'updateProfile'])->name('profile.complete.update');
    Route::put('profile/autosave', [ProfileController::class, 'autoSaveProfile'])->name('profile.complete.autosave');

    // Applied middleware only to dashboard for now to avoid global lockout issues
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Reports
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/funds/view', [ReportController::class, 'fundsView'])->name('reports.funds.view');
    Route::get('reports/{type}', [ReportController::class, 'show'])->name('reports.show');

    // ── Complaints & Internal Notifications ──
    Route::prefix('complaints')->name('complaints.')->group(function () {
        Route::get('inbox',          [ComplaintController::class, 'inbox'])->name('inbox');
        Route::get('sent',           [ComplaintController::class, 'sent'])->name('sent');
        Route::get('create',         [ComplaintController::class, 'create'])->name('create');
        Route::post('/',             [ComplaintController::class, 'store'])->name('store');
        Route::get('{complaint}',    [ComplaintController::class, 'show'])->name('show');
        Route::post('{complaint}/reply',  [ComplaintController::class, 'reply'])->name('reply');
        Route::post('{complaint}/delete', [ComplaintController::class, 'destroy'])->name('destroy');
        Route::get('api/bell',       [ComplaintController::class, 'bellData'])->name('bell');
    });

    // Centers (Super Admin & Executive only)
    Route::middleware('role:super-admin|executive-manager')->group(function () {
        Route::get('centers/{center}/export-pdf', [CenterController::class, 'exportPdf'])->name('centers.export-pdf');
        Route::patch('centers/{center}/toggle-status', [CenterController::class, 'toggleStatus'])->name('centers.toggle-status');
        Route::resource('centers', CenterController::class);
        Route::resource('managers', CenterManagerController::class);
        Route::post('managers/{manager}/toggle', [CenterManagerController::class, 'toggleStatus'])->name('managers.toggle');

        // Central Media Officers Management
        Route::resource('media-officers', \App\Http\Controllers\Admin\MediaOfficerController::class)->except(['show']);
        Route::post('media-officers/{media_officer}/toggle', [\App\Http\Controllers\Admin\MediaOfficerController::class, 'toggleStatus'])->name('media-officers.toggle');

        // GM Approvals
        Route::get('executive/approvals', [ApprovalController::class, 'index'])->name('executive.approvals');
        Route::post('executive/approvals/budget/{budget}/approve', [ApprovalController::class, 'approveBudget'])->name('executive.budgets.approve');
        Route::post('executive/approvals/settlement/{settlement}/approve', [ApprovalController::class, 'approveSettlement'])->name('executive.settlements.approve');

        // Center Expenses (Rent, Water, Electricity)
        Route::post('center-expenses/{center_expense}/mark-paid', [\App\Http\Controllers\Admin\CenterExpenseController::class, 'markAsPaid'])->name('center-expenses.mark-paid');
        Route::resource('center-expenses', \App\Http\Controllers\Admin\CenterExpenseController::class);

        // ── Programs Management (إدارة البرامج) ──
        Route::resource('programs', \App\Http\Controllers\ProgramController::class)->only(['index', 'create', 'store', 'edit', 'update']);

        // ── System Settings ──
        Route::get('admin/settings', [\App\Http\Controllers\Admin\SystemSettingController::class, 'index'])->name('admin.settings.index');
        Route::put('admin/settings', [\App\Http\Controllers\Admin\SystemSettingController::class, 'update'])->name('admin.settings.update');
    });

    // Staff Management for Center Managers & Super Admin
    Route::middleware('role:center-manager|super-admin')->group(function () {
        Route::get('admin/users/export/list', [CenterUserController::class, 'exportListPdf'])->name('admin.users.export-list');
        Route::resource('admin/users', CenterUserController::class)->except(['destroy'])->names([
            'index' => 'admin.users.index',
            'create' => 'admin.users.create',
            'store' => 'admin.users.store',
            'edit' => 'admin.users.edit',
            'update' => 'admin.users.update',
        ]);
        Route::post('admin/users/{user}/delete', [CenterUserController::class, 'destroy'])->name('admin.users.destroy');
        Route::post('admin/users/{user}/toggle', [CenterUserController::class, 'toggleStatus'])->name('admin.users.toggle');

        // Annual Rollover & Archives (الترحيل السنوي والأرشيف)
        Route::get('annual-rollover', [\App\Http\Controllers\AnnualRolloverController::class, 'index'])->name('annual-rollover.index');
        Route::post('annual-rollover', [\App\Http\Controllers\AnnualRolloverController::class, 'store'])->name('annual-rollover.store');
        Route::get('annual-rollover/archive/{archive}', [\App\Http\Controllers\AnnualRolloverController::class, 'showArchive'])->name('annual-rollover.show-archive');
        Route::get('annual-rollover/archive/{archive}/export-pdf', [\App\Http\Controllers\AnnualRolloverController::class, 'exportArchivePdf'])->name('annual-rollover.export-archive-pdf');
        Route::get('annual-rollover/export-pdf', [\App\Http\Controllers\AnnualRolloverController::class, 'exportPdf'])->name('annual-rollover.export-pdf');

        // System Backup (النسخ الاحتياطي للنظام)
        Route::post('backup/create', [\App\Http\Controllers\BackupController::class, 'createBackup'])->name('backup.create');
        Route::get('backup/download', [\App\Http\Controllers\BackupController::class, 'downloadBackup'])->name('backup.download');
        Route::post('backup/delete', [\App\Http\Controllers\BackupController::class, 'deleteBackup'])->name('backup.delete');
        Route::get('backup/list', [\App\Http\Controllers\BackupController::class, 'listBackups'])->name('backup.list');

        // Annual Reports (التقارير السنوية)
        Route::get('annual-reports', [\App\Http\Controllers\AnnualReportController::class, 'index'])->name('annual-reports.index');
        Route::post('annual-reports', [\App\Http\Controllers\AnnualReportController::class, 'store'])->name('annual-reports.store');
        Route::get('annual-reports/{annualReport}/download', [\App\Http\Controllers\AnnualReportController::class, 'download'])->name('annual-reports.download');
        Route::delete('annual-reports/{annualReport}', [\App\Http\Controllers\AnnualReportController::class, 'destroy'])->name('annual-reports.destroy');
    });

    // Students Resource - access control is handled inside the controller methods
    Route::resource('students', StudentController::class)->except(['destroy']);
    // Fallback for shared hosting that strips _method=DELETE spoofing
    Route::post('students/{student}/delete', [StudentController::class, 'destroy'])->name('students.destroy');
    Route::get('students/{student}/export-pdf', [StudentController::class, 'exportPdf'])->name('students.export-pdf');

    Route::middleware('permission:view-students')->group(function () {
        Route::get('students/export/list', [StudentController::class, 'exportListPdf'])->name('students.export-list-pdf');
        Route::get('alumni', [StudentController::class, 'alumni'])->name('students.alumni');
        Route::post('students/{student}/mark-graduate', [StudentController::class, 'markAsGraduate'])->name('students.mark-graduate');
        Route::post('students/{student}/restore-graduate', [StudentController::class, 'restoreFromGraduate'])->name('students.restore-graduate');
        Route::post('students/{student}/approve-profile', [StudentController::class, 'approveProfile'])->name('students.approve-profile');
        Route::post('students/{student}/toggle-edit', [StudentController::class, 'toggleEditPermission'])->name('students.toggle-edit');
        Route::post('students/{student}/toggle-circle-teacher', [StudentController::class, 'toggleCircleTeacher'])->name('students.toggle-circle-teacher');
        Route::post('students/{student}/toggle-activity-assistant', [StudentController::class, 'toggleActivityAssistant'])->name('students.toggle-activity-assistant');
        Route::post('students/apply-annual-fees', [StudentController::class, 'applyAnnualFees'])->name('students.apply-annual-fees');
        
        Route::get('violations/export/list', [ViolationController::class, 'exportListPdf'])->name('violations.export-list');
        Route::get('violations/{violation}/export', [ViolationController::class, 'exportPdf'])->name('violations.export');
        Route::resource('violations', ViolationController::class);

        Route::get('penalties/export/list', [PenaltyController::class, 'exportListPdf'])->name('penalties.export-list');
        Route::resource('penalties', PenaltyController::class);

        // ── Unified Administrative Actions Hub ──────────────────
        Route::get('administrative', [AdministrativeController::class, 'index'])->name('administrative.index');

        // Commitments
        Route::post('commitments', [CommitmentController::class, 'store'])->name('commitments.store');
        Route::get('commitments/{commitment}/edit', [CommitmentController::class, 'edit'])->name('commitments.edit');
        Route::put('commitments/{commitment}', [CommitmentController::class, 'update'])->name('commitments.update');
        Route::post('commitments/{commitment}/delete', [CommitmentController::class, 'destroy'])->name('commitments.destroy');
        Route::get('commitments/{commitment}/export', [CommitmentController::class, 'exportPdf'])->name('commitments.export');
        Route::get('commitments/export/list', [CommitmentController::class, 'exportListPdf'])->name('commitments.export-list');

        // Absences
        Route::post('absences', [AbsenceController::class, 'store'])->name('absences.store');
        Route::get('absences/{absence}/edit', [AbsenceController::class, 'edit'])->name('absences.edit');
        Route::put('absences/{absence}', [AbsenceController::class, 'update'])->name('absences.update');
        Route::post('absences/{absence}/delete', [AbsenceController::class, 'destroy'])->name('absences.destroy');
        Route::get('absences/export/list', [AbsenceController::class, 'exportListPdf'])->name('absences.export-list');

        // Leaves (Istizhan)
        Route::post('leaves/cutoff', [LeaveController::class, 'updateCutoffTime'])->name('leaves.cutoff');
        Route::post('leaves', [LeaveController::class, 'store'])->name('leaves.store');
        Route::get('leaves/{leave}/edit', [LeaveController::class, 'edit'])->name('leaves.edit');
        Route::put('leaves/{leave}', [LeaveController::class, 'update'])->name('leaves.update');
        Route::post('leaves/{leave}/delete', [LeaveController::class, 'destroy'])->name('leaves.destroy');
        Route::post('leaves/{leave}/approve', [LeaveController::class, 'approve'])->name('leaves.approve');
        Route::post('leaves/{leave}/reject', [LeaveController::class, 'reject'])->name('leaves.reject');
        Route::post('leaves/{leave}/return', [LeaveController::class, 'return'])->name('leaves.return');
        Route::post('leaves/{leave}/convert-violation', [LeaveController::class, 'convertToViolation'])->name('leaves.convert-violation');
        Route::get('leaves/export/list', [LeaveController::class, 'exportListPdf'])->name('leaves.export-list');
    });

    // Rooms
    Route::middleware('permission:view-rooms')->group(function () {
        Route::post('rooms/{room}/vacate', [RoomController::class, 'vacate'])->name('rooms.vacate');
        Route::get('rooms/export/list', [RoomController::class, 'exportListPdf'])->name('rooms.export-list');
        Route::get('rooms/{room}/export', [RoomController::class, 'exportPdf'])->name('rooms.export');
        Route::resource('rooms', RoomController::class);

        // Assignments & Transfers
        Route::post('assignments', [\App\Http\Controllers\RoomAssignmentController::class, 'store'])->name('assignments.store');
        Route::post('students/{student}/transfer', [\App\Http\Controllers\RoomAssignmentController::class, 'transfer'])->name('students.transfer');
    });

    // Financial — كل مورد محمي بصلاحيته المحددة
    Route::middleware('permission:view-funds')->group(function () {
        Route::resource('funds', FundController::class);
    });

    Route::middleware('permission:view-vouchers')->group(function () {
        Route::get('vouchers/{voucher}/export-pdf', [VoucherController::class, 'exportPdf'])->name('vouchers.export-pdf');
        Route::post('vouchers/{voucher}/approve', [VoucherController::class, 'approve'])->name('vouchers.approve');
        Route::post('vouchers/{voucher}/reject', [VoucherController::class, 'reject'])->name('vouchers.reject');
        Route::resource('vouchers', VoucherController::class);
    });

    Route::middleware('permission:view-budgets')->group(function () {
        Route::post('budgets/{budget}/confirm', [\App\Http\Controllers\Admin\MonthlyBudgetController::class, 'confirm'])->name('budgets.confirm');
        Route::post('budgets/{budget}/reject', [\App\Http\Controllers\Admin\MonthlyBudgetController::class, 'reject'])->name('budgets.reject');
        Route::post('budgets/{budget}/approve', [\App\Http\Controllers\Admin\MonthlyBudgetController::class, 'approve'])->name('budgets.approve');
        Route::get('budgets/{budget}/export-pdf', [\App\Http\Controllers\Admin\MonthlyBudgetController::class, 'exportPdf'])->name('budgets.export-pdf');
        Route::resource('budgets', \App\Http\Controllers\Admin\MonthlyBudgetController::class);
    });

    Route::middleware('permission:view-settlements')->group(function () {
        Route::post('settlements/{settlement}/confirm', [\App\Http\Controllers\Admin\MonthlySettlementController::class, 'confirm'])->name('settlements.confirm');
        Route::post('settlements/{settlement}/reject', [\App\Http\Controllers\Admin\MonthlySettlementController::class, 'reject'])->name('settlements.reject');
        Route::post('settlements/{settlement}/approve', [\App\Http\Controllers\Admin\MonthlySettlementController::class, 'approve'])->name('settlements.approve');
        Route::post('settlements/{settlement}/recalculate', [\App\Http\Controllers\Admin\MonthlySettlementController::class, 'recalculate'])->name('settlements.recalculate');
        Route::get('settlements/{settlement}/export-pdf', [\App\Http\Controllers\Admin\MonthlySettlementController::class, 'exportPdf'])->name('settlements.export-pdf');
        Route::resource('settlements', \App\Http\Controllers\Admin\MonthlySettlementController::class);
    });

    // Meals
    Route::middleware('permission:view-meals')->group(function () {
        Route::get('distributions/scan', [MealDistributionController::class, 'scanView'])->name('distributions.scan_view');
        Route::post('distributions/scan', [MealDistributionController::class, 'scan'])->name('distributions.scan');
        Route::resource('subscriptions', MealSubscriptionController::class);
        Route::resource('distributions', MealDistributionController::class);
        Route::resource('menus', WeeklyMenuController::class);
        Route::resource('groups', MealGroupController::class);
    });

    // Social & Activities
    Route::middleware('permission:view-activities')->group(function () {
        Route::get('clubs/export/list', [ClubController::class, 'exportListPdf'])->name('clubs.export-list');
        Route::resource('clubs', ClubController::class);
        Route::get('clubs/{club}/export-pdf', [ClubController::class, 'exportPdf'])->name('clubs.export-pdf');
        Route::post('clubs/{club}/members', [ClubController::class, 'addMember'])->name('clubs.members.add');
        Route::delete('clubs/{club}/members/{student}', [ClubController::class, 'removeMember'])->name('clubs.members.remove');

        Route::post('activities/{activity}/register', [ActivityController::class, 'register'])->name('activities.register');
        Route::patch('activities/{activity}/status', [ActivityController::class, 'updateStatus'])->name('activities.update-status');
        Route::get('activities/{activity}/export-absentees', [ActivityController::class, 'exportAbsenteesPdf'])->name('activities.export-absentees');
        Route::get('activities/export/list', [ActivityController::class, 'exportListPdf'])->name('activities.export-list');
        Route::resource('activities', ActivityController::class);

    });

    // News Interactions — available to ALL authenticated users
    Route::post('news/{news}/like', [NewsController::class, 'toggleLike'])->name('news.like');
    Route::post('news/{news}/comments', [NewsController::class, 'addComment'])->name('news.comments.add');
    Route::post('news/comments/{comment}/delete', [NewsController::class, 'deleteComment'])->name('news.comments.delete');

    // News & Announcements — management only
    Route::middleware('permission:manage-news')->group(function () {
        // Media Officer Approvals Dashboard
        Route::get('news/pending', [NewsController::class, 'pendingIndex'])->name('news.pending');
        Route::post('news/{news}/approve', [NewsController::class, 'approve'])->name('news.approve');
        Route::post('news/{news}/reject', [NewsController::class, 'reject'])->name('news.reject');

        // News Management
        Route::resource('news', NewsController::class)->except(['destroy']);
        Route::delete('news/{news}/delete', [NewsController::class, 'destroy'])->name('news.destroy');
        Route::post('news/{news}/toggle-publish', [NewsController::class, 'togglePublish'])->name('news.toggle-publish');
    });

    // Assets — مدير المخزون ومدير المركز فقط
    Route::middleware('permission:view-assets')->group(function () {
        Route::get('assets/export/list', [AssetController::class, 'exportListPdf'])->name('assets.export-list');
        Route::resource('assets', AssetController::class);
    });

    // Vehicles — مدير النقل ومدير المركز فقط
    Route::middleware('permission:view-vehicles')->group(function () {
        Route::resource('vehicles', VehicleController::class);
    });

    // Quran Circles (الحلقات القرآنية)
    Route::middleware('permission:view-quran-circles')->group(function () {
        Route::resource('quran-circles', \App\Http\Controllers\QuranCircleController::class);
        Route::get('quran-circles/{quran_circle}/students', [\App\Http\Controllers\QuranCircleController::class, 'students'])->name('quran-circles.students');
        Route::post('quran-circles/{quran_circle}/students', [\App\Http\Controllers\QuranCircleController::class, 'addStudent'])->name('quran-circles.students.add');
        Route::delete('quran-circles/{quran_circle}/students/{student}', [\App\Http\Controllers\QuranCircleController::class, 'removeStudent'])->name('quran-circles.students.remove');

        // Sessions & Attendance
        Route::get('quran-circles/{circle}/sessions/create', [\App\Http\Controllers\CircleSessionController::class, 'create'])->name('circle-sessions.create');
        Route::post('quran-circles/{circle}/sessions', [\App\Http\Controllers\CircleSessionController::class, 'store'])->name('circle-sessions.store');
        Route::get('circle-sessions/{session}', [\App\Http\Controllers\CircleSessionController::class, 'show'])->name('circle-sessions.show');
        Route::delete('circle-absences/{attendance}', [\App\Http\Controllers\CircleAbsenceController::class, 'destroy'])->name('circle-absences.destroy');

        // Statistics
        Route::get('quran-circles-stats', [\App\Http\Controllers\QuranCircleController::class, 'stats'])->name('quran-circles.stats');
        Route::get('quran-circles-export-stats', [\App\Http\Controllers\QuranCircleController::class, 'exportStats'])->name('quran-circles.export-stats');
        Route::get('quran-circles-absent-report', [\App\Http\Controllers\QuranCircleController::class, 'absentReport'])->name('quran-circles.absent-report');
        Route::get('quran-circles-export-absent-report', [\App\Http\Controllers\QuranCircleController::class, 'exportAbsentReport'])->name('quran-circles.export-absent-report');
    });

    // ══════════════════════════════════════════
    // Nutrition Module (وحدة التغذية)
    // ══════════════════════════════════════════
    Route::group(['prefix' => 'nutrition', 'as' => 'nutrition.'], function () {

        // Dashboard
        Route::get('/', [\App\Http\Controllers\Nutrition\FoodDashboardController::class, 'index'])->name('dashboard');

        // Budgets (ميزانية التغذية)
        Route::post('budgets/{budget}/submit', [\App\Http\Controllers\Nutrition\FoodBudgetController::class, 'submit'])->name('budgets.submit');
        Route::post('budgets/{budget}/approve', [\App\Http\Controllers\Nutrition\FoodBudgetController::class, 'approve'])->name('budgets.approve');
        Route::post('budgets/{budget}/reject', [\App\Http\Controllers\Nutrition\FoodBudgetController::class, 'reject'])->name('budgets.reject');
        Route::get('budgets/{budget}/export-pdf', [\App\Http\Controllers\Nutrition\FoodBudgetController::class, 'exportPdf'])->name('budgets.export-pdf');
        Route::resource('budgets', \App\Http\Controllers\Nutrition\FoodBudgetController::class);

        // Suppliers (الموردين)
        Route::get('suppliers/{supplier}/export-pdf', [\App\Http\Controllers\Nutrition\FoodSupplierController::class, 'exportPdf'])->name('suppliers.export-pdf');
        Route::resource('suppliers', \App\Http\Controllers\Nutrition\FoodSupplierController::class);

        Route::get('subscriptions/export-pdf', [\App\Http\Controllers\Nutrition\FoodSubscriptionController::class, 'exportPdf'])->name('subscriptions.export-pdf');
        Route::post('subscriptions/{subscription}/approve', [\App\Http\Controllers\Nutrition\FoodSubscriptionController::class, 'approve'])->name('subscriptions.approve');
        Route::post('subscriptions/{subscription}/reject', [\App\Http\Controllers\Nutrition\FoodSubscriptionController::class, 'reject'])->name('subscriptions.reject');
        Route::post('subscriptions/{subscription}/suspend', [\App\Http\Controllers\Nutrition\FoodSubscriptionController::class, 'suspend'])->name('subscriptions.suspend');
        Route::post('subscriptions/{subscription}/activate', [\App\Http\Controllers\Nutrition\FoodSubscriptionController::class, 'activate'])->name('subscriptions.activate');
        Route::post('subscriptions/{subscription}/payment', [\App\Http\Controllers\Nutrition\FoodSubscriptionController::class, 'addPayment'])->name('subscriptions.add-payment');
        Route::resource('subscriptions', \App\Http\Controllers\Nutrition\FoodSubscriptionController::class);

        // Purchase Invoices (فواتير المشتريات)
        Route::post('invoices/{invoice}/cancel', [\App\Http\Controllers\Nutrition\FoodInvoiceController::class, 'cancel'])->name('invoices.cancel');
        Route::get('invoices/{invoice}/export-pdf', [\App\Http\Controllers\Nutrition\FoodInvoiceController::class, 'exportPdf'])->name('invoices.export-pdf');
        Route::resource('invoices', \App\Http\Controllers\Nutrition\FoodInvoiceController::class);

        // Vouchers (سندات الصرف والقبض)
        Route::post('vouchers/{voucher}/cancel', [\App\Http\Controllers\Nutrition\FoodVoucherController::class, 'cancel'])->name('vouchers.cancel');
        Route::get('vouchers/{voucher}/export-pdf', [\App\Http\Controllers\Nutrition\FoodVoucherController::class, 'exportPdf'])->name('vouchers.export-pdf');
        Route::resource('vouchers', \App\Http\Controllers\Nutrition\FoodVoucherController::class);

        // Monthly Settlements (التصفية الشهرية)
        Route::post('settlements/{settlement}/approve', [\App\Http\Controllers\Nutrition\FoodSettlementController::class, 'approve'])->name('settlements.approve');
        Route::post('settlements/{settlement}/reject', [\App\Http\Controllers\Nutrition\FoodSettlementController::class, 'reject'])->name('settlements.reject');
        Route::post('settlements/{settlement}/recalculate', [\App\Http\Controllers\Nutrition\FoodSettlementController::class, 'recalculate'])->name('settlements.recalculate');
        Route::get('settlements/{settlement}/export-pdf', [\App\Http\Controllers\Nutrition\FoodSettlementController::class, 'exportPdf'])->name('settlements.export-pdf');
        Route::resource('settlements', \App\Http\Controllers\Nutrition\FoodSettlementController::class);

        // Distribution & QR (التوزيع)
        Route::get('distributions/scan', [\App\Http\Controllers\Nutrition\FoodDistributionController::class, 'scan'])->name('distributions.scan');
        Route::post('distributions/process-scan', [\App\Http\Controllers\Nutrition\FoodDistributionController::class, 'processScan'])->name('distributions.process-scan');
        Route::post('distributions/distribute', [\App\Http\Controllers\Nutrition\FoodDistributionController::class, 'distribute'])->name('distributions.distribute');
        Route::get('distributions/{distribution}/details', [\App\Http\Controllers\Nutrition\FoodDistributionController::class, 'details'])->name('distributions.details');
        Route::delete('distributions/{distribution}', [\App\Http\Controllers\Nutrition\FoodDistributionController::class, 'destroy'])->name('distributions.destroy');
        Route::resource('distributions', \App\Http\Controllers\Nutrition\FoodDistributionController::class)->only(['index']);

        // Attendance Reports & Schedules (جدولة الوجبات وتقارير الحضور)
        Route::get('attendance-reports', [\App\Http\Controllers\Nutrition\FoodDashboardController::class, 'attendanceReports'])->name('attendance-reports');
        Route::resource('schedules', \App\Http\Controllers\Nutrition\MealScheduleController::class)->only(['index', 'store']);

        // QR Groups (مجموعات QR - متاح للطلاب)
        Route::get('qr-groups/{qr_group}/export-pdf', [\App\Http\Controllers\Nutrition\FoodQrGroupController::class, 'exportPdf'])->name('qr-groups.export-pdf');
        Route::resource('qr-groups', \App\Http\Controllers\Nutrition\FoodQrGroupController::class)->only(['index', 'create', 'store', 'show']);
    });

    // Student Merged QR Groups (ميزة تجميع رموز QR للطلاب)
    Route::get('student-qr-groups/{student_qr_group}/export-pdf', [\App\Http\Controllers\StudentQrGroupController::class, 'exportPdf'])->name('student-qr-groups.export-pdf');
    Route::resource('student-qr-groups', \App\Http\Controllers\StudentQrGroupController::class);
    Route::get('student-qr-groups/scan/{token}', [\App\Http\Controllers\StudentQrGroupController::class, 'scan'])->name('student-qr-groups.scan');

    // Meal Attendance Management (Student Portal) - إدارة الحضور للوجبات
    Route::middleware('role:student')->group(function () {
        Route::get('my-meals/attendance', [\App\Http\Controllers\Student\MealAttendanceController::class, 'index'])->name('student.meals.attendance');
        Route::post('my-meals/attendance', [\App\Http\Controllers\Student\MealAttendanceController::class, 'update'])->name('student.meals.attendance.update');

        // Quran Circles
        Route::get('my-quran-circles', [\App\Http\Controllers\Student\QuranCircleController::class, 'index'])->name('student.quran-circles.index');

        // Leave / Istizhan Requests (Student submits -> Supervisor approves)
        Route::get('my-leave-requests', [\App\Http\Controllers\Student\LeaveRequestController::class, 'index'])->name('student.leave-requests.index');
        Route::post('my-leave-requests', [\App\Http\Controllers\Student\LeaveRequestController::class, 'store'])->name('student.leave-requests.store');

        // Graduation Data Completion (طالب يستكمل بيانات التخرج)
        Route::get('students/{student}/graduation-form', [\App\Http\Controllers\GraduationController::class, 'showForm'])->name('graduation.form');
        Route::post('students/{student}/graduation-submit', [\App\Http\Controllers\GraduationController::class, 'submitRequest'])->name('graduation.submit');
    });

    // Graduation Pending Review - for managers
    Route::middleware('role:center-manager|super-admin')->group(function () {
        Route::get('graduation/pending', [\App\Http\Controllers\GraduationController::class, 'pendingList'])->name('graduation.pending');
        Route::post('graduation/{student}/approve', [\App\Http\Controllers\GraduationController::class, 'approve'])->name('graduation.approve');
        Route::post('graduation/{student}/reject', [\App\Http\Controllers\GraduationController::class, 'reject'])->name('graduation.reject');
    });

    // Student Grades & Achievements
    Route::resource('student-grades', \App\Http\Controllers\StudentGradeController::class);
    Route::resource('student-achievements', \App\Http\Controllers\StudentAchievementController::class);

    // Student Food Subscriptions
    Route::middleware('role:student')->group(function () {
        Route::get('my-food-subscriptions', [\App\Http\Controllers\Student\FoodSubscriptionController::class, 'index'])->name('student.food-subscriptions.index');
        Route::post('my-food-subscriptions', [\App\Http\Controllers\Student\FoodSubscriptionController::class, 'store'])->name('student.food-subscriptions.store');
    });
});
