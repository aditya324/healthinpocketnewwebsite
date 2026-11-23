<?php

namespace App\Support;

use Illuminate\Support\Carbon;
use InvalidArgumentException;
use Smalot\PdfParser\Parser;

class WeeklyReportPdfReader
{
    /**
     * @return array{
     *     title: string,
     *     period_start: string,
     *     period_end: string,
     *     previous_start: string,
     *     previous_end: string,
     *     facebook: array<string, mixed>,
     *     instagram: array<string, mixed>,
     *     youtube: list<array{name: string, current: array<string, string|null>, previous: array<string, string|null>}>,
     *     narrative: array<string, array<string, string|null>>
     * }
     */
    public function read(string $path, string $kind = 'weekly'): array
    {
        try {
            $text = (new Parser)->parseFile($path)->getText();
        } catch (\Throwable) {
            throw new InvalidArgumentException($this->unreadable($kind));
        }

        return $this->parse(is_string($text) ? $text : '', $kind);
    }

    /**
     * @return array{
     *     title: string,
     *     period_start: string,
     *     period_end: string,
     *     previous_start: string,
     *     previous_end: string,
     *     facebook: array<string, mixed>,
     *     instagram: array<string, mixed>,
     *     youtube: list<array{name: string, current: array<string, string|null>, previous: array<string, string|null>}>,
     *     narrative: array<string, array<string, string|null>>
     * }
     */
    public function parse(string $text, string $kind = 'weekly'): array
    {
        $lines = $this->lines($text);
        $header = $this->header($lines);

        if ($header === null) {
            throw new InvalidArgumentException($this->unreadable($kind));
        }

        $facebook = [
            'current' => $this->blank(WeeklyReportFields::facebookMetrics(), true),
            'previous' => $this->blank(WeeklyReportFields::facebookMetrics()),
            'content' => $this->blank(WeeklyReportFields::facebookContent()),
            'views_by_format' => $this->blank(WeeklyReportFields::facebookViewsByFormat()),
            'interactions_by_format' => $this->blank(WeeklyReportFields::facebookInteractionsByFormat()),
        ];
        $instagram = [
            'current' => $this->blank(WeeklyReportFields::instagramMetrics()),
            'previous' => $this->blank(WeeklyReportFields::instagramMetrics()),
            'top' => [],
        ];
        $youtube = [];
        $narrative = [];

        foreach (array_keys(WeeklyReportFields::narrativeSections()) as $section) {
            $narrative[$section] = [];

            foreach (array_keys(WeeklyReportFields::narrativePlatforms()) as $platform) {
                $narrative[$section][$platform] = null;
            }
        }

        $section = null;
        $subsection = null;
        $channelName = null;

        foreach ($lines as $line) {
            $heading = $this->heading($line);

            if ($heading !== null) {
                [$kind, $name] = $heading;

                if ($kind === 'skip' || ($kind === 'current_week' && preg_match('/\d{4}/', $line) === 1) || ($kind === 'previous_week' && preg_match('/\d{4}/', $line) === 1)) {
                    continue;
                }

                if ($kind === 'facebook') {
                    $section = 'facebook';
                    $subsection = null;
                } elseif ($kind === 'instagram') {
                    $section = 'instagram';
                    $subsection = null;
                } elseif ($kind === 'youtube') {
                    if (count($youtube) >= 6) {
                        $section = null;
                        $subsection = null;

                        continue;
                    }

                    $section = 'youtube';
                    $subsection = null;
                    $youtube[] = [
                        'name' => $name ?? 'Channel',
                        'current' => $this->blank(WeeklyReportFields::youtubeMetrics()),
                        'previous' => $this->blank(WeeklyReportFields::youtubeMetrics()),
                    ];
                } elseif (in_array($kind, ['working', 'not_working', 'comparison'], true)) {
                    $section = $kind;
                    $subsection = null;
                    $channelName = null;
                } elseif ($kind === 'platform' && in_array($section, ['working', 'not_working', 'comparison'], true)) {
                    $subsection = $name;
                    $channelName = null;
                } elseif ($kind === 'platform_youtube' && in_array($section, ['working', 'not_working', 'comparison'], true)) {
                    $subsection = 'youtube';
                    $channelName = $name;
                } elseif (in_array($section, ['facebook', 'instagram', 'youtube'], true) && in_array($kind, ['current_week', 'previous_week', 'content', 'views_by_format', 'interactions_by_format', 'top'], true)) {
                    $subsection = $kind === 'current_week' ? 'current' : ($kind === 'previous_week' ? 'previous' : $kind);
                }

                continue;
            }

            $metric = $this->metric($line);

            if ($metric === null) {
                continue;
            }

            if ($section === 'facebook' && isset($facebook[$subsection ?? ''])) {
                $this->assign($facebook[$subsection], $this->facebookKey($subsection, $metric['label']), $metric);

                continue;
            }

            if ($section === 'instagram' && $subsection === 'top') {
                if (count($instagram['top']) < 8) {
                    $instagram['top'][] = [
                        'title' => $metric['label'],
                        'views' => preg_replace('/\s+views$/iu', '', $metric['value']) ?? $metric['value'],
                    ];
                }

                continue;
            }

            if ($section === 'instagram' && in_array($subsection, ['current', 'previous'], true)) {
                $this->assign($instagram[$subsection], $this->instagramKey($metric['label']), $metric);

                continue;
            }

            if ($section === 'youtube' && $youtube !== [] && in_array($subsection, ['current', 'previous'], true)) {
                $index = array_key_last($youtube);
                $this->assign($youtube[$index][$subsection], $this->youtubeKey($metric['label']), $metric);

                continue;
            }

            if (in_array($section, ['working', 'not_working', 'comparison'], true) && is_string($subsection)) {
                $text = $metric['value'] === '' ? $metric['label'] : $metric['label'].': '.$metric['value'];

                if ($text === '') {
                    continue;
                }

                if ($channelName !== null) {
                    $text = $channelName."\n".$text;
                    $channelName = null;
                }

                $narrative[$section][$subsection] = $narrative[$section][$subsection] === null
                    ? $text
                    : $narrative[$section][$subsection]."\n".$text;
            }
        }

        return [
            'title' => $header['title'],
            'period_start' => $header['period_start'],
            'period_end' => $header['period_end'],
            'previous_start' => $header['previous_start'],
            'previous_end' => $header['previous_end'],
            'facebook' => $facebook,
            'instagram' => $instagram,
            'youtube' => $youtube,
            'narrative' => $narrative,
        ];
    }

    /**
     * Narrative bullets are sentences, not "label: value" metrics.
     * A line that is only a sentence still needs to be kept.
     *
     * @param  list<string>  $lines
     * @return array{title: string, period_start: string, period_end: string, previous_start: string, previous_end: string}|null
     */
    private function header(array $lines): ?array
    {
        $current = null;
        $previous = null;
        $title = null;

        foreach ($lines as $index => $line) {
            if ($current === null && preg_match('/^Current (?:Week|Month):\s*(.+)$/u', $line, $matches) === 1) {
                $current = $this->range($matches[1]);
                $title = $this->title($lines, $index);
            }

            if ($previous === null && preg_match('/^Previous (?:Week|Month):\s*(.+)$/u', $line, $matches) === 1) {
                $previous = $this->range($matches[1]);
            }
        }

        if ($title === null || $current === null || $previous === null) {
            return null;
        }

        return [
            'title' => $title,
            'period_start' => $current[0],
            'period_end' => $current[1],
            'previous_start' => $previous[0],
            'previous_end' => $previous[1],
        ];
    }

    /**
     * @param  list<string>  $lines
     */
    private function title(array $lines, int $index): ?string
    {
        $parts = [];

        for ($cursor = $index - 1; $cursor >= 0 && count($parts) < 3; $cursor--) {
            $line = $lines[$cursor];

            if ($this->heading($line) !== null || $this->isBullet($line)) {
                break;
            }

            array_unshift($parts, $line);

            if (str_contains($line, 'Weekly SMM') || str_contains($line, 'Monthly SMM')) {
                break;
            }
        }

        $title = trim(implode(' ', $parts));

        if ($title === '' || ! str_contains($title, 'Performance Report')) {
            return null;
        }

        return $title;
    }

    /**
     * @return array{0: string, 1: string}|null
     */
    private function range(string $value): ?array
    {
        $value = trim($value);
        $months = 'January|February|March|April|May|June|July|August|September|October|November|December';
        $dash = '[–—-]';

        if (preg_match('/^(\d{1,2})\s*'.$dash.'\s*(\d{1,2})\s+('.$months.')\s+(\d{4})$/iu', $value, $matches) === 1) {
            return [
                $this->date($matches[1], $matches[3], $matches[4]),
                $this->date($matches[2], $matches[3], $matches[4]),
            ];
        }

        if (preg_match('/^(\d{1,2})\s+('.$months.')\s+(\d{4})\s*'.$dash.'\s*(\d{1,2})\s+('.$months.')\s+(\d{4})$/iu', $value, $matches) === 1) {
            return [
                $this->date($matches[1], $matches[2], $matches[3]),
                $this->date($matches[4], $matches[5], $matches[6]),
            ];
        }

        if (preg_match('/^('.$months.')\s+(\d{4})$/iu', $value, $matches) === 1) {
            $start = Carbon::parse('1 '.$matches[1].' '.$matches[2])->startOfMonth();

            return [$start->toDateString(), $start->copy()->endOfMonth()->toDateString()];
        }

        return null;
    }

    private function date(string $day, string $month, string $year): string
    {
        return Carbon::parse($day.' '.$month.' '.$year)->toDateString();
    }

    /**
     * @return list<string>
     */
    private function lines(string $text): array
    {
        $text = preg_replace('/\p{Cf}/u', '', $text) ?? $text;
        $text = str_replace(["\u{00A0}", "\t"], ' ', $text);
        $raw = preg_split("/\r\n|\n|\r/", $text) ?: [];
        $lines = [];
        $break = false;

        foreach ($raw as $line) {
            $line = trim(preg_replace('/\s+/u', ' ', $line) ?? $line);

            if ($line === '') {
                $break = true;

                continue;
            }

            if ($lines === [] || $break || $this->heading($line) !== null || $this->isBullet($line)) {
                $lines[] = $line;
                $break = false;

                continue;
            }

            $lines[array_key_last($lines)] .= ' '.$line;
        }

        return $lines;
    }

    /**
     * @return array{0: string, 1: ?string}|null
     */
    private function heading(string $line): ?array
    {
        if (preg_match('/^(?:\d+\.\s*)?What Is Working\??$/iu', $line) === 1) {
            return ['working', null];
        }

        if (preg_match('/^(?:\d+\.\s*)?What Is Not Working\??$/iu', $line) === 1) {
            return ['not_working', null];
        }

        if (preg_match('/^(?:\d+\.\s*)?Comparison With Last Week$/iu', $line) === 1) {
            return ['comparison', null];
        }

        if (preg_match('/^Current (?:Week|Month)\b/iu', $line) === 1) {
            return ['current_week', null];
        }

        if (preg_match('/^Previous (?:Week|Month)\b/iu', $line) === 1) {
            return ['previous_week', null];
        }

        if (preg_match('/^Content Published:?$/iu', $line) === 1) {
            return ['content', null];
        }

        if (preg_match('/^Views by Content Format:?$/iu', $line) === 1) {
            return ['views_by_format', null];
        }

        if (preg_match('/^Content Interactions by Format:?$/iu', $line) === 1) {
            return ['interactions_by_format', null];
        }

        if (preg_match('/^Top-Performing Content:?$/iu', $line) === 1) {
            return ['top', null];
        }

        if (preg_match('/^(?:[A-Z]\.\s*)?Facebook Performance\b/iu', $line) === 1) {
            return ['facebook', null];
        }

        if (preg_match('/^(?:[A-Z]\.\s*)?Instagram Performance\b/iu', $line) === 1) {
            return ['instagram', null];
        }

        if (preg_match('/^(?:[A-Z]\.\s*)?YouTube Performance\s*[–—-]\s*(.+)$/iu', $line, $matches) === 1) {
            return ['youtube', trim($matches[1])];
        }

        if (preg_match('/^YouTube\s*[–—-]\s*(.+)$/iu', $line, $matches) === 1) {
            return ['platform_youtube', trim($matches[1])];
        }

        if (preg_match('/^Facebook$/iu', $line) === 1) {
            return ['platform', 'facebook'];
        }

        if (preg_match('/^Instagram$/iu', $line) === 1) {
            return ['platform', 'instagram'];
        }

        if (preg_match('/^\d+\.\s+\S/u', $line) === 1) {
            return ['skip', null];
        }

        return null;
    }

    private function isBullet(string $line): bool
    {
        return preg_match('/^[●•]\s*/u', $line) === 1 || str_starts_with($line, '- ');
    }

    /**
     * @return array{label: string, value: string, change: ?string}|null
     */
    private function metric(string $line): ?array
    {
        $line = preg_replace('/^[●•]\s*/u', '', $line) ?? $line;
        $line = preg_replace('/^-\s+/', '', $line) ?? $line;

        if (! str_contains($line, ':')) {
            if ($line === '') {
                return null;
            }

            return ['label' => $line, 'value' => '', 'change' => null];
        }

        [$label, $rest] = array_map('trim', explode(':', $line, 2));

        if ($label === '') {
            return null;
        }

        $change = null;

        if (preg_match('/^(.*?)\s*\(([+-]\d+(?:\.\d+)?%)(?:,[^)]*)?\)\s*$/u', $rest, $matches) === 1) {
            $value = trim($matches[1]);
            $change = $matches[2];
        } else {
            $value = trim(preg_replace('/\s*\([^)]*(?:as displayed|displayed)[^)]*\)\s*$/iu', '', $rest) ?? $rest);
        }

        return ['label' => $label, 'value' => $value, 'change' => $change];
    }

    /**
     * @param  array<string, string|null>  $target
     * @param  array{label: string, value: string, change: ?string}  $metric
     */
    private function assign(array &$target, ?string $key, array $metric): void
    {
        if ($key === null || $metric['value'] === '') {
            return;
        }

        $target[$key] = $metric['value'];

        if ($metric['change'] !== null && array_key_exists($key.'_change', $target)) {
            $target[$key.'_change'] = $metric['change'];
        }
    }

    private function facebookKey(?string $subsection, string $label): ?string
    {
        $label = $this->label($label);

        if ($subsection === 'content') {
            return match ($label) {
                'reels' => 'reels',
                'stories' => 'stories',
                'photos' => 'photos',
                'total content' => 'total',
                default => null,
            };
        }

        if ($subsection === 'views_by_format') {
            return match ($label) {
                'reels' => 'reels',
                'photos' => 'photos',
                'links' => 'links',
                'stories' => 'stories',
                'multi-photo' => 'multi_photo',
                default => null,
            };
        }

        if ($subsection === 'interactions_by_format') {
            return match ($label) {
                'photos' => 'photos',
                'reels' => 'reels',
                'stories' => 'stories',
                default => null,
            };
        }

        return match ($label) {
            'views' => 'views',
            '3-second views' => 'three_second_views',
            'content interactions' => 'content_interactions',
            'watch time' => 'watch_time',
            'viewers' => 'viewers',
            'organic views' => 'organic_views',
            'ad views' => 'ad_views',
            default => null,
        };
    }

    private function instagramKey(string $label): ?string
    {
        return match ($this->label($label)) {
            'total views' => 'total_views',
            'instagram views' => 'instagram_views',
            'facebook views' => 'facebook_views',
            'reach' => 'reach',
            'content interactions' => 'content_interactions',
            'organic views' => 'organic_views',
            'ad views' => 'ad_views',
            'posts published' => 'posts_published',
            'stories published' => 'stories_published',
            default => null,
        };
    }

    private function youtubeKey(string $label): ?string
    {
        return match ($this->label($label)) {
            'views' => 'views',
            'engaged views' => 'engaged_views',
            'unique viewers' => 'unique_viewers',
            'new viewers' => 'new_viewers',
            'stayed to watch' => 'stayed_to_watch',
            'likes' => 'likes',
            'post likes' => 'post_likes',
            'subscribers gained' => 'subscribers_gained',
            'subscribers lost' => 'subscribers_lost',
            'watch time' => 'watch_time',
            'subscribers' => 'subscribers',
            default => null,
        };
    }

    private function label(string $label): string
    {
        $label = mb_strtolower(trim($label));

        return preg_replace('/\s+/u', ' ', $label) ?? $label;
    }

    /**
     * @param  array<string, string>  $fields
     * @return array<string, string|null>
     */
    private function blank(array $fields, bool $changes = false): array
    {
        $values = [];

        foreach (array_keys($fields) as $key) {
            $values[$key] = null;

            if ($changes) {
                $values[$key.'_change'] = null;
            }
        }

        return $values;
    }

    private function unreadable(string $kind = 'weekly'): string
    {
        $period = $kind === 'monthly' ? 'month' : 'week';
        $label = $kind === 'monthly' ? 'monthly' : 'weekly';

        return "This PDF does not look like a {$label} SMM report. It needs a title, the current {$period}, and the previous {$period}.";
    }
}
