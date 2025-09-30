<?php

namespace App\Models;

use Althinect\FilamentSpatieRolesPermissions\Concerns\HasSuperAdmin;
use App\Enums\Status;
use App\Traits\CanCreatePassword;
use App\Traits\HasAuditColumns;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Jeffgreco13\FilamentBreezy\Traits\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Traits\HasRoles;


class User extends Authenticatable implements FilamentUser, HasAvatar, Auditable
{
    use HasApiTokens, SoftDeletes, HasFactory, Notifiable, TwoFactorAuthenticatable, HasRoles, HasSuperAdmin, HasAuditColumns, \OwenIt\Auditing\Auditable;

    public const int SYSTEM_USER = 1;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar_url',
        'cpf',
        'telephone',
        'client_id',
        'created_by',
        'updated_by',
        'status',
        'created_at',
        'updated_at',
        'proposal',
    ];

    protected $dates = [
        'deleted_at',
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'status' => Status::class
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo('App\Role');
    }


    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url ? Storage::url($this->avatar_url) : null;
    }

    #[\Override] public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }
    public function canEditUser(User $otherUser): bool
    {
        return $otherUser->role_id == $this->roles()->first()->id;
    }

    public function scopeIsActive($query)
    {
        return $query->where('status', 'active');
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->status->value === 'active';
    }
}
