<?php

namespace App\Http\Requests;

use App\Models\ProviderEvaluation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProviderEvaluationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'type' => ['required', Rule::in(array_keys(ProviderEvaluation::TYPES))],
            'evaluated_at' => ['required', 'date', 'before_or_equal:today'],
            'evaluator_id' => ['required', 'exists:users,id'],
            'scores' => ['required', 'array'],
            'observations' => ['nullable', 'string'],
            'next_evaluation_at' => ['nullable', 'date', 'after:evaluated_at'],
        ];

        foreach (array_keys(ProviderEvaluation::CRITERIA) as $criterion) {
            $rules["scores.{$criterion}"] = ['required', 'integer', Rule::in(array_keys(ProviderEvaluation::SCALE))];
        }

        return $rules;
    }

    public function attributes(): array
    {
        $attributes = [
            'type' => 'tipo de evaluación',
            'evaluated_at' => 'fecha de evaluación',
            'evaluator_id' => 'evaluador',
            'observations' => 'observaciones',
            'next_evaluation_at' => 'próxima reevaluación',
        ];

        foreach (ProviderEvaluation::CRITERIA as $key => $criterion) {
            $attributes["scores.{$key}"] = mb_strtolower($criterion['label']);
        }

        return $attributes;
    }

    public function messages(): array
    {
        return [
            'scores.*.required' => 'Califica el criterio ":attribute".',
        ];
    }
}
