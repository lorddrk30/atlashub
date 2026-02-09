<?php

namespace Database\Factories;

use App\Models\Document;
use App\Models\System;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Document>
 */
class DocumentFactory extends Factory
{
    protected $model = Document::class;

    public function definition(): array
    {
        return [
            'system_id' => System::factory(),
            'module_id' => null,
            'endpoint_id' => null,
            'title' => fake()->sentence(4),
            'description' => fake()->sentence(),
            'type' => fake()->randomElement(Document::TYPES),
            'file_path' => 'documents/manual-demo.pdf',
            'mime_type' => 'application/pdf',
            'size' => fake()->numberBetween(1024, 1000000),
            'uploaded_by' => User::factory(),
        ];
    }
}
