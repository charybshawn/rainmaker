<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            // Drop the existing unique constraint
            $table->dropUnique(['ticker']);
        });

        // For SQLite, we need to use a raw query to create a partial unique index
        // For other databases, this would be different
        if (config('database.default') === 'sqlite') {
            DB::statement('CREATE UNIQUE INDEX companies_ticker_unique_not_deleted ON companies (ticker) WHERE deleted_at IS NULL');
        } else {
            // For MySQL/PostgreSQL, the syntax would be different
            Schema::table('companies', function (Blueprint $table) {
                $table->unique(['ticker'], 'companies_ticker_unique_not_deleted');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the partial unique index
        if (config('database.default') === 'sqlite') {
            DB::statement('DROP INDEX IF EXISTS companies_ticker_unique_not_deleted');
        } else {
            Schema::table('companies', function (Blueprint $table) {
                $table->dropUnique('companies_ticker_unique_not_deleted');
            });
        }

        // Restore the original unique constraint
        Schema::table('companies', function (Blueprint $table) {
            $table->unique('ticker');
        });
    }
};
