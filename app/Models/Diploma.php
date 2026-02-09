<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Diploma extends Model
{
    protected $fillable = [
        'template_id',
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

    public function template(): BelongsTo
    {
        return $this->belongsTo(DiplomaTemplate::class, 'template_id');
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
