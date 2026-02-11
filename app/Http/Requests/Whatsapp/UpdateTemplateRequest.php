<?php

namespace App\Http\Requests\Whatsapp;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $template = $this->route('template');
        $templateId = $template instanceof \App\Models\WhatsappTemplate
            ? $template->id
            : (int) $template;

        return [
            'category_id' => ['required', 'exists:whatsapp_template_categories,id'],
            'name' => ['required', 'string', 'max:255', Rule::unique('whatsapp_templates', 'name')->ignore($templateId)],
            'group_code' => ['nullable', 'string', 'max:100'],
            'channel' => ['nullable', 'string', 'max:50'],
            'provider' => ['nullable', 'string', 'max:100'],
            'language' => ['nullable', 'string', 'max:20'],
            'expected_fields' => ['nullable', 'string', function ($attribute, $value, $fail) {
                if ($value === '' || $value === null) {
                    return;
                }
                $decoded = json_decode($value, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $fail('El campo expected_fields debe ser un JSON válido.');
                    return;
                }
                if (! is_array($decoded)) {
                    $fail('El campo expected_fields debe ser un array de objetos.');
                    return;
                }
                foreach ($decoded as $item) {
                    if (! is_array($item) || empty($item['key'])) {
                        $fail('Cada elemento de expected_fields debe tener al menos la clave "key".');
                        return;
                    }
                    if (array_key_exists('required', $item) && ! is_bool($item['required'])) {
                        $fail('El campo "required" en expected_fields debe ser booleano.');
                        return;
                    }
                }
            }],
            'is_active' => ['boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('channel') && $this->channel === '') {
            $this->merge(['channel' => 'whatsapp']);
        }
    }

    public function attributes(): array
    {
        return [
            'category_id' => 'categoría',
            'name' => 'nombre',
            'group_code' => 'código de grupo',
            'channel' => 'canal',
            'provider' => 'proveedor',
            'language' => 'idioma',
            'expected_fields' => 'campos esperados',
            'is_active' => 'activo',
        ];
    }
}
