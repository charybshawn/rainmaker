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
        $fileUploadService = app(FileUploadService::class);
        return $fileUploadService->transformMediaForApi($this->getMedia('attachments'));
    }

    /**
     * Get the supported MIME types for file uploads.
     */
    public function getSupportedMimeTypes(): array
    {
        return FileUploadService::getSupportedMimeTypes();
    }
}