<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property string $place_id
 * @property string $name
 * @property string|null $address
 * @property string|null $phone
 * @property string|null $website
 * @property string|null $category
 * @property numeric|null $latitude
 * @property numeric|null $longitude
 * @property numeric|null $rating
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lead newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lead newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lead query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lead whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lead whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lead whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lead whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lead whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lead whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lead whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lead wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lead wherePlaceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lead whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lead whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lead whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lead whereWebsite($value)
 * @mixin \Eloquent
 */
class Lead extends Model
{
    protected $table = 'leads';

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
