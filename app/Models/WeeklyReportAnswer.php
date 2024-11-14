<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeeklyReportAnswer extends Model
{
    /** @use HasFactory<\Database\Factories\WeeklyReportAnswerFactory> */
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
        return $this->belongsTo(OpenEndedQuestion::class);
    }
}
