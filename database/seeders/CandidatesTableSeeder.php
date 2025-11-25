<?php

namespace Database\Seeders;

use App\Models\Candidate;
use App\Models\User;
use Illuminate\Database\Seeder;

class CandidatesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $recruiterIds = User::role('recruiter')->pluck('id');

        if ($recruiterIds->isEmpty()) {
            $recruiter = User::factory()->create([
                'name' => 'Seeder Recruiter',
                'email' => 'recruiter+seed@lolo.test',
            ]);

            $recruiter->assignRole('recruiter');

            $recruiterIds = collect([$recruiter->id]);
        }

        Candidate::factory()
            ->count(20)
            ->state(fn () => [
                'created_by' => $recruiterIds->random(),
            ])
            ->create();
    }
}
