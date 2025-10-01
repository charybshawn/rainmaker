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
        Schema::table('assets', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['company_id']);

            // Make company_id nullable
            $table->unsignedBigInteger('company_id')->nullable()->change();

            // Re-add the foreign key constraint with nullable support
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            // Drop the nullable foreign key constraint
            $table->dropForeign(['company_id']);

            // Make company_id required again
            $table->unsignedBigInteger('company_id')->nullable(false)->change();

            // Re-add the original foreign key constraint
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });
    }
};
