<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProjectsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $creator = User::where('role', 'super_admin')->first()
            ?? User::factory()->create(['role' => 'super_admin']);

        Project::factory()
            ->count(5)
            ->for($creator, 'creator')
            ->state(fn () => ['status' => 'open'])
            ->create();
    }
}
