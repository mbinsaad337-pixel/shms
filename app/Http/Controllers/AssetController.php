<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    public function index(Request $request)
    {
        $centerId = auth()->user()->center_id;
        $query = Asset::query();

        if (auth()->user()->hasRole('super-admin')) {
            if ($request->filled('center_id')) {
                $query->where('center_id', $request->center_id);
            }
        } elseif ($centerId) {
            $query->where('center_id', $centerId);
        }

        $assets = $query->get();
        $centers = \App\Models\Center::all();
        
        return view('assets.index', compact('assets', 'centers'));
    }

    public function exportListPdf(Request $request)
    {
        $centerId = auth()->user()->center_id;
        $query = Asset::query();
        $centerName = 'جميع المراكز';

        if (auth()->user()->hasRole('super-admin')) {
            if ($request->filled('center_id')) {
                $query->where('center_id', $request->center_id);
                $centerName = \App\Models\Center::find($request->center_id)->name;
            }
        } elseif ($centerId) {
            $query->where('center_id', $centerId);
            $centerName = auth()->user()->center->name;
        }

        $assets = $query->get();
        return view('assets.list-pdf', compact('assets', 'centerName'));
    }

    public function create()
    {
        return view('assets.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'type' => 'required|string',
            'category' => 'required|string',
            'code' => 'required|string|unique:assets,code',
            'value' => 'nullable|numeric',
            'status' => 'required|in:good,needs_maintenance,damaged,disposed',
            'photo' => 'nullable|image|max:2048',
            'notes' => 'nullable|string',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('assets', 'public');
        }

        Asset::create(array_merge($validated, [
            'center_id' => auth()->user()->center_id,
        ]));

        return redirect()->route('assets.index')->with('success', 'تم إضافة الأصل بنجاح');
    }

    public function edit(Asset $asset)
    {
        if (auth()->user()->center_id !== null && $asset->center_id != auth()->user()->center_id) {
            abort(403);
        }
        return view('assets.edit', compact('asset'));
    }

    public function update(Request $request, Asset $asset)
    {
        if (auth()->user()->center_id !== null && $asset->center_id != auth()->user()->center_id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string',
            'type' => 'required|string',
            'category' => 'required|string',
            'code' => 'required|string|unique:assets,code,' . $asset->id,
            'value' => 'nullable|numeric',
            'status' => 'required|in:good,needs_maintenance,damaged,disposed',
            'photo' => 'nullable|image|max:2048',
            'notes' => 'nullable|string',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('assets', 'public');
        }

        $asset->update($validated);

        return redirect()->route('assets.index')->with('success', 'تم تحديث بيانات الأصل بنجاح');
    }

    public function destroy(Asset $asset)
    {
        if (auth()->user()->center_id !== null && $asset->center_id != auth()->user()->center_id) {
            abort(403);
        }

        $asset->delete();
        return redirect()->route('assets.index')->with('success', 'تم حذف الأصل بنجاح');
    }
}
