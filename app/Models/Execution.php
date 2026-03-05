<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Execution extends Model
{
    protected $fillable = [
        'internal_code',
        'type', // 👈 agregar aquí
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
                ->orderBy('id', 'desc')
                ->first();

            $nextNumber = 1;

            if ($lastExecution && $lastExecution->internal_code) {
                $parts = explode('-', $lastExecution->internal_code);
                $lastNumber = intval(end($parts));
                $nextNumber = $lastNumber + 1;
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
}