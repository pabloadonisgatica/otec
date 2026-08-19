<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QualityRepresentative extends Model
{
    protected $fillable = [
        'name',
        'position',
        'start_date',
        'end_date',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * El representante vigente: el que no tiene fecha de término.
     */
    public static function current(): ?self
    {
        return self::whereNull('end_date')->latest('start_date')->first();
    }
}
