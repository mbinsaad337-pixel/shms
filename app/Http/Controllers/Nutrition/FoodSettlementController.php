<?php

namespace App\Http\Controllers\Nutrition;

use App\Http\Controllers\Controller;
use App\Models\FoodMonthlySettlement;
use App\Models\FoodBudget;
use App\Models\FoodSubscription;
use App\Models\FoodPurchaseInvoice;
use App\Models\FoodVoucher;
use Illuminate\Http\Request;

class FoodSettlementController extends Controller
{
    public function index()
    {
        $centerId = auth()->user()->center_id;
        $settlements = FoodMonthlySettlement::where('center_id', $centerId)
            ->with('budget')
            ->latest()
            ->paginate(15);
        return view('nutrition.settlements.index', compact('settlements'));
    }

    public function create(Request $request)
    {
        $centerId = auth()->user()->center_id;
        $currentMonth = (int)($request->month ?? date('n'));
        $currentYear = (int)($request->year ?? date('Y'));

        // Check if settlement already exists for this month
        $existing = FoodMonthlySettlement::where('center_id', $centerId)
            ->where('month', $currentMonth)
            ->where('year', $currentYear)
            ->first();

        if ($existing) {
            return redirect()->route('nutrition.settlements.show', $existing)
                ->with('info', 'يوجد تصفية لهذا الشهر بالفعل. يمكنك تحديثها من داخل الصفحة.');
        }

        // Auto-calculate using Vouchers only (as requested: Revenue = Receipt Vouchers, Expenses = Payment Vouchers)
        $totalRevenue = FoodVoucher::where('center_id', $centerId)
            ->where('type', 'receipt')
            ->where('status', 'active')
            ->whereMonth('voucher_date', $currentMonth)
            ->whereYear('voucher_date', $currentYear)
            ->sum('amount');

        $totalExpenses = FoodVoucher::where('center_id', $centerId)
            ->where('type', 'payment')
            ->where('status', 'active')
            ->whereMonth('voucher_date', $currentMonth)
            ->whereYear('voucher_date', $currentYear)
            ->sum('amount');

        $netResult = $totalRevenue - $totalExpenses;
        $resultType = $netResult > 0 ? 'surplus' : ($netResult < 0 ? 'deficit' : 'break_even');

        $budget = FoodBudget::where('center_id', $centerId)
            ->where('month', $currentMonth)
            ->where('year', $currentYear)
            ->where('status', 'approved')
            ->first();

        // Calculate total debt to suppliers (outstanding balances)
        $totalDebt = \App\Models\FoodSupplier::where('center_id', $centerId)
            ->get()
            ->sum(fn($s) => max(0, $s->balance_debit - $s->balance_credit));

        return view('nutrition.settlements.create', compact(
            'totalRevenue',
            'totalExpenses',
            'totalDebt',
            'netResult',
            'resultType',
            'currentMonth',
            'currentYear',
            'budget'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer',
            'total_revenue' => 'required|numeric',
            'total_expenses' => 'required|numeric',
            'total_debt' => 'required|numeric',
            'net_result' => 'required|numeric',
            'result_type' => 'required|in:surplus,deficit,break_even',
            'notes' => 'nullable|string',
            'budget_id' => 'nullable|exists:food_budgets,id',
        ]);

        $centerId = auth()->user()->center_id;

        // Final check to prevent duplicates
        $exists = FoodMonthlySettlement::where('center_id', $centerId)
            ->where('month', $request->month)
            ->where('year', $request->year)
            ->exists();

        if ($exists) {
            return redirect()->route('nutrition.settlements.index')
                ->with('error', 'يوجد تصفية لهذا الشهر بالفعل في النظام.');
        }

        $settlement = FoodMonthlySettlement::create([
            'center_id' => $centerId,
            'budget_id' => $request->budget_id,
            'month' => $request->month,
            'year' => $request->year,
            'total_revenue' => $request->total_revenue,
            'total_expenses' => $request->total_expenses,
            'total_debt' => $request->total_debt,
            'net_result' => $request->net_result,
            'result_type' => $request->result_type,
            'status' => 'submitted',
            'notes' => $request->notes,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('nutrition.settlements.show', $settlement)
            ->with('success', 'تم إنشاء التصفية وإرسالها للاعتماد.');
    }

    public function show(FoodMonthlySettlement $settlement)
    {
        $settlement->load(['budget', 'creator', 'approver']);
        $centerId = $settlement->center_id;
        $month = $settlement->month;
        $year = $settlement->year;

        // Fetch detailed data for the report
        $receipts = FoodVoucher::with('student')
            ->where('center_id', $centerId)
            ->where('type', 'receipt')
            ->where('status', 'active')
            ->whereMonth('voucher_date', $month)
            ->whereYear('voucher_date', $year)
            ->get();

        $invoices = FoodPurchaseInvoice::with('supplier')
            ->where('center_id', $centerId)
            ->where('status', 'approved')
            ->whereMonth('invoice_date', $month)
            ->whereYear('invoice_date', $year)
            ->get();

        $payments = FoodVoucher::with('supplier')
            ->where('center_id', $centerId)
            ->where('type', 'payment')
            ->where('status', 'active')
            ->whereMonth('voucher_date', $month)
            ->whereYear('voucher_date', $year)
            ->get();

        return view('nutrition.settlements.show', compact('settlement', 'receipts', 'invoices', 'payments'));
    }

    public function recalculate(FoodMonthlySettlement $settlement)
    {
        if ($settlement->status === 'approved') {
            return back()->with('error', 'لا يمكن تحديث تصفية معتمدة بالفعل. يجب رفضها أولاً إذا كنت ترغب في تعديلها.');
        }

        $centerId = $settlement->center_id;
        $month = $settlement->month;
        $year = $settlement->year;

        $totalRevenue = FoodVoucher::where('center_id', $centerId)
            ->where('type', 'receipt')
            ->where('status', 'active')
            ->whereMonth('voucher_date', $month)
            ->whereYear('voucher_date', $year)
            ->sum('amount');

        $totalExpenses = FoodVoucher::where('center_id', $centerId)
            ->where('type', 'payment')
            ->where('status', 'active')
            ->whereMonth('voucher_date', $month)
            ->whereYear('voucher_date', $year)
            ->sum('amount');

        $netResult = $totalRevenue - $totalExpenses;
        $resultType = $netResult > 0 ? 'surplus' : ($netResult < 0 ? 'deficit' : 'break_even');

        // Calculate total debt to suppliers (outstanding balances)
        $totalDebt = \App\Models\FoodSupplier::where('center_id', $centerId)
            ->get()
            ->sum(fn($s) => max(0, $s->balance_debit - $s->balance_credit));

        $settlement->update([
            'total_revenue' => $totalRevenue,
            'total_expenses' => $totalExpenses,
            'total_debt' => $totalDebt,
            'net_result' => $netResult,
            'result_type' => $resultType,
        ]);

        return back()->with('success', 'تم تحديث أرقام التصفية بناءً على البيانات الحالية في النظام.');
    }

    public function approve(FoodMonthlySettlement $settlement)
    {
        if ($settlement->status !== 'submitted') {
            return back()->with('error', 'التصفية ليست في حالة الانتظار.');
        }
        $settlement->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);
        return back()->with('success', 'تم اعتماد التصفية الشهرية.');
    }

    public function reject(Request $request, FoodMonthlySettlement $settlement)
    {
        $request->validate(['rejection_reason' => 'required|string']);
        $settlement->update(['status' => 'rejected', 'rejection_reason' => $request->rejection_reason]);
        return back()->with('success', 'تم رفض التصفية.');
    }

    public function destroy(FoodMonthlySettlement $settlement)
    {
        // Allow super-admin to delete anything, or allow others if not approved
        if ($settlement->status === 'approved' && !auth()->user()->hasRole('super-admin') && !auth()->user()->hasRole('nutrition-manager')) {
            return back()->with('error', 'لا يمكن حذف تصفية معتمدة بالفعل إلا من قبل المدير.');
        }

        $settlement->delete();

        return redirect()->route('nutrition.settlements.index')
            ->with('success', 'تم حذف التصفية الشهرية بنجاح.');
    }

    public function exportPdf(FoodMonthlySettlement $settlement, \App\Services\PdfService $pdfService)
    {
       $settlement->load(['budget', 'creator', 'approver', 'details']);
        $centerId = $settlement->center_id;
        $month = $settlement->month;
        $year = $settlement->year;

        $receipts = FoodVoucher::with('student')
            ->where('center_id', $centerId)
            ->where('type', 'receipt')
            ->where('status', 'active')
            ->whereMonth('voucher_date', $month)
            ->whereYear('voucher_date', $year)
            ->get();

        $invoices = FoodPurchaseInvoice::with('supplier')
            ->where('center_id', $centerId)
            ->where('status', 'approved')
            ->whereMonth('invoice_date', $month)
            ->whereYear('invoice_date', $year)
            ->get();

        $payments = FoodVoucher::with('supplier')
            ->where('center_id', $centerId)
            ->where('type', 'payment')
            ->where('status', 'active')
            ->whereMonth('voucher_date', $month)
            ->whereYear('voucher_date', $year)
            ->get();

        return $pdfService->stream('pdf.nutrition.settlements.show', [
            'settlement' => $settlement,
            'receipts' => $receipts,
            'invoices' => $invoices,
            'payments' => $payments,
        ], 'التصفية المالية لقسم التغذية', 'food_settlement_' . $settlement->id . '.pdf', 'portrait');
    }
}
