<?php

namespace App\Console\Commands;

use App\Services\AssetSyncService;
use Illuminate\Console\Command;

class SyncAssetsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assets:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync all media files to the assets table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting assets sync...');

        $syncService = new AssetSyncService();
        $syncService->syncAllAssets();

        $this->info('Assets sync completed successfully!');

        return Command::SUCCESS;
    }
}
