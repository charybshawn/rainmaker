<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class BasicApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'user']);

        // Create permissions
        Permission::create(['name' => 'download documents']);
        Permission::create(['name' => 'upload documents']);
        Permission::create(['name' => 'view companies']);
        Permission::create(['name' => 'manage companies']);
        Permission::create(['name' => 'view research items']);
        Permission::create(['name' => 'create research items']);
        Permission::create(['name' => 'edit research items']);
        Permission::create(['name' => 'delete research items']);

        // Create user with necessary permissions
        $this->user = User::factory()->create();
        $this->user->assignRole('user');

        // Give user necessary permissions for testing
        $this->user->givePermissionTo([
            'download documents',
            'upload documents',
            'view companies',
            'manage companies',
            'view research items',
            'create research items',
            'edit research items',
            'delete research items',
        ]);
    }

    public function test_api_requires_authentication()
    {
        $endpoints = [
            ['GET', '/api/companies'],
            ['GET', '/api/documents'],
            ['GET', '/api/research-items'],
            ['GET', '/api/categories'],
            ['GET', '/api/tags'],
        ];

        foreach ($endpoints as [$method, $endpoint]) {
            $response = $this->json($method, $endpoint);
            $response->assertStatus(401);
        }
    }

    public function test_can_fetch_companies_when_authenticated()
    {
        $this->actingAs($this->user);

        $response = $this->getJson('/api/companies');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [],
                'pagination' => [
                    'current_page',
                    'total_pages',
                    'has_more_pages',
                    'total_items',
                    'per_page',
                ]
            ]);
    }

    public function test_can_fetch_documents_when_authenticated()
    {
        $this->actingAs($this->user);

        $response = $this->getJson('/api/documents');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [],
                'pagination' => []
            ]);
    }

    public function test_can_fetch_research_items_when_authenticated()
    {
        $this->actingAs($this->user);

        $response = $this->getJson('/api/research-items');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [],
                'pagination' => []
            ]);
    }

    public function test_can_fetch_categories_when_authenticated()
    {
        $this->actingAs($this->user);

        $response = $this->getJson('/api/categories');

        $response->assertOk();
    }

    public function test_can_fetch_tags_when_authenticated()
    {
        $this->actingAs($this->user);

        $response = $this->getJson('/api/tags');

        $response->assertOk();
    }

    public function test_handles_404_for_missing_resources()
    {
        $this->actingAs($this->user);

        $endpoints = [
            '/api/research-items/999999',
            '/api/documents/999999',
            '/api/companies/999999',
        ];

        foreach ($endpoints as $endpoint) {
            $response = $this->getJson($endpoint);
            $response->assertStatus(404);
        }
    }

    public function test_search_functionality()
    {
        $this->actingAs($this->user);

        // Test search endpoints don't error
        $searchEndpoints = [
            '/api/companies?search=test',
            '/api/documents?search=test',
            '/api/research-items?search=test',
        ];

        foreach ($searchEndpoints as $endpoint) {
            $response = $this->getJson($endpoint);
            $response->assertOk();
        }
    }

    public function test_filtering_functionality()
    {
        $this->actingAs($this->user);

        // Test filter endpoints don't error
        $filterEndpoints = [
            '/api/documents?company_id=1',
            '/api/research-items?company_id=1',
            '/api/documents?visibility=private',
        ];

        foreach ($filterEndpoints as $endpoint) {
            $response = $this->getJson($endpoint);
            $response->assertOk();
        }
    }
}