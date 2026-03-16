<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property string $name
 * @property string $key
 * @property string $google_field
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Plan> $plans
 * @property-read int|null $plans_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feature newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feature newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feature query()
 * @mixin \Eloquent
 */
class Feature extends Model
{
    protected $table = 'features';

    /**
     * Os atributos que podem ser atribuídos em massa.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'key',
        'google_field'
    ];

    /**
     * Lista padrão de features do sistema
     */
    public const DEFAULT_FEATURES = [
        [
            'name' => 'Nome',
            'key' => 'name',
            'google_field' => 'displayName'
        ],
        [
            'name' => 'Endereço',
            'key' => 'address',
            'google_field' => 'formattedAddress'
        ],
        [
            'name' => 'Telefone',
            'key' => 'phone',
            'google_field' => 'nationalPhoneNumber'
        ],
        [
            'name' => 'Website',
            'key' => 'website',
            'google_field' => 'websiteUri'
        ],
        [
            'name' => 'Categoria',
            'key' => 'category',
            'google_field' => 'primaryType'
        ],
        [
            'name' => 'Rating',
            'key' => 'rating',
            'google_field' => 'rating'
        ],
        [
            'name' => 'Latitude',
            'key' => 'latitude',
            'google_field' => 'location.latitude'
        ],
        [
            'name' => 'Longitude',
            'key' => 'longitude',
            'google_field' => 'location.longitude'
        ],
        [
            'name' => 'Exportar Excel',
            'key' => 'export_excel',
            'google_field' => null
        ],
        [
            'name' => 'Exportar CSV',
            'key' => 'export_csv',
            'google_field' => null
        ],
    ];

    /**
     * Relações
     * 
     * Tabela pivô de Plans/Feature
     */
    public function plans(): BelongsToMany
    {
        return $this->belongsToMany(Plan::class, 'plan_features');
    }
}
