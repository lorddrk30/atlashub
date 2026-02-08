<?php

namespace Database\Factories;

use App\Models\Endpoint;
use App\Models\Module;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EndpointFactory extends Factory
{
    protected $model = Endpoint::class;

    public function definition(): array
    {
        $method = fake()->randomElement(Endpoint::METHODS);
        $path = '/api/'.fake()->slug().'/'.fake()->word();

        return [
            'module_id' => Module::factory(),
            'name' => fake()->sentence(3),
            'method' => $method,
            'path' => $path,
            'description' => fake()->paragraph(),
            'parameters' => [
                ['name' => 'id', 'in' => 'path', 'required' => true, 'type' => 'integer', 'description' => 'Identificador'],
            ],
            'request_example' => ['headers' => ['Accept' => 'application/json']],
            'response_example' => ['status' => 'ok'],
            'authentication_type' => fake()->randomElement(Endpoint::AUTH_TYPES),
            'urls' => [
                'prod' => 'https://api.example.com'.$path,
                'uat' => 'https://api-uat.example.com'.$path,
                'dev' => 'https://api-dev.example.com'.$path,
            ],
            'status' => 'published',
            'created_by' => User::factory(),
            'updated_by' => User::factory(),
        ];
    }
}
