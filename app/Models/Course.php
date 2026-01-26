<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    protected $fillable = [
        // relaciones base
        'company_id',

        // identificación
        'name',
        'sence_code',
        'activity_type',
        'instruction_modality',

        // reglas / números
        'attendance_percentage',
        'min_grade',
        'min_hours',
        'participants_count',

        // fechas / estado
        'start_date',
        'end_date',
        'status',

        // textos largos
        'technical_foundation',
        'target_population',
        'general_objectives',
        'teaching_methodology',
        'notes',

        // costos / vigencia
        'value_per_participant',
        'sence_request_date',
        'sence_expiration_date',

        // diploma futuro / totales
        'diploma_id',
        'hours',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'sence_request_date' => 'date',
        'sence_expiration_date' => 'date',

        'attendance_percentage' => 'decimal:2',
        'min_grade' => 'decimal:2',
        'value_per_participant' => 'integer',
        'participants_count' => 'integer',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function instructors(): BelongsToMany
    {
        return $this->belongsToMany(Instructor::class)
            ->withTimestamps()
            ->withPivot(['role']);
    }

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(Participant::class)
            ->withTimestamps()
            ->withPivot(['enrollment_status']);
    }

    public function contents(): HasMany
    {
        return $this->hasMany(CourseContent::class)->orderBy('sort_order');
    }
}
