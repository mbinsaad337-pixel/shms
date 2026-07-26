<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Services\ProgramService;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    public function __construct(private ProgramService $programService) {}

    public function index()
    {
        if (! auth()->user()->hasRole(['super-admin', 'center-manager'])) {
            abort(403);
        }

        $programs = Program::withCount('students')->get();

        return view('programs.index', compact('programs'));
    }

    public function create()
    {
        if (! auth()->user()->hasRole('super-admin')) {
            abort(403);
        }

        return view('programs.create');
    }

    public function store(Request $request)
    {
        if (! auth()->user()->hasRole('super-admin')) {
            abort(403);
        }

        $validated = $request->validate([
            'name'                   => 'required|string|max:100',
            'code'                   => 'required|string|max:50|unique:programs,code|alpha_dash',
            'description'            => 'nullable|string',
            'allows_activities'      => 'boolean',
            'allows_attendance'      => 'boolean',
            'allows_violations'      => 'boolean',
            'allows_evaluation'      => 'boolean',
            'allows_quran_circle'    => 'boolean',
            'allows_leaves'          => 'boolean',
            'requires_academic_data' => 'boolean',
            'nutrition_policy'       => 'required|in:mandatory,optional,none',
        ]);

        // تحويل checkboxes
        foreach (['allows_activities','allows_attendance','allows_violations',
                  'allows_evaluation','allows_quran_circle','allows_leaves',
                  'requires_academic_data'] as $flag) {
            $validated[$flag] = $request->boolean($flag);
        }

        Program::create($validated + ['is_active' => true]);

        $this->programService->clearCache();

        return redirect()->route('programs.index')->with('success', 'تم إنشاء البرنامج بنجاح.');
    }

    public function edit(Program $program)
    {
        if (! auth()->user()->hasRole('super-admin')) {
            abort(403);
        }

        return view('programs.edit', compact('program'));
    }

    public function update(Request $request, Program $program)
    {
        if (! auth()->user()->hasRole('super-admin')) {
            abort(403);
        }

        // منع تعديل البرامج الأساسية من الحذف
        $validated = $request->validate([
            'name'                   => 'required|string|max:100',
            'description'            => 'nullable|string',
            'allows_activities'      => 'boolean',
            'allows_attendance'      => 'boolean',
            'allows_violations'      => 'boolean',
            'allows_evaluation'      => 'boolean',
            'allows_quran_circle'    => 'boolean',
            'allows_leaves'          => 'boolean',
            'requires_academic_data' => 'boolean',
            'nutrition_policy'       => 'required|in:mandatory,optional,none',
            'is_active'              => 'boolean',
        ]);

        foreach (['allows_activities','allows_attendance','allows_violations',
                  'allows_evaluation','allows_quran_circle','allows_leaves',
                  'requires_academic_data','is_active'] as $flag) {
            $validated[$flag] = $request->boolean($flag);
        }

        $program->update($validated);

        $this->programService->clearCache($program->id);

        return redirect()->route('programs.index')->with('success', 'تم تحديث البرنامج بنجاح.');
    }
}
