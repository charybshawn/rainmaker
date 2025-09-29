<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Asset;
use App\Models\Document;
use App\Models\ResearchItem;
use Illuminate\Support\Facades\DB;

class CleanupOrphanedAssets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assets:cleanup
                            {--dry-run : Show what would be cleaned without actually deleting}
                            {--force : Skip confirmation prompts}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Find and clean up orphaned assets and broken document references';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        $force = $this->option('force');

        $this->info('🔍 Starting orphaned assets cleanup...');
        $this->newLine();

        // Find different types of orphaned assets
        $orphanedAssets = $this->findOrphanedAssets();
        $brokenDocumentAssets = $this->findBrokenDocumentAssets();
        $brokenResearchAssets = $this->findBrokenResearchAssets();
        $unreferencedAssets = $this->findUnreferencedAssets();

        $totalOrphaned = $orphanedAssets->count() + $brokenDocumentAssets->count() +
                        $brokenResearchAssets->count() + $unreferencedAssets->count();

        if ($totalOrphaned === 0) {
            $this->info('✅ No orphaned assets found. Database is clean!');
            return 0;
        }

        // Display findings
        $this->displayFindings($orphanedAssets, $brokenDocumentAssets, $brokenResearchAssets, $unreferencedAssets);

        if ($isDryRun) {
            $this->info('🏃 Dry run complete. Use --force to actually clean up these assets.');
            return 0;
        }

        // Confirm cleanup
        if (!$force && !$this->confirm("Are you sure you want to delete {$totalOrphaned} orphaned assets?")) {
            $this->info('Cleanup cancelled.');
            return 0;
        }

        // Perform cleanup
        $this->performCleanup($orphanedAssets, $brokenDocumentAssets, $brokenResearchAssets, $unreferencedAssets);

        $this->info('✅ Cleanup completed successfully!');
        return 0;
    }

    /**
     * Find assets marked as orphaned
     */
    private function findOrphanedAssets()
    {
        return Asset::where('is_orphaned', true)->get();
    }

    /**
     * Find assets referencing deleted documents
     */
    private function findBrokenDocumentAssets()
    {
        return Asset::where('source_type', 'document')
                   ->whereNotNull('source_id')
                   ->whereNotExists(function ($query) {
                       $query->select(DB::raw(1))
                             ->from('documents')
                             ->whereColumn('documents.id', 'assets.source_id');
                   })
                   ->get();
    }

    /**
     * Find assets referencing soft-deleted research items
     */
    private function findBrokenResearchAssets()
    {
        return Asset::where('source_type', 'research_item')
                   ->whereNotNull('source_id')
                   ->whereExists(function ($query) {
                       $query->select(DB::raw(1))
                             ->from('research_items')
                             ->whereColumn('research_items.id', 'assets.source_id')
                             ->whereNotNull('deleted_at');
                   })
                   ->get();
    }

    /**
     * Find assets not referenced by document_assets junction table
     */
    private function findUnreferencedAssets()
    {
        return Asset::whereNotExists(function ($query) {
                       $query->select(DB::raw(1))
                             ->from('document_assets')
                             ->whereColumn('document_assets.asset_id', 'assets.id');
                   })
                   ->where('is_orphaned', false)
                   ->get();
    }

    /**
     * Display findings summary
     */
    private function displayFindings($orphanedAssets, $brokenDocumentAssets, $brokenResearchAssets, $unreferencedAssets)
    {
        $this->warn('🗑️  Found orphaned assets:');
        $this->newLine();

        if ($orphanedAssets->count() > 0) {
            $this->line("📋 Assets marked as orphaned: {$orphanedAssets->count()}");
            foreach ($orphanedAssets as $asset) {
                $this->line("   - ID {$asset->id}: {$asset->title} ({$asset->file_name})");
            }
            $this->newLine();
        }

        if ($brokenDocumentAssets->count() > 0) {
            $this->line("📄 Assets referencing deleted documents: {$brokenDocumentAssets->count()}");
            foreach ($brokenDocumentAssets as $asset) {
                $this->line("   - ID {$asset->id}: {$asset->title} → deleted document ID {$asset->source_id}");
            }
            $this->newLine();
        }

        if ($brokenResearchAssets->count() > 0) {
            $this->line("📝 Assets referencing soft-deleted research items: {$brokenResearchAssets->count()}");
            foreach ($brokenResearchAssets as $asset) {
                $this->line("   - ID {$asset->id}: {$asset->title} → soft-deleted research item ID {$asset->source_id}");
            }
            $this->newLine();
        }

        if ($unreferencedAssets->count() > 0) {
            $this->line("🔗 Assets not referenced by any junction table: {$unreferencedAssets->count()}");
            foreach ($unreferencedAssets as $asset) {
                $this->line("   - ID {$asset->id}: {$asset->title} ({$asset->source_type})");
            }
            $this->newLine();
        }
    }

    /**
     * Perform the actual cleanup
     */
    private function performCleanup($orphanedAssets, $brokenDocumentAssets, $brokenResearchAssets, $unreferencedAssets)
    {
        $deletedCount = 0;

        $this->info('🧹 Starting cleanup...');

        // Clean up explicitly orphaned assets
        foreach ($orphanedAssets as $asset) {
            $asset->delete();
            $deletedCount++;
            $this->line("✓ Deleted orphaned asset: {$asset->title}");
        }

        // Clean up assets referencing deleted documents
        foreach ($brokenDocumentAssets as $asset) {
            $asset->delete();
            $deletedCount++;
            $this->line("✓ Deleted asset with broken document reference: {$asset->title}");
        }

        // Clean up assets referencing soft-deleted research items
        foreach ($brokenResearchAssets as $asset) {
            $asset->delete();
            $deletedCount++;
            $this->line("✓ Deleted asset with soft-deleted research reference: {$asset->title}");
        }

        // Clean up unreferenced assets
        foreach ($unreferencedAssets as $asset) {
            $asset->delete();
            $deletedCount++;
            $this->line("✓ Deleted unreferenced asset: {$asset->title}");
        }

        $this->newLine();
        $this->info("🎉 Successfully deleted {$deletedCount} orphaned assets");
    }
}
