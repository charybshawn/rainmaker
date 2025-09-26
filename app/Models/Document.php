<?php

namespace App\Models;

use App\Traits\TracksActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Document extends Model
{
    use TracksActivity;

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

    public function assets(): BelongsToMany
    {
        return $this->belongsToMany(Asset::class, 'document_assets');
    }

    /**
     * Get formatted attachments for API responses.
     * Now uses assets instead of media.
     */
    public function getFormattedAttachments(): array
    {
        return $this->assets->map(function ($asset) {
            return [
                'id' => $asset->id,
                'name' => $asset->title,
                'file_name' => $asset->file_name,
                'mime_type' => $asset->mime_type,
                'size' => $asset->size,
                'url' => $asset->url,
            ];
        })->toArray();
    }

    /**
     * Get the title/name of this model for activity descriptions.
     */
    public function getActivityTitle(): string
    {
        return $this->title;
    }

}
