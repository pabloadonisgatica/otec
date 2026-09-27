<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DiplomaLogo extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'path',
    ];

    public function url(): string
    {
        return asset('storage/' . $this->path);
    }
}
