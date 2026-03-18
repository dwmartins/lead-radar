<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int $plan_id
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $started_at
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property \Illuminate\Support\Carbon|null $canceled_at
 * @property \Illuminate\Support\Carbon|null $trial_ends_at
 * @property string|null $payment_gateway
 * @property string|null $payment_method
 * @property string $billing_cycle
 * @property numeric $amount
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Plan $plan
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereBillingCycle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereCanceledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription wherePaymentGateway($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription wherePlanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereTrialEndsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereUserId($value)
 * @mixin \Eloquent
 */
class Subscription extends Model
{
    /**
     * Os atributos que podem ser atribuídos em massa.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'plan_id',
        'status',
        'started_at',
        'expires_at',
        'canceled_at',
        'trial_ends_at',
        'payment_gateway',
        'payment_method',
        'billing_cycle',
        'amount',
    ];

    /**
     *  atributos que devem ser convertidos.
     */
    protected $casts = [
        'started_at' => 'datetime',
        'expires_at' => 'datetime',
        'canceled_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public const STATUS_ACTIVE   = 'active';
    public const STATUS_CANCELED = 'canceled';
    public const STATUS_EXPIRED  = 'expired';
    public const STATUS_TRIAL    = 'trial';

    public const BILLING_MONTHLY    = 'monthly';
    public const BILLING_SEMIANNUAL = 'semiannual';
    public const BILLING_YEARLY     = 'yearly';

    public const GATEWAY_STRIPE       = 'stripe';
    public const GATEWAY_MERCADO_PAGO = 'mercado_pago';
    public const GATEWAY_PAYPAL       = 'paypal';
    public const GATEWAY_MANUAL       = 'manual';
    public const GATEWAY_SEED         = 'seed';

    public const METHOD_CREDIT_CARD = 'credit_card';
    public const METHOD_PIX         = 'pix';
    public const METHOD_BOLETO      = 'boleto';
    public const METHOD_DEBIT_CARD  = 'debit_card';

    /*
    |--------------------------------------------------------------------------
    | RELAÇÕES
    |--------------------------------------------------------------------------
    */

    /**
     * Relacionamento com usuários
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento com planos
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    /**
     * Verifica se a assinatura está ativa.
     */
    public function isActive(): bool
    {
        if ($this->status !== self::STATUS_ACTIVE) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Verifica se a assinatura está expirada.
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Verifica se a assinatura foi cancelada.
     */
    public function isCanceled(): bool
    {
        return $this->status === self::STATUS_CANCELED;
    }

    /**
     * Verifica se a assinatura está em período de teste (trial).
     */
    public function isTrial(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    /**
     * Verifica se a assinatura está ativa OU em trial.
     */
    public function isValid(): bool
    {
        return $this->isActive() || $this->isTrial();
    }

    /**
     * Retorna quantos dias faltam para expirar.
     */
    public function daysRemaining(): ?int
    {
        if (!$this->expires_at) {
            return null;
        }

        return now()->diffInDays($this->expires_at, false);
    }

    /**
     * Retorna a quantidade de meses da subscription
     */
    public function getCycleInMonths(): int
    {
        return match ($this->billing_cycle) {
            self::BILLING_MONTHLY => 1,
            self::BILLING_SEMIANNUAL => 6,
            self::BILLING_YEARLY => 12,
            default => 1,
        };
    }

    /**
     * Cancela a assinatura.
     */
    public function cancel(): void
    {
        $this->update([
            'status' => self::STATUS_CANCELED,
            'canceled_at' => now(),
        ]);
    }

    /**
     * Marca como expirada.
     */
    public function markAsExpired(): void
    {
        $this->update([
            'status' => self::STATUS_EXPIRED,
        ]);
    }
}
