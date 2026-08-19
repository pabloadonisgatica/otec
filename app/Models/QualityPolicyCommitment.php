<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QualityPolicyCommitment extends Model
{
    protected $fillable = [
        'quality_policy_id',
        'title',
    ];

    public function policy()
    {
        return $this->belongsTo(QualityPolicy::class, 'quality_policy_id');
    }

    public function objectives()
    {
        return $this->hasMany(QualityPolicyObjective::class);
    }
}
