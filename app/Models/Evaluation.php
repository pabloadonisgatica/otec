<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    protected $fillable = [
        'execution_id',
        'participant_id',
        'final_grade',
    ];

    public function execution()
    {
        return $this->belongsTo(Execution::class);
    }

    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }
}
