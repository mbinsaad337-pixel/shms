<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class CenterUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()->where('id', '!=', auth()->id());

        if (auth()->user()->hasRole('super-admin')) {
            // Super admin sees everyone except students
            $query->whereDoesntHave('roles', function ($q) {
                $q->where('name', 'student');
            });

            // Filter by center if provided
            if ($request->filled('center_id')) {
                $query->where('center_id', $request->center_id);
            }
        } else {
            // Other managers see users in their center
            $centerId = auth()->user()->center_id;
            $query->where('center_id', $centerId);
        }

        $users = $query->with('roles')->get();
        $centers = \App\Models\Center::all();

        return view('admin.users.index', compact('users', 'centers'));
    }

    public function exportListPdf(Request $request, \App\Services\PdfService $pdfService)
    {
        $query = User::query()->where('id', '!=', auth()->id());
        $user = auth()->user();
        $centerName = 'جميع المراكز';

        if ($user->hasRole('super-admin')) {
            $query->whereDoesntHave('roles', function ($q) {
                $q->where('name', 'student');
            });
            if ($request->filled('center_id')) {
                $query->where('center_id', $request->center_id);
                $centerName = \App\Models\Center::find($request->center_id)->name;
            }
        } else {
            $query->where('center_id', $user->center_id);
            $centerName = $user->center->name;
        }

        $users = $query->with('roles')->get();

        return $pdfService->stream('pdf.admin.users.list-pdf', [
            'data' => $users,
        ], 'تقرير مستخدمي النظام', 'users_list.pdf', 'landscape', ['المركز' => $centerName]);
    }

    public function create()
    {
        $roles = Role::whereIn('name', [
            'housing-manager',
            'financial-manager',
            'social-manager',
            'nutrition-manager',
            'inventory-manager',
            'transport-manager',
            'academic-supervisor',
            'cooperative-supervisor',
            'student-supervisor',
            'circle-teacher',
            'center-guard',
            'student'
        ])->get();

        $permissions = \Spatie\Permission\Models\Permission::all()->groupBy(function ($p) {
            return explode('-', $p->name)[1] ?? 'other';
        });

        return view('admin.users.create', compact('roles', 'permissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string',
            'bank_account_number' => 'nullable|string|max:255',
            'salary' => 'nullable|numeric|min:0',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|exists:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'bank_account_number' => $validated['bank_account_number'] ?? null,
            'salary' => $validated['salary'] ?? null,
            'password' => Hash::make($validated['password']),
            'center_id' => auth()->user()->center_id,
            'is_active' => true,
        ]);

        $user->assignRole($validated['role']);

        if (!empty($validated['permissions'])) {
            $user->givePermissionTo($validated['permissions']);
        }

        return redirect()->route('admin.users.index')->with('success', 'تم إضافة الموظف بنجاح');
    }

    public function edit(User $user)
    {
        if ($user->center_id !== auth()->user()->center_id)
            abort(403);

        $roles = Role::whereIn('name', [
            'housing-manager',
            'financial-manager',
            'social-manager',
            'nutrition-manager',
            'inventory-manager',
            'transport-manager',
            'academic-supervisor',
            'cooperative-supervisor',
            'student-supervisor',
            'circle-teacher',
            'center-guard',
            'student'
        ])->get();

        $permissions = \Spatie\Permission\Models\Permission::all()->groupBy(function ($p) {
            return explode('-', $p->name)[1] ?? 'other';
        });

        return view('admin.users.edit', compact('user', 'roles', 'permissions'));
    }

    public function update(Request $request, User $user)
    {
        if ($user->center_id !== auth()->user()->center_id)
            abort(403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'required|string',
            'bank_account_number' => 'nullable|string|max:255',
            'salary' => 'nullable|numeric|min:0',
            'role' => 'required|exists:roles,name',
            'password' => 'nullable|string|min:8|confirmed',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'bank_account_number' => $validated['bank_account_number'] ?? null,
            'salary' => $validated['salary'] ?? null,
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        $user->syncRoles([$validated['role']]);

        if (isset($validated['permissions'])) {
            $user->syncPermissions($validated['permissions']);
        } else {
            $user->syncPermissions([]);
        }

        return redirect()->route('admin.users.index')->with('success', 'تم تحديث بيانات الموظف بنجاح');
    }

    public function toggleStatus(User $user)
    {
        if ($user->center_id !== auth()->user()->center_id)
            abort(403);

        $user->is_active = !$user->is_active;
        $user->save();

        return back()->with('success', 'تم تحديث حالة الحساب بنجاح');
    }

    public function destroy(User $user)
    {
        if ($user->center_id !== auth()->user()->center_id)
            abort(403);

        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'تم حذف الموظف بنجاح');
    }
}
