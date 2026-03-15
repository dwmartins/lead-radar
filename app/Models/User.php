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
 * Class User
 * 
 * @property int $id
 * @property string $name
 * @property string $last_name
 * @property string $email
 * @property string $password
 * @property string $role ['admin', 'user']
 * @property bool $account_status
 * @property int|null $plan_id
 * @property string|null $phone
 * @property string|null $avatar
 * @property \Illuminate\Support\Carbon|null $last_login_at
 */
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

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

    public function plan(): BelongsTo
    { 
        return $this->belongsTo(Plan::class); 
    }

    /**
     * Relação com LeadUsage
     */
    public function leadUsages(): HasMany
    { 
        return $this->hasMany(LeadUsage::class); 
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
     * Retorna o registro de uso de leads do usuário no mês atual.
     *
     * Busca na relação `leadUsages` o registro correspondente
     * ao mês e ano atuais.
     *
     * @return LeadUsage|null Registro de uso do mês atual ou null se não existir.
     */
    public function currentMonthUsage(): ?LeadUsage
    {
        return $this->leadUsages()
            ->where('month', now()->month)
            ->where('year', now()->year)
            ->first();
    }

    /**
     * Retorna a quantidade de leads utilizados pelo usuário no mês atual.
     *
     * @return int Quantidade de leads usados no mês atual.
     */
    public function leadsUsedThisMonth(): int
    {
        return $this->currentMonthUsage()?->leads_used ?? 0;
    }

    /**
     * Retorna o limite mensal de leads permitido pelo plano do usuário.
     *
     * @return int Limite mensal de leads do plano.
     */
    public function monthlyLeadsLimit(): int
    {
        return $this->plan?->monthly_leads_limit ?? 0;
    }

    /**
     * Retorna a quantidade de leads restantes disponíveis no mês atual.
     *
     * Garante que o valor nunca seja negativo.
     *
     * @return int Quantidade de leads ainda disponíveis.
     */
    public function remainingLeads(): int
    {
        return max(0, $this->monthlyLeadsLimit() - $this->leadsUsedThisMonth());
    }

    /**
     * Verifica se o usuário ainda pode capturar novos leads.
     *
     * O usuário pode capturar leads apenas se:
     * - possuir um plano ativo
     * - ainda tiver leads disponíveis no limite mensal
     *
     * @return bool True se puder capturar leads, caso contrário false.
     */
    public function canCaptureLeads(): bool
    {
        return $this->plan && $this->remainingLeads() > 0;
    }
}
