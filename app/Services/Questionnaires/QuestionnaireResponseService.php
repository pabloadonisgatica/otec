<?php

namespace App\Services\Questionnaires;

use App\Models\Questionnaire;
use App\Models\QuestionnaireQuestion;
use App\Models\QuestionnaireResponse;
use App\Services\Questionnaires\Instruments\Instrument;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class QuestionnaireResponseService
{
    /**
     * Guarda una respuesta completa. Las preguntas sin responder (opcionales) no se guardan.
     *
     * @param  Collection<int, QuestionnaireQuestion>  $questions
     */
    public function store(Questionnaire $questionnaire, Collection $questions, array $respondent, array $answers): QuestionnaireResponse
    {
        return DB::transaction(function () use ($questionnaire, $questions, $respondent, $answers) {
            $response = $questionnaire->responses()->create($this->respondentData($questionnaire, $respondent));

            foreach ($questions as $question) {
                $value = $answers[$question->id] ?? null;

                if ($value === null || $value === '' || $value === []) {
                    continue;
                }

                $response->answers()->create([
                    'questionnaire_question_id' => $question->id,
                    'question_label' => $question->label,
                    'question_type' => $question->type,
                    'value' => is_array($value) ? null : trim((string) $value),
                    // Opciones múltiples en el orden en que aparecen en la pregunta.
                    'choices' => is_array($value) ? array_values(array_intersect($question->options ?? [], $value)) : null,
                    'numeric_value' => $question->isScale() ? (int) $value : null,
                ]);
            }

            return $response;
        });
    }

    /**
     * Guarda la respuesta a un instrumento cerrado con su resultado calculado.
     */
    public function storeInstrument(Questionnaire $questionnaire, Instrument $instrument, array $respondent, array $answers): QuestionnaireResponse
    {
        return DB::transaction(function () use ($questionnaire, $instrument, $respondent, $answers) {
            $response = $questionnaire->responses()->create(
                $this->respondentData($questionnaire, $respondent) + ['result' => $instrument->calculate($answers)]
            );

            foreach ($instrument->answerRows($answers) as $row) {
                $response->answers()->create([
                    'questionnaire_question_id' => null,
                    'question_label' => $row['label'],
                    'question_type' => $row['type'],
                    'value' => $row['value'],
                    'numeric_value' => $row['numeric'],
                ]);
            }

            return $response;
        });
    }

    /**
     * Datos de quien responde. Si la encuesta está asociada a una empresa,
     * la organización se toma de ahí (no se le pregunta a la persona).
     */
    private function respondentData(Questionnaire $questionnaire, array $respondent): array
    {
        $fields = array_keys($questionnaire->respondentFields());
        $data = collect($respondent)->only($fields)->map(fn ($value) => trim((string) $value) ?: null)->all();

        if (in_array('organization', $fields, true) && ($company = $questionnaire->contextCompanyName())) {
            $data['organization'] = mb_substr($company, 0, 150);
        }

        return $data;
    }
}
