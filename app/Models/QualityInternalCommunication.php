<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QualityInternalCommunication extends Model
{
    protected $fillable = [
        'topics',
        'channel',
        'audience',
        'communicated_at',
        'notes',
    ];

    protected $casts = [
        'topics' => 'array',
        'communicated_at' => 'date',
    ];

    public const TOPICS = [
        'politica' => 'Política de Calidad',
        'requisitos_norma' => 'Requisitos de la Norma NCh 2728:2015',
        'objetivos' => 'Objetivos de Calidad',
        'desempeno' => 'Desempeño del SGC',
    ];

    public const CHANNELS = [
        'Reunión de trabajo',
        'Diario mural',
        'Revista interna',
        'Correo electrónico',
        'Sitio de red interna / Intranet',
        'Encuesta a trabajadores',
        'Otro',
    ];

    public function topicLabels(): array
    {
        return collect($this->topics ?? [])
            ->map(fn ($key) => self::TOPICS[$key] ?? $key)
            ->all();
    }
}
