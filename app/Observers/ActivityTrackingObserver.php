<?php

namespace App\Observers;

use Illuminate\Database\Eloquent\Model;

class ActivityTrackingObserver
{
    /**
     * Handle the model "created" event.
     */
    public function created(Model $model): void
    {
        if (!$this->shouldTrack($model)) {
            return;
        }

        $model->recordActivity(
            'created',
            null,
            null,
            null,
            [
                'created_fields' => $this->getFieldValues($model, $model->getTrackableFields())
            ]
        );
    }

    /**
     * Handle the model "updated" event.
     */
    public function updated(Model $model): void
    {
        if (!$this->shouldTrack($model)) {
            return;
        }

        $changes = $model->getChanges();
        $original = $model->getOriginal();

        foreach ($changes as $field => $newValue) {
            if (!$model->shouldTrackField($field)) {
                continue;
            }

            $oldValue = $original[$field] ?? null;

            // Skip if values are the same (shouldn't happen, but safety check)
            if ($oldValue === $newValue) {
                continue;
            }

            // Get display values
            $oldDisplayValue = $model->getFieldDisplayValue($field, $oldValue);
            $newDisplayValue = $model->getFieldDisplayValue($field, $newValue);

            $model->recordActivity(
                'updated',
                $field,
                $oldDisplayValue,
                $newDisplayValue,
                [
                    'field_changes' => [
                        $field => [
                            'old' => $oldValue,
                            'new' => $newValue,
                        ]
                    ]
                ]
            );
        }
    }

    /**
     * Handle the model "deleted" event.
     */
    public function deleted(Model $model): void
    {
        if (!$this->shouldTrack($model)) {
            return;
        }

        $model->recordActivity(
            'deleted',
            null,
            null,
            null,
            [
                'deleted_fields' => $this->getFieldValues($model, $model->getTrackableFields())
            ]
        );
    }

    /**
     * Handle the model "restored" event.
     */
    public function restored(Model $model): void
    {
        if (!$this->shouldTrack($model)) {
            return;
        }

        $model->recordActivity('restored');
    }

    /**
     * Determine if the model should be tracked.
     */
    protected function shouldTrack(Model $model): bool
    {
        // Don't track if no user is authenticated (unless it's a system operation)
        if (!auth()->check()) {
            return false;
        }

        // Don't track activity models themselves
        if ($model instanceof \App\Models\AssetActivity) {
            return false;
        }

        // Check if model has the TracksActivity trait
        return in_array(\App\Traits\TracksActivity::class, class_uses_recursive($model));
    }

    /**
     * Get field values for metadata.
     */
    protected function getFieldValues(Model $model, array $fields): array
    {
        $values = [];

        foreach ($fields as $field) {
            $value = $model->getAttribute($field);
            $values[$field] = $model->getFieldDisplayValue($field, $value);
        }

        return $values;
    }
}