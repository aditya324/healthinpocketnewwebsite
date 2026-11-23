<?php

namespace App\Support;

use App\Models\WeeklyReport;
use Illuminate\Support\Collection;

class WeeklyReportCharts
{
    /** @var list<string> */
    private const COLORS = [
        '#f5b400',
        '#111111',
        '#2f6f4e',
        '#9b2c2c',
        '#3d5a80',
        '#e07a3d',
        '#6b5b95',
        '#c4bdb2',
    ];

    /**
     * @return list<array{
     *     title: string,
     *     note: string,
     *     pies: list<array{
     *         label: string,
     *         slices: list<array{label: string, amount: float, display: string, color: string, path: string}>
     *     }>
     * }>
     */
    public function for(WeeklyReport $report, ?WeeklyReport $earlier = null): array
    {
        if ($report->kind === 'social') {
            return $this->socialCharts($report, $earlier);
        }

        if ($report->kind === 'seo') {
            return $this->seoCharts($report, $earlier);
        }

        if ($earlier instanceof WeeklyReport) {
            return $this->weeklyAcross($report, $earlier);
        }

        $noun = $report->isMonthly() ? 'month' : 'week';
        $current = $report->isMonthly() ? 'This month' : 'This week';
        $previous = $report->isMonthly() ? 'Previous month' : 'Previous week';

        return array_values(array_filter([
            $this->comparison(
                'Views',
                "Each circle is one {$noun}, so you can compare the two {$noun}s.",
                $previous,
                $current,
                $this->viewParts($report),
            ),
            $this->single(
                'Facebook views by format',
                "This {$noun}'s Facebook views, split by format.",
                $current,
                $this->formatParts($report),
            ),
            $this->comparison(
                'Watch time',
                "Each circle is one {$noun}. The slices are that {$noun}'s share of watch time.",
                $previous,
                $current,
                $this->watchParts($report),
            ),
        ]));
    }

    /**
     * Views, search clicks, or enquiries across the latest published reports of this kind.
     *
     * @param  Collection<int, WeeklyReport>  $others
     * @return array{
     *     title: string,
     *     note: string,
     *     width: int,
     *     height: int,
     *     plot_left: int,
     *     plot_right: int,
     *     ticks: list<array{y: float, label: string}>,
     *     labels: list<array{x: float, text: string}>,
     *     series: list<array{
     *         label: string,
     *         color: string,
     *         lines: list<string>,
     *         dots: list<array{cx: float, cy: float, label: string, display: string}>,
     *         points: list<array{label: string, full: string, amount: float|null, display: string|null}>
     *     }>
     * }|null
     */
    public function trend(WeeklyReport $report, Collection $others): ?array
    {
        $kind = $report->kind ?: 'weekly';
        $window = $others
            ->filter(fn (mixed $item): bool => $item instanceof WeeklyReport
                && $item->isPublished()
                && ($item->kind ?: 'weekly') === $kind
                && $item->isNot($report))
            ->when($report->isPublished(), fn (Collection $reports): Collection => $reports->push($report))
            ->unique(fn (WeeklyReport $item): int|string => $item->getKey() ?? spl_object_id($item))
            ->sortBy(fn (WeeklyReport $item): string => sprintf('%010d-%010d', $item->period_start?->getTimestamp() ?? 0, (int) $item->getKey()))
            ->slice(-6)
            ->values();

        if ($window->count() < 2) {
            return null;
        }

        $definitions = $report->isSeo()
            ? [
                ['metric' => 'clicks', 'label' => 'Search clicks', 'color' => '#f5b400'],
                ['metric' => 'enquiries', 'label' => 'Enquiries', 'color' => '#111111'],
            ]
            : [
                ['metric' => 'views', 'label' => 'Views', 'color' => '#f5b400'],
            ];

        $series = [];

        foreach ($definitions as $definition) {
            $points = [];

            foreach ($window as $item) {
                $amount = $this->trendAmount($item, $definition['metric']);
                $points[] = [
                    'label' => $this->trendAxisLabel($item),
                    'full' => $item->currentPeriodLabel(),
                    'amount' => $amount,
                    'display' => $amount === null ? null : $this->displayAmount($amount),
                ];
            }

            $filled = count(array_filter($points, fn (array $point): bool => $point['amount'] !== null));

            if ($filled === 0) {
                continue;
            }

            $series[] = [
                'label' => $definition['label'],
                'color' => $definition['color'],
                'points' => $points,
            ];
        }

        $longest = 0;

        foreach ($series as $item) {
            $longest = max($longest, count(array_filter($item['points'], fn (array $point): bool => $point['amount'] !== null)));
        }

        if ($longest < 2) {
            return null;
        }

        $plotted = $this->plotTrend($series);
        $count = $window->count();
        $span = $report->isMonthly() ? 'month' : 'week';
        $noun = $count === 1 ? $span : $span.'s';
        $note = $report->isSeo()
            ? "The last {$count} published {$noun}. Each point is that {$span}'s search clicks or enquiries."
            : "The last {$count} published {$noun}. Each point is that {$span}'s views.";

        return [
            'title' => 'Over time',
            'note' => $note,
            ...$plotted,
        ];
    }

    /**
     * @return list<array{title: string, note: string, pies: list<array{label: string, slices: list<array{label: string, amount: float, display: string, color: string, path: string}>}>}>
     */
    private function socialCharts(WeeklyReport $report, ?WeeklyReport $earlier): array
    {
        $body = is_array($report->body) ? $report->body : [];
        $earlierBody = is_array($earlier?->body) ? $earlier->body : [];
        $views = [];

        foreach (['facebook' => 'Facebook', 'instagram' => 'Instagram'] as $key => $label) {
            $part = $this->viewPart(
                $label,
                $this->metricValue(data_get($body, $key.'.metrics'), 'Views'),
                $this->metricValue(data_get($earlierBody, $key.'.metrics'), 'Views'),
            );

            if ($part !== null) {
                $views[] = $part;
            }
        }

        foreach ($this->rowNames(data_get($body, 'youtube', []), data_get($earlierBody, 'youtube', [])) as $name) {
            $channel = $this->namedRow(data_get($body, 'youtube', []), $name);
            $earlierChannel = $this->namedRow(data_get($earlierBody, 'youtube', []), $name);
            $part = $this->viewPart(
                'YouTube · '.($name !== '' ? $name : 'Channel'),
                $this->metricValue($channel['metrics'] ?? [], 'Views'),
                $this->metricValue($earlierChannel['metrics'] ?? [], 'Views'),
            );

            if ($part !== null) {
                $views[] = $part;
            }
        }

        $locations = [];

        foreach ($this->rowNames(data_get($body, 'profiles', []), data_get($earlierBody, 'profiles', [])) as $name) {
            $profile = $this->namedRow(data_get($body, 'profiles', []), $name);
            $earlierProfile = $this->namedRow(data_get($earlierBody, 'profiles', []), $name);
            $part = $this->viewPart(
                $name !== '' ? $name : 'Location',
                $this->metricValue($profile['metrics'] ?? [], 'Interactions'),
                $this->metricValue($earlierProfile['metrics'] ?? [], 'Interactions'),
            );

            if ($part !== null) {
                $locations[] = $part;
            }
        }

        return array_values(array_filter([
            $this->periodChart(
                'Views',
                'Each slice is that platform\'s share of views this month.',
                'Each circle is one month, so you can compare the two months.',
                $earlier,
                $report,
                $views,
            ),
            $this->periodChart(
                'Profile interactions',
                'Each slice is one location\'s share of Google Business Profile interactions.',
                'Each circle is one month, so you can compare profile interactions.',
                $earlier,
                $report,
                $locations,
            ),
        ]));
    }

    /**
     * @return list<array{title: string, note: string, pies: list<array{label: string, slices: list<array{label: string, amount: float, display: string, color: string, path: string}>}>}>
     */
    private function seoCharts(WeeklyReport $report, ?WeeklyReport $earlier): array
    {
        $body = is_array($report->body) ? $report->body : [];
        $earlierBody = is_array($earlier?->body) ? $earlier->body : [];
        $comparison = is_array($body['comparison'] ?? null) ? $body['comparison'] : [];
        $analytics = is_array($body['analytics'] ?? null) ? $body['analytics'] : [];
        $audience = $this->row($comparison, 'New Users') !== null ? $comparison : $analytics;
        $clicks = $this->platformParts(
            is_array($body['platforms'] ?? null) ? $body['platforms'] : [],
            is_array($earlierBody['platforms'] ?? null) ? $earlierBody['platforms'] : [],
        );
        $audienceParts = $earlier instanceof WeeklyReport
            ? $this->filedMonthParts($body, $earlierBody, ['New Users', 'Returning Users'])
            : $this->monthParts($audience, ['New Users', 'Returning Users']);
        $enquiryParts = $earlier instanceof WeeklyReport
            ? $this->filedMonthParts($body, $earlierBody, ['WhatsApp Clicks', 'Call Clicks', 'Form Submissions'])
            : $this->monthParts($analytics, ['WhatsApp Clicks', 'Call Clicks', 'Form Submissions']);

        return array_values(array_filter([
            $earlier instanceof WeeklyReport
                ? $this->periodChart('Audience', 'Each slice is that group\'s share of the audience this month.', 'Each circle is one month, so you can compare new and returning users.', $earlier, $report, $audienceParts)
                : $this->comparison('Audience', 'Each circle is one month, so you can compare new and returning users.', $report->previousPeriodLabel(), $report->currentPeriodLabel(), $audienceParts),
            $earlier instanceof WeeklyReport
                ? $this->periodChart('Enquiries', 'Each slice is that enquiry type\'s share this month.', 'Each circle is one month, so you can compare calls, WhatsApp, and forms.', $earlier, $report, $enquiryParts)
                : $this->comparison('Enquiries', 'Each circle is one month, so you can compare calls, WhatsApp, and forms.', $report->previousPeriodLabel(), $report->currentPeriodLabel(), $enquiryParts),
            $this->periodChart(
                'Search clicks',
                'Each slice is that platform\'s share of search clicks this month.',
                'Each circle is one month, so you can compare search clicks.',
                $earlier,
                $report,
                $clicks,
            ),
        ]));
    }

    /**
     * @param  list<array{label: string, current: array{amount: float, display: string}|null, previous: array{amount: float, display: string}|null}>  $parts
     * @return array<string, mixed>|null
     */
    private function periodChart(string $title, string $singleNote, string $comparisonNote, ?WeeklyReport $other, WeeklyReport $report, array $parts): ?array
    {
        if ($other instanceof WeeklyReport && $this->hasPrevious($parts)) {
            if ($this->reportIsOlder($report, $other)) {
                $parts = $this->swapSides($parts);
            }

            $older = $this->reportIsOlder($report, $other) ? $report : $other;
            $newer = $older->is($report) ? $other : $report;

            return $this->comparison($title, $comparisonNote, $older->currentPeriodLabel(), $newer->currentPeriodLabel(), $parts);
        }

        return $this->single($title, $singleNote, $report->isMonthly() ? 'This month' : 'This week', $parts);
    }

    private function reportIsOlder(WeeklyReport $report, WeeklyReport $other): bool
    {
        return $report->period_start !== null
            && $other->period_start !== null
            && $report->period_start->lt($other->period_start);
    }

    /**
     * @param  list<array{label: string, current: array{amount: float, display: string}|null, previous: array{amount: float, display: string}|null}>  $parts
     * @return list<array{label: string, current: array{amount: float, display: string}|null, previous: array{amount: float, display: string}|null}>
     */
    private function swapSides(array $parts): array
    {
        return array_map(fn (array $part): array => [
            'label' => $part['label'],
            'current' => $part['previous'],
            'previous' => $part['current'],
        ], $parts);
    }

    /**
     * @return list<array{title: string, note: string, pies: list<array{label: string, slices: list<array{label: string, amount: float, display: string, color: string, path: string}>}>}>
     */
    private function weeklyAcross(WeeklyReport $report, WeeklyReport $other): array
    {
        return array_values(array_filter([
            $this->periodChart(
                'Views',
                'Each slice is that platform\'s share of views this week.',
                'Each circle is one week, so you can compare the two weeks.',
                $other,
                $report,
                $this->acrossParts($report, $other, 'views'),
            ),
            $this->single(
                'Facebook views by format',
                'This week\'s Facebook views, split by format.',
                'This week',
                $this->formatParts($report),
            ),
            $this->periodChart(
                'Watch time',
                'Each slice is that channel\'s share of watch time this week.',
                'Each circle is one week. The slices are that week\'s share of watch time.',
                $other,
                $report,
                $this->acrossParts($report, $other, 'watch'),
            ),
        ]));
    }

    /**
     * @return list<array{label: string, current: array{amount: float, display: string}|null, previous: array{amount: float, display: string}|null}>
     */
    private function acrossParts(WeeklyReport $report, WeeklyReport $other, string $metric): array
    {
        $watch = $metric === 'watch';
        $parser = $watch ? $this->minutes(...) : $this->quantity(...);
        $key = $watch ? 'current.watch_time' : 'current.views';
        $parts = [];
        $facebook = $this->pair(data_get($report->facebook, $key), data_get($other->facebook, $key), $parser);

        if ($facebook !== null) {
            $parts[] = ['label' => 'Facebook', ...$facebook];
        }

        if (! $watch) {
            $instagram = $this->pair(
                data_get($report->instagram, 'current.total_views'),
                data_get($other->instagram, 'current.total_views'),
                $parser,
            );

            if ($instagram !== null) {
                $parts[] = ['label' => 'Instagram', ...$instagram];
            }
        }

        foreach ($this->rowNames($report->youtube ?? [], $other->youtube ?? []) as $name) {
            $channel = $this->namedRow($report->youtube ?? [], $name);
            $match = $this->namedRow($other->youtube ?? [], $name);
            $pair = $this->pair(data_get($channel, $key), data_get($match, $key), $parser);

            if ($pair === null) {
                continue;
            }

            $parts[] = ['label' => 'YouTube · '.($name !== '' ? $name : 'Channel'), ...$pair];
        }

        return $parts;
    }

    /**
     * @param  array<string, mixed>  $body
     * @param  array<string, mixed>  $otherBody
     * @param  list<string>  $labels
     * @return list<array{label: string, current: array{amount: float, display: string}|null, previous: array{amount: float, display: string}|null}>
     */
    private function filedMonthParts(array $body, array $otherBody, array $labels): array
    {
        $parts = [];

        foreach ($labels as $label) {
            $part = $this->viewPart($label, $this->currentFigure($body, $label), $this->currentFigure($otherBody, $label));

            if ($part !== null) {
                $parts[] = $part;
            }
        }

        return $parts;
    }

    /**
     * @param  array<string, mixed>  $body
     */
    private function currentFigure(array $body, string $label): ?string
    {
        foreach (['comparison', 'analytics'] as $section) {
            $rows = is_array($body[$section] ?? null) ? $body[$section] : [];
            $row = $this->row($rows, $label);
            $value = $row['current'] ?? null;

            if (is_string($value) && trim($value) !== '') {
                return $value;
            }
        }

        return null;
    }

    /**
     * @param  list<array{label: string, previous: array{amount: float, display: string}|null}>  $parts
     */
    private function hasPrevious(array $parts): bool
    {
        foreach ($parts as $part) {
            if ($part['previous'] !== null) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array<string, mixed>
     */
    private function namedRow(mixed $rows, string $name): array
    {
        if (! is_array($rows) || $name === '') {
            return [];
        }

        foreach ($rows as $row) {
            if (is_array($row) && strcasecmp((string) ($row['name'] ?? ''), $name) === 0) {
                return $row;
            }
        }

        return [];
    }

    /**
     * @return list<string>
     */
    private function rowNames(mixed ...$groups): array
    {
        $names = [];

        foreach ($groups as $rows) {
            if (! is_array($rows)) {
                continue;
            }

            foreach ($rows as $row) {
                if (! is_array($row)) {
                    continue;
                }

                $name = trim((string) ($row['name'] ?? ''));
                $key = strtolower($name);

                if ($key === '' || array_key_exists($key, $names)) {
                    continue;
                }

                $names[$key] = $name;
            }
        }

        return array_values($names);
    }

    /**
     * @return array{label: string, current: array{amount: float, display: string}|null, previous: array{amount: float, display: string}|null}|null
     */
    private function viewPart(string $label, ?string $current, ?string $previous = null): ?array
    {
        $pair = $this->pair($current, $previous, $this->quantity(...));

        if ($pair === null) {
            return null;
        }

        return ['label' => $label, ...$pair];
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     * @param  list<string>  $labels
     * @return list<array{label: string, current: array{amount: float, display: string}|null, previous: array{amount: float, display: string}|null}>
     */
    private function monthParts(array $rows, array $labels): array
    {
        $parts = [];

        foreach ($labels as $label) {
            $row = $this->row($rows, $label);

            if ($row === null) {
                continue;
            }

            $pair = $this->pair($row['current'] ?? null, $row['previous'] ?? null, $this->quantity(...));

            if ($pair === null) {
                continue;
            }

            $parts[] = ['label' => $label, ...$pair];
        }

        return $parts;
    }

    /**
     * @param  list<array<string, mixed>>  $platforms
     * @param  list<array<string, mixed>>  $previousPlatforms
     * @return list<array{label: string, current: array{amount: float, display: string}|null, previous: array{amount: float, display: string}|null}>
     */
    private function platformParts(array $platforms, array $previousPlatforms = []): array
    {
        $currentClicks = $this->platformClicks($platforms);
        $previousClicks = $this->platformClicks($previousPlatforms);
        $parts = [];

        foreach ($this->rowNames($platforms, $previousPlatforms) as $name) {
            $key = strtolower($name);
            $part = $this->viewPart($name, $currentClicks[$key] ?? null, $previousClicks[$key] ?? null);

            if ($part !== null) {
                $parts[] = $part;
            }
        }

        return $parts;
    }

    /**
     * @param  list<array<string, mixed>>  $platforms
     * @return array<string, string>
     */
    private function platformClicks(array $platforms): array
    {
        $clicks = [];

        foreach ($platforms as $platform) {
            if (! is_array($platform) || ! is_string($platform['name'] ?? null) || ! is_string($platform['clicks'] ?? null)) {
                continue;
            }

            $clicks[strtolower($platform['name'])] = $platform['clicks'];
        }

        return $clicks;
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     * @return array<string, mixed>|null
     */
    private function row(array $rows, string $label): ?array
    {
        foreach ($rows as $row) {
            if (is_array($row) && strcasecmp((string) ($row['label'] ?? ''), $label) === 0) {
                return $row;
            }
        }

        return null;
    }

    private function metricValue(mixed $metrics, string $label): ?string
    {
        if (! is_array($metrics)) {
            return null;
        }

        foreach ($metrics as $metric) {
            if (is_array($metric) && strcasecmp((string) ($metric['label'] ?? ''), $label) === 0 && is_string($metric['value'] ?? null)) {
                return $metric['value'];
            }
        }

        return null;
    }

    /**
     * @return list<array{label: string, current: array{amount: float, display: string}|null, previous: array{amount: float, display: string}|null}>
     */
    private function viewParts(WeeklyReport $report): array
    {
        $parts = [];
        $facebook = $this->pair(
            data_get($report->facebook, 'current.views'),
            data_get($report->facebook, 'previous.views'),
            $this->quantity(...),
        );

        if ($facebook !== null) {
            $parts[] = ['label' => 'Facebook', ...$facebook];
        }

        $instagram = $this->pair(
            data_get($report->instagram, 'current.total_views'),
            data_get($report->instagram, 'previous.total_views'),
            $this->quantity(...),
        );

        if ($instagram !== null) {
            $parts[] = ['label' => 'Instagram', ...$instagram];
        }

        foreach ($report->youtube ?? [] as $channel) {
            if (! is_array($channel)) {
                continue;
            }

            $pair = $this->pair(
                data_get($channel, 'current.views'),
                data_get($channel, 'previous.views'),
                $this->quantity(...),
            );

            if ($pair === null) {
                continue;
            }

            $parts[] = ['label' => 'YouTube · '.($channel['name'] ?: 'Channel'), ...$pair];
        }

        return $parts;
    }

    /**
     * @return list<array{label: string, current: array{amount: float, display: string}|null, previous: array{amount: float, display: string}|null}>
     */
    private function formatParts(WeeklyReport $report): array
    {
        $parts = [];

        foreach (WeeklyReportFields::facebookViewsByFormat() as $key => $label) {
            $display = data_get($report->facebook, 'views_by_format.'.$key);

            if (! is_string($display) || trim($display) === '') {
                continue;
            }

            $amount = $this->quantity($display);

            if ($amount === null || $amount <= 0) {
                continue;
            }

            $parts[] = [
                'label' => $label,
                'current' => ['amount' => $amount, 'display' => $display],
                'previous' => null,
            ];
        }

        return $parts;
    }

    /**
     * @return list<array{label: string, current: array{amount: float, display: string}|null, previous: array{amount: float, display: string}|null}>
     */
    private function watchParts(WeeklyReport $report): array
    {
        $parts = [];
        $facebook = $this->pair(
            data_get($report->facebook, 'current.watch_time'),
            data_get($report->facebook, 'previous.watch_time'),
            $this->minutes(...),
        );

        if ($facebook !== null) {
            $parts[] = ['label' => 'Facebook', ...$facebook];
        }

        foreach ($report->youtube ?? [] as $channel) {
            if (! is_array($channel)) {
                continue;
            }

            $pair = $this->pair(
                data_get($channel, 'current.watch_time'),
                data_get($channel, 'previous.watch_time'),
                $this->minutes(...),
            );

            if ($pair === null) {
                continue;
            }

            $parts[] = ['label' => 'YouTube · '.($channel['name'] ?: 'Channel'), ...$pair];
        }

        return $parts;
    }

    /**
     * @param  list<array{label: string, current: array{amount: float, display: string}|null, previous: array{amount: float, display: string}|null}>  $parts
     * @return array<string, mixed>|null
     */
    private function comparison(string $title, string $note, string $previous, string $current, array $parts): ?array
    {
        $colors = $this->colors($parts);
        $pies = array_values(array_filter([
            $this->pie($previous, $parts, 'previous', $colors),
            $this->pie($current, $parts, 'current', $colors),
        ]));

        if ($pies === []) {
            return null;
        }

        $chart = ['title' => $title, 'note' => $note, 'pies' => $pies];
        $changes = $this->changes($parts);

        if ($changes !== []) {
            $chart['changes'] = $changes;
        }

        return $chart;
    }

    /**
     * @param  list<array{label: string, current: array{amount: float, display: string}|null, previous: array{amount: float, display: string}|null}>  $parts
     * @return list<array{label: string, previous: string, current: string, change: string, direction: string}>
     */
    private function changes(array $parts): array
    {
        $changes = [];

        foreach ($parts as $part) {
            $current = $part['current'];
            $previous = $part['previous'];

            if ($current === null || $previous === null || $previous['amount'] <= 0) {
                if ($current !== null || $previous !== null) {
                    $changes[] = [
                        'label' => $part['label'],
                        'previous' => $previous['display'] ?? '—',
                        'current' => $current['display'] ?? '—',
                        'change' => '—',
                        'direction' => 'flat',
                    ];
                }

                continue;
            }

            $delta = (($current['amount'] - $previous['amount']) / $previous['amount']) * 100;
            $formatted = rtrim(rtrim(number_format($delta, 1, '.', ''), '0'), '.');

            if ($delta > 0) {
                $formatted = '+'.$formatted;
            }

            $changes[] = [
                'label' => $part['label'],
                'previous' => $previous['display'],
                'current' => $current['display'],
                'change' => $formatted.'%',
                'direction' => $delta > 0 ? 'up' : ($delta < 0 ? 'down' : 'flat'),
            ];
        }

        return $changes;
    }

    /**
     * @param  list<array{label: string, current: array{amount: float, display: string}|null, previous: array{amount: float, display: string}|null}>  $parts
     * @return array<string, mixed>|null
     */
    private function single(string $title, string $note, string $label, array $parts): ?array
    {
        $pie = $this->pie($label, $parts, 'current', $this->colors($parts));

        if ($pie === null) {
            return null;
        }

        return ['title' => $title, 'note' => $note, 'pies' => [$pie]];
    }

    /**
     * @param  list<array{label: string, current: array{amount: float, display: string}|null, previous: array{amount: float, display: string}|null}>  $parts
     * @param  array<string, string>  $colors
     * @return array{label: string, slices: list<array{label: string, amount: float, display: string, color: string, path: string}>}|null
     */
    private function pie(string $label, array $parts, string $side, array $colors): ?array
    {
        $slices = [];

        foreach ($parts as $part) {
            $value = $part[$side] ?? null;

            if ($value === null || $value['amount'] <= 0) {
                continue;
            }

            $slices[] = [
                'label' => $part['label'],
                'amount' => $value['amount'],
                'display' => $value['display'],
                'color' => $colors[$part['label']],
            ];
        }

        $total = array_sum(array_column($slices, 'amount'));

        if ($slices === [] || $total <= 0) {
            return null;
        }

        $angle = -90.0;

        foreach ($slices as $index => $slice) {
            $sweep = ($slice['amount'] / $total) * 360;
            $slices[$index]['path'] = $this->slicePath($angle, $sweep);
            $angle += $sweep;
        }

        return ['label' => $label, 'slices' => $slices];
    }

    /**
     * @param  list<array{label: string, current: mixed, previous: mixed}>  $parts
     * @return array<string, string>
     */
    private function colors(array $parts): array
    {
        $colors = [];

        foreach ($parts as $index => $part) {
            $colors[$part['label']] = self::COLORS[$index % count(self::COLORS)];
        }

        return $colors;
    }

    private function slicePath(float $start, float $sweep): string
    {
        $cx = 60;
        $cy = 60;
        $radius = 52;

        if ($sweep >= 359.99) {
            return sprintf(
                'M %.2F %.2F m -%.2F 0 a %.2F %.2F 0 1 1 %.2F 0 a %.2F %.2F 0 1 1 -%.2F 0',
                $cx,
                $cy,
                $radius,
                $radius,
                $radius,
                $radius * 2,
                $radius,
                $radius,
                $radius * 2,
            );
        }

        $startRad = deg2rad($start);
        $endRad = deg2rad($start + $sweep);
        $large = $sweep > 180 ? 1 : 0;

        return sprintf(
            'M %.2F %.2F L %.2F %.2F A %.2F %.2F 0 %d 1 %.2F %.2F Z',
            $cx,
            $cy,
            $cx + ($radius * cos($startRad)),
            $cy + ($radius * sin($startRad)),
            $radius,
            $radius,
            $large,
            $cx + ($radius * cos($endRad)),
            $cy + ($radius * sin($endRad)),
        );
    }

    /**
     * @param  callable(string): ?float  $parse
     * @return array{current: array{amount: float, display: string}|null, previous: array{amount: float, display: string}|null}|null
     */
    private function pair(mixed $current, mixed $previous, callable $parse): ?array
    {
        $currentSide = $this->side($current, $parse);
        $previousSide = $this->side($previous, $parse);

        if ($currentSide === null && $previousSide === null) {
            return null;
        }

        return ['current' => $currentSide, 'previous' => $previousSide];
    }

    /**
     * @param  callable(string): ?float  $parse
     * @return array{amount: float, display: string}|null
     */
    private function side(mixed $value, callable $parse): ?array
    {
        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        $amount = $parse(trim($value));

        if ($amount === null) {
            return null;
        }

        return ['amount' => $amount, 'display' => trim($value)];
    }

    private function trendAmount(WeeklyReport $report, string $metric): ?float
    {
        $platforms = data_get($report->body, 'platforms');
        $parts = match ($metric) {
            'clicks' => $this->platformParts(is_array($platforms) ? $platforms : []),
            'enquiries' => $this->enquiryParts($report),
            default => $report->isSocial() ? $this->socialViewParts($report) : $this->viewParts($report),
        };

        return $this->sumCurrent($parts);
    }

    /**
     * @return list<array{label: string, current: array{amount: float, display: string}|null, previous: array{amount: float, display: string}|null}>
     */
    private function socialViewParts(WeeklyReport $report): array
    {
        $body = is_array($report->body) ? $report->body : [];
        $parts = [];

        foreach (['facebook' => 'Facebook', 'instagram' => 'Instagram'] as $key => $label) {
            $part = $this->viewPart($label, $this->metricValue(data_get($body, $key.'.metrics'), 'Views'));

            if ($part !== null) {
                $parts[] = $part;
            }
        }

        foreach (data_get($body, 'youtube', []) as $channel) {
            if (! is_array($channel)) {
                continue;
            }

            $part = $this->viewPart('YouTube', $this->metricValue($channel['metrics'] ?? [], 'Views'));

            if ($part !== null) {
                $parts[] = $part;
            }
        }

        return $parts;
    }

    /**
     * @return list<array{label: string, current: array{amount: float, display: string}|null, previous: array{amount: float, display: string}|null}>
     */
    private function enquiryParts(WeeklyReport $report): array
    {
        $body = is_array($report->body) ? $report->body : [];
        $parts = [];

        foreach (['WhatsApp Clicks', 'Call Clicks', 'Form Submissions'] as $label) {
            $part = $this->viewPart($label, $this->currentFigure($body, $label));

            if ($part !== null) {
                $parts[] = $part;
            }
        }

        return $parts;
    }

    /**
     * @param  list<array{label: string, current: array{amount: float, display: string}|null, previous: array{amount: float, display: string}|null}>  $parts
     */
    private function sumCurrent(array $parts): ?float
    {
        $total = 0.0;
        $found = false;

        foreach ($parts as $part) {
            if ($part['current'] === null) {
                continue;
            }

            $found = true;
            $total += $part['current']['amount'];
        }

        return $found ? $total : null;
    }

    private function trendAxisLabel(WeeklyReport $report): string
    {
        if ($report->period_start === null) {
            return $report->currentPeriodLabel();
        }

        if ($report->isMonthly()) {
            return $report->period_start->format('M Y');
        }

        return $report->period_start->format('j M');
    }

    private function displayAmount(float $amount): string
    {
        if (abs($amount - round($amount)) < 0.05) {
            return number_format($amount);
        }

        return number_format($amount, 1);
    }

    /**
     * @param  list<array{label: string, color: string, points: list<array{label: string, full: string, amount: float|null, display: string|null}>}>  $series
     * @return array{
     *     width: int,
     *     height: int,
     *     plot_left: int,
     *     plot_right: int,
     *     ticks: list<array{y: float, label: string}>,
     *     labels: list<array{x: float, text: string}>,
     *     series: list<array{label: string, color: string, lines: list<string>, dots: list<array{cx: float, cy: float, label: string, display: string}>, points: list<array{label: string, full: string, amount: float|null, display: string|null}>}>
     * }
     */
    private function plotTrend(array $series): array
    {
        $count = count($series[0]['points']);
        $max = 0.0;

        foreach ($series as $item) {
            foreach ($item['points'] as $point) {
                if ($point['amount'] !== null) {
                    $max = max($max, $point['amount']);
                }
            }
        }

        $max = $max <= 0 ? 1.0 : $max;
        $width = 640;
        $height = 220;
        $left = 56;
        $right = 16;
        $top = 18;
        $bottom = 36;
        $plotWidth = $width - $left - $right;
        $plotHeight = $height - $top - $bottom;
        $xAt = function (int $index) use ($count, $left, $plotWidth): float {
            if ($count <= 1) {
                return $left + ($plotWidth / 2);
            }

            return $left + (($index / ($count - 1)) * $plotWidth);
        };
        $yAt = function (float $amount) use ($max, $top, $plotHeight): float {
            return $top + $plotHeight - (($amount / $max) * $plotHeight);
        };
        $labels = [];

        foreach ($series[0]['points'] as $index => $point) {
            $labels[] = ['x' => round($xAt($index), 1), 'text' => $point['label']];
        }

        $ticks = [];

        foreach ([0.0, 0.5, 1.0] as $fraction) {
            $ticks[] = [
                'y' => round($yAt($max * $fraction), 1),
                'label' => $this->displayAmount($max * $fraction),
            ];
        }

        foreach ($series as $index => $item) {
            $lines = [];
            $dots = [];
            $segment = [];

            foreach ($item['points'] as $pointIndex => $point) {
                if ($point['amount'] === null) {
                    if (count($segment) >= 2) {
                        $lines[] = implode(' ', $segment);
                    }

                    $segment = [];

                    continue;
                }

                $x = round($xAt($pointIndex), 1);
                $y = round($yAt($point['amount']), 1);
                $segment[] = $x.','.$y;
                $dots[] = [
                    'cx' => $x,
                    'cy' => $y,
                    'label' => $point['full'],
                    'display' => (string) $point['display'],
                ];
            }

            if (count($segment) >= 2) {
                $lines[] = implode(' ', $segment);
            }

            $series[$index]['lines'] = $lines;
            $series[$index]['dots'] = $dots;
        }

        return [
            'width' => $width,
            'height' => $height,
            'plot_left' => $left,
            'plot_right' => $right,
            'ticks' => $ticks,
            'labels' => $labels,
            'series' => $series,
        ];
    }

    private function quantity(string $value): ?float
    {
        $value = str_replace([',', ' '], '', trim($value));

        if (preg_match('/^(\d+(?:\.\d+)?)([kKmM])?$/', $value, $matches) !== 1) {
            return null;
        }

        $amount = (float) $matches[1];

        return match (strtolower($matches[2] ?? '')) {
            'k' => $amount * 1000,
            'm' => $amount * 1000000,
            default => $amount,
        };
    }

    private function minutes(string $value): ?float
    {
        $value = trim($value);

        if (preg_match('/^(\d+(?:\.\d+)?)\s*hours?$/i', $value, $matches) === 1) {
            return (float) $matches[1] * 60;
        }

        $total = 0.0;
        $matched = false;

        if (preg_match('/(\d+(?:\.\d+)?)\s*hours?/i', $value, $matches) === 1) {
            $total += (float) $matches[1] * 60;
            $matched = true;
        }

        if (preg_match('/(\d+)\s*minutes?/i', $value, $matches) === 1) {
            $total += (int) $matches[1];
            $matched = true;
        }

        if (preg_match('/(\d+)\s*seconds?/i', $value, $matches) === 1) {
            $total += ((int) $matches[1]) / 60;
            $matched = true;
        }

        return $matched ? $total : null;
    }
}
