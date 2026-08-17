<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('role'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('roles', 'name')->where('guard_name', 'web')->ignore($this->route('role')),
            ],
            'permissions' => ['array'],
            // Restricted to permissions the acting user already holds, not
            // merely "any permission that exists" - otherwise a non-Super
            // Admin with roles.edit could pad any role (including their
            // own) with permissions they don't have, bypassing every
            // Super-Admin-only restriction.
            'permissions.*' => ['string', Rule::in($this->user()->getAllPermissions()->pluck('name'))],
        ];
    }
}
