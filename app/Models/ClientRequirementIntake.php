<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Levantamiento del Requerimiento del Cliente (NCH 2728 — 7.2.1 y 7.2.2).
 */
class ClientRequirementIntake extends Model
{
    public const MODALITIES = [
        'presencial' => 'Presencial',
        'online' => 'En línea',
        'mixto' => 'Mixta',
    ];

    public const REVIEW_STATUSES = [
        'pending' => 'Pendiente de revisión',
        'feasible' => 'Se puede cumplir',
        'not_feasible' => 'No se puede cumplir',
    ];

    public const REVIEW_BADGES = [
        'pending' => 'bg-amber-50 text-amber-700',
        'feasible' => 'bg-green-50 text-green-700',
        'not_feasible' => 'bg-red-50 text-red-700',
    ];

    protected $fillable = [
        'company_id',
        'contact_name',
        'contact_email',
        'contact_phone',
        'requested_at',
        'title',
        'details',
        'participants_estimate',
        'modality',
        'desired_start_date',
        'requires_sence',
        'attachment_path',
        'attachment_name',
        'review_status',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
        'budget_id',
        'created_by',
    ];

    protected $casts = [
        'requested_at' => 'date',
        'desired_start_date' => 'date',
        'reviewed_at' => 'date',
        'requires_sence' => 'boolean',
        'participants_estimate' => 'integer',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function budget(): BelongsTo
    {
        return $this->belongsTo(Budget::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function modalityLabel(): ?string
    {
        return self::MODALITIES[$this->modality] ?? null;
    }

    public function reviewStatusLabel(): string
    {
        return self::REVIEW_STATUSES[$this->review_status] ?? $this->review_status;
    }

    public function reviewStatusBadge(): string
    {
        return self::REVIEW_BADGES[$this->review_status] ?? 'bg-gray-100 text-gray-600';
    }

    public function isReviewed(): bool
    {
        return $this->review_status !== 'pending';
    }

    public function scopeFilter(Builder $query, ?string $status, ?string $search, ?int $companyId): Builder
    {
        return $query
            ->when($status, fn ($q) => $q->where('review_status', $status))
            ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('code', 'like', "%{$search}%")
                        ->orWhere('title', 'like', "%{$search}%")
                        ->orWhere('contact_name', 'like', "%{$search}%");
                });
            });
    }

    /**
     * Código correlativo por año: LR-2026-001 (mismo criterio que No Conformidades).
     */
    protected static function booted(): void
    {
        static::creating(function (self $intake) {
            $prefix = 'LR-'.now()->year.'-';

            $lastCode = self::where('code', 'like', $prefix.'%')
                ->orderByDesc('code')
                ->value('code');

            $next = $lastCode ? ((int) substr($lastCode, strlen($prefix))) + 1 : 1;

            $intake->code = $prefix.str_pad((string) $next, 3, '0', STR_PAD_LEFT);
        });
    }
}
