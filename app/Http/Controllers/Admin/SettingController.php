<?php

namespace App\Http\Controllers\Admin;

use App\CMS\Services\SettingService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSettingsRequest;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function __construct(private readonly SettingService $settings)
    {
    }

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
            }
        }
        unset($group, $field);

        return view('admin.settings.edit', [
            'groups' => $groups,
            'values' => Setting::query()->pluck('value', 'key'),
            'canEdit' => auth()->user()->can('update', Setting::class),
        ]);
    }

    /**
     * Update every setting submitted from the tabbed settings form.
     */
    public function update(UpdateSettingsRequest $request): RedirectResponse
    {
        $this->settings->updateMany($request->validated());

        return redirect()->route('admin.settings.edit')->with('success', __('Settings updated successfully.'));
    }
}
