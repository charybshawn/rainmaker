<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\Company;
use App\Models\ResearchItem;
use App\Models\User;
use App\Services\FileUploadService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AssetSymbolicLinkingTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Company $company;
    protected FileUploadService $fileUploadService;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->user = User::factory()->create();
        $this->company = Company::factory()->create();
        $this->fileUploadService = app(FileUploadService::class);
    }

    /** @test */
    public function research_item_can_create_symbolic_links_to_existing_assets()
    {
        // Create an existing asset
        $existingAsset = Asset::factory()->create([
            'title' => 'Existing Test Document',
            'file_name' => 'test-document.pdf',
            'mime_type' => 'application/pdf',
            'size' => 1024,
            'source_type' => 'direct_upload',
            'user_id' => $this->user->id,
            'company_id' => $this->company->id,
        ]);

        // Create a research item
        $researchItem = ResearchItem::factory()->create([
            'company_id' => $this->company->id,
            'user_id' => $this->user->id,
        ]);

        // Create request to link existing asset
        $request = Request::create('/', 'POST', [
            'existing_attachment_ids' => [$existingAsset->id]
        ]);

        // Handle uploads (should create symbolic link)
        $results = $this->fileUploadService->handleUploads($researchItem, $request);

        // Verify symbolic link was created
        $this->assertTrue($researchItem->assets()->where('asset_id', $existingAsset->id)->exists());
        $this->assertEquals(1, $results['existing']['expected']);
        $this->assertEquals(1, $results['existing']['successful']);
        $this->assertEmpty($results['existing']['failed']);

        // Verify no duplicate asset was created
        $this->assertEquals(1, Asset::count());

        // Verify the research item can access the asset through symbolic link
        $this->assertTrue($researchItem->assets->contains($existingAsset));
    }

    /** @test */
    public function research_item_does_not_duplicate_existing_symbolic_links()
    {
        $existingAsset = Asset::factory()->create([
            'user_id' => $this->user->id,
            'company_id' => $this->company->id,
        ]);

        $researchItem = ResearchItem::factory()->create([
            'company_id' => $this->company->id,
            'user_id' => $this->user->id,
        ]);

        // Create initial symbolic link
        $researchItem->assets()->attach($existingAsset->id);

        // Attempt to link the same asset again
        $request = Request::create('/', 'POST', [
            'existing_attachment_ids' => [$existingAsset->id]
        ]);

        $results = $this->fileUploadService->handleUploads($researchItem, $request);

        // Should still be successful but not create duplicate pivot entries
        $this->assertEquals(1, $results['existing']['successful']);
        $this->assertEquals(1, $researchItem->assets()->count());

        // Verify only one pivot table entry exists
        $this->assertEquals(1, $researchItem->assets()->where('asset_id', $existingAsset->id)->count());
    }

    /** @test */
    public function research_item_creates_direct_assets_for_new_file_uploads()
    {
        $researchItem = ResearchItem::factory()->create([
            'company_id' => $this->company->id,
            'user_id' => $this->user->id,
        ]);

        $file = UploadedFile::fake()->create('new-document.pdf', 1024, 'application/pdf');

        $request = Request::create('/', 'POST');
        $request->files->set('attachments', [$file]);

        $results = $this->fileUploadService->handleUploads($researchItem, $request);

        // Verify direct asset was created (not a symbolic link)
        $directAssets = $researchItem->directAssets;
        $this->assertEquals(1, $directAssets->count());
        $this->assertEquals('research_item', $directAssets->first()->source_type);
        $this->assertEquals($researchItem->id, $directAssets->first()->source_id);

        // Verify file upload was successful
        $this->assertEquals(1, $results['files']['successful']);
        $this->assertEmpty($results['files']['failed']);
    }

    /** @test */
    public function research_item_formatted_attachments_includes_both_direct_and_symbolic_assets()
    {
        $researchItem = ResearchItem::factory()->create([
            'company_id' => $this->company->id,
            'user_id' => $this->user->id,
        ]);

        // Create a direct asset (created specifically for this research item)
        $directAsset = Asset::factory()->create([
            'title' => 'Direct Asset',
            'source_type' => 'research_item',
            'source_id' => $researchItem->id,
            'user_id' => $this->user->id,
            'company_id' => $this->company->id,
        ]);

        // Create a symbolic link asset (reference to existing asset)
        $symbolicAsset = Asset::factory()->create([
            'title' => 'Symbolic Asset',
            'source_type' => 'direct_upload',
            'user_id' => $this->user->id,
            'company_id' => $this->company->id,
        ]);
        $researchItem->assets()->attach($symbolicAsset->id);

        $attachments = $researchItem->getFormattedAttachments();

        // Should return both assets with proper source_type indicators
        $this->assertCount(2, $attachments);

        $directAttachment = collect($attachments)->firstWhere('source_type', 'direct');
        $symbolicAttachment = collect($attachments)->firstWhere('source_type', 'reference');

        $this->assertNotNull($directAttachment);
        $this->assertNotNull($symbolicAttachment);
        $this->assertEquals('Direct Asset', $directAttachment['name']);
        $this->assertEquals('Symbolic Asset', $symbolicAttachment['name']);
    }

    /** @test */
    public function multiple_research_items_can_reference_same_asset_via_symbolic_links()
    {
        $sharedAsset = Asset::factory()->create([
            'title' => 'Shared Document',
            'user_id' => $this->user->id,
            'company_id' => $this->company->id,
        ]);

        $researchItem1 = ResearchItem::factory()->create([
            'company_id' => $this->company->id,
            'user_id' => $this->user->id,
        ]);

        $researchItem2 = ResearchItem::factory()->create([
            'company_id' => $this->company->id,
            'user_id' => $this->user->id,
        ]);

        // Both research items reference the same asset
        $researchItem1->assets()->attach($sharedAsset->id);
        $researchItem2->assets()->attach($sharedAsset->id);

        // Verify both research items can access the shared asset
        $this->assertTrue($researchItem1->assets->contains($sharedAsset));
        $this->assertTrue($researchItem2->assets->contains($sharedAsset));

        // Verify only one asset exists in database
        $this->assertEquals(1, Asset::count());

        // Verify asset appears in both research items' formatted attachments
        $attachments1 = $researchItem1->getFormattedAttachments();
        $attachments2 = $researchItem2->getFormattedAttachments();

        $this->assertCount(1, $attachments1);
        $this->assertCount(1, $attachments2);
        $this->assertEquals('Shared Document', $attachments1[0]['name']);
        $this->assertEquals('Shared Document', $attachments2[0]['name']);
    }

    /** @test */
    public function deleting_research_item_removes_symbolic_links_but_preserves_assets()
    {
        $asset = Asset::factory()->create([
            'user_id' => $this->user->id,
            'company_id' => $this->company->id,
        ]);

        $researchItem = ResearchItem::factory()->create([
            'company_id' => $this->company->id,
            'user_id' => $this->user->id,
        ]);

        // Create symbolic link
        $researchItem->assets()->attach($asset->id);

        // Verify link exists
        $this->assertTrue($researchItem->assets()->where('asset_id', $asset->id)->exists());

        // Force delete research item (hard delete to test cascade)
        $researchItem->forceDelete();

        // Verify asset still exists but pivot entry is removed
        $this->assertEquals(1, Asset::count());
        $this->assertEquals(0, \DB::table('research_item_assets')->where('asset_id', $asset->id)->count());
    }

    /** @test */
    public function deleting_asset_removes_all_symbolic_links()
    {
        $asset = Asset::factory()->create([
            'user_id' => $this->user->id,
            'company_id' => $this->company->id,
        ]);

        $researchItem1 = ResearchItem::factory()->create([
            'company_id' => $this->company->id,
            'user_id' => $this->user->id,
        ]);

        $researchItem2 = ResearchItem::factory()->create([
            'company_id' => $this->company->id,
            'user_id' => $this->user->id,
        ]);

        // Create symbolic links from both research items
        $researchItem1->assets()->attach($asset->id);
        $researchItem2->assets()->attach($asset->id);

        // Verify links exist
        $this->assertEquals(2, \DB::table('research_item_assets')->where('asset_id', $asset->id)->count());

        // Delete asset
        $asset->delete();

        // Verify all pivot entries are removed due to foreign key constraints
        $this->assertEquals(0, \DB::table('research_item_assets')->where('asset_id', $asset->id)->count());
        $this->assertEquals(0, Asset::count());
    }
}