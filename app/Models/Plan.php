<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Plan
 *
 * @property int     $id
 * @property string  $name
 * @property int     $monthly_search_limit
 * @property float   $price
 * @property boolean $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $formatted_price
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereMonthlySearchLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan wherePrice($value)
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
    protected $fillable = ['name', 'monthly_search_limit', 'price', 'is_active'];

    /**
     *  atributos que devem ser convertidos.
     */
    protected $casts = [
        'price'               => 'decimal:2',
        'is_active'           => 'boolean',
        'monthly_search_limit' => 'integer',
    ];

    /**
     * Relações
     * 
     * Relacionamento com usuários
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Tabela pivô de Plans/Feature
     */
    public function features(): BelongsToMany
    {
        return $this->belongsToMany(Feature::class, 'plan_features');
    }

    /**
     * Atributos
     * 
     * Retorna o preço formatado.
     */
    public function getFormattedPriceAttribute(): string
    {
        return $this->price ? 'R$ '.number_format($this->price, 2, ',', '.') : 'Gratuito';
    }
}
