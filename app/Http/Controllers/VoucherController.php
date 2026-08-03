<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use App\Models\Fund;
use App\Models\MonthlySettlement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VoucherController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Voucher::query();

        if ($user->hasRole('super-admin')) {
            if ($request->filled('center_id')) {
                $query->where('center_id', $request->center_id);
            }
        } elseif ($user->center_id) {
            $query->where('center_id', $user->center_id);
        }

        if ($request->filled('fund_id')) {
            $query->where(function($q) use ($request) {
                $q->where('fund_id', $request->fund_id)
                  ->orWhere('target_fund_id', $request->fund_id);
            });
        }

        $selectedPeriod = null;

        if ($request->filled('period') && preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $request->period)) {
            [$year, $month] = array_map('intval', explode('-', $request->period));
            $query->whereMonth('date', $month)->whereYear('date', $year);
            $selectedPeriod = $request->period;
        } elseif ($request->filled('month')) {
            $query->whereMonth('date', $request->month);
        }

        if ($request->filled('year')) {
            $query->whereYear('date', $request->year);
        }

        $voucherStats = [
            'total' => (clone $query)->count(),
            'receipts' => (clone $query)->where('type', 'receipt')->sum('amount'),
            'expenses' => (clone $query)->whereIn('type', ['payment', 'salary'])->sum('amount'),
            'transfers' => (clone $query)->where('type', 'transfer')->count(),
        ];

        $vouchers = $query->with(['fund', 'targetFund', 'student'])
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $centers = \App\Models\Center::all();
        $lockedPeriods = MonthlySettlement::where('status', 'approved')
            ->get(['center_id', 'year', 'month'])
            ->mapWithKeys(fn (MonthlySettlement $settlement) => [
                "{$settlement->center_id}-{$settlement->year}-{$settlement->month}" => true,
            ])
            ->all();

        return view('vouchers.index', compact('vouchers', 'centers', 'selectedPeriod', 'voucherStats', 'lockedPeriods'));
    }

    public function create()
    {
        $user = auth()->user();
        $center_id = $user->center_id ?? (\App\Models\Center::first()->id ?? null);
        
        if (!$center_id) {
            return redirect()->back()->with('error', 'عذراً لا يوجد مراكز مسجلة في النظام.');
        }

        $funds = Fund::where('center_id', $center_id)->get();
        $students = \App\Models\Student::where('center_id', $center_id)
            ->where('is_graduate', false)
            ->latest()
            ->get();
            
        return view('vouchers.create', compact('funds', 'students'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:receipt,payment,transfer,salary',
            'sub_type' => 'nullable|string|in:housing,deposit',
            'fund_id' => 'required|exists:funds,id',
            'target_fund_id' => 'required_if:type,transfer|nullable|exists:funds,id',
            'student_id' => 'nullable|exists:students,id',
            'amount' => 'required|numeric|min:0.01',
            'payee_or_payer' => 'required|string',
            'date' => 'required|date',
            'description' => 'required|string',
            'attachment' => 'nullable|file|mimes:jpg,png,pdf,webp|max:10240',
        ]);

        $fund = Fund::findOrFail($validated['fund_id']);
        $centerId = auth()->user()->center_id ?? (\App\Models\Center::first()->id ?? null);

        if ($this->isSettlementApproved($centerId, $validated['date'])) {
            return back()->withInput()->with('error', 'لا يمكن إصدار سند لشهر تم اعتماد تصفيته المالية.');
        }
      

        $prefix = [
            'receipt' => 'RV',
            'payment' => 'PV',
            'transfer' => 'TV',
            'salary' => 'SV',
        ][$validated['type']];

        DB::transaction(function () use ($validated, $request, $prefix, $centerId) {
            $path = null;
            if ($request->hasFile('attachment')) {
                $path = $request->file('attachment')->store('vouchers/' . date('Y/m'), 'public');
            }

            $voucher = Voucher::create(array_merge($validated, [
                'center_id' => $centerId,
                'created_by' => auth()->id(),
                'status' => 'approved',
                'voucher_number' => $prefix . '-' . date('Ymd') . '-' . mt_rand(100, 999),
                'attachment' => $path,
            ]));

            $this->updateBalances($voucher);
            $voucher->update(['approved_by' => auth()->id(), 'approved_at' => now()]);
        });

        return redirect()->route('vouchers.index')->with('success', 'تم تسجيل السند وتحديث الرصيد بنجاح.');
    }

    private function updateBalances(Voucher $voucher)
    {
        $fund = $voucher->fund;
        $amount = (float) $voucher->amount;

        // Treat salary as payment (decrement)
        if ($voucher->type == 'receipt') {
            $fund->increment('balance', $amount);
        } elseif (in_array($voucher->type, ['payment', 'salary'])) {
            $fund->decrement('balance', $amount);
        } elseif ($voucher->type == 'transfer') {
            $fund->decrement('balance', $amount);
            if ($voucher->targetFund) {
                $voucher->targetFund->increment('balance', $amount);
            }
        }
    }

    public function show(Voucher $voucher)
    {
        $voucher->load(['fund', 'targetFund', 'creator', 'approver', 'center']);
        return view('vouchers.show', compact('voucher'));
    }

    public function exportPdf(Voucher $voucher, \App\Services\PdfService $pdfService)
    {
        $voucher->load(['fund', 'targetFund', 'creator', 'approver', 'center']);

        $typeName = [
            'receipt' => 'سند_قبض',
            'payment' => 'سند_صرف',
            'transfer' => 'سند_تحويل',
            'salary' => 'مسير_رواتب',
        ][$voucher->type] ?? 'سند';

        $filename = $typeName . '_' . $voucher->voucher_number . '.pdf';

        return $pdfService->stream('pdf.vouchers.show', [
            'voucher' => $voucher,
            'number' => $voucher->voucher_number
        ], str_replace('_', ' ', $typeName), $filename, 'portrait');
    }
    public function destroy(Voucher $voucher)
    {
        if ($voucher->isLockedByApprovedSettlement()) {
            return back()->with('error', 'لا يمكن حذف سند ضمن شهر تم اعتماد تصفيته المالية.');
        }

        DB::transaction(function () use ($voucher) {
            $this->reverseBalances($voucher);
            $voucher->delete();
        });

        return redirect()->route('vouchers.index')->with('success', 'تم حذف السند وتعديل الرصيد بنجاح.');
    }

    private function reverseBalances(Voucher $voucher)
    {
        $fund = $voucher->fund;
        $amount = (float) $voucher->amount;

        if ($voucher->type == 'receipt') {
            $fund->decrement('balance', $amount);
        } elseif (in_array($voucher->type, ['payment', 'salary'])) {
            $fund->increment('balance', $amount);
        } elseif ($voucher->type == 'transfer') {
            $fund->increment('balance', $amount);
            if ($voucher->targetFund) {
                $voucher->targetFund->decrement('balance', $amount);
            }
        }
    }

    private function isSettlementApproved(?int $centerId, string $date): bool
    {
        if (!$centerId) {
            return false;
        }

        $period = \Carbon\Carbon::parse($date);

        return MonthlySettlement::where('center_id', $centerId)
            ->where('year', $period->year)
            ->where('month', $period->month)
            ->where('status', 'approved')
            ->exists();
    }
}
