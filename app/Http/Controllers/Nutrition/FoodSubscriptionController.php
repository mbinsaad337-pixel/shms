<?php

namespace App\Http\Controllers\Nutrition;

use App\Http\Controllers\Controller;
use App\Models\FoodSubscription;
use App\Models\Student;
use App\Models\FoodBudget;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FoodSubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $centerId = auth()->user()->center_id;
        $query = FoodSubscription::with('student')
            ->where('center_id', $centerId);

        if ($request->status)
            $query->where('status', $request->status);
        if ($request->search) {
            $query->whereHas(
                'student',
                fn($q) =>
                $q->where('name_ar', 'like', "%{$request->search}%")
                    ->orWhere('university_id', 'like', "%{$request->search}%")
            );
        }

        $subscriptions = $query->latest()->paginate(20);
        $stats = [
            'active' => FoodSubscription::where('center_id', $centerId)->where('status', 'active')->count(),
            'suspended' => FoodSubscription::where('center_id', $centerId)->where('status', 'suspended')->count(),
            'expired' => FoodSubscription::where('center_id', $centerId)->where('status', 'expired')->count(),
        ];

        return view('nutrition.subscriptions.index', compact('subscriptions', 'stats'));
    }

    public function exportPdf(Request $request, \App\Services\PdfService $pdfService)
    {
        $centerId = auth()->user()->center_id;
        $query = FoodSubscription::with('student')
            ->where('center_id', $centerId);

        if ($request->status)
            $query->where('status', $request->status);
        if ($request->search) {
            $query->whereHas(
                'student',
                fn($q) =>
                $q->where('name_ar', 'like', "%{$request->search}%")
                    ->orWhere('university_id', 'like', "%{$request->search}%")
            );
        }

        $subscriptions = $query->latest()->get();

        return $pdfService->stream('pdf.nutrition.subscriptions.list-pdf', [
            'data' => $subscriptions,
        ], 'تقرير مشتركي التغذية', 'nutrition_subscribers_' . now()->format('Y-m-d') . '.pdf', 'portrait');
    }

    public function create()
    {
        $centerId = auth()->user()->center_id;
        $students = Student::where('center_id', $centerId)
            ->whereIn('status', ['registered', 'residing'])
            ->whereDoesntHave('foodSubscriptions', fn($q) => $q->where('status', 'active'))
            ->orderBy('name_ar')
            ->get(['id', 'name_ar', 'university_id']);
        $budgets = FoodBudget::where('center_id', $centerId)
            ->where('status', 'approved')
            ->orderByDesc('year')->orderByDesc('month')
            ->get();
        return view('nutrition.subscriptions.create', compact('students', 'budgets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'exists:students,id',
            'subscription_type' => 'required|in:daily,semi_monthly,monthly',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'days_count' => 'required|integer|min:1',
            'daily_rate' => 'required|numeric|min:0',
            'last_payment_date' => 'nullable|date',
            'budget_id' => 'nullable|exists:food_budgets,id',
        ]);

        $centerId = auth()->user()->center_id;
        $created = 0;

        foreach ($request->student_ids as $studentId) {
            // Check no active subscription
            $exists = FoodSubscription::where('student_id', $studentId)
                ->where('status', 'active')->exists();
            if ($exists)
                continue;

            $daysCount = (int)$request->days_count;
            $type = $request->subscription_type;
            $inputValue = (float)$request->daily_rate; // This is Total for monthly/semi, and Rate for daily
            
            if ($type === 'monthly' || $type === 'semi_monthly') {
                $totalDue = $inputValue;
                $dailyRate = $daysCount > 0 ? $totalDue / $daysCount : 0;
            } else {
                $dailyRate = $inputValue;
                $totalDue = $daysCount * $dailyRate;
            }

            $sub = FoodSubscription::create([
                'center_id' => $centerId,
                'student_id' => $studentId,
                'budget_id' => $request->budget_id,
                'subscription_type' => $request->subscription_type,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'days_count' => $daysCount,
                'last_payment_date' => $request->last_payment_date,
                'daily_rate' => $dailyRate,
                'total_due' => $totalDue,
                'total_paid' => 0,
                'status' => 'active',
                'created_by' => auth()->id(),
            ]);
            $sub->generateQrCode();
            $created++;
        }

        return redirect()->route('nutrition.subscriptions.index')
            ->with('success', "تم إضافة {$created} اشتراك بنجاح.");
    }

    public function show(FoodSubscription $subscription)
    {
        $subscription->load(['student', 'distributions.distributor', 'budget']);
        return view('nutrition.subscriptions.show', compact('subscription'));
    }

    public function suspend(Request $request, FoodSubscription $subscription)
    {
        $request->validate(['suspended_reason' => 'required|string']);
        $subscription->update(['status' => 'suspended', 'suspended_reason' => $request->suspended_reason]);
        return back()->with('success', 'تم إيقاف اشتراك الطالب.');
    }

    public function activate(FoodSubscription $subscription)
    {
        $subscription->update(['status' => 'active', 'suspended_reason' => null]);
        return back()->with('success', 'تم تفعيل اشتراك الطالب.');
    }

    public function edit(FoodSubscription $subscription)
    {
        $centerId = auth()->user()->center_id;
        $budgets = FoodBudget::where('center_id', $centerId)
            ->where('status', 'approved')
            ->orderByDesc('year')->orderByDesc('month')
            ->get();
        return view('nutrition.subscriptions.edit', compact('subscription', 'budgets'));
    }

    public function update(Request $request, FoodSubscription $subscription)
    {
        $request->validate([
            'subscription_type' => 'required|in:daily,semi_monthly,monthly',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'days_count' => 'required|integer|min:1',
            'daily_rate' => 'required|numeric|min:0',
            'last_payment_date' => 'nullable|date',
            'budget_id' => 'nullable|exists:food_budgets,id',
            'status' => 'required|in:active,suspended,expired,cancelled',
        ]);

        $daysCount = (int)$request->days_count;
        $type = $request->subscription_type;
        $inputValue = (float)$request->daily_rate;
        
        if ($type === 'monthly' || $type === 'semi_monthly') {
            $totalDue = $inputValue;
            $dailyRate = $daysCount > 0 ? $totalDue / $daysCount : 0;
        } else {
            $dailyRate = $inputValue;
            $totalDue = $daysCount * $dailyRate;
        }

        $subscription->update([
            'budget_id' => $request->budget_id,
            'subscription_type' => $request->subscription_type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'days_count' => $daysCount,
            'last_payment_date' => $request->last_payment_date,
            'daily_rate' => $dailyRate,
            'total_due' => $totalDue,
            'status' => $request->status,
        ]);

        return redirect()->route('nutrition.subscriptions.index')
            ->with('success', "تم تحديث اشتراك الطالب بنجاح.");
    }

    public function addPayment(Request $request, FoodSubscription $subscription)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'voucher_date' => 'nullable|date',
            'description' => 'nullable|string'
        ]);

        $centerId = auth()->user()->center_id;
        $amount = (float) $request->amount;
        $date = $request->voucher_date ?? date('Y-m-d');
        $desc = $request->description ?? ("سداد جزء من الرسوم - " . $subscription->student->name_ar);

        \Illuminate\Support\Facades\DB::transaction(function () use ($subscription, $centerId, $amount, $date, $desc) {
            // 1. Update subscription
            $subscription->increment('total_paid', $amount);

            // 2. Create Receipt Voucher
            $nextNumber = 'FV-' . date('Ym', strtotime($date)) . '-' . str_pad(
                \App\Models\FoodVoucher::where('center_id', $centerId)->count() + 1,
                3,
                '0',
                STR_PAD_LEFT
            );

            \App\Models\FoodVoucher::create([
                'center_id' => $centerId,
                'voucher_number' => $nextNumber,
                'type' => 'receipt',
                'voucher_date' => $date,
                'student_id' => $subscription->student_id,
                'amount' => $amount,
                'description' => $desc,
                'status' => 'active',
                'created_by' => auth()->id(),
            ]);
        });

        return back()->with('success', 'تم تسجيل الدفعة وإصدار سند قبض آلي بنجاح.');
    }

    public function approve(FoodSubscription $subscription)
    {
        if ($subscription->status !== 'pending') {
            return back()->with('error', 'هذا الطلب ليس قيد المراجعة.');
        }

        $subscription->update([
            'status' => 'active',
            'rejection_reason' => null
        ]);

        if (empty($subscription->qr_code)) {
            $subscription->generateQrCode();
        }

        return back()->with('success', 'تم اعتماد طلب الاشتراك بنجاح. الطالب الآن مشترك فعال.');
    }

    public function reject(Request $request, FoodSubscription $subscription)
    {
        if ($subscription->status !== 'pending') {
            return back()->with('error', 'هذا الطلب ليس قيد المراجعة.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:255'
        ]);

        $subscription->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason
        ]);

        return back()->with('success', 'تم رفض طلب الاشتراك.');
    }

    public function destroy(FoodSubscription $subscription)
    {
        $user = auth()->user();
        if (!$user->hasRole('super-admin') && !$user->hasRole('executive-manager') && !$user->hasRole('nutrition-manager')) {
            abort(403, 'غير مصرح لك بحذف اشتراكات التغذية.');
        }
        if (!$user->hasRole('super-admin') && $user->center_id && $subscription->center_id !== $user->center_id) {
            abort(403, 'غير مصرح لك بالتعامل مع اشتراكات هذا المركز.');
        }

        $subscription->delete();
        return back()->with('success', 'تم حذف الاشتراك بنجاح.');
    }
}
