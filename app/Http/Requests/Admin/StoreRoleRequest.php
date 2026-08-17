<?php

namespace App\Http\Requests\Admin;

use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Role::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('roles', 'name')->where('guard_name', 'web')],
            'permissions' => ['array'],
            // Restricted to permissions the acting user already holds, not
            // merely "any permission that exists" - otherwise a non-Super
            // Admin with roles.create could build a new role with every
            // permission (including ones they don't have) and hand it to
            // anyone, bypassing every Super-Admin-only restriction.
            'permissions.*' => ['string', Rule::in($this->user()->getAllPermissions()->pluck('name'))],
        ];
    }
}
