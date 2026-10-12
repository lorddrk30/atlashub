<?php

use Illuminate\Support\Facades\Route;

it('does not expose database connection settings to an anonymous visitor', function (): void {
    $original = config('database.connections.sqlite');
    config()->set('database.connections.sqlite.host', 'secret-db.internal.example');
    config()->set('database.connections.sqlite.port', '6543');
    config()->set('database.connections.sqlite.database', 'private_atlashub_data');

    Route::get('/simulate-database-outage', fn () => throw new RuntimeException('Connection refused'));

    try {
        $this->get('/simulate-database-outage')->assertServiceUnavailable()
            ->assertDontSee('secret-db.internal.example')
            ->assertDontSee('6543')
            ->assertDontSee('private_atlashub_data')
            ->assertDontSee('DB_HOST');
    } finally {
        config()->set('database.connections.sqlite', $original);
    }
});
