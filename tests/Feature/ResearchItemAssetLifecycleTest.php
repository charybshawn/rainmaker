<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Company;
use App\Models\ResearchItem;
use App\Models\Tag;
use App\Models\User;
use App\Services\AssetSyncService;
use App\Services\FileUploadService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\TestCase;

class ResearchItemAssetLifecycleTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;
    protected Company $company;
    protected Category $category;
    protected Tag $tag;
    protected Asset $existingAsset;
    protected $assetSyncService;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test data
        $this->user = User::factory()->create();
        $this->company = Company::factory()->create();
        $this->category = Category::factory()->create();
        $this->tag = Tag::factory()->create();

        // Create an existing asset that can be attached
        $this->existingAsset = Asset::factory()->create([
            'company_id' => $this->company->id,
            'user_id' => $this->user->id,
            'is_orphaned' => false,
            'visibility' => 'public',
        ]);

        // Create actual file for the asset to make tests more realistic
        Storage::fake('public');
        $fakeFile = UploadedFile::fake()->create('existing-asset.pdf', 1024, 'application/pdf');
        $storedPath = $fakeFile->store('research-assets', 'public');
        $this->existingAsset->update(['file_path' => $storedPath]);

        // Mock AssetSyncService
        $this->assetSyncService = Mockery::mock(AssetSyncService::class);
        $this->app->instance(AssetSyncService::class, $this->assetSyncService);

        // Create and assign permissions
        $permissions = [
            'view research items',
            'create research items',
            'edit research items',
            'delete research items'
        ];

        foreach ($permissions as $permission) {
            \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $permission]);
        }

        $this->user->givePermissionTo($permissions);

        // Authenticate user
        $this->actingAs($this->user, 'sanctum');
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_can_complete_full_research_item_lifecycle_with_asset_attachments()
    {
        // === STEP 1: CREATE RESEARCH ITEM WITH EXISTING ASSET ===
        $this->assetSyncService->shouldReceive('syncAssetsForModel')->twice();
        $this->assetSyncService->shouldReceive('removeAssetsForModel')->once();

        $createResponse = $this->postJson('/api/research-items', [
            'title' => 'Lifecycle Test Research Item',
            'content' => 'Initial content for lifecycle testing',
            'company_id' => $this->company->id,
            'category_id' => $this->category->id,
            'tag_ids' => [$this->tag->id],
            'visibility' => 'public',
            'existing_files' => [$this->existingAsset->id], // Using asset-based attachment
        ]);

        // Debug: dump the actual response before asserting
        if ($createResponse->status() !== 201) {
            dump('Response status: ' . $createResponse->status());
            dump('Response body: ' . $createResponse->getContent());
        }

        $createResponse->assertStatus(201)
            ->assertJson([
                'title' => 'Lifecycle Test Research Item',
                'content' => 'Initial content for lifecycle testing',
                'visibility' => 'public',
                'company' => [
                    'id' => $this->company->id,
                    'name' => $this->company->name,
                ],
                'category' => [
                    'id' => $this->category->id,
                    'name' => $this->category->name,
                ],
                'user' => [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                ],
            ])
            ->assertJsonStructure([
                'id',
                'attachments' => [
                    '*' => ['id', 'name', 'file_name', 'mime_type', 'size', 'url'],
                ],
            ]);

        $createdItemId = $createResponse->json('id');
        $researchItem = ResearchItem::find($createdItemId);

        // Verify research item was created
        $this->assertDatabaseHas('research_items', [
            'id' => $createdItemId,
            'title' => 'Lifecycle Test Research Item',
            'user_id' => $this->user->id,
            'company_id' => $this->company->id,
        ]);

        // Verify attachment was properly linked
        $this->assertGreaterThan(0, $researchItem->getMedia('attachments')->count());

        // === STEP 2: RETRIEVE RESEARCH ITEM TO VERIFY ATTACHMENTS ===
        $getResponse = $this->getJson("/api/research-items/{$createdItemId}");

        $getResponse->assertStatus(200)
            ->assertJson([
                'id' => $createdItemId,
                'title' => 'Lifecycle Test Research Item',
            ])
            ->assertJsonStructure([
                'id',
                'title',
                'content',
                'visibility',
                'attachments' => [
                    '*' => ['id', 'name', 'file_name', 'mime_type', 'size', 'url'],
                ],
                'company',
                'category',
                'tags',
                'user',
                'created_at',
                'updated_at',
            ]);

        $attachments = $getResponse->json('attachments');
        $this->assertNotEmpty($attachments, 'Research item should have attachments after creation');

        // === STEP 3: EDIT RESEARCH ITEM ===
        $updateResponse = $this->putJson("/api/research-items/{$createdItemId}", [
            'title' => 'Updated Lifecycle Test Research Item',
            'content' => 'Updated content after editing',
            'company_id' => $this->company->id,
            'category_id' => $this->category->id,
            'tag_ids' => [$this->tag->id],
            'visibility' => 'private', // Change visibility
        ]);

        $updateResponse->assertStatus(200)
            ->assertJson([
                'id' => $createdItemId,
                'title' => 'Updated Lifecycle Test Research Item',
                'content' => 'Updated content after editing',
                'visibility' => 'private',
            ]);

        // Verify the research item was updated in database
        $this->assertDatabaseHas('research_items', [
            'id' => $createdItemId,
            'title' => 'Updated Lifecycle Test Research Item',
            'content' => 'Updated content after editing',
            'visibility' => 'private',
        ]);

        // Verify attachments are still present after edit
        $updatedItem = ResearchItem::find($createdItemId);
        $this->assertGreaterThan(0, $updatedItem->getMedia('attachments')->count());

        // === STEP 4: DELETE RESEARCH ITEM ===
        $deleteResponse = $this->deleteJson("/api/research-items/{$createdItemId}");

        $deleteResponse->assertStatus(204);

        // Verify research item was deleted from database
        $this->assertDatabaseMissing('research_items', ['id' => $createdItemId]);

        // Verify the asset still exists (should not be deleted with research item)
        $this->assertDatabaseHas('assets', ['id' => $this->existingAsset->id]);
    }

    /** @test */
    public function it_creates_research_item_with_multiple_existing_assets()
    {
        // Create another asset
        $secondAsset = Asset::factory()->create([
            'company_id' => $this->company->id,
            'user_id' => $this->user->id,
            'is_orphaned' => false,
            'visibility' => 'public',
        ]);

        $this->assetSyncService->shouldReceive('syncAssetsForModel')->once();

        $response = $this->postJson('/api/research-items', [
            'title' => 'Multiple Assets Research',
            'content' => 'Research with multiple existing assets',
            'company_id' => $this->company->id,
            'visibility' => 'public',
            'existing_files' => [$this->existingAsset->id, $secondAsset->id],
        ]);

        $response->assertStatus(201);

        $researchItem = ResearchItem::where('title', 'Multiple Assets Research')->first();
        $this->assertGreaterThanOrEqual(2, $researchItem->getMedia('attachments')->count());
    }

    /** @test */
    public function it_handles_nonexistent_asset_ids_gracefully()
    {
        $this->assetSyncService->shouldReceive('syncAssetsForModel')->once();

        $response = $this->postJson('/api/research-items', [
            'title' => 'Invalid Asset Research',
            'content' => 'Research with invalid asset ID',
            'company_id' => $this->company->id,
            'visibility' => 'public',
            'existing_files' => [99999], // Non-existent asset ID
        ]);

        // Should still create the research item successfully
        $response->assertStatus(201);

        $researchItem = ResearchItem::where('title', 'Invalid Asset Research')->first();
        $this->assertEquals(0, $researchItem->getMedia('attachments')->count());
    }

    /** @test */
    public function it_validates_existing_file_ids_are_integers()
    {
        $response = $this->postJson('/api/research-items', [
            'title' => 'Invalid Input Research',
            'content' => 'Research with invalid input',
            'company_id' => $this->company->id,
            'visibility' => 'public',
            'existing_files' => ['not-a-number'], // Invalid type
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['existing_files.0']);
    }

    /** @test */
    public function it_preserves_existing_attachments_during_edit_without_new_files()
    {
        // Create research item with existing asset
        $this->assetSyncService->shouldReceive('syncAssetsForModel')->once();

        $createResponse = $this->postJson('/api/research-items', [
            'title' => 'Preserve Attachments Test',
            'content' => 'Initial content',
            'company_id' => $this->company->id,
            'visibility' => 'public',
            'existing_files' => [$this->existingAsset->id],
        ]);

        $createdItemId = $createResponse->json('id');
        $originalAttachmentCount = ResearchItem::find($createdItemId)->getMedia('attachments')->count();

        // Edit without providing file-related fields
        $updateResponse = $this->putJson("/api/research-items/{$createdItemId}", [
            'title' => 'Updated Title Only',
            'content' => 'Updated content only',
            'company_id' => $this->company->id,
            'visibility' => 'public',
        ]);

        $updateResponse->assertStatus(200);

        // Verify attachments are preserved
        $updatedItem = ResearchItem::find($createdItemId);
        $this->assertEquals($originalAttachmentCount, $updatedItem->getMedia('attachments')->count());
    }

    /** @test */
    public function it_handles_asset_validation_during_creation()
    {
        $response = $this->postJson('/api/research-items', [
            'title' => 'Asset Validation Test',
            'content' => 'Testing asset validation',
            'company_id' => $this->company->id,
            'visibility' => 'public',
            'existing_files' => ['invalid'], // Non-numeric asset ID
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['existing_files.0']);
    }

    /** @test */
    public function it_can_get_available_assets_for_attachment()
    {
        $response = $this->getJson('/api/research-items/files/available');

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'name',
                    'file_name',
                    'url',
                    'size',
                    'mime_type',
                    'source_type',
                    'source_id',
                    'company',
                    'created_at',
                ]
            ]);

        // Verify our test asset is in the available assets
        $availableAssets = $response->json();
        $assetIds = collect($availableAssets)->pluck('id')->toArray();
        $this->assertContains($this->existingAsset->id, $assetIds);
    }

    /** @test */
    public function it_filters_out_orphaned_assets_from_available_list()
    {
        // Create an orphaned asset
        $orphanedAsset = Asset::factory()->create([
            'company_id' => $this->company->id,
            'user_id' => $this->user->id,
            'is_orphaned' => true, // This should be filtered out
            'visibility' => 'public',
        ]);

        $response = $this->getJson('/api/research-items/files/available');

        $response->assertStatus(200);

        $availableAssets = $response->json();
        $assetIds = collect($availableAssets)->pluck('id')->toArray();

        // Regular asset should be available
        $this->assertContains($this->existingAsset->id, $assetIds);

        // Orphaned asset should NOT be available
        $this->assertNotContains($orphanedAsset->id, $assetIds);
    }
}