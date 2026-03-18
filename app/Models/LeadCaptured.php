<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int $month
 * @property int $year
 * @property int $leads_captured
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LeadCaptured newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LeadCaptured newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LeadCaptured query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LeadCaptured whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LeadCaptured whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LeadCaptured whereLeadsCaptured($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LeadCaptured whereMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LeadCaptured whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LeadCaptured whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LeadCaptured whereYear($value)
 * @mixin \Eloquent
 */
class LeadCaptured extends Model
{
    protected $table = 'leads_captureds';

    /**
     * Os atributos que podem ser atribuídos em massa.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id', 
        'month', 
        'year', 
        'leads_captured'
    ];

    /**
     *  atributos que devem ser convertidos.
     */
    protected $casts = [
        'month' => 'integer',
        'year' => 'integer',
        'leads_captured' => 'integer'
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
     * Incrementa atomicamente via SQL — sem sobrescrever increment() do Eloquent
     */
    public function incrementLeads(int $amount = 1): void
    {
        static::where('id', $this->id)->increment('leads_captured', $amount);
        $this->leads_captured += $amount;
    }
}
