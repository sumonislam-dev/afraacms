<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

#[Fillable(['name', 'father_name', 'mother_name', 'date_of_birth', 'phone', 'email', 'address'])]
class Student extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected static function booted(): void
    {
        static::creating(function (Student $student) {
            $student->student_code ??= static::generateStudentCode();
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable()->logOnlyDirty()->dontSubmitEmptyLogs();
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
        ];
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Generate a unique, human-readable student code, e.g. STU-2026-00042.
     *
     * Sequence is per registration year and derived from the current row
     * count - the while-loop guards the rare concurrent-create collision
     * rather than relying on the count alone (same approach as
     * Certificate::generateCertificateNumber()).
     */
    protected static function generateStudentCode(): string
    {
        $year = now()->year;
        $sequence = static::withTrashed()->whereYear('created_at', $year)->count() + 1;
        $code = sprintf('STU-%d-%05d', $year, $sequence);

        while (static::withTrashed()->where('student_code', $code)->exists()) {
            $sequence++;
            $code = sprintf('STU-%d-%05d', $year, $sequence);
        }

        return $code;
    }
}
