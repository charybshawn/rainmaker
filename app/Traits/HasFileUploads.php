<?php

namespace App\Traits;

use App\Services\FileUploadService;

trait HasFileUploads
{
    /**
     * Get formatted attachments for API responses.
     */
    public function getFormattedAttachments(): array
    {
        // Handle ResearchItem with new unified asset system
        if ($this instanceof \App\Models\ResearchItem) {
            $attachments = [];

            // Get direct assets (created specifically for this research item)
            if (method_exists($this, 'directAssets') && $this->relationLoaded('directAssets')) {
                foreach ($this->directAssets as $asset) {
                    $attachments[] = [
                        'id' => $asset->id,
                        'name' => $asset->title,
                        'file_name' => $asset->file_name,
                        'mime_type' => $asset->mime_type,
                        'size' => $asset->size,
                        'url' => $asset->url,
                        'source_type' => 'direct',
                        'created_at' => $asset->created_at,
                    ];
                }
            } elseif (method_exists($this, 'directAssets')) {
                // Load the relationship if it's not loaded
                $this->load('directAssets');
                foreach ($this->directAssets as $asset) {
                    $attachments[] = [
                        'id' => $asset->id,
                        'name' => $asset->title,
                        'file_name' => $asset->file_name,
                        'mime_type' => $asset->mime_type,
                        'size' => $asset->size,
                        'url' => $asset->url,
                        'source_type' => 'direct',
                        'created_at' => $asset->created_at,
                    ];
                }
            }

            // Get symbolic link assets (references to existing assets)
            if (method_exists($this, 'assets') && $this->relationLoaded('assets')) {
                foreach ($this->assets as $asset) {
                    $attachments[] = [
                        'id' => $asset->id,
                        'name' => $asset->title,
                        'file_name' => $asset->file_name,
                        'mime_type' => $asset->mime_type,
                        'size' => $asset->size,
                        'url' => $asset->url,
                        'source_type' => 'reference',
                        'created_at' => $asset->created_at,
                    ];
                }
            } elseif (method_exists($this, 'assets')) {
                // Load the relationship if it's not loaded
                $this->load('assets');
                foreach ($this->assets as $asset) {
                    $attachments[] = [
                        'id' => $asset->id,
                        'name' => $asset->title,
                        'file_name' => $asset->file_name,
                        'mime_type' => $asset->mime_type,
                        'size' => $asset->size,
                        'url' => $asset->url,
                        'source_type' => 'reference',
                        'created_at' => $asset->created_at,
                    ];
                }
            }

            return $attachments;
        }

        // Handle other models with MediaLibrary (legacy approach)
        if (method_exists($this, 'getMedia')) {
            $fileUploadService = app(FileUploadService::class);
            return $fileUploadService->transformMediaForApi($this->getMedia('attachments'));
        }

        return [];
    }

    /**
     * Get the supported MIME types for file uploads.
     */
    public function getSupportedMimeTypes(): array
    {
        return FileUploadService::getSupportedMimeTypes();
    }
}