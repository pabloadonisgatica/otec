<?php

namespace App\Enums;

/**
 * Tipos de encuesta. "custom" se arma con el constructor; las otras son
 * instrumentos cerrados: sus preguntas y su cálculo viven en el código
 * y no se pueden editar desde la interfaz.
 */
enum QuestionnaireType: string
{
    case Custom = 'custom';
    case GroupEvaluation = 'group_evaluation';
    case Leadership = 'leadership';

    public function label(): string
    {
        return match ($this) {
            self::Custom => 'Encuesta libre',
            self::GroupEvaluation => 'Escala de Evaluación de Grupo',
            self::Leadership => 'Habilidades Gerenciales y Liderazgo',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Custom => 'Armas tus propias secciones y preguntas.',
            self::GroupEvaluation => '10 ítems en escala 1 a 8. Calcula Madurez para la Tarea y Madurez Psicológica y envía el resultado por correo.',
            self::Leadership => '12 situaciones (A a D). Calcula los estilos Dirigir, Entrenar, Apoyar y Delegar y la flexibilidad para liderar, y envía el resultado por correo.',
        };
    }

    public function isInstrument(): bool
    {
        return $this !== self::Custom;
    }

    /**
     * Datos de quien responde que el instrumento necesita (el resultado se envía
     * por correo). null = los configura el usuario.
     *
     * @return array<string, string>|null
     */
    public function fixedRespondentFields(): ?array
    {
        return match ($this) {
            self::Custom => null,
            self::GroupEvaluation => ['first_name' => 'required', 'last_name' => 'required', 'email' => 'required'],
            self::Leadership => ['first_name' => 'required', 'last_name' => 'required', 'email' => 'required', 'organization' => 'required'],
        };
    }
}
