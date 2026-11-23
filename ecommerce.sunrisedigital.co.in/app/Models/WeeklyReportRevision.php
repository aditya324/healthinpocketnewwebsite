<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'path',
    'label',
    'old_value',
    'new_value',
])]
class WeeklyReportRevision extends Model
{
    /**
     * @return BelongsTo<WeeklyReport, $this>
     */
    public function report(): BelongsTo
    {
        return $this->belongsTo(WeeklyReport::class, 'weekly_report_id');
    }

    /**
     * @return BelongsTo<Reporter, $this>
     */
    public function reporter(): BelongsTo
    {
        return $this->belongsTo(Reporter::class);
    }
}
