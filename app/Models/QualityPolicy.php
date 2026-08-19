<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QualityPolicy extends Model
{
    protected $fillable = [
        'title_1',
        'title_2',
        'detail',
        'signature_name',
        'signature_position',
        'approval_date',
        'version',
    ];

    protected $casts = [
        'approval_date' => 'date',
    ];

    public function commitments()
    {
        return $this->hasMany(QualityPolicyCommitment::class);
    }

    public static function current(): self
    {
        return self::firstOrCreate([]);
    }

    protected static function booted()
    {
        static::updating(function ($policy) {

            $changed = collect($policy->getDirty())
                ->except(['version', 'updated_at'])
                ->isNotEmpty();

            if ($changed) {
                $policy->version = $policy->version + 1;
            }
        });
    }
}
