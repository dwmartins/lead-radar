<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $plan_id
 * @property numeric $price
 * @property string $currency
 * @property string|null $stripe_price_id
 * @property string $type
 * @property string|null $interval
 * @property int|null $interval_count
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $formatted_price
 * @property-read \App\Models\Plan $plan
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanPrice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanPrice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanPrice query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanPrice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanPrice whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanPrice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanPrice whereInterval($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanPrice whereIntervalCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanPrice whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanPrice wherePlanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanPrice wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanPrice whereStripePriceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanPrice whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanPrice whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PlanPrice extends Model
{
    protected $table = 'plan_prices';

    /**
     * Os atributos que podem ser atribuídos em massa.
     *
     * @var list<string>
     */
    protected $fillable = [
        'plan_id',
        'price',
        'currency',
        'is_active',
        'stripe_price_id',
        'type',
        'interval',
        'interval_count'
    ];

    /**
     *  atributos que devem ser convertidos.
     */
    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'interval_count' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELAÇÕES
    |--------------------------------------------------------------------------
    */

    /**
     * Relação com o plano
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    /*
    |--------------------------------------------------------------------------
    | ATTRIBUTES
    |--------------------------------------------------------------------------
    */

    /**
     * Retorna o Preço formatado.
     */
    public function getFormattedPriceAttribute(): string
    {
        if (!$this->price) {
            return __('messages.free');
        }

        return match ($this->currency) {
            'BRL' => 'R$ ' . number_format($this->price, 2, ',', '.'),
            'USD' => '$ ' . number_format($this->price, 2, '.', ','),
            default => $this->price . ' ' . $this->currency,
        };
    }

    /**
     * Verifica se é um plano recorrente
     */
    public function isRecurring(): bool
    {
        return $this->type === 'recurring';
    }

    /**
     * Verifica se é um pagamento único
     */
    public function isOneTime(): bool
    {
        return $this->type === 'one_time';
    }
}