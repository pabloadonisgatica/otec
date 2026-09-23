<?php

namespace App\Http\Controllers;

use App\Models\ExecutionSurvey;
use App\Models\ExecutionSurveyResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PublicExecutionSurveyController extends Controller
{
    public function show(string $token)
    {
        $executionSurvey = ExecutionSurvey::where('token', $token)
            ->with(['template.sections.fields', 'execution.instructors', 'execution.course'])
            ->firstOrFail();

        $sections    = $executionSurvey->template->sections;
        $instructors = $executionSurvey->execution->instructors;

        return view('execution_survey.show', [
            'executionSurvey' => $executionSurvey,
            'execution'       => $executionSurvey->execution,
            'sections'        => $sections,
            'instructors'     => $instructors,
        ]);
    }

    public function store(Request $request, string $token)
    {
        $executionSurvey = ExecutionSurvey::where('token', $token)
            ->with(['template.sections.fields', 'execution.instructors'])
            ->firstOrFail();

        $sections    = $executionSurvey->template->sections;
        $instructors = $executionSurvey->execution->instructors;

        // Reglas de validación: cada campo de cada sección
        // se valida para cada relator por separado.
        $rules    = [];
        $messages = [];

        foreach ($instructors as $instructor) {
            foreach ($sections as $section) {
                foreach ($section->fields as $field) {
                    $key         = "instructors.{$instructor->id}.{$field->id}";
                    $rules[$key] = $this->rulesForField($field);

                    if ($field->required) {
                        $messages["{$key}.required"] =
                            "\"{$field->label}\" (evaluación de {$instructor->name}) es obligatorio.";
                        $messages["{$key}.in"] =
                            "La opción de \"{$field->label}\" (evaluación de {$instructor->name}) no es válida.";
                    }
                }
            }
        }

        $data = $request->validate($rules, $messages);

        DB::transaction(function () use ($data, $executionSurvey, $sections, $instructors) {
            $response = ExecutionSurveyResponse::create([
                'execution_survey_id' => $executionSurvey->id,
                'submitted_at'        => now(),
            ]);

            foreach ($instructors as $instructor) {
                foreach ($sections as $section) {
                    foreach ($section->fields as $field) {
                        $response->answers()->create([
                            'survey_template_field_id' => $field->id,
                            'instructor_id'            => $instructor->id,
                            'value'                    => $data['instructors'][$instructor->id][$field->id] ?? null,
                        ]);
                    }
                }
            }
        });

        // PRG pattern: redirect para evitar doble envío.
        return redirect()->route('execution-survey.public.thanks', $token);
    }

    public function thanks(string $token)
    {
        $executionSurvey = ExecutionSurvey::where('token', $token)
            ->with('execution.course')
            ->firstOrFail();

        return view('execution_survey.thanks', [
            'execution' => $executionSurvey->execution,
        ]);
    }

    private function rulesForField($field): array
    {
        $rules = [$field->required ? 'required' : 'nullable', 'string'];

        if (in_array($field->type, ['radio', 'select'], true) && ! empty($field->options)) {
            $rules[] = 'in:' . implode(',', $field->options);
        } else {
            $rules[] = 'max:5000';
        }

        return $rules;
    }
}
