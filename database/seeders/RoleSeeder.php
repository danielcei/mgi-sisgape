<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        //Artisan::call('permissions:sync');
        //Artisan::call('permissions:sync -P');

        //Permission::create(['name' => 'access manage footer', 'guard_name' => 'web']);

        $roles = [
            ['id' => 1, 'name' => 'Admin', 'guard_name' => 'web', 'permissions' => [
                'view-any User',
                'view User',
                'create User',
                'update User',
                'delete User',
                'restore User',
                'force-delete User',
                'replicate User',
                'reorder User',
                'view-any Role',
                'view Role',
                'create Role',
                'update Role',
                'delete Role',
                'restore Role',
                'force-delete Role',
                'replicate Role',
                'reorder Role',
                'view-any Permission',
                'view Permission',
                'create Permission',
                'update Permission',
                'delete Permission',
                'restore Permission',
                'force-delete Permission',
                'replicate Permission',
                'reorder Permission',
                'audit',
                'restoreAudit',

            ]],
            ['id' => 2, 'name' => 'TI', 'guard_name' => 'web', 'permissions' => [


            ]],
            ['id' => 3, 'name' => 'BackOffice', 'guard_name' => 'web', 'permissions' => [


            ]],
        ];


        foreach ($roles as $role) {
            $roleFind = Role::updateOrCreate(
                ['id' => $role['id']],
                ['name' => $role['name'], 'guard_name' => $role['guard_name']]
            );

            if (isset($role['permissions'])) {
                foreach ($role['permissions'] as $permissionName) {
                    $permission = Permission::updateOrCreate(
                        [
                            'name' => $permissionName,
                            'guard_name' => 'web'

                        ]);
                    $roleFind->givePermissionTo($permission);
                }
            }
        }

        Artisan::call('cache:forget spatie.permission.cache');
    }
}
