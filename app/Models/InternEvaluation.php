<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class InternEvaluation extends Model
{
    /** @use HasFactory<\Database\Factories\InternEvaluationFactory> */
    use HasFactory;

    public function internEvaluationStatus(): BelongsTo
    {
        return $this->belongsTo(InternEvaluationStatus::class);
    }

    public function supervisor(): HasOneThrough
    {
        return $this->hasOneThrough(Supervisor::class, InternEvaluationStatus::class);
    }

    public function student(): HasOneThrough
    {
        return $this->hasOneThrough(Student::class, InternEvaluationStatus::class);
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
