<?php

namespace Database\Seeders;

use App\Models\Job;
use Illuminate\Database\Seeder;

class JobSeeder extends Seeder
{
    public function run(): void
    {
        if (Job::count() === 0) {
            Job::factory()->count(8)->create();
        }
    }
}
