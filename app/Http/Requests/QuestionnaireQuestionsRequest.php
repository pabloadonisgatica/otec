<?php

namespace App\Http\Requests;

use App\Models\QuestionnaireQuestion;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * Guarda todas las secciones y preguntas del constructor de una sola vez.
 */
class QuestionnaireQuestionsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sections' => ['nullable', 'array', 'max:30'],
            'sections.*.id' => ['nullable', 'integer'],
            'sections.*.title' => ['nullable', 'string', 'max:255'],
            'sections.*.description' => ['nullable', 'string', 'max:2000'],
            'sections.*.questions' => ['nullable', 'array', 'max:100'],
            'sections.*.questions.*.id' => ['nullable', 'integer'],
            'sections.*.questions.*.type' => ['required', Rule::in(array_keys(QuestionnaireQuestion::TYPES))],
            'sections.*.questions.*.label' => ['required', 'string', 'max:500'],
            'sections.*.questions.*.help' => ['nullable', 'string', 'max:1000'],
            'sections.*.questions.*.required' => ['nullable', 'boolean'],
            'sections.*.questions.*.options_text' => ['nullable', 'string', 'max:10000'],
            'sections.*.questions.*.min_label' => ['nullable', 'string', 'max:100'],
            'sections.*.questions.*.max_label' => ['nullable', 'string', 'max:100'],
        ];
    }

    /** Las preguntas de selección necesitan al menos 2 opciones distintas. */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                $number = 0;

                foreach ((array) $this->input('sections', []) as $section) {
                    foreach ((array) ($section['questions'] ?? []) as $question) {
                        $number++;

                        if (! in_array($question['type'] ?? null, QuestionnaireQuestion::CHOICE_TYPES, true)) {
                            continue;
                        }

                        $options = QuestionnaireQuestion::parseOptions($question['options_text'] ?? '');

                        if (count($options) < 2) {
                            $validator->errors()->add('sections', "Pregunta {$number}: necesita al menos 2 opciones (una por línea).");
                        } elseif (count($options) > QuestionnaireQuestion::MAX_OPTIONS) {
                            $validator->errors()->add('sections', "Pregunta {$number}: máximo " . QuestionnaireQuestion::MAX_OPTIONS . ' opciones.');
                        } elseif (collect($options)->contains(fn ($option) => mb_strlen($option) > 200)) {
                            $validator->errors()->add('sections', "Pregunta {$number}: cada opción puede tener hasta 200 caracteres.");
                        }
                    }
                }
            },
        ];
    }

    public function messages(): array
    {
        return [
            'sections.*.questions.*.label.required' => 'Todas las preguntas necesitan un enunciado.',
            'sections.*.questions.*.label.max' => 'El enunciado de una pregunta supera los 500 caracteres.',
            'sections.max' => 'Máximo 30 secciones.',
            'sections.*.questions.max' => 'Máximo 100 preguntas por sección.',
        ];
    }

    public function sections(): array
    {
        return (array) $this->validated('sections', []);
    }
}
