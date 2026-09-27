<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDiplomaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'execution_id' => ['required', 'exists:executions,id'],
            'diploma_logo_id' => ['nullable', 'integer', 'exists:diploma_logos,id'],
            'participant_ids' => ['required', 'array', 'min:1'],
            'participant_ids.*' => ['integer', 'exists:participants,id'],
        ];
    }

    public function attributes(): array
    {
        return [
            'execution_id' => 'ejecución',
            'diploma_logo_id' => 'logo adicional',
            'participant_ids' => 'participantes',
        ];
    }
}
