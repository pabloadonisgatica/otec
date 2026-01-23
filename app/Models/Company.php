<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

protected $fillable = [
    'rut',
    'name',
    'business_name',
    'email',
    'phone',
    'address',
    'region',
    'commune',
    'contact_name',
    'contact_email',
    'contact_phone',
];
}
