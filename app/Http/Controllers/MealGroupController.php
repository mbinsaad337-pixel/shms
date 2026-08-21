<?php

namespace App\Http\Controllers;

use App\Models\MealGroup;
use App\Models\MealGroupMember;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MealGroupController extends Controller
{
    public function index()
    {
        $groups = MealGroup::where('center_id', auth()->user()->center_id)
            ->with(['leader', 'members.student'])
            ->get();
        return view('meals.groups.index', compact('groups'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'leader_student_id' => 'required|exists:students,id',
            'member_ids' => 'required|array|max:4', // Max 5 total including leader
            'member_ids.*' => 'exists:students,id',
        ]);

        DB::transaction(function () use ($validated) {
            $group = MealGroup::create([
                'leader_student_id' => $validated['leader_student_id'],
                'center_id' => auth()->user()->center_id,
            ]);

            foreach ($validated['member_ids'] as $studentId) {
                MealGroupMember::create([
                    'group_id' => $group->id,
                    'student_id' => $studentId,
                ]);
            }
        });

        return back()->with('success', 'تم إنشاء مجموعة التغذية بنجاح.');
    }

    public function destroy(MealGroup $group)
    {
        $user = auth()->user();
        if (!$user->hasRole('super-admin') && !$user->hasRole('executive-manager') && !$user->hasRole('nutrition-manager')) {
            if ($user->center_id && $group->center_id !== $user->center_id) {
                abort(403, 'غير مصرح لك بحذف مجموعات التغذية لهذا المركز.');
            }
        }

        $group->delete();
        return back()->with('success', 'تم حذف المجموعة.');
    }
}
