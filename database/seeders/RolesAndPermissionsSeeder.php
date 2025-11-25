<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'projects.view',
            'projects.manage',
            'projects.create',
            'candidates.view',
            'candidates.manage',
            'applications.view',
            'applications.manage',
            'applications.update',
            'analytics.view',
            'offers.view',
            'offers.manage',
            'jobs.view',
            'jobs.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $rolePermissions = [
            'super_admin' => $permissions,
            'hr' => [
                'projects.view',
                'projects.create',
                'candidates.view',
                'candidates.manage',
                'applications.view',
                'applications.manage',
                'applications.update',
                'offers.view',
                'offers.manage',
                'jobs.view',
                'jobs.manage',
            ],
            'recruiter' => [
                'candidates.view',
                'applications.view',
                'applications.update',
                'offers.view',
                'offers.manage',
                'jobs.view',
            ],
        ];

        foreach ($rolePermissions as $role => $rolePermissionList) {
            Role::findOrCreate($role, 'web')->syncPermissions($rolePermissionList);
        }
    }
}
