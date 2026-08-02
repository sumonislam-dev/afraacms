<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Seed the default setting rows from config/settings.php.
     */
    public function run(): void
    {
        $sortOrder = 0;

        $computedDefaults = [
            'site_name' => config('app.name', 'AfraaCMS'),
            'copyright' => '© '.now()->year.' '.config('app.name', 'AfraaCMS').'. All rights reserved.',
        ];

        foreach (config('settings.groups', []) as $groupKey => $group) {
            foreach ($group['fields'] as $fieldKey => $field) {
                $default = $computedDefaults[$fieldKey] ?? ($field['default'] ?? '');

                Setting::firstOrCreate(
                    ['key' => $fieldKey],
                    [
                        'group' => $groupKey,
                        'value' => \is_bool($default) ? (string) (int) $default : (string) $default,
                        'type' => $field['type'],
                        'description' => $field['description'] ?? null,
                        'autoload' => true,
                        'sort_order' => $sortOrder++,
                    ]
                );
            }
        }
    }
}
