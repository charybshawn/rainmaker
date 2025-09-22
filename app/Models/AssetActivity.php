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