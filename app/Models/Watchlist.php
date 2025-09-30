<?php

namespace App\Models;

use App\Traits\TracksActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Watchlist extends Model
{
    use HasFactory, TracksActivity;

    protected $fillable = [
        'name',
        'description',
        'slug',
        'user_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'watchlist_companies')
                    ->withPivot('added_at')
                    ->withTimestamps()
                    ->orderByPivot('added_at', 'desc');
    }

    /**
     * Get the title/name of this model for activity descriptions.
     */
    public function getActivityTitle(): string
    {
        return $this->name;
    }

    /**
     * Check if a company is in this watchlist.
     */
    public function hasCompany(Company $company): bool
    {
        return $this->companies()->where('company_id', $company->id)->exists();
    }

    /**
     * Add a company to this watchlist.
     */
    public function addCompany(Company $company): void
    {
        if (!$this->hasCompany($company)) {
            $this->companies()->attach($company->id, ['added_at' => now()]);
        }
    }

    /**
     * Remove a company from this watchlist.
     */
    public function removeCompany(Company $company): void
    {
        $this->companies()->detach($company->id);
    }

    /**
     * Generate a unique slug from the watchlist name
     */
    public static function generateSlug(string $name, ?int $excludeId = null): string
    {
        $baseSlug = \Str::slug($name);
        $slug = $baseSlug;
        $counter = 1;

        while (self::where('slug', $slug)
            ->when($excludeId, fn($query) => $query->where('id', '!=', $excludeId))
            ->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Find a watchlist by slug for the authenticated user
     */
    public static function findBySlugForUser(string $slug, int $userId): ?Watchlist
    {
        return self::where('slug', $slug)
            ->where('user_id', $userId)
            ->with(['companies' => function ($query) {
                $query->orderBy('watchlist_companies.added_at', 'desc');
            }])
            ->first();
    }

    /**
     * Boot method to automatically generate slug when creating/updating
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($watchlist) {
            if (empty($watchlist->slug)) {
                $watchlist->slug = self::generateSlug($watchlist->name);
            }
        });

        static::updating(function ($watchlist) {
            if ($watchlist->isDirty('name')) {
                $watchlist->slug = self::generateSlug($watchlist->name, $watchlist->id);
            }
        });
    }
}
