<?php

namespace App\Providers;

use App\Auth\SecureUserProvider;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Opcodes\LogViewer\LogFile;
use Opcodes\LogViewer\LogFolder;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Auth::provider('atlashub-users', fn ($app, array $config) => new SecureUserProvider($app['hash'], $config['model']));

        Password::defaults(fn (): Password => Password::min(12)->mixedCase()->numbers()->symbols());

        Vite::prefetch(concurrency: 3);

        Gate::define('viewLogViewer', function (?User $user): bool {
            return $user?->can('logs.view') ?? false;
        });

        Gate::define('downloadLogFile', function (?User $user, LogFile $file): bool {
            return $user?->can('logs.view') ?? false;
        });

        Gate::define('downloadLogFolder', function (?User $user, LogFolder $folder): bool {
            return $user?->can('logs.view') ?? false;
        });

        Gate::define('deleteLogFile', function (?User $user, LogFile $file): bool {
            return $user?->can('logs.manage') ?? false;
        });

        Gate::define('deleteLogFolder', function (?User $user, LogFolder $folder): bool {
            return $user?->can('logs.manage') ?? false;
        });
    }
}
