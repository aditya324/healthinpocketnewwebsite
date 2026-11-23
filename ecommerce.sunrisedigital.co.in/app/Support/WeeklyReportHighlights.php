<?php

namespace App\Support;

use App\Models\WeeklyReport;

class WeeklyReportHighlights
{
    /**
     * @return array{rise: array{label: string, change: string}|null, drop: array{label: string, change: string}|null}
     */
    public static function for(WeeklyReport $report, ?WeeklyReport $compared = null): array
    {
        $rise = null;
        $drop = null;

        foreach ((new WeeklyReportCharts)->for($report, $compared) as $chart) {
            foreach ($chart['changes'] ?? [] as $change) {
                if (! is_array($change) || ! is_string($change['change'] ?? null)) {
                    continue;
                }

                $amount = (float) rtrim($change['change'], '%');

                if (($change['direction'] ?? null) === 'up' && ($rise === null || $amount > $rise['amount'])) {
                    $rise = ['amount' => $amount, 'label' => (string) $change['label'], 'change' => $change['change']];
                }

                if (($change['direction'] ?? null) === 'down' && ($drop === null || $amount < $drop['amount'])) {
                    $drop = ['amount' => $amount, 'label' => (string) $change['label'], 'change' => $change['change']];
                }
            }
        }

        return [
            'rise' => $rise === null ? null : ['label' => $rise['label'], 'change' => $rise['change']],
            'drop' => $drop === null ? null : ['label' => $drop['label'], 'change' => $drop['change']],
        ];
    }
}
