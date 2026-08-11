<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Execution extends Model
{
    protected $fillable = [
        'internal_code',
        'type',
        'course_id',
        'company_id',
        'course_name',
        'modality',
        'evaluation_type',
        'region_id',
        'city_id',
        'place',
        'start_date',
        'status',
        'observations',

        // Datos del curso
        'course_hours',
        'hours_per_day',
        'end_date',

        /*
        |--------------------------------------------------------------------------
        | Planificación (Temporal)
        |--------------------------------------------------------------------------
        | Estos campos se eliminarán cuando toda la lógica
        | migre a execution_plannings.
        */
        'planning_start_time',
        'exclude_saturdays',
        'exclude_sundays',
        'exclude_holidays',
    ];

    /*
    |--------------------------------------------------------------------------
    | Boot - Generación automática código interno
    |--------------------------------------------------------------------------
    */

    protected static function booted()
    {
        static::creating(function ($execution) {

            $year = now()->year;

            $lastExecution = self::whereYear('created_at', $year)
                ->orderByDesc('id')
                ->first();

            $nextNumber = 1;

            if ($lastExecution && $lastExecution->internal_code) {
                $parts = explode('-', $lastExecution->internal_code);
                $nextNumber = intval(end($parts)) + 1;
            }

            $execution->internal_code = sprintf(
                'EJ-%s-%03d',
                $year,
                $nextNumber
            );
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function sessions()
    {
        return $this->hasMany(ExecutionSession::class);
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }

    public function surveyResponses()
    {
        return $this->hasMany(SurveyResponse::class);
    }

    public function diplomas()
    {
        return $this->hasMany(Diploma::class);
    }

    public function participants()
    {
        return $this->belongsToMany(Participant::class)
            ->withTimestamps();
    }

    public function instructors()
    {
        return $this->belongsToMany(Instructor::class)
            ->withTimestamps();
    }

    /**
     * Nueva planificación académica.
     */
    public function planning()
    {
        return $this->hasOne(ExecutionPlanning::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isFinalized(): bool
    {
        return $this->status === 'finalizada';
    }

    /**
     * Resume el horario recurrente a partir de las sesiones
     * ya generadas (ej: "JUEVES de 09:00 a 13:00").
     * Usado en el Libro de Control de Clases (PDF).
     */
    public function scheduleSummary(): string
    {
        $patterns = $this->sessions
            ->filter(fn ($session) => $session->start_time && $session->end_time)
            ->map(function ($session) {
                $date = Carbon::parse($session->session_date);

                return strtoupper($date->translatedFormat('l')) . ' de '
                    . Carbon::parse($session->start_time)->format('H:i') . ' a '
                    . Carbon::parse($session->end_time)->format('H:i');
            })
            ->unique()
            ->values();

        return $patterns->isEmpty()
            ? '—'
            : $patterns->implode(' / ');
    }

    public function calculateEndDate(): ?string
    {
        if (
            !$this->start_date ||
            !$this->course_hours ||
            !$this->hours_per_day
        ) {
            return null;
        }

        $requiredDays = (int) ceil(
            $this->course_hours / $this->hours_per_day
        );

        $date = Carbon::parse($this->start_date);

        $count = 1;

        while ($count < $requiredDays) {

            $date->addDay();

            if ($date->isWeekday()) {
                $count++;
            }
        }

        return $date->format('Y-m-d');
    }
}