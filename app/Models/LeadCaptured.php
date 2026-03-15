<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class LeadCaptured
 *
 * @property int $id
 * @property int $user_id
 * @property int $month
 * @property int $year
 * @property int $leads_captured
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
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

    /**
     * Relações
     * 
     * Relação com usuário.
     */
    public function user(): BelongsTo
    { 
        return $this->belongsTo(User::class); 
    }

    /**
     * Métodos
     * 
     * Incrementa atomicamente via SQL — sem sobrescrever increment() do Eloquent
     */
    public function incrementLeads(int $amount = 1): void
    {
        static::where('id', $this->id)->increment('leads_captured', $amount);
        $this->leads_captured += $amount;
    }
}
