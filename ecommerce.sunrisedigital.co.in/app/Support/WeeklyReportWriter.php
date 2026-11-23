<?php

namespace App\Support;

use App\Mail\ReportPublishedMail;
use App\Models\Reporter;
use App\Models\User;
use App\Models\WeeklyReport;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class WeeklyReportWriter
{
    /**
     * @param  array<string, mixed>|null  $extracted
     * @param  list<array{path?: mixed, value?: mixed}>|null  $figures
     */
    public function save(User $client, Reporter $reporter, string $intent, ?array $extracted, ?UploadedFile $pdf, string $kind = 'weekly', ?WeeklyReport $report = null, ?array $figures = null): WeeklyReport
    {
        $report ??= new WeeklyReport;
        $publish = $intent === 'publish';
        $wasPublished = $report->exists && $report->isPublished();

        $report = DB::transaction(function () use ($client, $reporter, $extracted, $report, $publish, $kind, $figures): WeeklyReport {
            if (! $report->exists) {
                $report->client()->associate($client);
                $report->reporter()->associate($reporter);
                $report->kind = in_array($kind, ['social', 'seo', 'monthly'], true) ? $kind : 'weekly';
            }

            if ($extracted !== null) {
                $attributes = [
                    'title' => $extracted['title'],
                    'period_start' => $extracted['period_start'],
                    'period_end' => $extracted['period_end'],
                    'previous_start' => $extracted['previous_start'],
                    'previous_end' => $extracted['previous_end'],
                    'facebook' => $extracted['facebook'],
                    'instagram' => $extracted['instagram'],
                    'youtube' => $extracted['youtube'],
                    'narrative' => $extracted['narrative'],
                ];

                if (array_key_exists('body', $extracted)) {
                    $attributes['body'] = $extracted['body'];
                }

                $report->fill($attributes);
            } elseif (is_array($figures) && $report->exists) {
                $this->recordRevisions($report, $reporter, $figures);
                $report->fill(WeeklyReportFigures::apply($report, $figures));
            }

            $report->status = $publish ? 'published' : 'draft';
            $report->published_at = $publish ? ($report->published_at ?? now()) : null;
            $report->save();

            return $report;
        });

        if ($pdf instanceof UploadedFile && $pdf->isValid()) {
            $this->storePdf($report, $pdf);
        }

        if ($publish && ! $wasPublished) {
            ReportPublishedMail::deliver($report);
        }

        return $report;
    }

    /**
     * @param  list<array{path?: mixed, value?: mixed}>  $figures
     */
    private function recordRevisions(WeeklyReport $report, Reporter $reporter, array $figures): void
    {
        $current = collect(WeeklyReportFigures::fields($report))->keyBy('path');

        foreach ($figures as $figure) {
            if (! is_array($figure) || ! is_string($figure['path'] ?? null) || ! $current->has($figure['path'])) {
                continue;
            }

            $old = (string) $current[$figure['path']]['value'];
            $new = trim((string) ($figure['value'] ?? ''));

            if ($old === $new) {
                continue;
            }

            $revision = $report->revisions()->make([
                'path' => $figure['path'],
                'label' => (string) $current[$figure['path']]['label'],
                'old_value' => $old,
                'new_value' => $new,
            ]);
            $revision->reporter()->associate($reporter);
            $revision->save();
        }
    }

    private function storePdf(WeeklyReport $report, UploadedFile $pdf): void
    {
        $previous = $report->pdf_path;
        $path = $pdf->storeAs('weekly-reports/'.$report->getKey(), 'source.pdf', 'local');

        if ($path === false) {
            return;
        }

        if (is_string($previous) && $previous !== $path) {
            Storage::disk('local')->delete($previous);
        }

        $report->pdf_path = $path;
        $report->pdf_original_name = Str::limit($pdf->getClientOriginalName(), 200, '');
        $report->save();
    }
}
