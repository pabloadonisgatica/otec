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
        'legal_documents',
        'process_map_title',
        'process_map_path',
        'org_chart_title',
        'org_chart_path',
        'financial_documents',
    ];

    protected $casts = [
        'last_audit_date' => 'date',
        'legal_documents' => 'array',
        'financial_documents' => 'array',
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
