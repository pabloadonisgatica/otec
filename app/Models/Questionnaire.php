<?php

namespace App\Models;

use App\Enums\QuestionnaireType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * Encuesta independiente (módulo Encuestas). Se comparte por link + QR.
 */
class Questionnaire extends Model
{
    use SoftDeletes;

    /** La app guarda en UTC; las fechas se ingresan y muestran en hora de Chile. */
    public const TIMEZONE = 'America/Santiago';

    public const RESPONDENT_FIELDS = [
        'first_name' => 'Nombre',
        'last_name' => 'Apellido',
        'email' => 'Correo',
        'organization' => 'Organización',
    ];

    public const STATUSES = [
        'active' => 'Activa',
        'inactive' => 'Inactiva',
        'closed' => 'Cerrada',
    ];

    protected $fillable = [
        'type',
        'name',
        'description',
        'is_active',
        'closes_at',
        'company_id',
        'course_id',
        'execution_id',
        'respondent_fields',
        'banner_path',
    ];

    protected $casts = [
        'type' => QuestionnaireType::class,
        'is_active' => 'boolean',
        'closes_at' => 'datetime',
        'respondent_fields' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (Questionnaire $questionnaire) {
            $questionnaire->token ??= Str::random(32);
        });
    }

    public function sections(): HasMany
    {
        return $this->hasMany(QuestionnaireSection::class)->orderBy('sort_order');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(QuestionnaireQuestion::class);
    }

    /** Solo las encuestas libres tienen constructor; los instrumentos son fijos. */
    public function hasBuilder(): bool
    {
        return ! $this->type->isInstrument();
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function execution(): BelongsTo
    {
        return $this->belongsTo(Execution::class);
    }

    /** active | inactive | closed */
    public function status(): string
    {
        if (! $this->is_active) {
            return 'inactive';
        }

        return $this->closes_at && $this->closes_at->isPast() ? 'closed' : 'active';
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status()];
    }

    public function acceptsResponses(): bool
    {
        return $this->status() === 'active';
    }

    /**
     * Datos que se piden a quien responde: los del instrumento, o los configurados.
     *
     * @return array<string, string> campo => required|optional (solo los que se piden)
     */
    public function respondentFields(): array
    {
        $fields = $this->type?->fixedRespondentFields() ?? (array) $this->respondent_fields;

        return array_intersect_key($fields, self::RESPONDENT_FIELDS);
    }

    public function isAnonymous(): bool
    {
        return $this->respondentFields() === [];
    }

    /** Texto de la asociación para listados: "Ejecución EJ-2026-003", "Curso X"… */
    public function associationLabel(): ?string
    {
        if ($this->execution) {
            return 'Ejecución ' . trim(($this->execution->internal_code ?? '') . ' ' . $this->execution->course_name);
        }

        if ($this->course) {
            return 'Curso ' . $this->course->name;
        }

        return $this->company ? 'Empresa ' . $this->company->name : null;
    }

    public function associationType(): string
    {
        return match (true) {
            (bool) $this->execution_id => 'execution',
            (bool) $this->course_id => 'course',
            (bool) $this->company_id => 'company',
            default => 'none',
        };
    }

    public function bannerUrl(): ?string
    {
        return $this->banner_path ? asset('storage/' . $this->banner_path) : null;
    }

    public function closesAtLocal(): ?string
    {
        return $this->closes_at?->copy()->timezone(self::TIMEZONE)->format('d-m-Y H:i');
    }

    /** Filtros del listado. */
    public function scopeListFilters(Builder $query, ?string $search, ?string $status, ?string $type): Builder
    {
        return $query
            ->when($search, fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->when($type, fn ($q) => $q->where('type', $type))
            ->when($status === 'inactive', fn ($q) => $q->where('is_active', false))
            ->when($status === 'closed', fn ($q) => $q->where('is_active', true)
                ->whereNotNull('closes_at')->where('closes_at', '<=', now()))
            ->when($status === 'active', fn ($q) => $q->where('is_active', true)
                ->where(fn ($q) => $q->whereNull('closes_at')->orWhere('closes_at', '>', now())));
    }
}
