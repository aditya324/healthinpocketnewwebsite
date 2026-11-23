<?php

namespace App\Models;

use App\Models\Concerns\HasPublicUuid;
use Database\Factories\WeeklyReportFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'title',
    'kind',
    'period_start',
    'period_end',
    'previous_start',
    'previous_end',
    'status',
    'published_at',
    'facebook',
    'instagram',
    'youtube',
    'narrative',
    'body',
    'staff_note',
])]
class WeeklyReport extends Model
{
    /** @use HasFactory<WeeklyReportFactory> */
    use HasFactory, HasPublicUuid, SoftDeletes;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'period_start' => 'date',
            'period_end' => 'date',
            'previous_start' => 'date',
            'previous_end' => 'date',
            'published_at' => 'datetime',
            'facebook' => 'array',
            'instagram' => 'array',
            'youtube' => 'array',
            'narrative' => 'array',
            'body' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::forceDeleting(function (WeeklyReport $report): void {
            $report->screenshots()->get()->each->delete();
        });

        static::forceDeleted(function (WeeklyReport $report): void {
            Storage::disk('local')->deleteDirectory('weekly-reports/'.$report->getKey());
        });
    }

    /**
     * @param  Builder<WeeklyReport>  $query
     * @return Builder<WeeklyReport>
     */
    #[Scope]
    protected function published(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return BelongsTo<Reporter, $this>
     */
    public function reporter(): BelongsTo
    {
        return $this->belongsTo(Reporter::class);
    }

    /**
     * @return HasMany<WeeklyReportScreenshot, $this>
     */
    public function screenshots(): HasMany
    {
        return $this->hasMany(WeeklyReportScreenshot::class)->orderBy('sort')->orderBy('id');
    }

    /**
     * @return HasMany<WeeklyReportRevision, $this>
     */
    public function revisions(): HasMany
    {
        return $this->hasMany(WeeklyReportRevision::class)->latest();
    }

    /**
     * @return HasMany<WeeklyReportFlag, $this>
     */
    public function flags(): HasMany
    {
        return $this->hasMany(WeeklyReportFlag::class)->latest();
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function isSocial(): bool
    {
        return $this->kind === 'social';
    }

    public function isSeo(): bool
    {
        return $this->kind === 'seo';
    }

    public function isMonthly(): bool
    {
        return in_array($this->kind, ['monthly', 'social', 'seo'], true);
    }

    public function kindLabel(): string
    {
        return match ($this->kind) {
            'social' => 'Social media',
            'seo' => 'SEO',
            'monthly' => 'Monthly',
            default => 'Weekly',
        };
    }

    public function periodWord(): string
    {
        return $this->isMonthly() ? 'month' : 'week';
    }

    public function adjacentReport(string $direction): ?self
    {
        if (! $this->exists || $this->user_id === null || $this->period_start === null) {
            return null;
        }

        $query = static::query()
            ->where('user_id', $this->user_id)
            ->where('kind', $this->kind ?: 'weekly')
            ->where('status', 'published')
            ->whereKeyNot($this->getKey());

        if ($direction === 'later') {
            return $query->whereDate('period_start', '>', $this->period_start)->orderBy('period_start')->first();
        }

        return $query->whereDate('period_start', '<', $this->period_start)->orderByDesc('period_start')->first();
    }

    /**
     * @return Collection<int, self>
     */
    public function comparableReports(): Collection
    {
        if (! $this->exists || $this->user_id === null) {
            return new Collection;
        }

        return static::query()
            ->where('user_id', $this->user_id)
            ->where('kind', $this->kind ?: 'weekly')
            ->where('status', 'published')
            ->whereKeyNot($this->getKey())
            ->orderByDesc('period_start')
            ->orderByDesc('id')
            ->get();
    }

    /**
     * @param  Collection<int, self>|null  $reports
     */
    public function comparedWith(?string $uuid, ?Collection $reports = null): ?self
    {
        $reports ??= $this->comparableReports();
        $chosen = is_string($uuid) && $uuid !== '' ? $reports->firstWhere('uuid', $uuid) : null;

        if ($chosen instanceof self) {
            return $chosen;
        }

        if (! ($this->isSocial() || $this->isSeo() || $this->kind === 'monthly') || $this->period_start === null) {
            return null;
        }

        $earlier = $reports->first(fn (self $report): bool => $report->period_start !== null && $report->period_start->lt($this->period_start));

        if ($earlier instanceof self || ! $this->isSocial()) {
            return $earlier;
        }

        return $reports
            ->filter(fn (self $report): bool => $report->period_start !== null && $report->period_start->gt($this->period_start))
            ->sortBy(fn (self $report): int => $report->period_start?->getTimestamp() ?? 0)
            ->first();
    }

    public function visibleTo(?User $customer, ?Reporter $reporter): bool
    {
        if ($reporter !== null) {
            return true;
        }

        return $customer !== null
            && $this->user_id === $customer->id
            && $this->isPublished();
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function currentPeriodLabel(): string
    {
        if ($this->wholeMonth($this->period_start, $this->period_end)) {
            return $this->period_start->format('F Y');
        }

        return $this->formatPeriod($this->period_start, $this->period_end);
    }

    public function previousPeriodLabel(): string
    {
        if ($this->wholeMonth($this->previous_start, $this->previous_end)) {
            return $this->previous_start->format('F Y');
        }

        return $this->formatPeriod($this->previous_start, $this->previous_end);
    }

    public function hasFilled(mixed $value): bool
    {
        if (is_string($value)) {
            return trim($value) !== '';
        }

        if (! is_array($value)) {
            return false;
        }

        foreach ($value as $item) {
            if ($this->hasFilled($item)) {
                return true;
            }
        }

        return false;
    }

    private function wholeMonth(?Carbon $start, ?Carbon $end): bool
    {
        return $this->isMonthly()
            && $start instanceof Carbon
            && $end instanceof Carbon
            && $start->isSameMonth($end)
            && $start->isSameYear($end)
            && $start->day === 1
            && $end->day === $end->daysInMonth;
    }

    private function formatPeriod(Carbon $start, Carbon $end): string
    {
        if ($start->isSameMonth($end) && $start->isSameYear($end)) {
            return $start->format('j').'–'.$end->format('j F Y');
        }

        if ($start->isSameYear($end)) {
            return $start->format('j M').' – '.$end->format('j M Y');
        }

        return $start->format('j M Y').' – '.$end->format('j M Y');
    }
}
