<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    /**
     * Handle background file upload with progress tracking.
     */
    public function uploadFile(Request $request, string $token): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,csv,jpg,jpeg,png,gif,webp,svg'
        ]);

        try {
            $file = $request->file('file');
            $originalName = $file->getClientOriginalName();
            $fileSize = $file->getSize();

            // Initialize progress tracking
            $this->updateProgress($token, [
                'status' => 'uploading',
                'progress' => 10,
                'file_info' => [
                    'original_name' => $originalName,
                    'size' => $this->formatFileSize($fileSize),
                    'cleaned_title' => $this->cleanupFilename($originalName)
                ]
            ]);

            // Store file temporarily
            $tempPath = "temp/uploads/{$token}_" . $originalName;
            $file->storeAs('', $tempPath, 'local');

            // Update progress - upload complete
            $this->updateProgress($token, [
                'status' => 'complete',
                'progress' => 100,
                'file_info' => [
                    'original_name' => $originalName,
                    'size' => $this->formatFileSize($fileSize),
                    'cleaned_title' => $this->cleanupFilename($originalName)
                ],
                'temp_path' => $tempPath
            ]);

            return response()->json([
                'success' => true,
                'token' => $token,
                'message' => 'File uploaded successfully'
            ]);

        } catch (\Exception $e) {
            $this->updateProgress($token, [
                'status' => 'error',
                'progress' => 0,
                'error' => 'Upload failed: ' . $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle background URL download with progress tracking.
     */
    public function uploadUrl(Request $request, string $token): JsonResponse
    {
        $request->validate([
            'url' => 'required|url',
            'name' => 'nullable|string|max:255'
        ]);

        try {
            $url = $request->url;
            $documentName = $request->name ?: ('Document ' . time());

            // Initialize progress tracking
            $this->updateProgress($token, [
                'status' => 'downloading',
                'progress' => 10,
                'file_info' => [
                    'original_name' => $documentName,
                    'cleaned_title' => $this->cleanupFilename($documentName),
                    'url' => $url
                ]
            ]);

            // Use existing UrlDownloadService
            $urlDownloadService = app(\App\Services\UrlDownloadService::class);
            $tempFile = $urlDownloadService->downloadFile($url, $documentName);

            if (!$tempFile) {
                throw new \Exception('Failed to download file from URL');
            }

            // Get file info
            $fileSize = filesize($tempFile);
            $extension = pathinfo($url, PATHINFO_EXTENSION) ?: 'pdf';
            $finalName = $documentName . '.' . $extension;

            // Move to our temp storage
            $tempPath = "temp/uploads/{$token}_{$finalName}";
            Storage::disk('local')->put($tempPath, file_get_contents($tempFile));
            unlink($tempFile); // Clean up original temp file

            // Update progress - download complete
            $this->updateProgress($token, [
                'status' => 'complete',
                'progress' => 100,
                'file_info' => [
                    'original_name' => $finalName,
                    'size' => $this->formatFileSize($fileSize),
                    'cleaned_title' => $this->cleanupFilename($documentName),
                    'url' => $url
                ],
                'temp_path' => $tempPath
            ]);

            return response()->json([
                'success' => true,
                'token' => $token,
                'message' => 'URL downloaded successfully'
            ]);

        } catch (\Exception $e) {
            $this->updateProgress($token, [
                'status' => 'error',
                'progress' => 0,
                'error' => 'Download failed: ' . $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Download failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get upload progress for a token.
     */
    public function getProgress(string $token): JsonResponse
    {
        $progress = Cache::get("upload_progress_{$token}", [
            'status' => 'not_found',
            'progress' => 0,
            'error' => 'Upload not found'
        ]);

        return response()->json($progress);
    }

    /**
     * Cancel upload and cleanup temporary files.
     */
    public function cancelUpload(string $token): JsonResponse
    {
        $progress = Cache::get("upload_progress_{$token}");

        if ($progress && isset($progress['temp_path'])) {
            Storage::disk('local')->delete($progress['temp_path']);
        }

        Cache::forget("upload_progress_{$token}");

        return response()->json(['message' => 'Upload cancelled and cleaned up']);
    }

    /**
     * Clean up filename for use as document title.
     */
    private function cleanupFilename(string $filename): string
    {
        // Remove file extension
        $name = pathinfo($filename, PATHINFO_FILENAME);

        // Replace dashes, underscores, and dots with spaces
        $name = str_replace(['-', '_', '.'], ' ', $name);

        // Clean up multiple spaces
        $name = preg_replace('/\s+/', ' ', $name);

        // Title case each word
        $name = Str::title(trim($name));

        return $name;
    }

    /**
     * Format file size in human-readable format.
     */
    private function formatFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /**
     * Update progress in cache.
     */
    private function updateProgress(string $token, array $data): void
    {
        $existing = Cache::get("upload_progress_{$token}", []);
        $updated = array_merge($existing, $data);

        // Store for 5 minutes
        Cache::put("upload_progress_{$token}", $updated, 300);
    }
}