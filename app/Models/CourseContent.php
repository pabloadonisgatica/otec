<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseContent extends Model
{
    protected $fillable = [
        'course_id',
        'activity',
        'content',
        'hours_theoretical',
        'hours_practical',
        'hours_elearning',
        'sort_order',
    ];

    protected $casts = [
        'hours_theoretical' => 'integer',
        'hours_practical' => 'integer',
        'hours_elearning' => 'integer',
        'sort_order' => 'integer',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
