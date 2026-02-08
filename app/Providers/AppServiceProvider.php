<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
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
