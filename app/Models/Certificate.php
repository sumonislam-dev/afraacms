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

#[Fillable(['project_id', 'recipient_name', 'program', 'issued_at', 'status', 'notes'])]
class Certificate extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected static function booted(): void
    {
        static::creating(function (Certificate $certificate) {
            $year = $certificate->issued_at ? Carbon::parse($certificate->issued_at)->year : now()->year;

            $certificate->certificate_number ??= static::generateCertificateNumber($year);
            $certificate->verification_code ??= static::generateVerificationCode();
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
            'issued_at' => 'date',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Scope a query to only certificates that currently verify as valid.
     */
    public function scopeValid(Builder $query): Builder
    {
        return $query->where('status', 'valid');
    }

    /**
     * Generate a unique, human-readable certificate number, e.g. CERT-2026-00042.
     *
     * Sequence is per issue year and derived from the current row count -
     * good enough for this admin panel's realistic (low, manual) issuance
     * volume. The while-loop guards the rare concurrent-create collision
     * rather than relying on the count alone.
     */
    protected static function generateCertificateNumber(int $year): string
    {
        $sequence = static::withTrashed()->whereYear('issued_at', $year)->count() + 1;
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
    protected static function generateVerificationCode(): string
    {
        do {
            $code = Str::random(32);
        } while (static::withTrashed()->where('verification_code', $code)->exists());

        return $code;
    }
}
