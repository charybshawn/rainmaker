<?php

namespace App\Models;

use App\Traits\HasFileUploads;
use App\Traits\TracksActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
// use Xslain\OfflineSync\Traits\Syncable;
// use Xslain\OfflineSync\Contracts\SyncableModelInterface;

class ResearchItem extends Model // implements SyncableModelInterface
{
    use HasFactory, TracksActivity, HasFileUploads, SoftDeletes; // Syncable
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
     * Assets relationship - symbolic links to assets via pivot table
     */
    public function assets(): BelongsToMany
    {
        return $this->belongsToMany(Asset::class, 'research_item_assets');
    }

    /**
     * Direct assets relationship - assets created specifically for this research item
     */
    public function directAssets()
    {
        return $this->hasMany(Asset::class, 'source_id')
            ->where('source_type', 'research_item');
    }

    /**
     * Handle calls to undefined relationships.
     * This prevents errors when legacy code tries to access the old 'media' relationship.
     */
    public function __call($method, $parameters)
    {
        // Handle legacy media relationship access
        if ($method === 'media') {
            \Log::warning('Legacy media relationship accessed on ResearchItem', [
                'research_item_id' => $this->id,
                'backtrace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5)
            ]);

            // Return empty collection to maintain compatibility
            return collect();
        }

        return parent::__call($method, $parameters);
    }
}
