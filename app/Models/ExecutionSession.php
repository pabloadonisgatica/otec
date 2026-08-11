<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExecutionSession extends Model
{
    protected $fillable = [
        'execution_id',
        'instructor_id',
        'session_date',
        'start_time',
        'end_time',
        'hours',
    ];

    public function execution()
    {
        return $this->belongsTo(Execution::class);
    }

    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}