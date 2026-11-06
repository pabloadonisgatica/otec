<?php

namespace App\Services\Questionnaires\Instruments;

use App\Models\QuestionnaireResponse;
use Illuminate\Support\Collection;

/**
 * Instrumento cerrado (preguntas y cálculo fijos, definidos en el código).
 * Cada instrumento sabe validar, calcular, resumir y exportar sus respuestas.
 */
interface Instrument
{
    /** Clave del tipo (coincide con QuestionnaireType). Nombra sus vistas. */
    public function key(): string;

    public function title(): string;

    /**
     * true: pide correo, envía el resultado y lo muestra al terminar.
     * false: solo agradece (por ejemplo, la encuesta de satisfacción anónima).
     */
    public function emailsResult(): bool;

    /** Reglas de validación de las respuestas (campo "answers"). */
    public function rules(): array;

    /** Nombres legibles para los mensajes de error. */
    public function attributes(): array;

    /**
     * Respuestas validadas → filas a guardar.
     *
     * @return array<int, array{label: string, type: string, value: string, numeric: ?int}>
     */
    public function answerRows(array $answers): array;

    /** Respuestas validadas → resultado calculado (se guarda en la respuesta). */
    public function calculate(array $answers): array;

    /** Resultado en una línea, para listados. */
    public function shortResult(?array $result): string;

    /** Resumen agregado para el panel de resultados. */
    public function summary(Collection $responses): array;

    /** Columnas extra del CSV (además de N°, fecha y datos de quien responde). */
    public function exportHeaders(): array;

    public function exportRow(QuestionnaireResponse $response): array;
}
