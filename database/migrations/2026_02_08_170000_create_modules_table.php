<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('modules', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('system_id')->constrained('systems')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->string('status', 16)->default('active')->index();
            $table->timestamps();

            $table->unique(['system_id', 'slug']);
            $table->index(['system_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};
