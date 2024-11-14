<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompanyEvaluation extends Model
{
    /** @use HasFactory<\Database\Factories\CompanyEvaluationFactory> */
    use HasFactory;

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(CompanyEvaluationRating::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(CompanyEvaluationAnswer::class);
    }
}
