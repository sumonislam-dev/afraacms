<?php

namespace App\Http\Requests\Admin;

use App\Models\AnnualReport;
use Illuminate\Foundation\Http\FormRequest;

class StoreAnnualReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', AnnualReport::class);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'year' => ['required', 'string', 'max:20'],
            'attachment' => ['required', 'file', 'mimes:pdf', 'max:20480'],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['nullable', 'integer'],
        ];
    }
}
