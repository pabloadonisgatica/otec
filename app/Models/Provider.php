<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    protected $fillable = [
        'rut',
        'name',
        'business_name',
        'category',
        'email',
        'phone',
        'address',
        'region',
        'commune',
        'contact_name',
        'contact_email',
        'contact_phone',
        'notes',
    ];
}
