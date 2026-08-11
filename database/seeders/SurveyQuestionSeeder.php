<?php

namespace Database\Seeders;

use App\Models\SurveyQuestion;
use Illuminate\Database\Seeder;

class SurveyQuestionSeeder extends Seeder
{
    public function run(): void
    {
        $questions = [
            // Área A - Al Instructor
            ['area' => 'A. Al Instructor', 'text' => 'Demostraron un adecuado nivel de conocimientos'],
            ['area' => 'A. Al Instructor', 'text' => 'Escucharon y respondieron preguntas con agrado'],
            ['area' => 'A. Al Instructor', 'text' => 'La metodología utilizada fue adecuada'],

            // Área B - Relacionada al Programa
            ['area' => 'B. Relacionada al Programa', 'text' => 'La actividad me entregó el conocimiento apropiado y sus objetivos fueron alcanzados'],
            ['area' => 'B. Relacionada al Programa', 'text' => 'El material audiovisual y actividades prácticas estuvieron de acuerdo a los temas planteados'],
            ['area' => 'B. Relacionada al Programa', 'text' => 'Los participantes nos sentimos motivados a participar activamente'],
            ['area' => 'B. Relacionada al Programa', 'text' => 'El salón y mobiliario eran adecuados y cómodos para el desarrollo de la actividad'],
            ['area' => 'B. Relacionada al Programa', 'text' => 'Los equipos usados como apoyo eran apropiados y funcionaron adecuadamente'],
            ['area' => 'B. Relacionada al Programa', 'text' => 'Cada sesión se realizó con puntualidad en su inicio y en su término.'],
            ['area' => 'B. Relacionada al Programa', 'text' => 'El material, la información y las instrucciones fueron entregadas oportunamente.'],
            ['area' => 'B. Relacionada al Programa', 'text' => '¿Cómo calificaría usted la actividad de capacitación?'],
        ];

        foreach ($questions as $index => $question) {

            SurveyQuestion::updateOrCreate(
                [
                    'area' => $question['area'],
                    'text' => $question['text'],
                ],
                [
                    'sort_order' => $index + 1,
                ]
            );
        }
    }
}
