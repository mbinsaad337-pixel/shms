<?php

namespace App\Http\Controllers;

use App\Models\Fund;
use Illuminate\Http\Request;

class FundController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $funds = Fund::query()
            ->when($user->center_id, fn($q) => $q->where('center_id', $user->center_id))
            ->when($request->filled('center_id') && $user->hasRole('super-admin'), fn($q) => $q->where('center_id', $request->center_id))
            ->when($request->filled('search'), fn($q) => $q->where('name', 'like', '%' . $request->search . '%'))
            ->with('center')
            ->get();

        $centers = collect();
        if ($user->hasRole('super-admin')) {
            $centers = \App\Models\Center::all();
        }

        return view('funds.index', compact('funds', 'centers'));
    }

    public function store(Request $request)
    {
        abort_if(!auth()->user()->hasRole('super-admin'), 403, 'غير مصرح لك بإدارة الصناديق.');

        $validated = $request->validate([
            'center_id' => 'required|exists:centers,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'balance' => 'required|numeric|min:0',
        ]);

        Fund::create(array_merge($validated, [
            'is_system' => false,
        ]));

        return back()->with('success', 'تم إنشاء الصندوق بنجاح.');
    }

    public function update(Request $request, Fund $fund)
    {
        abort_if(!auth()->user()->hasRole('super-admin'), 403, 'غير مصرح لك بإدارة الصناديق.');

        $validated = $request->validate([
            'center_id' => 'required|exists:centers,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'balance' => 'required|numeric|min:0',
        ]);

        $fund->update($validated);

        return back()->with('success', 'تم تحديث بيانات الصندوق بنجاح.');
    }

    public function destroy(Fund $fund)
    {
        abort_if(!auth()->user()->hasRole('super-admin'), 403, 'غير مصرح لك بإدارة الصناديق.');

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
