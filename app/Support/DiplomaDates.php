<?php

namespace App\Support;

use Carbon\CarbonInterface;

/**
 * Fechas en español para diplomas: "30 de abril de 2026".
 * Los meses se escriben aquí (y no con el locale del servidor)
 * para que el resultado sea idéntico en local y en producción.
 */
class DiplomaDates
{
    private const MONTHS = [
        1 => 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
        'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre',
    ];

    /** "30 de abril de 2026" */
    public static function long(CarbonInterface $date): string
    {
        return $date->day . ' de ' . self::MONTHS[$date->month] . ' de ' . $date->year;
    }

    /**
     * Frase de realización:
     *  - mismo día:        "el 30 de abril de 2026"
     *  - mismo mes y año:  "entre el 23 y el 30 de julio de 2026"
     *  - mismo año:        "entre el 28 de julio y el 2 de agosto de 2026"
     *  - distinto año:     "entre el 29 de diciembre de 2025 y el 3 de enero de 2026"
     */
    public static function phrase(CarbonInterface $start, CarbonInterface $end): string
    {
        if ($start->isSameDay($end)) {
            return 'el ' . self::long($start);
        }

        if ($start->year !== $end->year) {
            return 'entre el ' . self::long($start) . ' y el ' . self::long($end);
        }

        if ($start->month !== $end->month) {
            return 'entre el ' . $start->day . ' de ' . self::MONTHS[$start->month]
                . ' y el ' . self::long($end);
        }

        return 'entre el ' . $start->day . ' y el ' . self::long($end);
    }
}
