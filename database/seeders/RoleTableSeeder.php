<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleTableSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $allPermissions = PermissionTableSeeder::PERMISSIONS;
        $adminPermissions = array_values(array_diff($allPermissions, [
            'role-create',
            'role-edit',
            'role-delete',
            'permission-create',
            'permission-edit',
            'permission-delete',
        ]));
        $editorPermissions = [
            'product-list',
            'product-create',
            'product-edit',
            'category-list',
            'category-create',
            'category-edit',
            'subcategory-list',
            'subcategory-create',
            'subcategory-edit',
            'childcategory-list',
            'childcategory-create',
            'childcategory-edit',
            'order-list',
            'order-create',
            'order-edit',
            'order-invoice',
            'order-process',
            'couponcode-list',
            'couponcode-create',
            'couponcode-edit',
            'review-list',
            'review-create',
            'review-edit',
            'page-list',
            'page-create',
            'page-edit',
            'social-list',
            'social-create',
            'social-edit',
            'contact-list',
            'contact-create',
            'contact-edit',
        ];

        $roles = [
            'Super Admin' => $allPermissions,
            'Admin' => $adminPermissions,
            'Editor' => $editorPermissions,
        ];

        foreach ($roles as $roleName => $permissions) {
            $role = Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web',
            ]);

            $role->syncPermissions($permissions);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
