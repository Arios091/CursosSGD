<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CleanExpiredPendingUsers extends Command
{
    protected $signature = 'pending-users:clean';

    protected $description = 'Elimina registros temporales de usuarios que no verificaron su correo en 24 horas';

    public function handle()
    {
        $deleted = \App\Models\PendingUser::where('expires_at', '<', now())->delete();

        $this->info("Se eliminaron {$deleted} registros temporales expirados.");

        return 0;
    }
}
