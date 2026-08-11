<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Participant extends Model
{
    protected $fillable = [
        'rut',
        'first_name',
        'last_name',
        'email',
        'phone',
        'company_id',
        'status',
        'notes',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function diplomas(): HasMany
    {
        return $this->hasMany(Diploma::class);
    }
    public function executions()
    {
        return $this->belongsToMany(Execution::class)
                    ->withTimestamps();
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class);
    }

    public function surveyResponses(): HasMany
    {
        return $this->hasMany(SurveyResponse::class);
    }

}

