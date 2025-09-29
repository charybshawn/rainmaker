<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Validation\ValidationException;

class Asset extends Model
{
    use HasFactory;
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

        // For files stored in public disk, use the public URL
        if ($this->file_path) {
            return \Storage::disk('public')->url($this->file_path);
        }

        return '#';
    }

    public function documents(): BelongsToMany
    {
        return $this->belongsToMany(Document::class, 'document_assets');
    }

    public function source(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Boot the model and set up validation observers
     */
    protected static function boot()
    {
        parent::boot();

        // Validate references before creating or updating
        static::creating(function ($asset) {
            $asset->validateReferences();
        });

        static::updating(function ($asset) {
            $asset->validateReferences();
        });
    }

    /**
     * Validate that the asset's references are valid
     */
    public function validateReferences()
    {
        // Skip validation for orphaned assets
        if ($this->is_orphaned) {
            return;
        }

        // If source_type and source_id are set, validate the reference exists
        if ($this->source_type && $this->source_id) {
            $this->validateSourceReference();
        }

        // Validate company reference if set
        if ($this->company_id) {
            $this->validateCompanyReference();
        }

        // Validate user reference if set
        if ($this->user_id) {
            $this->validateUserReference();
        }
    }

    /**
     * Validate the source reference exists
     */
    private function validateSourceReference()
    {
        $exists = false;

        switch ($this->source_type) {
            case 'document':
                $exists = Document::where('id', $this->source_id)->exists();
                break;
            case 'research_item':
            case 'research_note': // Legacy support for old source_type value
                // Check if the research item exists and is not soft deleted
                $exists = ResearchItem::where('id', $this->source_id)
                    ->whereNull('deleted_at')
                    ->exists();
                break;
        }

        if (!$exists) {
            throw ValidationException::withMessages([
                'source_id' => "The referenced {$this->source_type} with ID {$this->source_id} does not exist or has been deleted."
            ]);
        }
    }

    /**
     * Validate the company reference exists
     */
    private function validateCompanyReference()
    {
        if (!Company::where('id', $this->company_id)->exists()) {
            throw ValidationException::withMessages([
                'company_id' => "The referenced company with ID {$this->company_id} does not exist."
            ]);
        }
    }

    /**
     * Validate the user reference exists
     */
    private function validateUserReference()
    {
        if (!User::where('id', $this->user_id)->exists()) {
            throw ValidationException::withMessages([
                'user_id' => "The referenced user with ID {$this->user_id} does not exist."
            ]);
        }
    }

    /**
     * Check if this asset has valid references
     */
    public function hasValidReferences(): bool
    {
        try {
            $this->validateReferences();
            return true;
        } catch (ValidationException $e) {
            return false;
        }
    }

    /**
     * Mark this asset as orphaned
     */
    public function markAsOrphaned(string $reason = 'Reference validation failed')
    {
        $this->is_orphaned = true;
        $this->save();

        \Log::warning("Asset {$this->id} marked as orphaned: {$reason}");
    }
}
