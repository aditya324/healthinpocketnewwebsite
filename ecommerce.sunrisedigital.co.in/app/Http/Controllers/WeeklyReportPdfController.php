<?php

namespace App\Http\Controllers;

use App\Models\Reporter;
use App\Models\User;
use App\Models\WeeklyReport;
use App\Support\WeeklyReportFigures;
use App\Support\WeeklyReportHighlights;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class WeeklyReportPdfController extends Controller
{
    public function __invoke(WeeklyReport $report): BinaryFileResponse
    {
        $reporter = Auth::guard('reporter')->user();
        $customer = Auth::guard('web')->user();

        abort_unless(
            filled($report->pdf_path) && $report->visibleTo(
                $customer instanceof User ? $customer : null,
                $reporter instanceof Reporter ? $reporter : null,
            ),
            404,
        );

        abort_unless(Storage::disk('local')->exists($report->pdf_path), 404);

        $filename = $this->filename($report->pdf_original_name);

        return response()->file(Storage::disk('local')->path($report->pdf_path), [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$filename.'"',
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function summary(WeeklyReport $report): Response
    {
        $reporter = Auth::guard('reporter')->user();
        $customer = Auth::guard('web')->user();

        abort_unless($report->visibleTo(
            $customer instanceof User ? $customer : null,
            $reporter instanceof Reporter ? $reporter : null,
        ), 404);

        $fields = WeeklyReportFigures::fields($report);
        $compared = ($report->isSocial() || $report->isSeo() || $report->kind === 'monthly')
            ? $report->adjacentReport('earlier')
            : null;

        return Pdf::loadView('pdf.report-summary', [
            'report' => $report,
            'highlights' => WeeklyReportHighlights::for($report, $compared),
            'groups' => collect($fields)->groupBy('group'),
        ])->download($report->kind.'-'.$report->period_start?->format('Y-m-d').'.pdf');
    }

    private function filename(?string $name): string
    {
        $name = trim((string) $name);
        $name = str_replace(['"', "\r", "\n"], '', $name);
        $name = basename($name);

        if ($name === '' || $name === '.' || $name === '..') {
            return 'weekly-report.pdf';
        }

        return str_ends_with(strtolower($name), '.pdf') ? $name : $name.'.pdf';
    }
}
