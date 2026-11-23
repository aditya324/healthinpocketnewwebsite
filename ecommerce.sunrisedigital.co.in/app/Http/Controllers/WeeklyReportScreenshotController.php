<?php

namespace App\Http\Controllers;

use App\Models\Reporter;
use App\Models\User;
use App\Models\WeeklyReportScreenshot;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class WeeklyReportScreenshotController extends Controller
{
    public function __invoke(WeeklyReportScreenshot $screenshot): BinaryFileResponse
    {
        $report = $screenshot->report;
        $reporter = Auth::guard('reporter')->user();
        $customer = Auth::guard('web')->user();

        abort_unless(
            $report !== null && $report->visibleTo(
                $customer instanceof User ? $customer : null,
                $reporter instanceof Reporter ? $reporter : null,
            ),
            404,
        );

        abort_unless(Storage::disk('local')->exists($screenshot->path), 404);

        return response()->file(Storage::disk('local')->path($screenshot->path), [
            'Content-Disposition' => 'inline',
            'Cache-Control' => 'private, no-store',
        ]);
    }
}
