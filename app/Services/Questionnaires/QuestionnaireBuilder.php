<?php

namespace App\Services\Questionnaires;

use App\Models\Questionnaire;
use App\Models\QuestionnaireQuestion;
use App\Models\QuestionnaireSection;
use Illuminate\Support\Facades\DB;

/**
 * Sincroniza secciones y preguntas del constructor.
 * Lo que trae id se actualiza en el lugar (así las respuestas futuras siguen
 * apuntando a la misma pregunta), lo nuevo se crea y lo quitado se elimina.
 * Un id que no pertenece a esta encuesta se trata como nuevo.
 */
class QuestionnaireBuilder
{
    public function sync(Questionnaire $questionnaire, array $sections): void
    {
        DB::transaction(function () use ($questionnaire, $sections) {
            $existingSections = $questionnaire->sections()->get()->keyBy('id');
            $existingQuestions = $questionnaire->questions()->get()->keyBy('id');

            $keptSections = [];
            $keptQuestions = [];

            foreach (array_values($sections) as $sectionOrder => $sectionData) {
                $section = $existingSections->get($sectionData['id'] ?? null) ?? new QuestionnaireSection(['questionnaire_id' => $questionnaire->id]);

                $section->fill([
                    'title' => $this->clean($sectionData['title'] ?? null),
                    'description' => $this->clean($sectionData['description'] ?? null),
                    'sort_order' => $sectionOrder,
                ])->save();

                $keptSections[] = $section->id;

                foreach (array_values($sectionData['questions'] ?? []) as $questionOrder => $questionData) {
                    $question = $existingQuestions->get($questionData['id'] ?? null) ?? new QuestionnaireQuestion(['questionnaire_id' => $questionnaire->id]);

                    $question->fill($this->questionAttributes($questionData) + [
                        'questionnaire_section_id' => $section->id,
                        'sort_order' => $questionOrder,
                    ])->save();

                    $keptQuestions[] = $question->id;
                }
            }

            $questionnaire->questions()->whereNotIn('id', $keptQuestions)->delete();
            $questionnaire->sections()->whereNotIn('id', $keptSections)->delete();
        });
    }

    /** Estado inicial del constructor (mismo formato que envía el formulario). */
    public function toForm(Questionnaire $questionnaire): array
    {
        return $questionnaire->sections()->with('questions')->get()
            ->map(fn (QuestionnaireSection $section) => [
                'id' => $section->id,
                'title' => $section->title ?? '',
                'description' => $section->description ?? '',
                'questions' => $section->questions->map(fn (QuestionnaireQuestion $question) => [
                    'id' => $question->id,
                    'type' => $question->type,
                    'label' => $question->label,
                    'help' => $question->help ?? '',
                    'required' => $question->is_required,
                    'options_text' => implode("\n", $question->options ?? []),
                    'min_label' => $question->settings['min_label'] ?? '',
                    'max_label' => $question->settings['max_label'] ?? '',
                ])->all(),
            ])
            ->all();
    }

    /** Copia secciones y preguntas de una encuesta a otra. */
    public function copy(Questionnaire $from, Questionnaire $to): void
    {
        $sections = collect($this->toForm($from))
            ->map(function (array $section) {
                $section['id'] = null;
                $section['questions'] = array_map(fn ($question) => ['id' => null] + $question, $section['questions']);

                return $section;
            })
            ->all();

        $this->sync($to, $sections);
    }

    private function questionAttributes(array $data): array
    {
        $type = $data['type'];

        $settings = isset(QuestionnaireQuestion::SCALE_RANGES[$type])
            ? array_filter([
                'min_label' => $this->clean($data['min_label'] ?? null),
                'max_label' => $this->clean($data['max_label'] ?? null),
            ])
            : [];

        return [
            'type' => $type,
            'label' => trim($data['label']),
            'help' => $this->clean($data['help'] ?? null),
            'is_required' => (bool) ($data['required'] ?? false),
            'options' => in_array($type, QuestionnaireQuestion::CHOICE_TYPES, true)
                ? QuestionnaireQuestion::parseOptions($data['options_text'] ?? '')
                : null,
            'settings' => $settings ?: null,
        ];
    }

    private function clean(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
