<?php

namespace App\Http\Controllers\Nutrition;

use App\Http\Controllers\Controller;
use App\Models\FoodPurchaseInvoice;
use App\Models\FoodInvoiceItem;
use App\Models\FoodSupplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FoodInvoiceController extends Controller
{
    public function index()
    {
        $centerId = auth()->user()->center_id;
        $invoices = FoodPurchaseInvoice::with('supplier')
            ->where('center_id', $centerId)
            ->latest()
            ->paginate(20);
        return view('nutrition.invoices.index', compact('invoices'));
    }

    public function create()
    {
        $centerId = auth()->user()->center_id;
        $suppliers = FoodSupplier::where('center_id', $centerId)->where('is_active', true)->orderBy('name')->get();
        
        $nextNumber = 'INV-' . date('Ym') . '-' . str_pad(
            FoodPurchaseInvoice::where('center_id', $centerId)->count() + 1,
            3,
            '0',
            STR_PAD_LEFT
        );

        // Get active budget for this month
        $currentBudget = \App\Models\FoodBudget::where('center_id', $centerId)
            ->where('month', date('n'))
            ->where('year', date('Y'))
            ->where('status', 'approved')
            ->with('lines')
            ->first();

        $budgetItems = [];
        if ($currentBudget) {
            foreach ($currentBudget->lines as $line) {
                // Calculate consumed amount for this item in this month
                $consumed = \App\Models\FoodInvoiceItem::whereHas('invoice', function($q) use ($centerId) {
                    $q->where('center_id', $centerId)
                        ->whereMonth('invoice_date', date('n'))
                        ->whereYear('invoice_date', date('Y'))
                        ->where('status', '!=', 'cancelled');
                })->where('item_name', $line->item_name)->sum('total');

                $budgetItems[] = [
                    'name' => $line->item_name,
                    'total_budget' => $line->total,
                    'consumed' => $consumed,
                    'remaining' => $line->total - $consumed
                ];
            }
        }

        return view('nutrition.invoices.create', compact('suppliers', 'nextNumber', 'budgetItems'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:food_suppliers,id',
            'invoice_number' => 'required|string',
            'invoice_date' => 'required|date',
            'payment_type' => 'required|in:cash,credit',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0.001',
            'items.*.unit_price' => 'required|numeric|min:0',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        DB::transaction(function () use ($request) {
            $centerId = auth()->user()->center_id;
            $totalAmount = 0;

            foreach ($request->items as $item) {
                $totalAmount += $item['quantity'] * $item['unit_price'];
            }

            $attachmentPath = null;
            if ($request->hasFile('attachment')) {
                $attachmentPath = $request->file('attachment')->store('nutrition/invoices', 'public');
            }

            $invoice = FoodPurchaseInvoice::create([
                'center_id' => $centerId,
                'supplier_id' => $request->supplier_id,
                'invoice_number' => $request->invoice_number,
                'invoice_date' => $request->invoice_date,
                'total_amount' => $totalAmount,
                'payment_type' => $request->payment_type,
                'status' => 'approved',
                'attachment' => $attachmentPath,
                'notes' => $request->notes,
                'created_by' => auth()->id(),
            ]);

            foreach ($request->items as $item) {
                FoodInvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'item_name' => $item['item_name'],
                    'quantity' => $item['quantity'],
                    'unit' => $item['unit'] ?? null,
                    'unit_price' => $item['unit_price'],
                    'total' => $item['quantity'] * $item['unit_price'],
                ]);
            }

            // Auto update supplier balance (handled by model booted but explicit call is fine too)
            $invoice->supplier->recalculateBalance();
        });

        return redirect()->route('nutrition.invoices.index')
            ->with('success', 'تم حفظ الفاتورة وتحديث حساب المورد تلقائياً.');
    }

    public function show(FoodPurchaseInvoice $invoice)
    {
        $invoice->load(['supplier', 'items', 'creator']);
        return view('nutrition.invoices.show', compact('invoice'));
    }

    public function exportPdf(FoodPurchaseInvoice $invoice, \App\Services\PdfService $pdfService)
    {
        $invoice->load(['supplier', 'items', 'creator', 'center']);
        return $pdfService->stream('pdf.nutrition.invoices.show', [
            'invoice' => $invoice,
        ], 'فاتورة مشتريات (تغذية)', 'food_invoice_' . $invoice->invoice_number . '.pdf', 'portrait');
    }

    public function cancel(FoodPurchaseInvoice $invoice)
    {
        if ($invoice->status === 'cancelled') {
            return back()->with('error', 'الفاتورة ملغاة مسبقاً.');
        }
        $invoice->update(['status' => 'cancelled']);
        $invoice->supplier->recalculateBalance();
        return back()->with('success', 'تم إلغاء الفاتورة وتحديث الرصيد.');
    }

    public function destroy(FoodPurchaseInvoice $invoice)
    {
        $user = auth()->user();
        if (!$user->hasRole('super-admin') && !$user->hasRole('executive-manager') && !$user->hasRole('nutrition-manager')) {
            abort(403, 'غير مصرح لك بحذف فواتير التغذية.');
        }
        if (!$user->hasRole('super-admin') && $user->center_id && $invoice->center_id !== $user->center_id) {
            abort(403, 'غير مصرح لك بالتعامل مع فواتير هذا المركز.');
        }

        $supplier = $invoice->supplier;

        // Delete invoice (using soft delete as per model)
        $invoice->delete();

        // Recalculate supplier balance
        $supplier->recalculateBalance();

        return redirect()->route('nutrition.invoices.index')
            ->with('success', 'تم حذف الفاتورة وتحديث رصيد المورد بنجاح.');
    }
}
