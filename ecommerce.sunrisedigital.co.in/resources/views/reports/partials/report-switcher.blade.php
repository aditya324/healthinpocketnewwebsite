@php
    $earlier = $earlierReport ?? null;
    $later = $laterReport ?? null;
    $options = $comparableReports ?? collect();
    $chosen = $comparedReport ?? null;
    $forClient = request()->routeIs('dashboard.reports.*');
    $reportRoute = $forClient ? 'dashboard.reports.show' : 'reports.weekly-reports.show';
    $insideWeekly = ! $report->isSocial() && ! $report->isSeo() && $report->kind !== 'monthly';
@endphp
@if ($report->exists && ($earlier || $later || $options->isNotEmpty()))
    <section class="mb-4 rounded-2xl border border-[#ebe6df] bg-white p-4 sm:p-5" aria-label="Other {{ strtolower($report->kindLabel()) }} reports">
        <div class="grid gap-3 sm:grid-cols-[1fr_auto_1fr] sm:items-center">
            <div>
                @if ($earlier)
                    <a href="{{ route($reportRoute, $earlier) }}" class="inline-flex h-10 items-center rounded-full border border-[#e4e0da] bg-[#fbfaf7] px-4 text-sm font-medium text-[#1a1a1a]">← {{ $earlier->currentPeriodLabel() }}</a>
                @else
                    <span class="text-sm text-[#8a8680]">This is the earliest</span>
                @endif
            </div>
            <p class="text-sm font-semibold text-[#1a1a1a] sm:text-center">{{ $report->currentPeriodLabel() }}</p>
            <div class="sm:text-right">
                @if ($later)
                    <a href="{{ route($reportRoute, $later) }}" class="inline-flex h-10 items-center rounded-full border border-[#e4e0da] bg-[#fbfaf7] px-4 text-sm font-medium text-[#1a1a1a]">{{ $later->currentPeriodLabel() }} →</a>
                @else
                    <span class="text-sm text-[#8a8680]">This is the newest</span>
                @endif
            </div>
        </div>
        @if ($options->isNotEmpty())
            <form method="get" action="{{ route($reportRoute, $report) }}" class="mt-4 flex flex-col gap-2 border-t border-[#f3eee6] pt-4 sm:flex-row sm:items-center sm:gap-3">
                <label for="compare-report" class="text-sm font-medium text-[#1a1a1a]">Compare with</label>
                <select id="compare-report" name="compare" onchange="this.form.requestSubmit()" class="h-11 w-full rounded-xl border border-[#e4e0da] bg-white px-3 text-sm text-[#1a1a1a] sm:max-w-xs">
                    @if ($insideWeekly)
                        <option value="">The previous week in this report</option>
                    @else
                        <option value="">This report on its own</option>
                    @endif
                    @foreach ($options as $option)
                        <option value="{{ $option->uuid }}" @selected($chosen?->is($option))>{{ $option->currentPeriodLabel() }}</option>
                    @endforeach
                </select>
            </form>
        @endif
    </section>
@endif
