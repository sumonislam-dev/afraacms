<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class ReorderMenuItemsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('menu'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * The tree can nest arbitrarily deep, which Laravel's rule syntax can't
     * express for unknown depth, so structural/ownership checks happen in
     * withValidator() instead.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'tree' => ['present', 'array'],
        ];
    }

    /**
     * Configure the validator instance.
     *
     * Every id in the submitted tree must belong to this menu, so a request
     * can't be used to move another menu's items or reference bogus ids.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $menu = $this->route('menu');
            $validIds = $menu->items()->pluck('id')->all();
            $submittedIds = $this->flattenIds($this->input('tree', []));

            if (array_diff($submittedIds, $validIds)) {
                $validator->errors()->add('tree', __('The submitted menu structure is invalid.'));
            }
        });
    }

    /**
     * @param  array<int, array{id?: mixed, children?: array}>  $nodes
     * @return array<int, int>
     */
    private function flattenIds(array $nodes): array
    {
        $ids = [];

        foreach ($nodes as $node) {
            $ids[] = (int) ($node['id'] ?? 0);
            $ids = [...$ids, ...$this->flattenIds($node['children'] ?? [])];
        }

        return $ids;
    }
}
