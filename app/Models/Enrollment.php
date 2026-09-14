<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * An enrollment is not itself a certificate - the certificate_status/
 * certificate_number/verification_code below are computed accessors, not
 * real columns: they read through the certificate() relation, so there's
 * nothing to issue/revoke here directly. See EnrollmentService::issueCertificate()/
 * revokeCertificate(), which create/update the linked Certificate row instead.
 */
#[Fillable([
    'student_id', 'course_id', 'session', 'roll_number', 'registration_number',
    'admission_date', 'completion_date', 'grade', 'grade_point', 'grade_scale', 'result_status',
])]
class Enrollment extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

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
     * The Certificate record issued for this enrollment - null until
     * EnrollmentService::issueCertificate() creates one.
     */
    public function certificate(): HasOne
    {
        return $this->hasOne(Certificate::class);
    }

    /**
     * "not_issued" | "valid" | "revoked" - mirrors the linked Certificate's
     * own status, kept as a same-named computed attribute so every existing
     * view/query written against "certificate_status" as if it were a column
     * keeps working unchanged.
     */
    protected function certificateStatus(): Attribute
    {
        return Attribute::get(fn () => $this->certificate?->status ?? 'not_issued');
    }

    protected function certificateNumber(): Attribute
    {
        return Attribute::get(fn () => $this->certificate?->certificate_number);
    }

    protected function verificationCode(): Attribute
    {
        return Attribute::get(fn () => $this->certificate?->verification_code);
    }

    /**
     * Scope a query to only enrollments whose certificate currently verifies
     * as valid (not_issued and revoked are both excluded). certificate_status
     * is a computed accessor, not a column, so this has to join through the
     * relation rather than a plain where().
     */
    public function scopeCertificateValid(Builder $query): Builder
    {
        return $query->whereHas('certificate', fn ($q) => $q->where('status', 'valid'));
    }
}
