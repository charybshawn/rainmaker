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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('ticker_symbol', 10)->unique();
            $table->string('sector')->nullable();
            $table->string('industry')->nullable();
            $table->decimal('market_cap', 20, 2)->nullable();
            $table->text('description')->nullable();
            $table->string('website_url')->nullable();
            $table->string('headquarters')->nullable();
            $table->integer('employees')->nullable();
            $table->date('founded_date')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            $table->index(['sector', 'industry']);
            $table->index('created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
