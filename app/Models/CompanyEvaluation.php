<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class CompanyEvaluation extends Model
{
    /** @use HasFactory<\Database\Factories\CompanyEvaluationFactory> */
    use HasFactory;

    public function companyEvaluationStatus(): BelongsTo
    {
        return $this->belongsTo(CompanyEvaluation::class);
    }

    public function student(): HasOneThrough
    {
        return $this->hasOneThrough(Student::class, CompanyEvaluationStatus::class);
    }

    public function company(): HasOneThrough
    {
        return $this->hasOneThrough(Company::class, CompanyEvaluationStatus::class);
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
