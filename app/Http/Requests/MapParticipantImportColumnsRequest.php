<?php

namespace App\Http\Requests;

use App\Services\ParticipantImport\ParticipantImportFields;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class MapParticipantImportColumnsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'mapping' => ['required', 'array'],
            'mapping.*' => ['nullable', Rule::in(array_keys(ParticipantImportFields::FIELDS))],
        ];
    }

    /** Cada campo obligatorio debe tener al menos una columna asignada. */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                $used = array_filter((array) $this->input('mapping', []));

                foreach (ParticipantImportFields::REQUIRED as $field) {
                    if (! in_array($field, $used, true)) {
                        $validator->errors()->add(
                            'mapping',
                            'Falta asignar la columna de ' . ParticipantImportFields::FIELDS[$field] . '.'
                        );
                    }
                }
            },
        ];
    }

    /** @return string[] índice de columna => campo ('' = no importar) */
    public function mapping(): array
    {
        return array_map(fn ($field) => (string) $field, (array) $this->input('mapping', []));
    }
}
