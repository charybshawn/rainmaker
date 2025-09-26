<?php

namespace Tests\Unit\Http\Requests;

use App\Http\Requests\FileUploadRequest;
use App\Services\FileUploadService;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class FileUploadRequestTest extends TestCase
{
    use WithFaker;

    protected FileUploadRequest $request;

    protected function setUp(): void
    {
        parent::setUp();
        $this->request = new FileUploadRequest();
    }

    /** @test */
    public function it_authorizes_all_users()
    {
        $this->assertTrue($this->request->authorize());
    }

    /** @test */
    public function it_returns_validation_rules_from_file_upload_service()
    {
        $expectedRules = FileUploadService::getValidationRules();
        $actualRules = $this->request->rules();

        $this->assertEquals($expectedRules, $actualRules);
        $this->assertArrayHasKey('attachments', $actualRules);
        $this->assertArrayHasKey('files', $actualRules);
        $this->assertArrayHasKey('document_urls', $actualRules);
        $this->assertArrayHasKey('document_names', $actualRules);
    }

    /** @test */
    public function it_validates_attachment_files_successfully()
    {
        $file = UploadedFile::fake()->create('test.pdf', 1024, 'application/pdf');

        $data = ['attachments' => [$file]];
        $validator = Validator::make($data, $this->request->rules());

        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function it_validates_files_array_successfully()
    {
        $files = [
            UploadedFile::fake()->create('test1.pdf', 1024, 'application/pdf'),
            UploadedFile::fake()->create('test2.docx', 512, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'),
        ];

        $data = ['files' => $files];
        $validator = Validator::make($data, $this->request->rules());

        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function it_validates_document_urls_successfully()
    {
        $data = [
            'document_urls' => [
                'https://example.com/document.pdf',
                'https://test.com/file.docx'
            ]
        ];

        $validator = Validator::make($data, $this->request->rules());

        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function it_validates_document_names_successfully()
    {
        $data = [
            'document_names' => [
                'Important Document',
                'Another File'
            ]
        ];

        $validator = Validator::make($data, $this->request->rules());

        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function it_validates_all_supported_mime_types()
    {
        $supportedTypes = [
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'ppt' => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'txt' => 'text/plain',
            'csv' => 'text/csv',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'svg' => 'image/svg+xml'
        ];

        foreach ($supportedTypes as $extension => $mimeType) {
            $file = UploadedFile::fake()->create("test.{$extension}", 1024, $mimeType);
            $data = ['attachments' => [$file]];
            $validator = Validator::make($data, $this->request->rules());

            $this->assertFalse($validator->fails(), "Failed validation for {$extension} file");
        }
    }

    /** @test */
    public function it_fails_validation_for_unsupported_file_types()
    {
        $file = UploadedFile::fake()->create('test.exe', 1024, 'application/x-executable');
        $data = ['attachments' => [$file]];

        $validator = Validator::make($data, $this->request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('attachments.0', $validator->errors()->toArray());
    }

    /** @test */
    public function it_fails_validation_for_oversized_files()
    {
        // Create file larger than 10MB (10240 KB)
        $file = UploadedFile::fake()->create('large.pdf', 15000, 'application/pdf');
        $data = ['attachments' => [$file]];

        $validator = Validator::make($data, $this->request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('attachments.0', $validator->errors()->toArray());
    }

    /** @test */
    public function it_fails_validation_for_invalid_urls()
    {
        $data = [
            'document_urls' => [
                'not-a-url',
                'invalid://url',
                'ftp://notallowed.com/file.pdf'
            ]
        ];

        $validator = Validator::make($data, $this->request->rules());

        $this->assertTrue($validator->fails());
        $errors = $validator->errors()->toArray();
        $this->assertArrayHasKey('document_urls.0', $errors);
    }

    /** @test */
    public function it_fails_validation_for_long_document_names()
    {
        $longName = str_repeat('a', 256); // Exceeds 255 character limit
        $data = ['document_names' => [$longName]];

        $validator = Validator::make($data, $this->request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('document_names.0', $validator->errors()->toArray());
    }

    /** @test */
    public function it_allows_null_values_for_all_fields()
    {
        $data = [
            'attachments' => null,
            'files' => null,
            'document_urls' => null,
            'document_names' => null
        ];

        $validator = Validator::make($data, $this->request->rules());

        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function it_allows_empty_arrays()
    {
        $data = [
            'attachments' => [],
            'files' => [],
            'document_urls' => [],
            'document_names' => []
        ];

        $validator = Validator::make($data, $this->request->rules());

        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function it_validates_mixed_valid_data()
    {
        $data = [
            'attachments' => [
                UploadedFile::fake()->create('test.pdf', 1024, 'application/pdf')
            ],
            'files' => [
                UploadedFile::fake()->create('test.docx', 512, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document')
            ],
            'document_urls' => [
                'https://example.com/document.pdf'
            ],
            'document_names' => [
                'Test Document'
            ]
        ];

        $validator = Validator::make($data, $this->request->rules());

        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function it_has_custom_error_messages()
    {
        $messages = $this->request->messages();

        $this->assertIsArray($messages);

        // Check attachment messages
        $this->assertArrayHasKey('attachments.*.file', $messages);
        $this->assertArrayHasKey('attachments.*.max', $messages);
        $this->assertArrayHasKey('attachments.*.mimes', $messages);

        // Check files messages
        $this->assertArrayHasKey('files.*.file', $messages);
        $this->assertArrayHasKey('files.*.max', $messages);
        $this->assertArrayHasKey('files.*.mimes', $messages);

        // Check URL and name messages
        $this->assertArrayHasKey('document_urls.*.url', $messages);
        $this->assertArrayHasKey('document_names.*.string', $messages);
        $this->assertArrayHasKey('document_names.*.max', $messages);

        // Verify message content includes file types and size limits
        $this->assertStringContainsString('10MB', $messages['attachments.*.max']);
        $this->assertStringContainsString('255', $messages['document_names.*.max']);
    }

    /** @test */
    public function it_has_custom_attributes()
    {
        $attributes = $this->request->attributes();

        $this->assertIsArray($attributes);
        $this->assertArrayHasKey('attachments.*', $attributes);
        $this->assertArrayHasKey('files.*', $attributes);
        $this->assertArrayHasKey('document_urls.*', $attributes);
        $this->assertArrayHasKey('document_names.*', $attributes);

        $this->assertEquals('attachment', $attributes['attachments.*']);
        $this->assertEquals('file', $attributes['files.*']);
        $this->assertEquals('document URL', $attributes['document_urls.*']);
        $this->assertEquals('document name', $attributes['document_names.*']);
    }

    /** @test */
    public function it_validates_multiple_files_in_array()
    {
        $files = [
            UploadedFile::fake()->create('test1.pdf', 1024, 'application/pdf'),
            UploadedFile::fake()->create('test2.jpg', 512, 'image/jpeg'),
            UploadedFile::fake()->create('test3.docx', 256, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document')
        ];

        $data = ['attachments' => $files];
        $validator = Validator::make($data, $this->request->rules());

        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function it_fails_validation_when_one_file_in_array_is_invalid()
    {
        $files = [
            UploadedFile::fake()->create('test1.pdf', 1024, 'application/pdf'),
            UploadedFile::fake()->create('test2.exe', 512, 'application/x-executable'), // Invalid type
            UploadedFile::fake()->create('test3.docx', 256, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document')
        ];

        $data = ['attachments' => $files];
        $validator = Validator::make($data, $this->request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('attachments.1', $validator->errors()->toArray());
    }

    /** @test */
    public function it_validates_null_document_names_in_array()
    {
        $data = [
            'document_names' => [
                'Valid Name',
                null, // Null is allowed
                'Another Valid Name'
            ]
        ];

        $validator = Validator::make($data, $this->request->rules());

        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function it_fails_validation_for_non_string_document_names()
    {
        $data = [
            'document_names' => [
                'Valid Name',
                123, // Not a string
                'Another Valid Name'
            ]
        ];

        $validator = Validator::make($data, $this->request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('document_names.1', $validator->errors()->toArray());
    }

    /** @test */
    public function it_validates_edge_case_file_sizes()
    {
        // Test exactly 10MB (should pass)
        $maxSizeFile = UploadedFile::fake()->create('max.pdf', 10240, 'application/pdf');
        $data = ['attachments' => [$maxSizeFile]];
        $validator = Validator::make($data, $this->request->rules());
        $this->assertFalse($validator->fails());

        // Test just over 10MB (should fail)
        $oversizeFile = UploadedFile::fake()->create('oversize.pdf', 10241, 'application/pdf');
        $data = ['attachments' => [$oversizeFile]];
        $validator = Validator::make($data, $this->request->rules());
        $this->assertTrue($validator->fails());
    }

    /** @test */
    public function it_validates_edge_case_document_name_lengths()
    {
        // Test exactly 255 characters (should pass)
        $maxLengthName = str_repeat('a', 255);
        $data = ['document_names' => [$maxLengthName]];
        $validator = Validator::make($data, $this->request->rules());
        $this->assertFalse($validator->fails());

        // Test 256 characters (should fail)
        $tooLongName = str_repeat('a', 256);
        $data = ['document_names' => [$tooLongName]];
        $validator = Validator::make($data, $this->request->rules());
        $this->assertTrue($validator->fails());
    }
}