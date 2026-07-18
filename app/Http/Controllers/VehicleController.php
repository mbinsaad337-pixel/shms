<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index()
    {
        $centerId = auth()->user()->center_id;
        $vehicles = Vehicle::when($centerId, fn($q) => $q->where('center_id', $centerId))->with('student')->get();
        return view('vehicles.index', compact('vehicles'));
    }

    public function create()
    {
        $centerId = auth()->user()->center_id;
        $students = \App\Models\Student::where('center_id', $centerId)->get();
        return view('vehicles.create', compact('students'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'type' => 'required|string',
            'model' => 'required|string',
            'plate_number' => 'required|unique:vehicles,plate_number',
            'color' => 'nullable|string',
            'document_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('document_photo')) {
            $validated['document_photo'] = $request->file('document_photo')->store('vehicles', 'public');
        }

        Vehicle::create(array_merge($validated, [
            'center_id' => auth()->user()->center_id
        ]));

        return redirect()->route('vehicles.index')->with('success', 'تم تسجيل المركبة بنجاح');
    }

    public function edit(Vehicle $vehicle)
    {
        if ($vehicle->center_id !== auth()->user()->center_id)
            abort(403);
        $students = \App\Models\Student::where('center_id', auth()->user()->center_id)->get();
        return view('vehicles.edit', compact('vehicle', 'students'));
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        if ($vehicle->center_id !== auth()->user()->center_id)
            abort(403);

        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'type' => 'required|string',
            'model' => 'required|string',
            'plate_number' => 'required|unique:vehicles,plate_number,' . $vehicle->id,
            'color' => 'nullable|string',
            'document_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('document_photo')) {
            // Delete old photo if needed
            if ($vehicle->document_photo) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($vehicle->document_photo);
            }
            $validated['document_photo'] = $request->file('document_photo')->store('vehicles', 'public');
        }

        $vehicle->update($validated);

        return redirect()->route('vehicles.index')->with('success', 'تم تحديث بيانات المركبة بنجاح');
    }

    public function destroy(Vehicle $vehicle)
    {
        if ($vehicle->center_id !== auth()->user()->center_id)
            abort(403);
        $vehicle->delete();
        return redirect()->route('vehicles.index')->with('success', 'تم حذف المركبة بنجاح');
    }
}
