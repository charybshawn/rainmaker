<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AssetActivity extends Model
{
    protected $fillable = [
        'trackable_type',
        'trackable_id',
        'user_id',
        'activity_type',
        'field_name',
        'description',
        'old_value',
        'new_value',
        'metadata',
    ];

    protected $casts = [
        'old_value' => 'array',
        'new_value' => 'array',
        'metadata' => 'array',
    ];

    /**
     * Set the old_value attribute with UTF-8 cleaning only when needed.
     */
    public function setOldValueAttribute($value)
    {
        if ($value === null) {
            $this->attributes['old_value'] = null;
            return;
        }

        // Try normal JSON encoding first
        $encoded = json_encode($value);
        if ($encoded !== false) {
            $this->attributes['old_value'] = $encoded;
            return;
        }

        // Only clean if JSON encoding failed
        $this->attributes['old_value'] = json_encode($this->cleanUtf8($value));
    }

    /**
     * Set the new_value attribute with UTF-8 cleaning only when needed.
     */
    public function setNewValueAttribute($value)
    {
        if ($value === null) {
            $this->attributes['new_value'] = null;
            return;
        }

        // Try normal JSON encoding first
        $encoded = json_encode($value);
        if ($encoded !== false) {
            $this->attributes['new_value'] = $encoded;
            return;
        }

        // Only clean if JSON encoding failed
        $this->attributes['new_value'] = json_encode($this->cleanUtf8($value));
    }

    /**
     * Set the metadata attribute with UTF-8 cleaning only when needed.
     */
    public function setMetadataAttribute($value)
    {
        if ($value === null) {
            $this->attributes['metadata'] = null;
            return;
        }

        // Try normal JSON encoding first
        $encoded = json_encode($value);
        if ($encoded !== false) {
            $this->attributes['metadata'] = $encoded;
            return;
        }

        // Only clean if JSON encoding failed
        $this->attributes['metadata'] = json_encode($this->cleanUtf8($value));
    }

    /**
     * Clean UTF-8 encoding recursively for arrays and strings.
     */
    protected function cleanUtf8($value)
    {
        if (is_string($value)) {
            // Convert smart quotes and other problematic characters to regular ones
            $value = str_replace([
                "\u{201C}", // left double quotation mark
                "\u{201D}", // right double quotation mark
                "\u{2018}", // left single quotation mark
                "\u{2019}"  // right single quotation mark
            ], ['"', '"', "'", "'"], $value);

            // Clean and ensure valid UTF-8 encoding
            $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');

            // Only remove NULL bytes and other truly problematic control characters
            // Preserve spaces (\x20), tabs (\x09), newlines (\x0A), carriage returns (\x0D)
            $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $value);

            // Final check: if still invalid, try to detect and convert encoding
            if (!mb_check_encoding($value, 'UTF-8')) {
                $detected = mb_detect_encoding($value, ['UTF-8', 'ISO-8859-1', 'ASCII'], true);
                if ($detected) {
                    $value = mb_convert_encoding($value, 'UTF-8', $detected);
                } else {
                    // Last resort: convert with replacement characters
                    $value = @iconv('UTF-8', 'UTF-8//IGNORE', $value);
                }
            }

            return $value;
        }

        if (is_array($value)) {
            return array_map([$this, 'cleanUtf8'], $value);
        }

        return $value;
    }

    /**
     * Get the model that the activity belongs to.
     */
    public function trackable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the user who performed the activity.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get activities for a specific model.
     */
    public function scopeForModel($query, $model)
    {
        return $query->where('trackable_type', get_class($model))
                    ->where('trackable_id', $model->id);
    }

    /**
     * Scope to get activities by type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('activity_type', $type);
    }

    /**
     * Scope to get activities by user.
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to get recent activities.
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Get the activity icon based on type.
     */
    public function getIconAttribute(): string
    {
        return match($this->activity_type) {
            'created' => 'plus-circle',
            'updated' => 'pencil',
            'deleted' => 'trash',
            'restored' => 'arrow-path',
            'published' => 'eye',
            'archived' => 'archive-box',
            'attachment_added' => 'paper-clip',
            'attachment_removed' => 'x-circle',
            'tag_added' => 'tag',
            'tag_removed' => 'minus-circle',
            'category_changed' => 'folder',
            'visibility_changed' => 'shield-check',
            'status_changed' => 'arrow-right-circle',
            default => 'information-circle'
        };
    }

    /**
     * Get the activity color based on type.
     */
    public function getColorAttribute(): string
    {
        return match($this->activity_type) {
            'created' => 'green',
            'updated' => 'blue',
            'deleted' => 'red',
            'restored' => 'yellow',
            'published' => 'purple',
            'archived' => 'gray',
            'attachment_added' => 'indigo',
            'attachment_removed' => 'pink',
            'tag_added', 'tag_removed' => 'orange',
            'category_changed' => 'teal',
            'visibility_changed' => 'cyan',
            'status_changed' => 'lime',
            default => 'gray'
        };
    }

    /**
     * Generate a smart description for the activity.
     */
    public static function generateDescription($activityType, $fieldName = null, $oldValue = null, $newValue = null, $model = null): string
    {
        $modelName = $model ? class_basename($model) : 'Item';

        return match($activityType) {
            'created' => "Created {$modelName}",
            'updated' => $fieldName ?
                "Updated {$fieldName}" . ($oldValue && $newValue ? " from \"{$oldValue}\" to \"{$newValue}\"" : '') :
                "Updated {$modelName}",
            'deleted' => "Deleted {$modelName}",
            'restored' => "Restored {$modelName}",
            'published' => "Published {$modelName}",
            'archived' => "Archived {$modelName}",
            'attachment_added' => "Added attachment" . ($newValue ? ": {$newValue}" : ''),
            'attachment_removed' => "Removed attachment" . ($oldValue ? ": {$oldValue}" : ''),
            'tag_added' => "Added tag" . ($newValue ? ": {$newValue}" : ''),
            'tag_removed' => "Removed tag" . ($oldValue ? ": {$oldValue}" : ''),
            'category_changed' => "Changed category" . ($oldValue && $newValue ? " from \"{$oldValue}\" to \"{$newValue}\"" : ''),
            'visibility_changed' => "Changed visibility" . ($oldValue && $newValue ? " from {$oldValue} to {$newValue}" : ''),
            'status_changed' => "Changed status" . ($oldValue && $newValue ? " from {$oldValue} to {$newValue}" : ''),
            default => "Modified {$modelName}"
        };
    }
}