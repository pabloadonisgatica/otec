<?php

namespace App\Http\Controllers;

use App\Enums\QuestionnaireType;
use App\Http\Requests\QuestionnaireRequest;
use App\Models\Company;
use App\Models\Course;
use App\Models\Execution;
use App\Models\Questionnaire;
use App\Services\Questionnaires\QuestionnaireService;
use Illuminate\Http\Request;

/**
 * Módulo Encuestas: datos generales. El constructor de preguntas, la página
 * pública y el panel de resultados tienen sus propios controladores.
 */
class QuestionnaireController extends Controller
{
    public function __construct(private QuestionnaireService $questionnaires)
    {
    }

    public function index(Request $request)
    {
        $search = trim((string) $request->query('q')) ?: null;
        $status = $request->query('status') ?: null;
        $type = $request->query('type') ?: null;

        $questionnaires = Questionnaire::query()
            ->with(['company:id,name', 'course:id,name', 'execution:id,internal_code,course_name'])
            ->listFilters($search, $status, $type)
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('questionnaires.index', [
            'questionnaires' => $questionnaires,
            'search' => $search,
            'status' => $status,
            'type' => $type,
            'types' => QuestionnaireType::cases(),
            'statuses' => Questionnaire::STATUSES,
            'breadcrumbs' => [['label' => 'Encuestas']],
        ]);
    }

    public function create()
    {
        return view('questionnaires.create', $this->formData(
            new Questionnaire(['is_active' => true, 'type' => QuestionnaireType::Custom]),
            'Nueva encuesta'
        ));
    }

    public function store(QuestionnaireRequest $request)
    {
        $questionnaire = $this->questionnaires->save(
            new Questionnaire(),
            $request->validated(),
            $request->file('banner')
        );

        return redirect()
            ->route('questionnaires.edit', $questionnaire)
            ->with('success', 'Encuesta creada.');
    }

    public function edit(Questionnaire $questionnaire)
    {
        return view('questionnaires.edit', $this->formData($questionnaire, 'Editar encuesta'));
    }

    public function update(QuestionnaireRequest $request, Questionnaire $questionnaire)
    {
        $this->questionnaires->save(
            $questionnaire,
            $request->validated(),
            $request->file('banner'),
            $request->boolean('remove_banner')
        );

        return redirect()
            ->route('questionnaires.edit', $questionnaire)
            ->with('success', 'Encuesta actualizada.');
    }

    public function destroy(Questionnaire $questionnaire)
    {
        $this->questionnaires->delete($questionnaire);

        return redirect()
            ->route('questionnaires.index')
            ->with('success', 'Encuesta eliminada.');
    }

    private function formData(Questionnaire $questionnaire, string $title): array
    {
        return [
            'questionnaire' => $questionnaire,
            'title' => $title,
            'types' => QuestionnaireType::cases(),
            'respondentFields' => Questionnaire::RESPONDENT_FIELDS,
            'companies' => Company::orderBy('name')->get(['id', 'name']),
            'courses' => Course::orderBy('name')->get(['id', 'name']),
            'executions' => Execution::latest('start_date')->get(['id', 'internal_code', 'course_name', 'start_date']),
            'breadcrumbs' => [
                ['label' => 'Encuestas', 'url' => route('questionnaires.index')],
                ['label' => $title],
            ],
        ];
    }
}
