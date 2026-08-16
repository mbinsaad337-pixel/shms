<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use App\Models\Activity;
use App\Models\ActivityParticipant;
use App\Models\AnnualArchive;
use App\Models\AnnualRollover;
use App\Models\Center;
use App\Models\CenterExpense;
use App\Models\CircleAttendance;
use App\Models\CircleSession;
use App\Models\Commitment;
use App\Models\Club;
use App\Models\ClubMember;
use App\Models\Complaint;
use App\Models\FoodAttendanceReport;
use App\Models\Fund;
use App\Models\FoodBudget;
use App\Models\FoodDistribution;
use App\Models\FoodMonthlySettlement;
use App\Models\FoodPurchaseInvoice;
use App\Models\FoodSubscription;
use App\Models\FoodVoucher;
use App\Models\Leave;
use App\Models\MealDistribution;
use App\Models\MealSubscription;
use App\Models\MonthlyBudget;
use App\Models\MonthlySettlement;
use App\Models\News;
use App\Models\Penalty;
use App\Models\RoomAssignment;
use App\Models\Student;
use App\Models\StudentAchievement;
use App\Models\StudentGrade;
use App\Models\VehicleViolation;
use App\Models\Violation;
use App\Models\Voucher;
use App\Services\PdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnnualRolloverController extends Controller
{
    /**
     * Display the annual rollover dashboard & archive manager.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $centerId = $user->hasRole('super-admin') ? ($request->get('center_id') ?: $user->center_id) : $user->center_id;

        // Current Year Preview Counts (Data ready for rollover)
        $currentCounts = [
            'administrative' => [
                'violations' => Violation::when($centerId, fn($q) => $q->where('center_id', $centerId))->count(),
                'penalties' => Penalty::whereHas('student', fn($q) => $q->when($centerId, fn($sq) => $sq->where('center_id', $centerId)))->count(),
                'commitments' => Commitment::whereHas('student', fn($q) => $q->when($centerId, fn($sq) => $sq->where('center_id', $centerId)))->count(),
                'leaves' => Leave::whereHas('student', fn($q) => $q->when($centerId, fn($sq) => $sq->where('center_id', $centerId)))->count(),
                'absences' => Absence::whereHas('student', fn($q) => $q->when($centerId, fn($sq) => $sq->where('center_id', $centerId)))->count(),
            ],
            'activities' => [
                'activities' => Activity::when($centerId, fn($q) => $q->where('center_id', $centerId))->count(),
                'news' => News::when($centerId, fn($q) => $q->where('center_id', $centerId))->count(),
            ],
            'financial' => [
                'vouchers' => Voucher::when($centerId, fn($q) => $q->where('center_id', $centerId))->where('status', 'approved')->count(),
                'budgets' => MonthlyBudget::when($centerId, fn($q) => $q->where('center_id', $centerId))->count(),
                'settlements' => MonthlySettlement::when($centerId, fn($q) => $q->where('center_id', $centerId))->count(),
                'expenses' => CenterExpense::when($centerId, fn($q) => $q->where('center_id', $centerId))->count(),
            ],
            'nutrition' => [
                'distributions' => FoodDistribution::when($centerId, fn($q) => $q->where('center_id', $centerId))->count() + MealDistribution::count(),
                'subscriptions' => FoodSubscription::when($centerId, fn($q) => $q->where('center_id', $centerId))->count() + MealSubscription::count(),
                'invoices' => FoodPurchaseInvoice::when($centerId, fn($q) => $q->where('center_id', $centerId))->count(),
                'vouchers' => FoodVoucher::when($centerId, fn($q) => $q->where('center_id', $centerId))->count(),
                'budgets' => FoodBudget::when($centerId, fn($q) => $q->where('center_id', $centerId))->count(),
                'settlements' => FoodMonthlySettlement::when($centerId, fn($q) => $q->where('center_id', $centerId))->count(),
            ],
            'quran' => [
                'sessions' => CircleSession::whereHas('circle', fn($q) => $q->when($centerId, fn($sq) => $sq->where('center_id', $centerId)))->count(),
            ],
            'academic' => [
                'grades' => StudentGrade::whereHas('student', fn($q) => $q->when($centerId, fn($sq) => $sq->where('center_id', $centerId)))->count(),
                'achievements' => StudentAchievement::whereHas('student', fn($q) => $q->when($centerId, fn($sq) => $sq->where('center_id', $centerId)))->count(),
            ],
            'rooms' => [
                'assignments' => RoomAssignment::whereNull('released_at')
                    ->whereHas('room', fn($q) => $q->when($centerId, fn($sq) => $sq->where('center_id', $centerId)))->count(),
            ],
            'vehicles' => [
                'violations' => VehicleViolation::whereHas('vehicle', fn($q) => $q->when($centerId, fn($sq) => $sq->where('center_id', $centerId)))->count(),
            ],
            'complaints' => [
                'complaints' => Complaint::when($centerId, fn($q) => $q->where(fn($sq) => $sq->where('sender_center_id', $centerId)->orWhere('receiver_center_id', $centerId)->orWhereHas('sender', fn($ssq) => $ssq->where('center_id', $centerId))))->count(),
            ],
            'graduates' => [
                'graduates' => Student::when($centerId, fn($q) => $q->where('center_id', $centerId))
                    ->where(function ($q) {
                        $q->where('is_graduate', true)
                          ->orWhere('status', 'graduated');
                    })->count(),
            ],
            'funds' => [
                'funds' => Fund::when($centerId, fn($q) => $q->where('center_id', $centerId))
                    ->where('is_system', false)
                    ->count(),
            ],
            'clubs' => [
                'clubs' => Club::when($centerId, fn($q) => $q->where('center_id', $centerId))->count(),
            ],
        ];

        // ── Archive Query & Date Range Filtering ──
        $archivesQuery = AnnualArchive::query()
            ->when($centerId, fn($q) => $q->where('center_id', $centerId))
            ->with(['rollover', 'center', 'student']);

        if ($request->filled('archived_year')) {
            $archivesQuery->where('year', $request->archived_year);
        }

        if ($request->filled('module')) {
            $archivesQuery->where('module', $request->module);
        }

        if ($request->filled('date_from')) {
            $archivesQuery->whereDate('record_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $archivesQuery->whereDate('record_date', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $archivesQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('student_name', 'like', "%{$search}%")
                    ->orWhere('year', 'like', "%{$search}%");
            });
        }

        $archives = $archivesQuery->latest()->paginate(20)->withQueryString();

        // Historical Rollover Operations
        $rollovers = AnnualRollover::query()
            ->when($centerId, fn($q) => $q->where('center_id', $centerId))
            ->with(['user', 'center'])
            ->latest()
            ->get();

        $availableYears = AnnualArchive::query()
            ->when($centerId, fn($q) => $q->where('center_id', $centerId))
            ->select('year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        $centers = Center::where('is_active', true)->get();

        return view('annual_rollover.index', compact(
            'currentCounts',
            'archives',
            'rollovers',
            'availableYears',
            'centers',
            'centerId'
        ));
    }

    /**
     * Execute the annual rollover operation.
     */
    public function store(Request $request)
    {
        $request->validate([
            'year' => 'required|string|max:50',
            'modules' => 'required|array|min:1',
            'modules.*' => 'in:administrative,activities,financial,nutrition,quran,academic,rooms,vehicles,complaints,graduates,funds,clubs',
            'cutoff_date' => 'nullable|date',
            'notes' => 'nullable|string|max:1000',
        ], [
            'year.required' => 'يرجى تحديد السنة للترحيل',
            'modules.required' => 'يرجى اختيار قسم واحد على الأقل للترحيل',
        ]);

        $user = auth()->user();
        $centerId = $user->hasRole('super-admin') ? ($request->get('center_id') ?: $user->center_id) : $user->center_id;
        $year = $request->year;
        $cutoffDate = $request->cutoff_date;
        $selectedModules = $request->modules;

        DB::beginTransaction();

        try {
            $rollover = AnnualRollover::create([
                'center_id' => $centerId,
                'year' => $year,
                'from_date' => null,
                'to_date' => $cutoffDate,
                'performed_by' => $user->id,
                'modules' => $selectedModules,
                'summary' => [],
                'notes' => $request->notes,
            ]);

            $summary = [];

            // 1. Administrative Actions (الإجراءات الإدارية)
            if (in_array('administrative', $selectedModules)) {
                $count = 0;

                // Violations
                $violations = Violation::when($centerId, fn($q) => $q->where('center_id', $centerId))
                    ->when($cutoffDate, fn($q) => $q->whereDate('created_at', '<=', $cutoffDate))->get();
                foreach ($violations as $item) {
                    AnnualArchive::create([
                        'rollover_id' => $rollover->id,
                        'center_id' => $item->center_id,
                        'year' => $year,
                        'module' => 'administrative',
                        'sub_type' => 'violation',
                        'title' => 'مخالفة: ' . ($item->title ?? $item->type ?? 'مخالفة سلوكية'),
                        'record_id' => $item->id,
                        'record_date' => $item->created_at,
                        'student_id' => $item->student_id,
                        'student_name' => optional($item->student)->name_ar,
                        'data' => $item->toArray(),
                    ]);
                    $item->delete();
                    $count++;
                }

                // Penalties
                $penalties = Penalty::whereHas('student', fn($q) => $q->when($centerId, fn($sq) => $sq->where('center_id', $centerId)))
                    ->when($cutoffDate, fn($q) => $q->whereDate('created_at', '<=', $cutoffDate))->get();
                foreach ($penalties as $item) {
                    AnnualArchive::create([
                        'rollover_id' => $rollover->id,
                        'center_id' => optional($item->student)->center_id,
                        'year' => $year,
                        'module' => 'administrative',
                        'sub_type' => 'penalty',
                        'title' => 'جزاء: ' . ($item->penalty_type ?? 'جزاء إداري'),
                        'record_id' => $item->id,
                        'record_date' => $item->created_at,
                        'student_id' => $item->student_id,
                        'student_name' => optional($item->student)->name_ar,
                        'data' => $item->toArray(),
                    ]);
                    $item->delete();
                    $count++;
                }

                // Commitments
                $commitments = Commitment::whereHas('student', fn($q) => $q->when($centerId, fn($sq) => $sq->where('center_id', $centerId)))
                    ->when($cutoffDate, fn($q) => $q->whereDate('created_at', '<=', $cutoffDate))->get();
                foreach ($commitments as $item) {
                    AnnualArchive::create([
                        'rollover_id' => $rollover->id,
                        'center_id' => optional($item->student)->center_id,
                        'year' => $year,
                        'module' => 'administrative',
                        'sub_type' => 'commitment',
                        'title' => 'تعهد: ' . ($item->subject ?? 'تعهد خطي'),
                        'record_id' => $item->id,
                        'record_date' => $item->created_at,
                        'student_id' => $item->student_id,
                        'student_name' => optional($item->student)->name_ar,
                        'data' => $item->toArray(),
                    ]);
                    $item->delete();
                    $count++;
                }

                // Leaves
                $leaves = Leave::whereHas('student', fn($q) => $q->when($centerId, fn($sq) => $sq->where('center_id', $centerId)))
                    ->when($cutoffDate, fn($q) => $q->whereDate('created_at', '<=', $cutoffDate))->get();
                foreach ($leaves as $item) {
                    AnnualArchive::create([
                        'rollover_id' => $rollover->id,
                        'center_id' => optional($item->student)->center_id,
                        'year' => $year,
                        'module' => 'administrative',
                        'sub_type' => 'leave',
                        'title' => 'استئذان: ' . ($item->reason ?? 'طلب مغادرة'),
                        'record_id' => $item->id,
                        'record_date' => $item->created_at,
                        'student_id' => $item->student_id,
                        'student_name' => optional($item->student)->name_ar,
                        'data' => $item->toArray(),
                    ]);
                    $item->delete();
                    $count++;
                }

                // Absences
                $absences = Absence::whereHas('student', fn($q) => $q->when($centerId, fn($sq) => $sq->where('center_id', $centerId)))
                    ->when($cutoffDate, fn($q) => $q->whereDate('created_at', '<=', $cutoffDate))->get();
                foreach ($absences as $item) {
                    AnnualArchive::create([
                        'rollover_id' => $rollover->id,
                        'center_id' => optional($item->student)->center_id,
                        'year' => $year,
                        'module' => 'administrative',
                        'sub_type' => 'absence',
                        'title' => 'غياب: ' . ($item->notes ?? 'تسجيل غياب'),
                        'record_id' => $item->id,
                        'record_date' => $item->created_at,
                        'student_id' => $item->student_id,
                        'student_name' => optional($item->student)->name_ar,
                        'data' => $item->toArray(),
                    ]);
                    $item->delete();
                    $count++;
                }

                $summary['administrative'] = $count;
            }

            // 2. Activities & News (الأنشطة والأخبار)
            if (in_array('activities', $selectedModules)) {
                $count = 0;
                $activities = Activity::when($centerId, fn($q) => $q->where('center_id', $centerId))
                    ->when($cutoffDate, fn($q) => $q->whereDate('created_at', '<=', $cutoffDate))->get();

                foreach ($activities as $item) {
                    AnnualArchive::create([
                        'rollover_id' => $rollover->id,
                        'center_id' => $item->center_id,
                        'year' => $year,
                        'module' => 'activities',
                        'sub_type' => 'activity',
                        'title' => 'نشاط: ' . $item->name,
                        'record_id' => $item->id,
                        'record_date' => $item->created_at,
                        'amount' => $item->cost ?? 0,
                        'data' => $item->load('participants')->toArray(),
                    ]);
                    ActivityParticipant::where('activity_id', $item->id)->delete();
                    $item->delete();
                    $count++;
                }

                $newsList = News::when($centerId, fn($q) => $q->where('center_id', $centerId))
                    ->when($cutoffDate, fn($q) => $q->whereDate('created_at', '<=', $cutoffDate))->get();
                foreach ($newsList as $item) {
                    AnnualArchive::create([
                        'rollover_id' => $rollover->id,
                        'center_id' => $item->center_id,
                        'year' => $year,
                        'module' => 'activities',
                        'sub_type' => 'news',
                        'title' => 'خبر: ' . $item->title,
                        'record_id' => $item->id,
                        'record_date' => $item->created_at,
                        'data' => $item->toArray(),
                    ]);
                    $item->delete();
                    $count++;
                }

                $summary['activities'] = $count;
            }

            // 3. Financial System (النظام المالي)
            if (in_array('financial', $selectedModules)) {
                $count = 0;

                // Vouchers
                $vouchers = Voucher::when($centerId, fn($q) => $q->where('center_id', $centerId))
                    ->where('status', 'approved')
                    ->when($cutoffDate, fn($q) => $q->whereDate('created_at', '<=', $cutoffDate))->get();
                foreach ($vouchers as $item) {
                    AnnualArchive::create([
                        'rollover_id' => $rollover->id,
                        'center_id' => $item->center_id,
                        'year' => $year,
                        'module' => 'financial',
                        'sub_type' => 'voucher',
                        'title' => 'سند: ' . ($item->voucher_type == 'receipt' ? 'قبض' : 'صرف') . ' - ' . $item->description,
                        'record_id' => $item->id,
                        'record_date' => $item->created_at,
                        'amount' => $item->amount,
                        'student_id' => $item->student_id,
                        'data' => $item->toArray(),
                    ]);
                    $item->delete();
                    $count++;
                }

                // Monthly Budgets
                $budgets = MonthlyBudget::when($centerId, fn($q) => $q->where('center_id', $centerId))
                    ->when($cutoffDate, fn($q) => $q->whereDate('created_at', '<=', $cutoffDate))->get();
                foreach ($budgets as $item) {
                    AnnualArchive::create([
                        'rollover_id' => $rollover->id,
                        'center_id' => $item->center_id,
                        'year' => $year,
                        'module' => 'financial',
                        'sub_type' => 'budget',
                        'title' => 'موازنة شهر: ' . $item->month_year,
                        'record_id' => $item->id,
                        'record_date' => $item->created_at,
                        'amount' => $item->total_amount ?? 0,
                        'data' => $item->load('items')->toArray(),
                    ]);
                    $item->delete();
                    $count++;
                }

                // Monthly Settlements
                $settlements = MonthlySettlement::when($centerId, fn($q) => $q->where('center_id', $centerId))
                    ->when($cutoffDate, fn($q) => $q->whereDate('created_at', '<=', $cutoffDate))->get();
                foreach ($settlements as $item) {
                    AnnualArchive::create([
                        'rollover_id' => $rollover->id,
                        'center_id' => $item->center_id,
                        'year' => $year,
                        'module' => 'financial',
                        'sub_type' => 'settlement',
                        'title' => 'تصفية مالية: ' . $item->month_year,
                        'record_id' => $item->id,
                        'record_date' => $item->created_at,
                        'data' => $item->load('details')->toArray(),
                    ]);
                    $item->delete();
                    $count++;
                }

                // Center Expenses
                $expenses = CenterExpense::when($centerId, fn($q) => $q->where('center_id', $centerId))
                    ->when($cutoffDate, fn($q) => $q->whereDate('created_at', '<=', $cutoffDate))->get();
                foreach ($expenses as $item) {
                    AnnualArchive::create([
                        'rollover_id' => $rollover->id,
                        'center_id' => $item->center_id,
                        'year' => $year,
                        'module' => 'financial',
                        'sub_type' => 'expense',
                        'title' => 'مصروف مركز: ' . $item->title,
                        'record_id' => $item->id,
                        'record_date' => $item->created_at,
                        'amount' => $item->amount,
                        'data' => $item->toArray(),
                    ]);
                    $item->delete();
                    $count++;
                }

                $summary['financial'] = $count;
            }

            // 4. Nutrition Module (وحدة التغذية)
            if (in_array('nutrition', $selectedModules)) {
                $count = 0;

                // Food Distributions
                $distributions = FoodDistribution::when($centerId, fn($q) => $q->where('center_id', $centerId))
                    ->when($cutoffDate, fn($q) => $q->whereDate('created_at', '<=', $cutoffDate))->get();
                foreach ($distributions as $item) {
                    AnnualArchive::create([
                        'rollover_id' => $rollover->id,
                        'center_id' => $item->center_id,
                        'year' => $year,
                        'module' => 'nutrition',
                        'sub_type' => 'food_distribution',
                        'title' => 'توزيع وجبة: ' . ($item->meal_type ?? 'وجبة'),
                        'record_id' => $item->id,
                        'record_date' => $item->created_at,
                        'student_id' => $item->student_id,
                        'data' => $item->toArray(),
                    ]);
                    $item->delete();
                    $count++;
                }

                // Food Subscriptions
                $subscriptions = FoodSubscription::when($centerId, fn($q) => $q->where('center_id', $centerId))
                    ->when($cutoffDate, fn($q) => $q->whereDate('created_at', '<=', $cutoffDate))->get();
                foreach ($subscriptions as $item) {
                    AnnualArchive::create([
                        'rollover_id' => $rollover->id,
                        'center_id' => $item->center_id,
                        'year' => $year,
                        'module' => 'nutrition',
                        'sub_type' => 'food_subscription',
                        'title' => 'اشتراك تغذية للطالب: ' . optional($item->student)->name_ar,
                        'record_id' => $item->id,
                        'record_date' => $item->created_at,
                        'amount' => $item->total_amount ?? 0,
                        'student_id' => $item->student_id,
                        'student_name' => optional($item->student)->name_ar,
                        'data' => $item->toArray(),
                    ]);
                    $item->delete();
                    $count++;
                }

                // Food Purchase Invoices
                $invoices = FoodPurchaseInvoice::when($centerId, fn($q) => $q->where('center_id', $centerId))
                    ->when($cutoffDate, fn($q) => $q->whereDate('created_at', '<=', $cutoffDate))->get();
                foreach ($invoices as $item) {
                    AnnualArchive::create([
                        'rollover_id' => $rollover->id,
                        'center_id' => $item->center_id,
                        'year' => $year,
                        'module' => 'nutrition',
                        'sub_type' => 'food_invoice',
                        'title' => 'فاتورة مشتريات تغذية: ' . $item->invoice_number,
                        'record_id' => $item->id,
                        'record_date' => $item->created_at,
                        'amount' => $item->grand_total ?? $item->total_amount ?? 0,
                        'data' => $item->load('items')->toArray(),
                    ]);
                    $item->delete();
                    $count++;
                }

                // Food Vouchers
                $foodVouchers = FoodVoucher::when($centerId, fn($q) => $q->where('center_id', $centerId))
                    ->when($cutoffDate, fn($q) => $q->whereDate('created_at', '<=', $cutoffDate))->get();
                foreach ($foodVouchers as $item) {
                    AnnualArchive::create([
                        'rollover_id' => $rollover->id,
                        'center_id' => $item->center_id,
                        'year' => $year,
                        'module' => 'nutrition',
                        'sub_type' => 'food_voucher',
                        'title' => 'سند تغذية: ' . $item->voucher_number,
                        'record_id' => $item->id,
                        'record_date' => $item->created_at,
                        'amount' => $item->amount,
                        'data' => $item->toArray(),
                    ]);
                    $item->delete();
                    $count++;
                }

                // Food Budgets
                $foodBudgets = FoodBudget::when($centerId, fn($q) => $q->where('center_id', $centerId))
                    ->when($cutoffDate, fn($q) => $q->whereDate('created_at', '<=', $cutoffDate))->get();
                foreach ($foodBudgets as $item) {
                    AnnualArchive::create([
                        'rollover_id' => $rollover->id,
                        'center_id' => $item->center_id,
                        'year' => $year,
                        'module' => 'nutrition',
                        'sub_type' => 'food_budget',
                        'title' => 'ميزانية تغذية: ' . $item->month_year,
                        'record_id' => $item->id,
                        'record_date' => $item->created_at,
                        'amount' => $item->total_amount ?? 0,
                        'data' => $item->load('lines')->toArray(),
                    ]);
                    $item->delete();
                    $count++;
                }

                // Food Settlements
                $foodSettlements = FoodMonthlySettlement::when($centerId, fn($q) => $q->where('center_id', $centerId))
                    ->when($cutoffDate, fn($q) => $q->whereDate('created_at', '<=', $cutoffDate))->get();
                foreach ($foodSettlements as $item) {
                    AnnualArchive::create([
                        'rollover_id' => $rollover->id,
                        'center_id' => $item->center_id,
                        'year' => $year,
                        'module' => 'nutrition',
                        'sub_type' => 'food_settlement',
                        'title' => 'تصفية تغذية: ' . $item->month_year,
                        'record_id' => $item->id,
                        'record_date' => $item->created_at,
                        'data' => $item->load('details')->toArray(),
                    ]);
                    $item->delete();
                    $count++;
                }

                $summary['nutrition'] = $count;
            }

            // 5. Quran Circles (الحلقات القرآنية والجلسات)
            if (in_array('quran', $selectedModules)) {
                $count = 0;
                $sessions = CircleSession::whereHas('circle', fn($q) => $q->when($centerId, fn($sq) => $sq->where('center_id', $centerId)))
                    ->when($cutoffDate, fn($q) => $q->whereDate('created_at', '<=', $cutoffDate))->get();

                foreach ($sessions as $item) {
                    AnnualArchive::create([
                        'rollover_id' => $rollover->id,
                        'center_id' => optional($item->circle)->center_id,
                        'year' => $year,
                        'module' => 'quran',
                        'sub_type' => 'circle_session',
                        'title' => 'جلسة تحفيظ: ' . optional($item->circle)->name . ' بتاريخ ' . $item->session_date,
                        'record_id' => $item->id,
                        'record_date' => $item->created_at,
                        'data' => $item->load('attendance')->toArray(),
                    ]);
                    CircleAttendance::where('session_id', $item->id)->delete();
                    $item->delete();
                    $count++;
                }
                $summary['quran'] = $count;
            }

            // 6. Academic Grades & Achievements (الدرجات والإنجازات)
            if (in_array('academic', $selectedModules)) {
                $count = 0;
                $grades = StudentGrade::whereHas('student', fn($q) => $q->when($centerId, fn($sq) => $sq->where('center_id', $centerId)))
                    ->when($cutoffDate, fn($q) => $q->whereDate('created_at', '<=', $cutoffDate))->get();

                foreach ($grades as $item) {
                    AnnualArchive::create([
                        'rollover_id' => $rollover->id,
                        'center_id' => optional($item->student)->center_id,
                        'year' => $year,
                        'module' => 'academic',
                        'sub_type' => 'student_grade',
                        'title' => 'درجة أكاديمية: ' . $item->subject . ' للطالب ' . optional($item->student)->name_ar,
                        'record_id' => $item->id,
                        'record_date' => $item->created_at,
                        'student_id' => $item->student_id,
                        'student_name' => optional($item->student)->name_ar,
                        'data' => $item->toArray(),
                    ]);
                    $item->delete();
                    $count++;
                }

                $achievements = StudentAchievement::whereHas('student', fn($q) => $q->when($centerId, fn($sq) => $sq->where('center_id', $centerId)))
                    ->when($cutoffDate, fn($q) => $q->whereDate('created_at', '<=', $cutoffDate))->get();

                foreach ($achievements as $item) {
                    AnnualArchive::create([
                        'rollover_id' => $rollover->id,
                        'center_id' => optional($item->student)->center_id,
                        'year' => $year,
                        'module' => 'academic',
                        'sub_type' => 'student_achievement',
                        'title' => 'إنجاز طالب: ' . $item->title . ' - ' . optional($item->student)->name_ar,
                        'record_id' => $item->id,
                        'record_date' => $item->created_at,
                        'student_id' => $item->student_id,
                        'student_name' => optional($item->student)->name_ar,
                        'data' => $item->toArray(),
                    ]);
                    $item->delete();
                    $count++;
                }
                $summary['academic'] = $count;
            }

            // 7. Housing Assignments (تسكين الغرف - إعادة ضبط التسكين)
            if (in_array('rooms', $selectedModules)) {
                $count = 0;
                $assignments = RoomAssignment::whereNull('released_at')
                    ->whereHas('room', fn($q) => $q->when($centerId, fn($sq) => $sq->where('center_id', $centerId)))->get();

                foreach ($assignments as $item) {
                    AnnualArchive::create([
                        'rollover_id' => $rollover->id,
                        'center_id' => optional($item->room)->center_id,
                        'year' => $year,
                        'module' => 'rooms',
                        'sub_type' => 'room_assignment',
                        'title' => 'تسكين غرفة ' . optional($item->room)->room_number . ' للطالب ' . optional($item->student)->name_ar,
                        'record_id' => $item->id,
                        'record_date' => $item->created_at,
                        'student_id' => $item->student_id,
                        'student_name' => optional($item->student)->name_ar,
                        'data' => $item->toArray(),
                    ]);
                    $item->update(['released_at' => now(), 'release_reason' => 'الترحيل السنوي']);
                    $count++;
                }
                $summary['rooms'] = $count;
            }

            // 8. Vehicle Violations (مخالفات المركبات)
            if (in_array('vehicles', $selectedModules)) {
                $count = 0;
                $vViolations = VehicleViolation::whereHas('vehicle', fn($q) => $q->when($centerId, fn($sq) => $sq->where('center_id', $centerId)))
                    ->when($cutoffDate, fn($q) => $q->whereDate('created_at', '<=', $cutoffDate))->get();

                foreach ($vViolations as $item) {
                    AnnualArchive::create([
                        'rollover_id' => $rollover->id,
                        'center_id' => optional($item->vehicle)->center_id,
                        'year' => $year,
                        'module' => 'vehicles',
                        'sub_type' => 'vehicle_violation',
                        'title' => 'مخالفة مركبة: ' . $item->violation_type,
                        'record_id' => $item->id,
                        'record_date' => $item->created_at,
                        'data' => $item->toArray(),
                    ]);
                    $item->delete();
                    $count++;
                }
                $summary['vehicles'] = $count;
            }

            // 9. Complaints & Notifications (الشكاوى والإشعارات)
            if (in_array('complaints', $selectedModules)) {
                $count = 0;
                $complaints = Complaint::when($centerId, fn($q) => $q->where(fn($sq) => $sq->where('sender_center_id', $centerId)->orWhere('receiver_center_id', $centerId)->orWhereHas('sender', fn($ssq) => $ssq->where('center_id', $centerId))))
                    ->when($cutoffDate, fn($q) => $q->whereDate('created_at', '<=', $cutoffDate))->get();

                foreach ($complaints as $item) {
                    AnnualArchive::create([
                        'rollover_id' => $rollover->id,
                        'center_id' => $item->sender_center_id ?? $item->receiver_center_id,
                        'year' => $year,
                        'module' => 'complaints',
                        'sub_type' => 'complaint',
                        'title' => 'إشعار/شكوى: ' . $item->subject,
                        'record_id' => $item->id,
                        'record_date' => $item->created_at,
                        'data' => $item->toArray(),
                    ]);
                    $item->delete();
                    $count++;
                }
                $summary['complaints'] = $count;
            }

            // 10. Graduated Students Only (الطلاب الخريجون فقط)
            if (in_array('graduates', $selectedModules)) {
                $count = 0;
                $graduates = Student::when($centerId, fn($q) => $q->where('center_id', $centerId))
                    ->where(function ($q) {
                        $q->where('is_graduate', true)
                          ->orWhere('status', 'graduated');
                    })
                    ->when($cutoffDate, fn($q) => $q->whereDate('updated_at', '<=', $cutoffDate))
                    ->with(['center', 'activeRoomAssignment.room', 'program'])
                    ->get();

                foreach ($graduates as $student) {
                    AnnualArchive::create([
                        'rollover_id' => $rollover->id,
                        'center_id' => $student->center_id,
                        'year' => $year,
                        'module' => 'graduates',
                        'sub_type' => 'student_graduate',
                        'title' => 'أرشفة طالب خريج: ' . $student->name_ar,
                        'record_id' => $student->id,
                        'record_date' => $student->updated_at ?? $student->created_at,
                        'student_id' => $student->id,
                        'student_name' => $student->name_ar,
                        'data' => array_merge($student->toArray(), [
                            'room_number' => optional(optional($student->activeRoomAssignment)->room)->room_number,
                            'major' => $student->major,
                            'program_name' => optional($student->program)->name,
                        ]),
                    ]);

                    // Vacate room if active assignment exists
                    if ($student->activeRoomAssignment) {
                        $student->activeRoomAssignment->update([
                            'released_at' => now(),
                            'release_reason' => 'الترحيل السنوي للأرشيف (تخرج)'
                        ]);
                    }

                    // Soft-delete to archive and clear from active alumni lists
                    $student->delete();
                    $count++;
                }
                $summary['الطلاب الخريجون'] = $count;
            }

            // 11. Funds (الصناديق المالية القابلة للترحيل)
            if (in_array('funds', $selectedModules)) {
                $count = 0;
                $funds = Fund::when($centerId, fn($q) => $q->where('center_id', $centerId))
                    ->where('is_system', false)
                    ->when($cutoffDate, fn($q) => $q->whereDate('created_at', '<=', $cutoffDate))
                    ->get();

                foreach ($funds as $item) {
                    AnnualArchive::create([
                        'rollover_id' => $rollover->id,
                        'center_id' => $item->center_id,
                        'year' => $year,
                        'module' => 'funds',
                        'sub_type' => 'fund',
                        'title' => 'صندوق: ' . $item->name,
                        'record_id' => $item->id,
                        'record_date' => $item->created_at,
                        'amount' => $item->balance ?? 0,
                        'data' => $item->toArray(),
                    ]);
                    $item->delete();
                    $count++;
                }
                $summary['funds'] = $count;
            }

            // 12. Clubs (الأندية الطلابية)
            if (in_array('clubs', $selectedModules)) {
                $count = 0;
                $clubs = Club::when($centerId, fn($q) => $q->where('center_id', $centerId))
                    ->when($cutoffDate, fn($q) => $q->whereDate('created_at', '<=', $cutoffDate))
                    ->get();

                foreach ($clubs as $item) {
                    AnnualArchive::create([
                        'rollover_id' => $rollover->id,
                        'center_id' => $item->center_id,
                        'year' => $year,
                        'module' => 'clubs',
                        'sub_type' => 'club',
                        'title' => 'نادي: ' . $item->name,
                        'record_id' => $item->id,
                        'record_date' => $item->created_at,
                        'data' => $item->load('members')->toArray(),
                    ]);
                    ClubMember::where('club_id', $item->id)->delete();
                    $item->delete();
                    $count++;
                }
                $summary['clubs'] = $count;
            }

            $rollover->update(['summary' => $summary]);

            DB::commit();

            return redirect()->route('annual-rollover.index')
                ->with('success', "تم تنفيذ الترحيل السنوي لعام ({$year}) بنجاح، وتمت أرشفة البيانات المحددة وحفظها في أرشيف السنين.");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء تنفيذ الترحيل السنوي: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show archived record details in JSON for modal viewing or HTML view for dedicated page.
     */
    public function showArchive(Request $request, AnnualArchive $archive)
    {
        $user = auth()->user();
        if (!$user->hasRole('super-admin') && $archive->center_id && $archive->center_id != $user->center_id) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'غير مصرح'], 403);
            }
            abort(403, 'غير مصرح بالوصول إلى هذا السجل المؤرشف');
        }

        $archive->load(['rollover.user', 'center', 'student']);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'archive' => $archive,
                'data' => $archive->data,
            ]);
        }

        return view('annual_rollover.show', compact('archive'));
    }

    /**
     * Export a single archived record as a PDF document using the system letterhead.
     */
    public function exportArchivePdf(AnnualArchive $archive, PdfService $pdfService)
    {
        $user = auth()->user();
        if (!$user->hasRole('super-admin') && $archive->center_id && $archive->center_id != $user->center_id) {
            abort(403, 'غير مصرح بالوصول إلى هذا السجل المؤرشف');
        }

        $archive->load(['rollover.user', 'center', 'student']);

        $data = (array) $archive->data;

        // Separate scalar fields from complex array/subtable fields
        $scalarData = [];
        $complexData = [];
        foreach ($data as $key => $val) {
            if (is_array($val)) {
                $complexData[$key] = $val;
            } else {
                $scalarData[$key] = $val;
            }
        }

        $moduleNames = [
            'administrative' => 'الإجراءات الإدارية والجزاءات',
            'activities' => 'الأنشطة والأخبار والفعاليات',
            'financial' => 'النظام المالي والسندات',
            'nutrition' => 'نظام التغذية والوجبات',
            'quran' => 'الحلقات القرآنية والحضور',
            'academic' => 'الأكاديمي والدرجات والإنجازات',
            'rooms' => 'تسكين وإخلاء الغرف',
            'vehicles' => 'مركبات الطلاب ومخالفاتها',
            'complaints' => 'الشكاوى والاقتراحات',
            'graduates' => 'الطلاب الخريجون',
            'funds' => 'الصناديق المالية',
            'clubs' => 'الأندية الطلابية',
        ];

        $reportTitle = 'تفاصيل السجل المؤرشف';
        $filename = 'archive_record_' . $archive->id . '_' . now()->format('Y-m-d') . '.pdf';

        return $pdfService->stream(
            'pdf.annual_rollover.show',
            [
                'archive' => $archive,
                'data' => $data,
                'scalarData' => $scalarData,
                'complexData' => $complexData,
                'moduleName' => $moduleNames[$archive->module] ?? $archive->module,
            ],
            $reportTitle,
            $filename,
            'portrait',
            [
                'المركز' => optional($archive->center)->name ?? 'المركز العام',
                'السنة المؤرشفة' => $archive->year,
            ],
            [
                'رقم الأرشيف' => '#' . str_pad($archive->id, 6, '0', STR_PAD_LEFT),
                'تاريخ السجل الأصلي' => $archive->record_date ? $archive->record_date->format('Y/m/d') : '-',
            ]
        );
    }

    /**
     * Export annual archived records as PDF.
     */
    public function exportPdf(Request $request, PdfService $pdfService)
    {
        $user = auth()->user();
        $centerId = $user->hasRole('super-admin') ? ($request->get('center_id') ?: $user->center_id) : $user->center_id;

        $archivesQuery = AnnualArchive::query()
            ->when($centerId, fn($q) => $q->where('center_id', $centerId))
            ->with(['rollover', 'center', 'student']);

        if ($request->filled('archived_year')) {
            $archivesQuery->where('year', $request->archived_year);
        }

        if ($request->filled('module')) {
            $archivesQuery->where('module', $request->module);
        }

        if ($request->filled('date_from')) {
            $archivesQuery->whereDate('record_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $archivesQuery->whereDate('record_date', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $archivesQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('student_name', 'like', "%{$search}%")
                    ->orWhere('year', 'like', "%{$search}%");
            });
        }

        $archives = $archivesQuery->latest()->get();
        $center = $centerId ? Center::find($centerId) : null;
        $selectedYear = $request->get('archived_year', 'جميع السنوات');

        $moduleNames = [
            'administrative' => 'الإجراءات الإدارية والجزاءات',
            'activities' => 'الأنشطة والأخبار والفعاليات',
            'financial' => 'النظام المالي والسندات',
            'nutrition' => 'نظام التغذية والوجبات',
            'quran' => 'الحلقات القرآنية والحضور',
            'academic' => 'الأكاديمي والدرجات والإنجازات',
            'rooms' => 'تسكين وإخلاء الغرف',
            'vehicles' => 'مركبات الطلاب ومخالفاتها',
            'complaints' => 'الشكاوى والاقتراحات',
            'graduates' => 'الطلاب الخريجون',
            'funds' => 'الصناديق المالية',
            'clubs' => 'الأندية الطلابية',
        ];

        $filters = [
            'المركز' => $center ? $center->name : 'جميع المراكز',
            'السنة المؤرشفة' => $request->filled('archived_year') ? $request->archived_year : 'جميع السنوات',
        ];

        if ($request->filled('module')) {
            $filters['القسم / القطاع'] = $moduleNames[$request->module] ?? $request->module;
        }

        if ($request->filled('date_from')) {
            $filters['من تاريخ'] = $request->date_from;
        }

        if ($request->filled('date_to')) {
            $filters['إلى تاريخ'] = $request->date_to;
        }

        $stats = [
            'إجمالي السجلات' => count($archives) . ' سجل',
        ];

        $totalAmount = $archives->sum('amount');
        if ($totalAmount > 0) {
            $stats['إجمالي المبالغ المالية'] = number_format($totalAmount, 2) . ' ريال';
        }

        $reportTitle = 'تقرير أرشيف السنوات السابقة (' . $selectedYear . ')';
        $filename = 'annual_archive_' . ($request->archived_year ?: 'all') . '_' . now()->format('Y-m-d') . '.pdf';

        return $pdfService->stream(
            'annual_rollover.pdf',
            [
                'data' => $archives,
                'archives' => $archives,
                'center' => $center,
                'selectedYear' => $selectedYear,
                'stats' => $stats,
            ],
            $reportTitle,
            $filename,
            'landscape',
            $filters,
            $stats
        );
    }
}
