<?php

namespace App\Http\Requests\Whatsapp;

use Illuminate\Foundation\Http\FormRequest;

class SendTestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'phone' => ['required', 'string', 'max:20'],
            'template_name' => ['required', 'string', 'max:255'],
            'data' => ['nullable', 'array'],
            'data.*' => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'phone' => 'número de WhatsApp',
            'template_name' => 'nombre de plantilla',
            'data' => 'datos de la plantilla',
        ];
    }
}
