<?php

namespace App\Services\Questionnaires\Instruments;

use App\Models\QuestionnaireResponse;
use Illuminate\Support\Collection;

/**
 * Escala de Evaluación de Grupo (Proyecto Humano).
 * 10 ítems en escala 1 a 8. Ítems 1-5 = Madurez para la Tarea (MT),
 * ítems 6-10 = Madurez Psicológica (MP). Cada dimensión suma de 5 a 40:
 * 5-15 Bajo, 16-30 Moderado, 31-40 Alto.
 *
 * Contenido y cálculo trasladados sin cambios desde la encuesta original.
 */
class GroupEvaluationInstrument implements Instrument
{
    public const MIN = 1;
    public const MAX = 8;

    public const ITEMS = [
        1 => ['title' => 'Experiencia en el trabajo',
            'low' => 'Tienen poca experiencia relacionada con su trabajo',
            'high' => 'Tienen vasta experiencia relacionada con su trabajo'],
        2 => ['title' => 'Conocimiento del trabajo',
            'low' => 'Tienen escasos conocimientos necesarios para su trabajo',
            'high' => 'Tienen amplio conocimiento necesario para su trabajo actual'],
        3 => ['title' => 'Solución de problemas',
            'low' => 'No pueden resolver todos los problemas relacionados con su trabajo',
            'high' => 'Pueden resolver todos los problemas relacionados con su trabajo'],
        4 => ['title' => 'Responsabilidad',
            'low' => 'Necesitan una supervisión estrecha',
            'high' => 'Pueden trabajar solos'],
        5 => ['title' => 'Cumplimiento de fechas de término',
            'low' => 'Se atrasan respecto a las fechas de término',
            'high' => 'Siempre logran terminar el trabajo dentro de los plazos fijados'],
        6 => ['title' => 'Motivación al logro',
            'low' => 'Tienen una menor orientación hacia el logro de excelencia',
            'high' => 'Tienen una fuerte orientación hacia el logro de excelencia'],
        7 => ['title' => 'Persistencia',
            'low' => 'Se distraen con facilidad de sus tareas',
            'high' => 'No abandonan las tareas hasta que están terminadas'],
        8 => ['title' => 'Actitud hacia el trabajo',
            'low' => 'Considera el trabajo como una actividad necesaria',
            'high' => 'Considera el trabajo como una actividad agradable'],
        9 => ['title' => 'Independencia',
            'low' => 'Están más interesados en otros trabajos',
            'high' => 'Están muy deseosos de trabajar en lo suyo'],
        10 => ['title' => 'Disposición hacia la responsabilidad',
            'low' => 'Son más bien reticentes',
            'high' => 'Son muy luchadores y empeñosos'],
    ];

    public const DIMENSIONS = [
        'mt' => [
            'label' => 'Madurez para la Tarea',
            'items' => [1, 2, 3, 4, 5],
            'detail' => 'Experiencia, conocimiento, solución de problemas, responsabilidad y cumplimiento de plazos (ítems 1 a 5).',
        ],
        'mp' => [
            'label' => 'Madurez Psicológica',
            'items' => [6, 7, 8, 9, 10],
            'detail' => 'Motivación al logro, persistencia, actitud, independencia y disposición hacia la responsabilidad (ítems 6 a 10).',
        ],
    ];

    /** Nivel según el puntaje de una dimensión (5 a 40). */
    public const LEVELS = [
        'low' => ['label' => 'Bajo', 'max' => 15, 'color' => '#dc3545'],
        'moderate' => ['label' => 'Moderado', 'max' => 30, 'color' => '#e0a800'],
        'high' => ['label' => 'Alto', 'max' => 40, 'color' => '#198754'],
    ];

    public const SCORE_MIN = 5;
    public const SCORE_MAX = 40;

    public function key(): string
    {
        return 'group_evaluation';
    }

    public function title(): string
    {
        return 'Escala de Evaluación de Grupo';
    }

    public function emailsResult(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [];

        foreach (array_keys(self::ITEMS) as $number) {
            $rules["answers.{$number}"] = ['required', 'integer', 'between:' . self::MIN . ',' . self::MAX];
        }

        return $rules;
    }

    public function attributes(): array
    {
        $attributes = [];

        foreach (self::ITEMS as $number => $item) {
            $attributes["answers.{$number}"] = "el ítem {$number} ({$item['title']})";
        }

        return $attributes;
    }

    public function answerRows(array $answers): array
    {
        $rows = [];

        foreach (self::ITEMS as $number => $item) {
            $rows[] = [
                'label' => "{$number}. {$item['title']}",
                'type' => 'scale_8',
                'value' => (string) (int) $answers[$number],
                'numeric' => (int) $answers[$number],
            ];
        }

        return $rows;
    }

    public function calculate(array $answers): array
    {
        $dimensions = [];

        foreach (self::DIMENSIONS as $key => $dimension) {
            $score = array_sum(array_map(fn ($number) => (int) $answers[$number], $dimension['items']));
            $level = self::level($score);

            $dimensions[$key] = [
                'label' => $dimension['label'],
                'score' => $score,
                'level' => $level,
                'level_label' => self::LEVELS[$level]['label'],
            ];
        }

        return ['instrument' => $this->key(), 'dimensions' => $dimensions];
    }

    public function shortResult(?array $result): string
    {
        $dimensions = $result['dimensions'] ?? [];

        return collect($dimensions)
            ->map(fn ($d, $key) => strtoupper($key) . " {$d['score']} ({$d['level_label']})")
            ->implode(' · ');
    }

    public function summary(Collection $responses): array
    {
        $results = $responses->pluck('result')->filter();
        $count = $results->count();

        $dimensions = [];

        foreach (self::DIMENSIONS as $key => $dimension) {
            $scores = $results->pluck("dimensions.{$key}.score")->filter(fn ($v) => $v !== null);
            $levels = $results->pluck("dimensions.{$key}.level")->countBy();

            $dimensions[$key] = $dimension + [
                'average' => $scores->isNotEmpty() ? round($scores->avg(), 1) : null,
                'average_level' => $scores->isNotEmpty() ? self::LEVELS[self::level((int) round($scores->avg()))]['label'] : null,
                'distribution' => collect(self::LEVELS)->map(fn ($level, $levelKey) => [
                    'label' => $level['label'],
                    'color' => $level['color'],
                    'count' => $levels->get($levelKey, 0),
                    'percent' => $count ? round($levels->get($levelKey, 0) * 100 / $count, 1) : 0,
                ])->values()->all(),
            ];
        }

        // Promedio por ítem (1 a 8), a partir de las respuestas guardadas.
        $answers = $responses->flatMap->answers->groupBy('question_label');
        $items = [];

        foreach (self::ITEMS as $number => $item) {
            $values = $answers->get("{$number}. {$item['title']}", collect())->pluck('numeric_value')->filter(fn ($v) => $v !== null);

            $items[] = [
                'number' => $number,
                'title' => $item['title'],
                'dimension' => $number <= 5 ? 'mt' : 'mp',
                'average' => $values->isNotEmpty() ? round($values->avg(), 1) : null,
            ];
        }

        return ['count' => $count, 'dimensions' => $dimensions, 'items' => $items];
    }

    public function exportHeaders(): array
    {
        return array_merge(
            array_map(fn ($number, $item) => "{$number}. {$item['title']}", array_keys(self::ITEMS), self::ITEMS),
            ['Madurez para la Tarea', 'Nivel MT', 'Madurez Psicológica', 'Nivel MP']
        );
    }

    public function exportRow(QuestionnaireResponse $response): array
    {
        $byLabel = $response->answers->keyBy('question_label');
        $dimensions = $response->result['dimensions'] ?? [];

        return array_merge(
            array_map(fn ($number, $item) => $byLabel->get("{$number}. {$item['title']}")?->numeric_value, array_keys(self::ITEMS), self::ITEMS),
            [
                $dimensions['mt']['score'] ?? null,
                $dimensions['mt']['level_label'] ?? null,
                $dimensions['mp']['score'] ?? null,
                $dimensions['mp']['level_label'] ?? null,
            ]
        );
    }

    public static function level(int $score): string
    {
        foreach (self::LEVELS as $key => $level) {
            if ($score <= $level['max']) {
                return $key;
            }
        }

        return 'high';
    }

    /** Posición (0-100%) del marcador en la barra de bandas. */
    public static function markerPosition(int $score): float
    {
        return round(max(0, min(100, ($score - self::SCORE_MIN) / (self::SCORE_MAX - self::SCORE_MIN) * 100)), 1);
    }
}
