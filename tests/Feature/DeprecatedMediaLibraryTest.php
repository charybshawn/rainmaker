<?php

namespace Tests\Feature;

use App\Models\ResearchItem;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DeprecatedMediaLibraryTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Company $company;
    protected ResearchItem $researchItem;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->user = User::factory()->create();
        $this->company = Company::factory()->create();
        $this->researchItem = ResearchItem::factory()->create([
            'company_id' => $this->company->id,
            'user_id' => $this->user->id,
        ]);
    }

    /** @test */
    public function research_item_no_longer_implements_has_media_interface()
    {
        // Verify ResearchItem does not implement HasMedia interface
        $this->assertNotInstanceOf(\Spatie\MediaLibrary\HasMedia::class, $this->researchItem);
    }

    /** @test */
    public function research_item_does_not_use_interacts_with_media_trait()
    {
        // Get all traits used by ResearchItem
        $traits = class_uses_recursive(ResearchItem::class);

        // Verify InteractsWithMedia trait is not in use
        $this->assertNotContains(\Spatie\MediaLibrary\InteractsWithMedia::class, $traits);
    }

    /** @test */
    public function research_item_addMedia_method_does_not_exist()
    {
        // Verify addMedia method does not exist on ResearchItem
        $this->assertFalse(method_exists($this->researchItem, 'addMedia'));
    }

    /** @test */
    public function research_item_getMedia_method_does_not_exist()
    {
        // Verify getMedia method does not exist on ResearchItem
        $this->assertFalse(method_exists($this->researchItem, 'getMedia'));
    }

    /** @test */
    public function research_item_media_method_does_not_exist()
    {
        // Verify media method does not exist on ResearchItem
        $this->assertFalse(method_exists($this->researchItem, 'media'));
    }

    /** @test */
    public function research_item_addMediaFromRequest_method_does_not_exist()
    {
        // Verify addMediaFromRequest method does not exist on ResearchItem
        $this->assertFalse(method_exists($this->researchItem, 'addMediaFromRequest'));
    }

    /** @test */
    public function research_item_clearMediaCollection_method_does_not_exist()
    {
        // Verify clearMediaCollection method does not exist on ResearchItem
        $this->assertFalse(method_exists($this->researchItem, 'clearMediaCollection'));
    }

    /** @test */
    public function research_item_getMediaCollection_method_does_not_exist()
    {
        // Verify getMediaCollection method does not exist on ResearchItem
        $this->assertFalse(method_exists($this->researchItem, 'getMediaCollection'));
    }

    /** @test */
    public function research_item_hasMedia_method_does_not_exist()
    {
        // Verify hasMedia method does not exist on ResearchItem
        $this->assertFalse(method_exists($this->researchItem, 'hasMedia'));
    }

    /** @test */
    public function research_item_getFirstMedia_method_does_not_exist()
    {
        // Verify getFirstMedia method does not exist on ResearchItem
        $this->assertFalse(method_exists($this->researchItem, 'getFirstMedia'));
    }

    /** @test */
    public function research_item_getFirstMediaUrl_method_does_not_exist()
    {
        // Verify getFirstMediaUrl method does not exist on ResearchItem
        $this->assertFalse(method_exists($this->researchItem, 'getFirstMediaUrl'));
    }

    /** @test */
    public function attempting_to_call_addMedia_throws_error()
    {
        $this->expectException(\BadMethodCallException::class);

        $file = UploadedFile::fake()->create('test.pdf', 1024, 'application/pdf');
        $this->researchItem->addMedia($file);
    }

    /** @test */
    public function attempting_to_call_getMedia_throws_error()
    {
        $this->expectException(\BadMethodCallException::class);

        $this->researchItem->getMedia('attachments');
    }

    /** @test */
    public function attempting_to_access_media_property_returns_null_or_throws_error()
    {
        // Verify media property/relationship does not exist or is empty
        try {
            $media = $this->researchItem->media;
            // If no exception is thrown, verify media is null or empty
            $this->assertTrue(is_null($media) || (is_countable($media) && count($media) === 0));
        } catch (\BadMethodCallException $e) {
            // This is also acceptable - means the relationship doesn't exist
            $this->assertTrue(true);
        }
    }

    /** @test */
    public function research_item_uses_new_asset_relationships_instead()
    {
        // Verify new asset-related methods exist and work
        $this->assertTrue(method_exists($this->researchItem, 'assets'));
        $this->assertTrue(method_exists($this->researchItem, 'directAssets'));
        $this->assertTrue(method_exists($this->researchItem, 'getFormattedAttachments'));

        // Verify these relationships return proper collections
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsToMany::class, $this->researchItem->assets());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $this->researchItem->directAssets());
        $this->assertIsArray($this->researchItem->getFormattedAttachments());
    }

    /** @test */
    public function research_item_model_class_definition_is_clean()
    {
        // Get the actual class definition without parent classes
        $reflection = new \ReflectionClass(ResearchItem::class);
        $interfaces = $reflection->getInterfaceNames();

        // Verify ResearchItem does not implement HasMedia
        $this->assertNotContains(\Spatie\MediaLibrary\HasMedia::class, $interfaces);

        // Verify it implements the correct interfaces
        $this->assertContains(\Xslain\OfflineSync\Contracts\SyncableModelInterface::class, $interfaces);
    }

    /** @test */
    public function research_item_fillable_fields_do_not_include_media_columns()
    {
        $fillable = $this->researchItem->getFillable();

        // Verify no MediaLibrary-specific fields are fillable
        $mediaFields = ['media', 'media_collection', 'media_id', 'collection_name'];

        foreach ($mediaFields as $field) {
            $this->assertNotContains($field, $fillable, "ResearchItem should not have {$field} in fillable array");
        }
    }

    /** @test */
    public function research_item_casts_do_not_include_media_columns()
    {
        $casts = $this->researchItem->getCasts();

        // Verify no MediaLibrary-specific fields are cast
        $mediaFields = ['media', 'media_collection', 'media_id'];

        foreach ($mediaFields as $field) {
            $this->assertArrayNotHasKey($field, $casts, "ResearchItem should not cast {$field}");
        }
    }

    /** @test */
    public function research_item_database_table_should_not_have_media_columns()
    {
        // This test would normally check if media columns exist in the database
        // Since we're using migrations, we verify the model doesn't expect them

        $tableColumns = \Schema::getColumnListing('research_items');

        // Verify table doesn't have MediaLibrary-specific columns
        $mediaColumns = ['media_id', 'collection_name', 'media_collection'];

        foreach ($mediaColumns as $column) {
            $this->assertNotContains($column, $tableColumns, "research_items table should not have {$column} column");
        }
    }
}