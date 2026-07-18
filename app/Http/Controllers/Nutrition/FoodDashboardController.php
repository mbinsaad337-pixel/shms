<?php

namespace App\Http\Controllers\Nutrition;

use App\Http\Controllers\Controller;
use App\Models\FoodBudget;
use App\Models\FoodBudgetLine;
use App\Models\FoodSubscription;
use App\Models\FoodPurchaseInvoice;
use App\Models\FoodVoucher;
use Illuminate\Http\Request;

class FoodDashboardController extends Controller
{
    public function index()
    {
        $centerId = auth()->user()->center_id;

        $stats = [
            'total_budgets' => FoodBudget::where('center_id', $centerId)->sum('total_amount'),
            'total_subscriptions' => FoodSubscription::where('center_id', $centerId)->where('status', 'active')->sum('total_due'),
            'total_collected' => FoodVoucher::where('center_id', $centerId)->where('type', 'receipt')->where('status', 'active')->whereMonth('voucher_date', date('n'))->whereYear('voucher_date', date('Y'))->sum('amount'),
            'total_expenses' => FoodVoucher::where('center_id', $centerId)->where('type', 'payment')->where('status', 'active')->whereMonth('voucher_date', date('n'))->whereYear('voucher_date', date('Y'))->sum('amount'),
            'active_subscribers' => FoodSubscription::where('center_id', $centerId)->where('status', 'active')->count(),
            'suspended_subscribers' => FoodSubscription::where('center_id', $centerId)->where('status', 'suspended')->count(),
            'pending_payments' => FoodSubscription::where('center_id', $centerId)
                ->where('status', 'active')
                ->whereRaw('total_paid < total_due')
                ->count(),
            'late_today' => \App\Models\FoodAttendanceReport::whereHas('student', fn($q) => $q->where('center_id', $centerId))
                ->where('meal_date', today())
                ->where('status', 'late')
                ->count(),
            'absent_today' => \App\Models\FoodAttendanceReport::whereHas('student', fn($q) => $q->where('center_id', $centerId))
                ->where('meal_date', today())
                ->where('status', 'absent')
                ->count(),
        ];

        $stats['net_result'] = $stats['total_collected'] - $stats['total_expenses'];

        // Recent budgets
        $recentBudgets = FoodBudget::where('center_id', $centerId)
            ->latest()
            ->take(5)
            ->get();

        // Recent invoices
        $recentInvoices = FoodPurchaseInvoice::where('center_id', $centerId)
            ->with('supplier')
            ->latest()
            ->take(5)
            ->get();

        // Subscribers with debt
        $debtorStudents = FoodSubscription::with('student')
            ->where('center_id', $centerId)
            ->where('status', 'active')
            ->whereRaw('total_paid < total_due')
            ->orderByRaw('(total_due - total_paid) DESC')
            ->take(5)
            ->get();

        return view('nutrition.dashboard', compact(
            'stats',
            'recentBudgets',
            'recentInvoices',
            'debtorStudents'
        ));
    }
    public function attendanceReports(Request $request)
    {
        $centerId = auth()->user()->center_id;
        $query = \App\Models\FoodAttendanceReport::with('student')
            ->whereHas('student', fn($q) => $q->where('center_id', $centerId))
            ->whereIn('status', ['late', 'absent']);

        if ($request->date) {
            $query->whereDate('meal_date', $request->date);
        } else {
            $query->whereDate('meal_date', today());
        }

        if ($request->meal_type) {
            $query->where('meal_type', $request->meal_type);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $reports = $query->latest()->paginate(30)->withQueryString();

        return view('nutrition.reports.attendance', compact('reports'));
    }
}
