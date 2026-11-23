<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\WeeklyReport;
use App\Support\WeeklyReportFigures;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WeeklyReportController extends Controller
{
    public function index(Request $request): View
    {
        return view('dashboard.reports.index', [
            'reports' => $request->user()
                ->weeklyReports()
                ->published()
                ->orderByDesc('period_start')
                ->orderByDesc('id')
                ->get(),
        ]);
    }

    public function show(Request $request, WeeklyReport $report): View
    {
        abort_unless($report->user_id === $request->user()->id && $report->isPublished(), 404);

        $report->load(['client', 'screenshots']);
        $comparableReports = $report->comparableReports();

        return view('dashboard.reports.show', [
            'report' => $report,
            'earlierReport' => $report->adjacentReport('earlier'),
            'laterReport' => $report->adjacentReport('later'),
            'comparableReports' => $comparableReports,
            'comparedReport' => $report->comparedWith($request->string('compare')->toString(), $comparableReports),
        ]);
    }

    public function flag(Request $request, WeeklyReport $report): RedirectResponse
    {
        abort_unless($report->user_id === $request->user()->id && $report->isPublished(), 404);

        $data = $request->validate([
            'path' => ['required', 'string', 'max:200'],
            'message' => ['required', 'string', 'max:500'],
        ]);

        $field = collect(WeeklyReportFigures::fields($report))->firstWhere('path', $data['path']);

        if (! is_array($field)) {
            return back()->withErrors(['path' => 'Choose a figure from this report.']);
        }

        $flag = $report->flags()->make([
            'path' => $field['path'],
            'label' => $field['label'],
            'figure' => $field['value'],
            'message' => $data['message'],
        ]);
        $flag->client()->associate($request->user());
        $flag->save();

        return back()->with('status', 'Your note was sent to Sunrise.');
    }
}
