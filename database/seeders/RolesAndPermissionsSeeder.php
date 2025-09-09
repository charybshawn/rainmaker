<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            'manage users',
            'manage roles', 
            'manage permissions',
            'create companies',
            'edit companies',
            'delete companies',
            'view companies',
            'create research items',
            'edit research items',
            'delete research items',
            'view research items',
            'manage categories',
            'manage tags',
            'upload documents',
            'download documents',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        $editorRole = Role::create(['name' => 'editor']);
        $editorRole->givePermissionTo([
            'create companies',
            'edit companies',
            'view companies',
            'create research items',
            'edit research items',
            'view research items',
            'manage categories',
            'manage tags',
            'upload documents',
            'download documents',
        ]);

        $viewerRole = Role::create(['name' => 'viewer']);
        $viewerRole->givePermissionTo([
            'view companies',
            'view research items',
            'download documents',
        ]);

        // Create default admin user if no users exist
        if (User::count() == 0) {
            $admin = User::create([
                'name' => 'Admin User',
                'email' => 'admin@rainmaker.com',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]);
            $admin->assignRole('admin');
        } else {
            // Assign admin role to first user
            $firstUser = User::first();
            if (!$firstUser->hasRole('admin')) {
                $firstUser->assignRole('admin');
            }
        }
    }
}