<?php

namespace App\Services;

use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as Pdf;
use Illuminate\Support\Carbon;

class PdfService
{
    /**
     * System name displayed in headers/footers.
     */
    protected string $systemName = 'نظام إدارة المراكز الطلابية';

    /**
     * System version displayed in footers.
     */
    protected string $systemVersion = 'Version 1.0';

    /**
     * Default page orientation.
     */
    protected string $orientation = 'portrait';

    /**
     * Default paper size.
     */
    protected string $paper = 'a4';

    /**
     * Generate and download a PDF report.
     *
     * @param  string  $view        Blade view path (e.g. 'pdf.reports.students')
     * @param  array   $data        Data to pass to the view
     * @param  string  $reportTitle Title of the report (e.g. 'تقرير الطلاب')
     * @param  string  $filename    Output filename (e.g. 'students-report-2026-07-21.pdf')
     * @param  string  $orientation 'portrait' or 'landscape'
     * @param  array   $filters     Applied filters for the report info section
     * @param  array   $stats       Summary stats cards (key => value)
     * @return \Illuminate\Http\Response
     */
    public function export(
        string $view,
        array  $data,
        string $reportTitle,
        string $filename,
        string $orientation = 'portrait',
        array  $filters = [],
        array  $stats = []
    ) {
        $user = auth()->user();

        // Merge shared meta into data
        $data = array_merge($data, [
            'reportTitle'   => $reportTitle,
            'systemName'    => $this->systemName,
            'systemVersion' => $this->systemVersion,
            'exportDate'    => Carbon::now()->format('Y-m-d'),
            'exportTime'    => Carbon::now()->format('H:i:s'),
            'exportUser'    => $user->name ?? 'النظام',
            'exportCenter'  => $user->center->name ?? '',
            'filters'       => $filters,
            'stats'         => $stats,
        ]);

        $pdf = Pdf::loadView($view, $data, [], [
            'format' => $this->paper,
            'orientation' => $orientation == 'portrait' ? 'P' : 'L'
        ]);

        return $pdf->download($filename);
    }

    /**
     * Stream (display in browser) a PDF report.
     */
    public function stream(
        string $view,
        array  $data,
        string $reportTitle,
        string $filename,
        string $orientation = 'portrait',
        array  $filters = [],
        array  $stats = []
    ) {
        $user = auth()->user();

        $data = array_merge($data, [
            'reportTitle'   => $reportTitle,
            'systemName'    => $this->systemName,
            'systemVersion' => $this->systemVersion,
            'exportDate'    => Carbon::now()->format('Y-m-d'),
            'exportTime'    => Carbon::now()->format('H:i:s'),
            'exportUser'    => $user->name ?? 'النظام',
            'exportCenter'  => $user->center->name ?? '',
            'filters'       => $filters,
            'stats'         => $stats,
        ]);

        $pdf = Pdf::loadView($view, $data, [], [
            'format' => $this->paper,
            'orientation' => $orientation == 'portrait' ? 'P' : 'L'
        ]);

        return $pdf->stream($filename);
    }

    /**
     * Generate a clean filename with date suffix.
     *
     * @param  string  $prefix  e.g. 'students-report'
     * @return string  e.g. 'students-report-2026-07-21.pdf'
     */
    public static function filename(string $prefix): string
    {
        return $prefix . '-' . date('Y-m-d') . '.pdf';
    }
}
