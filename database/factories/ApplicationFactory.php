<?php

namespace Database\Factories;

use App\Models\Candidate;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Application>
 */
class ApplicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'candidate_id' => Candidate::factory(),
            'status' => fake()->randomElement(['applied', 'screening', 'interview', 'offer', 'hired', 'rejected']),
            'notes' => fake()->optional()->sentence(),
            'updated_by' => null,
        ];
    }
}
