@php
    use App\Support\WeeklyReportCharts;
    use App\Support\WeeklyReportFields;

    $forClient = request()->routeIs('dashboard.reports.*');
@endphp
@include('reports.partials.report-switcher')
@include('reports.partials.highlights')
@if ($report->kind === 'social')
    @include('reports.partials.social-document')
@elseif ($report->kind === 'seo')
    @include('reports.partials.seo-document')
@else
@php
    $charts = (new WeeklyReportCharts)->for($report, $comparedReport ?? null);
    $facebookShots = $report->screenshots->where('platform', 'facebook');
    $instagramShots = $report->screenshots->where('platform', 'instagram');
    $youtubeShots = $report->screenshots->where('platform', 'youtube');
    $channels = collect($report->youtube ?? []);
@endphp

<article class="rounded-2xl border border-[#ebe6df] bg-white p-6 shadow-sm sm:p-8">
    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-[#b8860b]">{{ $report->isMonthly() ? 'Monthly' : 'Weekly' }} SMM performance report</p>
    <h1 class="mt-2 text-3xl font-semibold tracking-tight text-[#1a1a1a]">{{ $report->title }}</h1>
    <dl class="mt-4 grid gap-3 text-sm sm:grid-cols-2">
        @unless ($forClient)
            <div>
                <dt class="text-xs font-semibold uppercase tracking-[0.14em] text-[#8a8680]">Client</dt>
                <dd class="mt-1 font-medium text-[#1a1a1a]">{{ $report->client?->name ?? $customer->name ?? '' }}</dd>
            </div>
        @endunless
        <div>
            <dt class="text-xs font-semibold uppercase tracking-[0.14em] text-[#8a8680]">Current week</dt>
            <dd class="mt-1 font-medium text-[#1a1a1a]">{{ $report->currentPeriodLabel() }}</dd>
        </div>
        <div>
            <dt class="text-xs font-semibold uppercase tracking-[0.14em] text-[#8a8680]">Previous week</dt>
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

    @if ($report->screenshots->isNotEmpty())
        <section class="mt-8">
            <h2 class="text-lg font-semibold text-[#1a1a1a]">Screenshots</h2>
            <div class="mt-4 flex flex-col gap-6">
                @if ($facebookShots->isNotEmpty())
                    <div>
                        <h3 class="text-sm font-semibold text-[#1a1a1a]">Facebook</h3>
                        <div class="mt-3">@include('reports.partials.screenshots', ['shots' => $facebookShots])</div>
                    </div>
                @endif
                @if ($instagramShots->isNotEmpty())
                    <div>
                        <h3 class="text-sm font-semibold text-[#1a1a1a]">Instagram</h3>
                        <div class="mt-3">@include('reports.partials.screenshots', ['shots' => $instagramShots])</div>
                    </div>
                @endif
                @foreach ($channels as $channel)
                    @php $channelShots = $youtubeShots->where('channel_name', $channel['name'] ?? null); @endphp
                    @if ($channelShots->isNotEmpty())
                        <div>
                            <h3 class="text-sm font-semibold text-[#1a1a1a]">YouTube — {{ $channel['name'] ?: 'Channel' }}</h3>
                            <div class="mt-3">@include('reports.partials.screenshots', ['shots' => $channelShots])</div>
                        </div>
                    @endif
                @endforeach
                @php
                    $named = $channels->pluck('name')->filter()->all();
                    $otherShots = $youtubeShots->reject(fn ($shot) => in_array($shot->channel_name, $named, true));
                @endphp
                @if ($otherShots->isNotEmpty())
                    <div>
                        <h3 class="text-sm font-semibold text-[#1a1a1a]">YouTube</h3>
                        <div class="mt-3">@include('reports.partials.screenshots', ['shots' => $otherShots])</div>
                    </div>
                @endif
            </div>
        </section>
    @endif

    @if ($report->hasFilled($report->facebook))
        <section class="mt-8 border-t border-[#f3eee6] pt-8">
            <h2 class="text-lg font-semibold text-[#1a1a1a]">Facebook</h2>
            <div class="mt-4 grid gap-6 lg:grid-cols-2">
                @include('reports.partials.metric-list', [
                    'heading' => 'Current week ('.$report->currentPeriodLabel().')',
                    'fields' => WeeklyReportFields::facebookMetrics(),
                    'values' => data_get($report->facebook, 'current', []),
                    'changes' => true,
                ])
                @include('reports.partials.metric-list', [
                    'heading' => 'Previous week ('.$report->previousPeriodLabel().')',
                    'fields' => WeeklyReportFields::facebookMetrics(),
                    'values' => data_get($report->facebook, 'previous', []),
                ])
            </div>
            <div class="mt-6 grid gap-6 sm:grid-cols-3">
                @include('reports.partials.metric-list', [
                    'heading' => 'Content published',
                    'fields' => WeeklyReportFields::facebookContent(),
                    'values' => data_get($report->facebook, 'content', []),
                ])
                @include('reports.partials.metric-list', [
                    'heading' => 'Views by content format',
                    'fields' => WeeklyReportFields::facebookViewsByFormat(),
                    'values' => data_get($report->facebook, 'views_by_format', []),
                ])
                @include('reports.partials.metric-list', [
                    'heading' => 'Content interactions by format',
                    'fields' => WeeklyReportFields::facebookInteractionsByFormat(),
                    'values' => data_get($report->facebook, 'interactions_by_format', []),
                ])
            </div>
        </section>
    @endif

    @if ($report->hasFilled($report->instagram))
        <section class="mt-8 border-t border-[#f3eee6] pt-8">
            <h2 class="text-lg font-semibold text-[#1a1a1a]">Instagram</h2>
            <div class="mt-4 grid gap-6 lg:grid-cols-2">
                @include('reports.partials.metric-list', [
                    'heading' => 'Current week ('.$report->currentPeriodLabel().')',
                    'fields' => WeeklyReportFields::instagramMetrics(),
                    'values' => data_get($report->instagram, 'current', []),
                ])
                @include('reports.partials.metric-list', [
                    'heading' => 'Previous week ('.$report->previousPeriodLabel().')',
                    'fields' => WeeklyReportFields::instagramMetrics(),
                    'values' => data_get($report->instagram, 'previous', []),
                ])
            </div>
            @php
                $top = collect(data_get($report->instagram, 'top', []))->filter(fn ($row) => filled($row['title'] ?? null) || filled($row['views'] ?? null));
            @endphp
            @if ($top->isNotEmpty())
                <div class="mt-6">
                    <h3 class="text-sm font-semibold text-[#1a1a1a]">Top-performing content</h3>
                    <ul class="mt-2 flex flex-col gap-1 text-sm leading-6 text-[#1a1a1a]">
                        @foreach ($top as $row)
                            <li>
                                <span class="font-medium">{{ $row['title'] ?: 'Post' }}:</span>
                                {{ $row['views'] }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </section>
    @endif

    @if ($channels->isNotEmpty())
        <section class="mt-8 border-t border-[#f3eee6] pt-8">
            <h2 class="text-lg font-semibold text-[#1a1a1a]">YouTube</h2>
            <div class="mt-4 flex flex-col gap-8">
                @foreach ($channels as $channel)
                    @if ($report->hasFilled($channel))
                        <div>
                            <h3 class="text-base font-semibold text-[#1a1a1a]">{{ $channel['name'] ?: 'Channel' }}</h3>
                            <div class="mt-3 grid gap-6 lg:grid-cols-2">
                                @include('reports.partials.metric-list', [
                                    'heading' => 'Current week ('.$report->currentPeriodLabel().')',
                                    'fields' => WeeklyReportFields::youtubeMetrics(),
                                    'values' => $channel['current'] ?? [],
                                ])
                                @include('reports.partials.metric-list', [
                                    'heading' => 'Previous week ('.$report->previousPeriodLabel().')',
                                    'fields' => WeeklyReportFields::youtubeMetrics(),
                                    'values' => $channel['previous'] ?? [],
                                ])
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </section>
    @endif

    @foreach (WeeklyReportFields::narrativeSections() as $section => $heading)
        @if ($report->hasFilled(data_get($report->narrative, $section)))
            <section class="mt-8 border-t border-[#f3eee6] pt-8">
                <h2 class="text-lg font-semibold text-[#1a1a1a]">{{ $heading }}</h2>
                <div class="mt-4 flex flex-col gap-4">
                    @foreach (WeeklyReportFields::narrativePlatforms() as $platform => $label)
                        @if (filled(data_get($report->narrative, $section.'.'.$platform)))
                            <div>
                                <h3 class="text-sm font-semibold text-[#1a1a1a]">{{ $label }}</h3>
                                <p class="mt-1 whitespace-pre-line text-sm leading-6 text-[#1a1a1a]">{{ data_get($report->narrative, $section.'.'.$platform) }}</p>
                            </div>
                        @endif
                    @endforeach
                </div>
            </section>
        @endif
    @endforeach

    @if (filled($report->pdf_path))
        <section class="mt-8 border-t border-[#f3eee6] pt-8">
            <h2 class="text-lg font-semibold text-[#1a1a1a]">Report PDF</h2>
            <p class="mt-1 text-sm text-[#6f6a64]">This is the file that was uploaded, including the analytics screenshots.</p>
            <iframe src="{{ route('weekly-reports.pdf', $report) }}" title="Report PDF" class="mt-4 h-[70vh] w-full rounded-xl border border-[#ebe6df]"></iframe>
        </section>
    @endif
</article>
@endif
