<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Company;
use App\Models\Document;
use App\Models\ResearchItem;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ResourcePermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create required permissions
        Permission::create(['name' => 'edit companies']);
        Permission::create(['name' => 'delete companies']);
        Permission::create(['name' => 'edit research items']);
        Permission::create(['name' => 'delete research items']);
        Permission::create(['name' => 'upload documents']);
        Permission::create(['name' => 'download documents']);

        // Create admin role with all permissions
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo([
            'edit companies', 'delete companies',
            'edit research items', 'delete research items',
            'upload documents', 'download documents'
        ]);

        // Create a test user for foreign key references
        $testUser = User::factory()->create();

        // Create test category
        $this->testCategory = Category::create([
            'name' => 'Test Category',
            'slug' => 'test-category',
            'color' => '#ff0000',
            'created_by' => $testUser->id
        ]);

        // Create test tag
        $this->testTag = Tag::create([
            'name' => 'Test Tag',
            'slug' => 'test-tag',
            'color' => '#00ff00',
            'created_by' => $testUser->id
        ]);
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

    // ===== COMPANY RESOURCE TESTS =====

    /** @test */
    public function admin_can_edit_any_company()
    {
        $adminUser = $this->createAdminUser();
        $otherUser = $this->createRegularUser();

        $company = Company::factory()->create(['created_by' => $otherUser->id]);

        $updateData = [
            'name' => 'Updated Company Name',
            'ticker' => $company->ticker,
            'market_cap' => 2000000,
            'sector' => 'Technology'
        ];

        $response = $this->actingAs($adminUser)
            ->putJson("/api/companies/{$company->id}", $updateData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('companies', [
            'id' => $company->id,
            'name' => 'Updated Company Name'
        ]);
    }

    /** @test */
    public function user_can_edit_own_company()
    {
        $user = $this->createUserWithPermission('edit companies');

        $company = Company::factory()->create(['created_by' => $user->id]);

        $updateData = [
            'name' => 'Updated Company Name',
            'ticker' => $company->ticker,
            'market_cap' => 2000000,
            'sector' => 'Technology'
        ];

        $response = $this->actingAs($user)
            ->putJson("/api/companies/{$company->id}", $updateData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('companies', [
            'id' => $company->id,
            'name' => 'Updated Company Name'
        ]);
    }

    /** @test */
    public function user_cannot_edit_others_company()
    {
        $user = $this->createUserWithPermission('edit companies');
        $otherUser = $this->createRegularUser();

        $company = Company::factory()->create(['created_by' => $otherUser->id]);
        $originalName = $company->name;

        $updateData = [
            'name' => 'Updated Company Name',
            'ticker' => $company->ticker,
            'market_cap' => 2000000,
            'sector' => 'Technology'
        ];

        $response = $this->actingAs($user)
            ->putJson("/api/companies/{$company->id}", $updateData);

        $response->assertStatus(403);
        $response->assertJson(['message' => 'You can only edit companies that you created.']);
        $this->assertDatabaseHas('companies', [
            'id' => $company->id,
            'name' => $originalName
        ]);
    }

    /** @test */
    public function user_without_permission_cannot_edit_company()
    {
        $user = $this->createRegularUser();

        $company = Company::factory()->create(['created_by' => $user->id]);
        $originalName = $company->name;

        $updateData = [
            'name' => 'Updated Company Name',
            'ticker' => $company->ticker,
            'market_cap' => 2000000,
            'sector' => 'Technology'
        ];

        $response = $this->actingAs($user)
            ->putJson("/api/companies/{$company->id}", $updateData);

        $response->assertStatus(403);
        $response->assertJson(['message' => 'You do not have permission to edit companies.']);
        $this->assertDatabaseHas('companies', [
            'id' => $company->id,
            'name' => $originalName
        ]);
    }

    /** @test */
    public function admin_can_delete_any_company()
    {
        $adminUser = $this->createAdminUser();
        $otherUser = $this->createRegularUser();

        $company = Company::factory()->create(['created_by' => $otherUser->id]);

        $response = $this->actingAs($adminUser)
            ->deleteJson("/api/companies/{$company->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('companies', [
            'id' => $company->id
        ]);
    }

    /** @test */
    public function user_can_delete_own_company()
    {
        $user = $this->createUserWithPermission('delete companies');

        $company = Company::factory()->create(['created_by' => $user->id]);

        $response = $this->actingAs($user)
            ->deleteJson("/api/companies/{$company->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('companies', [
            'id' => $company->id
        ]);
    }

    /** @test */
    public function user_cannot_delete_others_company()
    {
        $user = $this->createUserWithPermission('delete companies');
        $otherUser = $this->createRegularUser();

        $company = Company::factory()->create(['created_by' => $otherUser->id]);

        $response = $this->actingAs($user)
            ->deleteJson("/api/companies/{$company->id}");

        $response->assertStatus(403);
        $response->assertJson(['message' => 'You can only delete companies that you created.']);
        $this->assertDatabaseHas('companies', [
            'id' => $company->id
        ]);
    }

    // ===== RESEARCH ITEM RESOURCE TESTS =====

    /** @test */
    public function admin_can_edit_any_research_item()
    {
        $adminUser = $this->createAdminUser();
        $otherUser = $this->createRegularUser();
        $company = Company::factory()->create(['created_by' => $otherUser->id]);

        $researchItem = ResearchItem::factory()->create([
            'user_id' => $otherUser->id,
            'company_id' => $company->id,
            'category_id' => $this->testCategory->id
        ]);

        $updateData = [
            'title' => 'Updated Research Title',
            'content' => 'Updated research content',
            'company_id' => $company->id,
            'category_id' => $this->testCategory->id,
            'visibility' => 'public',
            'tag_ids' => [$this->testTag->id]
        ];

        $response = $this->actingAs($adminUser)
            ->putJson("/api/research-items/{$researchItem->id}", $updateData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('research_items', [
            'id' => $researchItem->id,
            'title' => 'Updated Research Title'
        ]);
    }

    /** @test */
    public function user_can_edit_own_research_item()
    {
        $user = $this->createUserWithPermission('edit research items');
        $company = Company::factory()->create(['created_by' => $user->id]);

        $researchItem = ResearchItem::factory()->create([
            'user_id' => $user->id,
            'company_id' => $company->id,
            'category_id' => $this->testCategory->id
        ]);

        $updateData = [
            'title' => 'Updated Research Title',
            'content' => 'Updated research content',
            'company_id' => $company->id,
            'category_id' => $this->testCategory->id,
            'visibility' => 'public',
            'tag_ids' => [$this->testTag->id]
        ];

        $response = $this->actingAs($user)
            ->putJson("/api/research-items/{$researchItem->id}", $updateData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('research_items', [
            'id' => $researchItem->id,
            'title' => 'Updated Research Title'
        ]);
    }

    /** @test */
    public function user_cannot_edit_others_research_item()
    {
        $user = $this->createUserWithPermission('edit research items');
        $otherUser = $this->createRegularUser();
        $company = Company::factory()->create(['created_by' => $otherUser->id]);

        $researchItem = ResearchItem::factory()->create([
            'user_id' => $otherUser->id,
            'company_id' => $company->id,
            'category_id' => $this->testCategory->id
        ]);
        $originalTitle = $researchItem->title;

        $updateData = [
            'title' => 'Updated Research Title',
            'content' => 'Updated research content',
            'company_id' => $company->id,
            'category_id' => $this->testCategory->id,
            'visibility' => 'public',
            'tag_ids' => [$this->testTag->id]
        ];

        $response = $this->actingAs($user)
            ->putJson("/api/research-items/{$researchItem->id}", $updateData);

        $response->assertStatus(403);
        $response->assertJson(['message' => 'You can only edit research items that you created.']);
        $this->assertDatabaseHas('research_items', [
            'id' => $researchItem->id,
            'title' => $originalTitle
        ]);
    }

    /** @test */
    public function admin_can_delete_any_research_item()
    {
        $adminUser = $this->createAdminUser();
        $otherUser = $this->createRegularUser();
        $company = Company::factory()->create(['created_by' => $otherUser->id]);

        $researchItem = ResearchItem::factory()->create([
            'user_id' => $otherUser->id,
            'company_id' => $company->id,
            'category_id' => $this->testCategory->id
        ]);

        $response = $this->actingAs($adminUser)
            ->deleteJson("/api/research-items/{$researchItem->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('research_items', [
            'id' => $researchItem->id
        ]);
    }

    /** @test */
    public function user_can_delete_own_research_item()
    {
        $user = $this->createUserWithPermission('delete research items');
        $company = Company::factory()->create(['created_by' => $user->id]);

        $researchItem = ResearchItem::factory()->create([
            'user_id' => $user->id,
            'company_id' => $company->id,
            'category_id' => $this->testCategory->id
        ]);

        $response = $this->actingAs($user)
            ->deleteJson("/api/research-items/{$researchItem->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('research_items', [
            'id' => $researchItem->id
        ]);
    }

    /** @test */
    public function user_cannot_delete_others_research_item()
    {
        $user = $this->createUserWithPermission('delete research items');
        $otherUser = $this->createRegularUser();
        $company = Company::factory()->create(['created_by' => $otherUser->id]);

        $researchItem = ResearchItem::factory()->create([
            'user_id' => $otherUser->id,
            'company_id' => $company->id,
            'category_id' => $this->testCategory->id
        ]);

        $response = $this->actingAs($user)
            ->deleteJson("/api/research-items/{$researchItem->id}");

        $response->assertStatus(403);
        $response->assertJson(['message' => 'You can only delete research items that you created.']);
        $this->assertDatabaseHas('research_items', [
            'id' => $researchItem->id
        ]);
    }

    // ===== DOCUMENT RESOURCE TESTS =====

    /** @test */
    public function user_with_download_permission_can_view_documents()
    {
        $user = $this->createUserWithPermission('download documents');
        $company = Company::factory()->create(['created_by' => $user->id]);

        $response = $this->actingAs($user)
            ->getJson('/api/documents');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data',
            'pagination'
        ]);
    }

    /** @test */
    public function user_without_download_permission_cannot_view_documents()
    {
        $user = $this->createRegularUser();

        $response = $this->actingAs($user)
            ->getJson('/api/documents');

        $response->assertStatus(403);
        $response->assertJson(['message' => 'You do not have permission to view documents.']);
    }

    /** @test */
    public function user_with_upload_permission_can_create_document()
    {
        $user = $this->createUserWithPermission('upload documents');
        $company = Company::factory()->create(['created_by' => $user->id]);

        $documentData = [
            'title' => 'Test Document',
            'description' => 'Test document description',
            'company_id' => $company->id,
            'visibility' => 'public'
        ];

        $response = $this->actingAs($user)
            ->postJson('/api/documents', $documentData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('documents', [
            'title' => 'Test Document',
            'user_id' => $user->id
        ]);
    }

    /** @test */
    public function user_without_upload_permission_cannot_create_document()
    {
        $user = $this->createRegularUser();
        $company = Company::factory()->create(['created_by' => $user->id]);

        $documentData = [
            'title' => 'Test Document',
            'description' => 'Test document description',
            'company_id' => $company->id,
            'visibility' => 'public'
        ];

        $response = $this->actingAs($user)
            ->postJson('/api/documents', $documentData);

        $response->assertStatus(403);
        $response->assertJson(['message' => 'You do not have permission to upload documents.']);
        $this->assertDatabaseMissing('documents', [
            'title' => 'Test Document'
        ]);
    }

    /** @test */
    public function admin_can_edit_any_document()
    {
        $adminUser = $this->createAdminUser();
        $otherUser = $this->createRegularUser();
        $company = Company::factory()->create(['created_by' => $otherUser->id]);

        $document = Document::factory()->create([
            'user_id' => $otherUser->id,
            'company_id' => $company->id
        ]);

        $updateData = [
            'title' => 'Updated Document Title',
            'description' => 'Updated description',
            'company_id' => $company->id,
            'visibility' => 'public'
        ];

        $response = $this->actingAs($adminUser)
            ->putJson("/api/documents/{$document->id}", $updateData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('documents', [
            'id' => $document->id,
            'title' => 'Updated Document Title'
        ]);
    }

    /** @test */
    public function user_can_edit_own_document()
    {
        $user = $this->createUserWithPermission('upload documents');
        $company = Company::factory()->create(['created_by' => $user->id]);

        $document = Document::factory()->create([
            'user_id' => $user->id,
            'company_id' => $company->id
        ]);

        $updateData = [
            'title' => 'Updated Document Title',
            'description' => 'Updated description',
            'company_id' => $company->id,
            'visibility' => 'public'
        ];

        $response = $this->actingAs($user)
            ->putJson("/api/documents/{$document->id}", $updateData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('documents', [
            'id' => $document->id,
            'title' => 'Updated Document Title'
        ]);
    }

    /** @test */
    public function user_cannot_edit_others_document()
    {
        $user = $this->createUserWithPermission('upload documents');
        $otherUser = $this->createRegularUser();
        $company = Company::factory()->create(['created_by' => $otherUser->id]);

        $document = Document::factory()->create([
            'user_id' => $otherUser->id,
            'company_id' => $company->id
        ]);
        $originalTitle = $document->title;

        $updateData = [
            'title' => 'Updated Document Title',
            'description' => 'Updated description',
            'company_id' => $company->id,
            'visibility' => 'public'
        ];

        $response = $this->actingAs($user)
            ->putJson("/api/documents/{$document->id}", $updateData);

        $response->assertStatus(403);
        $response->assertJson(['message' => 'You can only edit documents that you created.']);
        $this->assertDatabaseHas('documents', [
            'id' => $document->id,
            'title' => $originalTitle
        ]);
    }

    /** @test */
    public function admin_can_delete_any_document()
    {
        $adminUser = $this->createAdminUser();
        $otherUser = $this->createRegularUser();
        $company = Company::factory()->create(['created_by' => $otherUser->id]);

        $document = Document::factory()->create([
            'user_id' => $otherUser->id,
            'company_id' => $company->id
        ]);

        $response = $this->actingAs($adminUser)
            ->deleteJson("/api/documents/{$document->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('documents', [
            'id' => $document->id
        ]);
    }

    /** @test */
    public function user_can_delete_own_document()
    {
        $user = $this->createUserWithPermission('upload documents');
        $company = Company::factory()->create(['created_by' => $user->id]);

        $document = Document::factory()->create([
            'user_id' => $user->id,
            'company_id' => $company->id
        ]);

        $response = $this->actingAs($user)
            ->deleteJson("/api/documents/{$document->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('documents', [
            'id' => $document->id
        ]);
    }

    /** @test */
    public function user_cannot_delete_others_document()
    {
        $user = $this->createUserWithPermission('upload documents');
        $otherUser = $this->createRegularUser();
        $company = Company::factory()->create(['created_by' => $otherUser->id]);

        $document = Document::factory()->create([
            'user_id' => $otherUser->id,
            'company_id' => $company->id
        ]);

        $response = $this->actingAs($user)
            ->deleteJson("/api/documents/{$document->id}");

        $response->assertStatus(403);
        $response->assertJson(['message' => 'You can only delete documents that you created.']);
        $this->assertDatabaseHas('documents', [
            'id' => $document->id
        ]);
    }
}