<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MonthlyBudget;
use App\Models\BudgetItem;
use App\Models\Fund;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MonthlyBudgetController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = MonthlyBudget::query();

        if ($user->hasRole('super-admin')) {
            if ($request->filled('center_id')) {
                $query->where('center_id', $request->center_id);
            }
        } elseif ($user->center_id) {
            $query->where('center_id', $user->center_id);
        } else {
            // المدراء العامون والتنفيذيون يرون الموازنات التي تم تأكيدها من قبل مدير المركز
            if (!$user->can('confirm-budgets')) {
                $query->whereIn('status', ['confirmed', 'approved', 'rejected']);
            }
        }

        $budgets = $query->with(['items.fund', 'center'])
            ->latest()
            ->paginate(15);

        $centers = \App\Models\Center::all();

        return view('budgets.index', compact('budgets', 'centers'));
    }

    public function create()
    {
        $funds = Fund::where('center_id', auth()->user()->center_id)->get();
        return view('budgets.create', compact('funds'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer',
            'items' => 'required|array',
            'items.*.fund_id' => 'required|exists:funds,id',
            'items.*.amount' => 'required|numeric|min:0',
            'items.*.attachment_pdf' => 'nullable|file|mimes:pdf|max:5120', // Max 5MB
        ]);

        DB::transaction(function () use ($validated, $request) {
            $budget = MonthlyBudget::create([
                'center_id' => auth()->user()->center_id,
                'month' => $validated['month'],
                'year' => $validated['year'],
                'total_amount' => collect($validated['items'])->sum('amount'),
                'status' => 'submitted',
                'submitted_by' => auth()->id(),
            ]);

            foreach ($validated['items'] as $index => $item) {
                $attachmentPath = null;
                if ($request->hasFile("items.{$index}.attachment_pdf")) {
                    $attachmentPath = $request->file("items.{$index}.attachment_pdf")->store('budgets/attachments', 'public');
                }

                BudgetItem::create([
                    'monthly_budget_id' => $budget->id,
                    'fund_id' => $item['fund_id'],
                    'requested_amount' => $item['amount'],
                    'attachment_pdf' => $attachmentPath,
                ]);
            }
        });

        return redirect()->route('budgets.index')->with('success', 'تم إرسال طلب الموازنة لمدير المركز للمراجعة.');
    }

    public function show(MonthlyBudget $budget)
    {
        $budget->load(['items.fund', 'submitter', 'approver', 'center']);
        return view('budgets.show', compact('budget'));
    }

    public function exportPdf(MonthlyBudget $budget, \App\Services\PdfService $pdfService)
    {
        $budget->load(['items.fund', 'submitter', 'approver', 'center']);

        $filename = 'عهدة_' . $budget->center->name . '_' . $budget->month . '_' . $budget->year . '.pdf';

        return $pdfService->stream('pdf.budgets.show', [
            'budget' => $budget,
        ], 'طلب عهدة', $filename, 'portrait');
    }

    public function confirm(MonthlyBudget $budget)
    {
        if (!auth()->user()->can('confirm-budgets')) {
            abort(403);
        }

        if ($budget->status !== 'submitted') {
            return back()->with('error', 'هذه الموازنة ليست في حالة تتطلب التأكيد.');
        }

        $budget->update(['status' => 'confirmed']);

        return redirect()->route('budgets.index')->with('success', 'تم تأكيد الموازنة وإرسالها للمدير العام للاعتماد النهائي.');
    }

    public function reject(MonthlyBudget $budget)
    {
        if (!auth()->user()->can('confirm-budgets') && !auth()->user()->can('approve-budgets')) {
            abort(403);
        }

        if (in_array($budget->status, ['approved', 'rejected'])) {
            return back()->with('error', 'لا يمكن رفض موازنة تم اعتمادها أو رفضها مسبقاً.');
        }

        $budget->update(['status' => 'rejected']);

        return redirect()->route('budgets.index')->with('success', 'تم رفض طلب الموازنة.');
    }

    public function approve(MonthlyBudget $budget)
    {
        if (!auth()->user()->can('approve-budgets')) {
            abort(403);
        }

        if ($budget->status !== 'confirmed') {
            return back()->with('error', 'لا يمكن اعتماد موازنة لم يتم تأكيدها من قبل مدير المركز.');
        }

        DB::transaction(function () use ($budget) {
            $budget->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            foreach ($budget->items as $item) {
                $item->fund->increment('balance', (float) $item->requested_amount);
                $item->update(['approved_amount' => $item->requested_amount]);
            }
        });

        return redirect()->route('budgets.index')->with('success', 'تم الاعتماد النهائي وتحديث أرصدة الصناديق.');
    }

    public function destroy(MonthlyBudget $budget)
    {
        if (!auth()->user()->can('manage-budgets') && !auth()->user()->hasRole('super-admin')) {
            abort(403);
        }

        DB::transaction(function () use ($budget) {
            // Reverse fund balance if it was approved
            if ($budget->status === 'approved') {
                foreach ($budget->items as $item) {
                    if ($item->fund) {
                        $item->fund->decrement('balance', (float) $item->requested_amount);
                    }
                }
            }

            $budget->items()->delete();
            $budget->delete();
        });

        return redirect()->route('budgets.index')->with('success', 'تم حذف الموازنة بنجاح ' . ($budget->status === 'approved' ? 'وتم عكس المبالغ من أرصدة الصناديق.' : '.'));
    }
}
