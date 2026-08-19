<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QualityInternalTraining extends Model
{
    protected $fillable = [
        'activity_date',
        'activity_name',
        'objective',
        'instructor_name',
        'hours',
        'objective_met',
        'compliance_description',
        'additional_actions',
    ];

    protected $casts = [
        'activity_date' => 'date',
        'objective_met' => 'boolean',
    ];

    public function participants()
    {
        return $this->hasMany(QualityInternalTrainingParticipant::class);
    }
}
