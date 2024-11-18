<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class WeeklyReport extends Model
{
    /** @use HasFactory<\Database\Factories\WeeklyReportFactory> */
    use HasFactory;

    public function weeklyReportStatus(): BelongsTo
    {
        return $this->belongsTo(WeeklyReportStatus::class);
    }

    public function supervisor(): HasOneThrough
    {
        return $this->hasOneThrough(Supervisor::class, WeeklyReportStatus::class);
    }

    public function student(): HasOneThrough
    {
        return $this->hasOneThrough(Student::class, WeeklyReportStatus::class);
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(WeeklyReportRating::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(WeeklyReportAnswer::class);
    }
}
