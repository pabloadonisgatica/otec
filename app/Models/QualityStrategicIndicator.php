<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QualityStrategicIndicator extends Model
{
    protected $fillable = [
        'quality_document_id',
        'name',
        'value',
    ];

    public function document()
    {
        return $this->belongsTo(QualityDocument::class, 'quality_document_id');
    }
}
