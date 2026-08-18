<?php

namespace App\Http\Controllers\Admin;

use App\CMS\Services\SettingService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSettingsRequest;
use App\Models\Page;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function __construct(private readonly SettingService $settings) {}

    /**
     * Display the tabbed settings screen.
     */
    public function edit(): View
    {
        $this->authorize('viewAny', Setting::class);

        $groups = config('settings.groups', []);

        foreach ($groups as &$group) {
            foreach ($group['fields'] as &$field) {
                if (($field['options'] ?? null) === 'timezones') {
                    $field['resolved_options'] = collect(timezone_identifiers_list())
                        ->mapWithKeys(fn (string $timezone) => [$timezone => $timezone])
                        ->all();
                }

                if (($field['options'] ?? null) === 'pages') {
                    // Note: numeric-looking string keys (page ids) are coerced back to
                    // ints by PHP's array key normalization, so the option-selected
                    // comparison in _field.blade.php casts both sides to string.
                    $field['resolved_options'] = ['' => '— None —'] + Page::published()
                        ->orderBy('title')
                        ->pluck('title', 'id')
                        ->all();
                }

                if (is_array($field['options'] ?? null)) {
                    $field['resolved_options'] = $field['options'];
                }
            }
        }
        unset($group, $field);

        return view('admin.settings.edit', [
            'groups' => $groups,
            'values' => Setting::query()->pluck('value', 'key'),
            'canEdit' => auth()->user()->can('update', Setting::class),
            'canEditDeveloper' => auth()->user()->can('settings.developer'),
        ]);
    }

    /**
     * Update every setting submitted from the tabbed settings form.
     *
     * Fields flagged "locked" in config/settings.php (the developer credit)
     * stay Super-Admin-only regardless of the submitted payload: an Editor
     * who has been granted general settings.edit still cannot touch them,
     * even by forging a POST past their disabled form field.
     */
    public function update(UpdateSettingsRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->user()->cannot('settings.developer')) {
            foreach (config('settings.groups', []) as $group) {
                foreach ($group['fields'] as $key => $field) {
                    if ($field['locked'] ?? false) {
                        unset($data[$key]);
                    }
                }
            }
        }

        $this->settings->updateMany($data);

        return redirect()->route('admin.settings.edit')->with('success', __('Settings updated successfully.'));
    }
}
