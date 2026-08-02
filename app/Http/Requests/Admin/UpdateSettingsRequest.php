<?php

namespace App\Http\Requests\Admin;

use App\Models\Setting;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSettingsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', Setting::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * Rules are derived from each field's "type" in config/settings.php so
     * every setting is validated without repeating rules per field.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $rules = [];

        foreach (config('settings.groups', []) as $group) {
            foreach ($group['fields'] as $key => $field) {
                $rules[$key] = match ($field['type']) {
                    'email' => ['nullable', 'email', 'max:255'],
                    'url' => ['nullable', 'url', 'max:2048'],
                    'number' => ['nullable', 'numeric'],
                    'boolean' => ['nullable', 'boolean'],
                    'color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
                    'image' => ['nullable', 'integer', Rule::exists('media_items', 'id')],
                    'textarea' => ['nullable', 'string'],
                    default => ['nullable', 'string', 'max:255'],
                };
            }
        }

        return $rules;
    }
}
