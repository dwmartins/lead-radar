<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class LeadUsage
 * 
 * @property int     $id
 */
class LeadUsage extends Model
{
    /**
     * Os atributos que podem ser atribuídos em massa.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id', 
        'month', 
        'year', 
        'leads_used'
    ];

    /**
     *  atributos que devem ser convertidos.
     */
    protected $casts = [
        'month' => 'integer',
        'year' => 'integer',
        'leads_used' => 'integer'
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
        static::where('id', $this->id)->increment('leads_used', $amount);
        $this->leads_used += $amount;
    }
}
