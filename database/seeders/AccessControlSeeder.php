<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class AccessControlSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissionNames = [
            'system.manage',
            'module.manage',
            'endpoint.manage',
            'endpoint.publish',
            'artefact.manage',
            'document.view',
            'document.manage',
            'organization.manage',
            'user.manage',
            'role.manage',
            'logs.view',
            'logs.manage',
        ];

        foreach ($permissionNames as $permissionName) {
            Permission::query()->firstOrCreate(['name' => $permissionName, 'guard_name' => 'web']);
        }

        Role::query()->firstOrCreate(['name' => 'admin', 'guard_name' => 'web'])->syncPermissions($permissionNames);
        Role::query()->firstOrCreate(['name' => 'editor', 'guard_name' => 'web'])->syncPermissions([
            'system.manage', 'module.manage', 'endpoint.manage', 'endpoint.publish',
            'artefact.manage', 'document.view', 'document.manage', 'logs.view',
        ]);
        Role::query()->firstOrCreate(['name' => 'viewer', 'guard_name' => 'web'])->syncPermissions(['document.view']);
    }
}
