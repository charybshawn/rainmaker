<?php

namespace App\Models;

use App\Traits\TracksActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Validation\ValidationException;

class Document extends Model
{
    use TracksActivity;

    protected $fillable = [
        'title',
        'description',
        'company_id',
        'category_id',
        'user_id',
        'visibility',
    ];

    protected $casts = [
        'visibility' => 'string',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function assets(): BelongsToMany
    {
        return $this->belongsToMany(Asset::class, 'document_assets');
    }

    /**
     * Get formatted attachments for API responses.
     * Now uses assets instead of media.
     */
    public function getFormattedAttachments(): array
    {
        return $this->assets->map(function ($asset) {
            return [
                'id' => $asset->id,
                'name' => $asset->title,
                'file_name' => $asset->file_name,
                'mime_type' => $asset->mime_type,
                'size' => $asset->size,
                'url' => $asset->url,
            ];
        })->toArray();
    }

    /**
     * Get the title/name of this model for activity descriptions.
     */
    public function getActivityTitle(): string
    {
        return $this->title;
    }

    /**
     * Attach assets to this document with validation
     */
    public function attachAssets(array $assetIds): void
    {
        // Validate that all assets exist and are not orphaned
        foreach ($assetIds as $assetId) {
            $asset = Asset::find($assetId);

            if (!$asset) {
                throw ValidationException::withMessages([
                    'assets' => "Asset with ID {$assetId} does not exist."
                ]);
            }

            if ($asset->is_orphaned) {
                throw ValidationException::withMessages([
                    'assets' => "Cannot attach orphaned asset with ID {$assetId}."
                ]);
            }
        }

        // If validation passes, attach the assets
        $this->assets()->attach($assetIds);
    }

    /**
     * Validate all attached assets are still valid
     */
    public function validateAssetReferences(): bool
    {
        $invalidAssets = [];

        foreach ($this->assets as $asset) {
            if (!$asset->hasValidReferences()) {
                $invalidAssets[] = $asset->id;
            }
        }

        if (!empty($invalidAssets)) {
            \Log::warning("Document {$this->id} has invalid asset references: " . implode(', ', $invalidAssets));
            return false;
        }

        return true;
    }

    /**
     * Clean up invalid asset attachments
     */
    public function cleanupInvalidAssets(): int
    {
        $detachedCount = 0;
        $assetsToDetach = [];

        foreach ($this->assets as $asset) {
            if (!$asset->hasValidReferences()) {
                $assetsToDetach[] = $asset->id;
                $asset->markAsOrphaned("Detached from document {$this->id} due to invalid references");
            }
        }

        if (!empty($assetsToDetach)) {
            $this->assets()->detach($assetsToDetach);
            $detachedCount = count($assetsToDetach);
            \Log::info("Document {$this->id} detached {$detachedCount} invalid assets");
        }

        return $detachedCount;
    }

}
