<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $resetUrl;
    public $expires;

    public function __construct($user, $resetUrl)
    {
        $this->user = $user;
        $this->resetUrl = $resetUrl;
        $this->expires = config('auth.passwords.' . config('auth.defaults.passwords') . '.expire', 60);
    }

    public function build()
    {
        return $this->subject('🔐 Restablecimiento de Contraseña - Universidad Nacional Agraria de la Selva')
            ->markdown('emails.password-reset');
    }
}