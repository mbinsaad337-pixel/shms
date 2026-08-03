<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Center;
use App\Models\Room;
use App\Models\MonthlyBudget;
use App\Models\MonthlySettlement;
use App\Models\Voucher;
use App\Models\Fund;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\RoomAssignment;
use App\Models\User;
use App\Models\MealSubscription;
use App\Models\Leave;
use App\Models\Activity;
use App\Models\Violation;
use App\Models\Absence;
use App\Models\FoodBudget;
use App\Models\FoodMonthlySettlement;
use App\Models\QuranCircle;
use App\Models\CircleAttendance;
use App\Models\FoodVoucher;
use App\Models\Club;
use App\Models\News;



class DashboardController extends Controller
{
    public function index(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->hasRole('student')) {
            return $this->studentDashboard();
        }

        // صفحة الاعتمادات هي لوحة التحكم الرئيسية لمسؤول الإعلام.
        if ($user->hasRole('media-officer') && !$user->hasAnyRole(['super-admin', 'executive-manager'])) {
            return redirect()->route('news.pending');
        }

        $isExecutive = $user->hasRole('super-admin') || $user->hasRole('executive-manager');

        if ($isExecutive) {
            return $this->executiveDashboard();
        }

        // Nutrition manager goes directly to nutrition dashboard
        if ($user->hasRole('nutrition-manager')) {
            return \redirect()->route('nutrition.dashboard');
        }

        if ($user->hasRole('housing-manager') || $user->hasRole('supervisor')) {
            return $this->studentManagerDashboard($user->center_id);
        }

        if ($user->hasRole('financial-manager')) {
            $selectedPeriod = $request->input('period', now()->format('Y-m'));

            if (!preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $selectedPeriod)) {
                $selectedPeriod = now()->format('Y-m');
            }

            return $this->financialManagerDashboard($user->center_id, $selectedPeriod);
        }

        if ($user->hasRole('social-manager')) {
            return $this->socialManagerDashboard($user->center_id);
        }

        return $this->centerDashboard($user->center_id);
    }

    private function studentDashboard()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $student = $user->student;

        if (!$student) {
            return \abort(404, 'بيانات الطالب غير موجودة');
        }

        $student->load([
            'center',
            'roomAssignments.room',
            'activeFoodSubscription',
            'violations',
            'absences',
            'leaves',
            'penalties',
            'grades',
            'achievements'
        ]);

        $todayMeals = [];
        if ($student->activeFoodSubscription) {
            $todayMeals = app(\App\Http\Controllers\Student\MealAttendanceController::class)->getTodayMeals($student);
        }

        return \view('students.show', compact('student', 'todayMeals'));
    }

    private function executiveDashboard()
    {
        $stats = [
            'students_count' => Student::count(),
            'centers_count' => Center::count(),
            'total_liquidity' => Fund::sum('balance'),
            'pending_budgets' => MonthlyBudget::where('status', 'confirmed')->count(),
            'pending_settlements' => MonthlySettlement::where('status', 'submitted')->count(),
            'total_capacity' => Room::where('status', 'available')->sum('capacity'),
            'occupied_seats' => RoomAssignment::whereNull('released_at')->count(),
        ];

        // Combined recent pending actions for feed
        $recent_budgets = MonthlyBudget::with('center')
            ->where('status', 'confirmed')
            ->latest()
            ->take(5)
            ->get();

        $recent_settlements = MonthlySettlement::with('center')
            ->where('status', 'submitted')
            ->latest()
            ->take(5)
            ->get();

        $centers_performance = Center::withCount('students')
            ->withSum('funds', 'balance')
            ->withCount(['rooms as total_capacity' => fn($q) => $q->where('status', 'available')])
            ->get();

        return \view('dashboard.executive', compact('stats', 'recent_budgets', 'recent_settlements', 'centers_performance'));
    }

    private function centerDashboard($centerId)
    {
        $stats = [
            'students_count' => Student::where('center_id', $centerId)->count(),
            'students_suspended' => Student::where('center_id', $centerId)->where('status', 'suspended')->count(),
            'staff_count' => User::where('center_id', $centerId)->count(),
            'rooms_count' => Room::where('center_id', $centerId)->count(),
            'total_capacity' => Room::where('center_id', $centerId)->where('status', 'available')->sum('capacity'),
            'occupied_seats' => RoomAssignment::whereHas('room', function ($q) use ($centerId) {
                $q->where('center_id', $centerId);
            })->whereNull('released_at')->count(),
            'meal_subscribers' => MealSubscription::whereHas('student', function ($q) use ($centerId) {
                $q->where('center_id', $centerId);
            })->where('status', 'active')->count(),
            'center_funds' => Fund::where('center_id', $centerId)->sum('balance'),
            'pending_approval' => Student::where('center_id', $centerId)->where('is_profile_approved', false)->count(),
            'on_leave_count' => Leave::whereHas('student', fn($q) => $q->where('center_id', $centerId))
                ->whereNull('actual_return_date')->count(),
            'academic_students_count' => Student::where('center_id', $centerId)
                ->whereHas('program', fn($q) => $q->where('code', 'academic'))
                ->count(),
            'cooperative_students_count' => Student::where('center_id', $centerId)
                ->whereHas('program', fn($q) => $q->where('code', 'cooperative'))
                ->count(),
        ];

        $stats['remaining_seats'] = $stats['total_capacity'] - $stats['occupied_seats'];

        $recent_vouchers = Voucher::where('center_id', $centerId)
            ->latest()
            ->take(3)
            ->get();

        $active_activities = Activity::where('center_id', $centerId)
            ->where('status', 'published')
            ->where('start_date', '>=', \now()->toDateString())
            ->orderBy('start_date', 'asc')
            ->take(3)
            ->get();

        $recent_violations = Violation::with('student')
            ->where('center_id', $centerId)
            ->latest()
            ->take(5)
            ->get();

        $recent_absences = Absence::with('student')
            ->whereHas('student', fn($q) => $q->where('center_id', $centerId))
            ->latest('date')
            ->take(5)
            ->get();

        $students = Student::where('center_id', $centerId)->orderBy('name_ar')->get(['id', 'name_ar', 'barcode']);

        $pending_approvals = [
            'students' => Student::where('center_id', $centerId)->where('is_profile_approved', false)->count(),
            'leaves' => Leave::whereHas('student', fn($q) => $q->where('center_id', $centerId))->where('status', 'pending')->count(),
            'vouchers' => Voucher::where('center_id', $centerId)->where('status', 'pending')->count(),
            'budgets' => MonthlyBudget::where('center_id', $centerId)->where('status', 'submitted')->count(),
            'food_budgets' => FoodBudget::where('center_id', $centerId)->where('status', 'submitted')->count(),
            'food_settlements' => FoodMonthlySettlement::where('center_id', $centerId)->where('status', 'submitted')->count(),
        ];

        return \view('dashboard.center', compact('stats', 'recent_vouchers', 'active_activities', 'students', 'recent_violations', 'recent_absences', 'pending_approvals'));
    }
    private function studentManagerDashboard($centerId)
    {
        $stats = [
            'total_students' => Student::where('center_id', $centerId)->count(),
            'pending_approval' => Student::where('center_id', $centerId)->where('is_profile_approved', false)->count(),
            'suspended_students' => Student::where('center_id', $centerId)->where('status', 'suspended')->count(),
            'on_leave_count' => Leave::whereHas('student', fn($q) => $q->where('center_id', $centerId))
                ->whereNull('actual_return_date')->count(),
            'total_capacity' => Room::where('center_id', $centerId)->where('status', 'available')->sum('capacity'),
            'occupied_seats' => RoomAssignment::whereHas('room', fn($q) => $q->where('center_id', $centerId))
                ->whereNull('released_at')->count(),
            'quran_circles_count' => QuranCircle::where('center_id', $centerId)->count(),
        ];
        $stats['remaining_seats'] = $stats['total_capacity'] - $stats['occupied_seats'];

        $recent_violations = Violation::with('student')
            ->where('center_id', $centerId)
            ->latest()
            ->take(5)
            ->get();

        $recent_absences = Absence::with('student')
            ->whereHas('student', fn($q) => $q->where('center_id', $centerId))
            ->latest('date')
            ->take(5)
            ->get();

        $circle_absences = CircleAttendance::with(['student', 'session.circle'])
            ->whereHas('session.circle', fn($q) => $q->where('center_id', $centerId))
            ->where('status', 'absent')
            ->where('is_handled', false)
            ->latest()
            ->take(10)
            ->get();

        $students = Student::where('center_id', $centerId)->orderBy('name_ar')->get(['id', 'name_ar', 'barcode']);

        return \view('dashboard.student_manager', compact('stats', 'recent_violations', 'recent_absences', 'circle_absences', 'students'));
    }

    private function financialManagerDashboard($centerId, string $selectedPeriod)
    {
        [$year, $month] = array_map('intval', explode('-', $selectedPeriod));

        $monthlyVouchers = Voucher::where('center_id', $centerId)
            ->whereMonth('date', $month)
            ->whereYear('date', $year);

        $stats = [
            'total_liquidity' => Fund::where('center_id', $centerId)->sum('balance'),
            'total_revenues' => (clone $monthlyVouchers)->where('type', 'receipt')->where('status', 'approved')->sum('amount'), // General only
            'nutrition_total_revenues' => FoodVoucher::where('center_id', $centerId)->where('type', 'receipt')->where('status', 'active')->sum('amount'), // Nutrition only
            'total_expenses' => (clone $monthlyVouchers)->whereIn('type', ['payment', 'salary'])->where('status', 'approved')->sum('amount'), // General only
            'nutrition_total_expenses' => FoodVoucher::where('center_id', $centerId)->where('type', 'payment')->where('status', 'active')->sum('amount'), // Nutrition only

            'pending_vouchers' => Voucher::where('center_id', $centerId)->where('status', 'pending')->count(),
            'pending_budgets' => MonthlyBudget::where('center_id', $centerId)->where('status', 'submitted')->count(),
            'pending_settlements' => MonthlySettlement::where('center_id', $centerId)->where('status', 'submitted')->count(),

            'nutrition_budgets' => FoodBudget::where('center_id', $centerId)->where('status', 'submitted')->count(),
            'nutrition_settlements' => FoodMonthlySettlement::where('center_id', $centerId)->where('status', 'submitted')->count(),
        ];

        $recent_vouchers = Voucher::with('fund')
            ->where('center_id', $centerId)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->latest()
            ->take(5)
            ->get();

        $pending_approvals = [
            'vouchers' => $stats['pending_vouchers'],
            'budgets' => $stats['pending_budgets'],
            'settlements' => $stats['pending_settlements'],
            'food_budgets' => $stats['nutrition_budgets'],
            'food_settlements' => $stats['nutrition_settlements'],
        ];

        return \view('dashboard.financial_manager', compact('stats', 'recent_vouchers', 'pending_approvals', 'selectedPeriod'));
    }

    private function socialManagerDashboard($centerId)
    {
        $stats = [
            'clubs_count' => Club::where('center_id', $centerId)->count(),
            'activities_count' => Activity::where('center_id', $centerId)->where('status', 'planned')->count(),
            'news_count' => News::where('center_id', $centerId)->count(),
            'published_news' => News::where('center_id', $centerId)->where('is_published', true)->count(),
        ];

        $upcoming_activities = Activity::with(['club', 'participants'])
            ->where('center_id', $centerId)
            ->where('start_date', '>=', \now()->toDateString())
            ->orderBy('start_date')
            ->take(5)
            ->get();

        $recent_news = News::where('center_id', $centerId)
            ->latest()
            ->take(3)
            ->get();

        return \view('dashboard.social_manager', compact('stats', 'upcoming_activities', 'recent_news'));
    }
}
