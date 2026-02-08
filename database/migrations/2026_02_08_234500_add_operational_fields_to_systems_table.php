<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('systems', function (Blueprint $table): void {
            $table->string('prod_server')->nullable()->after('description');
            $table->string('uat_server')->nullable()->after('prod_server');
            $table->string('dev_server')->nullable()->after('uat_server');
            $table->string('internal_url')->nullable()->after('dev_server');
            $table->string('public_url')->nullable()->after('internal_url');
            $table->json('responsibles')->nullable()->after('public_url');
            $table->json('user_areas')->nullable()->after('responsibles');
            $table->string('gitlab_url')->nullable()->after('user_areas');
            $table->string('home_preview_url')->nullable()->after('gitlab_url');
        });
    }

    public function down(): void
    {
        Schema::table('systems', function (Blueprint $table): void {
            $table->dropColumn([
                'prod_server',
                'uat_server',
                'dev_server',
                'internal_url',
                'public_url',
                'responsibles',
                'user_areas',
                'gitlab_url',
                'home_preview_url',
            ]);
        });
    }
};
