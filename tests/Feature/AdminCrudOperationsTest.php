<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminCrudOperationsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create required permissions
        Permission::create(['name' => 'manage users']);
        Permission::create(['name' => 'manage roles']);
        Permission::create(['name' => 'manage permissions']);

        // Create admin role with all permissions
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(['manage users', 'manage roles', 'manage permissions']);

        // Create test role for role operations
        Role::create(['name' => 'test-role']);
    }

    /**
     * Create a user with admin role
     */
    private function createAdminUser(): User
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        return $user;
    }

    /**
     * Create a user with specific permission
     */
    private function createUserWithPermission(string $permission): User
    {
        $user = User::factory()->create();
        $user->givePermissionTo($permission);
        return $user;
    }

    /**
     * Create a regular user with no special permissions
     */
    private function createRegularUser(): User
    {
        return User::factory()->create();
    }

    /** @test */
    public function admin_can_update_user_roles()
    {
        $adminUser = $this->createAdminUser();
        $targetUser = $this->createRegularUser();

        $updateData = [
            'roles' => ['test-role']
        ];

        $response = $this->actingAs($adminUser)->put("/admin/users/{$targetUser->id}/roles", $updateData);

        $response->assertRedirect();
        $this->assertTrue($targetUser->fresh()->hasRole('test-role'));
    }

    /** @test */
    public function user_with_manage_users_permission_can_update_user_roles()
    {
        $user = $this->createUserWithPermission('manage users');
        $targetUser = $this->createRegularUser();

        $updateData = [
            'roles' => ['test-role']
        ];

        $response = $this->actingAs($user)->put("/admin/users/{$targetUser->id}/roles", $updateData);

        $response->assertRedirect();
        $this->assertTrue($targetUser->fresh()->hasRole('test-role'));
    }

    /** @test */
    public function regular_user_cannot_update_user_roles()
    {
        $regularUser = $this->createRegularUser();
        $targetUser = $this->createRegularUser();

        $updateData = [
            'roles' => ['test-role']
        ];

        $response = $this->actingAs($regularUser)->put("/admin/users/{$targetUser->id}/roles", $updateData);

        $response->assertStatus(403);
        $this->assertFalse($targetUser->fresh()->hasRole('test-role'));
    }

    /** @test */
    public function admin_can_create_role()
    {
        $adminUser = $this->createAdminUser();

        $roleData = [
            'name' => 'new-role',
            'permissions' => ['manage users']
        ];

        $response = $this->actingAs($adminUser)->post('/admin/roles', $roleData);

        $response->assertRedirect();
        $this->assertDatabaseHas('roles', [
            'name' => 'new-role'
        ]);
    }

    /** @test */
    public function user_with_manage_roles_permission_can_create_role()
    {
        $user = $this->createUserWithPermission('manage roles');

        $roleData = [
            'name' => 'new-role',
            'permissions' => ['manage users']
        ];

        $response = $this->actingAs($user)->post('/admin/roles', $roleData);

        $response->assertRedirect();
        $this->assertDatabaseHas('roles', [
            'name' => 'new-role'
        ]);
    }

    /** @test */
    public function regular_user_cannot_create_role()
    {
        $regularUser = $this->createRegularUser();

        $roleData = [
            'name' => 'new-role',
            'permissions' => ['manage users']
        ];

        $response = $this->actingAs($regularUser)->post('/admin/roles', $roleData);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('roles', [
            'name' => 'new-role'
        ]);
    }

    /** @test */
    public function admin_can_update_role()
    {
        $adminUser = $this->createAdminUser();
        $role = Role::where('name', 'test-role')->first();

        $updateData = [
            'name' => 'updated-role',
            'permissions' => ['manage users']
        ];

        $response = $this->actingAs($adminUser)->put("/admin/roles/{$role->id}", $updateData);

        $response->assertRedirect();
        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'name' => 'updated-role'
        ]);
    }

    /** @test */
    public function user_with_manage_roles_permission_can_update_role()
    {
        $user = $this->createUserWithPermission('manage roles');
        $role = Role::where('name', 'test-role')->first();

        $updateData = [
            'name' => 'updated-role',
            'permissions' => ['manage users']
        ];

        $response = $this->actingAs($user)->put("/admin/roles/{$role->id}", $updateData);

        $response->assertRedirect();
        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'name' => 'updated-role'
        ]);
    }

    /** @test */
    public function regular_user_cannot_update_role()
    {
        $regularUser = $this->createRegularUser();
        $role = Role::where('name', 'test-role')->first();

        $updateData = [
            'name' => 'updated-role',
            'permissions' => ['manage users']
        ];

        $response = $this->actingAs($regularUser)->put("/admin/roles/{$role->id}", $updateData);

        $response->assertStatus(403);
        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'name' => 'test-role'
        ]);
    }

    /** @test */
    public function admin_can_delete_role()
    {
        $adminUser = $this->createAdminUser();
        $role = Role::where('name', 'test-role')->first();

        $response = $this->actingAs($adminUser)->delete("/admin/roles/{$role->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('roles', [
            'id' => $role->id
        ]);
    }

    /** @test */
    public function user_with_manage_roles_permission_can_delete_role()
    {
        $user = $this->createUserWithPermission('manage roles');
        $role = Role::where('name', 'test-role')->first();

        $response = $this->actingAs($user)->delete("/admin/roles/{$role->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('roles', [
            'id' => $role->id
        ]);
    }

    /** @test */
    public function regular_user_cannot_delete_role()
    {
        $regularUser = $this->createRegularUser();
        $role = Role::where('name', 'test-role')->first();

        $response = $this->actingAs($regularUser)->delete("/admin/roles/{$role->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('roles', [
            'id' => $role->id
        ]);
    }

    /** @test */
    public function admin_can_create_permission()
    {
        $adminUser = $this->createAdminUser();

        $permissionData = [
            'name' => 'new permission'
        ];

        $response = $this->actingAs($adminUser)->post('/admin/permissions', $permissionData);

        $response->assertRedirect();
        $this->assertDatabaseHas('permissions', [
            'name' => 'new permission'
        ]);
    }

    /** @test */
    public function user_with_manage_permissions_permission_can_create_permission()
    {
        $user = $this->createUserWithPermission('manage permissions');

        $permissionData = [
            'name' => 'new permission'
        ];

        $response = $this->actingAs($user)->post('/admin/permissions', $permissionData);

        $response->assertRedirect();
        $this->assertDatabaseHas('permissions', [
            'name' => 'new permission'
        ]);
    }

    /** @test */
    public function regular_user_cannot_create_permission()
    {
        $regularUser = $this->createRegularUser();

        $permissionData = [
            'name' => 'new permission'
        ];

        $response = $this->actingAs($regularUser)->post('/admin/permissions', $permissionData);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('permissions', [
            'name' => 'new permission'
        ]);
    }

    /** @test */
    public function admin_can_update_permission()
    {
        $adminUser = $this->createAdminUser();
        $permission = Permission::where('name', 'manage users')->first();

        $updateData = [
            'name' => 'updated permission'
        ];

        $response = $this->actingAs($adminUser)->put("/admin/permissions/{$permission->id}", $updateData);

        $response->assertRedirect();
        $this->assertDatabaseHas('permissions', [
            'id' => $permission->id,
            'name' => 'updated permission'
        ]);
    }

    /** @test */
    public function user_with_manage_permissions_permission_can_update_permission()
    {
        $user = $this->createUserWithPermission('manage permissions');
        $permission = Permission::where('name', 'manage users')->first();

        $updateData = [
            'name' => 'updated permission'
        ];

        $response = $this->actingAs($user)->put("/admin/permissions/{$permission->id}", $updateData);

        $response->assertRedirect();
        $this->assertDatabaseHas('permissions', [
            'id' => $permission->id,
            'name' => 'updated permission'
        ]);
    }

    /** @test */
    public function regular_user_cannot_update_permission()
    {
        $regularUser = $this->createRegularUser();
        $permission = Permission::where('name', 'manage users')->first();

        $updateData = [
            'name' => 'updated permission'
        ];

        $response = $this->actingAs($regularUser)->put("/admin/permissions/{$permission->id}", $updateData);

        $response->assertStatus(403);
        $this->assertDatabaseHas('permissions', [
            'id' => $permission->id,
            'name' => 'manage users'
        ]);
    }

    /** @test */
    public function admin_can_delete_permission()
    {
        $adminUser = $this->createAdminUser();

        // Create a test permission that's safe to delete
        $permission = Permission::create(['name' => 'deletable permission']);

        $response = $this->actingAs($adminUser)->delete("/admin/permissions/{$permission->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('permissions', [
            'id' => $permission->id
        ]);
    }

    /** @test */
    public function user_with_manage_permissions_permission_can_delete_permission()
    {
        $user = $this->createUserWithPermission('manage permissions');

        // Create a test permission that's safe to delete
        $permission = Permission::create(['name' => 'deletable permission']);

        $response = $this->actingAs($user)->delete("/admin/permissions/{$permission->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('permissions', [
            'id' => $permission->id
        ]);
    }

    /** @test */
    public function regular_user_cannot_delete_permission()
    {
        $regularUser = $this->createRegularUser();

        // Create a test permission that's safe to delete
        $permission = Permission::create(['name' => 'deletable permission']);

        $response = $this->actingAs($regularUser)->delete("/admin/permissions/{$permission->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('permissions', [
            'id' => $permission->id
        ]);
    }
}