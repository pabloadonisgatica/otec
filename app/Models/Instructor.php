<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Instructor extends Model
{
    protected $fillable = [
    'rut',
    'name',
    'email',
    'phone',
    'profession',
    'bio',
];

protected $casts = [
    'documents' => 'array',
];
}
