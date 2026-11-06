<?php

namespace App\Support;

use Illuminate\Support\Str;

/**
 * Texto y tono de color de cada estado que se muestra como chip.
 * Un solo lugar para que los estados se vean igual en toda la plataforma.
 *
 * Tonos (los colores viven en el componente <x-ui.badge>):
 *   success   verde    terminado, aprobado, activo
 *   info      azul     en curso, informativo
 *   new       morado   recién creado, planificado, borrador
 *   warning   ámbar    pendiente de una acción
 *   attention naranja  inactivo, pausado
 *   critical  rojo     rechazado, cancelado, con error
 *
 * Un estado que no esté definido igual se muestra con color (info), nunca gris.
 */
class StatusBadge
{
    public const FALLBACK_TONE = 'info';

    /** @var array<string, array<string, array{0: string, 1: string}>> dominio => estado => [texto, tono] */
    public const DOMAINS = [
        'execution' => [
            'planificada' => ['Planificada', 'new'],
            'en_ejecucion' => ['En ejecución', 'info'],
            'finalizada' => ['Finalizada', 'success'],
            'cancelada' => ['Cancelada', 'critical'],
        ],
        'budget' => [
            'draft' => ['Borrador', 'new'],
            'pending' => ['Pendiente', 'warning'],
            'approved' => ['Aceptado', 'success'],
            'rejected' => ['Rechazado', 'critical'],
        ],
        'participant' => [
            'activo' => ['Activo', 'success'],
            'inactivo' => ['Inactivo', 'attention'],
        ],
        'questionnaire' => [
            'active' => ['Activa', 'success'],
            'inactive' => ['Inactiva', 'attention'],
            'closed' => ['Cerrada', 'info'],
        ],
        // Columnas "Activa: Sí / No" (plantillas).
        'active' => [
            'yes' => ['Sí', 'success'],
            'no' => ['No', 'attention'],
        ],
    ];

    /**
     * @return array{label: string, tone: string}
     */
    public static function for(string $domain, mixed $status): array
    {
        $key = is_bool($status) ? ($status ? 'yes' : 'no') : (string) $status;
        $definition = self::DOMAINS[$domain][$key] ?? null;

        return [
            'label' => $definition[0] ?? (string) Str::of($key)->replace('_', ' ')->ucfirst(),
            'tone' => $definition[1] ?? self::FALLBACK_TONE,
        ];
    }
}
