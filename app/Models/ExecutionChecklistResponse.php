<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExecutionChecklistResponse extends Model
{
    protected $fillable = [
        'execution_id',
        'checklist_item_id',
        'status',
    ];

    public function execution()
    {
        return $this->belongsTo(Execution::class);
    }

    public function item()
    {
        return $this->belongsTo(ExecutionChecklistItem::class, 'checklist_item_id');
    }
}
