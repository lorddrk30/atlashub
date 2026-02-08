<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('resources', function (Blueprint $table): void {
            $table->id();
            $table->string('type', 32)->index();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('description')->nullable();
            $table->json('tags')->nullable();
            $table->json('urls')->nullable();
            $table->string('environment', 16)->nullable()->index();
            $table->foreignId('system_id')->nullable()->constrained('systems')->nullOnDelete();
            $table->foreignId('team_id')->nullable()->constrained('teams')->nullOnDelete();
            $table->string('source', 32)->default('manual')->index();
            $table->string('status', 16)->default('draft')->index();
            $table->string('visibility', 16)->default('internal')->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['type', 'status', 'updated_at']);
            $table->index(['system_id', 'team_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resources');
    }
};
