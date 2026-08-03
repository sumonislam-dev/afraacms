<?php

namespace App\Support;

use App\Models\GalleryItem;
use App\Models\MenuItem;
use App\Models\SectionItem;
use Spatie\Activitylog\Models\Activity;

class ActivitySubject
{
    /**
     * Resolve a human-readable label for an activity log entry's subject,
     * e.g. "Web Design (in Services)" for a nested menu item.
     */
    public static function label(Activity $activity): ?string
    {
        $subject = $activity->subject;
        $label = $subject?->title ?? $subject?->name ?? $subject?->caption ?? $subject?->label ?? null;

        $parentContext = match (true) {
            $subject instanceof GalleryItem => $subject->gallery?->title,
            $subject instanceof MenuItem => $subject->menu?->name,
            $subject instanceof SectionItem => $subject->section?->heading ?: $subject->section?->type,
            default => null,
        };

        if ($parentContext) {
            $label = trim(($label ? "{$label} " : '')."(in {$parentContext})");
        }

        return $label;
    }
}
