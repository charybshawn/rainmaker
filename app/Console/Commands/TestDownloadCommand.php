<?php

namespace App\Console\Commands;

use App\Services\UrlDownloadService;
use Illuminate\Console\Command;

class TestDownloadCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'download:test {url?} {--name=Test Download}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test URL download functionality with comprehensive debugging';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $downloadService = new UrlDownloadService();

        // Test with provided URL or default test URL
        $url = $this->argument('url') ?? 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf';
        $name = $this->option('name');

        $this->info("Testing download for URL: {$url}");
        $this->info("Document name: {$name}");
        $this->line('');

        // First run the built-in test
        if (!$this->argument('url')) {
            $this->info('Running built-in test with known working URL...');
            $testResult = $downloadService->testDownload();

            if ($testResult['success']) {
                $this->info("✅ Built-in test passed: {$testResult['message']}");
                $this->info("File size: {$testResult['file_size']} bytes");
            } else {
                $this->error("❌ Built-in test failed: {$testResult['message']}");
            }
            $this->line('');
        }

        // Test with the specified URL
        $this->info("Testing download with specified URL...");
        $startTime = microtime(true);

        $result = $downloadService->downloadFile($url, $name);

        $endTime = microtime(true);
        $duration = round($endTime - $startTime, 2);

        if ($result) {
            $fileSize = filesize($result);
            $fileInfo = pathinfo($result);

            $this->info("✅ Download successful!");
            $this->table(['Property', 'Value'], [
                ['File Path', $result],
                ['File Size', number_format($fileSize) . ' bytes'],
                ['File Extension', $fileInfo['extension'] ?? 'none'],
                ['Download Time', $duration . ' seconds'],
            ]);

            // Clean up
            unlink($result);
            $this->info("Temporary file cleaned up.");

        } else {
            $this->error("❌ Download failed after {$duration} seconds");
            $this->error("Check the logs for detailed error information:");
            $this->line("tail -f storage/logs/laravel.log | grep -A 10 -B 10 'URL download'");
        }

        return $result ? Command::SUCCESS : Command::FAILURE;
    }
}
