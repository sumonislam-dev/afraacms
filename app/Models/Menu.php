<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

#[Fillable(['name', 'slug'])]
class Menu extends Model
{
    use HasFactory, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable()->logOnlyDirty()->dontSubmitEmptyLogs();
    }

    /**
     * All of this menu's items, flat (every nesting level).
     */
    public function items(): HasMany
    {
        return $this->hasMany(MenuItem::class);
    }

    /**
     * The menu's items nested into a parent/children tree.
     *
     * Uses whatever is already loaded on the "items" relation, so callers
     * control what's included (e.g. active-only for the frontend, everything
     * for the admin builder) by eager-loading "items" accordingly first.
     */
    public function getTreeAttribute(): Collection
    {
        $items = $this->relationLoaded('items') ? $this->items : $this->items()->get();

        return MenuItem::buildTree($items);
    }
}
