<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use App\Models\Commitment;
use App\Models\Leave;
use App\Models\Penalty;
use App\Models\Student;
use App\Models\Violation;
use Illuminate\Http\Request;

class AdministrativeController extends Controller
{
    /**
     * The unified administrative actions hub page.
     * All five sections (commitments, absences, leaves, violations, penalties) are shown as tabs.
     */
    public function index(Request $request)
    {
        $user  = auth()->user();
        $tab   = $request->get('tab', 'commitments');

        $students = Student::when($user->center_id, fn($q) => $q->where('center_id', $user->center_id))
            ->whereIn('status', ['registered', 'residing'])
            ->get(['id', 'name_ar', 'student_number']);

        // ── Commitments ──────────────────────────────────────
        $commitmentsQuery = Commitment::query()
            ->whereHas('student', fn($q) => $q->when($user->center_id, fn($sq) => $sq->where('center_id', $user->center_id)))
            ->with(['student', 'violation']);

        if ($request->filled('c_student_id'))  $commitmentsQuery->where('student_id', $request->c_student_id);
        if ($request->filled('c_date_from'))   $commitmentsQuery->whereDate('date', '>=', $request->c_date_from);
        if ($request->filled('c_date_to'))     $commitmentsQuery->whereDate('date', '<=', $request->c_date_to);
        if ($request->filled('c_status'))      $commitmentsQuery->where('status', $request->c_status);

        $commitments = $commitmentsQuery->latest()->paginate(15, ['*'], 'c_page')->withQueryString();

        // ── Absences ─────────────────────────────────────────
        $absencesQuery = Absence::query()
            ->whereHas('student', fn($q) => $q->when($user->center_id, fn($sq) => $sq->where('center_id', $user->center_id)))
            ->with(['student', 'recorder']);

        if ($request->filled('a_student_id'))   $absencesQuery->where('student_id', $request->a_student_id);
        if ($request->filled('a_date_from'))    $absencesQuery->whereDate('date', '>=', $request->a_date_from);
        if ($request->filled('a_date_to'))      $absencesQuery->whereDate('date', '<=', $request->a_date_to);
        if ($request->filled('a_has_excuse'))   $absencesQuery->where('has_excuse', $request->a_has_excuse == '1');
        if ($request->filled('a_type'))         $absencesQuery->where('absence_type', $request->a_type);

        $absences = $absencesQuery->latest()->paginate(15, ['*'], 'a_page')->withQueryString();

        // ── Leaves (Istizhan) ─────────────────────────────────
        $leavesQuery = Leave::query()
            ->whereHas('student', fn($q) => $q->when($user->center_id, fn($sq) => $sq->where('center_id', $user->center_id)))
            ->with(['student', 'approvedBy', 'violation']);

        $pendingLeavesCount = (clone $leavesQuery)->where('status', 'pending')->count();

        if ($request->filled('l_student_id'))  $leavesQuery->where('student_id', $request->l_student_id);
        if ($request->filled('l_type'))        $leavesQuery->where('type', $request->l_type);
        if ($request->filled('l_status'))      $leavesQuery->where('status', $request->l_status);
        if ($request->filled('l_date_from'))   $leavesQuery->whereDate('departure_date', '>=', $request->l_date_from);
        if ($request->filled('l_date_to'))     $leavesQuery->whereDate('departure_date', '<=', $request->l_date_to);

        $leaves = $leavesQuery->latest()->paginate(15, ['*'], 'l_page')->withQueryString();

        // ── Violations ────────────────────────────────────────
        $violationsQuery = Violation::query()
            ->when($user->center_id, fn($q) => $q->where('center_id', $user->center_id))
            ->with(['student', 'recordedBy']);

        if ($request->filled('v_student_id')) {
            $violationsQuery->where('student_id', $request->v_student_id);
        }
        if ($request->filled('v_date_from'))  $violationsQuery->whereDate('violation_date', '>=', $request->v_date_from);
        if ($request->filled('v_date_to'))    $violationsQuery->whereDate('violation_date', '<=', $request->v_date_to);
        if ($request->filled('v_type'))       $violationsQuery->where('type', $request->v_type);

        $violations = $violationsQuery->latest()->paginate(15, ['*'], 'v_page')->withQueryString();

        $violationTypes = Violation::query()
            ->when($user->center_id, fn($q) => $q->where('center_id', $user->center_id))
            ->distinct()->pluck('type');

        // ── Penalties ─────────────────────────────────────────
        $penaltiesQuery = Penalty::query()
            ->whereHas('student', fn($q) => $q->when($user->center_id, fn($sq) => $sq->where('center_id', $user->center_id)))
            ->with(['student', 'violation', 'appliedBy']);

        if ($request->filled('p_student_id'))  $penaltiesQuery->whereHas('student', fn($q) => $q->where('id', $request->p_student_id));
        if ($request->filled('p_date_from'))   $penaltiesQuery->whereDate('start_date', '>=', $request->p_date_from);
        if ($request->filled('p_date_to'))     $penaltiesQuery->whereDate('start_date', '<=', $request->p_date_to);

        $penalties = $penaltiesQuery->latest()->paginate(15, ['*'], 'p_page')->withQueryString();

        // ── Violations for form dropdowns ─────────────────────
        $violationsForForm = Violation::when($user->center_id, fn($q) => $q->where('center_id', $user->center_id))
            ->with('student:id,name_ar')
            ->latest()->take(50)->get();

        $center = $user->center;

        return view('administrative.index', compact(
            'tab',
            'students',
            'commitments',
            'absences',
            'leaves',
            'violations',
            'violationTypes',
            'violationsForForm',
            'penalties',
            'pendingLeavesCount',
            'center'
        ));
    }
}
