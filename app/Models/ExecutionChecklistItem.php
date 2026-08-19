<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExecutionChecklistItem extends Model
{
    protected $fillable = [
        'section',
        'subsection',
        'label',
        'sort_order',
    ];
}
