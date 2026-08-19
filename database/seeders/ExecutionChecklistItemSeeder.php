<?php

namespace Database\Seeders;

use App\Models\ExecutionChecklistItem;
use Illuminate\Database\Seeder;

class ExecutionChecklistItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            // 1. Chequeo de Diseño
            ['1. Chequeo de Diseño', 'General', 'Revisión del requerimiento'],
            ['1. Chequeo de Diseño', 'General', 'Asignar Relator para su diseño'],
            ['1. Chequeo de Diseño', 'General', 'Elaboración de la propuesta'],
            ['1. Chequeo de Diseño', 'General', 'Revisión de propuestas anteriores y de la nueva propuesta'],
            ['1. Chequeo de Diseño', 'General', 'Revisión de la normativa legal'],
            ['1. Chequeo de Diseño', 'General', 'Aprobación de la propuesta (orden de compra)'],
            ['1. Chequeo de Diseño', 'General', 'Inscripción del Curso en SENCE'],
            ['1. Chequeo de Diseño', 'General', 'Resolución SENCE autorizada'],
            ['1. Chequeo de Diseño', 'General', 'Validación del Curso'],

            // 2. Chequeo de Desarrollo
            ['2. Chequeo de Desarrollo', 'Del lugar de capacitación', 'Condiciones de orden y aseo de las instalaciones'],
            ['2. Chequeo de Desarrollo', 'Del lugar de capacitación', 'Condiciones de espacio, iluminación y ventilación adecuadas'],
            ['2. Chequeo de Desarrollo', 'Del lugar de capacitación', 'Se cuenta con acceso expedito para personas minusválidas'],
            ['2. Chequeo de Desarrollo', 'Del lugar de capacitación', 'Estado de los servicios higiénicos de uso colectivo'],
            ['2. Chequeo de Desarrollo', 'Equipos y mobiliario', 'Computador y data show'],
            ['2. Chequeo de Desarrollo', 'Elementos didácticos de apoyo', 'Entrega de: Manuales, carpetas, cuadernos de apuntes, programa del curso'],
            ['2. Chequeo de Desarrollo', 'Elementos didácticos de apoyo', 'Pizarra, borrador de pizarra, plumones, lápices'],

            // 3. Chequeo de Ejecución
            ['3. Chequeo de Ejecución', 'Ejecución del servicio', 'Lectura y entrega de las condiciones de la Empresa'],
            ['3. Chequeo de Ejecución', 'Ejecución del servicio', 'Registro de la asistencia en el Libro de Clases dentro de los primeros 20 min.'],
            ['3. Chequeo de Ejecución', 'Ejecución del servicio', 'Cumplimiento del programa'],
            ['3. Chequeo de Ejecución', 'Ejecución del servicio', 'La clase abarcó la totalidad de los contenidos teóricos-prácticos, previstos para esa jornada'],
            ['3. Chequeo de Ejecución', 'Ejecución del servicio', 'Aplicación de la encuesta de satisfacción'],
            ['3. Chequeo de Ejecución', 'Ejecución del servicio', 'Entrega de certificados de asistencia'],
            ['3. Chequeo de Ejecución', 'Elementos de coffee break', 'Café, té, azúcar, endulzante, agua, bebidas, jugos'],
            ['3. Chequeo de Ejecución', 'Elementos de coffee break', 'Galletas, sandwichs'],
            ['3. Chequeo de Ejecución', 'Elementos de coffee break', 'Platillos, cucharas, tazas, hervidor, servilletas'],
        ];

        foreach ($items as $index => [$section, $subsection, $label]) {
            ExecutionChecklistItem::updateOrCreate(
                ['section' => $section, 'subsection' => $subsection, 'label' => $label],
                ['sort_order' => $index + 1]
            );
        }
    }
}
