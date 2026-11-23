@php
    $body = is_array($report->body) ? $report->body : [];
    $charts = (new App\Support\WeeklyReportCharts)->for($report, $comparedReport ?? null);
    $comparison = data_get($body, 'comparison', []);
    $analytics = data_get($body, 'analytics', []);
@endphp

<article class="rounded-2xl border border-[#ebe6df] bg-white p-6 shadow-sm sm:p-8">
    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-[#b8860b]">Monthly SEO report</p>
    <h1 class="mt-2 text-3xl font-semibold tracking-tight text-[#1a1a1a]">{{ $report->title }}</h1>
    <dl class="mt-4 grid gap-3 text-sm sm:grid-cols-2">
        @unless ($forClient ?? false)
            <div>
                <dt class="text-xs font-semibold uppercase tracking-[0.14em] text-[#8a8680]">Client</dt>
                <dd class="mt-1 font-medium text-[#1a1a1a]">{{ $report->client?->name ?? $customer->name ?? '' }}</dd>
            </div>
        @endunless
        <div>
            <dt class="text-xs font-semibold uppercase tracking-[0.14em] text-[#8a8680]">Month</dt>
            <dd class="mt-1 font-medium text-[#1a1a1a]">{{ $report->currentPeriodLabel() }}</dd>
        </div>
        <div>
            <dt class="text-xs font-semibold uppercase tracking-[0.14em] text-[#8a8680]">Compared with</dt>
            <dd class="mt-1 font-medium text-[#1a1a1a]">{{ $report->previousPeriodLabel() }}</dd>
        </div>
    </dl>

    @if (filled($report->pdf_path))
        <div class="mt-6">
            <a href="{{ route('weekly-reports.pdf', $report) }}" target="_blank" class="inline-flex h-11 items-center rounded-full bg-[#111111] px-5 text-sm font-semibold text-white">Open the PDF</a>
        </div>
    @endif

    @if ($charts !== [])
        <section class="mt-8 border-t border-[#f3eee6] pt-8" aria-label="Charts">
            <h2 class="text-lg font-semibold text-[#1a1a1a]">At a glance</h2>
            <div class="mt-4 flex flex-col gap-4">
                @foreach ($charts as $chart)
                    @include('reports.partials.chart', ['chart' => $chart])
                @endforeach
            </div>
        </section>
    @endif

    @if ($report->hasFilled(data_get($body, 'summary')))
        <section class="mt-8 border-t border-[#f3eee6] pt-8">
            <h2 class="text-lg font-semibold text-[#1a1a1a]">Summary</h2>
            @include('reports.partials.note-list', ['notes' => data_get($body, 'summary', [])])
        </section>
    @endif

    @if ($report->hasFilled($comparison))
        <section class="mt-8 border-t border-[#f3eee6] pt-8">
            <h2 class="text-lg font-semibold text-[#1a1a1a]">Website</h2>
            @include('reports.partials.comparison-table', ['rows' => $comparison])
        </section>
    @endif

    @if ($report->hasFilled($analytics))
        <section class="mt-8 border-t border-[#f3eee6] pt-8">
            <h2 class="text-lg font-semibold text-[#1a1a1a]">Analytics and enquiries</h2>
            @include('reports.partials.comparison-table', ['rows' => $analytics])
        </section>
    @endif

    @if ($report->hasFilled(data_get($body, 'technical')) || filled(data_get($body, 'technical_note')))
        <section class="mt-8 border-t border-[#f3eee6] pt-8">
            <h2 class="text-lg font-semibold text-[#1a1a1a]">Technical SEO</h2>
            @include('reports.partials.metric-cards', ['metrics' => data_get($body, 'technical', [])])
            @if (filled(data_get($body, 'technical_note')))
                <p class="mt-4 text-sm leading-6 text-[#1a1a1a]">{{ data_get($body, 'technical_note') }}</p>
            @endif
        </section>
    @endif

    @if ($report->hasFilled(data_get($body, 'ai')))
        <section class="mt-8 border-t border-[#f3eee6] pt-8">
            <h2 class="text-lg font-semibold text-[#1a1a1a]">AI search visibility</h2>
            @include('reports.partials.metric-cards', ['metrics' => data_get($body, 'ai', [])])
        </section>
    @endif

    @if ($report->hasFilled(data_get($body, 'search')))
        <section class="mt-8 border-t border-[#f3eee6] pt-8">
            <h2 class="text-lg font-semibold text-[#1a1a1a]">Search visibility</h2>
            @include('reports.partials.metric-cards', ['metrics' => data_get($body, 'search', [])])
        </section>
    @endif

    @if ($report->hasFilled(data_get($body, 'platforms')))
        <section class="mt-8 border-t border-[#f3eee6] pt-8">
            <h2 class="text-lg font-semibold text-[#1a1a1a]">Search Console</h2>
            <div class="mt-4 overflow-x-auto">
                <table class="w-full min-w-[28rem] text-left text-sm">
                    <thead>
                        <tr class="border-b border-[#ebe6df] text-xs uppercase tracking-wide text-[#8a8680]">
                            <th class="py-2 pr-4 font-medium">Platform</th>
                            <th class="py-2 pr-4 font-medium">Clicks</th>
                            <th class="py-2 pr-4 font-medium">Impressions</th>
                            <th class="py-2 font-medium">Average position</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (data_get($body, 'platforms', []) as $platform)
                            <tr class="border-b border-[#f3eee6] last:border-0">
                                <td class="py-2 pr-4 font-medium text-[#1a1a1a]">{{ $platform['name'] }}</td>
                                <td class="py-2 pr-4 text-[#1a1a1a]">{{ $platform['clicks'] }}</td>
                                <td class="py-2 pr-4 text-[#1a1a1a]">{{ $platform['impressions'] }}</td>
                                <td class="py-2 text-[#1a1a1a]">{{ $platform['position'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    @endif

    @if ($report->hasFilled(data_get($body, 'activities')))
        <section class="mt-8 border-t border-[#f3eee6] pt-8">
            <h2 class="text-lg font-semibold text-[#1a1a1a]">Work this month</h2>
            <ul class="mt-4 flex flex-col gap-3">
                @foreach (data_get($body, 'activities', []) as $activity)
                    <li class="rounded-xl bg-[#f6f3ee] px-4 py-3">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <p class="text-sm leading-6 text-[#1a1a1a]">{{ $activity['text'] }}</p>
                            <span class="inline-flex rounded-full bg-white px-3 py-1 text-xs font-semibold text-[#1a1a1a]">{{ $activity['status'] }}</span>
                        </div>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    @if ($report->hasFilled(data_get($body, 'priorities')))
        <section class="mt-8 border-t border-[#f3eee6] pt-8">
            <h2 class="text-lg font-semibold text-[#1a1a1a]">Priority treatments</h2>
            <ul class="mt-4 flex flex-col gap-2 text-sm leading-6 text-[#1a1a1a]">
                @foreach (data_get($body, 'priorities', []) as $priority)
                    <li><span class="font-medium">{{ $priority['name'] }}:</span> {{ $priority['focus'] }}</li>
                @endforeach
            </ul>
        </section>
    @endif

    @if ($report->hasFilled(data_get($body, 'linking')))
        <section class="mt-8 border-t border-[#f3eee6] pt-8">
            <h2 class="text-lg font-semibold text-[#1a1a1a]">Internal linking</h2>
            @include('reports.partials.note-list', ['notes' => data_get($body, 'linking', [])])
        </section>
    @endif

    @if ($report->hasFilled(data_get($body, 'competitor')))
        <section class="mt-8 border-t border-[#f3eee6] pt-8">
            <h2 class="text-lg font-semibold text-[#1a1a1a]">Competitor ranking</h2>
            @include('reports.partials.note-list', ['notes' => data_get($body, 'competitor', [])])
        </section>
    @endif

    @if ($report->hasFilled(data_get($body, 'next')))
        <section class="mt-8 border-t border-[#f3eee6] pt-8">
            <h2 class="text-lg font-semibold text-[#1a1a1a]">Next month</h2>
            @include('reports.partials.note-list', ['notes' => data_get($body, 'next', [])])
        </section>
    @endif

    @if (filled($report->pdf_path))
        <section class="mt-8 border-t border-[#f3eee6] pt-8">
            <h2 class="text-lg font-semibold text-[#1a1a1a]">Report PDF</h2>
            <p class="mt-1 text-sm text-[#6f6a64]">This is the file that was uploaded, including the analytics screenshots.</p>
            <iframe src="{{ route('weekly-reports.pdf', $report) }}" title="Report PDF" class="mt-4 h-[70vh] w-full rounded-xl border border-[#ebe6df]"></iframe>
        </section>
    @endif
</article>
