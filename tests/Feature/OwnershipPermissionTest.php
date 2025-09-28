<?php

namespace Tests\Feature;

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

class OwnershipPermissionTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $userA;
    protected User $userB;
    protected User $regularUser;
    protected Category $testCategory;
    protected Tag $testTag;

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

        // Create test users
        $this->admin = User::factory()->create(['name' => 'Admin User']);
        $this->admin->assignRole('admin');

        $this->userA = User::factory()->create(['name' => 'User A']);
        $this->userA->givePermissionTo(['edit companies', 'delete companies', 'edit research items', 'delete research items', 'upload documents', 'download documents']);

        $this->userB = User::factory()->create(['name' => 'User B']);
        $this->userB->givePermissionTo(['edit companies', 'delete companies', 'edit research items', 'delete research items', 'upload documents', 'download documents']);

        $this->regularUser = User::factory()->create(['name' => 'Regular User']);

        // Create test category and tag
        $this->testCategory = Category::create([
            'name' => 'Test Category',
            'slug' => 'test-category',
            'color' => '#ff0000',
            'created_by' => $this->admin->id
        ]);

        $this->testTag = Tag::create([
            'name' => 'Test Tag',
            'slug' => 'test-tag',
            'color' => '#00ff00',
            'created_by' => $this->admin->id
        ]);
    }

    // ===== COMPANY TESTS =====

    /** @test */
    public function user_can_create_company()
    {
        $companyData = [
            'name' => 'Test Company A',
            'ticker' => 'TESTA',
            'sector' => 'Technology',
            'industry' => 'Software',
            'market_cap' => 1000000000,
            'description' => 'A test company for testing purposes'
        ];

        $response = $this->actingAs($this->userA)
            ->postJson('/api/companies', $companyData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('companies', [
            'name' => 'Test Company A',
            'ticker' => 'TESTA',
            'created_by' => $this->userA->id
        ]);
    }

    /** @test */
    public function user_can_edit_own_company()
    {
        // User A creates a company
        $company = Company::create([
            'name' => 'Original Company',
            'ticker' => 'ORIG',
            'sector' => 'Technology',
            'market_cap' => 1000000000,
            'created_by' => $this->userA->id
        ]);

        $updateData = [
            'name' => 'Updated Company Name',
            'ticker' => 'ORIG',
            'sector' => 'Healthcare',
            'market_cap' => 2000000000
        ];

        $response = $this->actingAs($this->userA)
            ->putJson("/api/companies/{$company->id}", $updateData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('companies', [
            'id' => $company->id,
            'name' => 'Updated Company Name',
            'sector' => 'Healthcare'
        ]);
    }

    /** @test */
    public function user_cannot_edit_others_company()
    {
        // User A creates a company
        $company = Company::create([
            'name' => 'User A Company',
            'ticker' => 'USERA',
            'sector' => 'Technology',
            'market_cap' => 1000000000,
            'created_by' => $this->userA->id
        ]);

        $updateData = [
            'name' => 'Hacked Company Name',
            'ticker' => 'USERA',
            'sector' => 'Evil Sector',
            'market_cap' => 1
        ];

        // User B tries to edit User A's company
        $response = $this->actingAs($this->userB)
            ->putJson("/api/companies/{$company->id}", $updateData);

        $response->assertStatus(403);
        $response->assertJson(['message' => 'You can only edit companies that you created.']);

        // Ensure the company wasn't changed
        $this->assertDatabaseHas('companies', [
            'id' => $company->id,
            'name' => 'User A Company',
            'sector' => 'Technology'
        ]);
    }

    /** @test */
    public function admin_can_edit_any_company()
    {
        // User A creates a company
        $company = Company::create([
            'name' => 'User A Company',
            'ticker' => 'USERA',
            'sector' => 'Technology',
            'market_cap' => 1000000000,
            'created_by' => $this->userA->id
        ]);

        $updateData = [
            'name' => 'Admin Updated Company',
            'ticker' => 'USERA',
            'sector' => 'Finance',
            'market_cap' => 5000000000
        ];

        // Admin edits User A's company
        $response = $this->actingAs($this->admin)
            ->putJson("/api/companies/{$company->id}", $updateData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('companies', [
            'id' => $company->id,
            'name' => 'Admin Updated Company',
            'sector' => 'Finance'
        ]);
    }

    /** @test */
    public function user_can_delete_own_company()
    {
        // User A creates a company
        $company = Company::create([
            'name' => 'Company to Delete',
            'ticker' => 'DEL',
            'sector' => 'Technology',
            'market_cap' => 1000000000,
            'created_by' => $this->userA->id
        ]);

        $response = $this->actingAs($this->userA)
            ->deleteJson("/api/companies/{$company->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('companies', ['id' => $company->id]);
    }

    /** @test */
    public function user_cannot_delete_others_company()
    {
        // User A creates a company
        $company = Company::create([
            'name' => 'Protected Company',
            'ticker' => 'PROT',
            'sector' => 'Technology',
            'market_cap' => 1000000000,
            'created_by' => $this->userA->id
        ]);

        // User B tries to delete User A's company
        $response = $this->actingAs($this->userB)
            ->deleteJson("/api/companies/{$company->id}");

        $response->assertStatus(403);
        $response->assertJson(['message' => 'You can only delete companies that you created.']);

        // Ensure the company still exists
        $this->assertDatabaseHas('companies', ['id' => $company->id]);
    }

    /** @test */
    public function admin_can_delete_any_company()
    {
        // User A creates a company
        $company = Company::create([
            'name' => 'Company Admin Will Delete',
            'ticker' => 'ADMINDEL',
            'sector' => 'Technology',
            'market_cap' => 1000000000,
            'created_by' => $this->userA->id
        ]);

        // Admin deletes User A's company
        $response = $this->actingAs($this->admin)
            ->deleteJson("/api/companies/{$company->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('companies', ['id' => $company->id]);
    }

    /** @test */
    public function regular_user_cannot_create_company()
    {
        $companyData = [
            'name' => 'Unauthorized Company',
            'ticker' => 'UNAUTH',
            'sector' => 'Technology',
            'market_cap' => 1000000000
        ];

        $response = $this->actingAs($this->regularUser)
            ->postJson('/api/companies', $companyData);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('companies', [
            'name' => 'Unauthorized Company'
        ]);
    }

    // ===== RESEARCH ITEM TESTS =====

    /** @test */
    public function user_can_create_research_item()
    {
        // First create a company
        $company = Company::create([
            'name' => 'Research Company',
            'ticker' => 'RESEARCH',
            'sector' => 'Technology',
            'market_cap' => 1000000000,
            'created_by' => $this->userA->id
        ]);

        $researchData = [
            'title' => 'Test Research Item',
            'content' => 'This is a detailed research analysis.',
            'company_id' => $company->id,
            'category_id' => $this->testCategory->id,
            'visibility' => 'public',
            'tag_ids' => [$this->testTag->id]
        ];

        $response = $this->actingAs($this->userA)
            ->postJson('/api/research-items', $researchData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('research_items', [
            'title' => 'Test Research Item',
            'user_id' => $this->userA->id,
            'company_id' => $company->id
        ]);
    }

    /** @test */
    public function user_can_edit_own_research_item()
    {
        // Create company and research item
        $company = Company::create([
            'name' => 'Research Company',
            'ticker' => 'RESEARCH',
            'sector' => 'Technology',
            'market_cap' => 1000000000,
            'created_by' => $this->userA->id
        ]);

        $researchItem = ResearchItem::create([
            'title' => 'Original Research',
            'content' => 'Original content',
            'user_id' => $this->userA->id,
            'company_id' => $company->id,
            'category_id' => $this->testCategory->id,
            'visibility' => 'public'
        ]);

        $updateData = [
            'title' => 'Updated Research Title',
            'content' => 'Updated research content',
            'company_id' => $company->id,
            'category_id' => $this->testCategory->id,
            'visibility' => 'private',
            'tag_ids' => [$this->testTag->id]
        ];

        $response = $this->actingAs($this->userA)
            ->putJson("/api/research-items/{$researchItem->id}", $updateData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('research_items', [
            'id' => $researchItem->id,
            'title' => 'Updated Research Title',
            'visibility' => 'private'
        ]);
    }

    /** @test */
    public function user_cannot_edit_others_research_item()
    {
        // User A creates company and research item
        $company = Company::create([
            'name' => 'Research Company',
            'ticker' => 'RESEARCH',
            'sector' => 'Technology',
            'market_cap' => 1000000000,
            'created_by' => $this->userA->id
        ]);

        $researchItem = ResearchItem::create([
            'title' => 'User A Research',
            'content' => 'User A content',
            'user_id' => $this->userA->id,
            'company_id' => $company->id,
            'category_id' => $this->testCategory->id,
            'visibility' => 'public'
        ]);

        $updateData = [
            'title' => 'Hacked Research Title',
            'content' => 'Malicious content',
            'company_id' => $company->id,
            'category_id' => $this->testCategory->id,
            'visibility' => 'public'
        ];

        // User B tries to edit User A's research
        $response = $this->actingAs($this->userB)
            ->putJson("/api/research-items/{$researchItem->id}", $updateData);

        $response->assertStatus(403);
        $response->assertJson(['message' => 'You can only edit research items that you created.']);

        // Ensure research wasn't changed
        $this->assertDatabaseHas('research_items', [
            'id' => $researchItem->id,
            'title' => 'User A Research'
        ]);
    }

    /** @test */
    public function admin_can_edit_any_research_item()
    {
        // User A creates company and research item
        $company = Company::create([
            'name' => 'Research Company',
            'ticker' => 'RESEARCH',
            'sector' => 'Technology',
            'market_cap' => 1000000000,
            'created_by' => $this->userA->id
        ]);

        $researchItem = ResearchItem::create([
            'title' => 'User A Research',
            'content' => 'User A content',
            'user_id' => $this->userA->id,
            'company_id' => $company->id,
            'category_id' => $this->testCategory->id,
            'visibility' => 'public'
        ]);

        $updateData = [
            'title' => 'Admin Updated Research',
            'content' => 'Admin updated content',
            'company_id' => $company->id,
            'category_id' => $this->testCategory->id,
            'visibility' => 'team'
        ];

        // Admin edits User A's research
        $response = $this->actingAs($this->admin)
            ->putJson("/api/research-items/{$researchItem->id}", $updateData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('research_items', [
            'id' => $researchItem->id,
            'title' => 'Admin Updated Research',
            'visibility' => 'team'
        ]);
    }

    /** @test */
    public function user_can_delete_own_research_item()
    {
        // Create company and research item
        $company = Company::create([
            'name' => 'Research Company',
            'ticker' => 'RESEARCH',
            'sector' => 'Technology',
            'market_cap' => 1000000000,
            'created_by' => $this->userA->id
        ]);

        $researchItem = ResearchItem::create([
            'title' => 'Research to Delete',
            'content' => 'Content to delete',
            'user_id' => $this->userA->id,
            'company_id' => $company->id,
            'category_id' => $this->testCategory->id,
            'visibility' => 'public'
        ]);

        $response = $this->actingAs($this->userA)
            ->deleteJson("/api/research-items/{$researchItem->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('research_items', ['id' => $researchItem->id]);
    }

    /** @test */
    public function user_cannot_delete_others_research_item()
    {
        // User A creates company and research item
        $company = Company::create([
            'name' => 'Research Company',
            'ticker' => 'RESEARCH',
            'sector' => 'Technology',
            'market_cap' => 1000000000,
            'created_by' => $this->userA->id
        ]);

        $researchItem = ResearchItem::create([
            'title' => 'Protected Research',
            'content' => 'Protected content',
            'user_id' => $this->userA->id,
            'company_id' => $company->id,
            'category_id' => $this->testCategory->id,
            'visibility' => 'public'
        ]);

        // User B tries to delete User A's research
        $response = $this->actingAs($this->userB)
            ->deleteJson("/api/research-items/{$researchItem->id}");

        $response->assertStatus(403);
        $response->assertJson(['message' => 'You can only delete research items that you created.']);

        // Ensure research still exists
        $this->assertDatabaseHas('research_items', ['id' => $researchItem->id]);
    }

    // ===== DOCUMENT TESTS =====

    /** @test */
    public function user_with_upload_permission_can_create_document()
    {
        $company = Company::create([
            'name' => 'Document Company',
            'ticker' => 'DOC',
            'sector' => 'Technology',
            'market_cap' => 1000000000,
            'created_by' => $this->userA->id
        ]);

        $documentData = [
            'title' => 'Test Document',
            'description' => 'A test document for testing',
            'company_id' => $company->id,
            'visibility' => 'public'
        ];

        $response = $this->actingAs($this->userA)
            ->postJson('/api/documents', $documentData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('documents', [
            'title' => 'Test Document',
            'user_id' => $this->userA->id,
            'company_id' => $company->id
        ]);
    }

    /** @test */
    public function user_without_upload_permission_cannot_create_document()
    {
        $company = Company::create([
            'name' => 'Document Company',
            'ticker' => 'DOC',
            'sector' => 'Technology',
            'market_cap' => 1000000000,
            'created_by' => $this->admin->id
        ]);

        $documentData = [
            'title' => 'Unauthorized Document',
            'description' => 'This should not be created',
            'company_id' => $company->id,
            'visibility' => 'public'
        ];

        $response = $this->actingAs($this->regularUser)
            ->postJson('/api/documents', $documentData);

        $response->assertStatus(403);
        $response->assertJson(['message' => 'You do not have permission to upload documents.']);

        $this->assertDatabaseMissing('documents', [
            'title' => 'Unauthorized Document'
        ]);
    }

    /** @test */
    public function user_with_download_permission_can_view_documents()
    {
        $response = $this->actingAs($this->userA)
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
        $response = $this->actingAs($this->regularUser)
            ->getJson('/api/documents');

        $response->assertStatus(403);
        $response->assertJson(['message' => 'You do not have permission to view documents.']);
    }

    /** @test */
    public function user_can_edit_own_document()
    {
        $company = Company::create([
            'name' => 'Document Company',
            'ticker' => 'DOC',
            'sector' => 'Technology',
            'market_cap' => 1000000000,
            'created_by' => $this->userA->id
        ]);

        $document = Document::create([
            'title' => 'Original Document',
            'description' => 'Original description',
            'user_id' => $this->userA->id,
            'company_id' => $company->id,
            'visibility' => 'public'
        ]);

        $updateData = [
            'title' => 'Updated Document Title',
            'description' => 'Updated description',
            'company_id' => $company->id,
            'visibility' => 'private'
        ];

        $response = $this->actingAs($this->userA)
            ->putJson("/api/documents/{$document->id}", $updateData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('documents', [
            'id' => $document->id,
            'title' => 'Updated Document Title',
            'visibility' => 'private'
        ]);
    }

    /** @test */
    public function user_cannot_edit_others_document()
    {
        $company = Company::create([
            'name' => 'Document Company',
            'ticker' => 'DOC',
            'sector' => 'Technology',
            'market_cap' => 1000000000,
            'created_by' => $this->userA->id
        ]);

        $document = Document::create([
            'title' => 'User A Document',
            'description' => 'User A description',
            'user_id' => $this->userA->id,
            'company_id' => $company->id,
            'visibility' => 'public'
        ]);

        $updateData = [
            'title' => 'Hacked Document Title',
            'description' => 'Malicious description',
            'company_id' => $company->id,
            'visibility' => 'public'
        ];

        // User B tries to edit User A's document
        $response = $this->actingAs($this->userB)
            ->putJson("/api/documents/{$document->id}", $updateData);

        $response->assertStatus(403);
        $response->assertJson(['message' => 'You can only edit documents that you created.']);

        // Ensure document wasn't changed
        $this->assertDatabaseHas('documents', [
            'id' => $document->id,
            'title' => 'User A Document'
        ]);
    }

    /** @test */
    public function admin_can_edit_any_document()
    {
        $company = Company::create([
            'name' => 'Document Company',
            'ticker' => 'DOC',
            'sector' => 'Technology',
            'market_cap' => 1000000000,
            'created_by' => $this->userA->id
        ]);

        $document = Document::create([
            'title' => 'User A Document',
            'description' => 'User A description',
            'user_id' => $this->userA->id,
            'company_id' => $company->id,
            'visibility' => 'public'
        ]);

        $updateData = [
            'title' => 'Admin Updated Document',
            'description' => 'Admin updated description',
            'company_id' => $company->id,
            'visibility' => 'team'
        ];

        // Admin edits User A's document
        $response = $this->actingAs($this->admin)
            ->putJson("/api/documents/{$document->id}", $updateData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('documents', [
            'id' => $document->id,
            'title' => 'Admin Updated Document',
            'visibility' => 'team'
        ]);
    }

    // ===== COMPREHENSIVE OWNERSHIP VALIDATION =====

    /** @test */
    public function comprehensive_ownership_validation_across_all_resources()
    {
        // Create a company, research item, and document for User A
        $companyA = Company::create([
            'name' => 'User A Company',
            'ticker' => 'USERA',
            'sector' => 'Technology',
            'market_cap' => 1000000000,
            'created_by' => $this->userA->id
        ]);

        $researchA = ResearchItem::create([
            'title' => 'User A Research',
            'content' => 'User A research content',
            'user_id' => $this->userA->id,
            'company_id' => $companyA->id,
            'category_id' => $this->testCategory->id,
            'visibility' => 'public'
        ]);

        $documentA = Document::create([
            'title' => 'User A Document',
            'description' => 'User A document description',
            'user_id' => $this->userA->id,
            'company_id' => $companyA->id,
            'visibility' => 'public'
        ]);

        // User B should NOT be able to modify any of User A's resources

        // Try to edit company
        $response = $this->actingAs($this->userB)
            ->putJson("/api/companies/{$companyA->id}", ['name' => 'Hacked Company', 'ticker' => 'USERA', 'sector' => 'Evil']);
        $response->assertStatus(403);

        // Try to delete company
        $response = $this->actingAs($this->userB)
            ->deleteJson("/api/companies/{$companyA->id}");
        $response->assertStatus(403);

        // Try to edit research item
        $response = $this->actingAs($this->userB)
            ->putJson("/api/research-items/{$researchA->id}", [
                'title' => 'Hacked Research',
                'content' => 'Malicious content',
                'company_id' => $companyA->id,
                'category_id' => $this->testCategory->id,
                'visibility' => 'public'
            ]);
        $response->assertStatus(403);

        // Try to delete research item
        $response = $this->actingAs($this->userB)
            ->deleteJson("/api/research-items/{$researchA->id}");
        $response->assertStatus(403);

        // Try to edit document
        $response = $this->actingAs($this->userB)
            ->putJson("/api/documents/{$documentA->id}", [
                'title' => 'Hacked Document',
                'description' => 'Malicious description',
                'company_id' => $companyA->id,
                'visibility' => 'public'
            ]);
        $response->assertStatus(403);

        // Try to delete document
        $response = $this->actingAs($this->userB)
            ->deleteJson("/api/documents/{$documentA->id}");
        $response->assertStatus(403);

        // Verify all original data is intact
        $this->assertDatabaseHas('companies', [
            'id' => $companyA->id,
            'name' => 'User A Company',
            'created_by' => $this->userA->id
        ]);

        $this->assertDatabaseHas('research_items', [
            'id' => $researchA->id,
            'title' => 'User A Research',
            'user_id' => $this->userA->id
        ]);

        $this->assertDatabaseHas('documents', [
            'id' => $documentA->id,
            'title' => 'User A Document',
            'user_id' => $this->userA->id
        ]);

        // But admin should be able to modify everything
        $response = $this->actingAs($this->admin)
            ->putJson("/api/companies/{$companyA->id}", ['name' => 'Admin Modified Company', 'ticker' => 'USERA', 'sector' => 'Technology']);
        $response->assertStatus(200);

        $this->assertDatabaseHas('companies', [
            'id' => $companyA->id,
            'name' => 'Admin Modified Company'
        ]);
    }
}