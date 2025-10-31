<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Candidate>
 */
class CandidateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'cv_file' => null,
            'linkedin_url' => fake()->optional()->url(),
            'skills' => implode(', ', fake()->words(fake()->numberBetween(3, 7))),
            'experience_years' => fake()->numberBetween(0, 15),
            'notes' => fake()->optional()->sentence(),
            'gender' => fake()->randomElement(['male', 'female', 'other']),
            'birth_date' => fake()->dateTimeBetween('-55 years', '-18 years'),
            'nationality' => fake()->country(),
            'education_level' => fake()->randomElement(['High School', 'Bachelor', 'Master', 'PhD']),
            'expected_salary' => fake()->randomFloat(2, 30000, 150000),
            'availability_date' => fake()->optional()->dateTimeBetween('now', '+90 days'),
            'source' => fake()->optional()->randomElement(['Referral', 'LinkedIn', 'Job Board', 'Career Fair', 'Company Website']),
            'rating' => fake()->numberBetween(0, 5),
            'status' => fake()->randomElement(['active', 'archived', 'blacklisted']),
            'created_by' => User::factory(),
        ];
    }
}
