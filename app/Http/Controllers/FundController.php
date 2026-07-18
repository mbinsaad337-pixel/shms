<?php

namespace App\Http\Controllers;

use App\Models\Fund;
use Illuminate\Http\Request;

class FundController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $funds = Fund::query()
            ->when($user->center_id, fn($q) => $q->where('center_id', $user->center_id))
            ->get();

        return view('funds.index', compact('funds'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'balance' => 'required|numeric|min:0',
        ]);

        Fund::create(array_merge($validated, [
            'center_id' => auth()->user()->center_id,
            'is_system' => false,
        ]));

        return back()->with('success', 'تم إنشاء الصندوق بنجاح.');
    }

    public function update(Request $request, Fund $fund)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'balance' => 'required|numeric|min:0',
        ]);

        $fund->update($validated);

        return back()->with('success', 'تم تحديث بيانات الصندوق بنجاح.');
    }

    public function destroy(Fund $fund)
    {
        if ($fund->is_system) {
            return back()->with('error', 'لا يمكن حذف صناديق النظام الأساسية.');
        }

        // Use relation directly to check
        if ($fund->vouchers()->exists()) {
            return back()->with('error', 'لا يمكن حذف هذا الصندوق لوجود عمليات مالية مسجلة عليه.');
        }

        $fund->delete();

        return back()->with('success', 'تم حذف الصندوق بنجاح.');
    }
}
