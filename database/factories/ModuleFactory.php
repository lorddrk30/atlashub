<?php

namespace Database\Factories;

use App\Models\Module;
use App\Models\System;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ModuleFactory extends Factory
{
    protected $model = Module::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'system_id' => System::factory(),
            'name' => Str::title($name),
            'slug' => Str::slug($name),
            'description' => fake()->sentence(),
            'status' => 'active',
        ];
    }
}
