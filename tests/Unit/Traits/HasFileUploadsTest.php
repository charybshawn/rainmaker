<?php

namespace Tests\Unit\Traits;

use App\Traits\HasFileUploads;
use App\Services\FileUploadService;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\HasMedia;
use Tests\TestCase;
use Illuminate\Database\Eloquent\Model;
use Mockery;

class HasFileUploadsTest extends TestCase
{
    protected $testModel;
    protected $fileUploadService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fileUploadService = Mockery::mock(FileUploadService::class);
        $this->app->instance(FileUploadService::class, $this->fileUploadService);

        $this->testModel = new class extends Model implements HasMedia {
            use InteractsWithMedia, HasFileUploads;

            protected $table = 'test_models';
            protected $fillable = ['name'];

            public function getMedia(string $collectionName = 'default', array $filters = [])
            {
                return collect([
                    (object) [
                        'id' => 1,
                        'name' => 'Test Document',
                        'file_name' => 'test.pdf',
                        'mime_type' => 'application/pdf',
                        'size' => 1024,
                    ],
                    (object) [
                        'id' => 2,
                        'name' => 'Test Image',
                        'file_name' => 'test.jpg',
                        'mime_type' => 'image/jpeg',
                        'size' => 2048,
                    ]
                ]);
            }

            public function addMediaCollection($name)
            {
                return new class {
                    public function acceptsMimeTypes($mimeTypes)
                    {
                        return $this;
                    }
                };
            }

            public function addMediaConversion($name)
            {
                return new class {
                    public function width($width) { return $this; }
                    public function height($height) { return $this; }
                    public function sharpen($amount) { return $this; }
                    public function performOnCollections($collections) { return $this; }
                    public function nonQueued() { return $this; }
                };
            }
        };
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_registers_media_collections_with_supported_mime_types()
    {
        // Mock FileUploadService::getSupportedMimeTypes
        FileUploadService::shouldReceive('getSupportedMimeTypes')
            ->once()
            ->andReturn([
                'application/pdf',
                'image/jpeg',
                'image/png',
                'text/plain'
            ]);

        // Create a spy to capture the method calls
        $collectionSpy = Mockery::spy('stdClass');
        $collectionSpy->shouldReceive('acceptsMimeTypes')
            ->with([
                'application/pdf',
                'image/jpeg',
                'image/png',
                'text/plain'
            ])
            ->once()
            ->andReturnSelf();

        // Override addMediaCollection to return our spy
        $model = Mockery::mock($this->testModel)->makePartial();
        $model->shouldReceive('addMediaCollection')
            ->with('attachments')
            ->once()
            ->andReturn($collectionSpy);

        $model->registerMediaCollections();
    }

    /** @test */
    public function it_registers_media_conversions_for_thumbnails()
    {
        $media = Mockery::mock(Media::class);

        // Create spies to capture the method chain calls
        $conversionSpy = Mockery::spy('stdClass');
        $conversionSpy->shouldReceive('width')->with(300)->once()->andReturnSelf();
        $conversionSpy->shouldReceive('height')->with(200)->once()->andReturnSelf();
        $conversionSpy->shouldReceive('sharpen')->with(10)->once()->andReturnSelf();
        $conversionSpy->shouldReceive('performOnCollections')->with('attachments')->once()->andReturnSelf();
        $conversionSpy->shouldReceive('nonQueued')->once()->andReturnSelf();

        // Override addMediaConversion to return our spy
        $model = Mockery::mock($this->testModel)->makePartial();
        $model->shouldReceive('addMediaConversion')
            ->with('thumb')
            ->once()
            ->andReturn($conversionSpy);

        $model->registerMediaConversions($media);
    }

    /** @test */
    public function it_registers_media_conversions_without_media_parameter()
    {
        $conversionSpy = Mockery::spy('stdClass');
        $conversionSpy->shouldReceive('width')->with(300)->once()->andReturnSelf();
        $conversionSpy->shouldReceive('height')->with(200)->once()->andReturnSelf();
        $conversionSpy->shouldReceive('sharpen')->with(10)->once()->andReturnSelf();
        $conversionSpy->shouldReceive('performOnCollections')->with('attachments')->once()->andReturnSelf();
        $conversionSpy->shouldReceive('nonQueued')->once()->andReturnSelf();

        $model = Mockery::mock($this->testModel)->makePartial();
        $model->shouldReceive('addMediaConversion')
            ->with('thumb')
            ->once()
            ->andReturn($conversionSpy);

        $model->registerMediaConversions();
    }

    /** @test */
    public function it_gets_formatted_attachments_using_file_upload_service()
    {
        $expectedResult = [
            [
                'id' => 1,
                'name' => 'Test Document',
                'file_name' => 'test.pdf',
                'mime_type' => 'application/pdf',
                'size' => 1024,
                'url' => 'https://example.com/test.pdf'
            ],
            [
                'id' => 2,
                'name' => 'Test Image',
                'file_name' => 'test.jpg',
                'mime_type' => 'image/jpeg',
                'size' => 2048,
                'url' => 'https://example.com/test.jpg'
            ]
        ];

        $mediaCollection = $this->testModel->getMedia('attachments');

        $this->fileUploadService->shouldReceive('transformMediaForApi')
            ->with($mediaCollection)
            ->once()
            ->andReturn($expectedResult);

        $result = $this->testModel->getFormattedAttachments();

        $this->assertEquals($expectedResult, $result);
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
    }

    /** @test */
    public function it_gets_formatted_attachments_returns_empty_array_when_no_media()
    {
        $emptyModel = new class extends Model implements HasMedia {
            use InteractsWithMedia, HasFileUploads;

            public function getMedia(string $collectionName = 'default', array $filters = [])
            {
                return collect();
            }
        };

        $this->fileUploadService->shouldReceive('transformMediaForApi')
            ->with(Mockery::type('Illuminate\Support\Collection'))
            ->once()
            ->andReturn([]);

        $result = $emptyModel->getFormattedAttachments();

        $this->assertEquals([], $result);
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    /** @test */
    public function it_calls_file_upload_service_from_app_container()
    {
        // Ensure the service is resolved from the container
        $this->fileUploadService->shouldReceive('transformMediaForApi')
            ->once()
            ->andReturn([]);

        $result = $this->testModel->getFormattedAttachments();

        $this->assertIsArray($result);
    }

    /** @test */
    public function it_passes_correct_collection_name_to_get_media()
    {
        $model = Mockery::mock($this->testModel)->makePartial();
        $model->shouldReceive('getMedia')
            ->with('attachments')
            ->once()
            ->andReturn(collect());

        $this->fileUploadService->shouldReceive('transformMediaForApi')
            ->once()
            ->andReturn([]);

        $model->getFormattedAttachments();
    }

    /** @test */
    public function it_handles_media_conversion_configuration_correctly()
    {
        $media = Mockery::mock(Media::class);

        // Test that all conversion settings are applied in correct order
        $conversionChain = Mockery::mock('stdClass');
        $conversionChain->shouldReceive('width')->with(300)->once()->andReturnSelf();
        $conversionChain->shouldReceive('height')->with(200)->once()->andReturnSelf();
        $conversionChain->shouldReceive('sharpen')->with(10)->once()->andReturnSelf();
        $conversionChain->shouldReceive('performOnCollections')->with('attachments')->once()->andReturnSelf();
        $conversionChain->shouldReceive('nonQueued')->once()->andReturnSelf();

        $model = Mockery::mock($this->testModel)->makePartial();
        $model->shouldReceive('addMediaConversion')
            ->with('thumb')
            ->once()
            ->andReturn($conversionChain);

        $model->registerMediaConversions($media);

        // Verify the conversion was set up with correct parameters
        $this->assertTrue(true); // If we get here without exceptions, the test passes
    }

    /** @test */
    public function it_integrates_with_spatie_media_library()
    {
        // Test that the trait properly integrates with Spatie MediaLibrary
        $this->assertTrue(method_exists($this->testModel, 'registerMediaCollections'));
        $this->assertTrue(method_exists($this->testModel, 'registerMediaConversions'));
        $this->assertTrue(method_exists($this->testModel, 'getFormattedAttachments'));

        // Test that it implements HasMedia interface
        $this->assertInstanceOf(HasMedia::class, $this->testModel);

        // Test that it uses InteractsWithMedia trait
        $this->assertTrue(method_exists($this->testModel, 'getMedia'));
    }

    /** @test */
    public function it_uses_correct_collection_name_for_media_registration()
    {
        FileUploadService::shouldReceive('getSupportedMimeTypes')->once()->andReturn([]);

        $model = Mockery::mock($this->testModel)->makePartial();
        $model->shouldReceive('addMediaCollection')
            ->with('attachments') // Verify correct collection name
            ->once()
            ->andReturnSelf();

        $model->shouldReceive('acceptsMimeTypes')
            ->once()
            ->andReturnSelf();

        $model->registerMediaCollections();
    }

    /** @test */
    public function it_configures_thumbnail_conversion_with_correct_collection()
    {
        $conversionChain = Mockery::mock('stdClass');
        $conversionChain->shouldReceive('width')->once()->andReturnSelf();
        $conversionChain->shouldReceive('height')->once()->andReturnSelf();
        $conversionChain->shouldReceive('sharpen')->once()->andReturnSelf();
        $conversionChain->shouldReceive('performOnCollections')
            ->with('attachments') // Verify it targets the correct collection
            ->once()
            ->andReturnSelf();
        $conversionChain->shouldReceive('nonQueued')->once()->andReturnSelf();

        $model = Mockery::mock($this->testModel)->makePartial();
        $model->shouldReceive('addMediaConversion')
            ->with('thumb')
            ->once()
            ->andReturn($conversionChain);

        $model->registerMediaConversions();
    }
}