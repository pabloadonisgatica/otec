<?php

namespace App\Services\ParticipantImport;

use Illuminate\Support\Str;

/**
 * Campos que se pueden importar y sugerencia automática de columnas.
 * Varias columnas pueden ir al mismo campo (ej: apellido paterno y materno,
 * o RUT y DV en columnas separadas): se unen en el orden del archivo.
 */
class ParticipantImportFields
{
    public const FIELDS = [
        'rut' => 'RUT',
        'first_name' => 'Nombres',
        'last_name' => 'Apellidos',
        'email' => 'Correo',
        'phone' => 'Teléfono',
    ];

    public const REQUIRED = ['rut', 'first_name', 'last_name'];

    /** Palabras que delatan cada campo en el encabezado (ya normalizado). El orden importa. */
    private const PATTERNS = [
        'email' => ['mail', 'correo'],
        'phone' => ['fono', 'telefono', 'celular', 'movil', 'phone', 'whatsapp'],
        'rut' => ['rut', 'run', 'dni', 'cedula', 'documento', 'digito'],
        'last_name' => ['apellido', 'lastname', 'surname', 'paterno', 'materno'],
        'first_name' => ['nombre', 'firstname', 'name'],
    ];

    /** Encabezados que no son del participante aunque contengan "nombre" o "rut". */
    private const EXCLUDE = ['empresa', 'razonsocial', 'curso', 'completo', 'relator', 'cargo'];

    /** Campos que solo admiten una columna en la sugerencia automática. */
    private const SINGLE = ['email', 'phone'];

    /**
     * @param  string[]  $headers
     * @return string[] índice de columna => campo ('' = no importar)
     */
    public static function suggest(array $headers): array
    {
        $mapping = [];
        $taken = [];

        foreach ($headers as $index => $header) {
            $mapping[$index] = '';
            $normalized = self::normalize($header);

            if ($normalized === '' || Str::contains($normalized, self::EXCLUDE)) {
                continue;
            }

            foreach (self::PATTERNS as $field => $words) {
                $matches = $normalized === 'dv' ? $field === 'rut' : Str::contains($normalized, $words);

                if (! $matches || (in_array($field, self::SINGLE, true) && isset($taken[$field]))) {
                    continue;
                }

                $mapping[$index] = $field;
                $taken[$field] = true;
                break;
            }
        }

        return $mapping;
    }

    private static function normalize(string $header): string
    {
        return (string) Str::of($header)->ascii()->lower()->replaceMatches('/[^a-z0-9]/', '');
    }
}
