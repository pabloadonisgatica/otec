<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NonConformity extends Model
{
    protected $fillable = [
        'code',
        'title',
        'description',
        'source',
        'process',
        'origin',
        'nc_type',
        'objective_evidence',
        'normative_reference',
        'correction',
        'root_cause',
        'execution_id',
        'responsible_id',
        'detected_at',
        'status',
        'notes',
        'closed_at',
    ];

    protected $casts = [
        'detected_at' => 'date',
        'closed_at' => 'datetime',
    ];

    public function execution()
    {
        return $this->belongsTo(Execution::class);
    }

    public function responsible()
    {
        return $this->belongsTo(User::class, 'responsible_id');
    }

    public function actions()
    {
        return $this->hasMany(CorrectiveAction::class);
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    /*
    |--------------------------------------------------------------------------
    | Boot - Generación automática de código (mismo patrón que Execution)
    |--------------------------------------------------------------------------
    */

    protected static function booted()
    {
        static::creating(function ($nonConformity) {

            $year = now()->year;

            $last = self::whereYear('created_at', $year)
                ->orderByDesc('id')
                ->first();

            $nextNumber = 1;

            if ($last && $last->code) {
                $parts = explode('-', $last->code);
                $nextNumber = intval(end($parts)) + 1;
            }

            $nonConformity->code = sprintf(
                'NC-%s-%03d',
                $year,
                $nextNumber
            );
        });
    }
}
