<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class CreateAdminUserSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = Permission::pluck('name')->all();
        $superAdminRole = Role::firstOrCreate([
            'name' => 'Super Admin',
            'guard_name' => 'web',
        ]);
        $superAdminRole->syncPermissions($permissions);

        $user = User::firstOrCreate([
            'email' => 'admin@gmail.com',
        ], [
            'name' => 'Super Admin',
            'password' => Hash::make('123456'),
            'status' => 1,
        ]);

        $user->update([
            'status' => 1,
        ]);

        $user->syncRoles(['Super Admin']);
        $user->syncPermissions($permissions);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
