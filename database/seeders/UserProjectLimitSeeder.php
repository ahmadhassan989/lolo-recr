<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserProjectLimitSeeder extends Seeder
{
    public function run(): void
    {
        $limits = [
            'hr@lolo.test' => 5,
            'recruiter@lolo.test' => 0,
        ];

        foreach ($limits as $email => $max) {
            $user = User::where('email', $email)->first();

            if (! $user) {
                continue;
            }

            $user->projectLimit()->updateOrCreate(
                ['user_id' => $user->id],
                ['max_projects' => $max]
            );
        }
    }
}
