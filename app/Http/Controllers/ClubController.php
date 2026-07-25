<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\ClubMember;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ClubController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Club::query()->withCount('members')->with('center');

        if ($user->center_id) {
            $query->where('center_id', $user->center_id);
        } elseif ($request->filled('center_id')) {
            $query->where('center_id', $request->center_id);
        }

        $clubs = $query->latest()->get();

        $centers = [];
        if (!$user->center_id) {
            $centers = \App\Models\Center::all();
        }

        return view('social.clubs.index', compact('clubs', 'centers'));
    }

    public function exportListPdf(Request $request, \App\Services\PdfService $pdfService)
    {
        $user = auth()->user();
        $query = Club::query()->withCount('members')->with('center');

        if ($user->center_id) {
            $query->where('center_id', $user->center_id);
        } elseif ($request->filled('center_id')) {
            $query->where('center_id', $request->center_id);
        }

        $clubs = $query->latest()->get();
        $centerName = $user->center_id ? $user->center->name : ($request->filled('center_id') ? \App\Models\Center::find($request->center_id)->name : 'جميع المراكز');

        return $pdfService->stream('pdf.social.clubs.list-pdf', [
            'data' => $clubs,
        ], 'تقرير الأندية الاجتماعية', 'clubs_list.pdf', 'portrait', ['المركز' => $centerName]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string',
            'description' => 'required|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('clubs/logos', 'public');
            $validated['logo'] = $path;
        }

        Club::create(array_merge($validated, [
            'center_id' => auth()->user()->center_id,
            'status' => 'active'
        ]));

        return back()->with('success', 'تم إنشاء النادي بنجاح.');
    }
    public function show(Club $club)
    {
        $club->load(['members.student']);
        $students = Student::where('center_id', auth()->user()->center_id)
            ->whereIn('status', ['registered', 'residing'])
            ->orderBy('name_ar')
            ->get();

        return view('social.clubs.show', compact('club', 'students'));
    }

    public function addMember(Request $request, Club $club)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'role' => 'required|string',
        ]);

        ClubMember::updateOrCreate(
            ['club_id' => $club->id, 'student_id' => $validated['student_id']],
            ['role' => $validated['role'], 'joined_at' => now()]
        );

        return back()->with('success', 'تم تحديث عضوية الطالب بنجاح.');
    }

    public function removeMember(Club $club, Student $student)
    {
        ClubMember::where('club_id', $club->id)
            ->where('student_id', $student->id)
            ->delete();

        return back()->with('success', 'تم إزالة العضو من النادي.');
    }

    public function edit(Club $club)
    {
        return view('social.clubs.edit', compact('club'));
    }

    public function update(Request $request, Club $club)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string',
            'description' => 'required|string',
            'status' => 'required|in:active,inactive',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            if ($club->logo && Storage::disk('public')->exists($club->logo)) {
                Storage::disk('public')->delete($club->logo);
            }
            $path = $request->file('logo')->store('clubs/logos', 'public');
            $validated['logo'] = $path;
        }

        $club->update($validated);

        return redirect()->route('clubs.index')->with('success', 'تم تحديث بيانات النادي.');
    }

    public function destroy(Club $club)
    {
        $club->members()->delete();
        $club->delete();

        return back()->with('success', 'تم حذف النادي بنجاح.');
    }

    public function exportPdf(Club $club, \App\Services\PdfService $pdfService)
    {
        $club->load(['members.student', 'center']);

        $filename = 'قائمة_أعضاء_' . str_replace(' ', '_', $club->name) . '.pdf';

        return $pdfService->stream('pdf.social.clubs.show-pdf', [
            'club' => $club,
        ], 'تقرير تفاصيل النادي', $filename, 'portrait');
    }
}
