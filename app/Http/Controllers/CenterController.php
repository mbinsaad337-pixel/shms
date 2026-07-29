<?php

namespace App\Http\Controllers;

use App\Models\Center;
use Illuminate\Http\Request;

class CenterController extends Controller
{
    public function index()
    {
        $centers = Center::withCount(['students', 'rooms', 'staff'])->get();
        return view('centers.index', compact('centers'));
    }

    public function create()
    {
        return view('centers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'logo' => 'nullable|image|max:2048',
            'has_rent' => 'boolean',
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('centers', 'public');
        }

        Center::create($validated);

        return redirect()->route('centers.index')->with('success', 'تم إنشاء المركز بنجاح.');
    }

    public function edit(Center $center)
    {
        return view('centers.edit', compact('center'));
    }

    public function show(Center $center)
    {
        $center->loadCount(['students', 'rooms', 'staff']);
        return view('centers.show', compact('center'));
    }

    public function exportPdf(Center $center, \App\Services\PdfService $pdfService)
    {
        $center->loadCount(['students', 'rooms', 'staff']);

        return $pdfService->stream(
            'pdf.centers.statistics',
            compact('center'),
            'تقرير إحصائيات المركز - ' . $center->name,
            'center_statistics_' . $center->id . '.pdf'
        );
    }

    public function toggleStatus(Center $center)
    {
        $center->update(['is_active' => ! $center->is_active]);

        return redirect()->route('centers.show', $center)->with(
            'success',
            $center->is_active ? 'تم تفعيل المركز بنجاح.' : 'تم إيقاف المركز بنجاح.'
        );
    }

    public function update(Request $request, Center $center)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'logo' => 'nullable|image|max:2048',
            'is_active' => 'required|boolean',
            'has_rent' => 'boolean',
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('centers', 'public');
        }

        $center->update($validated);

        return redirect()->route('centers.index')->with('success', 'تم تحديث المركز بنجاح.');
    }
}
