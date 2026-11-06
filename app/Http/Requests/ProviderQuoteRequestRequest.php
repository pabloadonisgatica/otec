<?php

namespace App\Http\Requests;

use App\Models\ProviderQuoteRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProviderQuoteRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Quita las filas de ítems que vienen completamente vacías y limpia el monto
     * ("$1.250.000" → 1250000).
     */
    protected function prepareForValidation(): void
    {
        $items = collect($this->input('items', []))
            ->filter(fn ($item) => is_array($item)
                && (filled($item['description'] ?? null) || filled($item['quantity'] ?? null)))
            ->map(fn ($item) => [
                'description' => trim((string) ($item['description'] ?? '')),
                'quantity' => str_replace(',', '.', (string) ($item['quantity'] ?? '')),
                'unit' => trim((string) ($item['unit'] ?? '')),
            ])
            ->values()
            ->all();

        $amount = $this->input('quoted_amount');

        $this->merge([
            'items' => $items,
            'status' => $this->input('status', 'sent'),
            'quoted_amount' => filled($amount) ? preg_replace('/\D/', '', (string) $amount) : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'provider_id' => ['required', 'exists:providers,id'],
            'execution_id' => ['nullable', 'exists:executions,id'],
            'requested_at' => ['required', 'date'],
            'needed_by' => ['nullable', 'date', 'after_or_equal:requested_at'],
            'items' => ['required', 'array', 'min:1', 'max:50'],
            'items.*.description' => ['required', 'string', 'max:500'],
            'items.*.quantity' => ['required', 'numeric', 'gt:0', 'max:1000000'],
            'items.*.unit' => ['nullable', 'string', 'max:50'],
            'conditions' => ['nullable', 'string'],

            'status' => ['required', Rule::in(array_keys(ProviderQuoteRequest::STATUSES))],
            'responded_at' => ['nullable', 'required_unless:status,sent', 'date', 'after_or_equal:requested_at'],
            'quoted_amount' => ['nullable', 'integer', 'min:0', 'max:9999999999'],
            'response_notes' => ['nullable', 'string'],
            'attachment' => ['nullable', 'file', 'max:20480', 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png'],
            'remove_attachment' => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'provider_id' => 'proveedor',
            'execution_id' => 'ejecución',
            'requested_at' => 'fecha de solicitud',
            'needed_by' => 'fecha requerida',
            'items' => 'ítems',
            'items.*.description' => 'descripción del ítem',
            'items.*.quantity' => 'cantidad',
            'items.*.unit' => 'unidad',
            'conditions' => 'condiciones',
            'status' => 'estado',
            'responded_at' => 'fecha de respuesta',
            'quoted_amount' => 'monto cotizado',
            'response_notes' => 'observaciones',
            'attachment' => 'cotización del proveedor',
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Agrega al menos un ítem a cotizar.',
            'items.*.description.required' => 'Cada ítem necesita una descripción.',
            'items.*.quantity.required' => 'Cada ítem necesita una cantidad.',
            'responded_at.required_unless' => 'Indica la fecha en que respondió el proveedor.',
        ];
    }
}
