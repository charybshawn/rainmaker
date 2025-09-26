<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\ResearchItem;
use App\Models\Company;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use App\Models\Asset;
use App\Services\FileUploadService;
use App\Services\UrlDownloadService;
use App\Services\AssetSyncService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Tests\TestCase;
use Mockery;

class FileUploadWorkflowTest extends TestCase
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
    public function complete_document_workflow_with_files()
    {
        Log::spy();

        // Step 1: Create document with mixed file sources
        $directFile = UploadedFile::fake()->create('direct-upload.pdf', 1024, 'application/pdf');
        $urlToDownload = 'https://example.com/external-document.pdf';
        $tempFile = '/tmp/external-document.pdf';

        $this->urlDownloadService->shouldReceive('downloadFile')
            ->with($urlToDownload, 'External Document')
            ->once()
            ->andReturn($tempFile);

        $this->assetSyncService->shouldReceive('syncAssetsForModel')->times(3); // create, update, delete

        file_put_contents($tempFile, 'fake external document content');

        $createResponse = $this->postJson('/api/documents', [
            'title' => 'Comprehensive Document Test',
            'description' => 'Testing complete workflow',
            'company_id' => $this->company->id,
            'category_id' => $this->category->id,
            'tag_ids' => [$this->tag->id],
            'visibility' => 'public',
            'attachments' => [$directFile],
            'document_urls' => [$urlToDownload],
            'document_names' => ['External Document']
        ]);

        $createResponse->assertStatus(201);
        $documentId = $createResponse->json('id');

        // Verify document was created with attachments
        $document = Document::find($documentId);
        $this->assertNotNull($document);
        $this->assertCount(2, $document->getMedia('attachments'));

        // Verify asset sync was called
        Log::shouldHaveReceived('info')
            ->with('File uploaded successfully', Mockery::type('array'))
            ->atLeast()->once();

        // Step 2: Retrieve document and verify attachment format
        $showResponse = $this->getJson("/api/documents/{$documentId}");
        $showResponse->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'title',
                'attachments' => [
                    '*' => ['id', 'name', 'file_name', 'mime_type', 'size', 'url']
                ]
            ]);

        $attachments = $showResponse->json('attachments');
        $this->assertCount(2, $attachments);

        // Step 3: Update document (without changing files)
        $updateResponse = $this->putJson("/api/documents/{$documentId}", [
            'title' => 'Updated Document Title',
            'description' => 'Updated description',
            'company_id' => $this->company->id,
            'category_id' => $this->category->id,
            'visibility' => 'private'
        ]);

        $updateResponse->assertStatus(200)
            ->assertJson([
                'title' => 'Updated Document Title',
                'visibility' => 'private'
            ]);

        // Verify files are still there
        $document->refresh();
        $this->assertCount(2, $document->getMedia('attachments'));

        // Step 4: Delete document and verify cleanup
        $this->assetSyncService->shouldReceive('removeAssetsForModel')
            ->with($document)
            ->once();

        $deleteResponse = $this->deleteJson("/api/documents/{$documentId}");
        $deleteResponse->assertStatus(204);

        // Verify document is deleted
        $this->assertDatabaseMissing('documents', ['id' => $documentId]);

        // Clean up temp file
        if (file_exists($tempFile)) {
            unlink($tempFile);
        }
    }

    /** @test */
    public function complete_research_item_workflow_with_file_cloning()
    {
        // Step 1: Create source research item with files
        $sourceFile = UploadedFile::fake()->create('source-research.pdf', 1024, 'application/pdf');

        $this->assetSyncService->shouldReceive('syncAssetsForModel')->times(3); // source, clone, delete

        $sourceResponse = $this->postJson('/api/research-items', [
            'title' => 'Source Research Item',
            'content' => 'Original research content',
            'company_id' => $this->company->id,
            'visibility' => 'public',
            'attachments' => [$sourceFile]
        ]);

        $sourceResponse->assertStatus(201);
        $sourceId = $sourceResponse->json('id');

        $sourceItem = ResearchItem::find($sourceId);
        $sourceMediaId = $sourceItem->getMedia('attachments')->first()->id;

        // Step 2: Create new research item by cloning files from source
        $newFile = UploadedFile::fake()->create('new-research.pdf', 512, 'application/pdf');

        $cloneResponse = $this->postJson('/api/research-items', [
            'title' => 'Cloned Research Item',
            'content' => 'Research with cloned and new files',
            'company_id' => $this->company->id,
            'visibility' => 'public',
            'attachments' => [$newFile],
            'existing_attachment_ids' => [$sourceMediaId]
        ]);

        $cloneResponse->assertStatus(201);
        $cloneId = $cloneResponse->json('id');

        // Verify cloned item has both files
        $clonedItem = ResearchItem::find($cloneId);
        $this->assertCount(2, $clonedItem->getMedia('attachments'));

        // Step 3: Verify file integrity and metadata
        $clonedAttachments = $clonedItem->getFormattedAttachments();
        $this->assertCount(2, $clonedAttachments);

        $fileNames = collect($clonedAttachments)->pluck('file_name')->toArray();
        $this->assertContains('new-research.pdf', $fileNames);

        // Step 4: Test list endpoint with attachments
        $listResponse = $this->getJson('/api/research-items');
        $listResponse->assertStatus(200);

        $items = $listResponse->json('data');
        $this->assertCount(2, $items); // source + cloned

        foreach ($items as $item) {
            $this->assertIsArray($item['attachments']);
        }

        // Step 5: Clean up - delete both items
        $this->assetSyncService->shouldReceive('removeAssetsForModel')->twice();

        $this->deleteJson("/api/research-items/{$cloneId}")->assertStatus(204);
        $this->deleteJson("/api/research-items/{$sourceId}")->assertStatus(204);
    }

    /** @test */
    public function error_handling_and_recovery_workflow()
    {
        Log::spy();

        // Test scenario with mixed success/failure uploads
        $validFile = UploadedFile::fake()->create('valid.pdf', 1024, 'application/pdf');
        $invalidUrl = 'https://nonexistent.example.com/fake.pdf';
        $validUrl = 'https://example.com/valid.pdf';
        $tempFile = '/tmp/valid-download.pdf';

        // Mock download service: one fails, one succeeds
        $this->urlDownloadService->shouldReceive('downloadFile')
            ->with($invalidUrl, 'Failed Document')
            ->once()
            ->andReturn(false); // Simulate failure

        $this->urlDownloadService->shouldReceive('downloadFile')
            ->with($validUrl, 'Success Document')
            ->once()
            ->andReturn($tempFile);

        $this->assetSyncService->shouldReceive('syncAssetsForModel')->once();

        file_put_contents($tempFile, 'valid file content');

        $response = $this->postJson('/api/documents', [
            'title' => 'Mixed Results Document',
            'description' => 'Testing error handling',
            'company_id' => $this->company->id,
            'visibility' => 'public',
            'attachments' => [$validFile],
            'document_urls' => [$invalidUrl, $validUrl],
            'document_names' => ['Failed Document', 'Success Document']
        ]);

        // Should still create document despite partial failures
        $response->assertStatus(201);

        $document = Document::where('title', 'Mixed Results Document')->first();
        $this->assertNotNull($document);

        // Should have 2 attachments: 1 direct upload + 1 successful URL download
        $this->assertCount(2, $document->getMedia('attachments'));

        // Verify error logging occurred
        Log::shouldHaveReceived('error')
            ->with('Failed to download from URL', Mockery::type('array'))
            ->once();

        Log::shouldHaveReceived('info')
            ->with('File uploaded successfully', Mockery::type('array'))
            ->once();

        Log::shouldHaveReceived('info')
            ->with('URL download successful', Mockery::type('array'))
            ->once();

        // Clean up
        if (file_exists($tempFile)) {
            unlink($tempFile);
        }
    }

    /** @test */
    public function performance_workflow_with_multiple_files()
    {
        // Test uploading multiple files of different types
        $files = [
            UploadedFile::fake()->create('document1.pdf', 1024, 'application/pdf'),
            UploadedFile::fake()->create('spreadsheet.xlsx', 512, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'),
            UploadedFile::fake()->create('image.jpg', 256, 'image/jpeg'),
            UploadedFile::fake()->create('text.txt', 128, 'text/plain'),
            UploadedFile::fake()->create('presentation.pptx', 2048, 'application/vnd.openxmlformats-officedocument.presentationml.presentation')
        ];

        $urls = [
            'https://example.com/doc1.pdf',
            'https://example.com/doc2.pdf',
            'https://example.com/doc3.pdf'
        ];

        $names = ['URL Doc 1', 'URL Doc 2', 'URL Doc 3'];

        // Mock all URL downloads to succeed
        foreach ($urls as $index => $url) {
            $tempFile = "/tmp/url-download-{$index}.pdf";
            file_put_contents($tempFile, "content for {$names[$index]}");

            $this->urlDownloadService->shouldReceive('downloadFile')
                ->with($url, $names[$index])
                ->once()
                ->andReturn($tempFile);
        }

        $this->assetSyncService->shouldReceive('syncAssetsForModel')->once();

        $startTime = microtime(true);

        $response = $this->postJson('/api/research-items', [
            'title' => 'Performance Test Item',
            'content' => 'Testing performance with many files',
            'company_id' => $this->company->id,
            'visibility' => 'public',
            'attachments' => $files,
            'document_urls' => $urls,
            'document_names' => $names
        ]);

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $response->assertStatus(201);

        // Should have processed all 8 files (5 direct + 3 URL)
        $researchItem = ResearchItem::where('title', 'Performance Test Item')->first();
        $this->assertCount(8, $researchItem->getMedia('attachments'));

        // Verify response time is reasonable (less than 5 seconds for test)
        $this->assertLessThan(5.0, $executionTime, 'File upload workflow took too long');

        // Clean up temp files
        for ($i = 0; $i < 3; $i++) {
            $tempFile = "/tmp/url-download-{$i}.pdf";
            if (file_exists($tempFile)) {
                unlink($tempFile);
            }
        }
    }

    /** @test */
    public function asset_synchronization_workflow()
    {
        // Mock the asset sync service to track calls
        $syncCallCount = 0;
        $this->assetSyncService->shouldReceive('syncAssetsForModel')
            ->andReturnUsing(function($model) use (&$syncCallCount) {
                $syncCallCount++;
                return true;
            });

        $file = UploadedFile::fake()->create('sync-test.pdf', 1024, 'application/pdf');

        // Step 1: Create document - should trigger sync
        $response = $this->postJson('/api/documents', [
            'title' => 'Asset Sync Test',
            'company_id' => $this->company->id,
            'visibility' => 'public',
            'attachments' => [$file]
        ]);

        $response->assertStatus(201);
        $documentId = $response->json('id');

        $this->assertEquals(1, $syncCallCount);

        // Step 2: Delete document - should trigger asset removal
        $this->assetSyncService->shouldReceive('removeAssetsForModel')
            ->once()
            ->andReturnUsing(function($model) {
                // Simulate asset removal logic
                return true;
            });

        $deleteResponse = $this->deleteJson("/api/documents/{$documentId}");
        $deleteResponse->assertStatus(204);
    }

    /** @test */
    public function backward_compatibility_workflow()
    {
        // Test that all legacy parameter names still work
        $legacyFile = UploadedFile::fake()->create('legacy.pdf', 1024, 'application/pdf');
        $legacyUrl = 'https://example.com/legacy.pdf';
        $tempFile = '/tmp/legacy-download.pdf';

        $this->urlDownloadService->shouldReceive('downloadFile')
            ->once()
            ->andReturn($tempFile);

        $this->assetSyncService->shouldReceive('syncAssetsForModel')->once();

        file_put_contents($tempFile, 'legacy content');

        // Use all legacy parameter names
        $response = $this->postJson('/api/research-items', [
            'title' => 'Legacy Compatibility Test',
            'content' => 'Testing backward compatibility',
            'company_id' => $this->company->id,
            'visibility' => 'public',
            'files' => [$legacyFile], // Legacy name
            'urls' => [$legacyUrl], // Legacy name
            'existing_files' => [], // Legacy name
            'existing_file_ids' => [] // Legacy name
        ]);

        $response->assertStatus(201);

        $researchItem = ResearchItem::where('title', 'Legacy Compatibility Test')->first();
        $this->assertCount(2, $researchItem->getMedia('attachments'));

        // Clean up
        if (file_exists($tempFile)) {
            unlink($tempFile);
        }
    }

    /** @test */
    public function file_validation_edge_cases_workflow()
    {
        // Test various edge cases for file validation

        // Edge case 1: File exactly at size limit (should pass)
        $maxSizeFile = UploadedFile::fake()->create('max-size.pdf', 10240, 'application/pdf'); // Exactly 10MB

        $this->assetSyncService->shouldReceive('syncAssetsForModel')->once();

        $response = $this->postJson('/api/documents', [
            'title' => 'Max Size Test',
            'company_id' => $this->company->id,
            'visibility' => 'public',
            'attachments' => [$maxSizeFile]
        ]);

        $response->assertStatus(201);

        // Edge case 2: Multiple files with one being invalid (should fail validation)
        $validFile = UploadedFile::fake()->create('valid.pdf', 1024, 'application/pdf');
        $invalidFile = UploadedFile::fake()->create('invalid.exe', 1024, 'application/x-executable');

        $response2 = $this->postJson('/api/documents', [
            'title' => 'Mixed Validation Test',
            'company_id' => $this->company->id,
            'visibility' => 'public',
            'attachments' => [$validFile, $invalidFile]
        ]);

        $response2->assertStatus(422)
            ->assertJsonValidationErrors(['attachments.1']);

        // Edge case 3: Empty arrays (should pass)
        $this->assetSyncService->shouldReceive('syncAssetsForModel')->once();

        $response3 = $this->postJson('/api/documents', [
            'title' => 'Empty Arrays Test',
            'company_id' => $this->company->id,
            'visibility' => 'public',
            'attachments' => [],
            'document_urls' => [],
            'document_names' => []
        ]);

        $response3->assertStatus(201);

        $document = Document::where('title', 'Empty Arrays Test')->first();
        $this->assertCount(0, $document->getMedia('attachments'));
    }

    /** @test */
    public function media_conversion_and_thumbnails_workflow()
    {
        // Test that media conversions are properly set up
        $imageFile = UploadedFile::fake()->image('test-image.jpg', 800, 600);

        $this->assetSyncService->shouldReceive('syncAssetsForModel')->once();

        $response = $this->postJson('/api/documents', [
            'title' => 'Media Conversion Test',
            'company_id' => $this->company->id,
            'visibility' => 'public',
            'attachments' => [$imageFile]
        ]);

        $response->assertStatus(201);

        $document = Document::where('title', 'Media Conversion Test')->first();
        $media = $document->getMedia('attachments')->first();

        // Verify media was created
        $this->assertNotNull($media);
        $this->assertEquals('image/jpeg', $media->mime_type);

        // Test formatted attachments include all necessary fields
        $formattedAttachments = $document->getFormattedAttachments();
        $this->assertCount(1, $formattedAttachments);

        $attachment = $formattedAttachments[0];
        $this->assertArrayHasKey('id', $attachment);
        $this->assertArrayHasKey('name', $attachment);
        $this->assertArrayHasKey('file_name', $attachment);
        $this->assertArrayHasKey('mime_type', $attachment);
        $this->assertArrayHasKey('size', $attachment);
        $this->assertArrayHasKey('url', $attachment);

        $this->assertEquals('image/jpeg', $attachment['mime_type']);
    }
}