<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class ReorderGalleryItemsRequest extends FormRequest
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
            'order' => ['present', 'array'],
            'order.*' => ['integer'],
        ];
    }

    /**
     * Configure the validator instance.
     *
     * Every id in the submitted order must belong to this album, so a
     * request can't be used to reorder another album's items.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $gallery = $this->route('gallery');
            $validIds = $gallery->items()->pluck('id')->all();
            $submittedIds = $this->input('order', []);

            if (array_diff($submittedIds, $validIds) || array_diff($validIds, $submittedIds)) {
                $validator->errors()->add('order', __('The submitted item order is invalid.'));
            }
        });
    }
}
