<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Center;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CenterManagerController extends Controller
{
    public function index()
    {
        $managers = User::role('center-manager')->with('center')->get();
        return view('admin.managers.index', compact('managers'));
    }

    public function create()
    {
        $centers = Center::where('is_active', true)->get();
        return view('admin.managers.create', compact('centers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'center_id' => 'required|exists:centers,id',
            'phone' => 'nullable|string',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'center_id' => $validated['center_id'],
            'phone' => $validated['phone'],
            'is_active' => true,
        ]);

        $user->assignRole('center-manager');

        return redirect()->route('managers.index')->with('success', 'تم إضافة مدير المركز بنجاح.');
    }

    public function edit(User $manager)
    {
        $centers = Center::where('is_active', true)->get();
        return view('admin.managers.edit', compact('manager', 'centers'));
    }

    public function update(Request $request, User $manager)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $manager->id,
            'center_id' => 'required|exists:centers,id',
            'phone' => 'nullable|string',
            'password' => 'nullable|min:8',
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'center_id' => $validated['center_id'],
            'phone' => $validated['phone'],
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $manager->update($data);

        return redirect()->route('managers.index')->with('success', 'تم تحديث بيانات المدير بنجاح.');
    }

    public function toggleStatus(User $manager)
    {
        $manager->update(['is_active' => !$manager->is_active]);
        $status = $manager->is_active ? 'تفعيل' : 'تعطيل';
        return back()->with('success', "تم {$status} حساب المدير بنجاح.");
    }
}
