<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Provider extends Model
{
    protected $fillable = [
        'rut',
        'name',
        'business_name',
        'category',
        'email',
        'phone',
        'address',
        'region',
        'commune',
        'contact_name',
        'contact_email',
        'contact_phone',
        'notes',
    ];

    /**
     * Evaluaciones del proveedor (NCH 2728 — 7.4), de la más reciente a la más antigua.
     */
    public function evaluations(): HasMany
    {
        return $this->hasMany(ProviderEvaluation::class)
            ->orderByDesc('evaluated_at')
            ->orderByDesc('id');
    }

    /**
     * Solicitudes de cotización enviadas a este proveedor (NCH 2728 — 7.4).
     */
    public function quoteRequests(): HasMany
    {
        return $this->hasMany(ProviderQuoteRequest::class);
    }

    public function latestEvaluation(): HasOne
    {
        return $this->hasOne(ProviderEvaluation::class)->ofMany([
            'evaluated_at' => 'max',
            'id' => 'max',
        ]);
    }
}
