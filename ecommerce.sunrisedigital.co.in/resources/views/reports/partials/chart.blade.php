<figure class="rounded-2xl border border-[#ebe6df] bg-[#fbfaf7] p-4 sm:p-6">
    <figcaption>
        <h3 class="text-base font-semibold text-[#1a1a1a]">{{ $chart['title'] }}</h3>
        <p class="mt-1 text-sm leading-6 text-[#6f6a64]">{{ $chart['note'] }}</p>
    </figcaption>

    <div @class(['mt-5 grid gap-8', 'sm:grid-cols-2' => count($chart['pies']) > 1])>
        @foreach ($chart['pies'] as $pie)
            @php $total = array_sum(array_column($pie['slices'], 'amount')); @endphp
            <div class="flex flex-col items-center gap-4">
                <p class="text-sm font-semibold text-[#1a1a1a]">{{ $pie['label'] }}</p>
                <svg viewBox="0 0 120 120" class="h-44 w-44" role="img" aria-label="{{ $pie['label'] }}">
                    @foreach ($pie['slices'] as $slice)
                        <path d="{{ $slice['path'] }}" fill="{{ $slice['color'] }}">
                            <title>{{ $slice['label'] }}: {{ $slice['display'] }}</title>
                        </path>
                    @endforeach
                </svg>
                <ul class="flex w-full max-w-xs flex-col gap-2 text-sm text-[#1a1a1a]">
                    @foreach ($pie['slices'] as $slice)
                        <li class="grid grid-cols-[minmax(0,1fr)_auto_auto] items-center gap-3">
                            <span class="inline-flex min-w-0 items-center gap-2">
                                <span class="h-2.5 w-2.5 shrink-0 rounded-full" style="background: {{ $slice['color'] }}"></span>
                                <span class="truncate">{{ $slice['label'] }}</span>
                            </span>
                            <span class="font-medium">{{ $slice['display'] }}</span>
                            <span class="w-10 text-right text-xs text-[#8a8680]">{{ $total > 0 ? round(($slice['amount'] / $total) * 100) : 0 }}%</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    </div>

    @if (! empty($chart['changes']) && count($chart['pies']) > 1)
        <div class="mt-6 overflow-x-auto border-t border-[#ebe6df] pt-4">
            <table class="w-full min-w-[28rem] text-left text-sm">
                <thead>
                    <tr class="text-xs uppercase tracking-wide text-[#8a8680]">
                        <th class="py-2 pr-4 font-medium">Compared</th>
                        <th class="py-2 pr-4 font-medium">{{ $chart['pies'][0]['label'] }}</th>
                        <th class="py-2 pr-4 font-medium">{{ $chart['pies'][1]['label'] }}</th>
                        <th class="py-2 text-right font-medium">Change</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($chart['changes'] as $change)
                        <tr class="border-t border-[#f3eee6]">
                            <td class="py-2 pr-4 text-[#1a1a1a]">{{ $change['label'] }}</td>
                            <td class="py-2 pr-4 text-[#6f6a64]">{{ $change['previous'] }}</td>
                            <td class="py-2 pr-4 font-medium text-[#1a1a1a]">{{ $change['current'] }}</td>
                            <td @class([
                                'py-2 text-right font-semibold',
                                'text-[#1f7a3a]' => $change['direction'] === 'up',
                                'text-[#9b2c2c]' => $change['direction'] === 'down',
                                'text-[#6f6a64]' => $change['direction'] === 'flat',
                            ])>{{ $change['change'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</figure>
