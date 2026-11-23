@extends('dashboard.layout')

@section('title', 'Reports')

@section('content')
    @php
        $groups = $reports->groupBy('kind')->sortBy(fn ($items, string $kind): int => match ($kind) {
            'weekly' => 0,
            'social' => 1,
            'seo' => 2,
            default => 3,
        });
        $intro = [
            'weekly' => 'How the week went on Facebook, Instagram, and YouTube.',
            'social' => 'A month of social media results.',
            'seo' => 'A month of search results, enquiries, and site health.',
        ];
    @endphp

    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-[#b8860b]">Reports</p>
    <h1 class="mt-1 text-3xl font-semibold tracking-tight text-[#1a1a1a]">Your reports</h1>
    <p class="mt-2 max-w-2xl text-sm leading-6 text-[#6f6a64]">Sunrise publishes a weekly report, a monthly social media report, and a monthly SEO report. Open one, then compare it with any other report of the same kind.</p>

    @forelse ($groups as $kind => $group)
        <section class="mt-8">
            <h2 class="text-lg font-semibold text-[#1a1a1a]">{{ $group->first()->kindLabel() }}</h2>
            <p class="mt-1 text-sm text-[#6f6a64]">{{ $intro[$kind] ?? 'Published reports.' }}</p>
            <div class="mt-3 grid gap-3">
                @foreach ($group as $report)
                    <a href="{{ route('dashboard.reports.show', $report) }}" class="flex flex-wrap items-center justify-between gap-4 rounded-2xl border border-[#ebe6df] bg-white p-4 shadow-sm hover:border-[#f5b400] sm:p-5">
                        <span>
                            <span class="block text-base font-semibold text-[#1a1a1a]">{{ $report->currentPeriodLabel() }}</span>
                            <span class="mt-1 block text-sm text-[#6f6a64]">{{ $report->title }}</span>
                        </span>
                        <span class="inline-flex h-10 items-center rounded-full bg-[#111111] px-4 text-sm font-semibold text-white">View</span>
                    </a>
                @endforeach
            </div>
        </section>
    @empty
        <div class="mt-6 rounded-2xl border border-[#ebe6df] bg-white px-5 py-10 text-sm text-[#6f6a64]">Your reports will show up here after Sunrise publishes them.</div>
    @endforelse
@endsection
