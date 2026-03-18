<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;

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

    const ROLE_ADMIN = 'admin';
    const ROLE_USER  = 'user';

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

    /*
    |--------------------------------------------------------------------------
    | RELAÇÕES
    |--------------------------------------------------------------------------
    */

    /**
     * Relação com leads
     */
    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    /**
     * Relação com Subscription
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Relação com SearchUsage
     */
    public function searchUsages(): HasMany
    {
        return $this->hasMany(SearchUsage::class);
    }

    /**
     * Relação com LeadCaptured
     */
    public function leadCaptured(): HasMany
    {
        return $this->hasMany(LeadCaptured::class);
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /**
     * Filtra por usuários ativos
     * 
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     */
    public function scopeActive($query): Builder
    {
        return $query->where('account_status', true);
    }

    /*
    |--------------------------------------------------------------------------
    | ATTRIBUTES
    |--------------------------------------------------------------------------
    */

    /**
     * Retorna o nome e sobrenome concatenado.
     */
    public function getFullNameAttribute(): string
    {
        return trim("{$this->name} " . ($this->last_name ?? ''));
    }

    /**
     * Retorna a url completa para a imagem do usuário.
     */
    public function getAvatarUrlAttribute(): ?string
    {
        if ($this->avatar) {
            return asset("storage/images/avatars/{$this->avatar}");
        }

        $initials = collect(explode(' ', $this->full_name))
            ->map(fn($w) => strtoupper($w[0]))
            ->take(2)
            ->join('');

        return "https://ui-avatars.com/api/?name={$initials}&background=e2e8f0&color=334155&size=128";
    }

    /**
     * Mantém compatibilidade com $user->plan
     */
    public function getPlanAttribute(): ?Plan
    {
        return $this->currentPlan();
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    public function isActive(): bool
    {
        return $this->account_status;
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isUser(): bool
    {
        return $this->role === self::ROLE_USER;
    }

    public function updateLastLogin(): void
    {
        $this->timestamps = false;
        $this->last_login_at = now();
        $this->save();
        $this->timestamps = true;
    }

    public function deleteAvatar(): void
    {
        if ($this->avatar && Storage::disk('public')->exists("images/avatars/{$this->avatar}")) {
            Storage::disk('public')->delete("images/avatars/{$this->avatar}");
        }
    }

    /*
    |--------------------------------------------------------------------------
    | SUBSCRIPTION / PLAN
    |--------------------------------------------------------------------------
    */

    /**
     * Retorna a assinatura ativa do usuário.
     */
    public function activeSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class)
            ->whereIn('status', [
                Subscription::STATUS_ACTIVE,
                Subscription::STATUS_TRIAL
            ])
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            })
            ->latest();
    }

    /**
     * Retorna a subscription ativa.
     */
    public function getActiveSubscription(): ?Subscription
    {
        return $this->activeSubscription()->first();
    }

    /**
     * Retorna o plano atual do usuário.
     */
    public function currentPlan(): ?Plan
    {
        return $this->getActiveSubscription()?->plan;
    }

    /**
     * Verifica se possui subscription ativa válida.
     */
    public function hasActiveSubscription(): bool
    {
        return $this->getActiveSubscription()?->isValid() ?? false;
    }

    /*
    |--------------------------------------------------------------------------
    | USAGE
    |--------------------------------------------------------------------------
    */

     /**
     * Retorna o registro de uso (buscas e leads) do usuário no mês atual.
     *
     * Busca na relação `searchUsages` o registro correspondente
     * ao mês e ano atuais.
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
     */
    public function searchesUsedThisMonth(): int
    {
        return $this->currentMonthUsage()?->searches_used ?? 0;
    }

    /**
     * Retorna a quantidade de leads capturados pelo usuário no mês atual (Ranking/Estatística).
     */
    public function leadsCapturedThisMonth(): int
    {
        return $this->leadCaptured()
            ->where('month', now()->month)
            ->where('year', now()->year)
            ->value('leads_captured') ?? 0;
    }

    /**
     * Retorna o limite mensal de buscas permitido pelo plano do usuário.
     */
    public function monthlySearchLimit(): int
    {
        return $this->currentPlan()?->monthly_search_limit ?? 0;
    }

    /**
     * Retorna a quantidade de buscas restantes disponíveis no mês atual.
     * Garante que o valor nunca seja negativo.
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
        return $this->hasActiveSubscription()
            && $this->remainingSearches() > 0;
    }
}