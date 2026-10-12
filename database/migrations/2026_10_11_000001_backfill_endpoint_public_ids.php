<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('endpoints')
            ->whereNull('public_id')
            ->select('id')
            ->orderBy('id')
            ->chunkById(200, function ($endpoints): void {
                foreach ($endpoints as $endpoint) {
                    DB::table('endpoints')
                        ->where('id', $endpoint->id)
                        ->whereNull('public_id')
                        ->update(['public_id' => (string) Str::ulid()]);
                }
            });
    }

    public function down(): void
    {
        // Public IDs may already be referenced by links and must remain stable.
    }
};
