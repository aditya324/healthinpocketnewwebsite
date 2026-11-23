@php
    $highlights = \App\Support\WeeklyReportHighlights::for($report, $comparedReport ?? null);
@endphp
@if ($highlights['rise'] || $highlights['drop'])
    <p class="mb-4 rounded-2xl border border-[#ead89a] bg-[#fff8e6] px-4 py-3 text-sm leading-6 text-[#1a1a1a]">
        @if ($highlights['rise'])
            <span class="font-semibold text-[#1f7a3a]">{{ $highlights['rise']['label'] }} is up {{ ltrim($highlights['rise']['change'], '+') }}.</span>
        @endif
        @if ($highlights['rise'] && $highlights['drop'])
            <span> </span>
        @endif
        @if ($highlights['drop'])
            <span class="font-semibold text-[#9b2c2c]">{{ $highlights['drop']['label'] }} is down {{ ltrim($highlights['drop']['change'], '-') }}.</span>
        @endif
    </p>
@endif
