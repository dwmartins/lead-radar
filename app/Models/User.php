<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
    const ROLE_USER = 'usuario';

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
}
