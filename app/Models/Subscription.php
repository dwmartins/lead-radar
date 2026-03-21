<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
        'billing_cycle',
        
        // Stripe
        'stripe_subscription_id',
        'stripe_customer_id',    
        'stripe_status', 
        
        // Manual
        'expires_at', // usado apenas para assinaturas manuais  
        'is_manual',  // indica assinatura manual
        'note',       // observação ou descrição
    ];

    /**
     *  atributos que devem ser convertidos.
     */
    protected $casts = [
        'expires_at' => 'datetime',
        'is_manual'  => 'boolean',
    ];


    public const STATUS_ACTIVE   = 'active';
    public const STATUS_CANCELED = 'canceled';
    public const STATUS_PENDING  = 'pending';
    public const STATUS_EXPIRED  = 'expired';
    
    public const BILLING_MONTHLY    = 'monthly';
    public const BILLING_SEMIANNUAL = 'semiannual';
    public const BILLING_YEARLY     = 'yearly';

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

    /**
     * Transações da assinatura
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    /**
     * Verifica se é uma assinatura manual
     */
    public function isManual(): bool
    {
        return $this->is_manual === true;
    }

    /**
     * Verifica se veio do Stripe
     */
    public function isStripe(): bool
    {
        return !$this->is_manual;
    }

    /**
     * Verifica se está ativa
     */
    public function isActive(): bool
    {
        if ($this->status !== self::STATUS_ACTIVE) {
            return false;
        }

        // Manual → usa expires_at
        if ($this->isManual()) {
            return !$this->expires_at || $this->expires_at->isFuture();
        }

        // Stripe → usa status do Stripe
        if ($this->stripe_status) {
            return in_array($this->stripe_status, ['active', 'trialing']);
        }

        return false;
    }

    /**
     * Verifica se expirou (manual)
     */
    public function isExpired(): bool
    {
        if ($this->isManual() && $this->expires_at) {
            return $this->expires_at->isPast();
        }

        return false;
    }

    /**
     * Verifica se a assinatura foi cancelada.
     */
    public function isCanceled(): bool
    {
        return $this->status === self::STATUS_CANCELED;
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
        ]);
    }

    /**
     * Retorna a última transaction
     */
    public function latestTransaction(): HasMany
    {
        return $this->transactions()->latest();
    }
}
