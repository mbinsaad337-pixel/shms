<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Center;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class MediaOfficerController extends Controller
{
    public function index()
    {
        // Ensure role exists in database
        Role::firstOrCreate(['name' => 'media-officer', 'guard_name' => 'web']);

        $mediaOfficers = User::role('media-officer')
            ->with('center')
            ->latest()
            ->get();

        return view('admin.media_officers.index', compact('mediaOfficers'));
    }

    public function create()
    {
        $centers = Center::where('is_active', true)->get();
        return view('admin.media_officers.create', compact('centers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string',
            'password' => 'required|min:8',
            'center_id' => 'nullable|exists:centers,id',
        ]);

        $role = Role::firstOrCreate(['name' => 'media-officer', 'guard_name' => 'web']);
        $role->syncPermissions([
            'manage-news',
            'publish-news',
            'approve-news',
            'delete-news',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'center_id' => $validated['center_id'] ?? null,
            'is_active' => true,
        ]);

        $user->assignRole('media-officer');

        return redirect()->route('media-officers.index')->with('success', 'تم إنشاء حساب مسؤول الإعلام بنجاح وتخصيص الصلاحيات المستقلة له.');
    }

    public function edit(User $mediaOfficer)
    {
        $centers = Center::where('is_active', true)->get();
        return view('admin.media_officers.edit', compact('mediaOfficer', 'centers'));
    }

    public function update(Request $request, User $mediaOfficer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $mediaOfficer->id,
            'phone' => 'required|string',
            'center_id' => 'nullable|exists:centers,id',
            'password' => 'nullable|min:8',
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'center_id' => $validated['center_id'] ?? null,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $mediaOfficer->update($data);

        return redirect()->route('media-officers.index')->with('success', 'تم تحديث بيانات مسؤول الإعلام بنجاح.');
    }

    public function toggleStatus(User $mediaOfficer)
    {
        $mediaOfficer->update(['is_active' => !$mediaOfficer->is_active]);
        $status = $mediaOfficer->is_active ? 'تفعيل' : 'تعطيل';
        return back()->with('success', "تم {$status} حساب مسؤول الإعلام بنجاح.");
    }

    public function destroy(User $mediaOfficer)
    {
        $mediaOfficer->delete();
        return redirect()->route('media-officers.index')->with('success', 'تم حذف حساب مسؤول الإعلام.');
    }
}
