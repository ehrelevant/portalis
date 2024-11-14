<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeeklyReportRating extends Model
{
    /** @use HasFactory<\Database\Factories\WeeklyReportRatingFactory> */
    use HasFactory;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function weekly_report(): BelongsTo
    {
        return $this->belongsTo(WeeklyReport::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(RatingQuestion::class);
    }
}
