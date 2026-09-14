<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

#[Fillable(['project_id', 'category_id', 'title', 'slug', 'excerpt', 'content', 'cover_image', 'published_at', 'is_featured', 'status'])]
class Story extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, LogsActivity, SoftDeletes;

    /**
     * An optional PDF/image attached directly to this model via Spatie's
     * media library, kept apart from the MediaItem-backed cover_image (see
     * NewsPost for the same pattern).
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('attachment')->singleFile();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable()->logOnlyDirty()->dontSubmitEmptyLogs();
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'published_at' => 'date',
            'is_featured' => 'boolean',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(StoryCategory::class);
    }

    public function seo(): MorphOne
    {
        return $this->morphOne(SeoMeta::class, 'seoable');
    }

    /**
     * Scope a query to only published stories.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    /**
     * Get this story's cover image URL, resolved from its stored MediaItem id.
     */
    public function getCoverImageUrlAttribute(): ?string
    {
        return media_url($this->cover_image);
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
