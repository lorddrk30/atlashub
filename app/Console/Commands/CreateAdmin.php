<?php

namespace App\Console\Commands;

use App\Models\User;
use Database\Seeders\AccessControlSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

class CreateAdmin extends Command
{
    protected $signature = 'atlashub:create-admin {email : Initial admin email} {name : Initial admin name}';

    protected $description = 'Securely provision an administrator and access-control roles';

    public function handle(): int
    {
        $email = Str::lower(trim((string) $this->argument('email')));
        $name = trim((string) $this->argument('name'));

        $identityValidator = Validator::make(compact('email', 'name'), [
            'email' => ['required', 'email', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
        ]);

        if ($identityValidator->fails()) {
            $this->error($identityValidator->errors()->first());

            return self::FAILURE;
        }

        $existing = User::query()->whereRaw('LOWER(email) = ?', [$email])->first();

        if ($existing) {
            if (! $existing->hasRole('admin')) {
                $this->error('The email belongs to an existing non-admin user; no changes were made.');

                return self::FAILURE;
            }

            $this->info('Administrator already exists; no credentials or roles were changed.');

            return self::SUCCESS;
        }

        $password = $this->secret('Admin password');
        $confirmation = $this->secret('Confirm admin password');
        $passwordValidator = Validator::make(['password' => $password, 'confirmation' => $confirmation], [
            'password' => ['required', Password::min(12)->mixedCase()->numbers()->symbols()],
            'confirmation' => ['required', 'same:password'],
        ]);

        if ($passwordValidator->fails()) {
            $this->error($passwordValidator->errors()->first());

            return self::FAILURE;
        }

        DB::transaction(function () use ($email, $name, $password): void {
            app(AccessControlSeeder::class)->run();

            $admin = User::query()->create([
                'email' => $email,
                'name' => $name,
                'password' => $password,
            ]);

            $admin->assignRole(Role::findByName('admin', 'web'));
        });

        $this->info('Administrator created. Change the password through account settings if needed.');

        return self::SUCCESS;
    }
}
