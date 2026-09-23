<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveyTemplate extends Model
{
    protected $fillable = [
        'name',
        'active',
        'banner_path',
        'description',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function bannerUrl(): ?string
    {
        return $this->banner_path
            ? asset('storage/' . $this->banner_path)
            : null;
    }

    public function sections()
    {
        return $this->hasMany(SurveyTemplateSection::class)
            ->orderBy('sort_order');
    }
}
