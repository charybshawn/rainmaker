<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class AssetController extends Controller
{
    /**
     * Delete an asset (for orphaned files)
     */
    public function destroy(Asset $asset): JsonResponse
    {
        // Only allow deletion of orphaned assets or user's own assets
        if (!$asset->is_orphaned && $asset->user_id !== auth()->id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        try {
            // For orphaned assets, we need to handle media deletion carefully
            if ($asset->media_id) {
                $media = Media::find($asset->media_id);
                if ($media) {
                    // Check if the associated model still exists
                    $modelClass = $media->model_type;
                    $modelId = $media->model_id;

                    if ($modelClass && $modelId) {
                        $relatedModel = $modelClass::find($modelId);
                        if (!$relatedModel) {
                            // The related model doesn't exist (was deleted), so we can safely delete the media
                            $media->forceDelete();
                        } else {
                            // Related model exists, detach the media first
                            $media->delete();
                        }
                    } else {
                        // No related model, safe to delete
                        $media->delete();
                    }
                }
            }

            // Delete the asset record
            $asset->delete();

            return response()->json(null, 204);
        } catch (\Exception $e) {
            \Log::error('Failed to delete asset: ' . $e->getMessage(), [
                'asset_id' => $asset->id,
                'is_orphaned' => $asset->is_orphaned,
                'source_type' => $asset->source_type,
                'source_id' => $asset->source_id,
                'user_id' => $asset->user_id,
            ]);
            return response()->json([
                'message' => 'Failed to delete asset: ' . $e->getMessage()
            ], 500);
        }
    }
}