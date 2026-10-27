<?php

namespace App\Http\Controllers;

use App\Http\Requests\QuestionnaireQuestionsRequest;
use App\Models\Questionnaire;
use App\Models\QuestionnaireQuestion;
use App\Services\Questionnaires\QuestionnaireBuilder;

/**
 * Constructor de secciones y preguntas (solo encuestas libres).
 */
class QuestionnaireBuilderController extends Controller
{
    public function __construct(private QuestionnaireBuilder $builder)
    {
    }

    public function edit(Questionnaire $questionnaire)
    {
        abort_unless($questionnaire->hasBuilder(), 404);

        return view('questionnaires.builder', [
            'questionnaire' => $questionnaire,
            'sections' => old('sections', $this->builder->toForm($questionnaire)),
            'types' => QuestionnaireQuestion::TYPES,
            'choiceTypes' => QuestionnaireQuestion::CHOICE_TYPES,
            'scaleRanges' => QuestionnaireQuestion::SCALE_RANGES,
            'breadcrumbs' => [
                ['label' => 'Encuestas', 'url' => route('questionnaires.index')],
                ['label' => $questionnaire->name],
            ],
        ]);
    }

    public function update(QuestionnaireQuestionsRequest $request, Questionnaire $questionnaire)
    {
        abort_unless($questionnaire->hasBuilder(), 404);

        $this->builder->sync($questionnaire, $request->sections());

        return redirect()
            ->route('questionnaires.builder.edit', $questionnaire)
            ->with('success', 'Preguntas guardadas.');
    }
}
