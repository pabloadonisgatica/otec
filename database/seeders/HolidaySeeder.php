<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Holiday;

class HolidaySeeder extends Seeder
{
    public function run(): void
    {
        $holidays = [

            ['date' => '2026-01-01', 'name' => 'Año Nuevo'],
            ['date' => '2026-04-03', 'name' => 'Viernes Santo'],
            ['date' => '2026-04-04', 'name' => 'Sábado Santo'],
            ['date' => '2026-05-01', 'name' => 'Día del Trabajador'],
            ['date' => '2026-05-21', 'name' => 'Glorias Navales'],
            ['date' => '2026-06-29', 'name' => 'San Pedro y San Pablo'],
            ['date' => '2026-07-16', 'name' => 'Virgen del Carmen'],
            ['date' => '2026-08-15', 'name' => 'Asunción de la Virgen'],
            ['date' => '2026-09-18', 'name' => 'Independencia Nacional'],
            ['date' => '2026-09-19', 'name' => 'Glorias del Ejército'],
            ['date' => '2026-10-12', 'name' => 'Encuentro de Dos Mundos'],
            ['date' => '2026-10-31', 'name' => 'Iglesias Evangélicas'],
            ['date' => '2026-11-01', 'name' => 'Todos los Santos'],
            ['date' => '2026-12-08', 'name' => 'Inmaculada Concepción'],
            ['date' => '2026-12-25', 'name' => 'Navidad'],

        ];

        foreach ($holidays as $holiday) {

            Holiday::updateOrCreate(
                ['date' => $holiday['date']],
                $holiday
            );

        }
    }
}