<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QualityDocumentVersion extends Model
{
    protected $fillable = [
        'quality_document_id',
        'version_number',
        'file_path',
        'file_name',
        'external_url',
        'observation',
        'uploaded_by',
        'uploaded_at',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
    ];

    public function isExternalLink(): bool
    {
        return !empty($this->external_url) && empty($this->file_path);
    }

    public function document()
    {
        return $this->belongsTo(QualityDocument::class, 'quality_document_id');
    }
}
