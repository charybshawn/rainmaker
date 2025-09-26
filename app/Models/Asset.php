<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Asset extends Model
{
    protected $fillable = [
        'title',
        'description',
        'file_name',
        'file_path',
        'mime_type',
        'size',
        'source_type',
        'source_id',
        'media_id',
        'company_id',
        'user_id',
        'visibility',
        'is_orphaned',
        'created_via',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getUrlAttribute(): string
    {
        if ($this->media_id) {
            // If we have a media_id, try to get URL from media table
            $media = \Spatie\MediaLibrary\MediaCollections\Models\Media::find($this->media_id);
            if ($media) {
                return $media->getUrl();
            }
        }

        // Fallback to storage URL
        return \Storage::url($this->file_path);
    }

    public function documents(): BelongsToMany
    {
        return $this->belongsToMany(Document::class, 'document_assets');
    }

    public function source(): MorphTo
    {
        return $this->morphTo();
    }
}
