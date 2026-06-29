<?php

namespace App\Notifications;

use App\Models\PendingUser;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerifyPendingEmail extends Notification
{
    use Queueable;

    protected $pendingUser;

    public function __construct(PendingUser $pendingUser)
    {
        $this->pendingUser = $pendingUser;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $url = route('pending-verification.verify', $this->pendingUser->token);

        return (new MailMessage)
            ->subject('Verifica tu correo electrónico - SGD UNAS')
            ->greeting('¡Hola ' . $this->pendingUser->primer_nombre . '!')
            ->line('Gracias por registrarte en el Sistema de Gestión de Docencia de la Universidad Nacional Agraria de la Selva.')
            ->line('Para completar tu registro, por favor verifica tu correo electrónico haciendo clic en el botón:')
            ->action('Verificar Correo', $url)
            ->line('Este enlace expirará en 24 horas.')
            ->line('Si no creaste esta cuenta, puedes ignorar este mensaje.')
            ->salutation('Atentamente, SGD UNAS');
    }
}
