<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QualityProfile extends Model
{
    protected $fillable = [
        'vision',
        'mission',
        'scope',
        'last_audit_date',
        'legal_documentation',
    ];

    protected $casts = [
        'last_audit_date' => 'date',
    ];

    /**
     * Solo existe un registro (perfil general de la OTEC
     * para el sistema de gestión de calidad).
     */
    public static function current(): self
    {
        return self::firstOrCreate([]);
    }
}
