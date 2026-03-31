<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'avatar',
        'profile_type',
        'document_number',
        'company_name',
        'birth_date',
        'phone',
        'phone_verified_at',
        'email_verified_at',
        'verification_code',
        'verification_code_expires_at',
        'onboarding_step',
        'mp_access_token',
        'mp_refresh_token',
        'mp_user_id',
        'mp_token_expires_at',
        'username',
        'bio',
        'website',
        'instagram',
        'whatsapp',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'verification_code',
        'mp_access_token',
        'mp_refresh_token',
    ];

    protected $attributes = [
        'onboarding_step' => 1,
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'verification_code_expires_at' => 'datetime',
            'birth_date' => 'date',
            'onboarding_step' => 'integer',
            'mp_token_expires_at' => 'datetime',
        ];
    }

    // --- Helpers de onboarding ---

    public function hasCompletedOnboarding(): bool
    {
        return $this->onboarding_step >= 4
            && $this->phone_verified_at !== null;
    }

    public function isEmailVerified(): bool
    {
        return $this->email_verified_at !== null;
    }

    public function isPhoneVerified(): bool
    {
        return $this->phone_verified_at !== null;
    }

    public function isCpf(): bool
    {
        return $this->profile_type === 'cpf';
    }

    public function isCnpj(): bool
    {
        return $this->profile_type === 'cnpj';
    }

    // Helpers — adicione ao final do model
    public function hasMpConnected(): bool
    {
        return !empty($this->mp_access_token);
    }

    public function isMpTokenValid(): bool
    {
        if (!$this->mp_token_expires_at) {
            return !empty($this->mp_access_token);
        }

        return $this->mp_token_expires_at->isFuture();
    }

    public function getProfileUrlAttribute(): string
    {
        if ($this->username) {
            return route('organizer.public', $this->username);
        }

        return route('organizer.public.id', $this->id);
    }
}