@if ($report->hasFilled($metrics ?? []))
    <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ($metrics as $metric)
            @if (filled($metric['value'] ?? null))
                <div class="rounded-xl bg-[#f6f3ee] px-4 py-3">
                    <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[#8a8680]">{{ $metric['label'] }}</p>
                    <p class="mt-1 text-xl font-semibold text-[#1a1a1a]">{{ $metric['value'] }}</p>
                </div>
            @endif
        @endforeach
    </div>
@endif
