<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Services\FileUploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Support\Facades\Storage;

class AssetController extends Controller
{
    /**
     * Create a new asset directly (for document uploads)
     */
    public function store(Request $request): JsonResponse
    {
        \Log::info('Asset upload started', [
            'files_count' => count($request->file('files') ?? []),
            'has_files' => $request->hasFile('files'),
            'user_id' => auth()->id(),
        ]);

        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'company_id' => 'required|exists:companies,id',
                'visibility' => 'required|in:public,team,private',
                'files' => 'required|array',
                'files.*' => 'file|max:10240|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,csv,jpg,jpeg,png,gif,webp,svg',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Asset validation failed', [
                'errors' => $e->errors(),
                'request_keys' => array_keys($request->all()),
                'files_key_exists' => $request->has('files'),
                'files_value' => $request->file('files'),
            ]);
            throw $e;
        }

        $assets = [];
        $errors = [];

        foreach ($request->file('files') ?? [] as $file) {
            try {
                // Store file in the public location for documents
                $path = $file->store('research-assets', 'public');

                $asset = Asset::create([
                    'title' => $validated['title'],
                    'description' => $validated['description'],
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'source_type' => 'document',
                    'source_id' => 0, // Use 0 for direct uploads since NULL is not allowed
                    'media_id' => null, // No media library for direct uploads
                    'company_id' => $validated['company_id'],
                    'user_id' => auth()->id(),
                    'visibility' => $validated['visibility'],
                    'created_via' => 'document',
                    'is_orphaned' => false,
                ]);

                \Log::info('Asset created successfully', [
                    'asset_id' => $asset->id,
                    'file_name' => $asset->file_name,
                    'file_path' => $asset->file_path,
                    'url' => $asset->url,
                ]);

                $assets[] = [
                    'id' => $asset->id,
                    'title' => $asset->title,
                    'file_name' => $asset->file_name,
                    'mime_type' => $asset->mime_type,
                    'size' => $asset->size,
                    'url' => $asset->url,
                ];

            } catch (\Exception $e) {
                \Log::error('Asset creation failed', [
                    'filename' => $file->getClientOriginalName(),
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                $errors[] = [
                    'filename' => $file->getClientOriginalName(),
                    'error' => $e->getMessage()
                ];
            }
        }

        return response()->json([
            'assets' => $assets,
            'errors' => $errors,
            'message' => count($assets) . ' assets created successfully'
        ], 201);
    }

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