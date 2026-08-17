<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QualityDocument extends Model
{
    protected $fillable = [
        'type',
        'code',
        'name',
        'reviewer_name',
        'review_date',
        'approver_name',
        'approval_date',
        'next_review_date',
    ];

    protected $casts = [
        'review_date' => 'date',
        'approval_date' => 'date',
        'next_review_date' => 'date',
    ];

    public function versions()
    {
        return $this->hasMany(QualityDocumentVersion::class)
            ->orderByDesc('version_number');
    }

    public function currentVersion()
    {
        return $this->versions()->first();
    }

    /**
     * El Manual de Calidad es un documento único (singleton),
     * igual que QualityProfile.
     */
    public static function manualCalidad(): self
    {
        return self::firstOrCreate(
            ['type' => 'manual_calidad'],
            ['name' => 'Manual de Calidad']
        );
    }
}
