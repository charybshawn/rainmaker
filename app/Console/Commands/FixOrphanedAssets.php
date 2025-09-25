<?php

namespace App\Console\Commands;

use App\Services\AssetSyncService;
use Illuminate\Console\Command;

class FixOrphanedAssets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assets:fix-orphaned';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix orphaned assets by marking them as orphaned when their related models no longer exist';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Scanning for orphaned assets...');

        $assetSyncService = app(AssetSyncService::class);
        $fixedCount = $assetSyncService->fixOrphanedAssets();

        if ($fixedCount > 0) {
            $this->info("Fixed {$fixedCount} orphaned assets.");
        } else {
            $this->info('No orphaned assets found.');
        }

        return 0;
    }
}
