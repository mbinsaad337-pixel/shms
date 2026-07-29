<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Center;
use App\Models\CenterExpense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CenterExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = CenterExpense::with('center')->latest('due_date');

        // Filters
        if ($request->filled('center_id')) {
            $query->where('center_id', $request->center_id);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('month')) {
            $query->where('month', $request->month);
        }
        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        $expenses = $query->paginate(15)->withQueryString();
        $centers = Center::all(); // for the filter

        return view('admin.expenses.index', compact('expenses', 'centers'));
    }

    public function create()
    {
        $centers = Center::where('has_rent', true)->get();
        // Fallback: If no centers have 'has_rent', fetch all so it's not totally broken. 
        // We assume they update their centers soon.
        if ($centers->isEmpty()) {
            $centers = Center::all();
        }
        return view('admin.expenses.create', compact('centers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'center_id' => 'required|exists:centers,id',
            'type' => 'required|in:rent,water,electricity,internet,other',
            'amount' => 'required|numeric|min:0.01',
            'due_date' => 'required|date',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020|max:2100',
            'status' => 'required|in:pending,paid',
            'payment_date' => 'nullable|required_if:status,paid|date',
            'receipt' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
            'notes' => 'nullable|string',
        ], [
            'payment_date.required_if' => 'يجب إدخال تاريخ الدفع إذا كانت الحالة "تم الدفع".',
            'receipt.mimes' => 'يجب أن يكون المرفق صورة (JPG/PNG) أو ملف PDF.',
            'receipt.max' => 'الحد الأقصى لحجم المرفق 10 ميجابايت.',
        ]);

        $data = $request->only(['center_id', 'type', 'amount', 'due_date', 'status', 'payment_date', 'month', 'year', 'notes']);

        if ($request->hasFile('receipt')) {
            $file = $request->file('receipt');
            $data['receipt'] = $file->store('expenses_receipts', 'public');
            $data['receipt_type'] = in_array($file->getClientOriginalExtension(), ['pdf']) ? 'pdf' : 'image';
        }

        // Auto set payment_date if paid and missing
        if ($data['status'] === 'paid' && empty($data['payment_date'])) {
            $data['payment_date'] = now()->toDateString();
        }

        CenterExpense::create($data);

        return redirect()->route('center-expenses.index')->with('success', 'تم تسجيل المصروف بنجاح.');
    }

    public function edit(CenterExpense $centerExpense)
    {
        $centers = Center::all();
        return view('admin.expenses.edit', compact('centerExpense', 'centers'));
    }

    public function update(Request $request, CenterExpense $centerExpense)
    {
        $request->validate([
            'center_id' => 'required|exists:centers,id',
            'type' => 'required|in:rent,water,electricity,internet,other',
            'amount' => 'required|numeric|min:0.01',
            'due_date' => 'required|date',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020|max:2100',
            'status' => 'required|in:pending,paid',
            'payment_date' => 'nullable|required_if:status,paid|date',
            'receipt' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
            'notes' => 'nullable|string',
        ], [
            'payment_date.required_if' => 'يجب إدخال تاريخ الدفع إذا كانت الحالة "تم الدفع".',
        ]);

        $data = $request->only(['center_id', 'type', 'amount', 'due_date', 'status', 'payment_date', 'month', 'year', 'notes']);

        if ($request->hasFile('receipt')) {
            // Delete old file if exists
            if ($centerExpense->receipt) {
                Storage::disk('public')->delete($centerExpense->receipt);
            }
            $file = $request->file('receipt');
            $data['receipt'] = $file->store('expenses_receipts', 'public');
            $data['receipt_type'] = in_array($file->getClientOriginalExtension(), ['pdf']) ? 'pdf' : 'image';
        }

        // Auto set payment_date if paid and missing
        if ($data['status'] === 'paid' && empty($data['payment_date'])) {
            $data['payment_date'] = now()->toDateString();
        } elseif ($data['status'] === 'pending') {
            $data['payment_date'] = null; // Clear if reverted to pending
        }

        $centerExpense->update($data);

        return redirect()->route('center-expenses.index')->with('success', 'تم تحديث بيانات المصروف بنجاح.');
    }

    public function destroy(CenterExpense $centerExpense)
    {
        if ($centerExpense->receipt) {
            Storage::disk('public')->delete($centerExpense->receipt);
        }
        $centerExpense->delete();

        return redirect()->route('center-expenses.index')->with('success', 'تم حذف المصروف بنجاح.');
    }

    public function markAsPaid(Request $request, CenterExpense $centerExpense)
    {
        $request->validate([
            'payment_date' => 'required|date',
            'receipt' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
        ]);

        $data = [
            'status' => 'paid',
            'payment_date' => $request->payment_date,
        ];

        if ($request->hasFile('receipt')) {
            if ($centerExpense->receipt) {
                Storage::disk('public')->delete($centerExpense->receipt);
            }
            $file = $request->file('receipt');
            $data['receipt'] = $file->store('expenses_receipts', 'public');
            $data['receipt_type'] = in_array($file->getClientOriginalExtension(), ['pdf']) ? 'pdf' : 'image';
        }

        $centerExpense->update($data);

        return redirect()->back()->with('success', 'تم تسجيل المصروف كـ "مدفوع" بنجاح.');
    }
}
