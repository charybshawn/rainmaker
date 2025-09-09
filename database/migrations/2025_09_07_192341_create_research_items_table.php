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
        Schema::create('research_items', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->text('summary')->nullable();
            $table->enum('visibility', ['public', 'team', 'private'])->default('private');
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->decimal('rating', 2, 1)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->index(['company_id', 'status']);
            $table->index(['category_id', 'visibility']);
            $table->index(['user_id', 'status']);
            $table->index('published_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('research_items');
    }
};
