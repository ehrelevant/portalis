<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Student extends Model
{
    /** @use HasFactory<\Database\Factories\StudentFactory> */
    use HasFactory;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'student_number';

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(Supervisor::class);
    }

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    public function submissionStatuses(): HasMany
    {
        return $this->hasMany(SubmissionStatus::class);
    }

    public function submissions(): HasManyThrough
    {
        return $this->hasManyThrough(Submission::class, SubmissionStatus::class);
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

    public function companyEvaluationStatuses(): HasMany
    {
        return $this->hasMany(CompanyEvaluationStatus::class);
    }

    public function companyEvaluations(): HasManyThrough
    {
        return $this->hasManyThrough(CompanyEvaluation::class, CompanyEvaluationStatus::class);
    }
}
