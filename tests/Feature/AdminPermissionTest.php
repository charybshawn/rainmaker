<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminPermissionTest extends TestCase
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
    public function admin_user_can_access_admin_dashboard()
    {
        $adminUser = $this->createAdminUser();

        $response = $this->actingAs($adminUser)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) =>
            $page->component('Admin/Dashboard')
                ->has('users_count')
                ->has('roles_count')
        );
    }

    /** @test */
    public function user_with_manage_users_permission_can_access_admin_dashboard()
    {
        $user = $this->createUserWithPermission('manage users');

        $response = $this->actingAs($user)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) =>
            $page->component('Admin/Dashboard')
        );
    }

    /** @test */
    public function regular_user_cannot_access_admin_dashboard()
    {
        $regularUser = $this->createRegularUser();

        $response = $this->actingAs($regularUser)->get('/admin/dashboard');

        $response->assertStatus(403);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_admin_dashboard()
    {
        $response = $this->get('/admin/dashboard');

        $response->assertRedirect('/login');
    }

    /** @test */
    public function admin_user_can_access_users_management()
    {
        $adminUser = $this->createAdminUser();

        $response = $this->actingAs($adminUser)->get('/admin/users');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) =>
            $page->component('Admin/Users')
                ->has('users')
                ->has('roles')
        );
    }

    /** @test */
    public function user_with_manage_users_permission_can_access_users_management()
    {
        $user = $this->createUserWithPermission('manage users');

        $response = $this->actingAs($user)->get('/admin/users');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) =>
            $page->component('Admin/Users')
        );
    }

    /** @test */
    public function regular_user_cannot_access_users_management()
    {
        $regularUser = $this->createRegularUser();

        $response = $this->actingAs($regularUser)->get('/admin/users');

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_user_can_access_roles_management()
    {
        $adminUser = $this->createAdminUser();

        $response = $this->actingAs($adminUser)->get('/admin/roles');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) =>
            $page->component('Admin/Roles')
                ->has('roles')
                ->has('permissions')
        );
    }

    /** @test */
    public function user_with_manage_roles_permission_can_access_roles_management()
    {
        $user = $this->createUserWithPermission('manage roles');

        $response = $this->actingAs($user)->get('/admin/roles');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) =>
            $page->component('Admin/Roles')
        );
    }

    /** @test */
    public function regular_user_cannot_access_roles_management()
    {
        $regularUser = $this->createRegularUser();

        $response = $this->actingAs($regularUser)->get('/admin/roles');

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_user_can_access_permissions_management()
    {
        $adminUser = $this->createAdminUser();

        $response = $this->actingAs($adminUser)->get('/admin/permissions');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) =>
            $page->component('Admin/Permissions')
                ->has('permissions')
        );
    }

    /** @test */
    public function user_with_manage_permissions_permission_can_access_permissions_management()
    {
        $user = $this->createUserWithPermission('manage permissions');

        $response = $this->actingAs($user)->get('/admin/permissions');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) =>
            $page->component('Admin/Permissions')
        );
    }

    /** @test */
    public function regular_user_cannot_access_permissions_management()
    {
        $regularUser = $this->createRegularUser();

        $response = $this->actingAs($regularUser)->get('/admin/permissions');

        $response->assertStatus(403);
    }

    /** @test */
    public function user_without_proper_permission_cannot_access_specific_admin_sections()
    {
        // User with only manage users permission
        $userManagerOnly = $this->createUserWithPermission('manage users');

        // Should NOT be able to access roles management
        $response = $this->actingAs($userManagerOnly)->get('/admin/roles');
        $response->assertStatus(403);

        // Should NOT be able to access permissions management
        $response = $this->actingAs($userManagerOnly)->get('/admin/permissions');
        $response->assertStatus(403);

        // But SHOULD be able to access users management
        $response = $this->actingAs($userManagerOnly)->get('/admin/users');
        $response->assertStatus(200);
    }

    /** @test */
    public function admin_role_grants_access_to_all_admin_sections()
    {
        $adminUser = $this->createAdminUser();

        // Should be able to access all admin sections
        $adminRoutes = [
            '/admin/dashboard',
            '/admin/users',
            '/admin/roles',
            '/admin/permissions'
        ];

        foreach ($adminRoutes as $route) {
            $response = $this->actingAs($adminUser)->get($route);
            $response->assertStatus(200, "Admin should have access to {$route}");
        }
    }
}