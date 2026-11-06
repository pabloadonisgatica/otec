<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Solicitud de cotización a un proveedor (NCH 2728 — 7.4 Compras).
 */
class ProviderQuoteRequest extends Model
{
    public const STATUSES = [
        'sent' => 'Enviada, esperando respuesta',
        'received' => 'Cotización recibida',
        'accepted' => 'Aceptada',
        'rejected' => 'Rechazada',
    ];

    public const STATUS_BADGES = [
        'sent' => 'bg-amber-50 text-amber-700',
        'received' => 'bg-blue-50 text-blue-700',
        'accepted' => 'bg-green-50 text-green-700',
        'rejected' => 'bg-red-50 text-red-700',
    ];

    protected $fillable = [
        'provider_id',
        'execution_id',
        'requested_by',
        'requested_at',
        'needed_by',
        'items',
        'conditions',
        'status',
        'responded_at',
        'quoted_amount',
        'response_notes',
        'attachment_path',
        'attachment_name',
    ];

    protected $casts = [
        'requested_at' => 'date',
        'needed_by' => 'date',
        'responded_at' => 'date',
        'items' => 'array',
        'quoted_amount' => 'integer',
    ];

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    public function execution(): BelongsTo
    {
        return $this->belongsTo(Execution::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function statusBadge(): string
    {
        return self::STATUS_BADGES[$this->status] ?? 'bg-gray-100 text-gray-600';
    }

    public function hasResponse(): bool
    {
        return $this->status !== 'sent';
    }

    /** Monto en formato chileno: "$1.250.000". */
    public function quotedAmountLabel(): ?string
    {
        return $this->quoted_amount === null
            ? null
            : '$'.number_format($this->quoted_amount, 0, ',', '.');
    }

    /** Cantidad sin decimales innecesarios: 2 → "2", 1.5 → "1,5". */
    public static function formatQuantity(mixed $quantity): string
    {
        $value = (float) $quantity;

        return floor($value) == $value
            ? number_format($value, 0, ',', '.')
            : rtrim(rtrim(number_format($value, 2, ',', '.'), '0'), ',');
    }

    public function isOverdue(): bool
    {
        return $this->status === 'sent'
            && $this->needed_by !== null
            && $this->needed_by->isPast()
            && ! $this->needed_by->isToday();
    }

    public function scopeFilter(Builder $query, ?string $status, ?int $providerId, ?string $search): Builder
    {
        return $query
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($providerId, fn ($q) => $q->where('provider_id', $providerId))
            ->when($search, fn ($q) => $q->where('code', 'like', "%{$search}%"));
    }

    /**
     * Código correlativo por año: SC-2026-001.
     */
    protected static function booted(): void
    {
        static::creating(function (self $quoteRequest) {
            $prefix = 'SC-'.now()->year.'-';

            $lastCode = self::where('code', 'like', $prefix.'%')
                ->orderByDesc('code')
                ->value('code');

            $next = $lastCode ? ((int) substr($lastCode, strlen($prefix))) + 1 : 1;

            $quoteRequest->code = $prefix.str_pad((string) $next, 3, '0', STR_PAD_LEFT);
        });
    }
}
