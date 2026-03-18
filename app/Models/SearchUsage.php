<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int $month
 * @property int $year
 * @property int $searches_used
 * @property int $leads_captured
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SearchUsage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SearchUsage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SearchUsage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SearchUsage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SearchUsage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SearchUsage whereLeadsCaptured($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SearchUsage whereMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SearchUsage whereSearchesUsed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SearchUsage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SearchUsage whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SearchUsage whereYear($value)
 * @mixin \Eloquent
 */
class SearchUsage extends Model
{
    protected $table = 'search_usages';

    /**
     * Os atributos que podem ser atribuídos em massa.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id', 
        'month', 
        'year', 
        'searches_used'
    ];

    /**
     *  atributos que devem ser convertidos.
     */
    protected $casts = [
        'month' => 'integer',
        'year' => 'integer',
        'searches_used' => 'integer'
    ];

    /*
    |--------------------------------------------------------------------------
    | RELAÇÕES
    |--------------------------------------------------------------------------
    */

    /**
     * Relação com usuário.
     */
    public function user(): BelongsTo
    { 
        return $this->belongsTo(User::class); 
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    /**
     * Incrementa o contador de buscas realizadas.
     * Isso consome o limite do plano.
     */
    public function incrementSearches(int $amount = 1): void
    {
        // Increment via SQL direto para atomicidade
        static::where('id', $this->id)->increment('searches_used', $amount);
        $this->searches_used += $amount;
    }
}