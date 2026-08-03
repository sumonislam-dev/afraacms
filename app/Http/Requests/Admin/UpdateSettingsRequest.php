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
                $rules[$key] = match (true) {
                    ($field['options'] ?? null) === 'pages' => ['nullable', 'integer', Rule::exists('pages', 'id')],
                    $field['type'] === 'email' => ['nullable', 'email', 'max:255'],
                    $field['type'] === 'url' => ['nullable', 'url', 'max:2048'],
                    $field['type'] === 'number' => ['nullable', 'numeric'],
                    $field['type'] === 'boolean' => ['nullable', 'boolean'],
                    $field['type'] === 'color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
                    $field['type'] === 'image' => ['nullable', 'integer', Rule::exists('media_items', 'id')],
                    $field['type'] === 'textarea' => ['nullable', 'string'],
                    default => ['nullable', 'string', 'max:255'],
                };
            }
        }

        return $rules;
    }
}
