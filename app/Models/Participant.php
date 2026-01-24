<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    protected $fillable = [
        'rut',
        'first_name',
        'last_name',
        'email',
        'phone',
        'company_id',
        'status',
        'notes',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}

