<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DiplomaTemplate extends Model
{
    /**
     * Diseño oficial (vista Blade fija, idéntica al diploma F38).
     * Las plantillas sin layout son las antiguas en HTML editable.
     */
    public const LAYOUT_OFFICIAL = 'official';

    protected $fillable = [
        'name',
        'layout',
        'background_path',
        'content_html',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public static function official(): self
    {
        return static::where('layout', self::LAYOUT_OFFICIAL)->firstOrFail();
    }

    public function isOfficial(): bool
    {
        return $this->layout === self::LAYOUT_OFFICIAL;
    }

    public function diplomas(): HasMany
    {
        return $this->hasMany(Diploma::class, 'template_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
