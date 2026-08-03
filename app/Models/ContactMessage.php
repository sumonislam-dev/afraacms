<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'email', 'subject', 'message', 'ip_address', 'is_read', 'read_at'])]
class ContactMessage extends Model
{
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
            'read_at' => 'datetime',
        ];
    }

    /**
     * Scope a query to only unread messages.
     */
    public function scopeUnread(Builder $query): Builder
    {
        return $query->where('is_read', false);
    }

    /**
     * Mark this message as read, if it isn't already.
     */
    public function markAsRead(): void
    {
        if (! $this->is_read) {
            $this->update(['is_read' => true, 'read_at' => now()]);
        }
    }
}
