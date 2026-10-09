<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'clinic_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function clinic()
    {
        return $this->belongsTo(Clinic::class, 'clinic_id');
    }

    public function isSaasAdmin(): bool
    {
        return $this->hasRole('saas_admin') || ($this->clinic_id === null && $this->hasRole('admin'));
    }

    public function isClinicAdmin(): bool
    {
        return $this->hasRole('clinic_admin') || ($this->clinic_id !== null && $this->hasRole('admin'));
    }

    public function isClinicOperator(): bool
    {
        return $this->hasRole('operator') || ($this->clinic_id !== null && !$this->isClinicAdmin());
    }

    public function getClinicNameAttribute(): string
    {
        return $this->clinic?->name ?? 'Platform Global';
    }

    public function getRoleDisplayAttribute(): string
    {
        if ($this->isSaasAdmin()) {
            return 'Administrator SaaS';
        }
        if ($this->isClinicAdmin()) {
            return 'Administrator Klinik';
        }
        return 'Operator Klinik';
    }
}
