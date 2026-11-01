<?php

namespace App\Services\Questionnaires\Instruments;

use App\Models\QuestionnaireResponse;
use Illuminate\Support\Collection;

/**
 * Cuestionario Habilidades Gerenciales y Liderazgo (Proyecto Humano).
 * 12 situaciones con 4 alternativas (A-D). Cada alternativa corresponde a un
 * estilo (Dirigir, Entrenar, Apoyar, Delegar) y suma puntos de flexibilidad.
 * Resultado: % de cada estilo (enteros que suman 100) y flexibilidad para liderar.
 *
 * Contenido y cálculo trasladados sin cambios desde el cuestionario original.
 */
class LeadershipInstrument implements Instrument
{
    /** El orden importa: es el desempate del redondeo de porcentajes (igual que el original). */
    public const STYLES = [
        'dirigir' => ['label' => 'Dirigir', 'color' => '#B35751'],
        'entrenar' => ['label' => 'Entrenar', 'color' => '#A2B966'],
        'apoyar' => ['label' => 'Apoyar', 'color' => '#64AAC3'],
        'delegar' => ['label' => 'Delegar', 'color' => '#7B669E'],
    ];

    /** Flexibilidad para liderar según la suma de puntos (de -24 a +24). */
    public const FLEXIBILITY = [
        ['max' => -17, 'label' => 'Inflexible'],
        ['max' => -9, 'label' => 'Difícilmente flexible'],
        ['max' => -1, 'label' => 'Ocasionalmente flexible'],
        ['max' => 8, 'label' => 'Moderadamente flexible'],
        ['max' => 16, 'label' => 'Flexible'],
        ['max' => PHP_INT_MAX, 'label' => 'Muy flexible'],
    ];

    public const QUESTIONS = [
        1 => [
            'situation' => 'Últimamente sus colaboradores no responden a su trato amistoso y a su obvia preocupación por el bienestar de ellos. El rendimiento desciende rápidamente.',
            'options' => [
                'A' => ['text' => 'Daría importancia al uso de procedimientos uniformes y a la necesidad de cumplir con la tarea.', 'style' => 'dirigir', 'points' => 2],
                'B' => ['text' => 'Estaría disponible para discutir el asunto, pero sin presionar.', 'style' => 'apoyar', 'points' => -1],
                'C' => ['text' => 'Hablaría con los colaboradores para luego fijar metas.', 'style' => 'entrenar', 'points' => 1],
                'D' => ['text' => 'No intervendría.', 'style' => 'delegar', 'points' => -2],
            ],
        ],
        2 => [
            'situation' => 'El rendimiento observable del grupo va en aumento. Se ha asegurado que todos los miembros estén conscientes de sus responsabilidades y de los niveles de rendimiento que se espera de ellos.',
            'options' => [
                'A' => ['text' => 'Crearía una relación cordial, pero asegurándome que todos los miembros estén conscientes de sus responsabilidades y niveles de rendimiento.', 'style' => 'entrenar', 'points' => 2],
                'B' => ['text' => 'No realizaría ninguna acción determinada.', 'style' => 'delegar', 'points' => -2],
                'C' => ['text' => 'Haría lo que pudiera para que el grupo se sintiera importante.', 'style' => 'apoyar', 'points' => 1],
                'D' => ['text' => 'Insistiría en la importancia de los plazos límites para los trabajos y tareas.', 'style' => 'dirigir', 'points' => -1],
            ],
        ],
        3 => [
            'situation' => 'Los miembros de su grupo no han podido solucionar un problema por sí solos. El rendimiento del grupo y las relaciones interpersonales han sido buenas en el pasado.',
            'options' => [
                'A' => ['text' => 'Involucraría al grupo y juntos trataríamos de solucionar los problemas.', 'style' => 'entrenar', 'points' => 1],
                'B' => ['text' => 'Dejaría que el grupo lo resuelva solo.', 'style' => 'delegar', 'points' => -1],
                'C' => ['text' => 'Actuaría rápida y firmemente para corregir la situación y dirigir al grupo.', 'style' => 'dirigir', 'points' => -2],
                'D' => ['text' => 'Estimularía al grupo a trabajar en el problema y estaría dispuesto a sostener cualquiera conversación.', 'style' => 'apoyar', 'points' => 2],
            ],
        ],
        4 => [
            'situation' => 'Está considerando un cambio. Sus colaboradores tienen excelentes antecedentes por sus logros. Ellos respetan la necesidad del cambio.',
            'options' => [
                'A' => ['text' => 'Permitiría que el grupo se involucre en el desarrollo del cambio, acompañando el proceso.', 'style' => 'apoyar', 'points' => 1],
                'B' => ['text' => 'Comunicaría los cambios y luego haría que se cumplieran bajo una estricta supervisión.', 'style' => 'dirigir', 'points' => -2],
                'C' => ['text' => 'Permitiría que el grupo formule su propia dirección.', 'style' => 'delegar', 'points' => 2],
                'D' => ['text' => 'Incorporaría a la solución las recomendaciones del grupo, pero dirigiendo personalmente todo.', 'style' => 'entrenar', 'points' => -1],
            ],
        ],
        5 => [
            'situation' => 'El rendimiento del grupo ha estado bajando en los últimos meses. Sus miembros no se preocupan por lograr los objetivos. La redefinición de roles y responsabilidades ha ayudado en el pasado. Siempre ha tenido que recordarles que deben cumplir sus tareas a tiempo.',
            'options' => [
                'A' => ['text' => 'Permitiría que el grupo formule su propia dirección.', 'style' => 'delegar', 'points' => -2],
                'B' => ['text' => 'Incorporaría a la solución las recomendaciones del grupo, pero vigilando que se alcancen los objetivos.', 'style' => 'entrenar', 'points' => 1],
                'C' => ['text' => 'Redefiniría los roles y responsabilidades y supervisaría estrictamente.', 'style' => 'dirigir', 'points' => 2],
                'D' => ['text' => 'Permitiría que el grupo se involucre en la fijación de metas, sin imponer mi punto de vista.', 'style' => 'apoyar', 'points' => -1],
            ],
        ],
        6 => [
            'situation' => 'Se ha incorporado a una organización donde las operaciones son eficientes. Su antecesor controlaba muy de cerca la situación. Usted quiere mantener una situación productiva, pero le gustaría comenzar a humanizar el ambiente.',
            'options' => [
                'A' => ['text' => 'Haría lo que pudiera para que el grupo se sintiera importante e involucrado en los asuntos.', 'style' => 'apoyar', 'points' => -1],
                'B' => ['text' => 'Daría importancia a los plazos límites para trabajos y tareas.', 'style' => 'dirigir', 'points' => 1],
                'C' => ['text' => 'No intervendría intencionalmente.', 'style' => 'delegar', 'points' => -2],
                'D' => ['text' => 'Trataría que el grupo se involucre en la toma de decisiones, pero vigilaría que se alcancen los objetivos.', 'style' => 'entrenar', 'points' => 2],
            ],
        ],
        7 => [
            'situation' => 'Está considerando algunos cambios importantes en la estructura organizativa. Los miembros del grupo han hecho sugerencias sobre la necesidad de cambio. El grupo ha sido productivo y ha demostrado flexibilidad en sus operaciones.',
            'options' => [
                'A' => ['text' => 'Definiría el cambio y lo supervisaría estrictamente.', 'style' => 'dirigir', 'points' => -2],
                'B' => ['text' => 'Participaría con el grupo en el desarrollo del cambio y dejaría que los miembros organizaran su realización.', 'style' => 'apoyar', 'points' => 2],
                'C' => ['text' => 'Me mostraría dispuesto a hacer los cambios recomendados, pero mantendría el control de la realización de los mismos.', 'style' => 'entrenar', 'points' => -1],
                'D' => ['text' => 'Evitaría la confrontación, dejando las cosas como están.', 'style' => 'delegar', 'points' => 1],
            ],
        ],
        8 => [
            'situation' => 'El rendimiento del grupo y sus relaciones interpersonales son buenas. Pero usted se siente algo inseguro por la supuesta falta de dirección del grupo. Tiene la impresión de que lo está dirigiendo poco.',
            'options' => [
                'A' => ['text' => 'Dejaría al grupo solo.', 'style' => 'delegar', 'points' => 2],
                'B' => ['text' => 'Discutiría la situación con el grupo y luego iniciaría yo mismo los cambios necesarios.', 'style' => 'entrenar', 'points' => -1],
                'C' => ['text' => 'Tomaría medidas para dirigir a los colaboradores para que trabajen de una manera determinada.', 'style' => 'dirigir', 'points' => -2],
                'D' => ['text' => 'Mostraría que respaldo al grupo en la discusión de la situación.', 'style' => 'apoyar', 'points' => 1],
            ],
        ],
        9 => [
            'situation' => 'Ha sido nombrado por su superior, jefe de un comité que ha tardado bastante en presentar sus recomendaciones con respecto a la ejecución de ciertos cambios. El grupo además no sabe con claridad cuáles son sus objetivos. La asistencia a las sesiones ha sido escasa. Sus reuniones se han convertido casi en tertulias sociales. Potencialmente tienen el talento necesario para ayudar.',
            'options' => [
                'A' => ['text' => 'Dejaría que el grupo busque solo las soluciones a sus problemas.', 'style' => 'delegar', 'points' => -2],
                'B' => ['text' => 'Incorporaría a la solución las recomendaciones del grupo, para vigilar que se alcancen los objetivos.', 'style' => 'entrenar', 'points' => 1],
                'C' => ['text' => 'Redefiniría los niveles de calidad y supervisaría cuidadosamente.', 'style' => 'dirigir', 'points' => 2],
                'D' => ['text' => 'Permitiría que el grupo intervenga en la determinación de los objetivos.', 'style' => 'apoyar', 'points' => -1],
            ],
        ],
        10 => [
            'situation' => 'Sus colaboradores, normalmente capaces de responsabilizarse, no están respondiendo a su reciente redefinición de niveles de calidad.',
            'options' => [
                'A' => ['text' => 'Permitiría que el grupo intervenga en la redefinición de los niveles de calidad, aportando puntos de vista.', 'style' => 'apoyar', 'points' => 1],
                'B' => ['text' => 'Redefiniría los niveles de calidad y supervisaría cuidadosamente.', 'style' => 'dirigir', 'points' => -2],
                'C' => ['text' => 'Evitaría la confrontación no aplicando presión y sin intervenir.', 'style' => 'delegar', 'points' => -1],
                'D' => ['text' => 'Incorporaría a la solución las recomendaciones del grupo, vigilando que se alcancen los niveles de calidad.', 'style' => 'entrenar', 'points' => 2],
            ],
        ],
        11 => [
            'situation' => 'Información reciente indica que existen algunas dificultades internas entre sus colaboradores. El grupo tiene antecedentes notables por sus logros. Los miembros han logrado efectivamente objetivos de largo alcance. Han trabajado en armonía durante el año anterior. Todos están bien capacitados para la tarea.',
            'options' => [
                'A' => ['text' => 'Tomaría medidas para conseguir que los colaboradores trabajen de una manera determinada.', 'style' => 'dirigir', 'points' => -2],
                'B' => ['text' => 'Haría que los colaboradores se vieran involucrados en la toma de decisiones y reforzaría las buenas contribuciones.', 'style' => 'apoyar', 'points' => 2],
                'C' => ['text' => 'Discutiría el rendimiento previo con el grupo y luego examinaría la necesidad de prácticas nuevas.', 'style' => 'entrenar', 'points' => -1],
                'D' => ['text' => 'Continuaría dejando libre al grupo.', 'style' => 'delegar', 'points' => 1],
            ],
        ],
        12 => [
            'situation' => 'Ha sido ascendido a un nuevo puesto. El jefe anterior no interfería en los asuntos del grupo. El grupo ha manejado bien sus tareas y la dirección. Las interrelaciones del grupo son buenas.',
            'options' => [
                'A' => ['text' => 'Probaría con los colaboradores la solución propuesta por mí mismo y examinaría la necesidad de nuevas prácticas.', 'style' => 'entrenar', 'points' => -1],
                'B' => ['text' => 'Permitiría que los propios miembros del grupo encuentren las soluciones.', 'style' => 'delegar', 'points' => 2],
                'C' => ['text' => 'Actuaría rápida y firmemente para corregir y dirigir.', 'style' => 'dirigir', 'points' => -2],
                'D' => ['text' => 'Participaría en la discusión del problema proporcionando apoyo a los colaboradores.', 'style' => 'apoyar', 'points' => 1],
            ],
        ],
    ];

    public function key(): string
    {
        return 'leadership';
    }

    public function title(): string
    {
        return 'Habilidades Gerenciales y Liderazgo';
    }

    public function rules(): array
    {
        $rules = [];

        foreach (self::QUESTIONS as $number => $question) {
            $rules["answers.{$number}"] = ['required', 'in:' . implode(',', array_keys($question['options']))];
        }

        return $rules;
    }

    public function attributes(): array
    {
        $attributes = [];

        foreach (array_keys(self::QUESTIONS) as $number) {
            $attributes["answers.{$number}"] = "la pregunta {$number}";
        }

        return $attributes;
    }

    public function answerRows(array $answers): array
    {
        $rows = [];

        foreach (self::QUESTIONS as $number => $question) {
            $letter = $answers[$number];
            $option = $question['options'][$letter];

            $rows[] = [
                'label' => "Pregunta {$number}",
                'type' => 'leadership',
                'value' => "{$letter}. {$option['text']} (" . self::STYLES[$option['style']]['label'] . ')',
                'numeric' => $option['points'],
            ];
        }

        return $rows;
    }

    public function calculate(array $answers): array
    {
        $counts = array_fill_keys(array_keys(self::STYLES), 0);
        $points = 0;

        foreach (self::QUESTIONS as $number => $question) {
            $option = $question['options'][$answers[$number]];
            $counts[$option['style']]++;
            $points += $option['points'];
        }

        $percentages = self::percentages($counts);

        $styles = [];
        foreach (self::STYLES as $key => $style) {
            $styles[$key] = ['label' => $style['label'], 'count' => $counts[$key], 'percent' => $percentages[$key]];
        }

        return [
            'instrument' => $this->key(),
            'styles' => $styles,
            'points' => $points,
            'flexibility' => self::flexibility($points),
        ];
    }

    public function shortResult(?array $result): string
    {
        if (! $result) {
            return '';
        }

        $styles = collect($result['styles'] ?? [])->map(fn ($s) => mb_substr($s['label'], 0, 3) . ' ' . $s['percent'] . '%')->implode(' · ');

        return $styles . ' · ' . ($result['flexibility'] ?? '');
    }

    public function summary(Collection $responses): array
    {
        $results = $responses->pluck('result')->filter();
        $count = $results->count();

        $styles = [];
        foreach (self::STYLES as $key => $style) {
            $values = $results->pluck("styles.{$key}.percent")->filter(fn ($v) => $v !== null);
            $styles[$key] = $style + ['average' => $values->isNotEmpty() ? round($values->avg(), 1) : null];
        }

        $flexibility = $results->pluck('flexibility')->countBy();
        $points = $results->pluck('points')->filter(fn ($v) => $v !== null);

        // Estilo elegido en cada pregunta: muestra en qué situaciones el grupo tiende a cada estilo.
        $answers = $responses->flatMap->answers->groupBy('question_label');
        $questions = [];

        foreach (self::QUESTIONS as $number => $question) {
            $chosen = $answers->get("Pregunta {$number}", collect())->map(function ($answer) use ($question) {
                $letter = mb_substr((string) $answer->value, 0, 1);

                return $question['options'][$letter]['style'] ?? null;
            })->filter()->countBy();

            $total = $chosen->sum();

            $questions[] = [
                'number' => $number,
                'situation' => $question['situation'],
                'total' => $total,
                'styles' => collect(self::STYLES)->map(fn ($style, $key) => [
                    'label' => $style['label'],
                    'color' => $style['color'],
                    'count' => $chosen->get($key, 0),
                    'percent' => $total ? round($chosen->get($key, 0) * 100 / $total, 1) : 0,
                ])->values()->all(),
            ];
        }

        return [
            'count' => $count,
            'styles' => $styles,
            'average_points' => $points->isNotEmpty() ? round($points->avg(), 1) : null,
            'average_flexibility' => $points->isNotEmpty() ? self::flexibility((int) round($points->avg())) : null,
            'flexibility' => collect(self::FLEXIBILITY)->map(fn ($level) => [
                'label' => $level['label'],
                'count' => $flexibility->get($level['label'], 0),
                'percent' => $count ? round($flexibility->get($level['label'], 0) * 100 / $count, 1) : 0,
            ])->all(),
            'questions' => $questions,
        ];
    }

    public function exportHeaders(): array
    {
        return array_merge(
            array_map(fn ($number) => "Pregunta {$number}", array_keys(self::QUESTIONS)),
            array_map(fn ($style) => $style['label'] . ' %', array_values(self::STYLES)),
            ['Puntaje flexibilidad', 'Flexibilidad para liderar']
        );
    }

    public function exportRow(QuestionnaireResponse $response): array
    {
        $byLabel = $response->answers->keyBy('question_label');
        $result = $response->result ?? [];

        return array_merge(
            array_map(fn ($number) => $byLabel->get("Pregunta {$number}")?->value, array_keys(self::QUESTIONS)),
            array_map(fn ($key) => $result['styles'][$key]['percent'] ?? null, array_keys(self::STYLES)),
            [$result['points'] ?? null, $result['flexibility'] ?? null]
        );
    }

    /** Porcentajes enteros que siempre suman 100 (método del mayor resto, igual que el original). */
    public static function percentages(array $counts): array
    {
        $total = array_sum($counts);
        $percentages = [];
        $remainders = [];

        foreach (array_keys(self::STYLES) as $style) {
            $exact = $total ? $counts[$style] * 100 / $total : 0;
            $percentages[$style] = (int) floor($exact);
            $remainders[$style] = $exact - floor($exact);
        }

        $missing = $total ? 100 - array_sum($percentages) : 0;
        $order = array_keys(self::STYLES);
        usort($order, fn ($a, $b) => $remainders[$b] <=> $remainders[$a]);

        for ($k = 0; $k < $missing; $k++) {
            $percentages[$order[$k % count($order)]]++;
        }

        return $percentages;
    }

    public static function flexibility(int $points): string
    {
        foreach (self::FLEXIBILITY as $level) {
            if ($points <= $level['max']) {
                return $level['label'];
            }
        }

        return 'Muy flexible';
    }
}
