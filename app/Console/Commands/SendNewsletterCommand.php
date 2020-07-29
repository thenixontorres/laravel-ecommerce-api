<?php

namespace App\Console\Commands;

use App\Notifications\NewsletterNotification;
use App\User;
use Illuminate\Console\Command;

class SendNewsletterCommand extends Command
{
    protected $signature = 'send:newsletter {emails?*}';

    protected $description = 'Envia un correo electronico a todos los usuarios que hayan verificado su cuenta';

    public function handle()
    {
        $userEmails = $this->argument('emails');

        $builder = User::query();

        if ($userEmails) {
            $builder->whereIn('email', $userEmails);
        }

        $builder->whereNotNull('email_verified_at');

        if ($count = $builder->count()) {
            $this->info("Se enviaran {$count} correos");

            if ($this->confirm('¿Estas de acuerdo?')) {
                $builder->each(function (User $user) {
                    $user->notify(new NewsletterNotification());
                });

                $this->info('Correos enviados');
                return;
            }
        }

        $this->info('No se enviaron correos');
    }
}
