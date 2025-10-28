<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VehicleExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => 'nullable|string|max:255',
            'types' => 'nullable|array',
            'types.*' => 'in:fuel,insurance,service',
            'min_cost' => 'nullable|numeric|min:0',
            'max_cost' => 'nullable|numeric|min:0|gte:min_cost',
            'min_date' => 'nullable|date',
            'max_date' => 'nullable|date|after_or_equal:min_date',
            'sort_by' => 'nullable|in:cost,created_at',
            'sort_direction' => 'nullable|in:asc,desc',
        ];
    }

    public function messages(): array
    {
        return [
            'types.*.in' => 'Type must be one of: fuel, insurance, service',
            'max_cost.gte' => 'Maximum cost must be greater than or equal to minimum cost',
            'max_date.after_or_equal' => 'Maximum date must be after or equal to minimum date',
        ];
    }
}
