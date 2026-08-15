<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

#[Fillable(['project_id', 'donor_name', 'donor_email', 'amount', 'currency', 'method', 'donated_at', 'status', 'notes'])]
class Donation extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected static function booted(): void
    {
        static::creating(function (Donation $donation) {
            $year = $donation->donated_at ? Carbon::parse($donation->donated_at)->year : now()->year;

            $donation->receipt_number ??= static::generateReceiptNumber($year);
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
            'amount' => 'decimal:2',
            'donated_at' => 'date',
            'receipt_sent_at' => 'datetime',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Scope a query to only completed (non-refunded) donations.
     */
    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', 'completed');
    }

    /**
     * Generate a unique, human-readable receipt number, e.g. RCPT-2026-00042.
     *
     * Sequence is per donation year and derived from the current row count -
     * the while-loop guards the rare concurrent-create collision rather than
     * relying on the count alone (same approach as Certificate::generateCertificateNumber()).
     */
    protected static function generateReceiptNumber(int $year): string
    {
        $sequence = static::withTrashed()->whereYear('donated_at', $year)->count() + 1;
        $number = sprintf('RCPT-%d-%05d', $year, $sequence);

        while (static::withTrashed()->where('receipt_number', $number)->exists()) {
            $sequence++;
            $number = sprintf('RCPT-%d-%05d', $year, $sequence);
        }

        return $number;
    }
}
