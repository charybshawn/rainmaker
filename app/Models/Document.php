<?php

namespace App\Models;

use App\Traits\HasFileUploads;
use App\Traits\TracksActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Document extends Model implements HasMedia
{
    use InteractsWithMedia, TracksActivity, HasFileUploads;

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

    /**
     * Register media collections for file uploads.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('attachments')
            ->acceptsMimeTypes($this->getSupportedMimeTypes());
    }

    /**
     * Register media conversions for uploaded files.
     */
    public function registerMediaConversions(?\Spatie\MediaLibrary\MediaCollections\Models\Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(300)
            ->height(200)
            ->sharpen(10)
            ->performOnCollections('attachments')
            ->nonQueued();
    }

    /**
     * Get the title/name of this model for activity descriptions.
     */
    public function getActivityTitle(): string
    {
        return $this->title;
    }

}
