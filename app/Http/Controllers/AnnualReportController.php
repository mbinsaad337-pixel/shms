<?php

namespace App\Http\Controllers;

use App\Models\AnnualReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AnnualReportController extends Controller
{
    public function index()
    {
        $query = AnnualReport::query();
        if (!auth()->user()->hasRole('super-admin')) {
            $query->where('center_id', auth()->user()->center_id);
        }

        $reports = $query->orderByDesc('year')
            ->orderByDesc('created_at')
            ->get();

        return view('annual-reports.index', compact('reports'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'     => 'required|string|max:255',
            'year'      => 'required|integer|min:2000|max:' . (date('Y') + 1),
            'file'      => 'required|file|max:20480|mimes:pdf,doc,docx,xls,xlsx',
        ]);

        $file = $request->file('file');
        $path = $file->store('annual-reports', 'public');

        AnnualReport::create([
            'center_id' => auth()->user()->center_id,
            'user_id'   => auth()->id(),
            'title'     => $validated['title'],
            'year'      => $validated['year'],
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
        ]);

        return redirect()->route('annual-reports.index')->with('success', 'تم رفع التقرير بنجاح');
    }

    public function download(AnnualReport $annualReport)
    {
        $this->authorizeReport($annualReport);

        return Storage::disk('public')->download(
            $annualReport->file_path,
            $annualReport->file_name
        );
    }

    public function destroy(AnnualReport $annualReport)
    {
        $this->authorizeReport($annualReport);

        Storage::disk('public')->delete($annualReport->file_path);
        $annualReport->delete();

        return redirect()->route('annual-reports.index')->with('success', 'تم حذف التقرير بنجاح');
    }

    private function authorizeReport(AnnualReport $report): void
    {
        if (auth()->user()->hasRole('super-admin')) {
            return;
        }
        if ($report->center_id !== auth()->user()->center_id) {
            abort(403);
        }
    }
}
