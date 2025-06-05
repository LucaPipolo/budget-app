<?php

declare(strict_types=1);

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property string $id
 * @property-read string $profile_photo_url
 */
class User extends Authenticatable implements FilamentUser, HasTenants, MustVerifyEmail
{
    use HasApiTokens;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;

    use HasProfilePhoto;
    use HasTeams;
    use HasUuids;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var list<string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter
     */
    public function getTenants(Panel $panel): Collection
    {
        return $this->allTeams();
    }

    public function canAccessTenant(Model $tenant): bool
    {
        return $this->belongsToTeam($tenant);
    }

    /**
     * Establish the user/accounts relationship.
     *
     * @return HasManyThrough The user/accounts relationship.
     */
    public function accounts(): HasManyThrough
    {
        return $this->hasManyThrough(
            Account::class,
            Team::class,
            'user_id',
            'team_id',
        )->whereHas('team', function (Builder $query): void {
            $query->whereIn('id', $this->allTeams()->pluck('id'));
        })->distinct();
    }

    /**
     * Establish the user/merchants relationship.
     *
     * @return HasManyThrough The user/merchants relationship.
     */
    public function merchants(): HasManyThrough
    {
        return $this->hasManyThrough(
            Merchant::class,
            Team::class,
            'user_id',
            'team_id',
        )->whereHas('team', function (Builder $query): void {
            $query->whereIn('id', $this->allTeams()->pluck('id'));
        })->distinct();
    }

    /**
     * Establish the user/categories relationship.
     *
     * @return HasManyThrough The user/categories relationship.
     */
    public function categories(): HasManyThrough
    {
        return $this->hasManyThrough(
            Category::class,
            Team::class,
            'user_id',
            'team_id',
        )->whereHas('team', function (Builder $query): void {
            $query->whereIn('id', $this->allTeams()->pluck('id'));
        })->distinct();
    }

    /**
     * Establish the user/tags relationship.
     *
     * @return HasManyThrough The user/tags relationship.
     */
    public function tags(): HasManyThrough
    {
        return $this->hasManyThrough(
            Tag::class,
            Team::class,
            'user_id',
            'team_id',
        )->whereHas('team', function (Builder $query): void {
            $query->whereIn('id', $this->allTeams()->pluck('id'));
        })->distinct();
    }

    /**
     * Establish the user/transactions relationship.
     *
     * @return HasManyThrough The user/transactions relationship.
     */
    public function transactions(): HasManyThrough
    {
        return $this->hasManyThrough(
            Transaction::class,
            Team::class,
            'user_id',
            'team_id',
        )->whereHas('team', function (Builder $query): void {
            $query->whereIn('id', $this->allTeams()->pluck('id'));
        })->distinct();
    }

    /**
     * The "booting" method of the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (User $user): void {
            $user->id = (string) Str::uuid7();
        });
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
