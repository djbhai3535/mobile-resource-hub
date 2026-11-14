<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Define all system permissions
        $permissionsByModule = [
            'posts' => [
                'posts.view' => 'View Posts',
                'posts.create' => 'Create Posts',
                'posts.edit' => 'Edit Any Post',
                'posts.publish' => 'Publish Posts',
                'posts.delete' => 'Delete Posts',
            ],
            'taxonomy' => [
                'categories.manage' => 'Manage Categories',
                'tags.manage' => 'Manage Tags',
            ],
            'catalog' => [
                'brands.manage' => 'Manage Mobile Brands',
                'devices.manage' => 'Manage Mobile Devices & Specifications',
                'specifications.manage' => 'Manage Dynamic Specification Groups & Fields',
            ],
            'downloads' => [
                'firmware.manage' => 'Manage Firmware & Stock ROM Releases',
                'flash-files.manage' => 'Manage Flash Files & Packages',
                'software.manage' => 'Manage Flashtools & Utility Software',
                'drivers.manage' => 'Manage USB Drivers & Packages',
            ],
            'pages' => [
                'pages.manage' => 'Manage Static CMS Pages',
            ],
            'media' => [
                'media.manage' => 'Manage Media Library',
            ],
            'messages' => [
                'messages.manage' => 'View & Manage Contact Inquiries',
            ],
            'menus' => [
                'menus.manage' => 'Manage Navigation Menus',
            ],
            'logs' => [
                'activity.view' => 'View Administrative Activity Logs',
            ],
            'settings' => [
                'settings.manage' => 'Manage System Settings',
            ],
        ];

        $createdPermissions = [];
        foreach ($permissionsByModule as $module => $permissions) {
            foreach ($permissions as $name => $displayName) {
                $createdPermissions[$name] = Permission::firstOrCreate(
                    ['name' => $name],
                    ['display_name' => $displayName, 'module' => $module]
                );
            }
        }

        // 1. Super Admin Role
        $superAdmin = Role::firstOrCreate(
            ['name' => 'super-admin'],
            [
                'display_name' => 'Super Administrator',
                'description' => 'Complete and unrestricted administrative access to the entire portal.',
            ]
        );
        $superAdmin->permissions()->sync(array_values(array_map(fn($p) => $p->id, $createdPermissions)));

        // 2. Administrator Role
        $admin = Role::firstOrCreate(
            ['name' => 'admin'],
            [
                'display_name' => 'Administrator',
                'description' => 'Manages website content, taxonomy, media, menus, contact messages, and activity audit.',
            ]
        );
        $adminPermissions = [
            'posts.view', 'posts.create', 'posts.edit', 'posts.publish', 'posts.delete',
            'brands.manage', 'devices.manage', 'specifications.manage',
            'firmware.manage', 'flash-files.manage', 'software.manage', 'drivers.manage',
            'categories.manage', 'tags.manage',
            'pages.manage',
            'media.manage',
            'messages.manage',
            'menus.manage',
            'activity.view',
        ];
        $admin->permissions()->sync(
            Permission::whereIn('name', $adminPermissions)->pluck('id')->toArray()
        );

        // 3. Editor Role
        $editor = Role::firstOrCreate(
            ['name' => 'editor'],
            [
                'display_name' => 'Senior Content Editor',
                'description' => 'Manages, edits, reviews, and publishes editorial posts and categories.',
            ]
        );
        $editorPermissions = [
            'posts.view', 'posts.create', 'posts.edit', 'posts.publish', 'posts.delete',
            'brands.manage', 'devices.manage',
            'firmware.manage', 'flash-files.manage', 'software.manage', 'drivers.manage',
            'categories.manage', 'tags.manage',
            'media.manage',
        ];
        $editor->permissions()->sync(
            Permission::whereIn('name', $editorPermissions)->pluck('id')->toArray()
        );

        // 4. Author Role
        $author = Role::firstOrCreate(
            ['name' => 'author'],
            [
                'display_name' => 'Staff Writer / Author',
                'description' => 'Creates and drafts own articles subject to editorial review.',
            ]
        );
        $authorPermissions = [
            'posts.view', 'posts.create',
            'media.manage',
        ];
        $author->permissions()->sync(
            Permission::whereIn('name', $authorPermissions)->pluck('id')->toArray()
        );
    }
}
