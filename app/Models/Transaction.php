<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Transaction extends Model
{
    protected $table = 'transactions';

    /**
     * Os atributos que podem ser atribuídos em massa.
     *
     * @var list<string>
     */
    protected $fillable = [
        'subscription_id',
        'plan_price_id',
        'amount',
        'currency',
        'status',
        'payment_gateway',
        'payment_method',
        'gateway_transaction_id',
        'paid_at',
        'meta',
    ];

    /**
     * Atributos que devem ser convertidos.
     */
    protected $casts = [
        'amount'  => 'decimal:2',
        'paid_at' => 'datetime',
        'meta'    => 'array',
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
}
