<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExecutionReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'purchase_order' => ['nullable', 'string', 'max:255'],
            'invoice_number' => ['nullable', 'string', 'max:255'],
            'guests' => ['nullable', 'string', 'max:255'],
            'observations' => ['nullable', 'string', 'max:10000'],
            'suggestions' => ['nullable', 'string', 'max:10000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'purchase_order' => 'orden de compra',
            'invoice_number' => 'factura',
            'guests' => 'invitados',
            'observations' => 'observaciones',
            'suggestions' => 'sugerencias',
        ];
    }
}
