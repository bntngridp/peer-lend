<?php

namespace App\Notifications;

use App\Mail\ResetPasswordMail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Mail;

class LendFlowResetPasswordNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly string $token
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): void
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        $expireMinutes = config('auth.passwords.'.config('auth.defaults.passwords').'.expire', 60);

        Mail::to($notifiable->email)
            ->send(new ResetPasswordMail(
                user: $notifiable,
                url: $url,
                expireMinutes: $expireMinutes
            ));
    }
}
