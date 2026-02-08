<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('organization_settings', function (Blueprint $table): void {
            $table->renameColumn('logo_url', 'logo');
            $table->renameColumn('favicon_url', 'favicon');
        });
    }

    public function down(): void
    {
        Schema::table('organization_settings', function (Blueprint $table): void {
            $table->renameColumn('logo', 'logo_url');
            $table->renameColumn('favicon', 'favicon_url');
        });
    }
};
