<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Spatie\MediaLibrary\HasMedia;
use App\Services\UrlDownloadService;
use App\Services\AssetSyncService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Exception;

class FileUploadService
{
    protected UrlDownloadService $urlDownloadService;
    protected AssetSyncService $assetSyncService;

    public function __construct(UrlDownloadService $urlDownloadService, AssetSyncService $assetSyncService)
    {
        $this->urlDownloadService = $urlDownloadService;
        $this->assetSyncService = $assetSyncService;
    }

    /**
     * Supported file types and their MIME types.
     */
    public static function getSupportedMimeTypes(): array
    {
        return [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'text/plain',
            'text/csv',
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
            'image/svg+xml',
        ];
    }

    /**
     * Get validation rules for file uploads.
     */
    public static function getValidationRules(): array
    {
        $mimeTypes = implode(',', [
            'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx',
            'txt', 'csv', 'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'
        ]);

        return [
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:10240|mimes:' . $mimeTypes,
            'files' => 'nullable|array',
            'files.*' => 'file|max:10240|mimes:' . $mimeTypes,
            'document_urls' => 'nullable|array',
            'document_urls.*' => 'url',
            'document_names' => 'nullable|array',
            'document_names.*' => 'nullable|string|max:255',
            'temp_upload_tokens' => 'nullable|array',
            'temp_upload_tokens.*' => 'string|max:255',
        ];
    }

    /**
     * Handle file uploads for a model with comprehensive error tracking.
     */
    public function handleUploads(HasMedia $model, Request $request, string $collection = 'attachments'): array
    {
        $results = [
            'files' => ['expected' => 0, 'successful' => 0, 'failed' => []],
            'urls' => ['expected' => 0, 'successful' => 0, 'failed' => []],
            'existing' => ['expected' => 0, 'successful' => 0, 'failed' => []],
            'temp_uploads' => ['expected' => 0, 'successful' => 0, 'failed' => []]
        ];

        // Handle direct file uploads
        $this->handleDirectFileUploads($model, $request, $collection, $results);

        // Handle URL downloads
        $this->handleUrlDownloads($model, $request, $collection, $results);

        // Handle existing file attachments (for updates)
        $this->handleExistingFileAttachments($model, $request, $collection, $results);

        // Handle temporary uploads from background upload system
        $this->handleTemporaryUploads($model, $request, $collection, $results);

        // Sync with assets table
        $this->assetSyncService->syncAssetsForModel($model);

        return $results;
    }

    /**
     * Handle direct file uploads from request.
     */
    protected function handleDirectFileUploads(HasMedia $model, Request $request, string $collection, array &$results): void
    {
        $files = $this->extractFilesFromRequest($request);
        $results['files']['expected'] = count($files);

        foreach ($files as $file) {
            try {
                $model->addMedia($file)->toMediaCollection($collection);
                $results['files']['successful']++;

                Log::info('File uploaded successfully', [
                    'model' => get_class($model),
                    'model_id' => $model->id,
                    'filename' => $file->getClientOriginalName(),
                    'collection' => $collection
                ]);

            } catch (Exception $e) {
                $results['files']['failed'][] = [
                    'filename' => $file->getClientOriginalName(),
                    'error' => $e->getMessage()
                ];

                Log::error('Failed to upload file', [
                    'model' => get_class($model),
                    'model_id' => $model->id,
                    'filename' => $file->getClientOriginalName(),
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * Handle URL-based file downloads.
     */
    protected function handleUrlDownloads(HasMedia $model, Request $request, string $collection, array &$results): void
    {
        // Support multiple parameter names for backward compatibility
        $urls = [];
        if ($request->has('document_urls') && is_array($request->document_urls)) {
            $urls = array_merge($urls, $request->document_urls);
        }
        if ($request->has('urls') && is_array($request->urls)) {
            $urls = array_merge($urls, $request->urls);
        }

        $urls = array_filter($urls);
        if (empty($urls)) {
            return;
        }

        $names = $request->document_names ?? [];
        $results['urls']['expected'] = count($urls);

        foreach ($urls as $index => $url) {
            if (empty($url)) continue;

            try {
                $documentName = $names[$index] ?? null;
                if (empty($documentName)) {
                    $documentName = 'Document ' . ($index + 1);
                }

                $tempFile = $this->urlDownloadService->downloadFile($url, $documentName);

                if ($tempFile) {
                    $model->addMedia($tempFile)
                        ->usingName($documentName)
                        ->toMediaCollection($collection);

                    unlink($tempFile); // Clean up temp file
                    $results['urls']['successful']++;

                    Log::info('URL download successful', [
                        'model' => get_class($model),
                        'model_id' => $model->id,
                        'url' => $url,
                        'document_name' => $documentName
                    ]);
                } else {
                    throw new Exception('Failed to download file from URL');
                }

            } catch (Exception $e) {
                $results['urls']['failed'][] = [
                    'url' => $url,
                    'document_name' => $names[$index] ?? 'Document ' . ($index + 1),
                    'error' => $e->getMessage()
                ];

                Log::error('Failed to download from URL', [
                    'model' => get_class($model),
                    'model_id' => $model->id,
                    'url' => $url,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * Handle existing file attachments (for research item cloning/updates).
     */
    protected function handleExistingFileAttachments(HasMedia $model, Request $request, string $collection, array &$results): void
    {
        // Support multiple parameter names for backward compatibility
        $existingIds = [];
        if ($request->has('existing_attachment_ids') && is_array($request->existing_attachment_ids)) {
            $existingIds = array_merge($existingIds, $request->existing_attachment_ids);
        }
        if ($request->has('existing_file_ids') && is_array($request->existing_file_ids)) {
            $existingIds = array_merge($existingIds, $request->existing_file_ids);
        }
        if ($request->has('existing_files') && is_array($request->existing_files)) {
            $existingIds = array_merge($existingIds, $request->existing_files);
        }

        $existingIds = array_filter($existingIds);
        $results['existing']['expected'] = count($existingIds);

        foreach ($existingIds as $assetId) {
            try {
                // First try to find as an asset (new system)
                $asset = \App\Models\Asset::find($assetId);

                if ($asset && $asset->file_path) {
                    // Get the full file path
                    $filePath = null;

                    if ($asset->media_id) {
                        // Asset has media - get path from media
                        $media = \Spatie\MediaLibrary\MediaCollections\Models\Media::find($asset->media_id);
                        if ($media && file_exists($media->getPath())) {
                            $filePath = $media->getPath();
                        }
                    } else {
                        // Direct file path - construct full path
                        $fullPath = storage_path('app/public/' . $asset->file_path);
                        if (file_exists($fullPath)) {
                            $filePath = $fullPath;
                        }
                    }

                    if ($filePath) {
                        $model->addMedia($filePath)
                            ->usingName($asset->title)
                            ->usingFileName($asset->file_name)
                            ->toMediaCollection($collection);

                        $results['existing']['successful']++;

                        Log::info('Existing asset attached successfully', [
                            'model' => get_class($model),
                            'model_id' => $model->id,
                            'asset_id' => $assetId,
                            'filename' => $asset->file_name,
                            'collection' => $collection
                        ]);
                } else {
                    throw new Exception('Original file not found or inaccessible');
                }
            }

            } catch (Exception $e) {
                $results['existing']['failed'][] = [
                    'asset_id' => $assetId,
                    'error' => $e->getMessage()
                ];

                Log::error('Failed to attach existing asset', [
                    'model' => get_class($model),
                    'model_id' => $model->id,
                    'asset_id' => $assetId,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * Handle temporary uploads from background upload system.
     */
    protected function handleTemporaryUploads(HasMedia $model, Request $request, string $collection, array &$results): void
    {
        $tempTokens = [];
        if ($request->has('temp_upload_tokens') && is_array($request->temp_upload_tokens)) {
            $tempTokens = array_filter($request->temp_upload_tokens);
        }

        $results['temp_uploads']['expected'] = count($tempTokens);

        foreach ($tempTokens as $token) {
            try {
                $progress = Cache::get("upload_progress_{$token}");

                if (!$progress || $progress['status'] !== 'complete' || !isset($progress['temp_path'])) {
                    throw new Exception('Upload not found or incomplete');
                }

                $tempPath = $progress['temp_path'];
                $fullPath = Storage::disk('local')->path($tempPath);

                if (!file_exists($fullPath)) {
                    throw new Exception('Temporary file not found');
                }

                $originalName = $progress['file_info']['original_name'];
                $cleanedTitle = $progress['file_info']['cleaned_title'] ?? $originalName;

                $model->addMedia($fullPath)
                    ->usingName($cleanedTitle)
                    ->usingFileName($originalName)
                    ->toMediaCollection($collection);

                // Clean up temporary file and cache
                Storage::disk('local')->delete($tempPath);
                Cache::forget("upload_progress_{$token}");

                $results['temp_uploads']['successful']++;

                Log::info('Temporary upload processed successfully', [
                    'model' => get_class($model),
                    'model_id' => $model->id,
                    'token' => $token,
                    'filename' => $originalName
                ]);

            } catch (Exception $e) {
                $results['temp_uploads']['failed'][] = [
                    'token' => $token,
                    'error' => $e->getMessage()
                ];

                Log::error('Failed to process temporary upload', [
                    'model' => get_class($model),
                    'model_id' => $model->id,
                    'token' => $token,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * Extract all files from request (supports both 'attachments' and 'files' keys).
     */
    protected function extractFilesFromRequest(Request $request): array
    {
        $files = [];

        if ($request->hasFile('attachments')) {
            $files = array_merge($files, $request->file('attachments'));
        }

        if ($request->hasFile('files')) {
            $files = array_merge($files, $request->file('files'));
        }

        return $files;
    }

    /**
     * Transform media collection for API responses.
     */
    public function transformMediaForApi($mediaCollection): array
    {
        return $mediaCollection->map(function ($media) {
            return [
                'id' => $media->id,
                'name' => $media->name,
                'file_name' => $media->file_name,
                'mime_type' => $media->mime_type,
                'size' => $media->size,
                'url' => $media->getUrl(),
            ];
        })->toArray();
    }

    /**
     * Remove media files from a model and sync assets.
     */
    public function removeMediaFromModel(HasMedia $model): void
    {
        // Remove from assets table first
        $this->assetSyncService->removeAssetsForModel($model);

        // Then remove media files
        $model->clearMediaCollection('attachments');
    }
}