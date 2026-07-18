<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Violation;
use App\Models\Room;
use App\Models\Voucher;
use App\Models\Fund;
use App\Models\MonthlySettlement;
use App\Models\SettlementDetail;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function show(Request $request, $type)
    {
        $user = auth()->user();
        $centerId = $user->center_id;
        $isExecutive = $user->hasRole('super-admin') || $user->hasRole('executive-manager');

        $data = [];
        $view = "reports.{$type}";

        switch ($type) {
            case 'students':
                $query = Student::with('center', 'room');
                if (!$isExecutive)
                    $query->where('center_id', $centerId);
                $data = $query->get();
                break;
            case 'violations':
                $query = Violation::with('student', 'center');
                if (!$isExecutive)
                    $query->where('center_id', $centerId);
                $data = $query->get();
                break;
            case 'funds':
                $month = (int) ($request->month ?? now()->month);
                $year  = (int) ($request->year  ?? now()->year);

                $query = Fund::with(['center', 'budgetItems' => function($q) use ($month, $year) {
                    $q->whereHas('monthlyBudget', function($sbq) use ($month, $year) {
                        $sbq->where('month', $month)
                            ->where('year', $year)
                            ->where('status', 'approved');
                    });
                }]);

                if ($request->filled('center_id') && $isExecutive) {
                    $query->where('center_id', (int) $request->center_id);
                } elseif (!$isExecutive) {
                    $query->where('center_id', $centerId);
                }
                $data = $query->get();
                break;
            case 'vouchers':
                $query = Voucher::with('center', 'fund')->orderBy('date', 'desc');
                if (!$isExecutive)
                    $query->where('center_id', $centerId);
                $data = $query->get();
                break;
        }

        if ($type === 'funds') {
            $month   = (int) ($request->month ?? now()->month);
            $year    = (int) ($request->year  ?? now()->year);
            $centers = \App\Models\Center::all();

            // جلب أرصدة التصفية لكل صندوق (جميع الحالات عدا المحذوفة والمرفوضة)
            $settlementBalances = SettlementDetail::whereHas('settlement', function ($q) use ($month, $year) {
                $q->where('month', $month)
                  ->where('year', $year)
                  ->whereNotIn('status', ['deleted', 'rejected']);
            })->get()->keyBy('fund_id');

            return view($view, compact('data', 'isExecutive', 'month', 'year', 'centers', 'settlementBalances'));
        }

        return view($view, compact('data', 'isExecutive'));
    }
}
