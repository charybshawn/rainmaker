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
        Schema::create('asset_activities', function (Blueprint $table) {
            $table->id();

            // Polymorphic relationship to any model
            $table->string('trackable_type');
            $table->unsignedBigInteger('trackable_id');

            // Who performed the action
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');

            // What type of activity occurred
            $table->enum('activity_type', [
                'created',
                'updated',
                'deleted',
                'restored',
                'published',
                'archived',
                'attachment_added',
                'attachment_removed',
                'tag_added',
                'tag_removed',
                'category_changed',
                'visibility_changed',
                'status_changed'
            ]);

            // Which field was changed (for updates)
            $table->string('field_name')->nullable();

            // Human-readable description of the change
            $table->text('description');

            // Previous and new values (JSON for flexibility)
            $table->json('old_value')->nullable();
            $table->json('new_value')->nullable();

            // Additional metadata (IP, user agent, file info, etc.)
            $table->json('metadata')->nullable();

            $table->timestamps();

            // Indexes for performance
            $table->index(['trackable_type', 'trackable_id'], 'trackable_index');
            $table->index(['user_id', 'created_at']);
            $table->index(['activity_type', 'created_at']);
            $table->index(['trackable_type', 'activity_type']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_activities');
    }
};