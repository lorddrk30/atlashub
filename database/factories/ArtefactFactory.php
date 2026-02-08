<?php

namespace Database\Factories;

use App\Models\Artefact;
use App\Models\Endpoint;
use App\Models\Module;
use App\Models\System;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArtefactFactory extends Factory
{
    protected $model = Artefact::class;

    public function definition(): array
    {
        $type = fake()->randomElement(Artefact::TYPES);

        return [
            'system_id' => System::factory(),
            'module_id' => Module::factory(),
            'endpoint_id' => Endpoint::factory(),
            'type' => $type,
            'title' => ucfirst($type).' '.fake()->word(),
            'url' => fake()->url(),
            'description' => fake()->sentence(),
            'metadata' => ['source' => 'manual'],
            'created_by' => User::factory(),
            'updated_by' => User::factory(),
        ];
    }
}
