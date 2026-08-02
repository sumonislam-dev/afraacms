<?php

use Illuminate\Support\Facades\Storage;

if (! function_exists('media_url')) {
    /**
     * Resolve a public-disk stored path (e.g. a Setting's image value) to a URL.
     *
     * Centralizes this lookup so it isn't duplicated across every view that
     * displays an uploaded image (logo, favicon, OG image, etc.). Once the
     * Media Library module is built, this is the one place to redirect calls
     * through it instead of the raw "public" disk.
     */
    function media_url(?string $path): ?string
    {
        return $path ? Storage::disk('public')->url($path) : null;
    }
}
