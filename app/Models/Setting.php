<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['group', 'key', 'value', 'type', 'description', 'autoload', 'sort_order'])]
class Setting extends Model
{
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'autoload' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Scope a query to only include settings belonging to the given group.
     */
    public function scopeGroup(Builder $query, string $group): Builder
    {
        return $query->where('group', $group);
    }
}
