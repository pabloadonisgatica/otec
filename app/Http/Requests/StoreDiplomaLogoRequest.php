<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDiplomaLogoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'logo_name' => ['nullable', 'string', 'max:100'],
            'logo_file' => ['required', 'file', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
        ];
    }

    public function attributes(): array
    {
        return [
            'logo_name' => 'nombre del logo',
            'logo_file' => 'imagen del logo',
        ];
    }
}
