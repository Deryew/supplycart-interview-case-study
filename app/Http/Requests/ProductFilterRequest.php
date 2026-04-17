<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'brand_id' => ['nullable', 'exists:brands,id'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'search' => ['nullable', 'string', 'max:100'],
        ];
    }
}
