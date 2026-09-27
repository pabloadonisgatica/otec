<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DestroyDiplomasRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'diploma_ids' => ['required', 'array', 'min:1'],
            'diploma_ids.*' => ['integer', 'exists:diplomas,id'],
        ];
    }

    public function attributes(): array
    {
        return [
            'diploma_ids' => 'diplomas seleccionados',
        ];
    }
}
