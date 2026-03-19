<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property int $monthly_search_limit
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Feature> $features
 * @property-read int|null $features_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PlanPrice> $prices
 * @property-read int|null $prices_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereMonthlySearchLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Plan extends Model
{
    use HasFactory;

    protected $table = 'plans';

    /**
     * Os atributos que podem ser atribuídos em massa.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name', 
        'monthly_search_limit', 
        'is_active'
    ];

    /**
     *  atributos que devem ser convertidos.
     */
    protected $casts = [
        'is_active'            => 'boolean',
        'monthly_search_limit' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELAÇÕES
    |--------------------------------------------------------------------------
    */

    /**
     * Tabela pivô de Plans/Feature
     */
    public function features(): BelongsToMany
    {
        return $this->belongsToMany(Feature::class, 'plan_features');
    }

    /**
     * Relação com prices
     */
    public function prices(): HasMany
    {
        return $this->hasMany(PlanPrice::class);
    }
    
    /*
    |--------------------------------------------------------------------------
    | ATTRIBUTES
    |--------------------------------------------------------------------------
    */

    /**
     * Retorna o preço do plano para uma moeda específica.
     *
     * Busca o primeiro preço ativo associado ao plano
     * de acordo com a moeda informada.
     *
     * @param string $currency Código da moeda (ex: BRL, USD, EUR)
     * @return \App\Models\PlanPrice|null Retorna a model PlanPrice ou null caso não exista
     */
    public function priceFor(string $currency = 'BRL'): ?PlanPrice
    {
        return $this->prices()
            ->where('currency', $currency)
            ->where('is_active', true)
            ->first();
    }
}
