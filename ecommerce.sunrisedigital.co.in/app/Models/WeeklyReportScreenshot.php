<?php

namespace App\Models;

use App\Models\Concerns\HasPublicUuid;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'platform',
    'channel_name',
    'path',
    'original_name',
    'sort',
])]
class WeeklyReportScreenshot extends Model
{
    use HasPublicUuid;

    protected static function booted(): void
    {
        static::deleting(function (WeeklyReportScreenshot $screenshot): void {
            Storage::disk('local')->delete($screenshot->path);
        });
    }

    /**
     * @return BelongsTo<WeeklyReport, $this>
     */
    public function report(): BelongsTo
    {
        return $this->belongsTo(WeeklyReport::class, 'weekly_report_id');
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
