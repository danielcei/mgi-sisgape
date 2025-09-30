<?php

namespace App\Observers;

use App\Enums\Status;
use App\Models\User;
use App\Notifications\SendPasswordToNewUser;
use http\Env\Request;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        $this->syncRole($user);
    }

    public function creating(User $user): void
    {
        //
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        $this->syncRole($user);
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }

    /**
     * @param User $user
     * @return void
     */
    protected function syncRole(User $user): void
    {
        $roleId = (int)optional(optional(optional(request('components'))[0])['updates'])['data.role_id'];
        if (optional(auth())->user() && blank($roleId)) {
            $myRole = (int)optional(optional(optional(auth())->user())->roles())->first()->id ?? 0;

            abort_if($myRole <= 0, 403, 'Você não tem permissão para criar este usuário. [0]');
            abort_if($roleId < $myRole, 403, 'Você não tem permissão para criar este usuário. [1]');
            $user->roles()->sync([$roleId]);
        }
    }
}
