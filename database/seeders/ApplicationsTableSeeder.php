<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\Candidate;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;

class ApplicationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $projects = Project::all();
        $candidates = Candidate::all();
        $users = User::all();

        if ($projects->isEmpty() || $candidates->isEmpty() || $users->isEmpty()) {
            return;
        }

        Application::factory()
            ->count(30)
            ->state(fn () => [
                'project_id' => $projects->random()->id,
                'candidate_id' => $candidates->random()->id,
                'updated_by' => $users->random()->id,
            ])
            ->create();
    }
}
