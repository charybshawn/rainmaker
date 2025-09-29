<?php

namespace App\Models;

use App\Traits\HasFileUploads;
use App\Traits\TracksActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Xslain\OfflineSync\Traits\Syncable;
use Xslain\OfflineSync\Contracts\SyncableModelInterface;

class ResearchItem extends Model implements HasMedia, SyncableModelInterface
{
    use HasFactory, InteractsWithMedia, TracksActivity, HasFileUploads, Syncable, SoftDeletes;
    protected $fillable = [
        'title',
        'content',
        'summary',
        'ai_synopsis',
        'visibility',
        'status',
        'rating',
        'metadata',
        'published_at',
        'source_date',
        'company_id',
        'category_id',
        'user_id',
    ];

    protected $casts = [
        'metadata' => 'array',
        'published_at' => 'datetime',
        'source_date' => 'date',
        'rating' => 'decimal:1',
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
}
