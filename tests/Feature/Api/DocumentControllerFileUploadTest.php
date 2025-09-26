<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\Company;
use App\Models\Document;
use App\Models\Tag;
use App\Models\User;
use App\Services\AssetSyncService;
use App\Services\UrlDownloadService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\TestCase;

class DocumentControllerFileUploadTest extends TestCase
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
    public function it_creates_document_with_file_attachments()
    {
        $file = UploadedFile::fake()->create('test-document.pdf', 1024, 'application/pdf');

        $this->assetSyncService->shouldReceive('syncAssetsForModel')->once();

        $response = $this->postJson('/api/documents', [
            'title' => 'Test Document',
            'description' => 'Test document description',
            'company_id' => $this->company->id,
            'category_id' => $this->category->id,
            'tag_ids' => [$this->tag->id],
            'visibility' => 'public',
            'attachments' => [$file],
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'title' => 'Test Document',
                'description' => 'Test document description',
                'visibility' => 'public',
                'company' => [
                    'id' => $this->company->id,
                    'name' => $this->company->name,
                    'ticker' => $this->company->ticker,
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

        $this->assertDatabaseHas('documents', [
            'title' => 'Test Document',
            'user_id' => $this->user->id,
            'company_id' => $this->company->id,
        ]);

        // Verify file was uploaded
        $document = Document::where('title', 'Test Document')->first();
        $this->assertCount(1, $document->getMedia('attachments'));
    }

    /** @test */
    public function it_creates_document_with_url_downloads()
    {
        $testUrl = 'https://example.com/test-document.pdf';
        $tempFile = '/tmp/downloaded-file.pdf';

        $this->urlDownloadService->shouldReceive('downloadFile')
            ->with($testUrl, 'Downloaded Document')
            ->once()
            ->andReturn($tempFile);

        $this->assetSyncService->shouldReceive('syncAssetsForModel')->once();

        // Mock file system for temp file
        file_put_contents($tempFile, 'fake pdf content');

        $response = $this->postJson('/api/documents', [
            'title' => 'Test Document',
            'description' => 'Test document with URL',
            'company_id' => $this->company->id,
            'visibility' => 'public',
            'document_urls' => [$testUrl],
            'document_names' => ['Downloaded Document'],
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'title' => 'Test Document',
                'description' => 'Test document with URL',
            ])
            ->assertJsonStructure([
                'id',
                'attachments' => [
                    '*' => ['id', 'name', 'file_name', 'mime_type', 'size', 'url'],
                ],
            ]);

        // Clean up temp file
        if (file_exists($tempFile)) {
            unlink($tempFile);
        }
    }

    /** @test */
    public function it_creates_document_with_mixed_file_sources()
    {
        $file = UploadedFile::fake()->create('uploaded-file.pdf', 512, 'application/pdf');
        $testUrl = 'https://example.com/downloaded-file.pdf';
        $tempFile = '/tmp/downloaded-file.pdf';

        $this->urlDownloadService->shouldReceive('downloadFile')
            ->once()
            ->andReturn($tempFile);

        $this->assetSyncService->shouldReceive('syncAssetsForModel')->once();

        file_put_contents($tempFile, 'fake pdf content');

        $response = $this->postJson('/api/documents', [
            'title' => 'Mixed Sources Document',
            'description' => 'Document with multiple file sources',
            'company_id' => $this->company->id,
            'visibility' => 'public',
            'attachments' => [$file],
            'document_urls' => [$testUrl],
            'document_names' => ['URL Document'],
        ]);

        $response->assertStatus(201);

        $document = Document::where('title', 'Mixed Sources Document')->first();
        $this->assertCount(2, $document->getMedia('attachments'));

        // Clean up
        if (file_exists($tempFile)) {
            unlink($tempFile);
        }
    }

    /** @test */
    public function it_supports_legacy_files_parameter()
    {
        $file = UploadedFile::fake()->create('legacy-file.pdf', 1024, 'application/pdf');

        $this->assetSyncService->shouldReceive('syncAssetsForModel')->once();

        $response = $this->postJson('/api/documents', [
            'title' => 'Legacy Test',
            'description' => 'Test legacy files parameter',
            'company_id' => $this->company->id,
            'visibility' => 'public',
            'files' => [$file], // Using legacy parameter name
        ]);

        $response->assertStatus(201);

        $document = Document::where('title', 'Legacy Test')->first();
        $this->assertCount(1, $document->getMedia('attachments'));
    }

    /** @test */
    public function it_handles_file_upload_validation_errors()
    {
        $invalidFile = UploadedFile::fake()->create('test.exe', 1024, 'application/x-executable');

        $response = $this->postJson('/api/documents', [
            'title' => 'Test Document',
            'company_id' => $this->company->id,
            'visibility' => 'public',
            'attachments' => [$invalidFile],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['attachments.0']);
    }

    /** @test */
    public function it_handles_oversized_file_validation_errors()
    {
        $largeFile = UploadedFile::fake()->create('large-file.pdf', 15000, 'application/pdf'); // > 10MB

        $response = $this->postJson('/api/documents', [
            'title' => 'Test Document',
            'company_id' => $this->company->id,
            'visibility' => 'public',
            'attachments' => [$largeFile],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['attachments.0']);
    }

    /** @test */
    public function it_handles_invalid_url_validation_errors()
    {
        $response = $this->postJson('/api/documents', [
            'title' => 'Test Document',
            'company_id' => $this->company->id,
            'visibility' => 'public',
            'document_urls' => ['not-a-valid-url'],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['document_urls.0']);
    }

    /** @test */
    public function it_handles_url_download_failures_gracefully()
    {
        $testUrl = 'https://example.com/nonexistent.pdf';

        $this->urlDownloadService->shouldReceive('downloadFile')
            ->with($testUrl, 'Failed Document')
            ->once()
            ->andReturn(false);

        $this->assetSyncService->shouldReceive('syncAssetsForModel')->once();

        $response = $this->postJson('/api/documents', [
            'title' => 'Test Document',
            'company_id' => $this->company->id,
            'visibility' => 'public',
            'document_urls' => [$testUrl],
            'document_names' => ['Failed Document'],
        ]);

        // Should still create document even if URL download fails
        $response->assertStatus(201);

        $document = Document::where('title', 'Test Document')->first();
        $this->assertCount(0, $document->getMedia('attachments'));
    }

    /** @test */
    public function it_creates_document_without_any_attachments()
    {
        $this->assetSyncService->shouldReceive('syncAssetsForModel')->once();

        $response = $this->postJson('/api/documents', [
            'title' => 'No Attachments Document',
            'description' => 'Document without any files',
            'company_id' => $this->company->id,
            'visibility' => 'public',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'title' => 'No Attachments Document',
                'attachments' => [],
            ]);

        $document = Document::where('title', 'No Attachments Document')->first();
        $this->assertCount(0, $document->getMedia('attachments'));
    }

    /** @test */
    public function it_updates_document_without_changing_attachments()
    {
        $document = Document::factory()->create([
            'user_id' => $this->user->id,
            'company_id' => $this->company->id,
        ]);

        $response = $this->putJson("/api/documents/{$document->id}", [
            'title' => 'Updated Title',
            'description' => 'Updated description',
            'company_id' => $this->company->id,
            'visibility' => 'private',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'id' => $document->id,
                'title' => 'Updated Title',
                'description' => 'Updated description',
                'visibility' => 'private',
            ]);

        $this->assertDatabaseHas('documents', [
            'id' => $document->id,
            'title' => 'Updated Title',
            'visibility' => 'private',
        ]);
    }

    /** @test */
    public function it_prevents_updating_other_users_documents()
    {
        $otherUser = User::factory()->create();
        $document = Document::factory()->create([
            'user_id' => $otherUser->id,
            'company_id' => $this->company->id,
        ]);

        $response = $this->putJson("/api/documents/{$document->id}", [
            'title' => 'Unauthorized Update',
            'company_id' => $this->company->id,
            'visibility' => 'public',
        ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function it_deletes_document_and_removes_media_files()
    {
        $document = Document::factory()->create([
            'user_id' => $this->user->id,
            'company_id' => $this->company->id,
        ]);

        // Add a file to the document
        $file = UploadedFile::fake()->create('test.pdf', 1024, 'application/pdf');
        $document->addMedia($file)->toMediaCollection('attachments');

        $this->assetSyncService->shouldReceive('removeAssetsForModel')
            ->with($document)
            ->once();

        $response = $this->deleteJson("/api/documents/{$document->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('documents', ['id' => $document->id]);
    }

    /** @test */
    public function it_prevents_deleting_other_users_documents()
    {
        $otherUser = User::factory()->create();
        $document = Document::factory()->create([
            'user_id' => $otherUser->id,
            'company_id' => $this->company->id,
        ]);

        $response = $this->deleteJson("/api/documents/{$document->id}");

        $response->assertStatus(403);

        $this->assertDatabaseHas('documents', ['id' => $document->id]);
    }

    /** @test */
    public function it_shows_document_with_formatted_attachments()
    {
        $document = Document::factory()->create([
            'user_id' => $this->user->id,
            'company_id' => $this->company->id,
            'category_id' => $this->category->id,
        ]);

        $document->tags()->attach($this->tag);

        // Add a file to the document
        $file = UploadedFile::fake()->create('test.pdf', 1024, 'application/pdf');
        $document->addMedia($file)->toMediaCollection('attachments');

        $response = $this->getJson("/api/documents/{$document->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $document->id,
                'title' => $document->title,
            ])
            ->assertJsonStructure([
                'id',
                'title',
                'description',
                'visibility',
                'company' => ['id', 'name', 'ticker'],
                'category' => ['id', 'name', 'color'],
                'tags' => [
                    '*' => ['id', 'name', 'color'],
                ],
                'attachments' => [
                    '*' => ['id', 'name', 'file_name', 'mime_type', 'size', 'url'],
                ],
                'user' => ['id', 'name'],
                'created_at',
                'updated_at',
            ]);
    }

    /** @test */
    public function it_validates_required_fields_for_document_creation()
    {
        $response = $this->postJson('/api/documents', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'company_id', 'visibility']);
    }

    /** @test */
    public function it_validates_company_exists_for_document_creation()
    {
        $response = $this->postJson('/api/documents', [
            'title' => 'Test Document',
            'company_id' => 99999, // Non-existent company
            'visibility' => 'public',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['company_id']);
    }

    /** @test */
    public function it_validates_visibility_options()
    {
        $response = $this->postJson('/api/documents', [
            'title' => 'Test Document',
            'company_id' => $this->company->id,
            'visibility' => 'invalid_visibility',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['visibility']);
    }

    /** @test */
    public function it_validates_tag_ids_exist()
    {
        $response = $this->postJson('/api/documents', [
            'title' => 'Test Document',
            'company_id' => $this->company->id,
            'visibility' => 'public',
            'tag_ids' => [99999], // Non-existent tag
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['tag_ids.0']);
    }

    /** @test */
    public function it_creates_document_with_all_supported_file_types()
    {
        $files = [
            UploadedFile::fake()->create('test.pdf', 512, 'application/pdf'),
            UploadedFile::fake()->create('test.docx', 512, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'),
            UploadedFile::fake()->create('test.xlsx', 512, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'),
            UploadedFile::fake()->create('test.jpg', 512, 'image/jpeg'),
            UploadedFile::fake()->create('test.png', 512, 'image/png'),
            UploadedFile::fake()->create('test.txt', 512, 'text/plain'),
        ];

        $this->assetSyncService->shouldReceive('syncAssetsForModel')->once();

        $response = $this->postJson('/api/documents', [
            'title' => 'Multi-type Document',
            'company_id' => $this->company->id,
            'visibility' => 'public',
            'attachments' => $files,
        ]);

        $response->assertStatus(201);

        $document = Document::where('title', 'Multi-type Document')->first();
        $this->assertCount(6, $document->getMedia('attachments'));
    }
}
