<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int $id
 * @property string $name
 * @property string $last_name
 * @property string $email
 * @property string|null $phone
 * @property string $password
 * @property string $role
 * @property bool $account_status
 * @property int|null $plan_id
 * @property string|null $avatar
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string|null $last_login_at
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string|null $avatar_url
 * @property-read string $full_name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LeadCaptured> $leadCaptured
 * @property-read int|null $lead_captured_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Lead> $leads
 * @property-read int|null $leads_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\Plan|null $plan
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SearchUsage> $searchUsages
 * @property-read int|null $search_usages_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User active()
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAccountStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLastLoginAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePlanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';

    /**
     * Os atributos que podem ser atribuídos em massa.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'last_name',
        'email',
        'phone',
        'avatar',
    ];

    /**
     * Os atributos que devem ser ocultados durante a serialização.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = [
        'full_name', 
        'avatar_url'
    ];

    const ROLE_ADMIN   = 'admin';
    const ROLE_USER = 'user';

    /**
     * Obtenha os atributos que devem ser convertidos.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'account_status' => 'boolean',
        ];
    }

    /**
     * Relações
     * 
     * Relação com leads
     */
    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    /**
     * Relação com Plan
     */
    public function plan(): BelongsTo
    { 
        return $this->belongsTo(Plan::class); 
    }

    /**
     * Relação com SearchUsage
     */
    public function searchUsages(): HasMany
    { 
        return $this->hasMany(SearchUsage::class); 
    }

    /**
     * Relação com LeadUsage
     */
    public function leadCaptured(): HasMany
    { 
        return $this->hasMany(LeadCaptured::class); 
    }

    /**
     * Scopes
     * 
     * Filtra por usuários ativos
     * 
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('account_status', true);
    }

    /**
     * Atributos
     * 
     * Retorna o nome e sobrenome concatenado.
     * @return string
     */
    public function getFullNameAttribute(): string
    {
        return trim("{$this->name} " . ($this->last_name ?? ''));
    }

    /**
     * Retorna a url completa para a imagem do usuário.
     * @return string|null
     */
    public function getAvatarUrlAttribute(): string|null
    {
        if($this->avatar) {
            return asset("storage/images/avatars/{$this->avatar}");
        }

        $initials = collect(explode(' ', $this->full_name))
            ->map(fn($w) => strtoupper($w[0]))
            ->take(2)
            ->join('');

        return "https://ui-avatars.com/api/?name={$initials}&background=e2e8f0&color=334155&size=128";
    }

    /**
     * Métodos
     * 
     * Verifica se o usuário está ativo.
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->account_status;
    }

    /**
     * Verifica se é um usuário Administrador.
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * Verifica se é um usuário Administrador.
     * @return bool
     */
    public function isUser(): bool
    {
        return $this->role === self::ROLE_USER;
    }

    /**
     * Retorna o nome e sobrenome concatenado.
     * 
     * @return string
     */
    public function getFullName(): string
    {
        return trim("{$this->name} " . ($this->last_name ?? ''));
    }

    /**
     * Atualiza a o dia e hora do ultimo login.
     * @return void
     */
    public function updateLastLogin(): void
    {
        $this->timestamps = false;
        $this->last_login_at = now();
        $this->save();
        $this->timestamps = true;
    }

    /**
     * Exclui a foto de avatar do usuário.
     * @return void
     */
    public function deleteAvatar(): void
    {
        if ($this->avatar && Storage::disk('public')->exists("images/avatars/{$this->avatar}")) {
            Storage::disk('public')->delete("images/avatars/{$this->avatar}");
        }
    }

    /**
     * Retorna o registro de uso (buscas e leads) do usuário no mês atual.
     *
     * Busca na relação `searchUsages` o registro correspondente
     * ao mês e ano atuais.
     *
     * @return SearchUsage|null Registro de uso do mês atual ou null se não existir.
     */
    public function currentMonthUsage(): ?SearchUsage
    {
        return $this->searchUsages()
            ->where('month', now()->month)
            ->where('year', now()->year)
            ->first();
    }

    /**
     * Retorna a quantidade de buscas realizadas pelo usuário no mês atual (Limitação do Plano).
     *
     * @return int Quantidade de buscas feitas.
     */
    public function searchesUsedThisMonth(): int
    {
        return $this->currentMonthUsage()?->searches_used ?? 0;
    }

    /**
     * Retorna a quantidade de leads capturados pelo usuário no mês atual (Ranking/Estatística).
     *
     * @return int Quantidade de leads capturados.
     */
    public function leadsCapturedThisMonth(): ?int
    {
        return $this->leadCaptured()
            ->where('month', now()->month)
            ->where('year', now()->year)
            ->first();
    }

    /**
     * Retorna o limite mensal de buscas permitido pelo plano do usuário.
     *
     * @return int Limite mensal de buscas do plano.
     */
    public function monthlySearchLimit(): int
    {
        return $this->plan?->monthly_search_limit ?? 0;
    }

    /**
     * Retorna a quantidade de buscas restantes disponíveis no mês atual.
     *
     * Garante que o valor nunca seja negativo.
     *
     * @return int Quantidade de buscas ainda disponíveis.
     */
    public function remainingSearches(): int
    {
        return max(0, $this->monthlySearchLimit() - $this->searchesUsedThisMonth());
    }

    /**
     * Verifica se o usuário ainda pode realizar novas buscas.
     *
     * O usuário pode buscar apenas se:
     * - possuir um plano ativo
     * - ainda tiver buscas disponíveis no limite mensal
     *
     * @return bool True se puder buscar, caso contrário false.
     */
    public function canPerformSearch(): bool
    {
        return $this->plan && $this->remainingSearches() > 0;
    }
}
