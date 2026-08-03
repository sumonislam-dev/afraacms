<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['type', 'title', 'subtitle', 'image', 'button_text', 'button_url', 'is_active', 'starts_at', 'ends_at', 'sort_order'])]
class Banner extends Model
{
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Scope a query to only banners currently active and within their schedule window.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', now()));
    }

    /**
     * Get this banner's image URL, resolved from its stored MediaItem id.
     */
    public function getImageUrlAttribute(): ?string
    {
        return media_url($this->image);
    }
}
