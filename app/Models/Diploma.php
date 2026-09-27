<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Diploma extends Model
{
    protected $fillable = [
        'template_id',
        'execution_id',
        'course_id',
        'participant_id',
        'code',
        'qr_path',
        'issued_at',
        'snapshot',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'snapshot'  => 'array',
    ];

    /**
     * RUT parcialmente oculto para la verificación pública
     * (ej: 12.345.678-9 → **.***.678-9).
     */
    public function maskedRut(): ?string
    {
        $rut = $this->snapshot['participant']['rut'] ?? null;

        if (! $rut) {
            return null;
        }

        $visible = 5; // "678-9"

        return preg_replace('/[0-9kK]/', '*', substr($rut, 0, -$visible)) . substr($rut, -$visible);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(DiplomaTemplate::class, 'template_id');
    }

    public function execution(): BelongsTo
    {
        return $this->belongsTo(Execution::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'participant_id');
    }
}
