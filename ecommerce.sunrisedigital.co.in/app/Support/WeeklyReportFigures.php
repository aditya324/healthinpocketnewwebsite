<?php

namespace App\Support;

use App\Models\WeeklyReport;
use Illuminate\Support\Str;

class WeeklyReportFigures
{
    /**
     * @return list<array{path: string, group: string, label: string, value: string}>
     */
    public static function fields(WeeklyReport $report): array
    {
        $fields = [];

        if ($report->isSocial() || $report->isSeo() || $report->kind === 'monthly') {
            self::walk(is_array($report->body) ? $report->body : [], 'body', $fields);
        } else {
            self::walk([
                'facebook' => $report->facebook ?? [],
                'instagram' => $report->instagram ?? [],
                'youtube' => $report->youtube ?? [],
            ], '', $fields);
        }

        foreach ($fields as $index => $field) {
            $fields[$index]['group'] = self::group($field['path']);
            $fields[$index]['label'] = self::label($report, $field['path']);
        }

        return $fields;
    }

    /**
     * @param  list<array{path?: mixed, value?: mixed}>  $submitted
     * @return array<string, mixed>
     */
    public static function apply(WeeklyReport $report, array $submitted): array
    {
        $allowed = collect(self::fields($report))->pluck('path')->all();
        $facebook = is_array($report->facebook) ? $report->facebook : [];
        $instagram = is_array($report->instagram) ? $report->instagram : [];
        $youtube = is_array($report->youtube) ? $report->youtube : [];
        $body = is_array($report->body) ? $report->body : [];

        foreach ($submitted as $figure) {
            if (! is_array($figure) || ! is_string($figure['path'] ?? null) || ! in_array($figure['path'], $allowed, true)) {
                continue;
            }

            $value = trim((string) ($figure['value'] ?? ''));
            $path = $figure['path'];

            if (str_starts_with($path, 'body.')) {
                data_set($body, Str::after($path, 'body.'), $value);
            } elseif (str_starts_with($path, 'facebook.')) {
                data_set($facebook, Str::after($path, 'facebook.'), $value);
            } elseif (str_starts_with($path, 'instagram.')) {
                data_set($instagram, Str::after($path, 'instagram.'), $value);
            } elseif (str_starts_with($path, 'youtube.')) {
                data_set($youtube, Str::after($path, 'youtube.'), $value);
            }
        }

        if ($report->isSocial() || $report->isSeo() || $report->kind === 'monthly') {
            return ['body' => $body];
        }

        return [
            'facebook' => $facebook,
            'instagram' => $instagram,
            'youtube' => $youtube,
        ];
    }

    /**
     * @param  array<mixed>  $node
     * @param  list<array{path: string, group: string, label: string, value: string}>  $fields
     */
    private static function walk(array $node, string $path, array &$fields): void
    {
        foreach ($node as $key => $value) {
            if ($key === 'notes' || $key === 'narrative') {
                continue;
            }

            $child = $path === '' ? (string) $key : $path.'.'.$key;

            if (is_array($value)) {
                self::walk($value, $child, $fields);

                continue;
            }

            if (! is_string($value) || ! self::isFigure($child)) {
                continue;
            }

            $fields[] = [
                'path' => $child,
                'group' => '',
                'label' => '',
                'value' => $value,
            ];
        }
    }

    private static function isFigure(string $path): bool
    {
        $leaf = Str::afterLast($path, '.');

        // Social commentary is a list of sentences under content. A numeric
        // key is one of those sentences, not a Facebook content count.
        if (preg_match('/^\d+$/', $leaf) === 1) {
            return false;
        }

        if (in_array($leaf, ['value', 'views', 'clicks', 'impressions', 'position'], true)) {
            return true;
        }

        return preg_match('/(^|\.)(current|previous|content|views_by_format|interactions_by_format)(\.|$)/', $path) === 1
            && ! in_array($leaf, ['label', 'name', 'title'], true);
    }

    private static function group(string $path): string
    {
        return match (true) {
            str_contains($path, '.profiles.') => 'Google Business Profile',
            str_contains($path, '.comparison.') => 'Website comparison',
            str_contains($path, '.analytics.') => 'Analytics and enquiries',
            str_contains($path, '.technical.') => 'Technical SEO',
            str_contains($path, '.platforms.') => 'Search Console',
            str_contains($path, 'instagram') => 'Instagram',
            str_contains($path, 'facebook') => 'Facebook',
            str_contains($path, 'youtube') => 'YouTube',
            str_contains($path, '.ai.') => 'AI search',
            str_contains($path, '.search.') => 'Search visibility',
            default => 'Figures',
        };
    }

    private static function label(WeeklyReport $report, string $path): string
    {
        $leaf = Str::afterLast($path, '.');
        $parent = Str::beforeLast($path, '.');
        $source = self::source($report);
        $name = data_get($source, $parent.'.name');
        $metric = data_get($source, $parent.'.label');

        if (in_array($leaf, ['current', 'previous'], true) && is_string($metric) && $metric !== '') {
            $when = $leaf === 'previous' ? 'Previous '.$report->periodWord() : 'This '.$report->periodWord();

            return $metric.' · '.$when;
        }

        if ($leaf === 'value' && is_string($metric) && $metric !== '') {
            $place = self::placeName($source, $parent, is_string($name) ? $name : null);

            return $place === null ? $metric : $place.' · '.$metric;
        }

        if (in_array($leaf, ['clicks', 'impressions', 'position'], true)) {
            $pretty = Str::headline($leaf);

            return is_string($name) && $name !== '' ? $name.' · '.$pretty : $pretty;
        }

        $when = str_contains($path, '.previous') ? 'Previous '.$report->periodWord() : (str_contains($path, '.current') ? 'This '.$report->periodWord() : null);
        $pretty = self::metricLabel($leaf);
        $channel = self::channelName($source, $path);
        $parts = array_filter([$channel, $when, $pretty]);

        return implode(' · ', $parts);
    }

    /**
     * @return array<string, mixed>
     */
    private static function source(WeeklyReport $report): array
    {
        return [
            'facebook' => is_array($report->facebook) ? $report->facebook : [],
            'instagram' => is_array($report->instagram) ? $report->instagram : [],
            'youtube' => is_array($report->youtube) ? $report->youtube : [],
            'body' => is_array($report->body) ? $report->body : [],
        ];
    }

    /**
     * @param  array<string, mixed>  $source
     */
    private static function placeName(array $source, string $parent, ?string $name): ?string
    {
        if (is_string($name) && $name !== '') {
            return $name;
        }

        if (preg_match('/^(.*)\.metrics\.\d+$/', $parent, $matches) !== 1) {
            return null;
        }

        $owner = data_get($source, $matches[1].'.name');

        return is_string($owner) && $owner !== '' ? $owner : null;
    }

    /**
     * @param  array<string, mixed>  $source
     */
    private static function channelName(array $source, string $path): ?string
    {
        if (preg_match('/^(?:body\.)?youtube\.(\d+)\./', $path, $matches) !== 1) {
            return null;
        }

        $name = data_get($source, (str_starts_with($path, 'body.') ? 'body.youtube.' : 'youtube.').$matches[1].'.name');

        return is_string($name) && $name !== '' ? $name : null;
    }

    private static function metricLabel(string $key): string
    {
        if (str_ends_with($key, '_change')) {
            return self::metricLabel(substr($key, 0, -7)).' change';
        }
        $catalogs = [
            WeeklyReportFields::facebookMetrics(),
            WeeklyReportFields::facebookContent(),
            WeeklyReportFields::facebookViewsByFormat(),
            WeeklyReportFields::facebookInteractionsByFormat(),
            WeeklyReportFields::instagramMetrics(),
            WeeklyReportFields::youtubeMetrics(),
        ];

        foreach ($catalogs as $catalog) {
            if (isset($catalog[$key])) {
                return $catalog[$key];
            }
        }

        return Str::headline(str_replace('_', ' ', $key));
    }
}
