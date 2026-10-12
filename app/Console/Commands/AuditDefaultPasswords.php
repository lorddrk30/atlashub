<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class AuditDefaultPasswords extends Command
{
    protected $signature = 'atlashub:audit-default-passwords';

    protected $description = 'Find accounts still using the known demonstration password';

    public function handle(): int
    {
        $affected = 0;

        User::query()->select(['id', 'email', 'password'])->chunkById(100, function ($users) use (&$affected): void {
            foreach ($users as $user) {
                if (! password_verify('password', $user->password)) {
                    continue;
                }

                $affected++;
                $this->warn("Password rotation required: {$user->email}");
            }
        });

        if ($affected > 0) {
            $this->error("{$affected} account(s) still use the demonstration password.");

            return self::FAILURE;
        }

        $this->info('No accounts use the demonstration password.');

        return self::SUCCESS;
    }
}
