<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QualityRecord extends Model
{
    protected $fillable = [
        'name',
        'version',
        'approval_user',
        'approval_date',
        'protection',
        'storage_location',
        'retention_time',
        'recovery',
        'final_disposition',
    ];

    protected $casts = [
        'approval_date' => 'date',
    ];

    protected static function booted()
    {
        static::updating(function ($record) {

            // Si hay cambios reales (más allá de "version" mismo),
            // sube la versión automáticamente.
            $changed = collect($record->getDirty())
                ->except(['version', 'updated_at'])
                ->isNotEmpty();

            if ($changed) {
                $record->version = $record->version + 1;
            }
        });
    }
}
