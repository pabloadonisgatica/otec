<?php

namespace App\Http\Controllers;

use App\Models\SurveyTemplate;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SurveyTemplateController extends Controller
{
    public function index()
    {
        $templates = SurveyTemplate::withCount('sections')
            ->latest()
            ->paginate(15);

        $breadcrumbs = [
            ['label' => 'Templates Encuestas'],
        ];

        return view('survey_templates.index', compact('templates', 'breadcrumbs'));
    }

    public function create()
    {
        $template = new SurveyTemplate(['active' => true]);
        $template->setRelation('sections', collect());

        $breadcrumbs = [
            ['label' => 'Templates Encuestas', 'url' => route('survey-templates.index')],
            ['label' => 'Nueva plantilla'],
        ];

        return view('survey_templates.create', compact('template', 'breadcrumbs'));
    }

    public function store(Request $request)
    {
        $data = $this->validateTemplate($request);

        DB::transaction(function () use ($data) {
            $template = SurveyTemplate::create([
                'name' => $data['name'],
                'active' => $data['active'] ?? false,
            ]);

            $this->syncSections($template, $data['sections'] ?? []);
        });

        return redirect()
            ->route('survey-templates.index')
            ->with('status', 'Plantilla creada correctamente.');
    }

    public function edit(SurveyTemplate $survey_template)
    {
        $survey_template->load('sections.fields');

        $breadcrumbs = [
            ['label' => 'Templates Encuestas', 'url' => route('survey-templates.index')],
            ['label' => 'Editar plantilla'],
        ];

        return view('survey_templates.edit', [
            'template' => $survey_template,
            'breadcrumbs' => $breadcrumbs,
        ]);
    }

    public function update(Request $request, SurveyTemplate $survey_template)
    {
        $data = $this->validateTemplate($request);
        $warnings = [];

        DB::transaction(function () use ($data, $survey_template, &$warnings) {
            $survey_template->update([
                'name' => $data['name'],
                'active' => $data['active'] ?? false,
            ]);

            // Actualiza en el lugar lo que ya existe (por id), crea lo nuevo,
            // y solo intenta borrar lo que el usuario quitó del formulario.
            // Si algo no se puede borrar porque ya tiene respuestas
            // registradas, se protege esa respuesta y se avisa (no se
            // rompe la página).
            $warnings = $this->syncSections($survey_template, $data['sections'] ?? []);
        });

        $status = 'Plantilla actualizada correctamente.';

        if (! empty($warnings)) {
            $status .= ' Atención: ' . implode(' ', $warnings);
        }

        return redirect()
            ->route('survey-templates.index')
            ->with('status', $status);
    }

    public function destroy(SurveyTemplate $survey_template)
    {
        try {
            $survey_template->delete();
        } catch (QueryException $e) {
            if ($this->isForeignKeyViolation($e)) {
                return back()->with(
                    'status',
                    'No se pudo eliminar: esta plantilla ya tiene encuestas o respuestas asociadas.'
                );
            }

            throw $e;
        }

        return redirect()
            ->route('survey-templates.index')
            ->with('status', 'Plantilla eliminada correctamente.');
    }

    private function validateTemplate(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'active' => ['nullable', 'boolean'],

            'sections' => ['required', 'array', 'min:1'],
            'sections.*.id' => ['nullable', 'integer'],
            'sections.*.title' => ['required', 'string', 'max:255'],
            'sections.*.description' => ['nullable', 'string', 'max:2000'],
            'sections.*.repeats_per_instructor' => ['nullable', 'boolean'],

            'sections.*.fields' => ['required', 'array', 'min:1'],
            'sections.*.fields.*.id' => ['nullable', 'integer'],
            'sections.*.fields.*.label' => ['required', 'string', 'max:255'],
            'sections.*.fields.*.type' => ['required', 'string', 'in:input,radio,select,textarea'],
            'sections.*.fields.*.options_text' => ['nullable', 'string'],
            'sections.*.fields.*.required' => ['nullable', 'boolean'],
        ]);
    }

    /**
     * Crea/actualiza secciones y campos por id (sin perder respuestas
     * ya guardadas), y borra solo lo que el usuario realmente quitó
     * del formulario — protegiendo lo que ya tiene respuestas.
     *
     * @return string[] Avisos de cosas que no se pudieron borrar.
     */
    private function syncSections(SurveyTemplate $template, array $sections): array
    {
        $warnings = [];
        $keptSectionIds = [];

        foreach ($sections as $sectionIndex => $sectionData) {
            $sectionId = $sectionData['id'] ?? null;
            $section = $sectionId ? $template->sections()->find($sectionId) : null;

            $attrs = [
                'title' => $sectionData['title'],
                'description' => $sectionData['description'] ?? null,
                'repeats_per_instructor' => (bool) ($sectionData['repeats_per_instructor'] ?? false),
                'sort_order' => $sectionIndex,
            ];

            if ($section) {
                $section->update($attrs);
            } else {
                $section = $template->sections()->create($attrs);
            }

            $keptSectionIds[] = $section->id;
            $keptFieldIds = [];

            foreach (($sectionData['fields'] ?? []) as $fieldIndex => $fieldData) {
                $fieldId = $fieldData['id'] ?? null;
                $field = $fieldId ? $section->fields()->find($fieldId) : null;

                $fieldAttrs = [
                    'label' => $fieldData['label'],
                    'type' => $fieldData['type'],
                    'options' => $this->parseOptions($fieldData),
                    'required' => (bool) ($fieldData['required'] ?? false),
                    'sort_order' => $fieldIndex,
                ];

                if ($field) {
                    $field->update($fieldAttrs);
                } else {
                    $field = $section->fields()->create($fieldAttrs);
                }

                $keptFieldIds[] = $field->id;
            }

            foreach ($section->fields()->whereNotIn('id', $keptFieldIds)->get() as $obsoleteField) {
                try {
                    $obsoleteField->delete();
                } catch (QueryException $e) {
                    if (! $this->isForeignKeyViolation($e)) {
                        throw $e;
                    }

                    $warnings[] = "No se pudo quitar la pregunta \"{$obsoleteField->label}\" porque ya tiene respuestas registradas.";
                }
            }
        }

        foreach ($template->sections()->whereNotIn('id', $keptSectionIds)->get() as $obsoleteSection) {
            try {
                $obsoleteSection->delete();
            } catch (QueryException $e) {
                if (! $this->isForeignKeyViolation($e)) {
                    throw $e;
                }

                $warnings[] = "No se pudo quitar la sección \"{$obsoleteSection->title}\" porque tiene preguntas con respuestas registradas.";
            }
        }

        return $warnings;
    }

    private function parseOptions(array $fieldData): ?array
    {
        if (! in_array($fieldData['type'], ['radio', 'select'], true)) {
            return null;
        }

        $optionsText = trim((string) ($fieldData['options_text'] ?? ''));

        if ($optionsText === '') {
            return null;
        }

        return array_values(array_filter(array_map(
            fn ($opt) => trim($opt),
            explode(',', $optionsText)
        ), fn ($opt) => $opt !== ''));
    }

    private function isForeignKeyViolation(QueryException $e): bool
    {
        return $e->getCode() === '23000' || str_contains($e->getMessage(), 'foreign key constraint');
    }
}
