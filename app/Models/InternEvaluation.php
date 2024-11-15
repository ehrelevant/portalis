<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InternEvaluation extends Model
{
    /** @use HasFactory<\Database\Factories\InternEvaluationFactory> */
    use HasFactory;

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(Supervisor::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(InternEvaluationRating::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(InternEvaluationAnswer::class);
    }
}
