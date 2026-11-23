<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Reporter;
use App\Models\User;
use App\Support\AdminFilter;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function index(Request $request): View
    {
        $filters = [
            'q' => AdminFilter::text($request, 'q'),
        ];

        $reporter = $this->reporter($request);
        $clients = User::query()->forReporter($reporter)->withCount('weeklyReports');

        if ($filters['q'] !== '') {
            $term = AdminFilter::like($filters['q']);
            $clients->where(function ($query) use ($term): void {
                $query->where('name', 'like', $term)->orWhere('email', 'like', $term);
            });
        }

        $clients = $clients->orderBy('name')->orderBy('id')->get();

        return view('reports.clients.index', [
            'clients' => $clients,
            'filters' => $filters,
            'filtering' => AdminFilter::active($filters),
            'missing' => $this->missing($reporter),
        ]);
    }

    public function show(Request $request, User $customer): View
    {
        $this->visible($request, $customer);

        return view('reports.clients.show', [
            'customer' => $customer,
            'reports' => $customer->weeklyReports()
                ->with('reporter')
                ->orderByDesc('period_start')
                ->orderByDesc('id')
                ->get(),
            'deletedReports' => $customer->weeklyReports()
                ->onlyTrashed()
                ->orderByDesc('deleted_at')
                ->get(),
        ]);
    }

    /**
     * @return array{weekly: Collection<int, User>, social: Collection<int, User>, seo: Collection<int, User>}
     */
    private function missing(Reporter $reporter): array
    {
        $weekStart = now()->startOfWeek(Carbon::SUNDAY);
        $weekEnd = $weekStart->copy()->addDays(6);
        $monthStart = now()->startOfMonth()->toDateString();

        $without = function (callable $constraint) use ($reporter) {
            return User::query()->forReporter($reporter)->whereDoesntHave('weeklyReports', $constraint)->orderBy('name')->get();
        };

        return [
            'weekly' => $without(function ($query) use ($weekStart, $weekEnd): void {
                $query->where('kind', 'weekly')
                    ->whereDate('period_start', '<=', $weekEnd)
                    ->whereDate('period_end', '>=', $weekStart);
            }),
            'social' => $without(function ($query) use ($monthStart): void {
                $query->where('kind', 'social')->whereDate('period_start', $monthStart);
            }),
            'seo' => $without(function ($query) use ($monthStart): void {
                $query->where('kind', 'seo')->whereDate('period_start', $monthStart);
            }),
        ];
    }

    private function visible(Request $request, User $customer): void
    {
        $reporter = $this->reporter($request);

        abort_unless($customer->visibleToReporter($reporter), 404);
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
