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

    /**
     * Solo dígitos y K mayúscula ("12345678K"). Sirve para comparar RUT
     * guardados en formatos distintos.
     */
    public static function clean(?string $rut): string
    {
        return strtoupper(preg_replace('/[^0-9kK]/', '', (string) $rut));
    }

    /**
     * Valida el dígito verificador (módulo 11).
     */
    public static function isValid(?string $rut): bool
    {
        $clean = self::clean($rut);

        if (strlen($clean) < 2) {
            return false;
        }

        $body = substr($clean, 0, -1);
        $dv = substr($clean, -1);

        if (! ctype_digit($body) || strlen($body) > 8) {
            return false;
        }

        $sum = 0;
        $factor = 2;

        for ($i = strlen($body) - 1; $i >= 0; $i--) {
            $sum += (int) $body[$i] * $factor;
            $factor = $factor === 7 ? 2 : $factor + 1;
        }

        $expected = match ($remainder = 11 - ($sum % 11)) {
            11 => '0',
            10 => 'K',
            default => (string) $remainder,
        };

        return $dv === $expected;
    }
}
