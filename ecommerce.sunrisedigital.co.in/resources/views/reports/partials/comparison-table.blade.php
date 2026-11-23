<div class="mt-4 overflow-x-auto">
    <table class="w-full min-w-[28rem] text-left text-sm">
        <thead>
            <tr class="border-b border-[#ebe6df] text-xs uppercase tracking-wide text-[#8a8680]">
                <th class="py-2 pr-4 font-medium">Metric</th>
                <th class="py-2 pr-4 font-medium">{{ $report->previousPeriodLabel() }}</th>
                <th class="py-2 font-medium">{{ $report->currentPeriodLabel() }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rows as $row)
                <tr class="border-b border-[#f3eee6] last:border-0">
                    <td class="py-2 pr-4 text-[#1a1a1a]">{{ $row['label'] }}</td>
                    <td class="py-2 pr-4 text-[#6f6a64]">{{ $row['previous'] }}</td>
                    <td class="py-2 font-semibold text-[#1a1a1a]">{{ $row['current'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
