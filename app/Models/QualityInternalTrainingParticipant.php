<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QualityInternalTrainingParticipant extends Model
{
    protected $fillable = [
        'quality_internal_training_id',
        'name',
        'position',
    ];

    public function training()
    {
        return $this->belongsTo(QualityInternalTraining::class, 'quality_internal_training_id');
    }
}
