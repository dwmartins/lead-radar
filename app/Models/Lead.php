<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Lead
 * 
 * @property int     $id
 */
class Lead extends Model
{
    /**
     * Os atributos que podem ser atribuídos em massa.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'place_id',
        'name',
        'address',
        'phone',
        'website',
        'category',
        'latitude',
        'longitude',
        'rating'
    ];

    /**
     *  atributos que devem ser convertidos.
     */
    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'rating' => 'decimal:1'
    ];

    /**
     * Relações
     * 
     * Relações com usuário.
     */
    public function user(): BelongsTo 
    { 
        return $this->belongsTo(User::class); 
    }

    /**
     * Métodos
     * 
     * Verifica se já existe um registro para um usuário com o place_id informado.
     * Usado para evitar duplicação de registros para o mesmo usuário.
     * 
     * @param int $user_id ID do usuário.
     * @param string $place_id ID do local (ex: Google Places).
     * @return bool Retorna true se o registro existir, caso contrário false.
     */
    public static function existsForUser(int $user_id, string $place_id): bool
    {
        return static::where('user_id', $user_id)->where('place_id', $user_id)->exists();
    }
}
