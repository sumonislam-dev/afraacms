<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

#[Fillable(['menu_id', 'parent_id', 'label', 'type', 'url', 'open_in_new_tab', 'icon', 'is_active', 'sort_order'])]
class MenuItem extends Model
{
    use LogsActivity;

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
            'open_in_new_tab' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order');
    }

    /**
     * The absolute URL to link to: the raw value for external links, or a
     * site-relative path resolved through url() for internal ones.
     */
    public function getResolvedUrlAttribute(): string
    {
        if ($this->type === 'external') {
            return $this->url;
        }

        return url($this->url === '/' ? '' : $this->url);
    }

    /**
     * The anchor "target" to use, per the item's own "open in new tab" flag.
     */
    public function getTargetAttribute(): string
    {
        return $this->open_in_new_tab ? '_blank' : '_self';
    }

    /**
     * Nest a flat collection of items (already belonging to one menu) into
     * a parent/children tree, ordered by sort_order at every level.
     *
     * @param  Collection<int, self>  $items
     * @return Collection<int, self>
     */
    public static function buildTree(Collection $items, ?int $parentId = null): Collection
    {
        return $items
            ->where('parent_id', $parentId)
            ->sortBy('sort_order')
            ->values()
            ->map(function (self $item) use ($items) {
                $item->setRelation('children', static::buildTree($items, $item->id));

                return $item;
            });
    }
}
