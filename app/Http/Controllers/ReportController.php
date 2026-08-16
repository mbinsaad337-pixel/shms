<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Violation;
use App\Models\Room;
use App\Models\Voucher;
use App\Models\Fund;
use App\Models\Center;
use App\Models\MonthlySettlement;
use App\Models\SettlementDetail;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    /**
     * Show the interactive HTML funds report page with filter support.
     */
    public function fundsView(Request $request)
    {
        $user        = auth()->user();
        $centerId    = $user->center_id;
        $isExecutive = $user->hasRole('super-admin') || $user->hasRole('executive-manager');

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

        $settlementBalances = SettlementDetail::whereHas('settlement', function ($q) use ($month, $year) {
            $q->where('month', $month)
              ->where('year', $year)
              ->whereNotIn('status', ['deleted', 'rejected']);
        })->get()->keyBy('fund_id');

        $centers = $isExecutive ? Center::orderBy('name')->get() : collect();

        return view('reports.funds', compact(
            'data', 'month', 'year', 'settlementBalances', 'isExecutive', 'centers'
        ));
    }

    public function show(Request $request, $type, \App\Services\PdfService $pdfService)
    {
        $user = auth()->user();
        $centerId = $user->center_id;
        $isExecutive = $user->hasRole('super-admin') || $user->hasRole('executive-manager');

        $data = [];
        $view = "pdf.reports.{$type}";

        switch ($type) {
            case 'students':
                $query = Student::with('center', 'room');
                if (!$isExecutive)
                    $query->where('center_id', $centerId);
                $data = $query->get();
                return $pdfService->stream($view, ['data' => $data], 'تقرير الطلاب', 'students_report.pdf', 'portrait');
                break;

            case 'violations':
                $query = Violation::with('student', 'center', 'penalty');
                if (!$isExecutive)
                    $query->where('center_id', $centerId);
                $data = $query->get();
                return $pdfService->stream($view, ['data' => $data], 'تقرير المخالفات', 'violations_report.pdf', 'portrait');
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

                $settlementBalances = SettlementDetail::whereHas('settlement', function ($q) use ($month, $year) {
                    $q->where('month', $month)
                      ->where('year', $year)
                      ->whereNotIn('status', ['deleted', 'rejected']);
                })->get()->keyBy('fund_id');

                return $pdfService->stream($view, [
                    'data' => $data,
                    'month' => $month,
                    'year' => $year,
                    'settlementBalances' => $settlementBalances
                ], 'تقرير الصناديق', 'funds_report.pdf', 'landscape');
                break;

            case 'vouchers':
                $query = Voucher::with('center', 'fund')->where('status', 'approved')->orderBy('date', 'desc');
                if (!$isExecutive)
                    $query->where('center_id', $centerId);
                $data = $query->get();
                return $pdfService->stream($view, ['data' => $data], 'تقرير السندات', 'vouchers_report.pdf', 'portrait');
                break;
        }

        abort(404, 'Report type not found');
    }
}
