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
     * Get paginated list of assets
     */
    public function index(Request $request): JsonResponse
    {
        $query = Asset::query();

        // Apply filters
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('file_type')) {
            $query->where('mime_type', 'like', $request->file_type . '%');
        }

        if ($request->filled('source_type')) {
            $query->where('source_type', $request->source_type);
        }

        if ($request->filled('size_filter')) {
            switch ($request->size_filter) {
                case 'small':
                    $query->where('file_size', '<', 1048576); // < 1MB
                    break;
                case 'medium':
                    $query->whereBetween('file_size', [1048576, 10485760]); // 1-10MB
                    break;
                case 'large':
                    $query->where('file_size', '>', 10485760); // > 10MB
                    break;
            }
        }

        // Apply sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');

        $query->orderBy($sortBy, $sortDirection);

        // Paginate
        $limit = min($request->get('limit', 16), 50);
        $assets = $query->paginate($limit);

        return response()->json($assets);
    }

    /**
     * Get available assets that can be linked to documents
     */
    public function getAvailable(): JsonResponse
    {
        \Log::info('AssetController::getAvailable called', [
            'user_id' => auth()->id(),
            'user_email' => auth()->user()?->email,
            'timestamp' => now(),
            'request_ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        try {
            \Log::info('Starting asset query...');

            // Get assets that are not orphaned and can be linked to documents
            $query = Asset::where('is_orphaned', false)
                ->whereNotNull('file_path');

            \Log::info('Base query built', [
                'query_sql' => $query->toSql(),
                'query_bindings' => $query->getBindings(),
            ]);

            $assetsWithCompany = $query->with(['company:id,name'])
                ->orderBy('created_at', 'desc')
                ->get();

            \Log::info('Query executed successfully', [
                'assets_count' => $assetsWithCompany->count(),
                'assets_ids' => $assetsWithCompany->pluck('id')->toArray(),
            ]);

            try {
                $mappedAssets = $assetsWithCompany->map(function ($asset) {
                    \Log::debug('Mapping asset', [
                        'asset_id' => $asset->id,
                        'file_name' => $asset->file_name,
                        'has_company' => $asset->company ? true : false,
                        'company_id' => $asset->company_id,
                    ]);

                    try {
                        $url = $asset->url;
                        \Log::debug('Generated URL for asset', [
                            'asset_id' => $asset->id,
                            'url' => $url,
                        ]);
                    } catch (\Exception $urlException) {
                        \Log::error('Failed to generate URL for asset', [
                            'asset_id' => $asset->id,
                            'error' => $urlException->getMessage(),
                            'trace' => $urlException->getTraceAsString(),
                        ]);
                        $url = '#error-generating-url';
                    }

                    try {
                        $companyData = null;
                        if ($asset->company) {
                            $companyData = [
                                'id' => $asset->company->id,
                                'name' => $asset->company->name
                            ];
                        }

                        return [
                            'id' => $asset->id,
                            'title' => $asset->title,
                            'description' => $asset->description,
                            'file_name' => $asset->file_name,
                            'mime_type' => $asset->mime_type,
                            'size' => $asset->size,
                            'url' => $url,
                            'source_type' => $asset->source_type,
                            'source_id' => $asset->source_id,
                            'company' => $companyData,
                            'created_at' => $asset->created_at,
                            'visibility' => $asset->visibility,
                        ];
                    } catch (\Exception $mappingException) {
                        \Log::error('Failed to map asset data', [
                            'asset_id' => $asset->id,
                            'error' => $mappingException->getMessage(),
                            'trace' => $mappingException->getTraceAsString(),
                        ]);
                        throw $mappingException;
                    }
                });

                \Log::info('Asset mapping completed successfully', [
                    'mapped_count' => $mappedAssets->count(),
                ]);

                $response = response()->json($mappedAssets);

                \Log::info('Response generated successfully', [
                    'response_status' => $response->getStatusCode(),
                    'response_size' => strlen($response->getContent()),
                    'first_asset_id' => $mappedAssets->first()['id'] ?? null,
                ]);

                return $response;

            } catch (\Exception $mappingException) {
                \Log::error('Asset mapping failed', [
                    'error' => $mappingException->getMessage(),
                    'trace' => $mappingException->getTraceAsString(),
                    'raw_assets_count' => $assetsWithCompany->count(),
                ]);
                throw $mappingException;
            }

        } catch (\Illuminate\Database\QueryException $dbException) {
            \Log::error('Database query failed in getAvailable', [
                'error' => $dbException->getMessage(),
                'sql' => $dbException->getSql() ?? 'N/A',
                'bindings' => $dbException->getBindings() ?? [],
                'trace' => $dbException->getTraceAsString(),
            ]);
            return response()->json([
                'message' => 'Database error while fetching available assets',
                'error' => $dbException->getMessage(),
                'debug_info' => [
                    'sql' => $dbException->getSql() ?? 'N/A',
                    'bindings' => $dbException->getBindings() ?? [],
                ]
            ], 500);
        } catch (\Exception $e) {
            \Log::error('Unexpected error in getAvailable', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
            ]);
            return response()->json([
                'message' => 'Failed to fetch available assets',
                'error' => $e->getMessage(),
                'debug_info' => [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'user_id' => auth()->id(),
                ]
            ], 500);
        }
    }

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