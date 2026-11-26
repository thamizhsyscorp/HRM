<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
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
        ];
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }

    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class);
    }

    public function createdPayrollRuns(): HasMany
    {
        return $this->hasMany(PayrollRun::class, 'created_by');
    }

    public function approvedPayrollRuns(): HasMany
    {
        return $this->hasMany(PayrollRun::class, 'approved_by');
    }

    public function hasRole(string $slugOrName): bool
    {
        $clean = strtolower(trim($slugOrName));
        $slug = \Illuminate\Support\Str::slug($slugOrName);

        return $this->roles()->where(function ($q) use ($clean, $slug, $slugOrName) {
            $q->where('slug', $slug)
              ->orWhere('slug', $slugOrName)
              ->orWhereRaw('LOWER(name) = ?', [$clean])
              ->orWhereRaw('LOWER(slug) = ?', [$clean]);
        })->exists();
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super-admin');
    }

    public function hasPermission(string $slug): bool
    {
        return $this->roles()->whereHas('permissions', fn ($query) => $query->where('slug', $slug))->exists();
    }
}
