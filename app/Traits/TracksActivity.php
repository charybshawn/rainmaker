<?php

namespace App\Traits;

use App\Models\AssetActivity;
use App\Observers\ActivityTrackingObserver;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Request;

trait TracksActivity
{
    /**
     * Boot the trait.
     */
    protected static function bootTracksActivity()
    {
        static::observe(ActivityTrackingObserver::class);
    }

    /**
     * Get all activities for this model.
     */
    public function activities(): MorphMany
    {
        return $this->morphMany(AssetActivity::class, 'trackable')->latest();
    }

    /**
     * Get recent activities for this model.
     */
    public function recentActivities($days = 7): MorphMany
    {
        return $this->activities()->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Record an activity for this model.
     */
    public function recordActivity(
        string $activityType,
        ?string $fieldName = null,
        $oldValue = null,
        $newValue = null,
        ?array $metadata = null,
        ?string $description = null
    ): AssetActivity {
        // Generate description if not provided
        if (! $description) {
            $description = AssetActivity::generateDescription(
                $activityType,
                $fieldName,
                $oldValue,
                $newValue,
                $this
            );
        }

        // Prepare metadata
        $defaultMetadata = [
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'model_class' => get_class($this),
            'model_title' => $this->getActivityTitle(),
        ];

        $metadata = array_merge($defaultMetadata, $metadata ?? []);

        return AssetActivity::create([
            'trackable_type' => get_class($this),
            'trackable_id' => $this->id,
            'user_id' => auth()->id(),
            'activity_type' => $activityType,
            'field_name' => $fieldName,
            'description' => $description,
            'old_value' => $this->castActivityValue($oldValue),
            'new_value' => $this->castActivityValue($newValue),
            'metadata' => $metadata,
        ]);
    }

    /**
     * Get the title/name of this model for activity descriptions.
     */
    public function getActivityTitle(): string
    {
        // Try common title/name fields
        if (isset($this->title)) {
            return $this->title;
        }
        if (isset($this->name)) {
            return $this->name;
        }
        if (isset($this->ticker)) {
            return $this->ticker;
        }

        return class_basename($this).' #'.$this->id;
    }

    /**
     * Cast activity values to appropriate format.
     */
    protected function castActivityValue($value)
    {
        if (is_null($value)) {
            return null;
        }

        if (is_array($value) || is_object($value)) {
            return $value;
        }

        // Handle string values with minimal cleaning only for activity tracking
        if (is_string($value)) {
            // Only convert smart quotes that cause JSON encoding issues
            $value = str_replace([
                "\u{201C}", // left double quotation mark
                "\u{201D}", // right double quotation mark
                "\u{2018}", // left single quotation mark
                "\u{2019}"  // right single quotation mark
            ], ['"', '"', "'", "'"], $value);

            // Truncate very long strings for activity tracking
            if (mb_strlen($value) > 500) {
                return mb_substr($value, 0, 500).'...';
            }
        }

        return $value;
    }

    /**
     * Get fields that should not be tracked.
     */
    public function getUntrackableFields(): array
    {
        return array_merge([
            'id',
            'created_at',
            'updated_at',
            'deleted_at',
            'remember_token',
            'email_verified_at',
            'password',
        ], $this->untrackableFields ?? []);
    }

    /**
     * Get fields that should be tracked for this model.
     */
    public function getTrackableFields(): array
    {
        $fillable = $this->getFillable();
        $untrackable = $this->getUntrackableFields();

        return array_diff($fillable, $untrackable);
    }

    /**
     * Check if a field should be tracked.
     */
    public function shouldTrackField(string $field): bool
    {
        return in_array($field, $this->getTrackableFields());
    }

    /**
     * Get the display value for a field.
     */
    public function getFieldDisplayValue(string $field, $value)
    {
        // Handle relationships
        if (str_ends_with($field, '_id') && $value) {
            $relationName = str_replace('_id', '', $field);
            if (method_exists($this, $relationName)) {
                try {
                    $related = $this->$relationName;
                    if ($related) {
                        return $related->getActivityTitle();
                    }
                } catch (\Exception $e) {
                    // If relationship access fails (e.g., undefined relationship),
                    // just return the ID value
                    \Log::warning('Failed to access relationship for activity tracking', [
                        'model' => get_class($this),
                        'field' => $field,
                        'relation' => $relationName,
                        'error' => $e->getMessage(),
                    ]);
                    return $value;
                }
            }
        }

        // Handle enums/status fields
        if (in_array($field, ['status', 'visibility'])) {
            return ucfirst($value);
        }

        // Handle boolean fields
        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }

        // Handle dates
        if ($this->isDateAttribute($field) && $value) {
            return $value instanceof \Carbon\Carbon ? $value->format('M j, Y g:i A') : $value;
        }

        return $value;
    }
}
