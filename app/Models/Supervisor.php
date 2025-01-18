<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Supervisor extends Model
{
    /** @use HasFactory<\Database\Factories\SupervisorFactory> */
    use HasFactory;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function reportStatuses(): HasMany
    {
        return $this->hasMany(ReportStatus::class);
    }

    public function reports(): HasManyThrough
    {
        return $this->hasManyThrough(Report::class, ReportStatus::class);
    }

    public function internEvaluationStatuses(): HasMany
    {
        return $this->hasMany(InternEvaluationStatus::class);
    }

    public function internEvaluations(): HasManyThrough
    {
        return $this->hasManyThrough(InternEvaluation::class, InternEvaluationStatus::class);
    }
}
