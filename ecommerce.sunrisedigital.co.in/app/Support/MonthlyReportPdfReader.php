<?php

namespace App\Support;

use Illuminate\Support\Carbon;
use InvalidArgumentException;
use Smalot\PdfParser\Parser;

class MonthlyReportPdfReader
{
    private const MONTHS = 'January|February|March|April|May|June|July|August|September|October|November|December';

    /**
     * @return array{
     *     title: string,
     *     period_start: string,
     *     period_end: string,
     *     previous_start: string,
     *     previous_end: string,
     *     facebook: array<string, mixed>,
     *     instagram: array<string, mixed>,
     *     youtube: list<mixed>,
     *     narrative: array<string, mixed>,
     *     body: array<string, mixed>
     * }
     */
    public function read(string $path, string $kind): array
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
     *     youtube: list<mixed>,
     *     narrative: array<string, mixed>,
     *     body: array<string, mixed>
     * }
     */
    public function parse(string $text, string $kind): array
    {
        $text = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $text) ?? $text;

        return $kind === 'seo' ? $this->parseSeo($text) : $this->parseSocial($text);
    }

    /**
     * @return array<string, mixed>
     */
    private function parseSocial(string $text): array
    {
        $when = $this->monthYear($text);

        if ($when === null) {
            throw new InvalidArgumentException($this->unreadable('social'));
        }

        $buckets = [
            'instagram' => [],
            'facebook' => [],
            'content' => [],
            'worked' => [],
            'next' => [],
            'profiles' => [],
            'skip' => [],
        ];
        $youtubeLines = [];
        $profileLines = [];
        $current = null;

        foreach ($this->lines($text) as $line) {
            if ($this->isFooter($line)) {
                continue;
            }

            $heading = $this->socialHeading($line);

            if ($heading !== null) {
                $current = $heading;

                continue;
            }

            if ($current === null) {
                continue;
            }

            if ($current['bucket'] === 'youtube') {
                $youtubeLines[$current['name']][] = $line;
            } elseif ($current['bucket'] === 'profile') {
                $profileLines[$current['name']][] = $line;
            } else {
                $buckets[$current['bucket']][] = $line;
            }
        }

        $instagram = $this->readMetrics($buckets['instagram']);
        $facebook = $this->readMetrics($buckets['facebook']);
        $profiles = [];
        $overview = $this->readMetrics($buckets['profiles']);

        foreach ($overview['metrics'] as $metric) {
            if (! is_string($metric['place'])) {
                continue;
            }

            $this->addMetric($profiles, $metric['place'], $metric['label'], $metric['value']);
        }

        foreach ($profileLines as $name => $lines) {
            $parsed = $this->readMetrics($lines);

            foreach ($parsed['metrics'] as $metric) {
                $this->addMetric($profiles, $name, $metric['label'], $metric['value']);
            }

            $this->addNotes($profiles, $name, $parsed['notes']);
        }

        $channels = [];

        foreach ($youtubeLines as $name => $lines) {
            $parsed = $this->readMetrics($lines);
            $channels[] = [
                'name' => $name,
                'metrics' => $this->plainMetrics($parsed['metrics']),
                'notes' => $parsed['notes'],
            ];
        }

        $body = [
            'instagram' => [
                'metrics' => $this->plainMetrics($instagram['metrics']),
                'notes' => $instagram['notes'],
            ],
            'facebook' => [
                'metrics' => $this->plainMetrics($facebook['metrics']),
                'notes' => $facebook['notes'],
            ],
            'youtube' => $channels,
            'profiles' => $profiles,
            'content' => $this->readMetrics($buckets['content'])['notes'],
            'worked' => $this->readMetrics($buckets['worked'])['notes'],
            'next' => $this->readMetrics($buckets['next'])['notes'],
        ];

        $filled = $body['instagram']['metrics'] !== []
            || $body['facebook']['metrics'] !== []
            || $channels !== []
            || $profiles !== [];

        if (! $filled) {
            throw new InvalidArgumentException($this->unreadable('social'));
        }

        return $this->envelope(
            $this->clientName($text).' – '.$when['month'].' '.$when['year'].' Social Media Report',
            $when,
            null,
            $body,
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function parseSeo(string $text): array
    {
        $when = $this->monthYear($text);
        $compared = $this->comparedMonth($text, $when['month'] ?? '', $when['year'] ?? 0);

        if ($when === null) {
            throw new InvalidArgumentException($this->unreadable('seo'));
        }

        $section = null;
        $comparison = [];
        $analytics = [];
        $technical = [];
        $technicalNote = '';
        $ai = [];
        $search = [];
        $platforms = [];
        $pendingPlatform = null;
        $activityLines = [];
        $summary = [];
        $nextLines = [];
        $priorityLines = [];
        $linking = [];
        $competitor = [];
        $lines = $this->lines($text);

        for ($index = 0, $count = count($lines); $index < $count; $index++) {
            $line = $lines[$index];

            if ($line === '' || $this->isFooter($line)) {
                continue;
            }

            if (preg_match('/^\d+\.\s+(.+)$/', $line, $numbered) === 1) {
                $aside = $numbered[1];

                while (isset($lines[$index + 1]) && preg_match('/^\d+\.\s+/', $lines[$index + 1]) !== 1 && ! $this->isFooter($lines[$index + 1]) && $this->seoSection($lines[$index + 1]) === null && ! str_ends_with($aside, '.')) {
                    $index++;
                    $aside .= ' '.$lines[$index];
                }

                $this->fileAside($aside, $linking, $competitor);

                continue;
            }

            $heading = $this->seoSection($line);

            if ($heading !== null && ! str_contains($line, "\t")) {
                $section = $heading;

                continue;
            }

            if (preg_match('/^Platform\t+Clicks/i', $line) === 1) {
                $section = 'platforms';

                continue;
            }

            if (preg_match('/\b(SEO Activity|On-Page SEO Activity)\b/i', $line) === 1) {
                $section = 'activities';

                continue;
            }

            if ($this->consumePlatform($line, $platforms, $pendingPlatform)) {
                continue;
            }

            if ($section === 'activities') {
                $activityLines[] = $line;

                continue;
            }

            if ($section === 'summary') {
                $this->pushSummary($summary, $line);

                continue;
            }

            if ($section === 'next') {
                $nextLines[] = $line;

                continue;
            }

            if ($section === 'priorities') {
                $priorityLines[] = $line;

                continue;
            }

            if (in_array($section, ['linking', 'competitor'], true)) {
                continue;
            }

            if ($section === 'technical' && preg_match('/sitemap/i', $line) === 1 && strlen($line) > 30) {
                $technicalNote = trim($technicalNote.' '.$line);

                continue;
            }

            if ($section === 'technical' && $technicalNote !== '' && ! str_ends_with($technicalNote, '.') && ! str_contains($line, "\t") && strlen($line) > 15) {
                $technicalNote = trim($technicalNote.' '.$line);

                continue;
            }

            $this->storeSeoRow($section, $line, $lines, $index, $comparison, $analytics, $technical, $ai, $search);
        }

        if (count($comparison) < 3) {
            throw new InvalidArgumentException($this->unreadable('seo'));
        }

        $body = [
            'comparison' => $comparison,
            'analytics' => $analytics,
            'technical' => $technical,
            'technical_note' => $this->tidy($technicalNote),
            'ai' => $ai,
            'search' => $search,
            'platforms' => $platforms,
            'activities' => $this->activities($activityLines),
            'summary' => $summary,
            'next' => $this->nextItems($nextLines),
            'priorities' => $this->priorities($priorityLines),
            'linking' => $linking,
            'competitor' => $competitor,
        ];

        return $this->envelope(
            $this->clientName($text).' – '.$when['month'].' '.$when['year'].' SEO Performance Report',
            $when,
            $compared,
            $body,
        );
    }

    /**
     * @param  array{month: string, year: int}  $when
     * @param  array{month: string, year: int}|null  $compared
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    private function envelope(string $title, array $when, ?array $compared, array $body): array
    {
        $start = Carbon::createFromDate($when['year'], $this->monthNumber($when['month']), 1)->startOfMonth();
        $previous = $compared === null
            ? $start->copy()->subMonth()
            : Carbon::createFromDate($compared['year'], $this->monthNumber($compared['month']), 1)->startOfMonth();

        return [
            'title' => $title,
            'period_start' => $start->toDateString(),
            'period_end' => $start->copy()->endOfMonth()->toDateString(),
            'previous_start' => $previous->toDateString(),
            'previous_end' => $previous->copy()->endOfMonth()->toDateString(),
            'facebook' => [],
            'instagram' => [],
            'youtube' => [],
            'narrative' => [],
            'body' => $body,
        ];
    }

    /**
     * @return list<string>
     */
    private function lines(string $text): array
    {
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        $raw = preg_split('/\n/', $text) ?: [];
        $lines = [];

        foreach ($raw as $line) {
            $line = trim(preg_replace('/[^\S\t]+/u', ' ', $line) ?? '');

            if ($line !== '') {
                $lines[] = $line;
            }
        }

        return $lines;
    }

    /**
     * @return array{month: string, year: int}|null
     */
    private function monthYear(string $text): ?array
    {
        $months = self::MONTHS;

        if (preg_match('/Reporting Period:\s*('.$months.')\s*(20\d{2})/i', $text, $matches) === 1) {
            return ['month' => ucfirst(strtolower($matches[1])), 'year' => (int) $matches[2]];
        }

        if (preg_match('/('.$months.')\s*(20\d{2})/i', $text, $matches) === 1) {
            return ['month' => ucfirst(strtolower($matches[1])), 'year' => (int) $matches[2]];
        }

        return null;
    }

    /**
     * @return array{month: string, year: int}|null
     */
    private function comparedMonth(string $text, string $currentMonth, int $year): ?array
    {
        if ($currentMonth === '' || preg_match('/Comparison:\s*('.self::MONTHS.')\s+vs\s+('.self::MONTHS.')/i', $text, $matches) !== 1) {
            return null;
        }

        $left = ucfirst(strtolower($matches[1]));
        $right = ucfirst(strtolower($matches[2]));
        $other = strcasecmp($left, $currentMonth) === 0 ? $right : $left;

        if (strcasecmp($other, $currentMonth) === 0) {
            return null;
        }

        $otherYear = $this->monthNumber($other) > $this->monthNumber($currentMonth) ? $year - 1 : $year;

        return ['month' => $other, 'year' => $otherYear];
    }

    private function monthNumber(string $month): int
    {
        return Carbon::parse($month.' 1')->month;
    }

    private function clientName(string $text): string
    {
        $compact = preg_replace('/[^a-z]/i', '', $text) ?? '';

        if (preg_match('/nanohospitals/i', $compact) === 1) {
            return 'Nano Hospitals';
        }

        $first = $this->lines($text)[0] ?? 'Report';
        $first = preg_replace('/(?<=[a-z])(?=[A-Z])/', ' ', $first) ?? $first;

        if ($first !== '' && mb_strtoupper($first) === $first) {
            $first = ucwords(strtolower($first));
        }

        return $first !== '' ? $first : 'Report';
    }

    /**
     * @return array{bucket: string, name?: string}|null
     */
    private function socialHeading(string $line): ?array
    {
        if (str_contains($line, "\t") || str_contains($line, '.') || strlen($line) > 48) {
            return null;
        }

        if (preg_match('/\b(the|during|recorded|generated|from)\b/i', $line) === 1) {
            return null;
        }

        $key = preg_replace('/[^a-z0-9]/', '', strtolower($line)) ?? '';

        if ($key === '') {
            return null;
        }

        if (str_contains($key, 'topperforming') || str_contains($key, '1kviews')) {
            return ['bucket' => 'skip'];
        }

        if (str_contains($key, 'youtube') && str_contains($key, 'bgroad')) {
            return ['bucket' => 'youtube', 'name' => 'BG Road'];
        }

        if (str_contains($key, 'youtube') && str_contains($key, 'uttarahalli')) {
            return ['bucket' => 'youtube', 'name' => 'Uttarahalli'];
        }

        if (str_contains($key, 'uttarahalli')) {
            return ['bucket' => 'profile', 'name' => 'Uttarahalli'];
        }

        if (str_contains($key, 'bgroad')) {
            return ['bucket' => 'profile', 'name' => 'BG Road'];
        }

        if (str_contains($key, 'googlebusiness')) {
            return ['bucket' => 'profiles'];
        }

        if (str_starts_with($key, 'whatworked')) {
            return ['bucket' => 'worked'];
        }

        if (str_starts_with($key, 'whatwere')) {
            return ['bucket' => 'next'];
        }

        if ($key === 'instagram' || (str_contains($key, 'instagram') && str_contains($key, 'performance'))) {
            return ['bucket' => 'instagram'];
        }

        if ($key === 'facebook' || (str_contains($key, 'facebook') && (str_contains($key, 'performance') || str_contains($key, 'page')))) {
            return ['bucket' => 'facebook'];
        }

        if (str_contains($key, 'content') && str_contains($key, 'performance')) {
            return ['bucket' => 'content'];
        }

        return null;
    }

    /**
     * @param  list<string>  $lines
     * @return array{metrics: list<array{label: string, value: string, place: ?string}>, notes: list<string>}
     */
    private function readMetrics(array $lines): array
    {
        $metrics = [];
        $notes = [];
        $pending = null;

        foreach ($lines as $line) {
            if ($this->isFooter($line)) {
                continue;
            }

            if (preg_match('/total reviews\D+(\d[\d,]*)\s*$/i', $line, $reviews) === 1) {
                $pending = null;
                $this->remember($metrics, 'Reviews', $reviews[1], null);

                continue;
            }

            $sameLine = $this->sameLineMetric($line);

            if ($sameLine !== null) {
                $pending = null;
                $this->remember($metrics, $sameLine['label'], $sameLine['value'], $sameLine['place']);

                continue;
            }

            $label = $this->canonicalLabel($line);

            if ($label !== null) {
                $pageNumber = $pending !== null && $pending['kind'] === 'value' && preg_match('/^\d{1,2}$/', $pending['value']) === 1;

                if (! $pageNumber && $pending !== null && $pending['kind'] === 'value' && $this->compatible($label['label'], $pending['value'])) {
                    $this->remember($metrics, $label['label'], $pending['value'], $label['place']);
                    $pending = null;
                } else {
                    $this->keepProse($pending, $notes);
                    $pending = ['kind' => 'label', 'label' => $label['label'], 'place' => $label['place']];
                }

                continue;
            }

            if ($this->isMetricValue($line)) {
                $tiny = preg_match('/^\d{1,2}$/', $line) === 1;

                if ($tiny && ! ($pending !== null && $pending['kind'] === 'label' && $this->smallCount($pending['label']))) {
                    continue;
                }

                if ($pending !== null && $pending['kind'] === 'label' && $this->compatible($pending['label'], $line)) {
                    $this->remember($metrics, $pending['label'], $line, $pending['place']);
                    $pending = null;
                } else {
                    $this->keepProse($pending, $notes);
                    $pending = ['kind' => 'value', 'value' => $line];
                }

                continue;
            }

            $text = ltrim($line, "•- \t");
            $fresh = str_starts_with(trim($line), '•') || str_starts_with(trim($line), '- ');

            if (! $fresh && $pending !== null && $pending['kind'] === 'prose' && ! preg_match('/[.!?]$/', $pending['text'])) {
                $pending['text'] .= ' '.$text;

                continue;
            }

            $this->keepProse($pending, $notes);
            $pending = ['kind' => 'prose', 'text' => $text];
        }

        $this->keepProse($pending, $notes);
        $notes = $this->cleanNotes($notes);
        $this->mineNotes($metrics, $notes);

        return ['metrics' => $metrics, 'notes' => $notes];
    }

    /**
     * @param  array{kind: string, text?: string}|null  $pending
     * @param  list<string>  $notes
     */
    private function keepProse(?array $pending, array &$notes): void
    {
        if ($pending !== null && $pending['kind'] === 'prose' && isset($pending['text'])) {
            $notes[] = $pending['text'];
        }
    }

    /**
     * @param  list<array{label: string, value: string, place: ?string}>  $metrics
     */
    private function remember(array &$metrics, string $label, string $value, ?string $place): void
    {
        foreach ($metrics as $metric) {
            if ($metric['label'] === $label && $metric['place'] === $place) {
                return;
            }
        }

        $metrics[] = ['label' => $label, 'value' => trim($value), 'place' => $place];
    }

    /**
     * @return array{label: string, value: string, place: ?string}|null
     */
    private function sameLineMetric(string $line): ?array
    {
        if (strlen($line) > 70 || preg_match('/during|generated|recorded|showing|represented/i', $line) === 1) {
            return null;
        }

        if (preg_match('/^(\+?\d[\d,]*(?:\.\d+)?[KkMm]?)\s+(.+)$/', $line, $matches) !== 1) {
            return null;
        }

        if (strlen($matches[2]) > 40 || str_contains($matches[2], ',')) {
            return null;
        }

        $rest = strtolower($matches[2]);
        $label = match (true) {
            str_contains($rest, 'chat') => 'Chat clicks',
            str_contains($rest, 'direction') => 'Direction requests',
            str_contains($rest, 'website') => 'Website clicks',
            str_contains($rest, 'review') => 'Reviews',
            str_contains($rest, 'interaction') => 'Interactions',
            str_contains($rest, 'call') => 'Calls',
            default => null,
        };

        if ($label === null) {
            return null;
        }

        return ['label' => $label, 'value' => $matches[1], 'place' => null];
    }

    /**
     * @return array{label: string, place: ?string}|null
     */
    private function canonicalLabel(string $line): ?array
    {
        if (strlen($line) > 40 || str_contains($line, '.')) {
            return null;
        }

        $pretty = $this->pretty($line);
        $key = strtolower($pretty);

        if (preg_match('/^(bg road|uttarahalli)\s+(interactions|calls)$/i', $key, $matches) === 1) {
            return [
                'label' => strcasecmp($matches[2], 'calls') === 0 ? 'Calls' : 'Interactions',
                'place' => strcasecmp($matches[1], 'bg road') === 0 ? 'BG Road' : 'Uttarahalli',
            ];
        }

        $labels = [
            'views' => 'Views',
            'total views' => 'Views',
            'instagram views' => 'Views',
            'facebook views' => 'Views',
            'organic views' => 'Organic views',
            'accounts reached' => 'Accounts reached',
            'reach' => 'Accounts reached',
            'content interactions' => 'Content interactions',
            'viewers' => 'Viewers',
            'follows' => 'Follows',
            'un-follows' => 'Unfollows',
            'unfollows' => 'Unfollows',
            'watch time' => 'Watch time',
            '3-second views' => '3-second views',
            'subscribers' => 'Subscribers',
            'realtime subscribers' => 'Subscribers now',
            'monthly audience' => 'Monthly audience',
            'stayed to watch' => 'Stayed to watch',
            'top video views' => 'Top video views',
            'likes' => 'Likes',
            'engaged views' => 'Engaged views',
            'interactions' => 'Interactions',
            'profile interactions' => 'Interactions',
            'calls' => 'Calls',
            'chat clicks' => 'Chat clicks',
            'direction requests' => 'Direction requests',
            'website clicks' => 'Website clicks',
            'total reviews' => 'Reviews',
            'female' => 'Female',
            'male' => 'Male',
        ];

        if (! isset($labels[$key])) {
            return null;
        }

        return ['label' => $labels[$key], 'place' => null];
    }

    private function smallCount(string $label): bool
    {
        return in_array($label, ['Follows', 'Unfollows', 'Reviews', 'Subscribers'], true);
    }

    private function compatible(string $label, string $value): bool
    {
        $rate = in_array($label, ['Stayed to watch', 'Female', 'Male'], true);
        $percent = str_ends_with($value, '%');

        return $rate === $percent;
    }

    private function isMetricValue(string $line): bool
    {
        return preg_match('/^(?:\+|-)?\d[\d,]*(?:\.\d+)?(?:\s*[KkMm]{1,2})?\+?%?$/', $line) === 1
            || preg_match('/^\d+h\d+m$/i', $line) === 1
            || preg_match('/^\d+(?:\.\d+)?\s*hours?$/i', $line) === 1
            || preg_match('/^\d+(?:\.\d+)?\s*hrs?$/i', $line) === 1;
    }

    /**
     * @param  list<array{label: string, value: string, place: ?string}>  $metrics
     * @param  list<string>  $notes
     */
    private function mineNotes(array &$metrics, array $notes): void
    {
        $text = implode(' ', $notes);

        if (! $this->hasLabel($metrics, '3-second views') && preg_match('/(\d+(?:\.\d+)?\s*[KkMm]?)\s+3-second views/i', $text, $matches) === 1) {
            $this->remember($metrics, '3-second views', trim($matches[1]), null);
        }

        if (! $this->hasLabel($metrics, 'Watch time') && preg_match('/(\d+\s+hours?\s+\d+\s+minutes?)\s+watch time/i', $text, $matches) === 1) {
            $this->remember($metrics, 'Watch time', trim($matches[1]), null);
        }

        if (! $this->hasLabel($metrics, 'Subscribers') && preg_match('/(\+\d+)\s*subscribers/i', $text, $matches) === 1) {
            $this->remember($metrics, 'Subscribers', $matches[1], null);
        }

        if (! $this->hasLabel($metrics, 'Female') && preg_match('/female viewers represented\s*([\d.]+%)/i', $text, $matches) === 1) {
            $this->remember($metrics, 'Female', $matches[1], null);
        }

        if (! $this->hasLabel($metrics, 'Male') && preg_match('/\bmale viewers represented\s*([\d.]+%)/i', $text, $matches) === 1) {
            $this->remember($metrics, 'Male', $matches[1], null);
        }

        if (! $this->hasLabel($metrics, 'Stayed to watch') && preg_match('/([\d.]+%)\s+stayed to watch/i', $text, $matches) === 1) {
            $this->remember($metrics, 'Stayed to watch', $matches[1], null);
        }
    }

    /**
     * @param  list<array{label: string, value: string, place: ?string}>  $metrics
     */
    private function hasLabel(array $metrics, string $label): bool
    {
        foreach ($metrics as $metric) {
            if ($metric['label'] === $label) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  list<string>  $notes
     * @return list<string>
     */
    private function cleanNotes(array $notes): array
    {
        $clean = [];

        foreach ($notes as $note) {
            $note = $this->tidy($note);

            if ($note === '' || preg_match('/^\d{1,2}$/', $note) === 1 || in_array($note, $clean, true)) {
                continue;
            }

            $clean[] = $note;
        }

        return $clean;
    }

    /**
     * @param  list<array{label: string, value: string, place: ?string}>  $metrics
     * @return list<array{label: string, value: string}>
     */
    private function plainMetrics(array $metrics): array
    {
        $plain = [];

        foreach ($metrics as $metric) {
            if ($metric['place'] !== null) {
                continue;
            }

            $plain[] = ['label' => $metric['label'], 'value' => $metric['value']];
        }

        return $plain;
    }

    /**
     * @param  list<array{name: string, metrics: list<array{label: string, value: string}>, notes: list<string>}>  $profiles
     */
    private function addMetric(array &$profiles, string $name, string $label, string $value): void
    {
        $index = $this->profileIndex($profiles, $name);

        foreach ($profiles[$index]['metrics'] as $metric) {
            if ($metric['label'] === $label) {
                return;
            }
        }

        $profiles[$index]['metrics'][] = ['label' => $label, 'value' => $value];
    }

    /**
     * @param  list<array{name: string, metrics: list<array{label: string, value: string}>, notes: list<string>}>  $profiles
     * @param  list<string>  $notes
     */
    private function addNotes(array &$profiles, string $name, array $notes): void
    {
        if ($notes === []) {
            return;
        }

        $index = $this->profileIndex($profiles, $name);

        foreach ($notes as $note) {
            if (! in_array($note, $profiles[$index]['notes'], true)) {
                $profiles[$index]['notes'][] = $note;
            }
        }
    }

    /**
     * @param  list<array{name: string, metrics: list<array{label: string, value: string}>, notes: list<string>}>  $profiles
     */
    private function profileIndex(array &$profiles, string $name): int
    {
        foreach ($profiles as $index => $profile) {
            if ($profile['name'] === $name) {
                return $index;
            }
        }

        $profiles[] = ['name' => $name, 'metrics' => [], 'notes' => []];

        return array_key_last($profiles);
    }

    private function seoSection(string $line): ?string
    {
        if (str_contains($line, "\t") || strlen($line) > 80 || str_contains($line, ':') || str_contains($line, '.') || str_starts_with($line, '•') || str_starts_with($line, '-')) {
            return null;
        }

        $key = preg_replace('/[^a-z]/', '', strtolower($line)) ?? '';

        if (strlen($key) < 10) {
            return null;
        }

        return match (true) {
            str_contains($key, 'analytics') && str_contains($key, 'business') => 'analytics',
            str_contains($key, 'technicalseo') => 'technical',
            str_starts_with($key, 'aisearchvisibility') || str_contains($key, 'aeogeo') => 'ai',
            str_starts_with($key, 'searchvisibility') || str_starts_with($key, 'keywordanalysis') => 'search',
            str_starts_with($key, 'searchconsole') || str_contains($key, 'digitalplatform') => 'platforms',
            str_starts_with($key, 'onpageseo') || str_starts_with($key, 'seoactivities') => 'activities',
            str_contains($key, 'executivesummary') => 'summary',
            str_starts_with($key, 'whatwere') || str_starts_with($key, 'whatweredoing') => 'next',
            (str_contains($key, 'prioritytreatment') && str_contains($key, 'special')) || str_starts_with($key, 'prioritytreatment') => 'priorities',
            str_contains($key, 'internallinkingstrategy') => 'linking',
            str_contains($key, 'websiteandorganic') || $key === 'augustvsseptember' => 'comparison',
            default => null,
        };
    }

    /**
     * @param  list<array{label: string, previous: string, current: string}>  $comparison
     * @param  list<array{label: string, previous: string, current: string}>  $analytics
     * @param  list<array{label: string, value: string}>  $technical
     * @param  list<array{label: string, value: string}>  $ai
     * @param  list<array{label: string, value: string}>  $search
     * @param  list<string>  $lines
     */
    private function storeSeoRow(?string $section, string $line, array $lines, int &$index, array &$comparison, array &$analytics, array &$technical, array &$ai, array &$search): void
    {
        if (! in_array($section, ['comparison', 'analytics', 'technical', 'ai', 'search'], true)) {
            return;
        }

        $cells = $this->cells($line);

        if ($cells === [] || preg_match('/^(metrics?\s*\/\s*parameters|metric|area)$/i', $cells[0]) === 1) {
            return;
        }

        $twoMonths = in_array($section, ['comparison', 'analytics'], true);

        if ($twoMonths && count($cells) >= 3 && $this->isSeoValue($cells[1]) && $this->isSeoValue($cells[2])) {
            $row = ['label' => $this->pretty($cells[0]), 'previous' => $cells[1], 'current' => $cells[2]];
            $section === 'analytics' ? $analytics[] = $row : $comparison[] = $row;

            return;
        }

        $next = $lines[$index + 1] ?? null;

        if ($twoMonths && count($cells) === 2 && $this->isSeoValue($cells[1]) && is_string($next) && ! str_contains($next, "\t") && $this->isSeoValue($next)) {
            $row = ['label' => $this->pretty($cells[0]), 'previous' => $cells[1], 'current' => trim($next)];
            $section === 'analytics' ? $analytics[] = $row : $comparison[] = $row;
            $index++;

            return;
        }

        if (! $twoMonths && count($cells) === 2 && $this->isSeoValue($cells[1])) {
            $row = ['label' => $this->pretty($cells[0]), 'value' => $cells[1]];

            if ($section === 'technical') {
                $technical[] = $row;
            } elseif ($section === 'ai') {
                $ai[] = $row;
            } else {
                $search[] = $row;
            }
        }
    }

    /**
     * @param  list<array{name: string, clicks: string, impressions: string, position: string}>  $platforms
     */
    private function consumePlatform(string $line, array &$platforms, ?string &$pending): bool
    {
        $cells = $this->cells($line);
        $name = $this->platformName($cells[0] ?? $line);

        if ($name !== null && count($cells) >= 4 && $this->isSeoValue($cells[1]) && $this->isSeoValue($cells[2]) && $this->isSeoValue($cells[3])) {
            $platforms[] = [
                'name' => $name,
                'clicks' => $cells[1],
                'impressions' => $cells[2],
                'position' => $cells[3],
            ];
            $pending = null;

            return true;
        }

        if ($name !== null && count($cells) === 1) {
            $pending = $name;

            return true;
        }

        if ($pending !== null && count($cells) >= 3 && $this->isSeoValue($cells[0]) && $this->isSeoValue($cells[1]) && $this->isSeoValue($cells[2])) {
            $platforms[] = [
                'name' => $pending,
                'clicks' => $cells[0],
                'impressions' => $cells[1],
                'position' => $cells[2],
            ];
            $pending = null;

            return true;
        }

        return false;
    }

    /**
     * @return list<string>
     */
    private function cells(string $line): array
    {
        if (! str_contains($line, "\t")) {
            return [$line];
        }

        $cells = array_map(trim(...), explode("\t", $line));

        return array_values(array_filter($cells, fn (string $cell): bool => $cell !== ''));
    }

    private function platformName(string $value): ?string
    {
        return match (strtolower(trim($value))) {
            'instagram' => 'Instagram',
            'youtube' => 'YouTube',
            'facebook' => 'Facebook',
            'google image search' => 'Google Image Search',
            default => null,
        };
    }

    private function isSeoValue(string $value): bool
    {
        $value = trim($value);

        if ($value === '' || strlen($value) > 20 || preg_match('/\d/', $value) !== 1) {
            return false;
        }

        $words = preg_replace('/\b(sec|secs|min|mins|hours?|hrs?)\b/i', '', $value) ?? $value;

        return preg_match('/[A-Za-z]{4,}/', $words) !== 1;
    }

    /**
     * @param  list<string>  $lines
     * @return list<array{text: string, status: string}>
     */
    private function activities(array $lines): array
    {
        $items = [];
        $buffer = [];

        foreach ($lines as $line) {
            $split = $this->splitStatus($line);

            if ($split === null) {
                if (preg_match('/^(no\.|on-page seo activity|seo activity)\b/i', $line) === 1 || preg_match('/^\d{1,2}$/', $line) === 1) {
                    continue;
                }

                $buffer[] = $line;

                continue;
            }

            if ($split['text'] !== '') {
                $buffer[] = $split['text'];
            }

            $text = trim(preg_replace('/\s+/', ' ', str_replace("\t", ' ', implode(' ', $buffer))) ?? '');
            $text = preg_replace('/^\d{1,2}\s*/', '', $text) ?? $text;

            if ($text !== '') {
                $items[] = ['text' => $this->tidy($text), 'status' => $split['status']];
            }

            $buffer = [];
        }

        return $items;
    }

    /**
     * @return array{text: string, status: string}|null
     */
    private function splitStatus(string $line): ?array
    {
        foreach (['Work in Progress', 'In Progress', 'Completed', 'Ongoing'] as $status) {
            if (strcasecmp(trim($line), $status) === 0) {
                return ['text' => '', 'status' => $status];
            }

            if (preg_match('/'.preg_quote($status, '/').'$/i', $line) === 1) {
                $text = trim(preg_replace('/'.preg_quote($status, '/').'$/i', '', $line) ?? '');

                return ['text' => $text, 'status' => $status];
            }
        }

        return null;
    }

    /**
     * @param  list<string>  $summary
     */
    private function pushSummary(array &$summary, string $line): void
    {
        $fresh = str_starts_with($line, '•') || str_starts_with($line, '-');

        if ($fresh) {
            $summary[] = $this->tidy(ltrim($line, "•- \t"));

            return;
        }

        if ($summary === []) {
            return;
        }

        $last = array_key_last($summary);

        if (! preg_match('/[.!?]$/', $summary[$last])) {
            $summary[$last] = $this->tidy($summary[$last].' '.$line);
        }
    }

    /**
     * @param  list<string>  $lines
     * @return list<string>
     */
    private function nextItems(array $lines): array
    {
        $items = [];
        $current = null;

        foreach ($lines as $line) {
            if (preg_match('/^(no\.|expected outcome|action roadmap)\b/i', $line) === 1) {
                continue;
            }

            if (preg_match('/^\d{1,2}\b\s*(.*)$/', $line, $matches) === 1) {
                if (is_string($current) && trim($current) !== '') {
                    $items[] = $this->tidy($current);
                }

                $current = trim(str_replace("\t", ' ', $matches[1]));

                continue;
            }

            if ($current === null) {
                continue;
            }

            $current = trim($current.' '.str_replace("\t", ' ', $line));
        }

        if (is_string($current) && trim($current) !== '') {
            $items[] = $this->tidy($current);
        }

        return $items;
    }

    /**
     * @param  list<string>  $lines
     * @return list<array{name: string, focus: string}>
     */
    private function priorities(array $lines): array
    {
        $items = [];

        foreach ($lines as $line) {
            $cells = $this->cells($line);

            if (count($cells) < 2 || preg_match('/priority treatment|seo focus/i', $cells[0]) === 1) {
                continue;
            }

            $items[] = ['name' => $cells[0], 'focus' => $cells[1]];
        }

        return $items;
    }

    /**
     * @param  list<string>  $linking
     * @param  list<string>  $competitor
     */
    private function fileAside(string $text, array &$linking, array &$competitor): void
    {
        $text = trim($text);

        if ($text === '') {
            return;
        }

        if (preg_match('/link|homepage|navigation|appointment|anchor|connect related|facility pages/i', $text) === 1) {
            $linking[] = $this->tidy($text);

            return;
        }

        $competitor[] = $this->tidy($text);
    }

    private function pretty(string $value): string
    {
        $value = trim(preg_replace('/\s+/', ' ', $value) ?? $value);

        if ($value !== '' && mb_strtoupper($value) === $value && mb_strlen($value) <= 5 && preg_match('/[A-Z]/', $value) === 1) {
            return $value;
        }

        $value = trim(preg_replace('/(?<=[a-z])(?=[A-Z])/', ' ', $value) ?? $value);
        $value = str_replace(['Whats App', 'You Tube', 'Chat GPT'], ['WhatsApp', 'YouTube', 'ChatGPT'], $value);

        if ($value !== '' && mb_strtoupper($value) === $value && preg_match('/[A-Z]/', $value) === 1) {
            $value = ucwords(strtolower($value));
        }

        return $value;
    }

    private function tidy(string $text): string
    {
        $text = ltrim($text, "•- \t");
        $text = trim(preg_replace('/\s+/', ' ', str_replace("\t", ' ', $text)) ?? '');
        $text = preg_replace('/(?<=[a-z])(?=\d)/', ' ', $text) ?? $text;
        $text = preg_replace('/views(?=generated)/i', 'views ', $text) ?? $text;
        $text = preg_replace('/subscribers(?=during)/i', 'subscribers ', $text) ?? $text;
        $text = preg_replace('/:(\S)/', ': $1', $text) ?? $text;

        return trim($text);
    }

    private function isFooter(string $line): bool
    {
        return preg_match('/social media report|monthly seo performance report/iu', $line) === 1
            || preg_match('/^nano hospitals\b/iu', $line) === 1;
    }

    private function unreadable(string $kind): string
    {
        if ($kind === 'seo') {
            return 'This PDF does not look like a monthly SEO report. It needs the month and a comparison with the previous month.';
        }

        return 'This PDF does not look like a monthly social media report. It needs the month and results for Facebook, Instagram, YouTube, or Google Business Profile.';
    }
}
