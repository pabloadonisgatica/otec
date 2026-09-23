<?php

namespace App\Services;

use App\Models\ExecutionSurvey;

class ExecutionSurveyResultsService
{
    /**
     * Resultados agrupados por relator.
     * Todas las secciones se muestran por cada relator — no hay secciones "generales".
     */
    public function summary(ExecutionSurvey $executionSurvey): array
    {
        $executionSurvey->loadMissing([
            'template.sections.fields',
            'execution.instructors',
            'responses.answers',
        ]);

        $totalResponses = $executionSurvey->responses->count();
        $sections       = $executionSurvey->template->sections;
        $allAnswers     = $executionSurvey->responses->flatMap->answers;

        $byInstructor = $executionSurvey->execution->instructors->map(function ($instructor) use ($sections, $allAnswers) {
            $instructorAnswers = $allAnswers->where('instructor_id', $instructor->id);

            return [
                'instructor' => $instructor,
                'sections'   => $sections->map(function ($section) use ($instructorAnswers) {
                    return [
                        'section' => $section,
                        'fields'  => $section->fields->map(function ($field) use ($instructorAnswers) {
                            return $this->summarizeField(
                                $field,
                                $instructorAnswers->where('survey_template_field_id', $field->id)
                            );
                        }),
                    ];
                }),
            ];
        });

        return [
            'total_responses' => $totalResponses,
            'by_instructor'   => $byInstructor,
        ];
    }

    private function summarizeField($field, $answers): array
    {
        $values = $answers->pluck('value')->filter(fn ($v) => $v !== null && $v !== '');

        if (in_array($field->type, ['radio', 'select'], true)) {
            $numeric = $values->filter(fn ($v) => is_numeric($v));
            $counts  = [];

            foreach (($field->options ?? []) as $option) {
                $counts[$option] = $values->filter(fn ($v) => $v === $option)->count();
            }

            return [
                'field'          => $field,
                'average'        => $numeric->isNotEmpty()
                    ? round($numeric->map(fn ($v) => (float) $v)->avg(), 1)
                    : null,
                'counts'         => $counts,
                'answered_count' => $values->count(),
            ];
        }

        return [
            'field'          => $field,
            'texts'          => $values->values(),
            'answered_count' => $values->count(),
        ];
    }
}
