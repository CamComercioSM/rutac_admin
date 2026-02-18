<?php

namespace App\Http\Requests\Whatsapp;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:100', 'unique:whatsapp_template_categories,code'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nombre',
            'code' => 'código',
            'description' => 'descripción',
            'is_active' => 'activo',
        ];
    }
}
