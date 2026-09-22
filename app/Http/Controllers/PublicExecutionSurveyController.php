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

        $generalSections = $executionSurvey->template->sections
            ->where('repeats_per_instructor', false)
            ->values();

        $repeatingSections = $executionSurvey->template->sections
            ->where('repeats_per_instructor', true)
            ->values();

        $instructors = $executionSurvey->execution->instructors;

        return view('execution_survey.show', [
            'executionSurvey' => $executionSurvey,
            'execution' => $executionSurvey->execution,
            'generalSections' => $generalSections,
            'repeatingSections' => $repeatingSections,
            'instructors' => $instructors,
        ]);
    }

    public function store(Request $request, string $token)
    {
        $executionSurvey = ExecutionSurvey::where('token', $token)
            ->with(['template.sections.fields', 'execution.instructors'])
            ->firstOrFail();

        $generalSections = $executionSurvey->template->sections
            ->where('repeats_per_instructor', false)
            ->values();

        $repeatingSections = $executionSurvey->template->sections
            ->where('repeats_per_instructor', true)
            ->values();

        $instructors = $executionSurvey->execution->instructors;

        // Construimos las reglas de validación dinámicamente
        // según los campos reales de la plantilla.
        $rules = [];

        foreach ($generalSections as $section) {
            foreach ($section->fields as $field) {
                $rules["general.{$field->id}"] = $this->rulesForField($field);
            }
        }

        foreach ($repeatingSections as $section) {
            foreach ($instructors as $instructor) {
                foreach ($section->fields as $field) {
                    $rules["instructors.{$instructor->id}.{$field->id}"] = $this->rulesForField($field);
                }
            }
        }

        $data = $request->validate($rules);

        DB::transaction(function () use ($data, $executionSurvey, $generalSections, $repeatingSections, $instructors) {
            $response = ExecutionSurveyResponse::create([
                'execution_survey_id' => $executionSurvey->id,
                'submitted_at' => now(),
            ]);

            foreach ($generalSections as $section) {
                foreach ($section->fields as $field) {
                    $response->answers()->create([
                        'survey_template_field_id' => $field->id,
                        'instructor_id' => null,
                        'value' => $data['general'][$field->id] ?? null,
                    ]);
                }
            }

            foreach ($repeatingSections as $section) {
                foreach ($instructors as $instructor) {
                    foreach ($section->fields as $field) {
                        $response->answers()->create([
                            'survey_template_field_id' => $field->id,
                            'instructor_id' => $instructor->id,
                            'value' => $data['instructors'][$instructor->id][$field->id] ?? null,
                        ]);
                    }
                }
            }
        });

        return view('execution_survey.thanks', [
            'execution' => $executionSurvey->execution,
        ]);
    }

    private function rulesForField($field): array
    {
        $rules = [$field->required ? 'required' : 'nullable'];

        if (in_array($field->type, ['radio', 'select'], true)) {
            $rules[] = 'string';
            if (! empty($field->options)) {
                $rules[] = 'in:' . implode(',', $field->options);
            }
        } else {
            $rules[] = 'string';
            $rules[] = 'max:5000';
        }

        return $rules;
    }
}
