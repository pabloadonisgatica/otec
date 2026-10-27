<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadParticipantImportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_id' => ['required', 'exists:companies,id'],
            'file' => ['required', 'file', 'max:5120', 'mimes:xlsx,xls,csv,txt'],
        ];
    }

    public function messages(): array
    {
        return [
            'file.mimes' => 'El archivo debe ser Excel (.xlsx o .xls) o CSV.',
            'file.max' => 'El archivo no puede pesar más de 5 MB.',
        ];
    }

    public function attributes(): array
    {
        return [
            'company_id' => 'empresa',
            'file' => 'archivo',
        ];
    }
}
