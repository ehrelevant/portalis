<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Report extends Model
{
    /** @use HasFactory<\Database\Factories\ReportFactory> */
    use HasFactory;

    public function reportStatus(): BelongsTo
    {
        return $this->belongsTo(ReportStatus::class);
    }

    public function supervisor(): HasOneThrough
    {
        return $this->hasOneThrough(Supervisor::class, ReportStatus::class);
    }

    public function student(): HasOneThrough
    {
        return $this->hasOneThrough(Student::class, ReportStatus::class);
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(ReportRating::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(ReportAnswer::class);
    }
}
