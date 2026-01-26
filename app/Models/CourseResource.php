<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseResource extends Model
{
    public const TYPE_SUPPORT_MEDIA = 'support_media';
    public const TYPE_PARTICIPANT_MATERIAL = 'participant_material';
    public const TYPE_EQUIPMENT = 'equipment';
    public const TYPE_CONSUMABLES = 'consumables';

    protected $fillable = [
        'course_id',
        'type',
        'description',
        'quantity',
        'sort_order',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'sort_order' => 'integer',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
