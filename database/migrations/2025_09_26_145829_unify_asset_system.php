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
        // Add created_via column to assets table
        Schema::table('assets', function (Blueprint $table) {
            $table->string('created_via')->default('research_item')->after('source_type');
            $table->index(['created_via']);
        });

        // Create document_assets pivot table for many-to-many relationship
        Schema::create('document_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained()->onDelete('cascade');
            $table->foreignId('asset_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['document_id', 'asset_id']);
            $table->index(['document_id']);
            $table->index(['asset_id']);
        });

        // Update existing assets to have created_via based on source_type
        DB::table('assets')->where('source_type', 'document')->update(['created_via' => 'document']);
        DB::table('assets')->where('source_type', 'research_note')->update(['created_via' => 'research_item']);

        // Migrate existing document media to asset references
        $this->migrateDocumentMediaToAssets();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_assets');

        Schema::table('assets', function (Blueprint $table) {
            $table->dropIndex(['created_via']);
            $table->dropColumn('created_via');
        });
    }

    /**
     * Migrate existing document media files to asset references
     */
    private function migrateDocumentMediaToAssets(): void
    {
        // Get all documents with media
        $documents = DB::table('documents')->get();

        foreach ($documents as $document) {
            // Find assets that belong to this document
            $assets = DB::table('assets')
                ->where('source_type', 'document')
                ->where('source_id', $document->id)
                ->get();

            // Create pivot table entries
            foreach ($assets as $asset) {
                DB::table('document_assets')->insert([
                    'document_id' => $document->id,
                    'asset_id' => $asset->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
};
