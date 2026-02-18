<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [

            // ===== Package =====
            [
                'name' => 'View Packages',
                'key' => 'package.view',
                'group' => 'package',
                'description' => 'Can view package list and details'
            ],
            [
                'name' => 'Create Package',
                'key' => 'package.create',
                'group' => 'package',
                'description' => 'Can create new packages'
            ],
            [
                'name' => 'Update Package',
                'key' => 'package.update',
                'group' => 'package',
                'description' => 'Can update existing packages'
            ],
            [
                'name' => 'Delete Package',
                'key' => 'package.delete',
                'group' => 'package',
                'description' => 'Can delete packages'
            ],

            // ===== Package Features =====
            [
                'name' => 'Manage Package Features',
                'key' => 'package_feature.manage',
                'group' => 'package',
                'description' => 'Can manage package features (tests, meetings, certificates)'
            ],

            // ===== User Packages =====
            [
                'name' => 'View User Packages',
                'key' => 'user_package.view',
                'group' => 'user_package',
                'description' => 'Can view user purchased packages'
            ],
            [
                'name' => 'Manage User Packages',
                'key' => 'user_package.manage',
                'group' => 'user_package',
                'description' => 'Can manage user package status and expiry'
            ],

            // ===== Reports =====
            [
                'name' => 'View Reports',
                'key' => 'report.view',
                'group' => 'report',
                'description' => 'Can view reports and analytics'
            ],

            // ===== Access Control =====
            [
                'name' => 'Manage Roles',
                'key' => 'role.manage',
                'group' => 'access',
                'description' => 'Can create and manage roles'
            ],
            [
                'name' => 'Manage Permissions',
                'key' => 'permission.manage',
                'group' => 'access',
                'description' => 'Can create and manage permissions'
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['key' => $permission['key']],
                $permission
            );
        }

        $role = Role::updateOrCreate(
            ['slug' => 'administration'],
            [
                'name' => 'Administration',
                'description' => 'Administration role with all permissions',
            ]
        );

        //  Get all permission IDs
        $permissionIds = Permission::pluck('id')->toArray();

        //  Assign all permissions to this role
        $role->permissions()->sync($permissionIds);
    }
}
