<?php

namespace App\Http\Controllers\Admin;

use App\CMS\Services\SettingService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSeoRequest;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SeoController extends Controller
{
    public function __construct(private readonly SettingService $settings) {}

    /**
     * Display the dedicated sitemap/robots SEO screen.
     *
     * This is deliberately separate from the generic Settings screen (see
     * config/seo.php's header comment): it's gated by "seo.*" instead of
     * "settings.*" so an Editor can manage it without needing full Settings
     * access, but it's backed by the exact same `settings` table/service.
     */
    public function edit(): View
    {
        $fields = config('seo.fields', []);

        foreach ($fields as $key => &$field) {
            if ($key === 'default_robots') {
                $field['resolved_options'] = [
                    'index, follow' => __('Index, Follow'),
                    'noindex, follow' => __('No Index, Follow'),
                    'index, nofollow' => __('Index, No Follow'),
                    'noindex, nofollow' => __('No Index, No Follow'),
                ];
            }
        }
        unset($field);

        return view('admin.seo.edit', [
            'fields' => $fields,
            'values' => Setting::whereIn('key', array_keys($fields))->pluck('value', 'key'),
        ]);
    }

    /**
     * Update the sitemap/robots settings.
     */
    public function update(UpdateSeoRequest $request): RedirectResponse
    {
        $this->settings->updateMany($request->validated());

        return redirect()->route('admin.seo.edit')->with('success', __('SEO settings updated successfully.'));
    }
}
