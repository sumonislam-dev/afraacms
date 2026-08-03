<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable(['meta_title', 'meta_description', 'meta_image', 'canonical_url', 'robots'])]
class SeoMeta extends Model
{
    protected $table = 'seo_meta';

    public function seoable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get this override's image URL, resolved from its stored MediaItem id.
     */
    public function getImageUrlAttribute(): ?string
    {
        return media_url($this->meta_image);
    }

    /**
     * Create or update the SEO override for a given model from a flat
     * "seo_*"-prefixed field set (as submitted by the shared admin SEO
     * fields partial), e.g. ['seo_title' => ..., 'seo_image' => ...].
     *
     * Silently does nothing if none of those keys are present, so every
     * content type's create()/update() can call this unconditionally
     * without checking whether SEO fields were actually submitted.
     */
    public static function syncFor(Model $seoable, array $data): void
    {
        $map = [
            'seo_title' => 'meta_title',
            'seo_description' => 'meta_description',
            'seo_image' => 'meta_image',
            'seo_canonical' => 'canonical_url',
            'seo_robots' => 'robots',
        ];

        if (! array_intersect_key($data, $map)) {
            return;
        }

        $values = [];

        foreach ($map as $inputKey => $column) {
            if (array_key_exists($inputKey, $data)) {
                $values[$column] = $data[$inputKey];
            }
        }

        $seoable->seo()->updateOrCreate([], $values);
    }

    /**
     * Reduce a (possibly null) SeoMeta relation into the plain array shape
     * every content type's cached public array embeds it as - reused so
     * PageService/ProjectService/GalleryService don't each repeat this.
     *
     * @return array{title: ?string, description: ?string, image_url: ?string, canonical_url: ?string, robots: ?string}
     */
    public static function toCacheArray(?self $seo): array
    {
        return [
            'title' => $seo?->meta_title,
            'description' => $seo?->meta_description,
            'image_url' => $seo?->image_url,
            'canonical_url' => $seo?->canonical_url,
            'robots' => $seo?->robots,
        ];
    }
}
