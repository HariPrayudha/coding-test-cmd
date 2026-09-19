<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    public function isAnalyst(): bool
    {
        return $this->role === 'analyst';
    }

    public function isMarketing(): bool
    {
        return $this->role === 'marketing';
    }

    public function roleLabel(): string
    {
        return match ($this->role) {
            'marketing' => 'Marketing Officer',
            default => 'Credit Analyst',
        };
    }

    public function roleBadgeClasses(): string
    {
        return match ($this->role) {
            'marketing' => 'bg-blue-50 text-blue-800 border-blue-200/80',
            default => 'bg-amber-50 text-amber-900 border-amber-200/80',
        };
    }

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
}
