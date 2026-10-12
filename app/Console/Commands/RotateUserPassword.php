<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class RotateUserPassword extends Command
{
    protected $signature = 'atlashub:rotate-password {email : Account email}';

    protected $description = 'Rotate an account password using hidden interactive prompts';

    public function handle(): int
    {
        $email = Str::lower(trim((string) $this->argument('email')));
        $user = User::query()->whereRaw('LOWER(email) = ?', [$email])->first();

        if (! $user) {
            $this->error('Account not found; no changes were made.');

            return self::FAILURE;
        }

        $password = $this->secret('New password');
        $confirmation = $this->secret('Confirm new password');
        $validator = Validator::make(['password' => $password, 'confirmation' => $confirmation], [
            'password' => ['required', Password::min(12)->mixedCase()->numbers()->symbols()],
            'confirmation' => ['required', 'same:password'],
        ]);

        if ($validator->fails()) {
            $this->error($validator->errors()->first());

            return self::FAILURE;
        }

        DB::transaction(function () use ($user, $password): void {
            $user->password = $password;
            $user->remember_token = Str::random(60);
            $user->save();
            $user->tokens()->delete();

            if (config('session.driver') === 'database') {
                DB::table(config('session.table', 'sessions'))->where('user_id', $user->id)->delete();
            }
        });

        $this->info('Password rotated and personal access tokens revoked.');

        if (config('session.driver') !== 'database') {
            $this->warn('Invalidate active sessions in the configured session store before reopening the site.');
        }

        return self::SUCCESS;
    }
}
