<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportAnswer extends Model
{
    /** @use HasFactory<\Database\Factories\ReportAnswerFactory> */
    use HasFactory;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(OpenEndedQuestion::class);
    }
}
