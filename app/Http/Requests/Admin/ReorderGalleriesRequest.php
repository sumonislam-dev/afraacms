<?php

namespace App\Http\Requests\Admin;

use App\Models\Gallery;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class ReorderGalleriesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('gallery.edit');
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
     * Every id in the submitted order must be a real album, so a request
     * can't be used to sneak in ids that don't exist.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $validIds = Gallery::query()->pluck('id')->all();
            $submittedIds = $this->input('order', []);

            if (array_diff($submittedIds, $validIds) || array_diff($validIds, $submittedIds)) {
                $validator->errors()->add('order', __('The submitted album order is invalid.'));
            }
        });
    }
}
