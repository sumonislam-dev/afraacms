<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

#[Fillable(['title', 'uploaded_by'])]
class MediaItem extends Model implements HasMedia
{
    use InteractsWithMedia;

    /**
     * Register the "thumb" and "webp" conversions generated for every upload.
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->fit(Fit::Crop, 300, 300)
            ->nonQueued();

        $this->addMediaConversion('webp')
            ->format('webp')
            ->quality(80)
            ->nonQueued();
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        return $query->where(function (Builder $query) use ($term) {
            $query->where('title', 'like', "%{$term}%")
                ->orWhereHas('media', fn (Builder $media) => $media->where('file_name', 'like', "%{$term}%"));
        });
    }

    public function getFileUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('file') ?: null;
    }

    public function getThumbUrlAttribute(): ?string
    {
        return $this->getFirstMedia('file')?->hasGeneratedConversion('thumb')
            ? $this->getFirstMediaUrl('file', 'thumb')
            : $this->file_url;
    }

    public function getWebpUrlAttribute(): ?string
    {
        return $this->getFirstMedia('file')?->hasGeneratedConversion('webp')
            ? $this->getFirstMediaUrl('file', 'webp')
            : null;
    }

    public function getSizeAttribute(): ?int
    {
        return $this->getFirstMedia('file')?->size;
    }

    public function getMimeTypeAttribute(): ?string
    {
        return $this->getFirstMedia('file')?->mime_type;
    }

    public function getDimensionsAttribute(): ?string
    {
        $media = $this->getFirstMedia('file');

        if (! $media || ! $media->getCustomProperty('width')) {
            return null;
        }

        return $media->getCustomProperty('width').'×'.$media->getCustomProperty('height');
    }
}
