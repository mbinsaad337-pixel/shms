<?php

namespace App\Http\Controllers\Nutrition;

use App\Http\Controllers\Controller;
use App\Models\FoodVoucher;
use App\Models\FoodSupplier;
use App\Models\FoodSubscription;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FoodVoucherController extends Controller
{
    public function index()
    {
        $centerId = auth()->user()->center_id;
        $vouchers = FoodVoucher::with(['supplier', 'student'])
            ->where('center_id', $centerId)
            ->latest()
            ->paginate(20);
        return view('nutrition.vouchers.index', compact('vouchers'));
    }

    public function create()
    {
        $centerId = auth()->user()->center_id;
        $suppliers = FoodSupplier::where('center_id', $centerId)->where('is_active', true)->orderBy('name')->get();
        $students = Student::where('center_id', $centerId)
            ->whereHas('foodSubscriptions', fn($q) => $q->where('status', 'active'))
            ->orderBy('name_ar')->get(['id', 'name_ar', 'university_id']);

        $nextNumber = 'FV-' . date('Ym') . '-' . str_pad(
            FoodVoucher::where('center_id', $centerId)->count() + 1,
            3,
            '0',
            STR_PAD_LEFT
        );

        return view('nutrition.vouchers.create', compact('suppliers', 'students', 'nextNumber'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:payment,receipt',
            'voucher_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string',
            'supplier_id' => 'nullable|exists:food_suppliers,id',
            'student_id' => 'nullable|exists:students,id',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $centerId = auth()->user()->center_id;

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('nutrition/vouchers', 'public');
        }

        $nextNumber = 'FV-' . date('Ym') . '-' . str_pad(
            FoodVoucher::where('center_id', $centerId)->count() + 1,
            3,
            '0',
            STR_PAD_LEFT
        );

        $voucher = FoodVoucher::create([
            'center_id' => $centerId,
            'voucher_number' => $nextNumber,
            'type' => $request->type,
            'voucher_date' => $request->voucher_date,
            'supplier_id' => $request->supplier_id,
            'student_id' => $request->student_id,
            'amount' => $request->amount,
            'description' => $request->description,
            'attachment' => $attachmentPath,
            'status' => 'active',
            'created_by' => auth()->id(),
        ]);

        // If receipt from student → update their subscription paid amount
        if ($request->type === 'receipt' && $request->student_id) {
            $subscription = FoodSubscription::where('student_id', $request->student_id)
                ->where('status', 'active')->first();
            if ($subscription) {
                $subscription->increment('total_paid', $request->amount);
            }
        }

        // Update supplier balance if linked
        if ($voucher->supplier_id) {
            $voucher->supplier->recalculateBalance();
        }

        return redirect()->route('nutrition.vouchers.index')
            ->with('success', 'تم حفظ السند بنجاح.');
    }

    public function show(FoodVoucher $voucher)
    {
        $voucher->load(['supplier', 'student', 'creator']);
        return view('nutrition.vouchers.show', compact('voucher'));
    }

    public function exportPdf(FoodVoucher $voucher, \App\Services\PdfService $pdfService)
    {
        $voucher->load(['supplier', 'student', 'creator', 'center']);
        return $pdfService->stream('pdf.nutrition.vouchers.show', [
            'voucher' => $voucher,
        ], 'سند مالي (تغذية)', 'food_voucher_' . $voucher->voucher_number . '.pdf', 'portrait');
    }

    public function cancel(FoodVoucher $voucher)
    {
        if ($voucher->status === 'cancelled') {
            return back()->with('error', 'السند ملغى مسبقاً.');
        }
        $voucher->update(['status' => 'cancelled']);
        if ($voucher->supplier_id) {
            $voucher->supplier->recalculateBalance();
        }
        return back()->with('success', 'تم إلغاء السند.');
    }

    public function destroy(FoodVoucher $voucher)
    {
        // If receipt from student → update subscription balance backwards
        if ($voucher->type === 'receipt' && $voucher->student_id && $voucher->status === 'active') {
            $subscription = FoodSubscription::where('student_id', $voucher->student_id)
                ->where('status', 'active')->first();
            if ($subscription) {
                $subscription->decrement('total_paid', (float) $voucher->amount);
            }
        }

        $voucher->delete();
        // Recalculation for suppliers is handled in model booted()

        return redirect()->route('nutrition.vouchers.index')
            ->with('success', 'تم حذف السند بنجاح وتحديث الأرصدة المرتبطة.');
    }
}
