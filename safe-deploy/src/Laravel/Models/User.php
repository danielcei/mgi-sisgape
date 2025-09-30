<?php

declare(strict_types=1);

namespace SafeDeploy\Laravel\Models;

use Exception;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Auditable as HasAudits;
use OwenIt\Auditing\Contracts\Auditable;
use SafeDeploy\Laravel\Concerns\ResetsTaggedCache;
use SafeDeploy\Laravel\Concerns\Userstamps;
use SafeDeploy\Laravel\Contracts\SetLocalePreference;
use SafeDeploy\Laravel\Contracts\Userstampable;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property ?string $name
 * @property ?string $email
 */
class User extends Authenticatable implements Auditable, FilamentUser, HasLocalePreference, SetLocalePreference, Userstampable
{
    use HasAudits;
    use HasRoles;
    use Notifiable;
    use ResetsTaggedCache;
    use SoftDeletes;
    use Userstamps;

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
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    /**
     * Get the user's preferred locale.
     */
    public function preferredLocale(): string
    {
        return app()->getLocale();
    }

    public function setLocalePreference(string $locale): void
    {
        throw new Exception('Not implemented');
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
