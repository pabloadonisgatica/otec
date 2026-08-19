<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QualityPolicyObjective extends Model
{
    protected $fillable = [
        'quality_policy_commitment_id',
        'title',
    ];

    public function commitment()
    {
        return $this->belongsTo(QualityPolicyCommitment::class, 'quality_policy_commitment_id');
    }
}
