<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MonthlyBudget;
use App\Models\MonthlySettlement;
use App\Models\Fund;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApprovalController extends Controller
{
    public function index()
    {
        $pendingBudgets = MonthlyBudget::with('center', 'submitter')->where('status', 'confirmed')->get();
        $pendingSettlements = MonthlySettlement::with('center', 'submitter')->where('status', 'submitted')->get();

        return view('admin.approvals.index', compact('pendingBudgets', 'pendingSettlements'));
    }

    public function approveBudget(Request $request, MonthlyBudget $budget)
    {
        DB::transaction(function () use ($budget) {
            $budget->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            // Increment fund balances
            foreach ($budget->items as $item) {
                $fund = Fund::find($item->fund_id);
                $fund->increment('balance', $item->approved_amount);
            }
        });

        return back()->with('success', 'تم اعتماد الميزانية وتحديث الصناديق');
    }

    public function approveSettlement(Request $request, MonthlySettlement $settlement)
    {
        $settlement->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'تم اعتماد التصفية وإغلاق الشهر المالي');
    }
}
