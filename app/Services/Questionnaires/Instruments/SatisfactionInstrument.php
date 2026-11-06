<?php

namespace App\Services\Questionnaires\Instruments;

use App\Models\QuestionnaireResponse;
use App\Models\SurveyTemplate;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;

/**
 * Encuesta de satisfacción libre: la misma encuesta de satisfacción de las
 * ejecuciones, para un relator cuyo nombre se escribe a mano (no está en
 * ninguna ejecución). Un relator por encuesta.
 *
 * Las preguntas son una copia congelada de la plantilla elegida al crear la
 * encuesta: si después se edita la plantilla en Configuración, las encuestas
 * ya creadas (y sus respuestas) no cambian. Es anónima y no envía resultados.
 */
class SatisfactionInstrument implements Instrument
{
    private const CHOICE_TYPES = ['radio', 'select'];

    private const TEXT_SAMPLE = 10;

    /** @var array<int, array{number: int, label: string, type: string, options: array, required: bool}> */
    private array $fields = [];

    public function __construct(private array $settings)
    {
        foreach ($this->sections() as $section) {
            foreach ($section['fields'] as $field) {
                $this->fields[$field['number']] = $field;
            }
        }
    }

    /**
     * Copia congelada de la plantilla + relator, para guardar en questionnaires.settings.
     * Las preguntas se numeran de 1 a N: el número identifica cada respuesta.
     */
    public static function settingsFor(SurveyTemplate $template, string $instructorName): array
    {
        $template->loadMissing('sections.fields');

        $sections = [];
        $number = 0;

        foreach ($template->sections as $section) {
            $fields = [];

            foreach ($section->fields as $field) {
                $fields[] = [
                    'number' => ++$number,
                    'label' => $field->label,
                    'type' => $field->type,
                    'options' => array_values(array_map('strval', $field->options ?? [])),
                    'required' => (bool) $field->required,
                ];
            }

            if ($fields !== []) {
                $sections[] = [
                    'title' => $section->title,
                    'description' => $section->description,
                    'fields' => $fields,
                ];
            }
        }

        return [
            'instructor_name' => trim($instructorName),
            'template' => [
                'id' => $template->id,
                'name' => $template->name,
                'description' => $template->description,
                'banner_path' => $template->banner_path,
                'sections' => $sections,
            ],
        ];
    }

    public function key(): string
    {
        return 'satisfaction';
    }

    public function title(): string
    {
        return 'Encuesta de satisfacción';
    }

    public function emailsResult(): bool
    {
        return false;
    }

    public function instructorName(): string
    {
        return (string) ($this->settings['instructor_name'] ?? '');
    }

    public function templateName(): ?string
    {
        return $this->settings['template']['name'] ?? null;
    }

    public function templateDescription(): ?string
    {
        return $this->settings['template']['description'] ?? null;
    }

    public function templateBannerUrl(): ?string
    {
        $path = $this->settings['template']['banner_path'] ?? null;

        return $path ? asset('storage/' . $path) : null;
    }

    /** @return array<int, array{title: ?string, description: ?string, fields: array}> */
    public function sections(): array
    {
        return $this->settings['template']['sections'] ?? [];
    }

    public function rules(): array
    {
        $rules = [];

        foreach ($this->fields as $number => $field) {
            $presence = $field['required'] ? 'required' : 'nullable';

            $rules["answers.{$number}"] = $this->isChoice($field) && $field['options'] !== []
                ? [$presence, 'string', Rule::in($field['options'])]
                : [$presence, 'string', 'max:5000'];
        }

        return $rules;
    }

    public function attributes(): array
    {
        $attributes = [];

        foreach ($this->fields as $number => $field) {
            $attributes["answers.{$number}"] = '«' . $field['label'] . '»';
        }

        return $attributes;
    }

    public function answerRows(array $answers): array
    {
        $rows = [];

        foreach ($this->fields as $number => $field) {
            $value = trim((string) ($answers[$number] ?? ''));

            if ($value === '') {
                continue;
            }

            $rows[] = [
                'label' => $this->label($number),
                'type' => $field['type'],
                'value' => $value,
                'numeric' => $this->isChoice($field) && is_numeric($value) ? (int) round((float) $value) : null,
            ];
        }

        return $rows;
    }

    /** Promedio de las respuestas con nota numérica (las de opción). */
    public function calculate(array $answers): array
    {
        $scores = collect($this->answerRows($answers))->pluck('numeric')->filter(fn ($v) => $v !== null);

        return [
            'instrument' => $this->key(),
            'instructor' => $this->instructorName(),
            'average' => $scores->isNotEmpty() ? round($scores->avg(), 2) : null,
            'scale_max' => $this->scaleMax(),
        ];
    }

    public function shortResult(?array $result): string
    {
        if (($result['average'] ?? null) === null) {
            return 'Sin notas';
        }

        $max = $result['scale_max'] ?? null;

        return 'Promedio ' . self::number($result['average']) . ($max ? " de {$max}" : '');
    }

    public function summary(Collection $responses): array
    {
        $byLabel = $responses->flatMap->answers->groupBy('question_label');
        $count = $responses->count();

        $sections = [];

        foreach ($this->sections() as $section) {
            $items = [];

            foreach ($section['fields'] as $field) {
                $answers = $byLabel->get($this->label($field['number']), collect());
                $items[] = $this->fieldSummary($field, $answers, $count, $responses);
            }

            $sections[] = ['title' => $section['title'], 'items' => $items];
        }

        $averages = $responses->pluck('result.average')->filter(fn ($v) => $v !== null);

        return [
            'count' => $count,
            'instructor' => $this->instructorName(),
            'template' => $this->templateName(),
            'average' => $averages->isNotEmpty() ? round($averages->avg(), 2) : null,
            'scale_max' => $this->scaleMax(),
            'sections' => $sections,
        ];
    }

    public function exportHeaders(): array
    {
        return array_merge(
            ['Relator'],
            array_map(fn ($number) => $this->label($number), array_keys($this->fields)),
            ['Promedio']
        );
    }

    public function exportRow(QuestionnaireResponse $response): array
    {
        $byLabel = $response->answers->keyBy('question_label');
        $average = $response->result['average'] ?? null;

        return array_merge(
            [$response->result['instructor'] ?? $this->instructorName()],
            array_map(fn ($number) => $byLabel->get($this->label($number))?->value, array_keys($this->fields)),
            [$average !== null ? self::number($average) : null]
        );
    }

    /** Número con coma decimal: 6.25 → "6,3". */
    public static function number(float|int|null $value, int $decimals = 1): string
    {
        return number_format((float) $value, $decimals, ',', '.');
    }

    private function label(int $number): string
    {
        return $number . '. ' . $this->fields[$number]['label'];
    }

    private function isChoice(array $field): bool
    {
        return in_array($field['type'], self::CHOICE_TYPES, true);
    }

    /** Nota máxima de las preguntas de opción numérica (por ejemplo 7), o null. */
    private function scaleMax(): ?int
    {
        $values = collect($this->fields)
            ->filter(fn ($field) => $this->isChoice($field))
            ->flatMap(fn ($field) => $field['options'])
            ->filter(fn ($option) => is_numeric($option))
            ->map(fn ($option) => (int) round((float) $option));

        return $values->isNotEmpty() ? $values->max() : null;
    }

    private function fieldSummary(array $field, Collection $answers, int $total, Collection $responses): array
    {
        $item = [
            'number' => $field['number'],
            'label' => $field['label'],
            'answered' => $answers->count(),
            'total' => $total,
        ];

        if ($this->isChoice($field)) {
            $counts = $answers->pluck('value')->countBy();
            $answered = $answers->count();
            $labels = collect($field['options'])->merge($counts->keys())->unique()->values();
            $numeric = $answers->pluck('numeric_value')->filter(fn ($v) => $v !== null);

            return $item + [
                'kind' => 'choice',
                'average' => $numeric->isNotEmpty() ? round($numeric->avg(), 2) : null,
                'rows' => $labels->map(fn ($label) => [
                    'label' => $label,
                    'count' => $counts->get($label, 0),
                    'percent' => $answered ? round($counts->get($label, 0) * 100 / $answered, 1) : 0,
                ])->all(),
            ];
        }

        $dates = $responses->keyBy('id');

        return $item + [
            'kind' => 'text',
            'samples' => $answers
                ->sortByDesc('questionnaire_response_id')
                ->take(self::TEXT_SAMPLE)
                ->map(fn ($answer) => [
                    'value' => $answer->value,
                    'date' => $dates->get($answer->questionnaire_response_id)?->submittedAtLocal(),
                ])
                ->values()
                ->all(),
        ];
    }
}
