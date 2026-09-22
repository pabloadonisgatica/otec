<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveyTemplate extends Model
{
    protected $fillable = [
        'name',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function sections()
    {
        return $this->hasMany(SurveyTemplateSection::class)
            ->orderBy('sort_order');
    }
}
