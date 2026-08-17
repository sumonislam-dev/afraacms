<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * An enrollment is not itself a certificate - it starts as certificate_status
 * "not_issued" with no certificate_number/verification_code, and only gets
 * those (via the saving() hook below) once its certificate_status moves to
 * "valid" or "revoked" for the first time. This is deliberately different
 * from Certificate, where every row IS a certificate from creation.
 */
#[Fillable([
    'student_id', 'course_id', 'session', 'roll_number', 'registration_number',
    'admission_date', 'completion_date', 'grade', 'grade_point', 'grade_scale', 'result_status',
])]
class Enrollment extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected static function booted(): void
    {
        static::saving(function (Enrollment $enrollment) {
            // Generated here (fired once per actual save) rather than by
            // whoever flips certificate_status, so it can never be computed
            // twice for two different rows before either commits - that
            // race is exactly what happened when a factory's state()
            // closure precomputed it for a whole batch before any insert.
            if ($enrollment->certificate_number || ! in_array($enrollment->certificate_status, ['valid', 'revoked'], true)) {
                return;
            }

            $year = $enrollment->completion_date ? Carbon::parse($enrollment->completion_date)->year : now()->year;

            $enrollment->certificate_number = static::generateCertificateNumber($year);
            $enrollment->verification_code = static::generateVerificationCode();
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
            'admission_date' => 'date',
            'completion_date' => 'date',
            'grade_point' => 'decimal:2',
            'grade_scale' => 'decimal:2',
        ];
    }

    /**
     * withTrashed() so a soft-deleted Student doesn't turn this relation
     * null and break every view that reads $enrollment->student - trashing
     * a student must not corrupt the enrollments that still reference them.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class)->withTrashed();
    }

    /**
     * withTrashed() - same reasoning as student() above.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class)->withTrashed();
    }

    /**
     * Scope a query to only enrollments whose certificate currently verifies
     * as valid (not_issued and revoked are both excluded).
     */
    public function scopeCertificateValid(Builder $query): Builder
    {
        return $query->where('certificate_status', 'valid');
    }

    /**
     * Generate a unique, human-readable certificate number, e.g. CERT-2026-00042.
     *
     * Sequence is per issuance year and derived from the current count of
     * already-issued enrollments - the while-loop guards the rare concurrent
     * collision rather than relying on the count alone (same approach as
     * Certificate::generateCertificateNumber()).
     */
    public static function generateCertificateNumber(int $year): string
    {
        $sequence = static::withTrashed()->whereNotNull('certificate_number')->whereYear('completion_date', $year)->count() + 1;
        $number = sprintf('CERT-%d-%05d', $year, $sequence);

        while (static::withTrashed()->where('certificate_number', $number)->exists()) {
            $sequence++;
            $number = sprintf('CERT-%d-%05d', $year, $sequence);
        }

        return $number;
    }

    /**
     * Generate a unique, non-guessable code used in the public verification
     * URL/QR - deliberately separate from certificate_number so the number
     * printed on paper can't be used to enumerate other recipients' records.
     */
    public static function generateVerificationCode(): string
    {
        do {
            $code = Str::random(32);
        } while (static::withTrashed()->where('verification_code', $code)->exists());

        return $code;
    }
}
