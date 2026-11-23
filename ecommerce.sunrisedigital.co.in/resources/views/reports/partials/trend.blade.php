@if ($trend)
    <section class="mb-4 rounded-2xl border border-[#ebe6df] bg-white p-6 shadow-sm" aria-label="Over time">
        <h2 class="text-lg font-semibold text-[#1a1a1a]">{{ $trend['title'] }}</h2>
        <p class="mt-1 text-sm leading-6 text-[#6f6a64]">{{ $trend['note'] }}</p>

        <svg viewBox="0 0 {{ $trend['width'] }} {{ $trend['height'] }}" class="mt-4 w-full" role="img" aria-label="{{ $trend['note'] }}">
            @foreach ($trend['ticks'] as $tick)
                <line x1="{{ $trend['plot_left'] }}" x2="{{ $trend['width'] - $trend['plot_right'] }}" y1="{{ $tick['y'] }}" y2="{{ $tick['y'] }}" stroke="#ebe6df" />
                <text x="0" y="{{ $tick['y'] + 4 }}" fill="#8a8680" font-size="11">{{ $tick['label'] }}</text>
            @endforeach
            @foreach ($trend['series'] as $series)
                @foreach ($series['lines'] as $line)
                    <polyline points="{{ $line }}" fill="none" stroke="{{ $series['color'] }}" stroke-width="2.5" stroke-linejoin="round" stroke-linecap="round" />
                @endforeach
                @foreach ($series['dots'] as $dot)
                    <circle cx="{{ $dot['cx'] }}" cy="{{ $dot['cy'] }}" r="4" fill="{{ $series['color'] }}">
                        <title>{{ $dot['label'] }}: {{ $series['label'] }} {{ $dot['display'] }}</title>
                    </circle>
                @endforeach
            @endforeach
            @foreach ($trend['labels'] as $label)
                <text x="{{ $label['x'] }}" y="{{ $trend['height'] - 8 }}" fill="#6f6a64" font-size="11" text-anchor="middle">{{ $label['text'] }}</text>
            @endforeach
        </svg>

        <ul class="mt-4 flex flex-wrap gap-4 text-sm text-[#1a1a1a]">
            @foreach ($trend['series'] as $series)
                <li class="inline-flex items-center gap-2">
                    <span class="h-2.5 w-2.5 rounded-full" style="background: {{ $series['color'] }}"></span>
                    {{ $series['label'] }}
                </li>
            @endforeach
        </ul>

        <div class="mt-4 overflow-x-auto border-t border-[#ebe6df] pt-4">
            <table class="w-full min-w-[28rem] text-left text-sm">
                <thead>
                    <tr class="text-xs uppercase tracking-wide text-[#8a8680]">
                        <th class="py-2 pr-4 font-medium">Period</th>
                        @foreach ($trend['series'] as $series)
                            <th class="py-2 pr-4 font-medium">{{ $series['label'] }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($trend['series'][0]['points'] as $index => $point)
                        <tr class="border-t border-[#f3eee6]">
                            <td class="py-2 pr-4 text-[#1a1a1a]">{{ $point['full'] }}</td>
                            @foreach ($trend['series'] as $series)
                                <td class="py-2 pr-4 font-medium text-[#1a1a1a]">{{ $series['points'][$index]['display'] ?? '—' }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endif
