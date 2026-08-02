<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['page_id', 'type', 'heading', 'subheading', 'body', 'image', 'button_text', 'button_url', 'layout', 'is_active', 'sort_order'])]
class Section extends Model
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
            'sort_order' => 'integer',
        ];
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SectionItem::class)->orderBy('sort_order');
    }

    public function getImageUrlAttribute(): ?string
    {
        return media_url($this->image);
    }

    /**
     * The section type's field configuration from config/sections.php.
     *
     * @return array{label: string, fields: array, has_items: bool, item_fields?: array}
     */
    public function typeConfig(): array
    {
        return config("sections.types.{$this->type}", []);
    }
}
