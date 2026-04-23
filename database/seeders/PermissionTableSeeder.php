<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    public const PERMISSIONS = [
        'user-list',
        'user-create',
        'user-edit',
        'user-delete',
        'role-list',
        'role-create',
        'role-edit',
        'role-delete',
        'permission-list',
        'permission-create',
        'permission-edit',
        'permission-delete',
        'product-list',
        'product-create',
        'product-edit',
        'product-delete',
        'category-list',
        'category-create',
        'category-edit',
        'category-delete',
        'subcategory-list',
        'subcategory-create',
        'subcategory-edit',
        'subcategory-delete',
        'childcategory-list',
        'childcategory-create',
        'childcategory-edit',
        'childcategory-delete',
        'shipping-list',
        'shipping-create',
        'shipping-edit',
        'shipping-delete',
        'social-list',
        'social-create',
        'social-edit',
        'social-delete',
        'color-list',
        'color-create',
        'color-edit',
        'color-delete',
        'size-list',
        'size-create',
        'size-edit',
        'size-delete',
        'banner-category-list',
        'banner-category-create',
        'banner-category-edit',
        'banner-category-delete',
        'couponcode-list',
        'couponcode-create',
        'couponcode-edit',
        'couponcode-delete',
        'order-list',
        'order-create',
        'order-edit',
        'order-delete',
        'order-invoice',
        'order-process',
        'setting-list',
        'setting-create',
        'setting-edit',
        'setting-delete',
        'review-list',
        'review-create',
        'review-edit',
        'review-delete',
        'contact-list',
        'contact-create',
        'contact-edit',
        'contact-delete',
        'page-list',
        'page-create',
        'page-edit',
        'page-delete',
    ];

    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (self::PERMISSIONS as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
