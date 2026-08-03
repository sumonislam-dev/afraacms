<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGalleryItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('gallery'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'type' => ['required', Rule::in(['image', 'video'])],
            'image' => ['required_if:type,image', 'nullable', 'integer', Rule::exists('media_items', 'id')],
            'video_url' => ['required_if:type,video', 'nullable', 'url', 'max:2048'],
            'caption' => ['nullable', 'string', 'max:255'],
        ];
    }
}
