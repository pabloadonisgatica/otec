<?php

namespace App\Services\Questionnaires;

use App\Models\Questionnaire;
use App\Models\QuestionnaireAnswer;
use App\Models\QuestionnaireQuestion;
use App\Models\QuestionnaireResponse;
use App\Services\Questionnaires\Instruments\Instrument;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Resultados de una encuesta libre: resumen por pregunta y exportación.
 * Las respuestas de preguntas eliminadas se muestran al final con su texto original.
 */
class QuestionnaireResultsService
{
    private const TEXT_SAMPLE = 20;

    public function responsesQuery(Questionnaire $questionnaire, ?Carbon $from, ?Carbon $to): Builder
    {
        return QuestionnaireResponse::query()
            ->where('questionnaire_id', $questionnaire->id)
            ->when($from, fn ($q) => $q->where('created_at', '>=', $from))
            ->when($to, fn ($q) => $q->where('created_at', '<=', $to));
    }

    /**
     * @return array{total: int, questions: array<int, array>}
     */
    public function summary(Questionnaire $questionnaire, ?Carbon $from, ?Carbon $to): array
    {
        if ($instrument = $questionnaire->instrument()) {
            $responses = $this->responsesQuery($questionnaire, $from, $to)->with('answers')->get();

            return [
                'total' => $responses->count(),
                'questions' => [],
                'instrument' => $instrument->summary($responses),
            ];
        }

        $responseIds = $this->responsesQuery($questionnaire, $from, $to)->pluck('id');
        $total = $responseIds->count();

        $answers = QuestionnaireAnswer::query()
            ->whereIn('questionnaire_response_id', $responseIds)
            ->with('response:id,created_at')
            ->get();

        $byQuestion = $answers->groupBy(fn ($answer) => $answer->questionnaire_question_id ?? 'deleted:' . $answer->question_type . ':' . $answer->question_label);

        $questions = [];
        $number = 0;

        foreach ($questionnaire->orderedQuestions() as $question) {
            $questions[] = $this->questionSummary(
                ++$number . '. ' . $question->label,
                $question->type,
                $question->options ?? [],
                $question->settings ?? [],
                $byQuestion->get($question->id, collect()),
                $total
            ) + ['deleted' => false];
        }

        // Preguntas eliminadas que tienen respuestas en el período.
        foreach ($byQuestion as $key => $group) {
            if (is_string($key) && str_starts_with($key, 'deleted:')) {
                $first = $group->first();
                $questions[] = $this->questionSummary($first->question_label, $first->question_type, [], [], $group, $total) + ['deleted' => true];
            }
        }

        return ['total' => $total, 'questions' => $questions];
    }

    /**
     * Filas para el CSV: una por respuesta, una columna por pregunta.
     *
     * @return array{headers: string[], rows: \Generator}
     */
    public function export(Questionnaire $questionnaire, ?Carbon $from, ?Carbon $to): array
    {
        $respondentFields = array_intersect_key(Questionnaire::RESPONDENT_FIELDS, $questionnaire->respondentFields());

        if ($instrument = $questionnaire->instrument()) {
            return $this->exportInstrument($questionnaire, $instrument, $respondentFields, $from, $to);
        }
        $questions = $questionnaire->orderedQuestions();

        // Columnas extra para preguntas eliminadas que tienen respuestas.
        $deleted = QuestionnaireAnswer::query()
            ->whereNull('questionnaire_question_id')
            ->whereIn('questionnaire_response_id', $this->responsesQuery($questionnaire, $from, $to)->select('id'))
            ->distinct()
            ->pluck('question_label');

        $headers = array_merge(
            ['N°', 'Fecha'],
            array_values($respondentFields),
            $questions->values()->map(fn ($q, $i) => ($i + 1) . '. ' . $q->label)->all(),
            $deleted->map(fn ($label) => $label . ' (eliminada)')->all()
        );

        $rows = (function () use ($questionnaire, $from, $to, $respondentFields, $questions, $deleted) {
            $query = $this->responsesQuery($questionnaire, $from, $to)->with('answers');

            foreach ($query->lazyById(200) as $response) {
                $byQuestion = $response->answers->keyBy('questionnaire_question_id');
                $byDeletedLabel = $response->answers->whereNull('questionnaire_question_id')->keyBy('question_label');

                yield array_merge(
                    [$response->id, $response->submittedAtLocal()],
                    array_map(fn ($field) => $response->{$field}, array_keys($respondentFields)),
                    $questions->map(fn ($q) => $this->csvValue($byQuestion->get($q->id)))->all(),
                    $deleted->map(fn ($label) => $this->csvValue($byDeletedLabel->get($label)))->all()
                );
            }
        })();

        return ['headers' => $headers, 'rows' => $rows];
    }

    private function exportInstrument(Questionnaire $questionnaire, Instrument $instrument, array $respondentFields, ?Carbon $from, ?Carbon $to): array
    {
        $headers = array_merge(['N°', 'Fecha'], array_values($respondentFields), $instrument->exportHeaders(), ['Correo enviado']);

        $rows = (function () use ($questionnaire, $instrument, $respondentFields, $from, $to) {
            foreach ($this->responsesQuery($questionnaire, $from, $to)->with('answers')->lazyById(200) as $response) {
                yield array_merge(
                    [$response->id, $response->submittedAtLocal()],
                    array_map(fn ($field) => $response->{$field}, array_keys($respondentFields)),
                    $instrument->exportRow($response),
                    [$response->result_emailed_at ? 'Sí' : 'No']
                );
            }
        })();

        return ['headers' => $headers, 'rows' => $rows];
    }

    private function questionSummary(string $title, string $type, array $options, array $settings, Collection $answers, int $total): array
    {
        $answered = $answers->count();

        $summary = [
            'title' => $title,
            'type' => $type,
            'type_label' => QuestionnaireQuestion::TYPES[$type] ?? $type,
            'answered' => $answered,
            'total' => $total,
        ];

        return array_merge($summary, match (true) {
            in_array($type, ['single_choice', 'dropdown', 'yes_no'], true) => $this->choiceSummary(
                $type === 'yes_no' ? ['Sí', 'No'] : $options,
                $answers->pluck('value'),
                $answered
            ),
            $type === 'multiple_choice' => $this->choiceSummary($options, $answers->pluck('choices')->flatten(), $answered) + ['multiple' => true],
            $type === 'nps' => $this->npsSummary($settings, $answers->pluck('numeric_value'), $answered),
            isset(QuestionnaireQuestion::SCALE_RANGES[$type]) => $this->scaleSummary($type, $settings, $answers->pluck('numeric_value'), $answered),
            default => $this->textSummary($answers),
        });
    }

    /** Conteo por opción. Incluye valores que ya no están entre las opciones (si se editaron). */
    private function choiceSummary(array $options, Collection $values, int $answered): array
    {
        $counts = $values->filter(fn ($v) => $v !== null && $v !== '')->countBy();

        $labels = collect($options)->merge($counts->keys())->unique()->values();

        return [
            'kind' => 'choice',
            'rows' => $labels->map(fn ($label) => [
                'label' => $label,
                'count' => $counts->get($label, 0),
                'percent' => $answered ? round($counts->get($label, 0) * 100 / $answered, 1) : 0,
            ])->all(),
        ];
    }

    private function scaleSummary(string $type, array $settings, Collection $values, int $answered): array
    {
        [$min, $max] = QuestionnaireQuestion::SCALE_RANGES[$type];
        $values = $values->filter(fn ($v) => $v !== null);
        $counts = $values->countBy();

        return [
            'kind' => 'scale',
            'average' => $values->isNotEmpty() ? round($values->avg(), 2) : null,
            'min' => $min,
            'max' => $max,
            'min_label' => $settings['min_label'] ?? null,
            'max_label' => $settings['max_label'] ?? null,
            'rows' => collect(range($min, $max))->map(fn ($value) => [
                'label' => (string) $value,
                'count' => $counts->get($value, 0),
                'percent' => $answered ? round($counts->get($value, 0) * 100 / $answered, 1) : 0,
            ])->all(),
        ];
    }

    /** NPS = % promotores (9-10) − % detractores (0-6). */
    private function npsSummary(array $settings, Collection $values, int $answered): array
    {
        $values = $values->filter(fn ($v) => $v !== null);
        $promoters = $values->filter(fn ($v) => $v >= 9)->count();
        $passives = $values->filter(fn ($v) => $v >= 7 && $v <= 8)->count();
        $detractors = $values->filter(fn ($v) => $v <= 6)->count();
        $percent = fn ($count) => $answered ? round($count * 100 / $answered, 1) : 0;

        return array_merge($this->scaleSummary('nps', $settings, $values, $answered), [
            'kind' => 'nps',
            'nps' => $answered ? (int) round($percent($promoters) - $percent($detractors)) : null,
            'groups' => [
                ['label' => 'Promotores (9-10)', 'count' => $promoters, 'percent' => $percent($promoters), 'tone' => 'good'],
                ['label' => 'Pasivos (7-8)', 'count' => $passives, 'percent' => $percent($passives), 'tone' => 'neutral'],
                ['label' => 'Detractores (0-6)', 'count' => $detractors, 'percent' => $percent($detractors), 'tone' => 'bad'],
            ],
        ]);
    }

    private function textSummary(Collection $answers): array
    {
        return [
            'kind' => 'text',
            'samples' => $answers
                ->sortByDesc(fn ($answer) => $answer->response?->created_at)
                ->take(self::TEXT_SAMPLE)
                ->map(fn ($answer) => [
                    'value' => $answer->question_type === 'date' && $answer->value
                        ? Carbon::parse($answer->value)->format('d-m-Y')
                        : $answer->value,
                    'date' => $answer->response?->submittedAtLocal(),
                ])
                ->values()
                ->all(),
        ];
    }

    private function csvValue(?QuestionnaireAnswer $answer): string
    {
        if (! $answer) {
            return '';
        }

        return $answer->choices ? implode('; ', $answer->choices) : (string) $answer->value;
    }
}
