<?php

namespace Database\Factories;

use App\Models\Job;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class JobFactory extends Factory
{
    protected $model = Job::class;

    public function definition(): array
    {
        $employmentTypes = ['full_time', 'part_time', 'contract', 'internship'];
        $statuses = ['open', 'closed', 'draft'];

        $projectId = Project::query()->inRandomOrder()->value('id') ?? Project::factory()->create()->id;
        $creatorId = User::query()->inRandomOrder()->value('id') ?? User::factory()->create()->id;

        return [
            'title' => fake()->jobTitle(),
            'department' => fake()->randomElement(['Engineering', 'Marketing', 'Sales', 'Operations']),
            'project_id' => $projectId,
            'location' => fake()->city(),
            'description' => fake()->paragraph(4),
            'requirements' => fake()->paragraph(3),
            'skills' => implode(', ', fake()->words(5)),
            'employment_type' => fake()->randomElement($employmentTypes),
            'salary_range' => '$' . fake()->numberBetween(60, 150) . 'k',
            'deadline' => fake()->dateTimeBetween('+15 days', '+90 days'),
            'status' => fake()->randomElement($statuses),
            'created_by' => $creatorId,
        ];
    }
}
