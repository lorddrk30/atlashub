<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SystemFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'name' => Str::title($name),
            'slug' => Str::slug($name),
            'status' => 'published',
            'description' => fake()->sentence(),
            'prod_server' => fake()->optional()->domainName(),
            'prod_server_ip' => fake()->optional()->ipv4(),
            'uat_server' => fake()->optional()->domainName(),
            'uat_server_ip' => fake()->optional()->ipv4(),
            'dev_server' => fake()->optional()->domainName(),
            'dev_server_ip' => fake()->optional()->ipv4(),
            'internal_url' => fake()->optional()->url(),
            'public_url' => fake()->optional()->url(),
            'responsibles' => fake()->optional()->randomElements(
                ['Plataforma', 'Arquitectura', 'DevOps', 'Backend Squad', 'Frontend Squad'],
                rand(1, 3)
            ),
            'user_areas' => fake()->optional()->randomElements(
                ['Operaciones', 'Comercial', 'Atencion a cliente', 'Finanzas', 'Producto'],
                rand(1, 3)
            ),
            'repository_url' => fake()->optional()->url(),
            'home_preview_url' => fake()->optional()->imageUrl(1280, 720, 'business'),
        ];
    }
}
