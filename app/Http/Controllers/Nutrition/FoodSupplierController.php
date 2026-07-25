<?php

namespace App\Http\Controllers\Nutrition;

use App\Http\Controllers\Controller;
use App\Models\FoodSupplier;
use App\Models\FoodPurchaseInvoice;
use App\Models\FoodVoucher;
use Illuminate\Http\Request;

class FoodSupplierController extends Controller
{
    public function index()
    {
        $centerId = auth()->user()->center_id;
        $suppliers = FoodSupplier::where('center_id', $centerId)
            ->withCount('invoices')
            ->orderBy('name')
            ->paginate(20);
        return view('nutrition.suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('nutrition.suppliers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'tax_number' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        FoodSupplier::create([
            ...$request->only(['name', 'address', 'phone', 'email', 'tax_number', 'notes']),
            'center_id' => auth()->user()->center_id,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('nutrition.suppliers.index')
            ->with('success', 'تم إضافة المورد بنجاح.');
    }

    public function show(FoodSupplier $supplier)
    {
        $supplier->load(['invoices.items', 'vouchers']);
        // Build ledger (statement of account)
        $invoices = $supplier->invoices()->where('status', 'approved')->orderBy('invoice_date')->get();
        $vouchers = $supplier->vouchers()->where('status', 'active')->orderBy('voucher_date')->get();

        // Merge & sort by date
        $ledgerItems = [];
        foreach ($invoices as $invoice) {
            $ledgerItems[] = [
                'date' => $invoice->invoice_date,
                'type' => 'invoice',
                'reference' => $invoice->invoice_number,
                'description' => 'فاتورة مشتريات',
                'debit' => $invoice->total_amount,
                'credit' => 0,
                'item' => $invoice,
            ];
        }
        foreach ($vouchers as $voucher) {
            $ledgerItems[] = [
                'date' => $voucher->voucher_date,
                'type' => 'voucher',
                'reference' => $voucher->voucher_number,
                'description' => $voucher->type === 'payment' ? 'سند صرف' : 'سند قبض',
                'debit' => $voucher->type === 'receipt' ? $voucher->amount : 0,
                'credit' => $voucher->type === 'payment' ? $voucher->amount : 0,
                'item' => $voucher,
            ];
        }
        $ledger = collect($ledgerItems)->sortBy('date')->values();

        // Running balance
        $runningBalance = 0;
        $ledger = $ledger->map(function ($row) use (&$runningBalance) {
            $runningBalance += $row['debit'] - $row['credit'];
            $row['running_balance'] = $runningBalance;
            return $row;
        });

        return view('nutrition.suppliers.show', compact('supplier', 'ledger'));
    }

    public function edit(FoodSupplier $supplier)
    {
        return view('nutrition.suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, FoodSupplier $supplier)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
        ]);
        $supplier->update($request->only(['name', 'address', 'phone', 'email', 'tax_number', 'notes']));
        return redirect()->route('nutrition.suppliers.index')
            ->with('success', 'تم تحديث بيانات المورد.');
    }

    public function destroy(FoodSupplier $supplier)
    {
        if ($supplier->invoices()->exists() || $supplier->vouchers()->exists()) {
            return back()->with('error', 'لا يمكن حذف مورد لديه معاملات مالية.');
        }
        $supplier->delete();
        return redirect()->route('nutrition.suppliers.index')
            ->with('success', 'تم حذف المورد.');
    }

    public function exportPdf(FoodSupplier $supplier, \App\Services\PdfService $pdfService)
    {
        $supplier->load(['invoices.items', 'vouchers']);
        
        // Build ledger (statement of account)
        $invoices = $supplier->invoices()->where('status', 'approved')->orderBy('invoice_date')->get();
        $vouchers = $supplier->vouchers()->where('status', 'active')->orderBy('voucher_date')->get();

        $ledgerItems = [];
        foreach ($invoices as $invoice) {
            $ledgerItems[] = [
                'date' => $invoice->invoice_date,
                'type' => 'invoice',
                'reference' => $invoice->invoice_number,
                'description' => 'فاتورة مشتريات',
                'debit' => $invoice->total_amount,
                'credit' => 0,
                'item' => $invoice,
            ];
        }
        foreach ($vouchers as $voucher) {
            $ledgerItems[] = [
                'date' => $voucher->voucher_date,
                'type' => 'voucher',
                'reference' => $voucher->voucher_number,
                'description' => $voucher->type === 'payment' ? 'سند صرف' : 'سند قبض',
                'debit' => $voucher->type === 'receipt' ? $voucher->amount : 0,
                'credit' => $voucher->type === 'payment' ? $voucher->amount : 0,
                'item' => $voucher,
            ];
        }
        $ledger = collect($ledgerItems)->sortBy('date')->values();

        $runningBalance = 0;
        $ledger = $ledger->map(function ($row) use (&$runningBalance) {
            $runningBalance += $row['debit'] - $row['credit'];
            $row['running_balance'] = $runningBalance;
            return $row;
        });

        return $pdfService->stream('pdf.nutrition.suppliers.show', [
            'supplier' => $supplier,
            'ledger' => $ledger,
        ], 'كشف حساب مورد', 'supplier_statement_' . $supplier->id . '.pdf', 'portrait');
    }
}
