<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FoodSubscriptionController extends Controller
{
    public function index()
    {
        $student = auth()->user()->student;
        if (!$student) {
            abort(403, 'غير مصرح.');
        }

        // Active or pending subscriptions
        $subscriptions = \App\Models\FoodSubscription::where('student_id', $student->id)
            ->with(['budget'])
            ->latest()
            ->paginate(5);

        // Available budgets to subscribe
        $availableBudgets = \App\Models\FoodBudget::where('center_id', $student->center_id)
            ->where('status', 'approved')
            ->orderByDesc('year')->orderByDesc('month')
            ->get();

        // Check if student already has a pending or active subscription
        $hasActiveOrPending = \App\Models\FoodSubscription::where('student_id', $student->id)
            ->whereIn('status', ['active', 'pending'])
            ->exists();
            
        // Student Receipt Vouchers
        $vouchers = \App\Models\FoodVoucher::where('student_id', $student->id)
            ->where('type', 'receipt')
            ->latest()
            ->get();

        return view('student.food-subscriptions.index', compact('subscriptions', 'availableBudgets', 'hasActiveOrPending', 'vouchers'));
    }

    public function store(Request $request)
    {
        $student = auth()->user()->student;
        if (!$student) {
            abort(403);
        }

        $request->validate([
            'budget_id' => 'required|exists:food_budgets,id',
            'subscription_type' => 'required|in:daily,semi_monthly,monthly',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        // Check if already has active or pending
        $exists = \App\Models\FoodSubscription::where('student_id', $student->id)
            ->whereIn('status', ['active', 'pending'])->exists();
            
        if ($exists) {
            return back()->with('error', 'لديك اشتراك فعال أو قيد المراجعة بالفعل.');
        }

        $budget = \App\Models\FoodBudget::findOrFail($request->budget_id);
        
        $start = \Carbon\Carbon::parse($request->start_date);
        $end = \Carbon\Carbon::parse($request->end_date);
        $daysCount = $start->diffInDays($end) + 1;

        $type = $request->subscription_type;
        
        if ($type === 'monthly' || $type === 'semi_monthly') {
            $totalDue = $budget->cost_per_student;
            if ($type === 'semi_monthly') {
                $totalDue = $totalDue / 2;
            }
            $dailyRate = $daysCount > 0 ? $totalDue / $daysCount : 0;
        } else {
            $dailyRate = $budget->daily_rate;
            $totalDue = $daysCount * $dailyRate;
        }

        \App\Models\FoodSubscription::create([
            'center_id' => $student->center_id,
            'student_id' => $student->id,
            'budget_id' => $budget->id,
            'subscription_type' => $type,
            'start_date' => $start,
            'end_date' => $end,
            'days_count' => $daysCount,
            'last_payment_date' => $budget->last_payment_date,
            'daily_rate' => $dailyRate,
            'total_due' => $totalDue,
            'total_paid' => 0,
            'status' => 'pending', // Starts as pending
            'created_by' => auth()->id(),
        ]);

        return back()->with('success', 'تم إرسال طلب الاشتراك بنجاح وهو الآن قيد المراجعة.');
    }
}
