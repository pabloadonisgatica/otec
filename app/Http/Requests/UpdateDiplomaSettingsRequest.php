<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDiplomaSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'signer_name' => ['required', 'string', 'max:255'],
            'signer_title' => ['required', 'string', 'max:255'],
            'form_code' => ['nullable', 'string', 'max:50'],
            // PNG o WEBP para conservar el fondo transparente sobre el diploma.
            'signature' => ['nullable', 'file', 'image', 'mimes:png,webp', 'max:2048'],
            'remove_signature' => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'signer_name' => 'nombre del firmante',
            'signer_title' => 'cargo del firmante',
            'form_code' => 'código del formulario',
            'signature' => 'firma y timbre',
        ];
    }

    public function messages(): array
    {
        return [
            'signature.mimes' => 'La firma debe ser PNG o WEBP (con fondo transparente).',
        ];
    }
}
