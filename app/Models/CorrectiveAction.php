<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CorrectiveAction extends Model
{
    protected $fillable = [
        'non_conformity_id',
        'type',
        'description',
        'responsible_id',
        'due_date',
        'status',
        'evidence',
        'completed_at',
        'implemented_at',
        'implementation_verified_at',
        'verifier_name',
        'effectiveness_verified_at',
        'effectiveness_satisfactory',
        'effectiveness_notes',
    ];

    protected $casts = [
        'due_date' => 'date',
        'completed_at' => 'datetime',
        'implemented_at' => 'date',
        'implementation_verified_at' => 'date',
        'effectiveness_verified_at' => 'date',
        'effectiveness_satisfactory' => 'boolean',
    ];

    public function nonConformity()
    {
        return $this->belongsTo(NonConformity::class);
    }

    public function responsible()
    {
        return $this->belongsTo(User::class, 'responsible_id');
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }
}
