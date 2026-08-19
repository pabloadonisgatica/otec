<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QualityJobProfile extends Model
{
    protected $fillable = [
        'profile_date',
        'position_name',
        'functions',
        'responsibilities',
        'area',
        'reports_to',
        'direct_reports',
        'education_requirements',
        'training_requirements',
        'skills_requirements',
        'experience_requirements',
    ];

    protected $casts = [
        'profile_date' => 'date',
    ];

    /**
     * Procedimientos asignados a este perfil de cargo.
     * (viven en quality_documents con type = 'procedimiento')
     */
    public function procedures()
    {
        return $this->belongsToMany(
            QualityDocument::class,
            'quality_job_profile_procedure',
            'quality_job_profile_id',
            'quality_document_id'
        )->withTimestamps();
    }
}
