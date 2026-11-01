<?php

namespace App\Http\Requests;

use App\Models\Questionnaire;
use App\Models\QuestionnaireQuestion;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;

/**
 * Respuesta pública a una encuesta libre. Las reglas se arman según
 * las preguntas y los datos que pide cada encuesta.
 */
class SubmitQuestionnaireRequest extends FormRequest
{
    private ?Collection $questions = null;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = ['website' => ['nullable']]; // campo trampa anti-bots

        foreach ($this->questionnaire()->respondentFields() as $field => $mode) {
            $rules["respondent.{$field}"] = array_filter([
                $mode === 'required' ? 'required' : 'nullable',
                'string',
                'max:150',
                $field === 'email' ? 'email' : null,
            ]);
        }

        if ($instrument = $this->questionnaire()->instrument()) {
            return $rules + $instrument->rules();
        }

        foreach ($this->questions() as $question) {
            $key = "answers.{$question->id}";
            $presence = $question->is_required ? 'required' : 'nullable';

            [$min, $max] = QuestionnaireQuestion::SCALE_RANGES[$question->type] ?? [null, null];

            $rules[$key] = match ($question->type) {
                'short_text' => [$presence, 'string', 'max:500'],
                'long_text' => [$presence, 'string', 'max:5000'],
                'single_choice', 'dropdown' => [$presence, 'string', Rule::in($question->options ?? [])],
                'multiple_choice' => array_filter([$presence, 'array', $question->is_required ? 'min:1' : null]),
                'yes_no' => [$presence, Rule::in(['Sí', 'No'])],
                'scale_5', 'scale_10', 'nps' => [$presence, 'integer', "between:{$min},{$max}"],
                'date' => [$presence, 'date_format:Y-m-d'],
                default => [$presence],
            };

            if ($question->type === 'multiple_choice') {
                $rules["{$key}.*"] = ['string', Rule::in($question->options ?? [])];
            }
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'answers.*.required' => 'Responde :attribute.',
            'answers.*.min' => 'Elige al menos una opción en :attribute.',
            'answers.*.in' => 'Elige una opción válida en :attribute.',
            'answers.*.*.in' => 'Elige una opción válida.',
            'answers.*.between' => 'Elige un valor válido en :attribute.',
            'answers.*.date_format' => 'Ingresa una fecha válida en :attribute.',
            'answers.*.max' => 'La respuesta de :attribute es demasiado larga.',
            'respondent.*.required' => 'Completa :attribute.',
            'respondent.email.email' => 'Ingresa un correo válido.',
        ];
    }

    public function attributes(): array
    {
        $attributes = [];

        foreach (Questionnaire::RESPONDENT_FIELDS as $field => $label) {
            $attributes["respondent.{$field}"] = mb_strtolower($label);
        }

        if ($instrument = $this->questionnaire()->instrument()) {
            return $attributes + $instrument->attributes();
        }

        foreach ($this->questions()->values() as $index => $question) {
            $attributes["answers.{$question->id}"] = 'la pregunta ' . ($index + 1);
        }

        return $attributes;
    }

    public function isSpam(): bool
    {
        return filled($this->input('website'));
    }

    public function questionnaire(): Questionnaire
    {
        return $this->route('questionnaire');
    }

    public function questions(): Collection
    {
        return $this->questions ??= $this->questionnaire()->orderedQuestions();
    }
}
