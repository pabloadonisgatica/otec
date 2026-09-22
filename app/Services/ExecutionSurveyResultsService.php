<?php

namespace App\Services;

use App\Models\ExecutionSurvey;

class ExecutionSurveyResultsService
{
    /**
     * Resumen de resultados: generales + agrupados por relator.
     */
    public function summary(ExecutionSurvey $executionSurvey): array
    {
        $executionSurvey->loadMissing([
            'template.sections.fields',
            'execution.instructors',
            'responses.answers',
        ]);

        $totalResponses = $executionSurvey->responses->count();

        $generalSections = $executionSurvey->template->sections
            ->where('repeats_per_instructor', false)
            ->values();

        $repeatingSections = $executionSurvey->template->sections
            ->where('repeats_per_instructor', true)
            ->values();

        $allAnswers = $executionSurvey->responses->flatMap->answers;

        $generalResults = $generalSections->map(function ($section) use ($allAnswers) {
            return [
                'section' => $section,
                'fields' => $section->fields->map(function ($field) use ($allAnswers) {
                    return $this->summarizeField($field, $allAnswers->where('survey_template_field_id', $field->id));
                }),
            ];
        });

        $byInstructor = $executionSurvey->execution->instructors->map(function ($instructor) use ($repeatingSections, $allAnswers) {
            $instructorAnswers = $allAnswers->where('instructor_id', $instructor->id);

            return [
                'instructor' => $instructor,
                'sections' => $repeatingSections->map(function ($section) use ($instructorAnswers) {
                    return [
                        'section' => $section,
                        'fields' => $section->fields->map(function ($field) use ($instructorAnswers) {
                            return $this->summarizeField($field, $instructorAnswers->where('survey_template_field_id', $field->id));
                        }),
                    ];
                }),
            ];
        });

        return [
            'total_responses' => $totalResponses,
            'general_results' => $generalResults,
            'by_instructor' => $byInstructor,
        ];
    }

    /**
     * Para radio/select: promedio (si las opciones son numéricas) y conteo por opción.
     * Para input/textarea: lista de respuestas de texto.
     */
    private function summarizeField($field, $answersForField): array
    {
        $values = $answersForField->pluck('value')->filter(fn ($v) => $v !== null && $v !== '');

        if (in_array($field->type, ['radio', 'select'], true)) {
            $numeric = $values->filter(fn ($v) => is_numeric($v));

            $counts = [];
            foreach (($field->options ?? []) as $option) {
                $counts[$option] = $values->filter(fn ($v) => $v === $option)->count();
            }

            return [
                'field' => $field,
                'average' => $numeric->isNotEmpty() ? round($numeric->map(fn ($v) => (float) $v)->avg(), 1) : null,
                'counts' => $counts,
                'answered_count' => $values->count(),
            ];
        }

        return [
            'field' => $field,
            'texts' => $values->values(),
            'answered_count' => $values->count(),
        ];
    }
}
