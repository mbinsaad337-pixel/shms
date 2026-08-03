<?php

namespace App\Http\Controllers\Nutrition;

use App\Http\Controllers\Controller;
use App\Models\FoodBudget;
use App\Models\FoodBudgetLine;
use App\Models\FoodSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FoodBudgetController extends Controller
{
    public function index()
    {
        $centerId = auth()->user()->center_id;
        $budgets = FoodBudget::where('center_id', $centerId)
            ->withCount('lines')
            ->latest()
            ->paginate(15);
        return view('nutrition.budgets.index', compact('budgets'));
    }

    public function create()
    {
        $centerId = auth()->user()->center_id;
        $suppliers = \App\Models\FoodSupplier::where('center_id', $centerId)->where('is_active', true)->orderBy('name')->get();
        $latestBudget = FoodBudget::where('center_id', $centerId)->with('lines')->latest()->first();
        return view('nutrition.budgets.create', compact('suppliers', 'latestBudget'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020|max:2099',
            'title' => 'nullable|string|max:255',
            'days_count' => 'required|integer|min:1',
            'cost_per_student' => 'required|numeric|min:0',
            'daily_rate' => 'nullable|numeric|min:0',
            'last_payment_date' => 'nullable|date',
            'lines' => 'nullable|array',
            'lines.*.item_name' => 'required_with:lines.*|string',
            'lines.*.days' => 'nullable|integer|min:0',
            'lines.*.quantity' => 'nullable|numeric|min:0',
            'lines.*.unit_price' => 'nullable|numeric|min:0',
            'lines.*.supplier_name' => 'nullable|string',
        ]);

        $isNutritionManager = auth()->user()->hasRole('nutrition-manager');

        DB::transaction(function () use ($request, $isNutritionManager) {
            $centerId = auth()->user()->center_id;
            $totalAmount = 0;

            // Calculate total from lines
            if ($request->lines) {
                foreach ($request->lines as $line) {
                    $days = $line['days'] ?? 1;
                    $qty = $line['quantity'] ?? 1;
                    $price = $line['unit_price'] ?? 0;
                    $totalAmount += $days * $qty * $price;
                }
            }

            $subscribersCount = FoodSubscription::where('center_id', $centerId)
                ->where('status', 'active')->count();

            $budget = FoodBudget::create([
                'center_id' => $centerId,
                'month' => $request->month,
                'year' => $request->year,
                'title' => $request->title,
                'total_amount' => $totalAmount,
                'days_count' => $request->days_count,
                'daily_rate' => $request->daily_rate,
                'last_payment_date' => $request->last_payment_date,
                'subscribers_count' => $subscribersCount,
                'cost_per_student' => $request->cost_per_student,
                // تُرسل ميزانية مسؤول التغذية مباشرةً إلى مدير المركز للاعتماد.
                'status' => $isNutritionManager ? 'submitted' : 'draft',
                'created_by' => auth()->id(),
            ]);

            if ($request->lines) {
                foreach ($request->lines as $i => $line) {
                    if (empty($line['item_name']))
                        continue;
                    $days = $line['days'] ?? 1;
                    $qty = $line['quantity'] ?? 1;
                    $price = $line['unit_price'] ?? 0;
                    FoodBudgetLine::create([
                        'budget_id' => $budget->id,
                        'item_name' => $line['item_name'],
                        'days' => $line['days'] ?? null,
                        'quantity' => $line['quantity'] ?? null,
                        'unit_price' => $line['unit_price'] ?? null,
                        'total' => $days * $qty * $price,
                        'supplier_name' => $line['supplier_name'] ?? null,
                        'sort_order' => $i,
                    ]);
                }
            }
        });

        return redirect()->route('nutrition.budgets.index')
            ->with('success', 'تم إنشاء ميزانية التغذية بنجاح.');
    }

    public function show(FoodBudget $budget)
    {
        $budget->load(['lines', 'creator', 'approver']);
        return view('nutrition.budgets.show', compact('budget'));
    }

    public function submit(FoodBudget $budget)
    {
        if ($budget->status !== 'draft') {
            return back()->with('error', 'لا يمكن إرسال هذه الميزانية.');
        }
        $budget->update(['status' => 'submitted']);
        return back()->with('success', 'تم إرسال الميزانية لاعتماد مدير المركز.');
    }

    public function approve(FoodBudget $budget)
    {
        $user = auth()->user();

        if (!$user->hasRole(['center-manager', 'super-admin']) ||
            (!$user->hasRole('super-admin') && $user->center_id !== $budget->center_id)) {
            abort(403);
        }

        if ($budget->status !== 'submitted') {
            return back()->with('error', 'الميزانية غير في حالة الانتظار.');
        }
        $budget->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);
        return back()->with('success', 'تم اعتماد الميزانية بنجاح.');
    }

    public function reject(Request $request, FoodBudget $budget)
    {
        $user = auth()->user();

        if (!$user->hasRole(['center-manager', 'super-admin']) ||
            (!$user->hasRole('super-admin') && $user->center_id !== $budget->center_id)) {
            abort(403);
        }

        $request->validate(['rejection_reason' => 'required|string']);
        $budget->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);
        return back()->with('success', 'تم رفض الميزانية.');
    }

    public function edit(FoodBudget $budget)
    {
        if (!in_array($budget->status, ['draft', 'rejected'])) {
            return redirect()->route('nutrition.budgets.show', $budget)
                ->with('error', 'لا يمكن تعديل ميزانية تم إرسالها للاعتماد.');
        }

        $centerId = auth()->user()->center_id;
        $suppliers = \App\Models\FoodSupplier::where('center_id', $centerId)->where('is_active', true)->orderBy('name')->get();
        $budget->load('lines');
        return view('nutrition.budgets.edit', compact('budget', 'suppliers'));
    }

    public function update(Request $request, FoodBudget $budget)
    {
        if (!in_array($budget->status, ['draft', 'rejected'])) {
            return redirect()->route('nutrition.budgets.show', $budget)
                ->with('error', 'لا يمكن تعديل ميزانية تم إرسالها للاعتماد.');
        }

        $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020|max:2099',
            'title' => 'nullable|string|max:255',
            'days_count' => 'required|integer|min:1',
            'cost_per_student' => 'required|numeric|min:0',
            'daily_rate' => 'nullable|numeric|min:0',
            'last_payment_date' => 'nullable|date',
            'lines' => 'nullable|array',
            'lines.*.item_name' => 'required_with:lines.*|string',
            'lines.*.days' => 'nullable|integer|min:0',
            'lines.*.quantity' => 'nullable|numeric|min:0',
            'lines.*.unit_price' => 'nullable|numeric|min:0',
            'lines.*.supplier_name' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $budget) {
            $totalAmount = 0;

            if ($request->lines) {
                foreach ($request->lines as $line) {
                    $days = $line['days'] ?? 1;
                    $qty = $line['quantity'] ?? 1;
                    $price = $line['unit_price'] ?? 0;
                    $totalAmount += $days * $qty * $price;
                }
            }

            $budget->update([
                'month' => $request->month,
                'year' => $request->year,
                'title' => $request->title,
                'total_amount' => $totalAmount,
                'days_count' => $request->days_count,
                'daily_rate' => $request->daily_rate,
                'last_payment_date' => $request->last_payment_date,
                'cost_per_student' => $request->cost_per_student,
                'status' => auth()->user()->hasRole('nutrition-manager') ? 'submitted' : $budget->status,
                'rejection_reason' => null,
            ]);

            // Sync lines: Delete old and create new
            $budget->lines()->delete();

            if ($request->lines) {
                foreach ($request->lines as $i => $line) {
                    if (empty($line['item_name'])) continue;
                    $days = $line['days'] ?? 1;
                    $qty = $line['quantity'] ?? 1;
                    $price = $line['unit_price'] ?? 0;
                    FoodBudgetLine::create([
                        'budget_id' => $budget->id,
                        'item_name' => $line['item_name'],
                        'days' => $line['days'] ?? null,
                        'quantity' => $line['quantity'] ?? null,
                        'unit_price' => $line['unit_price'] ?? null,
                        'total' => $days * $qty * $price,
                        'supplier_name' => $line['supplier_name'] ?? null,
                        'sort_order' => $i,
                    ]);
                }
            }
        });

        return redirect()->route('nutrition.budgets.show', $budget)
            ->with('success', 'تم تحديث الميزانية بنجاح.');
    }

    public function destroy(FoodBudget $budget)
    {
        if (!in_array($budget->status, ['draft', 'rejected'])) {
            return back()->with('error', 'لا يمكن حذف ميزانية تم إرسالها للاعتماد أو تم اعتمادها بالفعل.');
        }

        if ($budget->status === 'approved') {
            return back()->with('error', 'لا يمكن حذف ميزانية معتمدة بالفعل.');
        }

        // Delete budget lines
        $budget->lines()->delete();

        // Soft delete the budget
        $budget->delete();

        return redirect()->route('nutrition.budgets.index')
            ->with('success', 'تم حذف الميزانية (العهدة) بنجاح.');
    }

    public function exportPdf(FoodBudget $budget, \App\Services\PdfService $pdfService)
    {
        $budget->load(['lines', 'creator', 'approver']);
        
        return $pdfService->stream('pdf.nutrition.budgets.show', [
            'budget' => $budget,
        ], 'ميزانية قسم التغذية', 'food_budget_' . $budget->id . '.pdf', 'portrait');
    }
}
