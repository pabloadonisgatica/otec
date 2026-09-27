<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExecutionReport extends Model
{
    protected $fillable = [
        'execution_id',
        'purchase_order',
        'invoice_number',
        'guests',
        'observations',
        'suggestions',
    ];

    public function execution()
    {
        return $this->belongsTo(Execution::class);
    }
}
