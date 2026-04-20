<?php

namespace App\Providers;

use Illuminate\Notifications\Notification;
use Illuminate\Auth\Passwords\DatabaseTokenRepository;
use Illuminate\Auth\Passwords\PasswordBroker as BasePasswordBroker;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Support\Str;

class PasswordBroker extends BasePasswordBroker
{
    public function sendResetLink(array $credentials)
    {
        $user = $this->getUser($credentials);

        if (is_null($user)) {
            return static::INVALID_USER;
        }

        if ($this->tokens->recentlyCreatedToken($user)) {
            return static::TOKEN_RENEWAL_DELAY;
        }

        $user->notify(new \App\Notifications\ResetPasswordNotification(
            $this->tokens->create($user)
        ));

        return static::RESET_LINK_SENT;
    }
}