<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Plan
 * @property int     $id
 * @property string  $name
 * @property int     $monthly_leads_limit
 * @property float   $price
 * @property boolean $is_active
 */
class Plan extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'monthly_leads_limit', 'price', 'is_active'];

    protected $casts = [
        'price'               => 'decimal:2',
        'is_active'           => 'boolean',
        'monthly_leads_limit' => 'integer',
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
     * Atributos
     * 
     * Retorna o preço formatado.
     */
    public function getFormattedPriceAttribute(): string
    {
        return $this->price ? 'R$ '.number_format($this->price, 2, ',', '.') : 'Gratuito';
    }
}
