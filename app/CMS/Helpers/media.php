<?php

use App\Models\MediaItem;

if (! function_exists('media_url')) {
    /**
     * Resolve a stored Media Library reference (a MediaItem id, e.g. one of
     * Settings' image-type values) to a usable URL.
     *
     * Centralizes this lookup so it isn't duplicated across every view that
     * displays an uploaded image (logo, favicon, OG image, etc.), and gives
     * the Media Library module the single place other modules resolve
     * through instead of talking to storage/media models directly.
     */
    function media_url(mixed $mediaItemId): ?string
    {
        if (empty($mediaItemId)) {
            return null;
        }

        return MediaItem::find($mediaItemId)?->file_url;
    }
}
