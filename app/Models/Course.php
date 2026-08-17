<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

#[Fillable(['course_name', 'duration', 'description', 'status'])]
class Course extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected static function booted(): void
    {
        static::creating(function (Course $course) {
            $course->course_code ??= static::generateCourseCode();
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable()->logOnlyDirty()->dontSubmitEmptyLogs();
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Scope a query to only active courses.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    /**
     * Generate a unique, human-readable course code, e.g. CRS-00042.
     *
     * Derived from the current row count - the while-loop guards the rare
     * concurrent-create collision rather than relying on the count alone
     * (same approach as Certificate::generateCertificateNumber()).
     */
    protected static function generateCourseCode(): string
    {
        $sequence = static::withTrashed()->count() + 1;
        $code = sprintf('CRS-%05d', $sequence);

        while (static::withTrashed()->where('course_code', $code)->exists()) {
            $sequence++;
            $code = sprintf('CRS-%05d', $sequence);
        }

        return $code;
    }
}
