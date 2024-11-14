<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InternEvaluationRating extends Model
{
    /** @use HasFactory<\Database\Factories\InternEvaluationRatingFactory> */
    use HasFactory;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function intern_evaluation(): BelongsTo
    {
        return $this->belongsTo(InternEvaluation::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(RatingQuestion::class);
    }
}
