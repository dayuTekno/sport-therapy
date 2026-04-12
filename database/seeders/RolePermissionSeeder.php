<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // reset cache permission
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // permissions
        $permissions = [
            'user.view',
            'user.create',
            'user.edit',
            'user.delete',
            'role.view',
            'role.create',
            'role.edit',
            'role.delete',
            'master-eselon.view',
            'master-eselon.create',
            'master-eselon.edit',
            'master-eselon.delete',
            'master-procedures.view',
            'master-procedures.create',
            'master-procedures.edit',
            'master-procedures.delete',
            'master-polyclinic.view',
            'master-polyclinic.create',
            'master-polyclinic.edit',
            'master-polyclinic.delete',
            'master-medicine.view',
            'master-medicine.create',
            'master-medicine.edit',
            'master-medicine.delete',
            'master-icd.view',
            'master-icd.create',
            'master-icd.edit',
            'master-icd.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        // roles
        $admin = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);

        $operator = Role::firstOrCreate([
            'name' => 'operator',
            'guard_name' => 'web',
        ]);

        $user = Role::firstOrCreate([
            'name' => 'user',
            'guard_name' => 'web',
        ]);

        // assign permission ke role
        $admin->givePermissionTo(Permission::all());
        $operator->givePermissionTo(['user.view']);
    }
}
