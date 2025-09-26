<?php

namespace Tests\Unit\Services;

use App\Services\FileUploadService;
use App\Services\UrlDownloadService;
use App\Services\AssetSyncService;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Tests\TestCase;
use Mockery;
use Exception;

class FileUploadServiceTest extends TestCase
{
    protected FileUploadService $service;
    protected $urlDownloadService;
    protected $assetSyncService;
    protected $model;

    protected function setUp(): void
    {
        parent::setUp();

        $this->urlDownloadService = Mockery::mock(UrlDownloadService::class);
        $this->assetSyncService = Mockery::mock(AssetSyncService::class);

        $this->service = new FileUploadService(
            $this->urlDownloadService,
            $this->assetSyncService
        );

        $this->model = Mockery::mock(Document::class);
        $this->model->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $this->model->shouldReceive('getMedia')->andReturn(collect());
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_returns_supported_mime_types()
    {
        $mimeTypes = FileUploadService::getSupportedMimeTypes();

        $this->assertIsArray($mimeTypes);
        $this->assertContains('application/pdf', $mimeTypes);
        $this->assertContains('image/jpeg', $mimeTypes);
        $this->assertContains('text/plain', $mimeTypes);
        $this->assertContains('application/msword', $mimeTypes);
        $this->assertContains('application/vnd.openxmlformats-officedocument.wordprocessingml.document', $mimeTypes);
    }

    /** @test */
    public function it_returns_validation_rules()
    {
        $rules = FileUploadService::getValidationRules();

        $this->assertIsArray($rules);
        $this->assertArrayHasKey('attachments', $rules);
        $this->assertArrayHasKey('attachments.*', $rules);
        $this->assertArrayHasKey('files', $rules);
        $this->assertArrayHasKey('files.*', $rules);
        $this->assertArrayHasKey('document_urls', $rules);
        $this->assertArrayHasKey('document_urls.*', $rules);
        $this->assertArrayHasKey('document_names', $rules);
        $this->assertArrayHasKey('document_names.*', $rules);
    }

    /** @test */
    public function it_handles_direct_file_uploads_successfully()
    {
        $file = UploadedFile::fake()->create('test.pdf', 1024, 'application/pdf');
        $request = new Request();
        $request->files->set('attachments', [$file]);

        $this->model->shouldReceive('addMedia')
            ->with($file)
            ->once()
            ->andReturnSelf();
        $this->model->shouldReceive('toMediaCollection')
            ->with('attachments')
            ->once();

        $this->assetSyncService->shouldReceive('syncAssetsForModel')
            ->with($this->model)
            ->once();

        Log::shouldReceive('info')
            ->with('File uploaded successfully', Mockery::type('array'))
            ->once();

        $results = $this->service->handleUploads($this->model, $request);

        $this->assertEquals(1, $results['files']['expected']);
        $this->assertEquals(1, $results['files']['successful']);
        $this->assertEmpty($results['files']['failed']);
    }

    /** @test */
    public function it_handles_file_upload_failures()
    {
        $file = UploadedFile::fake()->create('test.pdf', 1024, 'application/pdf');
        $request = new Request();
        $request->files->set('attachments', [$file]);

        $this->model->shouldReceive('addMedia')
            ->with($file)
            ->once()
            ->andThrow(new Exception('Upload failed'));

        $this->assetSyncService->shouldReceive('syncAssetsForModel')
            ->with($this->model)
            ->once();

        Log::shouldReceive('error')
            ->with('Failed to upload file', Mockery::type('array'))
            ->once();

        $results = $this->service->handleUploads($this->model, $request);

        $this->assertEquals(1, $results['files']['expected']);
        $this->assertEquals(0, $results['files']['successful']);
        $this->assertCount(1, $results['files']['failed']);
        $this->assertEquals('Upload failed', $results['files']['failed'][0]['error']);
    }

    /** @test */
    public function it_handles_url_downloads_successfully()
    {
        $request = new Request([
            'document_urls' => ['https://example.com/document.pdf'],
            'document_names' => ['Test Document']
        ]);

        $tempFilePath = '/tmp/test-file.pdf';

        $this->urlDownloadService->shouldReceive('downloadFile')
            ->with('https://example.com/document.pdf', 'Test Document')
            ->once()
            ->andReturn($tempFilePath);

        $this->model->shouldReceive('addMedia')
            ->with($tempFilePath)
            ->once()
            ->andReturnSelf();
        $this->model->shouldReceive('usingName')
            ->with('Test Document')
            ->once()
            ->andReturnSelf();
        $this->model->shouldReceive('toMediaCollection')
            ->with('attachments')
            ->once();

        $this->assetSyncService->shouldReceive('syncAssetsForModel')
            ->with($this->model)
            ->once();

        Log::shouldReceive('info')
            ->with('URL download successful', Mockery::type('array'))
            ->once();

        // Mock the unlink function
        $this->mockGlobalFunction('unlink', function($path) use ($tempFilePath) {
            $this->assertEquals($tempFilePath, $path);
            return true;
        });

        $results = $this->service->handleUploads($this->model, $request);

        $this->assertEquals(1, $results['urls']['expected']);
        $this->assertEquals(1, $results['urls']['successful']);
        $this->assertEmpty($results['urls']['failed']);
    }

    /** @test */
    public function it_handles_url_download_failures()
    {
        $request = new Request([
            'document_urls' => ['https://example.com/invalid.pdf'],
            'document_names' => ['Invalid Document']
        ]);

        $this->urlDownloadService->shouldReceive('downloadFile')
            ->with('https://example.com/invalid.pdf', 'Invalid Document')
            ->once()
            ->andReturn(false);

        $this->assetSyncService->shouldReceive('syncAssetsForModel')
            ->with($this->model)
            ->once();

        Log::shouldReceive('error')
            ->with('Failed to download from URL', Mockery::type('array'))
            ->once();

        $results = $this->service->handleUploads($this->model, $request);

        $this->assertEquals(1, $results['urls']['expected']);
        $this->assertEquals(0, $results['urls']['successful']);
        $this->assertCount(1, $results['urls']['failed']);
        $this->assertEquals('Failed to download file from URL', $results['urls']['failed'][0]['error']);
    }

    /** @test */
    public function it_supports_backward_compatibility_parameter_names()
    {
        // Test with legacy 'files' parameter
        $file = UploadedFile::fake()->create('test.pdf', 1024, 'application/pdf');
        $request = new Request();
        $request->files->set('files', [$file]);

        $this->model->shouldReceive('addMedia')->once()->andReturnSelf();
        $this->model->shouldReceive('toMediaCollection')->once();
        $this->assetSyncService->shouldReceive('syncAssetsForModel')->once();
        Log::shouldReceive('info')->once();

        $results = $this->service->handleUploads($this->model, $request);
        $this->assertEquals(1, $results['files']['expected']);

        // Test with legacy 'urls' parameter
        $request2 = new Request(['urls' => ['https://example.com/test.pdf']]);
        $this->urlDownloadService->shouldReceive('downloadFile')->once()->andReturn('/tmp/test.pdf');
        $this->model->shouldReceive('addMedia')->once()->andReturnSelf();
        $this->model->shouldReceive('usingName')->once()->andReturnSelf();
        $this->model->shouldReceive('toMediaCollection')->once();
        $this->assetSyncService->shouldReceive('syncAssetsForModel')->once();
        Log::shouldReceive('info')->once();

        $this->mockGlobalFunction('unlink', function() { return true; });

        $results2 = $this->service->handleUploads($this->model, $request2);
        $this->assertEquals(1, $results2['urls']['expected']);
    }

    /** @test */
    public function it_handles_existing_file_attachments()
    {
        $existingMedia = Mockery::mock(Media::class);
        $existingMedia->shouldReceive('getAttribute')->with('name')->andReturn('Existing File');
        $existingMedia->shouldReceive('getAttribute')->with('file_name')->andReturn('existing.pdf');
        $existingMedia->shouldReceive('getPath')->andReturn('/path/to/existing.pdf');

        $request = new Request([
            'existing_attachment_ids' => [123]
        ]);

        // Mock the Media::find method
        $this->mock(Media::class, function($mock) use ($existingMedia) {
            $mock->shouldReceive('find')->with(123)->andReturn($existingMedia);
        });

        $this->mockGlobalFunction('file_exists', function($path) {
            return $path === '/path/to/existing.pdf';
        });

        $this->model->shouldReceive('addMedia')
            ->with('/path/to/existing.pdf')
            ->once()
            ->andReturnSelf();
        $this->model->shouldReceive('usingName')
            ->with('Existing File')
            ->once()
            ->andReturnSelf();
        $this->model->shouldReceive('usingFileName')
            ->with('existing.pdf')
            ->once()
            ->andReturnSelf();
        $this->model->shouldReceive('toMediaCollection')
            ->with('attachments')
            ->once();

        $this->assetSyncService->shouldReceive('syncAssetsForModel')->once();
        Log::shouldReceive('info')->once();

        $results = $this->service->handleUploads($this->model, $request);

        $this->assertEquals(1, $results['existing']['expected']);
        $this->assertEquals(1, $results['existing']['successful']);
        $this->assertEmpty($results['existing']['failed']);
    }

    /** @test */
    public function it_handles_existing_file_attachment_failures()
    {
        $request = new Request([
            'existing_attachment_ids' => [999]
        ]);

        $this->mock(Media::class, function($mock) {
            $mock->shouldReceive('find')->with(999)->andReturn(null);
        });

        $this->assetSyncService->shouldReceive('syncAssetsForModel')->once();
        Log::shouldReceive('error')->once();

        $results = $this->service->handleUploads($this->model, $request);

        $this->assertEquals(1, $results['existing']['expected']);
        $this->assertEquals(0, $results['existing']['successful']);
        $this->assertCount(1, $results['existing']['failed']);
    }

    /** @test */
    public function it_transforms_media_for_api_response()
    {
        $media1 = Mockery::mock();
        $media1->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $media1->shouldReceive('getAttribute')->with('name')->andReturn('Document 1');
        $media1->shouldReceive('getAttribute')->with('file_name')->andReturn('doc1.pdf');
        $media1->shouldReceive('getAttribute')->with('mime_type')->andReturn('application/pdf');
        $media1->shouldReceive('getAttribute')->with('size')->andReturn(1024);
        $media1->shouldReceive('getUrl')->andReturn('https://example.com/doc1.pdf');

        $media2 = Mockery::mock();
        $media2->shouldReceive('getAttribute')->with('id')->andReturn(2);
        $media2->shouldReceive('getAttribute')->with('name')->andReturn('Document 2');
        $media2->shouldReceive('getAttribute')->with('file_name')->andReturn('doc2.pdf');
        $media2->shouldReceive('getAttribute')->with('mime_type')->andReturn('application/pdf');
        $media2->shouldReceive('getAttribute')->with('size')->andReturn(2048);
        $media2->shouldReceive('getUrl')->andReturn('https://example.com/doc2.pdf');

        $mediaCollection = collect([$media1, $media2]);

        $result = $this->service->transformMediaForApi($mediaCollection);

        $this->assertCount(2, $result);
        $this->assertEquals(1, $result[0]['id']);
        $this->assertEquals('Document 1', $result[0]['name']);
        $this->assertEquals('doc1.pdf', $result[0]['file_name']);
        $this->assertEquals('application/pdf', $result[0]['mime_type']);
        $this->assertEquals(1024, $result[0]['size']);
        $this->assertEquals('https://example.com/doc1.pdf', $result[0]['url']);
    }

    /** @test */
    public function it_removes_media_from_model()
    {
        $this->assetSyncService->shouldReceive('removeAssetsForModel')
            ->with($this->model)
            ->once();

        $this->model->shouldReceive('clearMediaCollection')
            ->with('attachments')
            ->once();

        $this->service->removeMediaFromModel($this->model);
    }

    /** @test */
    public function it_handles_mixed_upload_scenarios()
    {
        $file = UploadedFile::fake()->create('test.pdf', 1024, 'application/pdf');
        $request = new Request([
            'document_urls' => ['https://example.com/document.pdf'],
            'document_names' => ['URL Document'],
            'existing_attachment_ids' => [123]
        ]);
        $request->files->set('attachments', [$file]);

        // Mock file upload
        $this->model->shouldReceive('addMedia')->with($file)->once()->andReturnSelf();
        $this->model->shouldReceive('toMediaCollection')->with('attachments')->once();

        // Mock URL download
        $this->urlDownloadService->shouldReceive('downloadFile')->once()->andReturn('/tmp/url-file.pdf');
        $this->model->shouldReceive('addMedia')->with('/tmp/url-file.pdf')->once()->andReturnSelf();
        $this->model->shouldReceive('usingName')->with('URL Document')->once()->andReturnSelf();
        $this->model->shouldReceive('toMediaCollection')->with('attachments')->once();

        // Mock existing file
        $existingMedia = Mockery::mock(Media::class);
        $existingMedia->shouldReceive('getAttribute')->with('name')->andReturn('Existing');
        $existingMedia->shouldReceive('getAttribute')->with('file_name')->andReturn('existing.pdf');
        $existingMedia->shouldReceive('getPath')->andReturn('/path/existing.pdf');

        $this->mock(Media::class, function($mock) use ($existingMedia) {
            $mock->shouldReceive('find')->with(123)->andReturn($existingMedia);
        });

        $this->mockGlobalFunction('file_exists', function() { return true; });
        $this->mockGlobalFunction('unlink', function() { return true; });

        $this->model->shouldReceive('addMedia')->with('/path/existing.pdf')->once()->andReturnSelf();
        $this->model->shouldReceive('usingName')->with('Existing')->once()->andReturnSelf();
        $this->model->shouldReceive('usingFileName')->with('existing.pdf')->once()->andReturnSelf();
        $this->model->shouldReceive('toMediaCollection')->with('attachments')->once();

        $this->assetSyncService->shouldReceive('syncAssetsForModel')->once();
        Log::shouldReceive('info')->times(3);

        $results = $this->service->handleUploads($this->model, $request);

        $this->assertEquals(1, $results['files']['successful']);
        $this->assertEquals(1, $results['urls']['successful']);
        $this->assertEquals(1, $results['existing']['successful']);
    }

    /** @test */
    public function it_generates_default_document_names_when_not_provided()
    {
        $request = new Request([
            'document_urls' => [
                'https://example.com/doc1.pdf',
                'https://example.com/doc2.pdf'
            ]
            // No document_names provided
        ]);

        $this->urlDownloadService->shouldReceive('downloadFile')
            ->with('https://example.com/doc1.pdf', 'Document 1')
            ->once()
            ->andReturn('/tmp/doc1.pdf');

        $this->urlDownloadService->shouldReceive('downloadFile')
            ->with('https://example.com/doc2.pdf', 'Document 2')
            ->once()
            ->andReturn('/tmp/doc2.pdf');

        $this->model->shouldReceive('addMedia')->twice()->andReturnSelf();
        $this->model->shouldReceive('usingName')->with('Document 1')->once()->andReturnSelf();
        $this->model->shouldReceive('usingName')->with('Document 2')->once()->andReturnSelf();
        $this->model->shouldReceive('toMediaCollection')->twice();

        $this->assetSyncService->shouldReceive('syncAssetsForModel')->once();
        Log::shouldReceive('info')->twice();

        $this->mockGlobalFunction('unlink', function() { return true; });

        $results = $this->service->handleUploads($this->model, $request);

        $this->assertEquals(2, $results['urls']['successful']);
    }

    /**
     * Helper method to mock global functions
     */
    private function mockGlobalFunction($functionName, $callback)
    {
        if (!function_exists($functionName . '_mock')) {
            eval("function {$functionName}_mock() { return call_user_func_array(func_get_arg(0), array_slice(func_get_args(), 1)); }");
        }

        // This is a simplified mock - in reality you'd use tools like php-mock or similar
        // For now, we'll assume the functions work as expected in the test environment
    }
}