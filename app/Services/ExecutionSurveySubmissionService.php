<?php

namespace App\Services;

use App\Models\ExecutionSurvey;
use App\Models\ExecutionSurveyResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * Respuesta pública a la encuesta de satisfacción de una ejecución.
 *
 * Cada participante elige al relator que le hizo clases y evalúa solo a ese
 * relator: en ejecuciones con varios relatores divididos por grupos, nadie
 * tiene que evaluar a un relator que no conoció.
 */
class ExecutionSurveySubmissionService
{
    public function rules(ExecutionSurvey $executionSurvey): array
    {
        $rules = [
            'instructor_id' => ['required', Rule::in($this->instructors($executionSurvey)->pluck('id')->all())],
        ];

        foreach ($this->fields($executionSurvey) as $field) {
            $rules["answers.{$field->id}"] = $this->rulesForField($field);
        }

        return $rules;
    }

    public function messages(ExecutionSurvey $executionSurvey): array
    {
        $messages = [
            'instructor_id.required' => 'Elige el relator que te hizo clases.',
            'instructor_id.in' => 'El relator elegido no corresponde a este curso.',
        ];

        foreach ($this->fields($executionSurvey) as $field) {
            $messages["answers.{$field->id}.required"] = "\"{$field->label}\" es obligatorio.";
            $messages["answers.{$field->id}.in"] = "La opción elegida en \"{$field->label}\" no es válida.";
            $messages["answers.{$field->id}.max"] = "La respuesta de \"{$field->label}\" es demasiado larga.";
        }

        return $messages;
    }

    /**
     * Guarda una respuesta: todas las preguntas, asociadas al relator elegido.
     */
    public function store(ExecutionSurvey $executionSurvey, int $instructorId, array $answers): ExecutionSurveyResponse
    {
        return DB::transaction(function () use ($executionSurvey, $instructorId, $answers) {
            $response = ExecutionSurveyResponse::create([
                'execution_survey_id' => $executionSurvey->id,
                'submitted_at' => now(),
            ]);

            foreach ($this->fields($executionSurvey) as $field) {
                $value = $answers[$field->id] ?? null;

                $response->answers()->create([
                    'survey_template_field_id' => $field->id,
                    'instructor_id' => $instructorId,
                    'value' => $value === null ? null : trim((string) $value),
                ]);
            }

            return $response;
        });
    }

    public function instructors(ExecutionSurvey $executionSurvey): Collection
    {
        return $executionSurvey->execution->instructors;
    }

    private function fields(ExecutionSurvey $executionSurvey): Collection
    {
        return $executionSurvey->template->sections->flatMap->fields;
    }

    private function rulesForField($field): array
    {
        $rules = [$field->required ? 'required' : 'nullable', 'string'];

        if (in_array($field->type, ['radio', 'select'], true) && ! empty($field->options)) {
            $rules[] = Rule::in(array_map('strval', $field->options));
        } else {
            $rules[] = 'max:5000';
        }

        return $rules;
    }
}
