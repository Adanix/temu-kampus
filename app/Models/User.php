<?php

namespace App\Models;

use App\Enums\DemoPersona;
use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'campus_unit', 'student_staff_code', 'preferred_locale'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

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
            'role' => UserRole::class,
        ];
    }

    public function itemReports(): HasMany
    {
        return $this->hasMany(ItemReport::class, 'reporter_id');
    }

    public function claims(): HasMany
    {
        return $this->hasMany(ItemClaim::class, 'claimant_id');
    }

    public function notificationLogs(): HasMany
    {
        return $this->hasMany(NotificationLog::class, 'user_id');
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function isCustodian(): bool
    {
        return $this->role === UserRole::Custodian;
    }

    public function isCustodianOrAdmin(): bool
    {
        return in_array($this->role, [UserRole::Custodian, UserRole::Admin], true);
    }

    public function initials(): string
    {
        $configured = config("demo.personas.{$this->role?->value}.initials");
        if (is_string($configured) && $configured !== '') {
            return $configured;
        }

        $parts = preg_split('/\s+/', trim($this->name)) ?: [];
        $letters = '';
        foreach (array_slice($parts, 0, 2) as $part) {
            if ($part !== '') {
                $letters .= mb_strtoupper(mb_substr($part, 0, 1));
            }
        }

        return $letters !== '' ? $letters : 'TK';
    }

    public function defaultLandingRoute(): string
    {
        $roleValue = $this->role?->value ?? UserRole::Student->value;

        return DemoPersona::from($roleValue)->landingRoute();
    }
}
