<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Exception;

class UrlDownloadService
{
    private array $userAgents = [
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/121.0',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:109.0) Gecko/20100101 Firefox/121.0',
    ];

    /**
     * Download a file from URL with comprehensive debugging and bypass techniques.
     */
    public function downloadFile(string $url, string $documentName): ?string
    {
        $startTime = microtime(true);

        Log::info('URL download started', [
            'url' => $url,
            'document_name' => $documentName,
            'timestamp' => now()->toISOString(),
            'memory_usage' => memory_get_usage(true),
        ]);

        try {
            // Validate URL
            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                throw new Exception('Invalid URL format');
            }

            // Parse URL components for logging
            $urlParts = parse_url($url);
            Log::info('URL components', [
                'scheme' => $urlParts['scheme'] ?? null,
                'host' => $urlParts['host'] ?? null,
                'path' => $urlParts['path'] ?? null,
                'query' => $urlParts['query'] ?? null,
            ]);

            // Try multiple strategies
            $strategies = [
                'comprehensive' => [$this, 'downloadWithComprehensiveHeaders'],
                'cloudflare_bypass' => [$this, 'downloadWithCloudflareBypass'],
                'session_based' => [$this, 'downloadWithSessionBased'],
                'simple' => [$this, 'downloadWithSimpleHeaders'],
                'stealth' => [$this, 'downloadWithStealthMode'],
            ];

            foreach ($strategies as $strategyName => $strategy) {
                Log::info("Attempting download with strategy: {$strategyName}");

                $result = call_user_func($strategy, $url, $documentName);

                if ($result) {
                    $downloadTime = microtime(true) - $startTime;
                    Log::info('URL download successful', [
                        'url' => $url,
                        'document_name' => $documentName,
                        'strategy' => $strategyName,
                        'file_size' => filesize($result),
                        'download_time' => $downloadTime,
                        'memory_peak' => memory_get_peak_usage(true),
                    ]);
                    return $result;
                }

                // Add delay between attempts
                sleep(rand(1, 3));
            }

            throw new Exception('All download strategies failed');

        } catch (Exception $e) {
            $downloadTime = microtime(true) - $startTime;
            Log::error('URL download completely failed', [
                'url' => $url,
                'document_name' => $documentName,
                'error' => $e->getMessage(),
                'download_time' => $downloadTime,
                'memory_peak' => memory_get_peak_usage(true),
            ]);
            return null;
        }
    }

    /**
     * Download with comprehensive headers and full debugging.
     */
    private function downloadWithComprehensiveHeaders(string $url, string $documentName): ?string
    {
        try {
            $ch = curl_init();

            // Random user agent
            $userAgent = $this->userAgents[array_rand($this->userAgents)];

            // Get domain for referrer
            $urlParts = parse_url($url);
            $domain = $urlParts['scheme'] . '://' . $urlParts['host'];

            $curlOptions = [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 60,
                CURLOPT_CONNECTTIMEOUT => 30,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_USERAGENT => $userAgent,
                CURLOPT_ENCODING => 'gzip, deflate, br',
                CURLOPT_AUTOREFERER => true,
                CURLOPT_COOKIEJAR => tempnam(sys_get_temp_dir(), 'cookies'),
                CURLOPT_COOKIEFILE => tempnam(sys_get_temp_dir(), 'cookies'),
                CURLOPT_REFERER => $domain,
                CURLOPT_HTTPHEADER => [
                    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
                    'Accept-Language: en-US,en;q=0.9,es;q=0.8,fr;q=0.7',
                    'Accept-Encoding: gzip, deflate, br',
                    'Connection: keep-alive',
                    'Upgrade-Insecure-Requests: 1',
                    'Sec-Fetch-Dest: document',
                    'Sec-Fetch-Mode: navigate',
                    'Sec-Fetch-Site: cross-site',
                    'Sec-Fetch-User: ?1',
                    'Cache-Control: max-age=0',
                    'DNT: 1',
                    'Sec-GPC: 1',
                ],
                CURLOPT_HEADERFUNCTION => function($ch, $header) {
                    Log::debug('Response header received', ['header' => trim($header)]);
                    return strlen($header);
                },
            ];

            // Set all options
            curl_setopt_array($ch, $curlOptions);

            // Log the configuration
            Log::info('cURL configuration set', [
                'user_agent' => $userAgent,
                'referrer' => $domain,
                'timeout' => 60,
                'max_redirects' => 10,
                'headers_count' => count($curlOptions[CURLOPT_HTTPHEADER]),
            ]);

            // Add random delay to simulate human behavior
            usleep(rand(500000, 2000000)); // 0.5 to 2 seconds

            // Execute request
            Log::info('Executing cURL request...');
            $data = curl_exec($ch);

            // Get comprehensive information
            $curlInfo = curl_getinfo($ch);
            $curlError = curl_error($ch);

            // Log detailed response information
            Log::info('cURL response details', [
                'http_code' => $curlInfo['http_code'],
                'effective_url' => $curlInfo['url'],
                'content_type' => $curlInfo['content_type'],
                'download_content_length' => $curlInfo['download_content_length'],
                'total_time' => $curlInfo['total_time'],
                'namelookup_time' => $curlInfo['namelookup_time'],
                'connect_time' => $curlInfo['connect_time'],
                'pretransfer_time' => $curlInfo['pretransfer_time'],
                'starttransfer_time' => $curlInfo['starttransfer_time'],
                'redirect_count' => $curlInfo['redirect_count'],
                'redirect_url' => $curlInfo['redirect_url'] ?? null,
                'primary_ip' => $curlInfo['primary_ip'],
                'ssl_verify_result' => $curlInfo['ssl_verify_result'],
            ]);

            curl_close($ch);

            if ($curlError) {
                throw new Exception('cURL error: ' . $curlError);
            }

            if ($curlInfo['http_code'] !== 200) {
                throw new Exception("HTTP error: {$curlInfo['http_code']} - Response received but not OK");
            }

            if (empty($data)) {
                throw new Exception('Empty response from URL');
            }

            // Validate content
            $dataLength = strlen($data);
            $firstBytes = bin2hex(substr($data, 0, 16));
            $isBinary = !mb_check_encoding($data, 'UTF-8');

            Log::info('Content validation', [
                'data_length' => $dataLength,
                'is_binary' => $isBinary,
                'first_bytes' => $firstBytes,
                'content_preview' => $isBinary ? 'Binary content' : substr($data, 0, 200),
            ]);

            if ($dataLength < 100) {
                Log::warning('Suspiciously small response', [
                    'data_length' => $dataLength,
                    'content_preview' => substr($data, 0, 200),
                ]);
            }

            // Determine file extension
            $extension = $this->getFileExtensionFromUrl($url, $curlInfo['content_type']);

            // Create temporary file
            $tempFilePath = tempnam(sys_get_temp_dir(), 'download_comprehensive_') . '.' . $extension;

            if (file_put_contents($tempFilePath, $data) === false) {
                throw new Exception('Failed to write downloaded content to temporary file');
            }

            Log::info('File written successfully', [
                'temp_path' => $tempFilePath,
                'file_size' => filesize($tempFilePath),
                'extension' => $extension,
            ]);

            return $tempFilePath;

        } catch (Exception $e) {
            Log::error('Comprehensive download strategy failed', [
                'url' => $url,
                'error' => $e->getMessage(),
                'curl_info' => isset($curlInfo) ? $curlInfo : null,
            ]);
            return null;
        }
    }

    /**
     * Download with simple headers as fallback.
     */
    private function downloadWithSimpleHeaders(string $url, string $documentName): ?string
    {
        try {
            Log::info('Trying simple headers approach');

            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_MAXREDIRS => 5,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_USERAGENT => 'curl/8.0.0',
                CURLOPT_HTTPHEADER => [
                    'Accept: */*',
                    'Connection: keep-alive',
                ],
            ]);

            $data = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
            $error = curl_error($ch);

            Log::info('Simple headers response', [
                'http_code' => $httpCode,
                'content_type' => $contentType,
                'data_length' => strlen($data ?: ''),
                'curl_error' => $error,
            ]);

            curl_close($ch);

            if ($error) {
                throw new Exception('cURL error: ' . $error);
            }

            if ($httpCode !== 200 || empty($data)) {
                throw new Exception("Simple approach failed - HTTP: {$httpCode}");
            }

            $extension = $this->getFileExtensionFromUrl($url, $contentType);
            $tempFilePath = tempnam(sys_get_temp_dir(), 'download_simple_') . '.' . $extension;

            if (file_put_contents($tempFilePath, $data) === false) {
                throw new Exception('Failed to write file');
            }

            return $tempFilePath;

        } catch (Exception $e) {
            Log::error('Simple download strategy failed', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Download with stealth mode techniques.
     */
    private function downloadWithStealthMode(string $url, string $documentName): ?string
    {
        try {
            Log::info('Trying stealth mode approach');

            // First, make a HEAD request to check if the resource exists
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_NOBODY => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_TIMEOUT => 15,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_USERAGENT => $this->userAgents[array_rand($this->userAgents)],
            ]);

            curl_exec($ch);
            $headCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            Log::info('HEAD request result', ['http_code' => $headCode]);

            if ($headCode !== 200) {
                throw new Exception("HEAD request failed with code: {$headCode}");
            }

            // Now make the actual request with stealth parameters
            $ch = curl_init();
            $userAgent = $this->userAgents[array_rand($this->userAgents)];

            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_MAXREDIRS => 3,
                CURLOPT_TIMEOUT => 45,
                CURLOPT_CONNECTTIMEOUT => 15,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_USERAGENT => $userAgent,
                CURLOPT_HTTPHEADER => [
                    'Accept: application/pdf,application/octet-stream,*/*',
                    'Accept-Language: en-US,en;q=0.5',
                    'Connection: keep-alive',
                    'Cache-Control: no-cache',
                ],
                // Simulate slower connection
                CURLOPT_LOW_SPEED_LIMIT => 1000,
                CURLOPT_LOW_SPEED_TIME => 30,
            ]);

            // Add another random delay
            sleep(rand(2, 5));

            $data = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
            $error = curl_error($ch);

            Log::info('Stealth mode response', [
                'http_code' => $httpCode,
                'content_type' => $contentType,
                'data_length' => strlen($data ?: ''),
                'curl_error' => $error,
            ]);

            curl_close($ch);

            if ($error) {
                throw new Exception('cURL error: ' . $error);
            }

            if ($httpCode !== 200 || empty($data)) {
                throw new Exception("Stealth approach failed - HTTP: {$httpCode}");
            }

            $extension = $this->getFileExtensionFromUrl($url, $contentType);
            $tempFilePath = tempnam(sys_get_temp_dir(), 'download_stealth_') . '.' . $extension;

            if (file_put_contents($tempFilePath, $data) === false) {
                throw new Exception('Failed to write file');
            }

            return $tempFilePath;

        } catch (Exception $e) {
            Log::error('Stealth download strategy failed', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Test download functionality with a known working URL.
     */
    public function testDownload(): array
    {
        Log::info('Starting download test with known working URL');

        $testUrl = 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf';
        $result = $this->downloadFile($testUrl, 'Test PDF');

        if ($result) {
            $fileSize = filesize($result);
            $fileInfo = pathinfo($result);

            // Clean up test file
            unlink($result);

            Log::info('Test download successful', [
                'url' => $testUrl,
                'file_size' => $fileSize,
                'extension' => $fileInfo['extension'] ?? null,
            ]);

            return [
                'success' => true,
                'message' => 'Test download completed successfully',
                'file_size' => $fileSize,
            ];
        } else {
            Log::error('Test download failed');

            return [
                'success' => false,
                'message' => 'Test download failed',
            ];
        }
    }

    /**
     * Get file extension from URL or content type.
     */
    private function getFileExtensionFromUrl(string $url, ?string $contentType): string
    {
        // First try to get extension from content type
        if ($contentType) {
            $mimeToExtension = [
                'application/pdf' => 'pdf',
                'application/msword' => 'doc',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
                'application/vnd.ms-excel' => 'xls',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
                'application/vnd.ms-powerpoint' => 'ppt',
                'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
                'text/plain' => 'txt',
                'text/csv' => 'csv',
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/gif' => 'gif',
                'image/webp' => 'webp',
                'image/svg+xml' => 'svg',
            ];

            $contentType = strtolower(trim(explode(';', $contentType)[0]));
            if (isset($mimeToExtension[$contentType])) {
                return $mimeToExtension[$contentType];
            }
        }

        // Fallback to URL extension
        $urlPath = parse_url($url, PHP_URL_PATH);
        if ($urlPath) {
            $extension = strtolower(pathinfo($urlPath, PATHINFO_EXTENSION));
            if (!empty($extension)) {
                return $extension;
            }
        }

        // Default fallback
        return 'bin';
    }

    /**
     * Download with advanced Cloudflare bypass techniques.
     */
    private function downloadWithCloudflareBypass(string $url, string $documentName): ?string
    {
        try {
            Log::info('Trying Cloudflare bypass approach');

            $ch = curl_init();

            // Use a very realistic browser signature
            $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36';

            // Get domain for cookie and referrer handling
            $urlParts = parse_url($url);
            $domain = $urlParts['scheme'] . '://' . $urlParts['host'];

            // First, visit the main domain to establish session
            Log::info('Establishing session with main domain', ['domain' => $domain]);

            $cookieJar = tempnam(sys_get_temp_dir(), 'cf_cookies');

            curl_setopt_array($ch, [
                CURLOPT_URL => $domain,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_MAXREDIRS => 5,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_USERAGENT => $userAgent,
                CURLOPT_COOKIEJAR => $cookieJar,
                CURLOPT_COOKIEFILE => $cookieJar,
                CURLOPT_HTTPHEADER => [
                    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                    'Accept-Language: en-US,en;q=0.5',
                    'Accept-Encoding: gzip, deflate',
                    'Connection: keep-alive',
                    'Upgrade-Insecure-Requests: 1',
                    'Cache-Control: no-cache',
                    'Pragma: no-cache',
                    // Cloudflare-specific headers
                    'CF-Visitor: {"scheme":"https"}',
                    'CF-Ray: ' . bin2hex(random_bytes(8)),
                ],
            ]);

            $homepageResponse = curl_exec($ch);
            $homepageCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            Log::info('Homepage visit result', [
                'http_code' => $homepageCode,
                'response_length' => strlen($homepageResponse ?: ''),
            ]);

            // Wait a bit to simulate human behavior
            sleep(rand(2, 4));

            // Now try to access the PDF with established session
            Log::info('Attempting PDF download with session');

            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 60,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_USERAGENT => $userAgent,
                CURLOPT_COOKIEJAR => $cookieJar,
                CURLOPT_COOKIEFILE => $cookieJar,
                CURLOPT_REFERER => $domain,
                CURLOPT_HTTPHEADER => [
                    'Accept: application/pdf,application/octet-stream,*/*',
                    'Accept-Language: en-US,en;q=0.5',
                    'Connection: keep-alive',
                    'Sec-Fetch-Dest: document',
                    'Sec-Fetch-Mode: navigate',
                    'Sec-Fetch-Site: same-origin',
                    'Cache-Control: max-age=0',
                ],
            ]);

            $data = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
            $error = curl_error($ch);
            $curlInfo = curl_getinfo($ch);

            Log::info('Cloudflare bypass response', [
                'http_code' => $httpCode,
                'content_type' => $contentType,
                'data_length' => strlen($data ?: ''),
                'curl_error' => $error,
                'effective_url' => $curlInfo['url'],
            ]);

            curl_close($ch);

            // Clean up cookie file
            if (file_exists($cookieJar)) {
                unlink($cookieJar);
            }

            if ($error) {
                throw new Exception('cURL error: ' . $error);
            }

            if ($httpCode !== 200 || empty($data)) {
                throw new Exception("Cloudflare bypass failed - HTTP: {$httpCode}");
            }

            // Check if we got HTML instead of PDF (indicating challenge page)
            if (str_contains(strtolower($contentType), 'text/html')) {
                $preview = substr($data, 0, 500);
                if (str_contains(strtolower($preview), 'cloudflare') || str_contains(strtolower($preview), 'challenge')) {
                    throw new Exception('Received Cloudflare challenge page instead of PDF');
                }
            }

            $extension = $this->getFileExtensionFromUrl($url, $contentType);
            $tempFilePath = tempnam(sys_get_temp_dir(), 'download_cf_bypass_') . '.' . $extension;

            if (file_put_contents($tempFilePath, $data) === false) {
                throw new Exception('Failed to write file');
            }

            return $tempFilePath;

        } catch (Exception $e) {
            Log::error('Cloudflare bypass strategy failed', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Download with session-based approach for protected content.
     */
    private function downloadWithSessionBased(string $url, string $documentName): ?string
    {
        try {
            Log::info('Trying session-based approach with multiple requests');

            $ch = curl_init();
            $userAgent = $this->userAgents[array_rand($this->userAgents)];
            $urlParts = parse_url($url);
            $baseUrl = $urlParts['scheme'] . '://' . $urlParts['host'];

            $cookieJar = tempnam(sys_get_temp_dir(), 'session_cookies');

            // Step 1: Visit homepage
            curl_setopt_array($ch, [
                CURLOPT_URL => $baseUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_USERAGENT => $userAgent,
                CURLOPT_COOKIEJAR => $cookieJar,
                CURLOPT_COOKIEFILE => $cookieJar,
                CURLOPT_HTTPHEADER => [
                    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                    'Accept-Language: en-US,en;q=0.9',
                    'Connection: keep-alive',
                ],
            ]);

            curl_exec($ch);
            $homeCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            Log::info('Session setup - homepage visit', ['http_code' => $homeCode]);

            sleep(1); // Brief pause

            // Step 2: Visit a related page (like /research or /wp-content)
            $relatedPath = '/wp-content/';
            curl_setopt($ch, CURLOPT_URL, $baseUrl . $relatedPath);
            curl_exec($ch);
            $relatedCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            Log::info('Session setup - related page visit', ['http_code' => $relatedCode]);

            sleep(rand(1, 3)); // Random pause to simulate reading

            // Step 3: Now try to access the actual PDF
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_HTTPHEADER => [
                    'Accept: application/pdf,*/*',
                    'Accept-Language: en-US,en;q=0.9',
                    'Connection: keep-alive',
                    'Referer: ' . $baseUrl,
                    'Sec-Fetch-Dest: document',
                    'Sec-Fetch-Mode: navigate',
                    'Sec-Fetch-Site: same-origin',
                    'Sec-Fetch-User: ?1',
                ],
            ]);

            $data = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
            $error = curl_error($ch);

            Log::info('Session-based download result', [
                'http_code' => $httpCode,
                'content_type' => $contentType,
                'data_length' => strlen($data ?: ''),
                'curl_error' => $error,
            ]);

            curl_close($ch);

            // Clean up cookie file
            if (file_exists($cookieJar)) {
                unlink($cookieJar);
            }

            if ($error) {
                throw new Exception('cURL error: ' . $error);
            }

            if ($httpCode !== 200 || empty($data)) {
                throw new Exception("Session-based approach failed - HTTP: {$httpCode}");
            }

            $extension = $this->getFileExtensionFromUrl($url, $contentType);
            $tempFilePath = tempnam(sys_get_temp_dir(), 'download_session_') . '.' . $extension;

            if (file_put_contents($tempFilePath, $data) === false) {
                throw new Exception('Failed to write file');
            }

            return $tempFilePath;

        } catch (Exception $e) {
            Log::error('Session-based download strategy failed', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }
}