<?php

namespace App\Http\Requests\Admin;

use App\Models\FeaturedVisitor;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFeaturedVisitorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', FeaturedVisitor::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'organization' => ['nullable', 'string', 'max:255'],
            'country' => ['required', 'string', 'max:255'],
            'visited_at' => ['required', 'date'],
            'photo' => ['nullable', 'integer', Rule::exists('media_items', 'id')],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['nullable', 'integer'],
        ];
    }
}
