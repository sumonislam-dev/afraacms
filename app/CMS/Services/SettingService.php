<?php

namespace App\CMS\Services;

use App\CMS\Services\Concerns\CachesForFrontend;
use App\Models\Setting;

class SettingService
{
    use CachesForFrontend;

    protected function cacheKey(): string
    {
        return 'settings.all';
    }

    /**
     * Get every setting's value, from cache where possible.
     *
     * @return array<string, string>
     */
    public function all(): array
    {
        return $this->rememberForever(fn () => Setting::query()->pluck('value', 'key')->all());
    }

    /**
     * Get a single setting's value by key.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->all()[$key] ?? $default;
    }

    /**
     * Update a batch of settings, then clear the cache.
     *
     * Image-type values are Media Library MediaItem ids (selected via the
     * media picker), so they update exactly like any other value here.
     *
     * @param  array<string, mixed>  $values
     */
    public function updateMany(array $values): void
    {
        foreach ($values as $key => $value) {
            $setting = Setting::where('key', $key)->first();

            if (! $setting) {
                continue;
            }

            // A blank password field means "keep the existing secret".
            if ($setting->type === 'password' && $value === '') {
                continue;
            }

            $setting->update(['value' => $value]);
        }

        $this->forget();
    }
}
