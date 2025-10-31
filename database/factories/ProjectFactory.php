<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->jobTitle(),
            'department' => fake()->randomElement(['Engineering', 'Marketing', 'Sales', 'Operations', 'Human Resources']),
            'location' => fake()->city(),
            'description' => fake()->paragraph(3),
            'status' => fake()->randomElement(['open', 'closed']),
            'created_by' => User::factory(),
        ];
    }
}
