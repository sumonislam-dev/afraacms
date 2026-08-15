<?php

namespace Database\Seeders;

use App\CMS\Services\SettingService;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    private int $sortOrder = 0;

    /**
     * Computed defaults that can't be hardcoded in a config file.
     *
     * @var array<string, string>
     */
    private array $computedDefaults;

    /**
     * Seed the default setting rows from config/settings.php and
     * config/seo.php (the dedicated SEO screen's fields - same underlying
     * `settings` table, just edited on a separate, "seo.*"-gated screen).
     */
    public function run(): void
    {
        $this->computedDefaults = [
            'site_name' => config('app.name', 'AfraaCMS'),
            'copyright' => '© '.now()->year.' '.config('app.name', 'AfraaCMS').'. All rights reserved.',
        ];

        foreach (config('settings.groups', []) as $groupKey => $group) {
            foreach ($group['fields'] as $fieldKey => $field) {
                $this->seedField($groupKey, $fieldKey, $field);
            }
        }

        foreach (config('seo.fields', []) as $fieldKey => $field) {
            $this->seedField('sitemap', $fieldKey, $field);
        }

        // Settings are cached forever (see SettingService) - on any
        // environment where that cache is already warm (e.g. re-running
        // this seeder to backfill a newly-added config field on an existing
        // install), inserting rows directly like this would otherwise
        // silently not take effect until something else happened to bust it.
        app(SettingService::class)->forget();
    }

    private function seedField(string $group, string $key, array $field): void
    {
        $default = $this->computedDefaults[$key] ?? ($field['default'] ?? '');

        Setting::firstOrCreate(
            ['key' => $key],
            [
                'group' => $group,
                'value' => \is_bool($default) ? (string) (int) $default : (string) $default,
                'type' => $field['type'],
                'description' => $field['description'] ?? null,
                'autoload' => true,
                'sort_order' => $this->sortOrder++,
            ]
        );
    }
}
