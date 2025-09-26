<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\Document;
use App\Models\ResearchItem;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class AssetSyncService
{
    /**
     * Sync media files to assets table for a given model
     */
    public function syncAssetsForModel($model)
    {
        // Only sync ResearchItem media files now - Documents use direct asset references
        if (!($model instanceof ResearchItem)) {
            return;
        }

        $sourceType = 'research_note';
        $createdVia = 'research_item';

        // Get all media for this model
        $mediaFiles = $model->getMedia('attachments');

        foreach ($mediaFiles as $media) {
            // Check if asset already exists
            $existingAsset = Asset::where('media_id', $media->id)->first();

            if (!$existingAsset) {
                // Create new asset record
                Asset::create([
                    'title' => $media->name ?: $media->file_name,
                    'description' => "From research note: " . $model->title,
                    'file_name' => $media->file_name,
                    'file_path' => $media->getPathRelativeToRoot(),
                    'mime_type' => $media->mime_type,
                    'size' => $media->size,
                    'source_type' => $sourceType,
                    'source_id' => $model->id,
                    'media_id' => $media->id,
                    'company_id' => $model->company_id,
                    'user_id' => $model->user_id,
                    'visibility' => $model->visibility ?? 'private',
                    'created_via' => $createdVia,
                ]);
            }
        }
    }

    /**
     * Mark assets as orphaned when model is deleted (only for research notes)
     * Documents should be completely removed
     */
    public function removeAssetsForModel($model)
    {
        if (!($model instanceof ResearchItem)) {
            return;
        }

        // Mark research note assets as orphaned instead of deleting
        Asset::where('source_type', 'research_note')
              ->where('source_id', $model->id)
              ->update(['is_orphaned' => true]);
    }

    /**
     * Mark assets as orphaned if their related models no longer exist
     */
    public function fixOrphanedAssets()
    {
        $fixedCount = 0;

        // Check research note assets
        $researchAssets = Asset::where('source_type', 'research_note')
                                ->where('is_orphaned', false)
                                ->get();

        foreach ($researchAssets as $asset) {
            $researchItem = ResearchItem::find($asset->source_id);
            if (!$researchItem) {
                $asset->update(['is_orphaned' => true]);
                $fixedCount++;
            }
        }

        // Check document assets
        $documentAssets = Asset::where('source_type', 'document')
                                ->get();

        foreach ($documentAssets as $asset) {
            $document = Document::find($asset->source_id);
            if (!$document) {
                // Document assets should be deleted completely, not orphaned
                $asset->delete();
                $fixedCount++;
            }
        }

        return $fixedCount;
    }

    /**
     * Sync all existing media to assets table (for initial population)
     */
    public function syncAllAssets()
    {
        // Clear existing assets
        Asset::truncate();

        // Sync all research items (documents now use direct asset references)
        ResearchItem::with('media')->chunk(100, function ($researchItems) {
            foreach ($researchItems as $researchItem) {
                $this->syncAssetsForModel($researchItem);
            }
        });
    }
}