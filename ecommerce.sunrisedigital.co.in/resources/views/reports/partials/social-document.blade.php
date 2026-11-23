@php
    $body = is_array($report->body) ? $report->body : [];
    $charts = (new App\Support\WeeklyReportCharts)->for($report, $comparedReport ?? null);
@endphp

<article class="rounded-2xl border border-[#ebe6df] bg-white p-6 shadow-sm sm:p-8">
    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-[#b8860b]">Monthly social media report</p>
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

    @foreach (['instagram' => 'Instagram', 'facebook' => 'Facebook'] as $key => $heading)
        @if ($report->hasFilled(data_get($body, $key)))
            <section class="mt-8 border-t border-[#f3eee6] pt-8">
                <h2 class="text-lg font-semibold text-[#1a1a1a]">{{ $heading }}</h2>
                @include('reports.partials.metric-cards', ['metrics' => data_get($body, $key.'.metrics', [])])
                @include('reports.partials.note-list', ['notes' => data_get($body, $key.'.notes', [])])
            </section>
        @endif
    @endforeach

    @foreach (data_get($body, 'youtube', []) as $channel)
        @if ($report->hasFilled($channel))
            <section class="mt-8 border-t border-[#f3eee6] pt-8">
                <h2 class="text-lg font-semibold text-[#1a1a1a]">YouTube — {{ $channel['name'] ?: 'Channel' }}</h2>
                @include('reports.partials.metric-cards', ['metrics' => $channel['metrics'] ?? []])
                @include('reports.partials.note-list', ['notes' => $channel['notes'] ?? []])
            </section>
        @endif
    @endforeach

    @foreach (data_get($body, 'profiles', []) as $profile)
        @if ($report->hasFilled($profile))
            <section class="mt-8 border-t border-[#f3eee6] pt-8">
                <h2 class="text-lg font-semibold text-[#1a1a1a]">Google Business Profile — {{ $profile['name'] ?: 'Location' }}</h2>
                @include('reports.partials.metric-cards', ['metrics' => $profile['metrics'] ?? []])
                @include('reports.partials.note-list', ['notes' => $profile['notes'] ?? []])
            </section>
        @endif
    @endforeach

    @if ($report->hasFilled(data_get($body, 'content')))
        <section class="mt-8 border-t border-[#f3eee6] pt-8">
            <h2 class="text-lg font-semibold text-[#1a1a1a]">Content</h2>
            @include('reports.partials.note-list', ['notes' => data_get($body, 'content', [])])
        </section>
    @endif

    @if ($report->hasFilled(data_get($body, 'worked')))
        <section class="mt-8 border-t border-[#f3eee6] pt-8">
            <h2 class="text-lg font-semibold text-[#1a1a1a]">What worked</h2>
            @include('reports.partials.note-list', ['notes' => data_get($body, 'worked', [])])
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
