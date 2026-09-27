<?php

namespace App\Support;

/**
 * Formato de RUT chileno: 19.056.881-4
 * Acepta cualquier forma en que venga guardado (con o sin puntos/guion).
 */
class Rut
{
    public static function format(?string $rut): ?string
    {
        if ($rut === null) {
            return null;
        }

        $clean = strtoupper(preg_replace('/[^0-9kK]/', '', $rut));

        if (strlen($clean) < 2) {
            return trim($rut) ?: null;
        }

        $body = substr($clean, 0, -1);
        $dv = substr($clean, -1);

        if (! ctype_digit($body)) {
            return trim($rut);
        }

        return number_format((int) $body, 0, '', '.') . '-' . $dv;
    }
}
