<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * A single yearly report PDF, listed on the public "Annual Reports" page.
 * The PDF itself is attached directly via Spatie's media library, in its
 * own collection - same pattern as NewsPost/Story's "attachment".
 */
#[Fillable(['title', 'year', 'is_active', 'sort_order'])]
class AnnualReport extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, LogsActivity;

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('attachment')->singleFile();
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
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function getAttachmentUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('attachment') ?: null;
    }

    public function getAttachmentFileNameAttribute(): ?string
    {
        return $this->getFirstMedia('attachment')?->file_name;
    }
}
