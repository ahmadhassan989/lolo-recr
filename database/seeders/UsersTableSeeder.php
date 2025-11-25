<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'super_admin@lolo.test',
                'role' => 'super_admin',
            ],
            [
                'name' => 'HR Manager',
                'email' => 'hr@lolo.test',
                'role' => 'hr',
            ],
            [
                'name' => 'Recruiter Jane',
                'email' => 'recruiter@lolo.test',
                'role' => 'recruiter',
            ],
        ];

        foreach ($users as $userData) {
            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );

            $role = Role::findOrCreate($userData['role'], 'web');
            $user->syncRoles([$role]);

        }
    }
}
