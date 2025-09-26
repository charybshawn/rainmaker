<?php

namespace Tests\Feature\Api;

use App\Models\ResearchItem;
use App\Models\Company;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use App\Services\FileUploadService;
use App\Services\UrlDownloadService;
use App\Services\AssetSyncService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Tests\TestCase;
use Mockery;

class ResearchItemControllerFileUploadTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;
    protected Company $company;
    protected Category $category;
    protected Tag $tag;
    protected $urlDownloadService;
    protected $assetSyncService;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test data
        $this->user = User::factory()->create();
        $this->company = Company::factory()->create();
        $this->category = Category::factory()->create();
        $this->tag = Tag::factory()->create();

        // Mock external services
        $this->urlDownloadService = Mockery::mock(UrlDownloadService::class);
        $this->assetSyncService = Mockery::mock(AssetSyncService::class);

        $this->app->instance(UrlDownloadService::class, $this->urlDownloadService);
        $this->app->instance(AssetSyncService::class, $this->assetSyncService);

        // Set up storage fake
        Storage::fake('public');

        // Authenticate user
        $this->actingAs($this->user, 'sanctum');
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_creates_research_item_with_file_attachments()
    {
        $file = UploadedFile::fake()->create('research-document.pdf', 1024, 'application/pdf');

        $this->assetSyncService->shouldReceive('syncAssetsForModel')->once();

        $response = $this->postJson('/api/research-items', [
            'title' => 'Test Research Item',
            'content' => 'Research item content',
            'company_id' => $this->company->id,
            'category_id' => $this->category->id,
            'tag_ids' => [$this->tag->id],
            'visibility' => 'public',
            'attachments' => [$file]
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'title' => 'Test Research Item',
                'content' => 'Research item content',
                'visibility' => 'public',
                'company' => [
                    'id' => $this->company->id,
                    'name' => $this->company->name,
                    'ticker' => $this->company->ticker_symbol
                ],
                'category' => [
                    'id' => $this->category->id,
                    'name' => $this->category->name
                ],
                'user' => [
                    'id' => $this->user->id,
                    'name' => $this->user->name
                ]
            ])
            ->assertJsonStructure([
                'id',
                'attachments' => [
                    '*' => ['id', 'name', 'file_name', 'mime_type', 'size', 'url']
                ]
            ]);

        $this->assertDatabaseHas('research_items', [
            'title' => 'Test Research Item',
            'user_id' => $this->user->id,
            'company_id' => $this->company->id
        ]);

        // Verify file was uploaded
        $researchItem = ResearchItem::where('title', 'Test Research Item')->first();
        $this->assertCount(1, $researchItem->getMedia('attachments'));
    }

    /** @test */
    public function it_creates_research_item_with_url_downloads()
    {
        $testUrl = 'https://example.com/research-report.pdf';
        $tempFile = '/tmp/downloaded-research.pdf';

        $this->urlDownloadService->shouldReceive('downloadFile')
            ->with($testUrl, 'Research Report')
            ->once()
            ->andReturn($tempFile);

        $this->assetSyncService->shouldReceive('syncAssetsForModel')->once();

        // Mock file system for temp file
        file_put_contents($tempFile, 'fake pdf content');

        $response = $this->postJson('/api/research-items', [
            'title' => 'Research with URL',
            'content' => 'Research item with URL download',
            'company_id' => $this->company->id,
            'visibility' => 'public',
            'document_urls' => [$testUrl],
            'document_names' => ['Research Report']
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'title' => 'Research with URL',
                'content' => 'Research item with URL download'
            ])
            ->assertJsonStructure([
                'id',
                'attachments' => [
                    '*' => ['id', 'name', 'file_name', 'mime_type', 'size', 'url']
                ]
            ]);

        // Clean up temp file
        if (file_exists($tempFile)) {
            unlink($tempFile);
        }
    }

    /** @test */
    public function it_creates_research_item_with_existing_file_attachments()
    {
        // Create another research item with a file to clone from
        $sourceItem = ResearchItem::factory()->create(['user_id' => $this->user->id]);
        $file = UploadedFile::fake()->create('source-file.pdf', 1024, 'application/pdf');
        $mediaItem = $sourceItem->addMedia($file)->toMediaCollection('attachments');

        $this->assetSyncService->shouldReceive('syncAssetsForModel')->once();

        $response = $this->postJson('/api/research-items', [
            'title' => 'Cloned Research Item',
            'content' => 'Research item with existing files',
            'company_id' => $this->company->id,
            'visibility' => 'public',
            'existing_attachment_ids' => [$mediaItem->id]
        ]);

        $response->assertStatus(201);

        $newItem = ResearchItem::where('title', 'Cloned Research Item')->first();
        $this->assertCount(1, $newItem->getMedia('attachments'));
    }

    /** @test */
    public function it_supports_backward_compatibility_parameters()
    {
        $file = UploadedFile::fake()->create('legacy-file.pdf', 1024, 'application/pdf');
        $testUrl = 'https://example.com/legacy-url.pdf';

        $this->urlDownloadService->shouldReceive('downloadFile')->once()->andReturn('/tmp/temp.pdf');
        $this->assetSyncService->shouldReceive('syncAssetsForModel')->once();

        file_put_contents('/tmp/temp.pdf', 'fake content');

        $response = $this->postJson('/api/research-items', [
            'title' => 'Legacy Parameters Test',
            'content' => 'Testing backward compatibility',
            'company_id' => $this->company->id,
            'visibility' => 'public',
            'files' => [$file], // Legacy parameter
            'urls' => [$testUrl] // Legacy parameter
        ]);

        $response->assertStatus(201);

        $researchItem = ResearchItem::where('title', 'Legacy Parameters Test')->first();
        $this->assertCount(2, $researchItem->getMedia('attachments'));

        // Clean up
        if (file_exists('/tmp/temp.pdf')) {
            unlink('/tmp/temp.pdf');
        }
    }

    /** @test */
    public function it_handles_mixed_upload_scenarios_for_research_items()
    {
        $file = UploadedFile::fake()->create('direct-upload.pdf', 512, 'application/pdf');
        $testUrl = 'https://example.com/url-download.pdf';
        $tempFile = '/tmp/url-download.pdf';

        // Create source item for existing file test
        $sourceItem = ResearchItem::factory()->create(['user_id' => $this->user->id]);
        $existingFile = UploadedFile::fake()->create('existing-file.pdf', 256, 'application/pdf');
        $existingMediaItem = $sourceItem->addMedia($existingFile)->toMediaCollection('attachments');

        $this->urlDownloadService->shouldReceive('downloadFile')
            ->once()
            ->andReturn($tempFile);

        $this->assetSyncService->shouldReceive('syncAssetsForModel')->once();

        file_put_contents($tempFile, 'fake pdf content');

        $response = $this->postJson('/api/research-items', [
            'title' => 'Mixed Sources Research',
            'content' => 'Research with all file source types',
            'company_id' => $this->company->id,
            'visibility' => 'public',
            'attachments' => [$file],
            'document_urls' => [$testUrl],
            'document_names' => ['URL Document'],
            'existing_attachment_ids' => [$existingMediaItem->id]
        ]);

        $response->assertStatus(201);

        $researchItem = ResearchItem::where('title', 'Mixed Sources Research')->first();
        $this->assertCount(3, $researchItem->getMedia('attachments'));

        // Clean up
        if (file_exists($tempFile)) {
            unlink($tempFile);
        }
    }

    /** @test */
    public function it_handles_file_upload_failures_gracefully()
    {
        $testUrl = 'https://example.com/nonexistent.pdf';

        $this->urlDownloadService->shouldReceive('downloadFile')
            ->with($testUrl, 'Nonexistent File')
            ->once()
            ->andReturn(false); // Simulate download failure

        $this->assetSyncService->shouldReceive('syncAssetsForModel')->once();

        $response = $this->postJson('/api/research-items', [
            'title' => 'Failed Upload Test',
            'content' => 'Research item with failed URL',
            'company_id' => $this->company->id,
            'visibility' => 'public',
            'document_urls' => [$testUrl],
            'document_names' => ['Nonexistent File']
        ]);

        // Should still create research item even if URL download fails
        $response->assertStatus(201);

        $researchItem = ResearchItem::where('title', 'Failed Upload Test')->first();
        $this->assertCount(0, $researchItem->getMedia('attachments'));
    }

    /** @test */
    public function it_validates_file_types_for_research_items()
    {
        $invalidFile = UploadedFile::fake()->create('malicious.exe', 1024, 'application/x-executable');

        $response = $this->postJson('/api/research-items', [
            'title' => 'Invalid File Test',
            'content' => 'Testing invalid file type',
            'company_id' => $this->company->id,
            'visibility' => 'public',
            'attachments' => [$invalidFile]
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['attachments.0']);
    }

    /** @test */
    public function it_validates_file_sizes_for_research_items()
    {
        $largeFile = UploadedFile::fake()->create('huge-file.pdf', 15000, 'application/pdf'); // > 10MB

        $response = $this->postJson('/api/research-items', [
            'title' => 'Large File Test',
            'content' => 'Testing large file upload',
            'company_id' => $this->company->id,
            'visibility' => 'public',
            'attachments' => [$largeFile]
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['attachments.0']);
    }

    /** @test */
    public function it_updates_research_item_without_changing_attachments()
    {
        $researchItem = ResearchItem::factory()->create([
            'user_id' => $this->user->id,
            'company_id' => $this->company->id
        ]);

        $response = $this->putJson("/api/research-items/{$researchItem->id}", [
            'title' => 'Updated Research Title',
            'content' => 'Updated research content',
            'company_id' => $this->company->id,
            'visibility' => 'private'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'id' => $researchItem->id,
                'title' => 'Updated Research Title',
                'content' => 'Updated research content',
                'visibility' => 'private'
            ]);
    }

    /** @test */
    public function it_shows_research_item_with_formatted_attachments()
    {
        $researchItem = ResearchItem::factory()->create([
            'user_id' => $this->user->id,
            'company_id' => $this->company->id,
            'category_id' => $this->category->id
        ]);

        $researchItem->tags()->attach($this->tag);

        // Add a file to the research item
        $file = UploadedFile::fake()->create('research-file.pdf', 1024, 'application/pdf');
        $researchItem->addMedia($file)->toMediaCollection('attachments');

        $response = $this->getJson("/api/research-items/{$researchItem->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'title',
                'content',
                'visibility',
                'company' => ['id', 'name', 'ticker'],
                'category' => ['id', 'name', 'color'],
                'tags' => [
                    '*' => ['id', 'name', 'color']
                ],
                'attachments' => [
                    '*' => ['id', 'name', 'file_name', 'mime_type', 'size', 'url']
                ],
                'user' => ['id', 'name'],
                'created_at',
                'updated_at'
            ]);
    }

    /** @test */
    public function it_deletes_research_item_and_removes_media_files()
    {
        $researchItem = ResearchItem::factory()->create([
            'user_id' => $this->user->id,
            'company_id' => $this->company->id
        ]);

        // Add a file to the research item
        $file = UploadedFile::fake()->create('test-research.pdf', 1024, 'application/pdf');
        $researchItem->addMedia($file)->toMediaCollection('attachments');

        $this->assetSyncService->shouldReceive('removeAssetsForModel')
            ->with($researchItem)
            ->once();

        $response = $this->deleteJson("/api/research-items/{$researchItem->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('research_items', ['id' => $researchItem->id]);
    }

    /** @test */
    public function it_lists_research_items_with_attachment_info()
    {
        $researchItem = ResearchItem::factory()->create([
            'user_id' => $this->user->id,
            'company_id' => $this->company->id,
            'visibility' => 'public'
        ]);

        // Add a file
        $file = UploadedFile::fake()->create('list-test.pdf', 1024, 'application/pdf');
        $researchItem->addMedia($file)->toMediaCollection('attachments');

        $response = $this->getJson('/api/research-items');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'content',
                        'attachments' => [
                            '*' => ['id', 'name', 'file_name', 'mime_type', 'size', 'url']
                        ]
                    ]
                ]
            ]);
    }

    /** @test */
    public function it_filters_research_items_by_company_with_attachments()
    {
        $anotherCompany = Company::factory()->create();

        $item1 = ResearchItem::factory()->create([
            'user_id' => $this->user->id,
            'company_id' => $this->company->id,
            'visibility' => 'public',
            'title' => 'Company 1 Research'
        ]);

        $item2 = ResearchItem::factory()->create([
            'user_id' => $this->user->id,
            'company_id' => $anotherCompany->id,
            'visibility' => 'public',
            'title' => 'Company 2 Research'
        ]);

        // Add files to both
        $file1 = UploadedFile::fake()->create('company1.pdf', 512, 'application/pdf');
        $file2 = UploadedFile::fake()->create('company2.pdf', 512, 'application/pdf');

        $item1->addMedia($file1)->toMediaCollection('attachments');
        $item2->addMedia($file2)->toMediaCollection('attachments');

        $response = $this->getJson("/api/research-items?company_id={$this->company->id}");

        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('Company 1 Research', $data[0]['title']);
        $this->assertCount(1, $data[0]['attachments']);
    }

    /** @test */
    public function it_handles_nonexistent_existing_attachment_ids()
    {
        $this->assetSyncService->shouldReceive('syncAssetsForModel')->once();

        $response = $this->postJson('/api/research-items', [
            'title' => 'Invalid Existing Files',
            'content' => 'Testing invalid existing file IDs',
            'company_id' => $this->company->id,
            'visibility' => 'public',
            'existing_attachment_ids' => [99999] // Non-existent ID
        ]);

        // Should still create the research item
        $response->assertStatus(201);

        $researchItem = ResearchItem::where('title', 'Invalid Existing Files')->first();
        $this->assertCount(0, $researchItem->getMedia('attachments'));
    }

    /** @test */
    public function it_creates_research_item_with_all_supported_file_types()
    {
        $files = [
            UploadedFile::fake()->create('research.pdf', 512, 'application/pdf'),
            UploadedFile::fake()->create('analysis.xlsx', 512, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'),
            UploadedFile::fake()->create('notes.txt', 512, 'text/plain'),
            UploadedFile::fake()->create('chart.png', 512, 'image/png'),
            UploadedFile::fake()->create('data.csv', 512, 'text/csv')
        ];

        $this->assetSyncService->shouldReceive('syncAssetsForModel')->once();

        $response = $this->postJson('/api/research-items', [
            'title' => 'Multi-format Research',
            'content' => 'Research with various file formats',
            'company_id' => $this->company->id,
            'visibility' => 'public',
            'attachments' => $files
        ]);

        $response->assertStatus(201);

        $researchItem = ResearchItem::where('title', 'Multi-format Research')->first();
        $this->assertCount(5, $researchItem->getMedia('attachments'));
    }

    /** @test */
    public function it_handles_empty_attachment_arrays()
    {
        $this->assetSyncService->shouldReceive('syncAssetsForModel')->once();

        $response = $this->postJson('/api/research-items', [
            'title' => 'Empty Attachments Test',
            'content' => 'Research with empty attachment arrays',
            'company_id' => $this->company->id,
            'visibility' => 'public',
            'attachments' => [],
            'document_urls' => [],
            'document_names' => [],
            'existing_attachment_ids' => []
        ]);

        $response->assertStatus(201);

        $researchItem = ResearchItem::where('title', 'Empty Attachments Test')->first();
        $this->assertCount(0, $researchItem->getMedia('attachments'));
    }

    /** @test */
    public function it_prevents_accessing_private_research_items_from_other_users()
    {
        $otherUser = User::factory()->create();
        $privateItem = ResearchItem::factory()->create([
            'user_id' => $otherUser->id,
            'company_id' => $this->company->id,
            'visibility' => 'private'
        ]);

        $response = $this->getJson('/api/research-items');

        $data = $response->json('data');
        $itemIds = collect($data)->pluck('id')->toArray();

        $this->assertNotContains($privateItem->id, $itemIds);
    }
}