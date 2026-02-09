<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('systems', function (Blueprint $table): void {
            $table->string('repository_url')->nullable()->after('user_areas');
        });

        DB::table('systems')
            ->whereNull('repository_url')
            ->whereNotNull('gitlab_url')
            ->orderBy('id')
            ->chunkById(100, function ($systems): void {
                foreach ($systems as $system) {
                    DB::table('systems')
                        ->where('id', $system->id)
                        ->update(['repository_url' => $system->gitlab_url]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('systems', function (Blueprint $table): void {
            $table->dropColumn('repository_url');
        });
    }
};
