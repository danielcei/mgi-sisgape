<?php

namespace App\Notifications;

use Filament\Facades\Filament;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;

class SendPasswordToNewUser extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {

        $token = app('auth.password.broker')->createToken($notifiable);
        $url = Filament::getResetPasswordUrl($token, $notifiable);

        return (new MailMessage)
            ->subject(Lang::get('Bem-vindo ao MPS!'))
            ->line(Lang::get('Você foi convidado para ter acesso exclusivo ao nosso sistema. Estamos entusiasmados em compartilhar com você as ferramentas'))
            ->line(Lang::get('Clique no link abaixo para concluir seu cadastro e iniciar sua jornada conosco:'))
            ->action(Lang::get('Criar Senha'), $url)
            ->line(Lang::get('This password reset link will expire in :count minutes.', ['count' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire')]))
            ->line(Lang::get('If you did not request a password reset, no further action is required.'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
