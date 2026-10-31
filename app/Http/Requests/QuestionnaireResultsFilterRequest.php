<?php

namespace App\Http\Requests;

use App\Models\Questionnaire;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;

/**
 * Filtro por fechas (hora de Chile) del panel de resultados y la exportación.
 */
class QuestionnaireResultsFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:from'],
        ];
    }

    public function attributes(): array
    {
        return ['from' => 'desde', 'to' => 'hasta'];
    }

    /** @return array{0: ?Carbon, 1: ?Carbon} rango en UTC */
    public function range(): array
    {
        return [
            $this->filled('from') ? Carbon::parse($this->input('from'), Questionnaire::TIMEZONE)->startOfDay()->utc() : null,
            $this->filled('to') ? Carbon::parse($this->input('to'), Questionnaire::TIMEZONE)->endOfDay()->utc() : null,
        ];
    }

    /** Filtros para conservar en links y formularios. */
    public function filters(): array
    {
        return array_filter($this->only(['from', 'to']));
    }
}
