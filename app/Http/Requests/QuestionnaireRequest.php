<?php

namespace App\Http\Requests;

use App\Enums\QuestionnaireType;
use App\Models\Questionnaire;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class QuestionnaireRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $questionnaire = $this->route('questionnaire');
        $creating = ! $questionnaire;

        // Encuesta de satisfacción: el relator se pide al crear y mientras no haya respuestas.
        $instructorRule = match (true) {
            $creating => 'required_if:type,' . QuestionnaireType::Satisfaction->value,
            $questionnaire->type === QuestionnaireType::Satisfaction && ! $questionnaire->responses()->exists() => 'required',
            default => 'nullable',
        };

        return [
            // El tipo se elige al crear y no cambia después.
            'type' => [$creating ? 'required' : 'prohibited', Rule::enum(QuestionnaireType::class)],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'is_active' => ['boolean'],
            'closes_at' => ['nullable', 'date'],

            'association' => ['required', Rule::in(['none', 'company', 'course', 'execution'])],
            'company_id' => ['nullable', 'required_if:association,company', 'exists:companies,id'],
            'course_id' => ['nullable', 'required_if:association,course', 'exists:courses,id'],
            'execution_id' => ['nullable', 'required_if:association,execution', 'exists:executions,id'],

            'respondent_fields' => ['nullable', 'array:' . implode(',', array_keys(Questionnaire::RESPONDENT_FIELDS))],
            'respondent_fields.*' => ['nullable', Rule::in(['', 'optional', 'required'])],

            'survey_template_id' => $creating
                ? ['nullable', 'required_if:type,' . QuestionnaireType::Satisfaction->value, 'exists:survey_templates,id']
                : ['prohibited'],
            'instructor_name' => ['nullable', $instructorRule, 'string', 'max:150'],

            'banner' => ['nullable', 'image', 'max:2048'],
            'remove_banner' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'company_id.required_if' => 'Selecciona la empresa.',
            'course_id.required_if' => 'Selecciona el curso.',
            'execution_id.required_if' => 'Selecciona la ejecución.',
            'survey_template_id.required_if' => 'Elige la plantilla de satisfacción.',
            'instructor_name.required_if' => 'Escribe el nombre del relator.',
            'instructor_name.required' => 'Escribe el nombre del relator.',
        ];
    }

    public function attributes(): array
    {
        return [
            'type' => 'tipo',
            'name' => 'nombre',
            'description' => 'descripción',
            'closes_at' => 'fecha de cierre',
            'banner' => 'banner',
            'survey_template_id' => 'plantilla de satisfacción',
            'instructor_name' => 'nombre del relator',
        ];
    }
}
