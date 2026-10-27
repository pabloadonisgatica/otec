<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Descarga masiva: o diplomas seleccionados, o todos los del filtro
 * actual del listado (búsqueda y/o curso). Sin filtro no se permite.
 */
class DownloadDiplomasRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'diploma_ids' => ['nullable', 'array', 'min:1'],
            'diploma_ids.*' => ['integer', 'exists:diplomas,id'],
            'q' => ['nullable', 'string', 'max:100'],
            'course_id' => ['nullable', 'integer', 'required_without_all:diploma_ids,q'],
        ];
    }

    public function messages(): array
    {
        return [
            'course_id.required_without_all' => 'Selecciona diplomas o filtra por curso o búsqueda antes de descargar.',
        ];
    }

    public function attributes(): array
    {
        return [
            'diploma_ids' => 'diplomas seleccionados',
        ];
    }
}
