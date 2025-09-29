<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Asset;
use App\Models\Document;
use App\Models\ResearchItem;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DatabaseHealthCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:health-check
                            {--fix : Automatically fix issues where possible}
                            {--detailed : Show detailed information about issues}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perform comprehensive database health checks and integrity validation';

    private $issues = [];
    private $stats = [];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $fix = $this->option('fix');
        $detailed = $this->option('detailed');

        $this->info('🔍 Starting database health check...');
        $this->newLine();

        // Perform all health checks
        $this->checkReferentialIntegrity();
        $this->checkOrphanedRecords();
        $this->checkDataConsistency();
        $this->collectStatistics();

        // Display results
        $this->displayResults($detailed);

        // Fix issues if requested
        if ($fix && !empty($this->issues)) {
            $this->fixIssues();
        }

        // Display summary
        $this->displaySummary();

        return empty($this->issues) ? 0 : 1;
    }

    /**
     * Check referential integrity
     */
    private function checkReferentialIntegrity()
    {
        $this->line('🔗 Checking referential integrity...');

        // Check assets with invalid source references
        $invalidSourceAssets = Asset::where('source_type', 'document')
            ->whereNotNull('source_id')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                      ->from('documents')
                      ->whereColumn('documents.id', 'assets.source_id');
            })
            ->count();

        if ($invalidSourceAssets > 0) {
            $this->issues[] = [
                'type' => 'referential_integrity',
                'description' => "Found {$invalidSourceAssets} assets with invalid document references",
                'count' => $invalidSourceAssets,
                'fixable' => true
            ];
        }

        // Check assets with invalid research item references
        $invalidResearchAssets = Asset::where('source_type', 'research_item')
            ->whereNotNull('source_id')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                      ->from('research_items')
                      ->whereColumn('research_items.id', 'assets.source_id')
                      ->whereNotNull('deleted_at');
            })
            ->count();

        if ($invalidResearchAssets > 0) {
            $this->issues[] = [
                'type' => 'referential_integrity',
                'description' => "Found {$invalidResearchAssets} assets referencing soft-deleted research items",
                'count' => $invalidResearchAssets,
                'fixable' => true
            ];
        }

        // Check document_assets with missing assets
        $brokenDocumentAssets = DB::table('document_assets')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                      ->from('assets')
                      ->whereColumn('assets.id', 'document_assets.asset_id');
            })
            ->count();

        if ($brokenDocumentAssets > 0) {
            $this->issues[] = [
                'type' => 'referential_integrity',
                'description' => "Found {$brokenDocumentAssets} document_assets records with missing assets",
                'count' => $brokenDocumentAssets,
                'fixable' => true
            ];
        }

        // Check document_assets with missing documents
        $brokenAssetDocuments = DB::table('document_assets')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                      ->from('documents')
                      ->whereColumn('documents.id', 'document_assets.document_id');
            })
            ->count();

        if ($brokenAssetDocuments > 0) {
            $this->issues[] = [
                'type' => 'referential_integrity',
                'description' => "Found {$brokenAssetDocuments} document_assets records with missing documents",
                'count' => $brokenAssetDocuments,
                'fixable' => true
            ];
        }
    }

    /**
     * Check for orphaned records
     */
    private function checkOrphanedRecords()
    {
        $this->line('🗑️  Checking for orphaned records...');

        // Orphaned assets (marked as such)
        $orphanedAssets = Asset::where('is_orphaned', true)->count();
        if ($orphanedAssets > 0) {
            $this->issues[] = [
                'type' => 'orphaned_records',
                'description' => "Found {$orphanedAssets} explicitly orphaned assets",
                'count' => $orphanedAssets,
                'fixable' => true
            ];
        }

        // Unreferenced assets
        $unreferencedAssets = Asset::whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                      ->from('document_assets')
                      ->whereColumn('document_assets.asset_id', 'assets.id');
            })
            ->where('is_orphaned', false)
            ->count();

        if ($unreferencedAssets > 0) {
            $this->issues[] = [
                'type' => 'orphaned_records',
                'description' => "Found {$unreferencedAssets} assets not referenced by any document",
                'count' => $unreferencedAssets,
                'fixable' => true
            ];
        }
    }

    /**
     * Check data consistency
     */
    private function checkDataConsistency()
    {
        $this->line('📊 Checking data consistency...');

        // Check for assets with empty or invalid file paths
        $invalidFilePaths = Asset::where(function ($query) {
                $query->whereNull('file_path')
                      ->orWhere('file_path', '')
                      ->orWhere('file_path', 'like', '%//%');
            })
            ->whereNull('media_id')
            ->count();

        if ($invalidFilePaths > 0) {
            $this->issues[] = [
                'type' => 'data_consistency',
                'description' => "Found {$invalidFilePaths} assets with invalid file paths",
                'count' => $invalidFilePaths,
                'fixable' => false
            ];
        }

        // Check for duplicate asset entries
        $duplicateAssets = DB::table('assets')
            ->select('file_name', 'file_path', 'source_type', 'source_id')
            ->whereNotNull('file_name')
            ->whereNotNull('file_path')
            ->groupBy('file_name', 'file_path', 'source_type', 'source_id')
            ->havingRaw('COUNT(*) > 1')
            ->count();

        if ($duplicateAssets > 0) {
            $this->issues[] = [
                'type' => 'data_consistency',
                'description' => "Found {$duplicateAssets} potential duplicate asset entries",
                'count' => $duplicateAssets,
                'fixable' => false
            ];
        }
    }

    /**
     * Collect database statistics
     */
    private function collectStatistics()
    {
        $this->stats = [
            'companies' => Company::count(),
            'users' => User::count(),
            'documents' => Document::count(),
            'research_items' => ResearchItem::count(),
            'research_items_deleted' => ResearchItem::onlyTrashed()->count(),
            'assets' => Asset::count(),
            'orphaned_assets' => Asset::where('is_orphaned', true)->count(),
            'document_asset_links' => DB::table('document_assets')->count(),
        ];
    }

    /**
     * Display results
     */
    private function displayResults($detailed)
    {
        $this->newLine();

        if (empty($this->issues)) {
            $this->info('✅ No issues found! Database is healthy.');
        } else {
            $this->warn('⚠️  Issues found:');
            $this->newLine();

            foreach ($this->issues as $issue) {
                $status = $issue['fixable'] ? '🔧 Fixable' : '⚠️  Manual';
                $this->line("   [{$status}] {$issue['description']}");

                if ($detailed) {
                    $this->line("       Type: {$issue['type']}, Count: {$issue['count']}");
                }
            }
        }

        $this->newLine();
        $this->info('📈 Database Statistics:');
        foreach ($this->stats as $key => $value) {
            $label = ucwords(str_replace('_', ' ', $key));
            $this->line("   {$label}: {$value}");
        }
    }

    /**
     * Fix issues automatically
     */
    private function fixIssues()
    {
        $this->newLine();
        $this->info('🔧 Attempting to fix issues...');

        if (!$this->confirm('This will permanently delete orphaned and invalid records. Continue?')) {
            $this->info('Fix cancelled.');
            return;
        }

        $fixedCount = 0;

        foreach ($this->issues as $issue) {
            if (!$issue['fixable']) {
                continue;
            }

            switch ($issue['type']) {
                case 'referential_integrity':
                case 'orphaned_records':
                    // Use the existing cleanup command
                    $this->call('assets:cleanup', ['--force' => true]);
                    $fixedCount++;
                    break;
            }
        }

        if ($fixedCount > 0) {
            $this->info("✅ Fixed {$fixedCount} issue categories.");
        } else {
            $this->warn('No fixable issues were processed.');
        }
    }

    /**
     * Display summary
     */
    private function displaySummary()
    {
        $this->newLine();
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        $totalIssues = count($this->issues);
        $fixableIssues = count(array_filter($this->issues, fn($issue) => $issue['fixable']));

        if ($totalIssues === 0) {
            $this->info('🎉 Database health check completed successfully - no issues found!');
        } else {
            $this->warn("📋 Summary: {$totalIssues} issues found ({$fixableIssues} fixable)");

            if ($fixableIssues > 0) {
                $this->line('💡 Run with --fix to automatically resolve fixable issues');
            }
        }

        $this->line('💡 Run with --detailed for more information about issues');
        $this->line('🧹 Use "php artisan assets:cleanup" for targeted orphan cleanup');
    }
}
