<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Budget;



class Course extends Model
{
    protected $fillable = [
        // relaciones base (empresa ahora puede ser nullable, se define en ejecución)
        'company_id',

        // nuevos
        'folio',
        'course_type', // sence | licitacion | privado

        // identificación
        'name',
        'sence_code',
        'activity_type',

        // modalidad múltiple
        'instruction_modalities', // json array

        // reglas / números
        'attendance_percentage',
        'min_grade',
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

        // costos / SENCE
        'value_per_participant',
        'sence_approval_date', // para calcular caducidad (+4 años) solo informativo

        // totales
        'hours',

        // diploma futuro
        'diploma_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'sence_approval_date' => 'date',

        'instruction_modalities' => 'array',

        'attendance_percentage' => 'decimal:2',
        'min_grade' => 'decimal:2',
        'hours' => 'decimal:2',

        'value_per_participant' => 'integer',
        'participants_count' => 'integer',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    // OJO: relatores ya NO se seleccionan en curso (se seleccionan en ejecución)
    // La relación puede quedarse por compatibilidad, pero luego la moveremos a Ejecución.
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
    public function budgets(): BelongsToMany
{
    return $this->belongsToMany(Budget::class, 'budget_courses')->withTimestamps();
}

}
