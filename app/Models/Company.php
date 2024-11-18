<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Company extends Model
{
    /** @use HasFactory<\Database\Factories\CompanyFactory> */
    use HasFactory;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function supervisors(): HasMany
    {
        return $this->hasMany(Supervisor::class);
    }

    public function companyEvaluationStatuses(): HasMany
    {
        return $this->hasMany(CompanyEvaluationStatus::class);
    }

    public function companyEvaluations(): HasManyThrough
    {
        return $this->hasManyThrough(CompanyEvaluation::class, CompanyEvaluationStatus::class);
    }
}
