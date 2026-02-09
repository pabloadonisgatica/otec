<?php

namespace App\Support;

class DiplomaVariables
{
    public static function definitions(): array
    {
        return [
            'participant.full_name' => 'Nombre completo del participante',
            'participant.rut'       => 'RUT del participante',

            'course.name'           => 'Nombre del curso',
            'course.hours'          => 'Horas del curso',
            'course.start_date'     => 'Fecha inicio curso',
            'course.end_date'       => 'Fecha término curso',

            'issued_at'             => 'Fecha de emisión del diploma',
            'code'                  => 'Código único del diploma',
        ];
    }

    public static function placeholders(): array
    {
        return collect(self::definitions())
            ->keys()
            ->map(fn ($v) => '{{'.$v.'}}')
            ->toArray();
    }
}
