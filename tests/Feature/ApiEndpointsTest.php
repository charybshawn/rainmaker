<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\Document;
use App\Models\Asset;
use App\Models\ResearchItem;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Role;

class ApiEndpointsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'user']);

        // Create users
        $this->user = User::factory()->create();
        $this->user->assignRole('user');

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
    }

    /** @test */
    public function it_requires_authentication_for_api_endpoints()
    {
        // Test various endpoints require authentication
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

    /** @test */
    public function it_can_fetch_companies_when_authenticated()
    {
        $this->actingAs($this->user);

        $company = Company::create([
            'name' => 'Test Company',
            'ticker' => 'TEST',
            'sector' => 'Technology',
            'industry' => 'Software',
            'market_cap' => '1000000000',
        ]);

        $response = $this->getJson('/api/companies');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'ticker',
                        'sector',
                        'industry',
                        'market_cap',
                        'created_at',
                        'updated_at',
                    ]
                ]
            ]);
    }

    /** @test */
    public function it_can_create_company_when_authenticated()
    {
        $this->actingAs($this->admin);

        $companyData = [
            'name' => 'New Test Company',
            'ticker' => 'NEWTEST',
            'sector' => 'Technology',
            'industry' => 'Software',
            'market_cap' => '1000000000',
            'description' => 'A test company',
        ];

        $response = $this->postJson('/api/companies', $companyData);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'name' => 'New Test Company',
                'ticker' => 'NEWTEST',
            ]);

        $this->assertDatabaseHas('companies', [
            'name' => 'New Test Company',
            'ticker' => 'NEWTEST',
        ]);
    }

    /** @test */
    public function it_can_fetch_documents_when_authenticated()
    {
        $this->actingAs($this->user);

        $response = $this->getJson('/api/documents');

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

    /** @test */
    public function it_can_create_document_when_authenticated()
    {
        $this->actingAs($this->user);

        $company = Company::create([
            'name' => 'Test Company',
            'ticker' => 'TEST',
            'sector' => 'Technology',
            'industry' => 'Software',
            'market_cap' => '1000000000',
        ]);
        $category = Category::create([
            'name' => 'Test Category',
            'color' => '#FF0000',
        ]);

        $documentData = [
            'title' => 'Test Document',
            'description' => 'A test document',
            'company_id' => $company->id,
            'category_id' => $category->id,
            'visibility' => 'private',
        ];

        $response = $this->postJson('/api/documents', $documentData);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'title' => 'Test Document',
                'description' => 'A test document',
            ]);

        $this->assertDatabaseHas('documents', [
            'title' => 'Test Document',
            'user_id' => $this->user->id,
        ]);
    }

    /** @test */
    public function it_can_delete_document_when_authorized()
    {
        $this->actingAs($this->user);

        $company = Company::factory()->create();
        $document = Document::factory()->create([
            'user_id' => $this->user->id,
            'company_id' => $company->id,
        ]);

        $response = $this->deleteJson("/api/documents/{$document->id}");

        $response->assertStatus(204);

        $this->assertSoftDeleted('documents', [
            'id' => $document->id,
        ]);
    }

    /** @test */
    public function it_can_fetch_research_items_when_authenticated()
    {
        $this->actingAs($this->user);

        $response = $this->getJson('/api/research-items');

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

    /** @test */
    public function it_can_create_research_item_when_authenticated()
    {
        $this->actingAs($this->user);

        $company = Company::create([
            'name' => 'Test Company',
            'ticker' => 'TEST',
            'sector' => 'Technology',
            'industry' => 'Software',
            'market_cap' => '1000000000',
        ]);
        $category = Category::create([
            'name' => 'Test Category',
            'color' => '#FF0000',
        ]);

        $researchData = [
            'title' => 'Test Research Item',
            'content' => 'Research content here',
            'summary' => 'Research summary',
            'company_id' => $company->id,
            'category_id' => $category->id,
            'visibility' => 'private',
            'status' => 'draft',
        ];

        $response = $this->postJson('/api/research-items', $researchData);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'title' => 'Test Research Item',
                'content' => 'Research content here',
            ]);

        $this->assertDatabaseHas('research_items', [
            'title' => 'Test Research Item',
            'user_id' => $this->user->id,
        ]);
    }

    /** @test */
    public function it_can_soft_delete_research_item()
    {
        $this->actingAs($this->user);

        $company = Company::factory()->create();
        $researchItem = ResearchItem::factory()->create([
            'user_id' => $this->user->id,
            'company_id' => $company->id,
        ]);

        $response = $this->deleteJson("/api/research-items/{$researchItem->id}");

        $response->assertStatus(204);

        // Verify it's soft deleted
        $this->assertDatabaseHas('research_items', [
            'id' => $researchItem->id,
        ]);

        $this->assertNotNull(ResearchItem::withTrashed()->find($researchItem->id)->deleted_at);
    }

    /** @test */
    public function it_can_fetch_categories_when_authenticated()
    {
        $this->actingAs($this->user);

        $category = Category::factory()->create([
            'name' => 'Test Category',
            'color' => '#FF0000',
        ]);

        $response = $this->getJson('/api/categories');

        $response->assertOk()
            ->assertJsonFragment([
                'name' => 'Test Category',
                'color' => '#FF0000',
            ]);
    }

    /** @test */
    public function it_can_fetch_tags_when_authenticated()
    {
        $this->actingAs($this->user);

        $response = $this->getJson('/api/tags');

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

    /** @test */
    public function it_can_create_tag_when_authenticated()
    {
        $this->actingAs($this->user);

        $tagData = [
            'name' => 'Test Tag',
            'color' => '#0000FF',
        ];

        $response = $this->postJson('/api/tags', $tagData);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'name' => 'Test Tag',
                'color' => '#0000FF',
            ]);

        $this->assertDatabaseHas('tags', [
            'name' => 'Test Tag',
            'created_by' => $this->user->id,
        ]);
    }

    /** @test */
    public function it_handles_404_for_missing_research_items()
    {
        $this->actingAs($this->user);

        $response = $this->getJson('/api/research-items/999999');

        $response->assertStatus(404);
    }

    /** @test */
    public function it_handles_404_for_missing_documents()
    {
        $this->actingAs($this->user);

        $response = $this->getJson('/api/documents/999999');

        $response->assertStatus(404);
    }

    /** @test */
    public function it_can_search_companies()
    {
        $this->actingAs($this->user);

        $company1 = Company::factory()->create(['name' => 'Apple Inc', 'ticker' => 'AAPL']);
        $company2 = Company::factory()->create(['name' => 'Microsoft Corp', 'ticker' => 'MSFT']);

        $response = $this->getJson('/api/companies?search=Apple');

        $response->assertOk()
            ->assertJsonFragment(['name' => 'Apple Inc'])
            ->assertJsonMissing(['name' => 'Microsoft Corp']);
    }

    /** @test */
    public function it_can_filter_documents_by_company()
    {
        $this->actingAs($this->user);

        $company1 = Company::factory()->create();
        $company2 = Company::factory()->create();

        $response = $this->getJson("/api/documents?company_id={$company1->id}");

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [],
                'pagination' => []
            ]);
    }

    /** @test */
    public function it_returns_proper_error_for_unauthorized_document_deletion()
    {
        $this->actingAs($this->user);

        $otherUser = User::factory()->create();
        $company = Company::factory()->create();
        $document = Document::factory()->create([
            'user_id' => $otherUser->id,
            'company_id' => $company->id,
        ]);

        $response = $this->deleteJson("/api/documents/{$document->id}");

        $response->assertStatus(403);
    }
}