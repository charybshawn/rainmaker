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
        Schema::create('research_item_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('research_item_id')->constrained()->onDelete('cascade');
            $table->foreignId('asset_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            // Ensure unique combinations
            $table->unique(['research_item_id', 'asset_id']);

            // Add indexes for performance
            $table->index('research_item_id');
            $table->index('asset_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('research_item_assets');
    }
};