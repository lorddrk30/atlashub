<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('systems', function (Blueprint $table): void {
            $table->string('prod_server_ip', 45)->nullable()->after('prod_server');
            $table->string('uat_server_ip', 45)->nullable()->after('uat_server');
            $table->string('dev_server_ip', 45)->nullable()->after('dev_server');
        });
    }

    public function down(): void
    {
        Schema::table('systems', function (Blueprint $table): void {
            $table->dropColumn([
                'prod_server_ip',
                'uat_server_ip',
                'dev_server_ip',
            ]);
        });
    }
};
