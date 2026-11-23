<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Http\Requests\Reports\WeeklyReportRequest;
use App\Models\Reporter;
use App\Models\User;
use App\Models\WeeklyReport;
use App\Support\WeeklyReportWriter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WeeklyReportController extends Controller
{
    public function __construct(private WeeklyReportWriter $reports) {}

    public function create(Request $request, User $customer, ?string $kind = null): View
    {
        abort_unless($customer->visibleToReporter($this->reporter($request)), 404);

        $kind = in_array($kind, ['social', 'seo'], true) ? $kind : 'weekly';

        return view('reports.form', [
            'customer' => $customer,
            'report' => new WeeklyReport(['kind' => $kind]),
        ]);
    }

    public function store(WeeklyReportRequest $request, User $customer): RedirectResponse
    {
        abort_unless($customer->visibleToReporter($this->reporter($request)), 404);

        $report = $this->reports->save(
            $customer,
            $this->reporter($request),
            (string) $request->validated('intent'),
            $request->extracted(),
            $request->file('pdf'),
            (string) $request->validated('kind'),
        );

        return $this->saved($customer, $report);
    }

    public function show(Request $request, WeeklyReport $report): View
    {
        $report->load(['client', 'screenshots', 'reporter', 'revisions.reporter', 'flags']);
        $comparableReports = $report->comparableReports();

        return view('reports.show', [
            'report' => $report,
            'earlierReport' => $report->adjacentReport('earlier'),
            'laterReport' => $report->adjacentReport('later'),
            'comparableReports' => $comparableReports,
            'comparedReport' => $report->comparedWith($request->string('compare')->toString(), $comparableReports),
        ]);
    }

    public function edit(WeeklyReport $report): View
    {
        $report->load(['client', 'screenshots']);

        return view('reports.form', [
            'customer' => $report->client,
            'report' => $report,
        ]);
    }

    public function update(WeeklyReportRequest $request, WeeklyReport $report): RedirectResponse
    {
        $report = $this->reports->save(
            $report->client,
            $this->reporter($request),
            (string) $request->validated('intent'),
            $request->extracted(),
            $request->file('pdf'),
            (string) $request->validated('kind'),
            $report,
            $request->figures(),
        );

        return $this->saved($report->client, $report);
    }

    public function destroy(WeeklyReport $report): RedirectResponse
    {
        $customer = $report->client;
        $report->delete();

        return redirect()
            ->route('reports.clients.show', $customer)
            ->with('status', 'Report deleted. You can restore it from this client.');
    }

    public function restore(WeeklyReport $report): RedirectResponse
    {
        $report->restore();

        return redirect()
            ->route('reports.clients.show', $report->client)
            ->with('status', 'Report restored.');
    }

    public function forceDestroy(WeeklyReport $report): RedirectResponse
    {
        abort_unless($report->trashed(), 404);

        $customer = $report->client;
        $report->forceDelete();

        return redirect()
            ->route('reports.clients.show', $customer)
            ->with('status', 'Report permanently deleted.');
    }

    public function note(Request $request, WeeklyReport $report): RedirectResponse
    {
        $data = $request->validate([
            'staff_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $report->staff_note = $data['staff_note'] ?? null;
        $report->save();

        return back()->with('status', 'Private note saved. The client cannot see it.');
    }

    private function saved(User $customer, WeeklyReport $report): RedirectResponse
    {
        return redirect()
            ->route('reports.clients.show', $customer)
            ->with('status', $report->isPublished()
                ? 'Report published. The client can open it on their dashboard.'
                : 'Report saved as a draft.');
    }

    private function reporter(Request $request): Reporter
    {
        $reporter = $request->user('reporter');

        if (! $reporter instanceof Reporter) {
            abort(403);
        }

        return $reporter;
    }
}
