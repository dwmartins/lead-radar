<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property int $subscription_id
 * @property int|null $plan_price_id
 * @property numeric $amount
 * @property numeric|null $amount_refunded
 * @property string $currency
 * @property string $status
 * @property string|null $payment_gateway
 * @property string|null $payment_method
 * @property string|null $gateway_transaction_id
 * @property \Illuminate\Support\Carbon|null $paid_at
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property array<array-key, mixed>|null $meta
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $formatted_amount
 * @property-read \App\Models\PlanPrice|null $planPrice
 * @property-read \App\Models\Subscription $subscription
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereAmountRefunded($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereGatewayTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction wherePaidAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction wherePaymentGateway($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction wherePlanPriceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereSubscriptionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Transaction extends Model
{
    /**
     * Os atributos que podem ser atribuídos em massa.
     *
     * @var list<string>
     */
    protected $fillable = [
        'subscription_id',
        'plan_price_id',
        'amount',
        'amount_refunded',
        'currency',
        'status',
        'payment_gateway',
        'payment_method',
        'gateway_transaction_id',
        'paid_at',
        'expires_at',
        'meta',
    ];

    /**
     * Atributos que devem ser convertidos.
     */
    protected $casts = [
        'amount'          => 'decimal:2',
        'amount_refunded' => 'decimal:2',
        'paid_at'         => 'datetime',
        'expires_at'      => 'datetime',
        'meta'            => 'array',
    ];

    /*
    |--------------------------------------------------------------------------
    | STATUS
    |--------------------------------------------------------------------------
    */

    public const STATUS_PENDING  = 'pending';
    public const STATUS_PAID     = 'paid';
    public const STATUS_FAILED   = 'failed';
    public const STATUS_REFUNDED = 'refunded';

    public const GATEWAY_STRIPE       = 'stripe';
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
     * Relacionamento com subscription
     */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    /**
     * Relacionamento com o preço do plano
     */
    public function planPrice(): BelongsTo
    {
        return $this->belongsTo(PlanPrice::class);
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    /**
     * Retorna o valor formatado com base na moeda.
     */
    public function getFormattedAmountAttribute(): string
    {
        return match ($this->currency) {
            'BRL' => 'R$ ' . number_format($this->amount, 2, ',', '.'),
            'USD' => '$ ' . number_format($this->amount, 2, '.', ','),
            default => $this->amount . ' ' . $this->currency,
        };
    }

    /**
     * Verifica se o pagamento foi concluído.
     */
    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    /**
     * Verifica se o pagamento está pendente.
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Verifica se o pagamento falhou.
     */
    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    /**
     * Verifica se o pagamento foi reembolsado.
     */
    public function isRefunded(): bool
    {
        return $this->status === self::STATUS_REFUNDED;
    }

    /**
     * Verifica se houve reembolso parcial
     */
    public function isPartiallyRefunded(): bool
    {
        return $this->amount_refunded > 0 && $this->amount_refunded < $this->amount;
    }

    /**
     * Marca a transação como paga.
     */
    public function markAsPaid(): void
    {
        $this->update([
            'status'  => self::STATUS_PAID,
            'paid_at' => now(),
        ]);
    }

    /**
     * Marca a transação como falha.
     */
    public function markAsFailed(): void
    {
        $this->update([
            'status' => self::STATUS_FAILED,
        ]);
    }

    /**
     * Marca a transação como reembolsada.
     */
    public function markAsRefunded(): void
    {
        $this->update([
            'status' => self::STATUS_REFUNDED,
        ]);
    }

    /**
     * Verifica se a transação já expirou (ciclo vencido).
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }
}
