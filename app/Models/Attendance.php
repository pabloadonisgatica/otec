<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'execution_session_id',
        'participant_id',
        'present',
        'observations',
    ];

    protected $casts = [
        'present' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function session()
    {
        return $this->belongsTo(ExecutionSession::class, 'execution_session_id');
    }

    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }
}
