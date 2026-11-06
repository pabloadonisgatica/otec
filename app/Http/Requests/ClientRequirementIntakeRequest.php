<?php

namespace App\Http\Requests;

use App\Models\Budget;
use App\Models\ClientRequirementIntake;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClientRequirementIntakeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'requires_sence' => $this->boolean('requires_sence'),
            'review_status' => $this->input('review_status', 'pending'),
        ]);
    }

    public function rules(): array
    {
        return [
            // 7.2.1 Determinación del requisito
            'company_id' => ['required', 'exists:companies,id'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'requested_at' => ['required', 'date'],
            'title' => ['required', 'string', 'max:255'],
            'details' => ['required', 'string'],
            'participants_estimate' => ['nullable', 'integer', 'min:1', 'max:5000'],
            'modality' => ['nullable', Rule::in(array_keys(ClientRequirementIntake::MODALITIES))],
            'desired_start_date' => ['nullable', 'date'],
            'requires_sence' => ['boolean'],
            'attachment' => ['nullable', 'file', 'max:20480', 'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png'],
            'remove_attachment' => ['nullable', 'boolean'],

            // 7.2.2 Revisión del requisito
            'review_status' => ['required', Rule::in(array_keys(ClientRequirementIntake::REVIEW_STATUSES))],
            'reviewed_by' => ['nullable', 'required_unless:review_status,pending', 'exists:users,id'],
            'reviewed_at' => ['nullable', 'required_unless:review_status,pending', 'date', 'after_or_equal:requested_at'],
            'review_notes' => ['nullable', 'required_if:review_status,not_feasible', 'string'],
            'budget_id' => ['nullable', 'exists:budgets,id', $this->budgetBelongsToCompany()],
        ];
    }

    public function attributes(): array
    {
        return [
            'company_id' => 'empresa',
            'contact_name' => 'contacto',
            'contact_email' => 'correo del contacto',
            'contact_phone' => 'teléfono del contacto',
            'requested_at' => 'fecha de solicitud',
            'title' => 'título',
            'details' => 'detalle del requerimiento',
            'participants_estimate' => 'N° de participantes',
            'modality' => 'modalidad',
            'desired_start_date' => 'fecha deseada de inicio',
            'attachment' => 'documento adjunto',
            'review_status' => 'resultado de la revisión',
            'reviewed_by' => 'revisor',
            'reviewed_at' => 'fecha de revisión',
            'review_notes' => 'observaciones de la revisión',
            'budget_id' => 'presupuesto',
        ];
    }

    public function messages(): array
    {
        return [
            'reviewed_by.required_unless' => 'Indica quién revisó el requerimiento.',
            'reviewed_at.required_unless' => 'Indica la fecha de la revisión.',
            'review_notes.required_if' => 'Explica por qué no se puede cumplir el requerimiento.',
        ];
    }

    /**
     * El presupuesto asociado debe ser de la misma empresa del requerimiento.
     */
    private function budgetBelongsToCompany(): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail) {
            $budgetCompany = Budget::whereKey($value)->value('company_id');

            if ($budgetCompany && (int) $budgetCompany !== (int) $this->input('company_id')) {
                $fail('El presupuesto seleccionado pertenece a otra empresa.');
            }
        };
    }
}
