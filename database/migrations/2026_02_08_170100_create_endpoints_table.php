<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('endpoints', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('module_id')->constrained('modules')->cascadeOnDelete();
            $table->string('name');
            $table->string('method', 10)->index();
            $table->string('path')->index();
            $table->text('description')->nullable();
            $table->json('parameters')->nullable();
            $table->json('request_example')->nullable();
            $table->json('response_example')->nullable();
            $table->string('authentication_type', 24)->default('none')->index();
            $table->json('urls')->nullable();
            $table->string('status', 16)->default('published')->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['module_id', 'method', 'path']);
            $table->index(['module_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('endpoints');
    }
};
