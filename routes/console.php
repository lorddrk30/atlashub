<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('atlashub:reset {--without-seed : Limpia todo sin volver a sembrar} {--yes : Ejecuta sin confirmacion interactiva}', function (): int {
    $shouldContinue = (bool) $this->option('yes');

    if (! $shouldContinue) {
        $shouldContinue = $this->confirm(
            'Esto limpiara toda la base de datos local y ejecutara migraciones desde cero. Deseas continuar?',
            false
        );
    }

    if (! $shouldContinue) {
        $this->warn('Operacion cancelada.');

        return 0;
    }

    $this->call('migrate:fresh');

    if (! $this->option('without-seed')) {
        $this->call('db:seed');
    }

    $this->info('Base reiniciada correctamente.');
    $this->line('Siguiente paso recomendado: actualiza la configuracion de organizacion en /admin/organization-settings.');

    return 0;
})->purpose('Limpia la base local y vuelve a dejar AtlasHub listo para adaptarlo a otra organizacion.');
