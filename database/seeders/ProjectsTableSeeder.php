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
        $creator = User::role('super_admin')->first();

        if (! $creator) {
            $creator = User::factory()->create([
                'name' => 'Seeder Super Admin',
                'email' => 'superadmin+seed@lolo.test',
            ]);

            $creator->assignRole('super_admin');
        }

        $teamLead = User::role('hr')->first();

        if (! $teamLead) {
            $teamLead = User::factory()->create([
                'name' => 'Seeder HR Lead',
                'email' => 'hr+lead@lolo.test',
            ]);
            $teamLead->assignRole('hr');
        }

        Project::factory()
            ->count(5)
            ->for($creator, 'creator')
            ->state(fn () => ['status' => 'open', 'team_lead_id' => $teamLead->id])
            ->create();
    }
}
