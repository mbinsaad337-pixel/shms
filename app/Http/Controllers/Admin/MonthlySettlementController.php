<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MonthlySettlement;
use App\Models\SettlementDetail;
use App\Models\Fund;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MonthlySettlementController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $month = (int) $request->get('month', date('m'));
        $year = (int) $request->get('year', date('Y'));

        $centerId = $user->center_id ?? $request->get('center_id');

        $funds = collect();
        $currentMonthVouchers = collect();
        $currentSettlementStatus = null;

        if ($centerId) {
            $funds = Fund::where('center_id', $centerId)->get();
            $currentMonthVouchers = Voucher::with(['creator', 'targetFund', 'fund', 'student'])
                ->where('center_id', $centerId)
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
                ->get();

            $currentSettlementStatus = MonthlySettlement::where('center_id', $centerId)
                ->where('month', $month)
                ->where('year', $year)
                ->first();
        }

        $settlements = MonthlySettlement::query()
            ->where('status', '!=', 'deleted')
            ->when($user->hasRole('super-admin'), function ($q) use ($request) {
                if ($request->filled('center_id')) {
                    $q->where('center_id', $request->center_id);
                }
            })
            ->when(!$user->hasRole('super-admin') && $user->center_id, fn($q) => $q->where('center_id', $user->center_id))
            ->when(!$user->hasRole('super-admin') && !$user->center_id && !$user->can('confirm-settlements'), function ($q) {
                // المدير العام يرى فقط ما تم تأكيده من مدير المركز
                return $q->whereIn('status', ['confirmed', 'approved', 'rejected']);
            })
            ->with(['submitter', 'center'])
            ->latest()
            ->paginate(15);

        $centers = \App\Models\Center::all();

        return view('settlements.index', compact('settlements', 'funds', 'currentMonthVouchers', 'currentSettlementStatus', 'month', 'year', 'centerId', 'centers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'month' => 'required|integer',
            'year' => 'required|integer',
            'fund_ids' => 'required|array|min:1',
            'fund_ids.*' => 'exists:funds,id',
        ]);

        $centerId = auth()->user()->center_id;

        if (!$centerId) {
            return back()->with('error', 'يجب أن تكون مرتبطاً بمركز لرفع التصفية.');
        }

        // Check for existing settlement for this month (excluding deleted/returned)
        $exists = MonthlySettlement::where('center_id', $centerId)
            ->where('month', $validated['month'])
            ->where('year', $validated['year'])
            ->whereNotIn('status', ['deleted', 'returned', 'rejected'])
            ->exists();

        if ($exists) {
            return back()->with('error', 'تم رفع تصفية مالية لهذا الشهر مسبقاً لهذا المركز وهي قيد المعالجة.');
        }

        DB::transaction(function () use ($validated, $centerId) {
            $settlement = MonthlySettlement::create([
                'center_id' => $centerId,
                'month' => $validated['month'],
                'year' => $validated['year'],
                'status' => 'submitted',
                'submitted_by' => auth()->id(),
                'submitted_at' => now(),
            ]);

            $funds = Fund::whereIn('id', $validated['fund_ids'])->where('center_id', $centerId)->get();
            $totalBudget = 0;
            $totalSpent = 0;
            $totalIncomeAll = 0;

            foreach ($funds as $fund) {
                // Calculate income: receipts/sales_invoices where fund_id is this fund,
                // OR transfers where target_fund_id is this fund.
                $income = Voucher::whereMonth('date', $validated['month'])
                    ->whereYear('date', $validated['year'])
                    ->where(function($q) use ($fund) {
                        $q->where(function($q2) use ($fund) {
                            $q2->where('fund_id', $fund->id)->where('type', 'receipt');
                        })->orWhere(function($q2) use ($fund) {
                            $q2->where('target_fund_id', $fund->id)->where('type', 'transfer');
                        });
                    })
                    ->sum('amount');

                // Calculate expense: payments/salaries/purchase_invoices/transfers where fund_id is this fund.
                $expense = Voucher::whereMonth('date', $validated['month'])
                    ->whereYear('date', $validated['year'])
                    ->where('fund_id', $fund->id)
                    ->whereIn('type', ['payment', 'salary', 'transfer'])
                    ->sum('amount');

                // Opening balance = Current balance - Month's Net Change (Income - Expense)
                $openingBalance = $fund->balance - $income + $expense;

                SettlementDetail::create([
                    'settlement_id' => $settlement->id,
                    'fund_id' => $fund->id,
                    'opening_balance' => $openingBalance,
                    'total_income' => $income,
                    'total_expense' => $expense,
                    'closing_balance' => $fund->balance,
                ]);

                $totalBudget += $openingBalance;
                $totalSpent += $expense;
                $totalIncomeAll += $income;
            }

            $settlement->update([
                'total_budget' => $totalBudget,
                'total_spent' => $totalSpent,
                'total_remaining' => $totalBudget + $totalIncomeAll - $totalSpent,
            ]);
        });

        return redirect()->route('settlements.index')->with('success', 'تم إرسال التصفية الشهرية للمراجعة والاعتماد.');
    }

    public function show(MonthlySettlement $settlement)
    {
        $settlement->load(['details.fund', 'submitter', 'approver', 'center']);

        $vouchers = Voucher::with(['creator', 'targetFund', 'student'])
            ->where('center_id', $settlement->center_id)
            ->whereMonth('date', $settlement->month)
            ->whereYear('date', $settlement->year)
            ->get();

        return view('settlements.show', compact('settlement', 'vouchers'));
    }

    public function exportPdf(MonthlySettlement $settlement, \App\Services\PdfService $pdfService)
    {
        $settlement->load(['details.fund', 'submitter', 'approver', 'center']);

        $vouchers = Voucher::with(['creator', 'fund', 'targetFund', 'student'])
            ->where('center_id', $settlement->center_id)
            ->whereMonth('date', $settlement->month)
            ->whereYear('date', $settlement->year)
            ->get();

        $filename = 'تصفية_' . $settlement->month . '_' . $settlement->year . '.pdf';

        return $pdfService->stream('pdf.settlements.show', [
            'settlement' => $settlement,
            'vouchers'   => $vouchers,
        ], 'تقرير التصفية المالية', $filename, 'portrait');
    }

    public function confirm(MonthlySettlement $settlement)
    {
        if (!auth()->user()->can('confirm-settlements')) {
            abort(403);
        }

        if ($settlement->status !== 'submitted') {
            return back()->with('error', 'هذه التصفية ليست في حالة تتطلب التأكيد.');
        }

        $settlement->update(['status' => 'confirmed']);

        return redirect()->route('settlements.index')->with('success', 'تم تأكيد التصفية وإرسالها للمدير العام للاعتماد النهائي.');
    }

    public function reject(MonthlySettlement $settlement)
    {
        if (!auth()->user()->can('confirm-settlements') && !auth()->user()->can('approve-settlements')) {
            abort(403);
        }

        if (in_array($settlement->status, ['approved', 'rejected'])) {
            return back()->with('error', 'لا يمكن رفض تصفية تم اعتمادها أو رفضها مسبقاً.');
        }

        // Returned status allows re-submission
        $settlement->update(['status' => 'returned']);

        return redirect()->route('settlements.index')->with('success', 'تم إعادة التصفية للمراجعة والتعديل.');
    }

    public function approve(MonthlySettlement $settlement)
    {
        if (!auth()->user()->can('approve-settlements')) {
            abort(403);
        }

        $isSuperAdmin = auth()->user()->hasRole('super-admin');

        // السوبر آدمن يمكنه الاعتماد مباشرة حتى من حالة مرسلة دون انتظار التأكيد
        $allowedStatuses = $isSuperAdmin ? ['submitted', 'confirmed'] : ['confirmed'];

        if (!in_array($settlement->status, $allowedStatuses)) {
            return back()->with('error', 'لا يمكن اعتماد تصفية لم يتم تأكيدها من قبل مدير المركز.');
        }

        DB::transaction(function () use ($settlement) {
            $settlement->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            // تصفير الصناديق التي دخلت في التصفية عبر استعلام مباشر لضمان التنفيذ
            $fundIds = $settlement->details()->pluck('fund_id')->filter()->toArray();
            if (!empty($fundIds)) {
                \DB::table('funds')->whereIn('id', $fundIds)->update(['balance' => 0.00]);
            }
        });

        return redirect()->route('settlements.index')->with('success', 'تم الاعتماد النهائي للتصفية الشهرية وتصفير أرصدة الصناديق المتضمنة بنجاح.');
    }

    public function destroy(MonthlySettlement $settlement)
    {
        if ($settlement->status === 'approved' && !auth()->user()->hasRole('super-admin')) {
            return back()->with('error', 'لا يمكن حذف تصفية معتمدة نهائياً إلا من خلال مدير قسم المراكز الطلابية.');
        }

        // Only owner or admin can delete
        if ($settlement->submitted_by !== auth()->id() && !auth()->user()->hasRole('super-admin')) {
            abort(403);
        }

        $settlement->update(['status' => 'deleted']);

        return redirect()->route('settlements.index')->with('success', 'تم حذف طلب التصفية بنجاح.');
    }

    public function recalculate(MonthlySettlement $settlement)
    {
        if ($settlement->status === 'approved') {
            return back()->with('error', 'لا يمكن إعادة حساب تصفية معتمدة.');
        }

        DB::transaction(function () use ($settlement) {
            $totalBudget = 0;
            $totalSpent = 0;
            $totalIncomeAll = 0;

            foreach ($settlement->details as $detail) {
                $fund = $detail->fund;
                
                $income = Voucher::whereMonth('date', $settlement->month)
                    ->whereYear('date', $settlement->year)
                    ->where(function($q) use ($fund) {
                        $q->where(function($q2) use ($fund) {
                            $q2->where('fund_id', $fund->id)->where('type', 'receipt');
                        })->orWhere(function($q2) use ($fund) {
                            $q2->where('target_fund_id', $fund->id)->where('type', 'transfer');
                        });
                    })
                    ->sum('amount');

                $expense = Voucher::whereMonth('date', $settlement->month)
                    ->whereYear('date', $settlement->year)
                    ->where('fund_id', $fund->id)
                    ->whereIn('type', ['payment', 'salary', 'transfer'])
                    ->sum('amount');

                $openingBalance = $detail->closing_balance - $income + $expense;

                $detail->update([
                    'opening_balance' => $openingBalance,
                    'total_income' => $income,
                    'total_expense' => $expense,
                ]);

                $totalBudget += $openingBalance;
                $totalSpent += $expense;
                $totalIncomeAll += $income;
            }

            $settlement->update([
                'total_budget' => $totalBudget,
                'total_spent' => $totalSpent,
                'total_remaining' => $totalBudget + $totalIncomeAll - $totalSpent,
            ]);
        });

        return back()->with('success', 'تم إعادة حساب التصفية بناءً على البيانات الجديدة.');
    }
}
