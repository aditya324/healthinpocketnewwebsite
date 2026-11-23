<?php

namespace App\Http\Requests\Reports;

use App\Models\User;
use App\Models\WeeklyReport;
use App\Support\MonthlyReportPdfReader;
use App\Support\WeeklyReportFigures;
use App\Support\WeeklyReportPdfReader;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use InvalidArgumentException;

class WeeklyReportRequest extends FormRequest
{
    private bool $didExtract = false;

    private ?string $extractError = null;

    /**
     * @var array<string, mixed>|null
     */
    private ?array $extractedReport = null;

    public function authorize(): bool
    {
        return Auth::guard('reporter')->check();
    }

    protected function prepareForValidation(): void
    {
        $report = $this->route('report');

        if ($this->filled('kind')) {
            return;
        }

        $kind = 'weekly';

        if ($report instanceof WeeklyReport && in_array($report->kind, ['weekly', 'social', 'seo', 'monthly'], true)) {
            $kind = $report->kind;
        }

        $this->merge([
            'kind' => $kind,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $report = $this->route('report');

        return [
            'kind' => ['required', Rule::in(['weekly', 'social', 'seo', 'monthly'])],
            'pdf' => [
                $report instanceof WeeklyReport && filled($report->pdf_path) ? 'nullable' : 'required',
                'file',
                'mimes:pdf',
                'max:20480',
            ],
            'intent' => ['required', Rule::in(['draft', 'publish'])],
            'figures' => ['nullable', 'array'],
            'figures.*.path' => ['required', 'string', 'max:200'],
            'figures.*.value' => ['nullable', 'string', 'max:80'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'kind.required' => 'Choose a weekly, social media, or SEO report.',
            'kind.in' => 'Choose a weekly, social media, or SEO report.',
            'pdf.required' => 'Upload the report PDF.',
            'pdf.mimes' => 'The report must be a PDF.',
            'pdf.max' => 'The PDF must be 20 MB or smaller.',
            'intent.required' => 'Save the report as a draft or publish it.',
            'intent.in' => 'Save the report as a draft or publish it.',
            'figures.*.value.max' => 'A figure must be 80 characters or fewer.',
        ];
    }

    /**
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $this->guardKind($validator);
                $this->guardPdf($validator);
                $this->guardFigures($validator);
                $this->guardPublish($validator);
            },
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    public function extracted(): ?array
    {
        if ($this->didExtract) {
            return $this->extractedReport;
        }

        $this->didExtract = true;
        $file = $this->file('pdf');

        if (! $file instanceof UploadedFile || ! $file->isValid()) {
            return null;
        }

        try {
            $this->extractedReport = $this->reader()->read($file->getRealPath(), $this->kind());
        } catch (InvalidArgumentException $exception) {
            $this->extractError = $exception->getMessage();
            $this->extractedReport = null;
        }

        return $this->extractedReport;
    }

    /**
     * @return list<array{path?: mixed, value?: mixed}>|null
     */
    public function figures(): ?array
    {
        if ($this->file('pdf') instanceof UploadedFile || ! $this->exists('figures')) {
            return null;
        }

        $figures = $this->validated('figures');

        return is_array($figures) ? $figures : null;
    }

    public function client(): User
    {
        $customer = $this->route('customer');

        if ($customer instanceof User) {
            return $customer;
        }

        /** @var WeeklyReport $report */
        $report = $this->route('report');

        return $report->client;
    }

    private function guardKind(Validator $validator): void
    {
        $report = $this->route('report');

        if ($report instanceof WeeklyReport && $report->kind !== $this->kind()) {
            $validator->errors()->add('kind', 'This file stays '.$this->article($report->kind).' '.$this->phrase($report->kind).' report.');
        }
    }

    private function guardPdf(Validator $validator): void
    {
        $file = $this->file('pdf');

        if (! $file instanceof UploadedFile) {
            return;
        }

        $parsed = $this->extracted();

        if ($parsed === null) {
            $validator->errors()->add('pdf', $this->extractError ?? 'Upload the report PDF.');

            return;
        }

        if ($parsed['previous_end'] >= $parsed['period_start']) {
            $validator->errors()->add('pdf', 'The previous '.$this->periodWord().' must end before this '.$this->periodWord().' starts.');

            return;
        }

        $report = $this->route('report');
        $existing = WeeklyReport::withTrashed()
            ->whereBelongsTo($this->client(), 'client')
            ->where('kind', $this->kind())
            ->whereDate('period_start', $parsed['period_start'])
            ->when(
                $report instanceof WeeklyReport,
                fn ($query) => $query->whereKeyNot($report->getKey()),
            )
            ->first();

        if ($existing instanceof WeeklyReport && $existing->trashed()) {
            $validator->errors()->add('pdf', 'A deleted report already exists for that '.$this->periodWord().'. Restore it from the client page.');

            return;
        }

        if ($existing instanceof WeeklyReport) {
            $validator->errors()->add('pdf', 'This client already has '.$this->article().' '.$this->phrase().' report for that '.$this->periodWord().'.');
        }
    }

    private function guardFigures(Validator $validator): void
    {
        if ($this->file('pdf') instanceof UploadedFile || $validator->errors()->isNotEmpty()) {
            return;
        }

        $report = $this->route('report');
        $figures = $this->input('figures');

        if (! $report instanceof WeeklyReport || ! is_array($figures)) {
            return;
        }

        $allowed = collect(WeeklyReportFigures::fields($report))->pluck('path')->all();

        foreach ($figures as $figure) {
            $path = is_array($figure) ? ($figure['path'] ?? null) : null;

            if (! is_string($path) || ! in_array($path, $allowed, true)) {
                $validator->errors()->add('figures', 'One of these figures is not on this report.');

                return;
            }
        }
    }

    private function guardPublish(Validator $validator): void
    {
        if ($this->input('intent') !== 'publish' || $validator->errors()->isNotEmpty()) {
            return;
        }

        if ($this->file('pdf') instanceof UploadedFile) {
            return;
        }

        $report = $this->route('report');

        if ($report instanceof WeeklyReport && filled($report->pdf_path)) {
            return;
        }

        $validator->errors()->add('pdf', 'Upload the report PDF before publishing.');
    }

    private function reader(): WeeklyReportPdfReader|MonthlyReportPdfReader
    {
        return in_array($this->kind(), ['social', 'seo'], true)
            ? new MonthlyReportPdfReader
            : new WeeklyReportPdfReader;
    }

    private function kind(): string
    {
        return match ($this->input('kind')) {
            'social' => 'social',
            'seo' => 'seo',
            'monthly' => 'monthly',
            default => 'weekly',
        };
    }

    private function periodWord(): string
    {
        return in_array($this->kind(), ['social', 'seo', 'monthly'], true) ? 'month' : 'week';
    }

    private function phrase(?string $kind = null): string
    {
        return match ($kind ?? $this->kind()) {
            'social' => 'social media',
            'seo' => 'SEO',
            'monthly' => 'monthly',
            default => 'weekly',
        };
    }

    private function article(?string $kind = null): string
    {
        return ($kind ?? $this->kind()) === 'seo' ? 'an' : 'a';
    }
}
