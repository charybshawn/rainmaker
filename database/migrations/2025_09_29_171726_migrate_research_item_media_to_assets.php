<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Convert existing MediaLibrary attachments to asset records for research items
        $mediaItems = DB::table('media')
            ->where('model_type', 'App\\Models\\ResearchItem')
            ->where('collection_name', 'attachments')
            ->get();

        foreach ($mediaItems as $media) {
            // Check if asset already exists for this media
            $existingAsset = DB::table('assets')
                ->where('media_id', $media->id)
                ->first();

            if (!$existingAsset) {
                // Create asset record from media
                $assetId = DB::table('assets')->insertGetId([
                    'title' => $media->name,
                    'description' => null,
                    'file_name' => $media->file_name,
                    'file_path' => null, // MediaLibrary handles path internally
                    'mime_type' => $media->mime_type,
                    'size' => $media->size,
                    'source_type' => 'research_item',
                    'source_id' => $media->model_id,
                    'media_id' => $media->id,
                    'company_id' => null, // Will be set based on research item
                    'user_id' => null, // Will be set based on research item
                    'visibility' => 'private',
                    'is_orphaned' => false,
                    'created_via' => 'research_item_migration',
                    'created_at' => $media->created_at,
                    'updated_at' => $media->updated_at,
                ]);

                // Get research item details to populate company_id and user_id
                $researchItem = DB::table('research_items')
                    ->where('id', $media->model_id)
                    ->first();

                if ($researchItem) {
                    // Update asset with research item details
                    DB::table('assets')
                        ->where('id', $assetId)
                        ->update([
                            'company_id' => $researchItem->company_id,
                            'user_id' => $researchItem->user_id,
                            'visibility' => $researchItem->visibility ?? 'private',
                        ]);

                    // Create pivot table entry for symbolic link
                    DB::table('research_item_assets')->insert([
                        'research_item_id' => $researchItem->id,
                        'asset_id' => $assetId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            } else {
                // Asset exists, just create pivot table entry if it doesn't exist
                $pivotExists = DB::table('research_item_assets')
                    ->where('research_item_id', $media->model_id)
                    ->where('asset_id', $existingAsset->id)
                    ->exists();

                if (!$pivotExists) {
                    DB::table('research_item_assets')->insert([
                        'research_item_id' => $media->model_id,
                        'asset_id' => $existingAsset->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        \Log::info('Research item media to assets migration completed', [
            'media_items_processed' => $mediaItems->count(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove asset records that were created by this migration
        DB::table('assets')
            ->where('created_via', 'research_item_migration')
            ->delete();

        // The pivot table entries will be deleted by cascade when assets are deleted
        \Log::info('Research item media to assets migration rolled back');
    }
};
