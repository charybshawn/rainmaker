<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if document_assets table exists and if foreign keys already exist
        if (Schema::hasTable('document_assets')) {
            // The foreign keys were already added in the unify_asset_system migration
            // This migration is redundant, so we'll skip it
            return;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_assets', function (Blueprint $table) {
            // Drop foreign key constraints
            $table->dropForeign(['document_id']);
            $table->dropForeign(['asset_id']);
        });
    }
};
