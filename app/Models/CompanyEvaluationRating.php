<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyEvaluationRating extends Model
{
    /** @use HasFactory<\Database\Factories\CompanyEvaluationRatingFactory> */
    use HasFactory;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function companyEvaluation(): BelongsTo
    {
        return $this->belongsTo(CompanyEvaluation::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(RatingQuestion::class);
    }
}
